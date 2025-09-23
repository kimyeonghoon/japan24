#!/bin/bash

# Monitoring Setup Script for Japan24
# This script sets up basic monitoring tools and health checks

set -e

echo "📊 Setting up monitoring for Japan24..."

# Create monitoring directories
mkdir -p monitoring/{grafana,prometheus}
mkdir -p scripts/health-checks

# Create Docker Compose override for monitoring
cat > monitoring/docker-compose.monitoring.yml <<EOF
services:
  prometheus:
    image: prom/prometheus:latest
    container_name: castle24-prometheus
    restart: unless-stopped
    ports:
      - "9090:9090"
    volumes:
      - ./monitoring/prometheus/prometheus.yml:/etc/prometheus/prometheus.yml:ro
      - prometheus_data:/prometheus
    command:
      - '--config.file=/etc/prometheus/prometheus.yml'
      - '--storage.tsdb.path=/prometheus'
      - '--web.console.libraries=/etc/prometheus/console_libraries'
      - '--web.console.templates=/etc/prometheus/consoles'
      - '--storage.tsdb.retention.time=200h'
      - '--web.enable-lifecycle'
    networks:
      - castle24-prod

  grafana:
    image: grafana/grafana:latest
    container_name: castle24-grafana
    restart: unless-stopped
    ports:
      - "3000:3000"
    environment:
      - GF_SECURITY_ADMIN_PASSWORD=admin123  # Change this!
    volumes:
      - grafana_data:/var/lib/grafana
      - ./monitoring/grafana/provisioning:/etc/grafana/provisioning
    networks:
      - castle24-prod

  node-exporter:
    image: prom/node-exporter:latest
    container_name: castle24-node-exporter
    restart: unless-stopped
    ports:
      - "9100:9100"
    volumes:
      - /proc:/host/proc:ro
      - /sys:/host/sys:ro
      - /:/rootfs:ro
    command:
      - '--path.procfs=/host/proc'
      - '--path.rootfs=/rootfs'
      - '--path.sysfs=/host/sys'
      - '--collector.filesystem.mount-points-exclude=^/(sys|proc|dev|host|etc)($$|/)'
    networks:
      - castle24-prod

volumes:
  prometheus_data:
  grafana_data:
EOF

# Create Prometheus configuration
cat > monitoring/prometheus/prometheus.yml <<EOF
global:
  scrape_interval: 15s
  evaluation_interval: 15s

rule_files:
  # - "first_rules.yml"
  # - "second_rules.yml"

scrape_configs:
  - job_name: 'prometheus'
    static_configs:
      - targets: ['localhost:9090']

  - job_name: 'node-exporter'
    static_configs:
      - targets: ['node-exporter:9100']

  - job_name: 'nginx'
    static_configs:
      - targets: ['webserver:80']
    metrics_path: '/nginx-status'
    scrape_interval: 30s

  - job_name: 'japan24-app'
    static_configs:
      - targets: ['app:9000']
    metrics_path: '/health'
    scrape_interval: 30s
EOF

# Create health check script
cat > scripts/health-checks/app-health.sh <<'EOF'
#!/bin/bash

# Japan24 Application Health Check Script

HEALTH_URL="http://localhost/health"
LOG_FILE="/var/log/japan24-health.log"
ALERT_EMAIL="admin@yourdomain.com"

# Function to log with timestamp
log_message() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$LOG_FILE"
}

# Function to send alert
send_alert() {
    local message="$1"
    echo "$message" | mail -s "Japan24 Health Alert" "$ALERT_EMAIL" 2>/dev/null || true
    log_message "ALERT: $message"
}

# Check application health
check_app_health() {
    local response=$(curl -s -o /dev/null -w "%{http_code}" "$HEALTH_URL" 2>/dev/null)

    if [ "$response" = "200" ]; then
        log_message "INFO: Application health check passed"
        return 0
    else
        send_alert "Application health check failed. HTTP status: $response"
        return 1
    fi
}

# Check database connectivity
check_database() {
    local db_check=$(docker compose -f docker-compose.prod.yml exec -T app php artisan tinker --execute="echo 'DB OK: ' . \App\Models\User::count();" 2>/dev/null)

    if echo "$db_check" | grep -q "DB OK:"; then
        log_message "INFO: Database connectivity check passed"
        return 0
    else
        send_alert "Database connectivity check failed"
        return 1
    fi
}

# Check disk space
check_disk_space() {
    local disk_usage=$(df / | tail -1 | awk '{print $5}' | sed 's/%//')

    if [ "$disk_usage" -gt 85 ]; then
        send_alert "High disk usage detected: ${disk_usage}%"
        return 1
    else
        log_message "INFO: Disk usage check passed (${disk_usage}%)"
        return 0
    fi
}

# Check memory usage
check_memory() {
    local memory_usage=$(free | grep Mem | awk '{printf "%.0f", $3/$2 * 100.0}')

    if [ "$memory_usage" -gt 85 ]; then
        send_alert "High memory usage detected: ${memory_usage}%"
        return 1
    else
        log_message "INFO: Memory usage check passed (${memory_usage}%)"
        return 0
    fi
}

# Main health check
main() {
    log_message "Starting health check"

    check_app_health
    check_database
    check_disk_space
    check_memory

    log_message "Health check completed"
}

# Run main function
main
EOF

chmod +x scripts/health-checks/app-health.sh

# Create systemd service for health checks
cat > monitoring/japan24-health.service <<EOF
[Unit]
Description=Japan24 Health Check
Wants=japan24-health.timer

[Service]
Type=oneshot
ExecStart=$(pwd)/scripts/health-checks/app-health.sh
WorkingDirectory=$(pwd)

[Install]
WantedBy=multi-user.target
EOF

# Create systemd timer for health checks
cat > monitoring/japan24-health.timer <<EOF
[Unit]
Description=Japan24 Health Check Timer
Requires=japan24-health.service

[Timer]
OnCalendar=*:0/5  # Every 5 minutes
Persistent=true

[Install]
WantedBy=timers.target
EOF

# Create log rotation configuration
cat > monitoring/japan24-logrotate <<EOF
/var/log/japan24-health.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    copytruncate
}
EOF

echo "✅ Monitoring setup completed!"
echo ""
echo "📁 Created files:"
echo "  - monitoring/docker-compose.monitoring.yml"
echo "  - monitoring/prometheus/prometheus.yml"
echo "  - scripts/health-checks/app-health.sh"
echo "  - monitoring/japan24-health.service"
echo "  - monitoring/japan24-health.timer"
echo ""
echo "🚀 To start monitoring services:"
echo "  docker compose -f docker-compose.prod.yml -f monitoring/docker-compose.monitoring.yml up -d"
echo ""
echo "📊 Access monitoring dashboards:"
echo "  - Prometheus: http://localhost:9090"
echo "  - Grafana: http://localhost:3000 (admin/admin123)"
echo ""
echo "⏰ To enable health check timer:"
echo "  sudo cp monitoring/japan24-health.service /etc/systemd/system/"
echo "  sudo cp monitoring/japan24-health.timer /etc/systemd/system/"
echo "  sudo systemctl enable japan24-health.timer"
echo "  sudo systemctl start japan24-health.timer"
echo ""
echo "📝 To setup log rotation:"
echo "  sudo cp monitoring/japan24-logrotate /etc/logrotate.d/japan24"
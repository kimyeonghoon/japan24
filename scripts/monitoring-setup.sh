#!/bin/bash

# Japan24 모니터링 시스템 설정 스크립트
#
# 이 스크립트는 Japan24 애플리케이션을 위한 포괄적인 모니터링 시스템을 설정합니다.
# Prometheus, Grafana, Node Exporter를 통한 완전한 모니터링 스택을 구축합니다.
#
# 포함된 모니터링 도구:
# - Prometheus: 메트릭 수집 및 저장
# - Grafana: 시각화 대시보드 (포트: 3000)
# - Node Exporter: 시스템 메트릭 수집
# - 자동 헬스 체크: 애플리케이션 상태 모니터링
# - 로그 로테이션: 디스크 공간 관리
# - 알림 시스템: 이메일 기반 알림
#
# 모니터링 대상:
# - 애플리케이션 가용성 (HTTP 200 응답)
# - 데이터베이스 연결 상태
# - 시스템 리소스 (CPU, 메모리, 디스크)
# - Nginx 웹 서버 상태
# - PHP-FPM 프로세스 상태
#
# 사용법:
#   ./scripts/monitoring-setup.sh
#
# 설정 후 접속:
#   Prometheus: http://localhost:9090
#   Grafana: http://localhost:3000 (admin/admin123)
#
# 주의사항:
# - Grafana 기본 비밀번호를 반드시 변경하세요
# - 알림 이메일 주소를 실제 관리자 주소로 설정하세요
# - 프로덕션 환경에서는 방화벽 설정을 확인하세요

# 스크립트 실행 중 오류 발생 시 즉시 중단
set -e

echo "📊 Japan24 모니터링 시스템 설정을 시작합니다..."

# Step 1: 모니터링 관련 디렉토리 구조 생성
echo "📁 모니터링 디렉토리 구조 생성 중..."
mkdir -p monitoring/{grafana,prometheus}    # Grafana 및 Prometheus 설정 디렉토리
mkdir -p scripts/health-checks              # 헬스 체크 스크립트 디렉토리

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
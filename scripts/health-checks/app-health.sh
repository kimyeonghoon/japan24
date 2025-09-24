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

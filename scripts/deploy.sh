#!/bin/bash

# Japan24 프로덕션 배포 스크립트
#
# 이 스크립트는 Japan24 애플리케이션을 프로덕션 환경에 안전하게 배포하기 위한
# 자동화된 배포 도구입니다. 배포 과정의 모든 단계를 체계적으로 수행합니다.
#
# 주요 기능:
# - 환경 설정 파일 검증
# - Laravel 애플리케이션 키 생성/확인
# - 필수 디렉토리 자동 생성
# - 권한 설정 자동화
# - 의존성 설치 및 최적화
# - Laravel 캐시 최적화
# - 데이터베이스 마이그레이션
# - Docker 컨테이너 빌드 및 시작
# - 헬스 체크 자동 실행
#
# 사용법:
#   ./scripts/deploy.sh
#
# 전제 조건:
# - .env.production 파일이 올바르게 설정되어 있어야 함
# - Docker 및 Docker Compose가 설치되어 있어야 함
# - SSL 인증서가 준비되어 있어야 함 (선택사항)
#
# 주의사항:
# - 프로덕션 환경에서만 실행하세요
# - 배포 전 백업을 권장합니다
# - 데이터베이스 시딩은 선택적으로 실행됩니다

# 스크립트 실행 중 오류 발생 시 즉시 중단
set -e

echo "🚀 Japan24 프로덕션 배포를 시작합니다..."

# Step 1: 환경 설정 파일 존재 여부 확인
echo "📋 Step 1: 환경 설정 파일 검증 중..."
if [ ! -f ".env.production" ]; then
    echo "❌ .env.production 파일을 찾을 수 없습니다!"
    echo "📝 .env.production.example 파일을 복사하고 설정을 완료한 후 다시 실행하세요."
    echo "💡 명령어: cp .env.production.example .env.production"
    exit 1
fi

# Step 2: Laravel 애플리케이션 키 생성 및 확인
echo "🔐 Step 2: 애플리케이션 보안 키 확인 중..."
if ! grep -q "APP_KEY=base64:" .env.production; then
    echo "🔑 새로운 애플리케이션 키를 생성합니다..."
    echo "⚠️  생성된 키를 .env.production 파일에 수동으로 추가하세요:"
    php artisan key:generate --show --env=production
fi

# Step 3: 필수 디렉토리 구조 생성
echo "📁 Step 3: 필수 디렉토리 구조 생성 중..."
mkdir -p storage/logs                           # 애플리케이션 로그 디렉토리
mkdir -p storage/framework/{cache,sessions,views}  # Laravel 프레임워크 캐시 디렉토리
mkdir -p bootstrap/cache                        # Laravel 부트스트랩 캐시 디렉토리
mkdir -p docker/ssl                             # SSL 인증서 디렉토리

# Set permissions
echo "🔧 Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Install production dependencies
echo "📦 Installing production dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Clear and cache Laravel configurations
echo "⚡ Optimizing Laravel..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "💾 Running database migrations..."
php artisan migrate --force

# Seed database if needed
read -p "Do you want to seed the database? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan db:seed --force
fi

# Build and start production containers
echo "🐳 Building production Docker containers..."
docker compose -f docker-compose.prod.yml build --no-cache

echo "🚀 Starting production containers..."
docker compose -f docker-compose.prod.yml up -d

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 10

# Run health check
echo "🏥 Running health check..."
if curl -f http://localhost/api/public/status > /dev/null 2>&1; then
    echo "✅ Application is running successfully!"
else
    echo "❌ Health check failed. Please check the logs:"
    echo "docker compose -f docker-compose.prod.yml logs"
    exit 1
fi

echo ""
echo "🎉 Deployment completed successfully!"
echo ""
echo "📊 Container status:"
docker compose -f docker-compose.prod.yml ps
echo ""
echo "📝 To view logs:"
echo "docker compose -f docker-compose.prod.yml logs -f"
echo ""
echo "🔧 To access the application container:"
echo "docker compose -f docker-compose.prod.yml exec app sh"
echo ""
echo "🌐 Your application should be available at:"
echo "http://localhost (or your configured domain)"
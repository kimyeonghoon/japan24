# 🚀 Japan24 배포 가이드

Japan24 애플리케이션을 프로덕션 환경에 배포하는 방법을 설명합니다.

## 📋 사전 요구사항

- Docker 및 Docker Compose 설치
- 도메인 이름 (선택사항)
- SSL 인증서 (Let's Encrypt 권장)
- 최소 2GB RAM, 10GB 디스크 공간

## 🔧 배포 단계

### 1. 환경 설정

```bash
# 프로덕션 환경 변수 설정
cp .env.production .env

# 중요: 다음 값들을 반드시 변경하세요
# - APP_KEY (새로 생성)
# - APP_URL (실제 도메인)
# - DB_PASSWORD (강력한 비밀번호)
# - REDIS_PASSWORD (강력한 비밀번호)
# - MAIL_* (실제 메일 서버 정보)
```

### 2. SSL 인증서 설정

#### 개발/테스트용 (자체 서명 인증서)
```bash
./scripts/generate-ssl.sh localhost 365
```

#### 프로덕션용 (Let's Encrypt)
```bash
./scripts/setup-letsencrypt.sh your-domain.com admin@your-domain.com
```

### 3. 애플리케이션 배포

```bash
# 배포 스크립트 실행
./scripts/deploy.sh
```

### 4. 배포 확인

```bash
# 컨테이너 상태 확인
docker compose -f docker-compose.prod.yml ps

# 로그 확인
docker compose -f docker-compose.prod.yml logs -f

# 헬스 체크
curl -k https://localhost/health
```

## 🔐 보안 설정

### 필수 보안 체크리스트

- [ ] APP_KEY 새로 생성
- [ ] 강력한 데이터베이스 비밀번호 설정
- [ ] Redis 비밀번호 설정
- [ ] SSL 인증서 설정 (Let's Encrypt 권장)
- [ ] 방화벽 설정 (80, 443 포트만 오픈)
- [ ] 정기적인 백업 설정

### 환경변수 보안

중요한 환경변수는 Docker Secrets 또는 외부 키 관리 서비스 사용을 권장합니다:

```bash
# 예: Docker Secrets 사용
echo "your-strong-password" | docker secret create db_password -
echo "your-redis-password" | docker secret create redis_password -
```

## 📊 모니터링

### 로그 확인
```bash
# 애플리케이션 로그
docker compose -f docker-compose.prod.yml logs app

# Nginx 로그
docker compose -f docker-compose.prod.yml logs webserver

# Redis 로그
docker compose -f docker-compose.prod.yml logs redis
```

### 성능 모니터링
```bash
# 컨테이너 리소스 사용량
docker stats

# Nginx 상태 확인
curl http://localhost/nginx-status
```

## 🔄 업데이트 및 유지보수

### 애플리케이션 업데이트
```bash
# 새 버전 배포
git pull origin main
./scripts/deploy.sh

# 롤백 (필요시)
docker compose -f docker-compose.prod.yml down
git checkout previous-version
./scripts/deploy.sh
```

### 데이터베이스 백업
```bash
# SQLite 백업 (기본 설정)
docker compose -f docker-compose.prod.yml exec app cp /var/www/database/database.sqlite /var/www/storage/backup/

# MySQL 백업 (사용하는 경우)
docker compose -f docker-compose.prod.yml exec db mysqldump -u username -p database_name > backup.sql
```

### SSL 인증서 갱신
```bash
# Let's Encrypt 자동 갱신 확인
sudo certbot renew --dry-run

# 수동 갱신 (필요시)
sudo certbot renew
docker compose -f docker-compose.prod.yml restart webserver
```

## 🚨 문제 해결

### 일반적인 문제

1. **502 Bad Gateway**
   ```bash
   # PHP-FPM 컨테이너 상태 확인
   docker compose -f docker-compose.prod.yml logs app

   # 컨테이너 재시작
   docker compose -f docker-compose.prod.yml restart app
   ```

2. **SSL 인증서 오류**
   ```bash
   # 인증서 파일 확인
   ls -la docker/ssl/

   # 인증서 유효성 확인
   openssl x509 -in docker/ssl/cert.pem -text -noout
   ```

3. **높은 메모리 사용량**
   ```bash
   # OPcache 상태 확인
   docker compose -f docker-compose.prod.yml exec app php -i | grep opcache

   # 캐시 클리어
   docker compose -f docker-compose.prod.yml exec app php artisan cache:clear
   ```

### 로그 분석
```bash
# 에러 로그 확인
docker compose -f docker-compose.prod.yml exec app tail -f /var/log/php_errors.log

# Nginx 접근 로그 분석
docker compose -f docker-compose.prod.yml exec webserver tail -f /var/log/nginx/access.log

# 애플리케이션 로그
docker compose -f docker-compose.prod.yml exec app tail -f storage/logs/laravel.log
```

## 📞 지원

배포 관련 문제가 있으시면:

1. 로그를 확인하여 오류 메시지를 찾아보세요
2. [GitHub Issues](https://github.com/your-repo/issues)에 문제를 보고해주세요
3. 보안 관련 문제는 이메일로 직접 연락해주세요

## 🏆 성능 최적화

### 추가 최적화 옵션

1. **CDN 설정**: 정적 파일을 CDN으로 서빙
2. **캐시 계층**: Redis 클러스터 구성
3. **로드 밸런서**: 다중 인스턴스 배포
4. **데이터베이스 최적화**: MySQL/PostgreSQL로 마이그레이션

자세한 최적화 가이드는 별도 문서를 참조하세요.
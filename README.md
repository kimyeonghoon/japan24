# 🏯 Japan24 - 일본 24명성 인증 시스템

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?logo=docker&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)
![Security](https://img.shields.io/badge/Security-Hardened-red.svg)

일본의 유명한 24개 성을 방문하고 인증할 수 있는 웹 애플리케이션입니다. GPS 위치 인증, 사진 업로드, 스탬프 수집 등을 통해 성 방문을 기록하고 배지를 획득할 수 있습니다.

## ✨ 주요 기능

### 🏰 **핵심 기능**
- **GPS 위치 인증**: 실제 성 위치에서만 인증 가능 (정확도 100m)
- **사진 인증**: 성 사진 3장 + 스탬프 사진 1장 업로드
- **배지 시스템**: 방문 횟수에 따른 6단계 성취 시스템
- **진행률 추적**: 24개 성 완주 진행률 실시간 표시
- **지도 연동**: OpenStreetMap 기반 대화형 지도

### 👥 **소셜 기능**
- **친구 시스템**: 친구 추가/관리 및 방문 기록 공유
- **소셜 피드**: 친구들의 최신 방문 기록 타임라인
- **좋아요 시스템**: 방문 기록에 좋아요 및 댓글
- **실시간 알림**: 친구 요청, 좋아요, 배지 획득 알림

### 🔧 **관리 기능**
- **관리자 대시보드**: 방문 기록 승인/거부, 사용자 관리
- **통계 시스템**: 인기 성, 활성 사용자, 완주율 분석
- **모니터링**: 실시간 성능 모니터링 및 로깅

## 🚀 빠른 시작

### 전제 조건
- Docker & Docker Compose
- PHP 8.2+ (로컬 개발용)
- Git

### 1. 저장소 클론
```bash
git clone https://github.com/your-username/japan24.git
cd japan24
```

### 2. 환경 설정
```bash
# 환경 변수 설정
cp .env.example .env

# 애플리케이션 키 생성
php artisan key:generate

# 의존성 설치
composer install
```

### 3. 개발 환경 실행
```bash
# Docker 개발 환경 시작
docker compose up -d

# 데이터베이스 마이그레이션 및 시딩
docker compose exec app php artisan migrate:fresh --seed
```

### 4. 접속
- **웹 애플리케이션**: http://localhost:8000
- **관리자 페널**: http://localhost:8000/admin

## 🏭 프로덕션 배포

### 원클릭 배포
```bash
# 프로덕션 환경 설정
cp .env.production.example .env.production
# .env.production 파일 편집 필요

# SSL 인증서 생성 (개발용)
./scripts/generate-ssl.sh

# 프로덕션 배포 실행
./scripts/deploy.sh
```

### 모니터링 설정
```bash
# Prometheus + Grafana 모니터링 스택 설치
./scripts/monitoring-setup.sh

# 접속 정보
# Grafana: http://localhost:3000 (admin/admin123)
# Prometheus: http://localhost:9090
```

자세한 배포 가이드는 [README-DEPLOYMENT.md](README-DEPLOYMENT.md)를 참조하세요.

## 📊 성능 지표

- **🚀 쿼리 성능**: 캐시 시스템으로 93.6% 성능 향상
- **🔒 보안**: Rate Limiting, CSRF 보호, XSS 방지
- **📱 반응형**: Bootstrap 5 기반 모바일 친화적 UI
- **⚡ 최적화**: 이미지 압축, Gzip, HTTP/2 지원

## 🛠️ 기술 스택

### Backend
- **Framework**: Laravel 12 (PHP 8.2)
- **Database**: SQLite (개발) / MySQL/PostgreSQL (프로덕션)
- **Cache**: Redis
- **Queue**: Redis/Database

### Frontend
- **UI Framework**: Bootstrap 5
- **Map**: OpenStreetMap + Leaflet.js
- **Icons**: Bootstrap Icons

### DevOps
- **Containerization**: Docker + Docker Compose
- **Web Server**: Nginx (프로덕션)
- **SSL**: Let's Encrypt 지원
- **Monitoring**: Prometheus + Grafana

## 📚 문서

- [🚀 배포 가이드](README-DEPLOYMENT.md)
- [🔒 보안 가이드](SECURITY.md)

## 🤝 기여하기

1. 저장소를 포크합니다
2. 기능 브랜치를 생성합니다 (`git checkout -b feature/amazing-feature`)
3. 변경사항을 커밋합니다 (`git commit -m 'Add amazing feature'`)
4. 브랜치에 푸시합니다 (`git push origin feature/amazing-feature`)
5. Pull Request를 생성합니다

### 개발 가이드라인
- PSR-12 코딩 표준 준수
- 모든 코드에 한글 주석 작성
- 보안 취약점 사전 검토

## 🏆 배지 시스템

| 배지 | 필요 방문 횟수 | 설명 |
|------|----------------|------|
| 🏯 초보자 | 1회 | 첫 번째 성 방문 |
| 🏰 성 순례 입문 | 3회 | 성 탐방 시작 |
| 🏯 성 애호가 | 8회 | 진정한 성 애호가 |
| 👑 성 마스터 | 15회 | 성 전문가 수준 |
| 🎌 성 박사 | 20회 | 성 연구자 수준 |
| ⭐ 성 컴플리트 | 24회 | 모든 성 정복 |

## 📄 라이센스

이 프로젝트는 MIT 라이센스 하에 배포됩니다.

## 🔒 보안

보안 취약점을 발견하시면 [SECURITY.md](SECURITY.md)의 가이드라인에 따라 신고해주세요.

---

**🏯 일본의 아름다운 성들을 함께 탐험해보세요!**

Made with ❤️ by Japan24 Development Team
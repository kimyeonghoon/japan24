# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

The "Japan24" project is a web application for authenticating visits to 24 famous Japanese castles. Users can verify their visits through GPS, photo authentication, and stamp collection, earning badges for their achievements.

## Technology Stack

- **Backend**: PHP 8.2 with Laravel 12
- **Database**: SQLite
- **Frontend**: Bootstrap 5 with Blade templates
- **Development Environment**: Docker with nginx and PHP-FPM
- **Language**: Korean UI with English code

## Project Structure

### Core Models
- `Castle`: Represents the 24 Japanese castles with GPS coordinates, descriptions, and visit information
- `User`: Standard Laravel authentication with visit tracking and badge relationships
- `VisitRecord`: Tracks user visits with GPS verification, photos, and approval status
- `Badge` & `UserBadge`: Achievement system based on visit count

### Key Controllers
- `DashboardController`: User dashboard with progress tracking
- `CastleController`: Castle listings and details
- `VisitRecordController`: Visit authentication and record management
- `AuthenticatedSessionController` & `RegisteredUserController`: User authentication

### Database Seeding
- 24 famous Japanese castles with accurate GPS coordinates and descriptions
- 6 achievement badges (초보자, 성 순례 입문, 성 애호가, 성 마스터, 성 박사, 성 컴플리트)

## Development Commands

### Docker Commands
- `docker compose up -d`: Start development environment on http://localhost:8000
- `docker compose build`: Build Docker containers
- `docker compose exec app php artisan [command]`: Run Laravel artisan commands

### Laravel Commands
- `php artisan migrate:fresh --seed`: Reset database with fresh data
- `php artisan config:clear && php artisan cache:clear`: Clear Laravel caches

## Key Features Implemented

### User Authentication
- Registration and login with Laravel's built-in auth
- Simple template system using Bootstrap CDN

### Castle Management
- Complete 24-castle database with Korean and Japanese names
- GPS coordinates for location verification
- Google Maps integration and access methods
- Official stamp collection locations

### Interactive Map System
- OpenStreetMap-based interactive castle map
- Custom castle markers with real castle images (5 castles implemented)
- Popup displays with castle photos and visit authentication links
- Responsive design with mobile support

### Castle Image System
- Real castle photos for map markers and popups
- Two image sizes: marker icons (48x30) and detail view (320x200)
- Currently implemented for: 고료카쿠, 히로사키성, 오다와라성, 에도성, 아이츠와카마츠성
- Fallback emoji icons (🏰) for castles without custom images

### Visit Authentication System
- GPS-based location verification (200m radius)
- Photo upload requirement (3 castle photos)
- Optional stamp booklet photo verification
- Automatic approval system (can be enhanced with manual review)

### Badge System
- Automatic badge awarding based on verified visit count
- 6 progressive achievement levels
- User dashboard showing badge progress

### Dashboard Features
- Visit progress tracking with percentage completion
- Recent visits display
- Statistics overview (visited/pending/badges)
- Quick action buttons

## Template System

The project uses a simple Blade template system:
- `layouts/simple.blade.php`: Basic Bootstrap layout
- `auth/simple-login.blade.php`: Login form
- Complex templates in `layouts/app.blade.php` and other views (note: may have rendering issues with certain Laravel packages)

## Known Issues

1. **Blade Template Rendering**: Complex templates with syntax highlighting may cause issues due to Phiki package conflicts
2. **Frontend Dependencies**: Currently uses CDN for Bootstrap and icons instead of NPM/Vite build process
3. **Development Environment**: Node.js not included in Docker container - use CDN resources for frontend assets
4. **Castle Image Issues**:
   - 고료카쿠 이미지가 지도에서 표시되지 않는 문제 (데이터베이스에 image_url이 설정되지 않음)
   - 나머지 19개 성의 이미지 미구현

## Development Notes

- Korean language used for UI text and documentation
- All castle data includes both Korean and Japanese names
- GPS coordinates are real and accurate for authentication
- Database uses SQLite for simplicity but can be changed via Laravel config
- Bootstrap 5 provides responsive design without custom CSS compilation
- Simple authentication flow without email verification

## Future Enhancements

Areas identified for potential development:
- **Castle Image System**: 나머지 19개 성의 실제 이미지 추가 및 고료카쿠 이미지 문제 해결
- Photo validation using AI/ML for castle recognition
- OCR for stamp booklet verification
- Social features for sharing achievements
- Mobile app development
- Enhanced admin panel for manual visit approval

## 관리자 계정 관리

### 데이터베이스 초기화 시 관리자 생성
데이터베이스가 초기화되어도 관리자 계정에 접근할 수 있도록 다음 방법들이 구현되어 있습니다:

#### 1. 자동 시더링 (권장)
```bash
php artisan db:seed
```
- DatabaseSeeder에서 자동으로 기본 관리자 계정 생성
- 환경변수로 커스터마이징 가능

#### 2. 아티즌 명령어
```bash
php artisan admin:create
php artisan admin:create --email=custom@email.com --password=newpass123
```

#### 3. 환경변수 설정
`.env` 파일에서 기본 관리자 정보 설정:
```
ADMIN_NAME="관리자"
ADMIN_EMAIL="admin@japan24.com"
ADMIN_PASSWORD="admin123"
```

### 시스템 설정 관리
- 관리자는 `/admin/settings`에서 회원가입 허용/차단 설정 가능
- 회원가입 차단 시 로그인 페이지에서 회원가입 링크 자동 숨김
- SystemSetting 모델로 확장 가능한 설정 시스템

## 보안 시스템

### 브루트포스 공격 방어
다층 보안 시스템으로 악의적인 로그인 시도를 차단:

#### 1. Rate Limiting
- **IP별 제한**: 1분에 5번 로그인 시도 허용
- **이메일별 제한**: 1분에 3번 로그인 시도 허용
- **자동 증가 제한**: 실패할 때마다 대기 시간 증가

#### 2. 자동 IP 차단
- **과도한 요청**: 1시간에 500회 이상 요청 시 1시간 차단
- **로그인 페이지 집중 공격**: 10분에 50회 이상 접근 시 30분 차단
- **실패 기록 누적**: 지속적인 실패 시도 시 추가 제재

#### 3. 보안 로그 시스템
- 모든 로그인 실패 기록 저장
- 관리자 로그인 활동 추적
- 의심스러운 활동 자동 알림

#### 4. 관리자 보안 대시보드 (`/admin/security`)
- 실시간 차단된 IP 목록 조회
- IP 수동 차단/해제 기능
- 최근 로그인 실패 기록 모니터링
- 보안 통계 및 설정 현황

### 보안 기능 사용법
```bash
# 로그 모니터링
tail -f storage/logs/laravel.log | grep "로그인\|브루트포스\|차단"

# 차단된 IP 수동 확인 (Redis/Cache)
php artisan tinker
Cache::get('blocked_ip:192.168.1.100')
```

## Current Development Tasks

### Completed Features ✅
1. **고료카쿠 이미지 수정**: 데이터베이스에 image_url 설정 완료
2. **관리자 계정 자동 생성**: DB 초기화 시에도 관리자 접근 보장
3. **회원가입 허용/차단 시스템**: 관리자가 제어 가능한 회원가입 정책
4. **SQLite 호환성**: DATE_FORMAT → strftime 변경으로 SQLite 지원
5. **브루트포스 공격 방어**: 다층 Rate Limiting 및 자동 IP 차단 시스템
6. **보안 모니터링 대시보드**: 실시간 위협 탐지 및 관리 기능

### Future Development
1. **나머지 19개 성 이미지 추가**: 각 성의 대표 이미지를 수집하고 마커/팝업에 적용

### Image File Structure
- `public/images/castles/`: 원본 이미지 파일들 (팝업용, 320x200 크기)
- `public/images/markers/`: 지도 마커용 이미지들
  - `{castle_name}.png`: 마커용 작은 이미지 (48x30)
  - `{castle_name}_aspect.png`: 팝업용 큰 이미지 (320x200)

### Database Structure
- Castle 모델에 `image_url` 필드 사용
- image_url이 설정된 성들은 실제 이미지로 표시
- image_url이 null인 성들은 🏰 이모지로 표시
- SystemSetting 테이블로 시스템 설정 중앙 관리
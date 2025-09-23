# 🔒 Japan24 보안 가이드

## 🚨 중요 보안 공지

이 저장소를 포크하거나 배포하기 전에 반드시 다음 보안 조치를 취하세요.

## 📋 배포 전 필수 보안 체크리스트

### ✅ **환경 설정 보안**
- [ ] `APP_KEY` 새로 생성 (`php artisan key:generate`)
- [ ] `.env` 파일 절대 커밋하지 않음
- [ ] 프로덕션용 강력한 비밀번호 설정
- [ ] `APP_DEBUG=false` 설정 (프로덕션)

### ✅ **데이터베이스 보안**
- [ ] SQLite 파일 Git에서 제외 확인
- [ ] 프로덕션 DB 접속 정보 보안
- [ ] 백업 파일 보안 관리

### ✅ **파일 시스템 보안**
- [ ] 업로드 디렉토리 권한 설정
- [ ] 로그 파일 외부 접근 차단
- [ ] SSL 인증서 개인 키 보호

### ✅ **웹 서버 보안**
- [ ] Nginx/Apache 보안 헤더 설정
- [ ] Rate Limiting 적용
- [ ] HTTPS 강제 적용

## 🔐 중요 파일 보안 관리

### 절대 공개하면 안 되는 파일들:
```
.env                     # 환경 변수 (APP_KEY, DB 정보)
database/database.sqlite # SQLite 데이터베이스
storage/logs/*.log       # 애플리케이션 로그
docker/ssl/private.key   # SSL 개인 키
composer.phar           # Composer 실행 파일
```

### 안전한 파일들:
```
.env.example            # 환경 변수 템플릿 (OK)
.env.production.example # 프로덕션 템플릿 (OK)
docker/ssl/cert.pem     # 자체 서명 인증서 (OK)
```

## 🛡️ 보안 강화 권장사항

### 1. **인증 및 권한**
- 2FA(이중 인증) 활성화 권장
- 정기적인 비밀번호 변경
- 최소 권한 원칙 적용

### 2. **모니터링**
- 이상 접근 패턴 모니터링
- 로그 정기 검토
- 보안 업데이트 적용

### 3. **백업 및 복구**
- 암호화된 백업 보관
- 정기적인 복구 테스트
- 백업 파일 접근 제어

## 📞 보안 문제 신고

보안 취약점을 발견하시면 다음으로 연락주세요:

- **이메일**: security@japan24.example.com
- **GPG 키**: [공개 키 링크]

### 신고 시 포함할 정보:
1. 취약점 상세 설명
2. 재현 단계
3. 영향도 평가
4. 제안 해결책 (선택사항)

## 📚 추가 보안 리소스

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel 보안 모범 사례](https://laravel.com/docs/security)
- [Docker 보안 가이드](https://docs.docker.com/engine/security/)

## 🔄 보안 업데이트 이력

| 날짜 | 업데이트 내용 | 버전 |
|------|---------------|------|
| 2025-09-23 | 초기 보안 가이드 작성 | 1.0.0 |
| | .env 보안 강화 | |
| | SQLite 파일 Git 제외 | |
| | 로그 파일 정리 | |

---

**⚠️ 중요**: 이 가이드를 준수하지 않을 경우 심각한 보안 위험에 노출될 수 있습니다.
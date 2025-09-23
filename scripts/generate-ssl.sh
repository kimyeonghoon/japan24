#!/bin/bash

# Japan24 SSL 인증서 생성 스크립트
#
# 이 스크립트는 Japan24 애플리케이션을 위한 SSL 인증서를 생성합니다.
# 개발 및 테스트 환경에서 사용할 수 있는 자체 서명 인증서를 만듭니다.
#
# 중요 사항:
# - 이 스크립트는 개발/테스트 환경용입니다
# - 프로덕션 환경에서는 Let's Encrypt 또는 공인 CA의 인증서를 사용하세요
# - 브라우저에서 "안전하지 않음" 경고가 표시될 수 있습니다
#
# 사용법:
#   ./scripts/generate-ssl.sh [도메인] [유효기간]
#
# 예시:
#   ./scripts/generate-ssl.sh localhost 365
#   ./scripts/generate-ssl.sh japan24.example.com 730
#
# 매개변수:
#   도메인: SSL 인증서에 포함할 도메인 이름 (기본값: localhost)
#   유효기간: 인증서 유효 기간 (일 단위, 기본값: 365일)
#
# 생성되는 파일:
#   docker/ssl/private.key - 개인 키 (4096비트 RSA)
#   docker/ssl/cert.pem    - SSL 인증서 (X.509 형식)
#
# 보안 고려사항:
# - 개인 키는 절대 외부에 노출하지 마세요
# - 인증서는 지정된 도메인에서만 유효합니다
# - SAN(Subject Alternative Names)을 통해 여러 도메인 지원

# 스크립트 실행 중 오류 발생 시 즉시 중단
set -e

# 설정 변수
SSL_DIR="docker/ssl"                    # SSL 인증서가 저장될 디렉토리
DOMAIN="${1:-localhost}"               # 첫 번째 인수로 도메인 지정, 기본값은 localhost
DAYS="${2:-365}"                       # 두 번째 인수로 유효기간 지정, 기본값은 365일

echo "🔐 $DOMAIN 도메인용 SSL 인증서를 생성합니다 (유효기간: ${DAYS}일)"

# Step 1: SSL 디렉토리 생성
echo "📁 SSL 인증서 저장 디렉토리 생성 중..."
mkdir -p "$SSL_DIR"

# Step 2: 4096비트 RSA 개인 키 생성
echo "🔑 RSA 개인 키 생성 중 (4096비트)..."
echo "ℹ️  보안을 위해 높은 강도의 키를 사용합니다"
openssl genrsa -out "$SSL_DIR/private.key" 4096

# Step 3: 인증서 서명 요청(CSR) 생성
echo "📋 인증서 서명 요청(CSR) 생성 중..."
echo "ℹ️  한국 기준으로 조직 정보를 설정합니다"
openssl req -new -key "$SSL_DIR/private.key" -out "$SSL_DIR/cert.csr" \
    -subj "/C=KR/ST=Seoul/L=Seoul/O=Japan24/OU=IT Department/CN=$DOMAIN"

# Generate self-signed certificate
echo "📋 Generating self-signed certificate..."
openssl x509 -req -days "$DAYS" -in "$SSL_DIR/cert.csr" \
    -signkey "$SSL_DIR/private.key" -out "$SSL_DIR/cert.pem" \
    -extensions v3_req -extfile <(cat <<EOF
[v3_req]
keyUsage = keyEncipherment, dataEncipherment
extendedKeyUsage = serverAuth
subjectAltName = @alt_names

[alt_names]
DNS.1 = $DOMAIN
DNS.2 = www.$DOMAIN
DNS.3 = localhost
IP.1 = 127.0.0.1
EOF
)

# Set proper permissions
chmod 600 "$SSL_DIR/private.key"
chmod 644 "$SSL_DIR/cert.pem"

# Clean up CSR file
rm "$SSL_DIR/cert.csr"

echo "✅ SSL certificates generated successfully!"
echo ""
echo "📁 Certificate files:"
echo "  Private Key: $SSL_DIR/private.key"
echo "  Certificate: $SSL_DIR/cert.pem"
echo ""
echo "⚠️  Note: These are self-signed certificates for development/testing."
echo "   For production, replace with certificates from a trusted CA."
echo ""
echo "🔧 To trust the certificate locally (Chrome/Firefox):"
echo "   1. Open https://$DOMAIN in your browser"
echo "   2. Click 'Advanced' -> 'Proceed to $DOMAIN (unsafe)'"
echo "   3. Or import cert.pem to your browser's trusted certificates"
echo ""
echo "🌐 For production with Let's Encrypt:"
echo "   Use certbot or similar tools to generate trusted certificates"
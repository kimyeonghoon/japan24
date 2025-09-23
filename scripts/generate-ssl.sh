#!/bin/bash

# SSL Certificate Generation Script for Japan24
# This script generates self-signed certificates for development/testing
# For production, use Let's Encrypt or your SSL provider's certificates

set -e

SSL_DIR="docker/ssl"
DOMAIN="${1:-localhost}"
DAYS="${2:-365}"

echo "🔐 Generating SSL certificates for domain: $DOMAIN"

# Create SSL directory if it doesn't exist
mkdir -p "$SSL_DIR"

# Generate private key
echo "📋 Generating private key..."
openssl genrsa -out "$SSL_DIR/private.key" 4096

# Generate certificate signing request
echo "📋 Generating certificate signing request..."
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
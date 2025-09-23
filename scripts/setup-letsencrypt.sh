#!/bin/bash

# Let's Encrypt SSL Setup Script for Japan24 Production
# This script sets up SSL certificates using certbot and Let's Encrypt

set -e

DOMAIN="${1}"
EMAIL="${2}"

if [ -z "$DOMAIN" ] || [ -z "$EMAIL" ]; then
    echo "Usage: $0 <domain> <email>"
    echo "Example: $0 japan24.yourdomain.com admin@yourdomain.com"
    exit 1
fi

echo "🔐 Setting up Let's Encrypt SSL for domain: $DOMAIN"
echo "📧 Admin email: $EMAIL"

# Install certbot if not already installed
if ! command -v certbot &> /dev/null; then
    echo "📦 Installing certbot..."

    # Ubuntu/Debian
    if command -v apt-get &> /dev/null; then
        sudo apt-get update
        sudo apt-get install -y certbot python3-certbot-nginx
    # CentOS/RHEL
    elif command -v yum &> /dev/null; then
        sudo yum install -y epel-release
        sudo yum install -y certbot python3-certbot-nginx
    else
        echo "❌ Please install certbot manually for your system"
        exit 1
    fi
fi

# Stop nginx temporarily
echo "🛑 Stopping nginx for certificate generation..."
docker compose -f docker-compose.prod.yml stop webserver || true

# Generate certificate using standalone mode
echo "📋 Generating Let's Encrypt certificate..."
sudo certbot certonly \
    --standalone \
    --agree-tos \
    --no-eff-email \
    --email "$EMAIL" \
    -d "$DOMAIN" \
    --non-interactive

# Copy certificates to docker directory
echo "📁 Copying certificates to docker directory..."
sudo cp "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" "docker/ssl/cert.pem"
sudo cp "/etc/letsencrypt/live/$DOMAIN/privkey.pem" "docker/ssl/private.key"

# Set proper ownership and permissions
sudo chown $(whoami):$(whoami) docker/ssl/cert.pem docker/ssl/private.key
chmod 644 docker/ssl/cert.pem
chmod 600 docker/ssl/private.key

# Update nginx configuration with correct domain
echo "🔧 Updating nginx configuration..."
sed -i "s/server_name localhost;/server_name $DOMAIN;/g" docker/nginx/production.conf

# Start services
echo "🚀 Starting services with SSL..."
docker compose -f docker-compose.prod.yml up -d

# Set up automatic renewal
echo "⏰ Setting up automatic certificate renewal..."
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer

# Create renewal hook script
sudo tee /etc/letsencrypt/renewal-hooks/deploy/restart-japan24.sh > /dev/null <<EOF
#!/bin/bash
# Copy new certificates to docker directory
cp "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" "$(pwd)/docker/ssl/cert.pem"
cp "/etc/letsencrypt/live/$DOMAIN/privkey.pem" "$(pwd)/docker/ssl/private.key"

# Restart nginx container
cd "$(pwd)"
docker compose -f docker-compose.prod.yml restart webserver
EOF

sudo chmod +x /etc/letsencrypt/renewal-hooks/deploy/restart-japan24.sh

echo ""
echo "✅ Let's Encrypt SSL setup completed successfully!"
echo ""
echo "📋 Certificate information:"
sudo certbot certificates
echo ""
echo "🔄 Certificate will auto-renew. Check renewal status with:"
echo "   sudo certbot renew --dry-run"
echo ""
echo "🌐 Your site should now be available at:"
echo "   https://$DOMAIN"
echo ""
echo "📊 Test SSL configuration at:"
echo "   https://www.ssllabs.com/ssltest/analyze.html?d=$DOMAIN"
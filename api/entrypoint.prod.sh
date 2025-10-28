#!/bin/sh

set -e

echo "🚀 Iniciando aplicação em modo PRODUÇÃO..."

# Aguardar PostgreSQL estar pronto
echo "⏳ Aguardando PostgreSQL..."
until pg_isready -h postgres -U adagri_user; do
  sleep 2
done
echo "✅ PostgreSQL está pronto!"

cd /var/www/html

# Executar migrations (apenas aplicar, não refresh)
echo "📊 Executando migrations..."
php artisan migrate --force

# Cachear configurações para PRODUÇÃO (performance)
echo "⚡ Cacheando configurações..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Link simbólico para storage (se necessário)
if [ ! -L public/storage ]; then
  echo "🔗 Criando link storage..."
  php artisan storage:link
fi

echo "✅ Aplicação pronta para produção!"
echo "🎯 Iniciando PHP-FPM..."

# Iniciar PHP-FPM
exec "$@"

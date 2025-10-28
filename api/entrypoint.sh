#!/bin/sh

set -e

# Aguardar o PostgreSQL estar pronto
until pg_isready -h postgres -U adagri_user; do
  echo "Aguardando PostgreSQL..."
  sleep 2
done

echo "PostgreSQL está pronto!"

# Navegar para o diretório correto
cd /var/www/html

# Instalar dependências do Composer se vendor não existir
if [ ! -d "vendor" ]; then
  echo "Instalando dependências do Composer..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Gerar chave da aplicação se não existir
if ! grep -q "^APP_KEY=" .env 2>/dev/null || [ -z "$(grep "^APP_KEY=" .env | cut -d '=' -f2)" ]; then
  echo "Gerando APP_KEY..."
  php artisan key:generate
fi

# Executar migrations
echo "Executando migrations..."
php artisan migrate

# Limpar e cachear configurações
echo "Otimizando aplicação..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Executar seeds (opcional - remova se não quiser)
# php artisan db:seed

# Iniciar PHP-FPM
echo "Iniciando PHP-FPM..."
exec "$@"

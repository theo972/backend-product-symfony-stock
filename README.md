# Backend Product (Symfony 7) — JWT API, Orders & Items, Mailer, Messenger

Minimal e-commerce API:
- **JWT auth** (LexikJWT) with JSON-login firewall.
- **Product** catalog.
- Clean order model: **Order ↔ OrderItem ↔ Product** (quantity, unit price snapshot).
- **Serializer** (groups) + **custom denormalizer** to create an Order **with multiple items** in a single POST.
- **Mailer** (Twig `TemplatedEmail`) + **Messenger** (async delivery) on order creation.
- Validation (constraints + **validation groups**).
- Code quality: **PSR-12** via PHP-CS-Fixer / PHPCS (Composer scripts).

> References: Symfony Messenger, Mailer, Serializer, Doctrine Migrations, MailDev, and LexikJWT docs.

## Stack & Prerequisites
- PHP 8.2+, Composer
- MySQL/MariaDB
- Symfony CLI (optional)
- Dev SMTP tool: **MailDev** (UI at `http://localhost:1080`, SMTP on `1025`). :contentReference[oaicite:1]{index=1}

## Quick Start

```bash
# Install dependencies
composer installs

# Local env
cp .env .env.local
# Edit .env.local:
# DATABASE_URL="mysql://user:pass@127.0.0.1:3306/db?serverVersion=mariadb-10.11"
# JWT_PASSPHRASE=changeme
# MAILER_DSN=smtp://localhost:1025
# MESSENGER_TRANSPORT_DSN=doctrine://default

# Database
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate -n

# JWT keys (LexikJWT)
php bin/console lexik:jwt:generate-keypair

# Messenger (async queue)
php bin/console messenger:setup-transports
php bin/console messenger:consume async -vv

# franken-railway

A production-ready Symfony 7 boilerplate powered by FrankenPHP, with Mercure real-time updates and async messaging via Redis — ready to deploy on Railway in minutes.

## Features

- **FrankenPHP** — PHP application server with Caddy, worker mode enabled
- **Mercure** — Real-time push via Server-Sent Events, built into FrankenPHP
- **Vulcain** — HTTP/2 server push, built into FrankenPHP
- **Symfony Messenger** — Async message processing with Redis transport
- **JMS Serializer** — DTO-based serialization with groups (`list`, `detail`)
- **Doctrine ORM** — PostgreSQL with auto-migrations on boot
- **API Documentation** — OpenAPI spec via NelmioApiDocBundle, rendered with RapiDoc
- **Validation** — Symfony Validator on request DTOs with structured error responses
- **CI Pipeline** — GitHub Actions with CS Fixer, PHPStan (level 6), PHPUnit

## Quick Start (Local)

```bash
git clone https://github.com/Lazy-Bogdi/franken-railway.git
cd franken-railway
docker compose up -d --build --wait
```

That's it. The app is running at `https://localhost`.

- **API docs**: https://localhost/api/doc
- **Health check**: https://localhost/api/health
- **CRUD example**: https://localhost/api/articles

To run the Messenger worker locally:

```bash
docker compose exec php php bin/console messenger:consume async -vv
```

## Deploy on Railway

1. Create a new project on [Railway](https://railway.com)

2. Add services:
   - **PostgreSQL** — Add plugin from Railway dashboard
   - **Redis** — Add plugin from Railway dashboard
   - **Web** — Add service from GitHub repo (`Lazy-Bogdi/franken-railway`)
   - **Worker** — Add service from same GitHub repo, override start command:
     ```
     php bin/console messenger:consume async --time-limit=3600 --memory-limit=128M
     ```

3. Configure shared variables (accessible by both web and worker):

   | Variable | Value |
   |----------|-------|
   | `DATABASE_URL` | `${{Postgres.DATABASE_URL}}` |
   | `REDIS_URL` | `${{Redis.REDIS_URL}}` |
   | `MESSENGER_TRANSPORT_DSN` | `${{Redis.REDIS_URL}}` |
   | `APP_SECRET` | Generate a random string |
   | `APP_ENV` | `prod` |
   | `MERCURE_JWT_SECRET` | Generate a random string |

4. Configure web-only variables:

   | Variable | Value |
   |----------|-------|
   | `MERCURE_URL` | `http://localhost/.well-known/mercure` |
   | `MERCURE_PUBLIC_URL` | `https://<your-app>.up.railway.app/.well-known/mercure` |
   | `TRUSTED_PROXIES` | `REMOTE_ADDR` |

5. Deploy. Migrations run automatically on boot.

## Environment Variables

| Variable | Required | Web | Worker | Description |
|----------|----------|-----|--------|-------------|
| `DATABASE_URL` | Yes | x | x | PostgreSQL connection string |
| `REDIS_URL` | Yes | x | x | Redis connection string |
| `MESSENGER_TRANSPORT_DSN` | Yes | x | x | Messenger transport (same as `REDIS_URL`) |
| `APP_SECRET` | Yes | x | x | Symfony application secret |
| `APP_ENV` | Yes | x | x | `prod` in production |
| `MERCURE_JWT_SECRET` | Yes | x | x | HMAC secret for Mercure JWT signing |
| `MERCURE_URL` | Yes | x | x | Internal hub URL. Web: `http://localhost/.well-known/mercure`. Worker: use Railway internal networking |
| `MERCURE_PUBLIC_URL` | Yes | x | | Public hub URL for browser SSE connections |
| `CORS_ALLOW_ORIGIN` | No | x | | Allowed CORS origins (regex) |
| `TRUSTED_PROXIES` | Yes | x | | `REMOTE_ADDR` behind Railway proxy |
| `TRUSTED_HOSTS` | No | x | | Allowed hostnames (regex) |

## Architecture

### Request flow

```
Client → Caddy (:{$PORT}) → FrankenPHP (worker mode) → Symfony
```

### Real-time updates (Mercure)

```
Symfony Controller → flush() → Doctrine postFlush
  → dispatch(MercureUpdateMessage) → Redis
  → Worker: messenger:consume → MercureUpdateHandler
  → hub->publish(Update) → Mercure hub (SSE)
  → Browser EventSource receives update
```

### Two Railway services, one repo

| Service | Start command | Role |
|---------|--------------|------|
| **Web** | Default Dockerfile CMD | FrankenPHP + Caddy + Mercure hub |
| **Worker** | `php bin/console messenger:consume async` | Processes messages from Redis |

Both services use the same Docker image. Railway restarts the worker automatically on exit (`restartPolicyType: ON_FAILURE`).

### Project structure

```
src/
├── Controller/
│   ├── AbstractApiController.php  # deserialize, validate, jsonResponse
│   ├── ArticleController.php      # CRUD example
│   ├── ApiDocController.php       # RapiDoc UI
│   └── HealthController.php       # Railway healthcheck
├── Dto/
│   ├── Request/                   # Input DTOs with validation
│   └── Response/                  # Output DTOs with JMS groups
├── Entity/
│   └── Article.php
├── EventListener/
│   ├── ArticleMercureListener.php # Doctrine → Messenger dispatch
│   └── ExceptionListener.php      # Uniform JSON errors
├── Message/
│   └── MercureUpdateMessage.php
└── MessageHandler/
    └── MercureUpdateHandler.php   # Messenger → Mercure publish
```

## Listening to real-time updates (client-side)

```javascript
const url = new URL('/.well-known/mercure', window.location.origin);
url.searchParams.append('topic', '/api/articles/{id}');

const es = new EventSource(url);
es.onmessage = (event) => {
    const data = JSON.parse(event.data);
    console.log(data.action, data.data);
};
```

## Quality tools

```bash
# Lint
docker compose exec php vendor/bin/php-cs-fixer fix --dry-run --diff

# Static analysis
docker compose exec php php -d memory_limit=512M vendor/bin/phpstan analyse

# Tests
docker compose exec php vendor/bin/phpunit
```

## Customization

### Adding a new entity + CRUD

1. Create entity in `src/Entity/`
2. Create request/response DTOs in `src/Dto/`
3. Create controller extending `AbstractApiController`
4. Generate migration: `php bin/console doctrine:migrations:diff`
5. The `ArticleMercureListener` shows how to add real-time updates for your entity

### Switching Messenger to sync (no worker needed)

In `config/packages/messenger.yaml`, remove the `async` transport and the routing:

```yaml
framework:
    messenger:
        transports: {}
        routing: {}
```

Messages will be handled immediately in the HTTP request.

## License

MIT

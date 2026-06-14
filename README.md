# Notification Service

A microservice for sending bulk SMS and Email notifications with priority queuing, delivery tracking, and guaranteed at-least-once delivery.

---

## Running Locally

**Requirements:** Docker, Docker Compose

```bash
cp .env.example .env
docker compose up -d
docker compose exec app php artisan key:generate
```

The API will be available at `http://localhost:8000`.
RabbitMQ Management UI — `http://localhost:15672` (guest / guest).
Swagger UI — `http://localhost:8000/api/documentation`.

Two queue workers start automatically as separate Docker services:
- `worker_high` — processes transactional notifications (OTP codes, critical alerts)
- `worker_standard` — processes marketing notifications (bulk campaigns)

No manual setup required — migrations and seeders run on container start.

To run tests:
```bash
docker compose exec app php artisan test
```

To run static analysis:
```bash
docker compose exec app ./vendor/bin/phpstan analyse --memory-limit=512M
```

To check code style:
```bash
docker compose exec app ./vendor/bin/pint --test
```

To fix code style:
```bash
docker compose exec app ./vendor/bin/pint
```

---

## Architecture

### Layered Architecture
The codebase follows a strict layered structure: Controllers → Services → Repositories → Models. Each layer knows only about the next one. Controllers handle HTTP, services orchestrate business logic, repositories own all database queries, models define structure and relationships.

### Repository Pattern
All database queries live in repository classes behind interfaces (`NotificationRepositoryInterface`, `SubscriberRepositoryInterface`). Services depend on interfaces, not implementations — swapping or mocking a repository requires changing one binding in `AppServiceProvider`.

### DTOs
Input data is wrapped in DTOs (`SendNotificationDTO`, `SubscriberNotificationsDTO`) before reaching the service layer. Providers and jobs communicate through `NotificationPayload` and `ProviderResult`. No raw arrays or primitive soup passed between layers.

### Two-queue Priority System
Transactional and marketing notifications go into separate RabbitMQ queues (`notifications.transactional`, `notifications.marketing`). Each queue has a dedicated worker with different retry delays — 5s for transactional, 30s for marketing. Critical messages are never blocked by bulk campaigns.

### Guaranteed Delivery
At-least-once delivery is provided by RabbitMQ — messages are not acknowledged until the job completes successfully. Exactly-once semantics are enforced at the business logic level through three layers:

1. **Final status check** — if a recipient is already `delivered` or `discarded`, duplicate jobs are skipped immediately.
2. **Redis distributed lock** — prevents two workers from processing the same recipient simultaneously.
3. **Unique DB constraint** — `(notification_id, subscriber_id)` makes duplicates impossible at the database level.

### Strategy Pattern for Channels
Each channel (`sms`, `email`) is a separate class implementing `NotificationProviderInterface`. A `ProviderFactory` resolves the correct provider by channel. Adding a new channel means writing one class and registering it in `AppServiceProvider` — nothing else changes.

### Idempotency
Clients can pass an `X-Idempotency-Key` header with any request. If the same key is seen again, the original response is returned from cache without creating a new notification. The key is stored in PostgreSQL (survives restarts) and expires after 24 hours.

### Retry with Exponential Backoff
Failed jobs retry with exponential backoff: `5s → 25s → 125s → 300s`. After all attempts are exhausted, the recipient is marked `discarded` with the failure reason stored in the database.

### Native PostgreSQL Enums
`notification_type` and `notification_status` are real PostgreSQL `ENUM` types — not `VARCHAR` with a `CHECK` constraint. Type safety at the database level, cleaner schema dumps.

---

## API

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/v1/notifications` | Start a bulk send |
| `GET` | `/api/v1/notifications/{id}` | Notification status + recipient breakdown |
| `GET` | `/api/v1/subscribers/{id}/notifications` | Subscriber delivery history |
| `GET` | `/api/v1/subscribers/{id}/notifications/{id}` | Single notification status for subscriber |

Full interactive documentation available at `http://localhost:8000/api/documentation`.

### Send a notification

```bash
curl -X POST http://localhost:8000/api/v1/notifications \
  -H "Content-Type: application/json" \
  -H "X-Idempotency-Key: your-unique-key" \
  -d '{
    "channel": "sms",
    "type": "transactional",
    "message": "Your confirmation code: 1234",
    "subscriber_ids": ["user_1", "user_2", "user_3"]
  }'
```

### Delivery statuses

| Status | Meaning |
|--------|---------|
| `queued` | Accepted, waiting to be sent |
| `sent` | Passed to the provider |
| `delivered` | Confirmed by the provider |
| `discarded` | Permanent failure — invalid number/email or retries exhausted |

---

## What Would Be Improved for Production

**Authentication.** The API has no auth. In production every request should be authenticated (API key or JWT) and scoped to a tenant.

**Observability.** Add correlation IDs to trace a notification from API request through queue to provider. Export delivery rate and error rate per channel to Prometheus or Datadog.

**Rate limiting.** No protection against a single caller flooding the queue. A per-client rate limit on the send endpoint would be the first line of defense.

**Horizontal scaling.** Workers are stateless — scaling means adding more `worker_high` / `worker_standard` containers. Redis locks already handle concurrent processing correctly.

**Real providers.** Swap mock classes for real SDK clients (Twilio for SMS, SendGrid for email) by implementing `NotificationProviderInterface`. Nothing else changes.

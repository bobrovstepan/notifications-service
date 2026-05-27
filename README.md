# Notifications Service

A microservice for sending notifications (email, Telegram, etc.) and generating reports on notification activity.

---

## Running Locally

**Requirements:** Docker, Docker Compose

```bash
cp .env.example .env
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan migrate --seed
```

The API will be available at `http://localhost:8080`.

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

### Repository Pattern + Service Layer
Business logic lives in services, data access is behind repository interfaces. Controllers stay thin — they validate input and return responses. This makes it easy to swap implementations (e.g., switch from Eloquent to a raw query builder) without touching business logic.

### Strategy Pattern for Channels
Each notification channel (`email`, `telegram`) is a separate class implementing `ChannelHandlerInterface`. A factory resolves the correct handler by channel name. Adding a new channel means adding one class — no existing code changes.

### Queue-based Processing
Notifications and reports are processed asynchronously via jobs. Jobs have retry logic (`tries=3`, backoff `30s/60s`) and a `failed()` hook that marks the record as failed in the database. This ensures delivery attempts are tracked and failures are visible. A scheduled cron command retries failed jobs periodically via `queue:retry all`.

### API Versioning
All routes are prefixed with `/api/v1/`. This allows breaking changes in future versions without affecting existing clients.

### DTOs and Resources
Input data is wrapped in DTOs (`NotificationData`, `ReportData`) before reaching the service layer — this decouples HTTP request structure from the domain. API responses are shaped by Resource classes (`NotificationResource`, `ReportResource`), keeping response format concerns out of models and controllers.

### Avoiding Primitive Types
Raw primitives (`string`, `int`) are replaced with typed objects where possible — enums for statuses (`NotificationStatus`, `ReportStatus`, `ChannelName`) and DTOs for grouped data. This prevents passing values in the wrong order, makes invalid states unrepresentable, and shifts errors to compile time rather than runtime.

### Dedicated Storage Disk
Reports are stored on a dedicated `reports` disk (configured in `config/filesystems.php`) separate from the default `local` disk. This isolates report files from other application storage, makes it trivial to swap the underlying driver (e.g., to S3) without touching application code, and keeps the storage root configurable per environment.

### Service Container Bindings
Repository interfaces and the report generator interface are bound to their implementations in `AppServiceProvider`. No class depends on a concrete implementation — only on the interface. This makes it easy to swap implementations (e.g., replace `NotificationRepository` with a cached version) in one place without touching any other code.

### Event-driven Dispatch
Controllers fire events (`NotificationCreated`, `ReportRequested`) rather than dispatching jobs directly. Listeners handle job dispatch. This decouples the HTTP layer from queue logic — adding a new reaction to an event (e.g., sending a webhook) means adding a listener, not modifying the controller.

### Query Filters
Filtering logic for listing notifications is encapsulated in `NotificationQueryFilter`, with filter parameters transported via `NotificationFilterDTO`. This keeps the repository method clean and makes it easy to add or remove filters without touching the query itself.

### Partial DDD Influence
The codebase is organized around domain concepts: `Notification`, `Report`, `Channel`. Each has its own model, repository, DTO, and resource. Not strict DDD, but the boundaries are clear enough to extract into separate services if needed.

---

## What Would Be Improved for Production

**Replace database queue with a message broker.** The current setup uses Laravel's database queue driver — fine for development, but not for production scale. RabbitMQ or Kafka would give proper durability, backpressure, and fan-out to multiple consumers.

**Expand test coverage.** Currently only the most critical paths are covered (happy path, job failure hooks). Production requires full coverage: all validation rules, edge cases in report generation, retry behavior, concurrent job execution.

**Authentication and authorization.** The API currently has no auth. In production, each request should be authenticated (API key or JWT) and scoped to a tenant/user.

**Structured logging and observability.** Add correlation IDs to trace a notification through queue → job → channel handler. Export metrics (delivery rate, error rate per channel) to Prometheus or Datadog.

**Idempotency.** Retried jobs can send duplicate notifications. A deduplication key on the notifications table and a check before sending would prevent this.

**Report storage.** Currently reports are stored on the local filesystem. In production this should be S3 (or compatible) so reports survive container restarts and are accessible across multiple app instances.

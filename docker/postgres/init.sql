-- Initial database setup runs automatically on first container start
-- The database and user are created via POSTGRES_DB / POSTGRES_USER env vars

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pg_trgm";

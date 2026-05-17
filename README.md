# Thungedawtawya

PHP/MySQL monastery examination and registration system.

## Local run

1. Put the project in your web root.
2. Make sure MySQL is running.
3. Set these environment variables if you are not using the local defaults:
   - `DB_HOST`
   - `DB_PORT`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
4. Open `public/index.php` through Apache.

Default local database settings are in [configuration/db.php](configuration/db.php).

## Database

The current SQL dump in this repo is:

- `mysql_database/thungwetaw_db (8).sql`

Import it into your MySQL database before testing features that need data.

## Deploy to Render

This repo includes:

- `Dockerfile` for Apache + PHP 8.2
- `render.yaml` for a Render web service blueprint
- `health.php` for Render HTTP health checks that do not require a database connection

### Recommended setup

1. Push this repo to GitHub.
2. In Render, create a new Blueprint from the repo. Render will read `render.yaml`.
3. Confirm the service uses Docker and the included `Dockerfile`.
4. Set the required environment variables in Render:
   - `DB_HOST`
   - `DB_PORT`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
5. Use a MySQL-compatible database and import `mysql_database/thungwetaw_db (8).sql`.
6. Deploy the service.

### Important note

This app currently uses MySQL via PDO. Render's managed database offering is PostgreSQL, so either use an external MySQL-compatible provider and copy its credentials into the Render environment variables, or plan a separate migration from MySQL to PostgreSQL.

The Docker service listens on port `80`, so `render.yaml` sets `PORT=80`. The health check path is `/health.php` so deploy health checks can pass even before the MySQL database is fully imported.

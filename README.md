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

### Recommended setup

1. Push this repo to GitHub.
2. In Render, create a new Blueprint or Web Service from the repo.
3. Use the included `Dockerfile`.
4. Set the environment variables:
   - `DB_HOST`
   - `DB_PORT`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
5. Use a MySQL database and import the SQL dump.

### Important note

This app currently uses MySQL via PDO, so your Render web service should connect to MySQL. You can run MySQL on Render or use another MySQL provider, then copy those credentials into the Render environment variables.

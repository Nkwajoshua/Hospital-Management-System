# Vercel Deployment Guide

This project can be deployed to Vercel as an academic prototype using the community `vercel-php` runtime.

## Architecture

```text
Browser
   |
   v
Vercel Edge / Routing
   |
   v
Laravel PHP Function (`api/index.php`)
   |
   v
Managed MySQL database
```

The application remains a single Laravel codebase. Vercel runs Laravel as a serverless PHP function while MySQL must be hosted by a persistent external database service.

## Serverless compatibility

The repository includes:

- `api/index.php` as the Vercel PHP entry point.
- `vercel.json` using `vercel-php@0.7.4` (PHP 8.3).
- Static routing for files under `public/`.
- Cookie-backed sessions so login state does not depend on local session files.
- In-memory application cache.
- `stderr` logging for Vercel runtime logs.
- Laravel cache/view paths redirected to `/tmp`.

## Required Vercel environment variables

Copy the values from `.env.vercel.example` into the Vercel project environment. The following values are mandatory and secret:

- `APP_KEY`
- `DB_HOST`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

Set `APP_URL` to the production Vercel URL after the project is created.

Do not commit these values to GitHub.

## Database requirements

Use a MySQL or MySQL-compatible managed database reachable from Vercel over TLS/public networking. Create an empty database for the HMS and provide its connection details through Vercel environment variables.

After connecting the database, run:

```bash
php artisan migrate --force
```

For the final-year demonstration dataset only:

```bash
php artisan db:seed --class=DemoSeeder --force
```

## Production checklist

1. Deploy the repository to Vercel.
2. Add the required environment variables.
3. Connect the hosted MySQL database.
4. Run migrations against the hosted database.
5. Seed demo data if this deployment is strictly for presentation/testing.
6. Visit `/login` and verify authentication.
7. Test the patient workflow from registration through simulated payment.
8. Keep a local Laravel/MySQL copy as the offline defence fallback.

## Important scope note

This Vercel deployment is intended for an undergraduate project demonstration. The PHP runtime is community maintained, and the system is not being represented as a production hospital platform.

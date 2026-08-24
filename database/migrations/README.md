# Migrations in this repo

swan-api owns every shared table (`users`, `tb_exam`, `inspection_images`, `sessions`). Both
apps point at one Postgres database (`db_swan`) and share a single `migrations` table.

The migrations in this directory exist only for historical/local-dev reasons. Each `up()` is
guarded with `if (Schema::hasTable('<table>')) { return; }` so that running `php artisan migrate`
here never re-creates or mutates a table swan-api already owns.

**Never add a migration here that creates or alters a shared table.** Schema changes for
`users`, `tb_exam`, `inspection_images`, or `sessions` belong in swan-api.

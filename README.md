# 🚢 Ship Inspection Portal

Laravel 12 dashboard connected to external API at `192.168.100.245:8001`.

## Setup

```bash
cp .env.example .env
# Edit .env — set DB credentials and API URL
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

## .env Key Settings
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ship_inspection
DB_USERNAME=postgres
DB_PASSWORD=your_password
EXTERNAL_API_BASE_URL=http://192.168.100.245:8001/api
```

## Features
- ✅ Login via external API (192.168.100.245:8001)
- ✅ Dashboard with charts
- ✅ Inspection Images gallery (with modal viewer)
- ✅ Exam Records with search/filter
- ✅ Vessels grouped view
- ✅ Users management
- ✅ Dark / Light mode toggle
- ✅ Export to Excel & PDF (all tables)
- ✅ Import from CSV/Excel

## Modules
| Route | Module |
|-------|--------|
| `/dashboard` | Overview stats & charts |
| `/images` | Inspection Images |
| `/exams` | Exam Records |
| `/vessels` | Vessel List |
| `/users` | System Users |

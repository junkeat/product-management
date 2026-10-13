# Product Management API

This Laravel API manages products, categories, and suppliers. It supports product search, filters, stock tracking, supplier links, and token-based authentication.

## Requirements

- PHP 8.2 or later
- Composer

## Setup
Add .env file and update it as depending on your setup

Run these commands in the terminal from the project folder:

```powershell
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

The default database is SQLite. To use another database, update the database settings in `.env`.

The seeders add 3 categories, 2 suppliers, 10 products, and a local admin account:

- Email: `admin@example.com`
- Password: `11221122`

Use this account for local development only.

## Products dashboard

Open [http://127.0.0.1:8000/products](http://127.0.0.1:8000/products) to view the product dashboard, including inventory summaries and the complete product list.

## API documentation

Open these URLs after you start the local server:

- Interactive API reference: [http://127.0.0.1:8000/docs/api](http://127.0.0.1:8000/docs/api)
- OpenAPI JSON: [http://127.0.0.1:8000/docs/api.json](http://127.0.0.1:8000/docs/api.json)

Documentation routes are available in the local environment by default.

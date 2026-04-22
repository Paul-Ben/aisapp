# Alven International Schools (AIS) Portal

Welcome to the Alven International Schools (AIS) portal. This application is a comprehensive school management system designed to streamline administrative tasks, enhance communication, and provide a holistic learning environment for students.

## Features

- **Responsive Landing Page**: A modern, interactive landing page for the public.
- **User Authentication**: Secure login and registration systems powered by Laravel Breeze.
- **Roles & Permissions**: Fine-grained access control using Spatie Laravel Permission.
- **Dashboard**: A centralized hub for authenticated users.
- **Newsletter Subscription**: Integrated newsletter signup for parents and stakeholders.

## Tech Stack

- **Framework**: [Laravel 12.x](https://laravel.com)
- **Language**: PHP 8.2+
- **Frontend**: 
    - [Tailwind CSS 4.x](https://tailwindcss.com) (via Vite)
    - [Bootstrap 5.x](https://getbootstrap.com) (Landing Page)
    - [Alpine.js](https://alpinejs.dev)
- **Database**: MySQL / SQLite (configurable)
- **Authentication**: Laravel Breeze
- **Permissions**: Spatie Laravel Permission

## Prerequisites

Before you begin, ensure you have the following installed:
- PHP >= 8.2
- Composer
- Node.js & NPM
- A local database server (MySQL, PostgreSQL, or SQLite)

## Setup Guide

Follow these steps to get the application running on your local machine:

### 1. Clone the Repository

```bash
git clone <repository-url>
cd ais
```

### 2. Install Dependencies

Install PHP and JavaScript dependencies:

```bash
composer install
npm install
```

### 3. Environment Configuration

Copy the example environment file and configure your database:

```bash
cp .env.example .env
```

Open `.env` and update the database settings (`DB_CONNECTION`, `DB_DATABASE`, etc.).

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Run Migrations & Seeders

Create the database tables and seed them with initial data (including permissions):

```bash
php artisan migrate --seed
```

### 6. Build Assets

Compile the frontend assets:

```bash
npm run build
```

### 7. Run the Application

You can use the built-in setup script or start the services individually:

**Option A: Using the custom dev script**
This will start the PHP server, queue listener, and Vite dev server simultaneously:
```bash
composer dev
```

**Option B: Standard Laravel serve**
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`.

## Available Commands

- `composer setup`: Runs a full installation and build process.
- `composer dev`: Starts the full development environment.
- `composer test`: Runs the Pest test suite.
- `npm run dev`: Starts the Vite development server.
- `npm run build`: Compiles assets for production.

## Testing

The project uses [Pest PHP](https://pestphp.com) for testing. To run the tests:

```bash
composer test
```

## License

This project is licensed under the [MIT license](LICENSE).

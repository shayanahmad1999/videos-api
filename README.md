# Laravel Project for Videos API and User Authenticated API

This README will guide you through setting up and running the Laravel project locally.

## Prerequisites

Ensure the following tools are installed on your system:
🔧 Tech Stack:

-   PHP >= 8.4
-   Laravel = 12
-   Laravel Sanctum
-   Composer
-   MySQL or any supported database

## Installation & Setup

Follow the steps below to get started:

```bash
# Clone the repository
git clone https://github.com/shayanahmad1999/videos-api.git
cd videos-api

# Install PHP dependencies
composer install

# Copy and set up the environment configuration
cp .env.example .env

# Generate application key
php artisan key:generate

# Setting up the .env file
After setting up the .env file, please ensure that you have
API_BASE_URL=http://videos-api.test/api/
setup the url with your url

# Run database migrations
php artisan migrate

# Run the development server
php artisan serve

```

# OR Install from scratch

```bash
# 🛠️ Step 1: Install Laravel
composer create-project laravel/laravel laravel-api
cd laravel-api

# Step 1.a Install Sanctum with no Authentication
php artisan install:api

# Step a.1 Configuration in AppServiceProvider
use App\Models\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
}

# Step a.2 API Token Authentication
Models/User.php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
}

# Step a.3 Sanctum Middleware
bootstrap/app.php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->api(prepend: [
        ForceJsonResponse::class,
    ]);
})

# Step a.4 CORS and Cookies
php artisan config:publish cors


#Step 1.b: Install with Breeze Authentication
composer require laravel/breeze --dev
php artisan breeze:install
npm install && npm run dev
php artisan migrate


# Run database migrations
php artisan migrate

# Run the development server
php artisan serve
npm run dev

```

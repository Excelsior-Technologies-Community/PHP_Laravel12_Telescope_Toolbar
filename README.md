# PHP_Laravel12_Telescope_Toolbar

## Project Introduction

PHP_Laravel12_Telescope_Toolbar is a Laravel 12 demonstration project that showcases how to integrate and configure Laravel Telescope along with a custom-built debug toolbar for real-time application monitoring.

Laravel Telescope is an official debugging assistant for Laravel applications. It provides detailed insights into application requests, database queries, logs, jobs, cache operations, events, and more.

In this project, we go beyond basic installation by implementing a custom bottom toolbar that allows developers to quickly access Telescope directly from any application page during local development.

This project is designed for learning purposes and demonstrates modern Laravel 12 structure, middleware registration using bootstrap/app.php, and secure local-only debugging practices.

------------------------------------------------------------------------

## Project Overview

This project demonstrates a complete step-by-step implementation of Laravel Telescope in a Laravel 12 application.

The project includes:

- Fresh Laravel 12 installation
- Telescope package installation and configuration
- Database migration setup
- Authorization control for secure Telescope access
- Custom middleware creation
- Response manipulation to inject a dynamic debug toolbar
- Proper Laravel 12 middleware registration 
- Structured and organized project architecture

The custom debug toolbar appears at the bottom of every HTML page in the local environment and provides a quick link to the Telescope dashboard, making debugging faster and more efficient during development.

------------------------------------------------------------------------

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL
- Laravel 12

------------------------------------------------------------------------

## Step 1: Create Laravel 12 Project

Open terminal and run:

``` bash
composer create-project laravel/laravel PHP_Laravel12_Telescope_Toolbar "12.*"
cd PHP_Laravel12_Telescope_Toolbar
```

Check Laravel version:

``` bash
php artisan --version
```

------------------------------------------------------------------------

## Step 2: Configure Environment

Update `.env` file:

``` env
APP_NAME=PHP_Laravel12_Telescope_Toolbar
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=telescope_toolbar
DB_USERNAME=root
DB_PASSWORD=
```

Run migration:

``` bash
php artisan migrate
```

------------------------------------------------------------------------

## Step 3: Install Laravel Telescope

``` bash
composer require laravel/telescope
```

Install Telescope:

``` bash
php artisan telescope:install
php artisan migrate
```

Start server:

``` bash
php artisan serve
```

Access Telescope:

```bash
http://127.0.0.1:8000/telescope
```
------------------------------------------------------------------------

## Step 4: Understanding Telescope Structure

After installation:

```bash
    app/Providers/TelescopeServiceProvider.php
    config/telescope.php
    database/migrations/xxxx_create_telescope_entries_table.php
```

Important files:

-   TelescopeServiceProvider → Controls authorization
-   config/telescope.php → Controls watchers and configuration

------------------------------------------------------------------------

## Step 5: Secure Telescope (Important)

Open:

```bash
app/Providers/TelescopeServiceProvider.php
```

Modify gate method:

``` php
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user = null) {
            return app()->environment('local');
        });
    }
```

------------------------------------------------------------------------

## Step 6: Create Custom Debug Toolbar

Now we create a simple toolbar injected into layout.

Create middleware:

``` bash
php artisan make:middleware InjectToolbar
```

Open:

```bash
app/Http/Middleware/InjectToolbar.php
```

Add:

``` php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectToolbar
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            app()->environment('local') &&
            $response instanceof Response &&
            str_contains($response->headers->get('Content-Type'), 'text/html')
        ) {
            $toolbar = view('toolbar')->render();

            $content = $response->getContent();

            $content = str_replace(
                '</body>',
                $toolbar . '</body>',
                $content
            );

            $response->setContent($content);
        }

        return $response;
    }
}
```

Register middleware in:

```bash
    bootstrap/app.php
```

Find:

```php
->withMiddleware(function (Middleware $middleware) {
    //
})
```

Modify it like this:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->appendToGroup('web', [
        \App\Http\Middleware\InjectToolbar::class,
    ]);
})
```

------------------------------------------------------------------------

## Step 7: Create Toolbar View

Create file:

```bash
resources/views/toolbar.blade.php
```

Add:

``` html
<div style="
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:#111;
    color:#fff;
    padding:10px;
    font-size:14px;
    z-index:9999;
">
    <strong>Laravel Telescope Toolbar</strong> |

    <a href="/telescope" style="color:#00ff99;">
        Open Telescope
    </a> |

    Time: {{ now() }}
</div>
```

------------------------------------------------------------------------

## Step 8: Create Test Route

Open:

```bash
routes/web.php
```

Add:

``` php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    logger('Testing Telescope Log Entry');
    return "Test Route Working";
});
```

Visit:

```bash
http://127.0.0.1:8000/test
```

Check Telescope dashboard for logs.

------------------------------------------------------------------------

## Output

<img width="1919" height="1032" alt="Screenshot 2026-02-26 103940" src="https://github.com/user-attachments/assets/4accdec2-8b6a-4d8c-a791-8d5f057eef34" />

<img width="1829" height="1086" alt="Screenshot 2026-02-26 104129" src="https://github.com/user-attachments/assets/c9129641-d440-4874-bc54-baad90d44c93" />

<img width="1902" height="1030" alt="Screenshot 2026-02-26 103408" src="https://github.com/user-attachments/assets/495ad3d3-855b-49fa-a8df-ca46a37bc02f" />

------------------------------------------------------------------------

## Project Folder Structure

```
PHP_Laravel12_Telescope_Toolbar/
│
├── app/
│   ├── Http/
│   │   └── Middleware/
│   │       └── InjectToolbar.php
│   └── Providers/
│       └── TelescopeServiceProvider.php
│
├── bootstrap/
│   └── app.php
│
├── config/
│   └── telescope.php
│
├── database/
│   └── migrations/
│
├── public/
├── resources/
│   └── views/
│       └── toolbar.blade.php
│
├── routes/
│   └── web.php
│
├── storage/
├── tests/
├── vendor/
│
├── .env
├── artisan
├── composer.json
└── README.md
```
------------------------------------------------------------------------

Your PHP_Laravel12_Telescope_Toolbar Project is now ready!


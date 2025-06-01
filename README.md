# Laravel DDD API

This project is a Laravel-based RESTful API structured using Domain-Driven Design (DDD) principles. It manages tenants, plans, and clients, and provides clean separation between domain, application, and infrastructure layers.

## Features
- Domain-Driven Design (DDD) architecture
- Repository pattern for data access
- Form Request validation
- Authorization policies
- OpenAPI (Swagger) documentation

## Project Structure
- `app/Domain` — Domain entities, repositories, value objects
- `app/Application` — Use cases, DTOs, application services
- `app/Infrastructure` — HTTP controllers, resources, requests, persistence, providers
- `config/ddd.php` — DDD-specific configuration
- `config/openapi.php` — OpenAPI documentation config

## API Documentation
- OpenAPI annotations are used in controllers
- Documentation is generated and served using [L5 Swagger](https://github.com/DarkaOnLine/L5-Swagger)
- View the docs at: `http://localhost:8000/api/documentation`

## Setup & Run

1. **Clone the repository**
   ```sh
   git clone <your-repo-url>
   cd <project-directory>
   ```

2. **Install dependencies**
   ```sh
   composer install
   cp .env.example .env
   php artisan key:generate
   # Configure your .env (DB, etc.)
   ```

3. **Run migrations**
   ```sh
   php artisan migrate
   ```

4. **(Optional) Seed the database**
   ```sh
   php artisan db:seed
   ```

5. **Serve the application**
   ```sh
   php artisan serve
   # App will be available at http://localhost:8000
   ```

6. **Generate API documentation**
   ```sh
   php artisan l5-swagger:generate
   # View at http://localhost:8000/api/documentation
   ```

7. **Run tests**
   ```sh
   ./vendor/bin/pest
   # or
   php artisan test
   ```

## Notes
- Controllers use Form Requests for validation and Policies for authorization.
- API Resources are used for consistent JSON responses.
- The DDD structure is enforced via service providers and configuration.
- OpenAPI annotations are present in controllers for automatic API docs.

## Useful Commands
- `php artisan l5-swagger:generate` — Regenerate API docs
- `php artisan migrate:fresh --seed` — Reset and seed DB
- `php artisan test` or `./vendor/bin/pest` — Run tests

---

For more details, see the code and comments in each layer.

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

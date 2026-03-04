## News aggregator

This repo contains the implementation of a news aggregator . I am assuming each achievement unlocks a new badge

## Backend Installation & Requirement
Clone the repository and install dependencies 
- Composer (requires 8.2 upwards):
- laravel (requires 11):
- PHP (requires 8.2):

```bash
# Install PHP dependencies
composer install
```

## Environment Configuration

Copy the example environment file and set up the required configurations:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Run migration:
```bash
php artisan migrate
```

Fetch news from various apis:
```bash
php artisan db:seed
```


```bash
./vendor/bin/sail up
```
## Deployment

### Deploying to Production

For production deployment, set up your web server:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```



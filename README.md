# Symfony Elasticsearch Shop

Modern e-commerce platform built with Symfony 8 and Elasticsearch.

## Features

- Product catalog
- Categories
- Shopping cart
- AJAX add-to-cart
- Checkout system
- Order history
- Product search via Elasticsearch
- Pagination
- Sorting
- Docker support
- PostgreSQL

## Stack

- PHP 8.3
- Symfony 8
- PostgreSQL
- Elasticsearch 8
- Docker
- Doctrine ORM
- Twig
- JavaScript (Fetch API)

## Installation

### Clone repository

```bash
git clone https://github.com/vladislavPrikhodko/symfony-elasticsearch-shop.git
cd symfony-elasticsearch-shop
```

### Install dependencies

```bash
composer install
```

### Configure environment

```bash
cp .env.example .env
```

Edit `.env` if needed.

---

### Start Docker containers

```bash
docker compose up -d
```

### Run migrations

```bash
php bin/console doctrine:migrations:migrate
```

### Load fixtures (optional)

```bash
php bin/console doctrine:fixtures:load
```

### Reindex Elasticsearch

```bash
php bin/console app:reindex-products
```

### Run Symfony server

```bash
symfony server:start
```

or

```bash
php -S localhost:8000 -t public
```

---

## Elasticsearch

Project uses Elasticsearch for:

- Full-text search
- Typo tolerance
- Relevance scoring

---

## Future Improvements

- Live autocomplete
- Search suggestions
- Admin panel
- Redis caching
- Payment integrations
- API Platform
- RabbitMQ

---

## Screenshots

(Add screenshots here later)

---

## License

MIT# symfony-elasticsearch-shop

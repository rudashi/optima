<p align="center"><img src="./art/logo-mock.svg" width="400" alt=""></p>

Comarch Optima wrapper
================

![Twitter Follow](https://img.shields.io/twitter/follow/rudashi?style=social)

Unofficial wrapper for ERP Comarch OPTIMA.

## General System Requirements

- [PHP ^8.1](http://php.net/)
- [Laravel ^11.0](https://github.com/laravel/framework)
- [SQL Server for PHP](https://docs.microsoft.com/en-us/sql/connect/php/overview-of-the-php-sql-driver?view=sql-server-ver15)

## Quick Installation

If necessary, use the composer to download the library

```bash
composer require rudashi/optima
```

If not working, add repository to yours composer.json

```bash
"repositories": [
    {
        "type": "vcs",
        "url":  "https://github.com/rudashi/optima.git"
    }
],
```

Add  to `.env` your sqlSRV database configuration

```dotenv
MS_HOST=127.0.0.1
MS_PORT=1433
MS_DATABASE=cdn_optima
MS_USERNAME=su
MS_PASSWORD=
MS_SOCKET=
```

## Usage

To get access to optima query you can use it:
```php
optima()->from('table')->get();
```

### Customers | Kontrahenci
To get information about customer you can use one of two methods:
```php
(new CustomerRepository(optima(false)))->findByCode('TEST!');

(new CustomerRepository(optima(false)))->find(1111, 222, 3333);
```

## Authors

* **Borys Żmuda** - Lead designer - [LinkedIn](https://www.linkedin.com/in/boryszmuda/), [Portfolio](https://rudashi.github.io/)

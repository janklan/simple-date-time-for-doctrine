# Simple Date Time for Doctrine

![Packagist Version](https://img.shields.io/packagist/v/janklan/simple-date-time-for-doctrine)
[![CI](https://github.com/janklan/simple-date-time-for-doctrine/actions/workflows/ci.yml/badge.svg)](https://github.com/janklan/simple-date-time-for-doctrine/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/janklan/simple-date-time-for-doctrine/graph/badge.svg)](https://codecov.io/gh/janklan/simple-date-time-for-doctrine)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A Doctrine integration of [janklan/simple-date-time](https://github.com/janklan/simple-date-time).

## Why?

Databases Doctrine integrates with support `DATE` and `TIME` data types, but Doctrine converts those values to a PHP's native \DateTimeImmutable objects, forcing additional complexity onto you when you don't want it: time zones, and a _date_ when you only want to work with _time_ and vice versa.

For example:

- **Birthdays**: A birthday is January 15th, not "January 15th in UTC-5"
- **Special Days**: The Australia Day is on January 26th, The Independence Day is on July 4th - regardless of which time zone in Australia or the U.S. you are
- **Business hours**: Standard business hours across a global enterprise could be 9am to 5pm, regardless of where on Earth do you work for the business: you have to rock up at 9am for work, wherever you are.

[janklan/simple-date-time](https://github.com/janklan/simple-date-time) provides a solution at a PHP level, and this package provides the connecting dots between that solution and Doctrine.

## Installation

```bash
composer require janklan/simple-date-time-for-doctrine
```

## Configuration

This package provides four Doctrine DBAL types:

| Type Name | Doctrine Type | PHP Type |
|-----------|-------|----------|
| `simple_date_immutable` | `SimpleDateImmutableType` | `SimpleDateImmutable` |
| `simple_date` | `SimpleDateType` | `SimpleDate` |
| `simple_time_immutable` | `SimpleTimeImmutableType` | `SimpleTimeImmutable` |
| `simple_time` | `SimpleTimeType` | `SimpleTime` |

### Doctrine (standalone)

Register the types before using them:

```php
use Doctrine\DBAL\Types\Type;
use JanKlan\SimpleDateTimeForDoctrine\SimpleDateImmutableType;
use JanKlan\SimpleDateTimeForDoctrine\SimpleTimeImmutableType;

Type::addType('simple_date_immutable', SimpleDateImmutableType::class);
Type::addType('simple_time_immutable', SimpleTimeImmutableType::class);
```

### Symfony

Add to `config/packages/doctrine.yaml`:

```yaml
doctrine:
    dbal:
        types:
            simple_date_immutable: JanKlan\SimpleDateTimeForDoctrine\SimpleDateImmutableType
            simple_date: JanKlan\SimpleDateTimeForDoctrine\SimpleDateType
            simple_time_immutable: JanKlan\SimpleDateTimeForDoctrine\SimpleTimeImmutableType
            simple_time: JanKlan\SimpleDateTimeForDoctrine\SimpleTimeType
```

## Usage

Once configured, use the types in your entity mappings:

```php
use Doctrine\ORM\Mapping as ORM;
use JanKlan\SimpleDateTime\SimpleDate;
use JanKlan\SimpleDateTime\SimpleDateImmutable;
use JanKlan\SimpleDateTime\SimpleTime;
use JanKlan\SimpleDateTime\SimpleTimeImmutable;

#[ORM\Entity]
class Employee
{
    #[ORM\Column(type: 'simple_date_immutable')]
    public SimpleDateImmutable $immutableBirthday;

    #[ORM\Column(type: 'simple_time_immutable')]
    public SimpleTimeImmutable $immutableShiftStart;

    #[ORM\Column(type: 'simple_date')]
    public SimpleDate $mutableBirthday;

    #[ORM\Column(type: 'simple_time')]
    public SimpleTime $mutableShiftStart;
}
```

### A warning about object comparison

PHP will let you compare the `Simple(Date|Time)(Immutable|)` classes with native `\DateTimeInterface` objects, but doing so is risky and discouraged. All classes offer a custom `isSame`, `isBefore` and `isAfter` methods that should be used for comparison - they will ignore the time zone in the object you're comparing the other side with.

[janklan/simple-date-time](https://github.com/janklan/simple-date-time) provides a [PHPStan rule](https://github.com/janklan/simple-date-time?tab=readme-ov-file#phpstan-integration) that you should add to your PHPStan configuration to get warnings about unsafe comparisons.

## Contributing

PR are welcome. All you need to do to get started is to check out the repository, run `composer install` and you're done.

Several shorthand commands are configured in Composer: `cs`, `phpstan`, `rector`, `test`, and `preflight`. See `composer.json` to find out what they do.

Run `composer preflight` at least at the end of your work, before you create a pull request.

## Testing

The test suite is split into two categories: **Unit** and **Functional** tests.

### Running all tests

By default, PHPUnit runs both unit and functional tests:

```bash
./vendor/bin/paratest
```

Note: Functional tests require database containers to be running (see below).

### Unit Tests

Unit tests verify type conversion logic without requiring a database connection:

```bash
composer test:unit
```

### Functional Tests

Functional tests verify actual database round-trips (PHP -> DB -> PHP) against real PostgreSQL and MySQL databases.

#### Starting the databases

Start the Docker containers before running functional tests:

```bash
docker compose up -d
```

This starts:
- **PostgreSQL** on port 15432 (default version: 18)
- **MySQL** on port 13306 (default version: 9)

#### Running functional tests

Run all functional tests:

```bash
composer test:functional
```

Run only PostgreSQL tests:

```bash
composer test:functional -- --group postgres
```

Run only MySQL tests:

```bash
composer test:functional -- --group mysql
```

#### Stopping the databases

```bash
docker compose down
```

### Code Coverage

Generate coverage reports in `./coverage`:

```bash
composer test:cc
```

## License

MIT

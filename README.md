# Simple Date Time for Doctrine

A Doctrine integration of [janklan/simple-date-time](https://github.com/janklan/simple-date-time).

## Why?

Databases Doctrine integrates with support `DATE` and `TIME` data types, but Doctrine converts those values to a PHP's native \DateTimeImmutable objects, forcing additional complexity onto you when you don't want it: time zones, and a _date_ when you only want to work with _time_ and vice versa.

For example:

- **Birthdays** - A birthday is January 15th, not "January 15th in UTC-5"
- **Special Days** - The Australia Day is on January 26th, The Independence Day is on July 4th - regardless of which time zone in Australia or the U.S. you are
- **Business hours** - Standard business hours across a global enterprise could be 9am to 5pm, regardless of where on Earth do you work for the business: you have to rock up at 9am for work, wherever you are.

[janklan/simple-date-time](https://github.com/janklan/simple-date-time) provides a solution at a PHP level, and this package provides the connecting dots between that solution and Doctrine.

## Installation

```bash
composer require janklan/simple-date-time-for-doctrine
```

## Configuration

This package provides four Doctrine DBAL types:

| Type Name | Class | PHP Type |
|-----------|-------|----------|
| `simple_date_immutable` | `DateImmutableType` | `DateImmutable` |
| `simple_date` | `DateType` | `Date` |
| `simple_time_immutable` | `TimeImmutableType` | `TimeImmutable` |
| `simple_time` | `TimeType` | `Time` |

### Doctrine (standalone)

Register the types before using them:

```php
use Doctrine\DBAL\Types\Type;
use JanKlan\SimpleDateTimeDoctrine\DateImmutableType;
use JanKlan\SimpleDateTimeDoctrine\TimeImmutableType;

Type::addType('simple_date_immutable', DateImmutableType::class);
Type::addType('simple_time_immutable', TimeImmutableType::class);
```

### Symfony

Add to `config/packages/doctrine.yaml`:

```yaml
doctrine:
    dbal:
        types:
            simple_date_immutable: JanKlan\SimpleDateTimeDoctrine\DateImmutableType
            simple_date: JanKlan\SimpleDateTimeDoctrine\DateType
            simple_time_immutable: JanKlan\SimpleDateTimeDoctrine\TimeImmutableType
            simple_time: JanKlan\SimpleDateTimeDoctrine\TimeType
```

## Usage

Once configured, use the types in your entity mappings:

```php
use Doctrine\ORM\Mapping as ORM;
use JanKlan\SimpleDateTime\DateImmutable;
use JanKlan\SimpleDateTime\TimeImmutable;

#[ORM\Entity]
class Employee
{
    #[ORM\Column(type: 'simple_date_immutable')]
    public DateImmutable $birthday;

    #[ORM\Column(type: 'simple_time_immutable')]
    public TimeImmutable $shiftStart;
}
```

## Contributing

PR are welcome. All you need to do to get started is to check out the repository, run `composer install` and you're done.

Several shorthand commands are configured in Composer: `cs`, `phpstan`, `rector`, `test`, and `preflight`. See `composer.json` to find out what they do. 

Run `composer preflight` at least at the end of your work, before you create a pull request.

## License

MIT

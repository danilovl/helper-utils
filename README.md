[![phpunit](https://github.com/danilovl/helper-utils/actions/workflows/phpunit.yml/badge.svg)](https://github.com/danilovl/helper-utils/actions/workflows/phpunit.yml)
[![downloads](https://img.shields.io/packagist/dt/danilovl/helper-utils)](https://packagist.org/packages/danilovl/helper-utils)
[![latest Stable Version](https://img.shields.io/packagist/v/danilovl/helper-utils)](https://packagist.org/packages/danilovl/helper-utils)
[![license](https://img.shields.io/packagist/l/danilovl/helper-utils)](https://packagist.org/packages/danilovl/helper-utils)

# 🛠️ Helper Utils

Universal collection of **PHP 8.5+** helper utilities — dates, strings, arrays, files, network, reflection, hashing, colors, validation and more.

This is a **standalone library**, not a Symfony bundle. It does not require any framework configuration. Helpers that depend on the current time, timezone, or other side-effects either accept those as explicit method parameters (for static helpers) or are injected via constructor (for service helpers).

---

## 🚀 Key Features

- **Modern PHP**: Strictly typed for PHP 8.5+.
- **Zero Configuration**: Works out of the box in any project.
- **Framework Agnostic**: Core logic is pure PHP, with optional integrations for Symfony components.
- **Testable**: Design focuses on mockability and explicit state management.
- **Developer Experience**: Consistent naming, clean API, and comprehensive documentation.

## 📦 Installation

```bash
composer require danilovl/helper-utils
```

## 🛠️ Included Helpers

### Static Helpers
*All helper classes are `final` with private constructors.*

| Group | Classes | Description |
|:---|:---|:---|
| **Array** | `ArrayHelper`, `ArrayMapHelper`, `CollectionHelper` | Operations with arrays, mapping, filtering, and grouping. |
| **Color** | `ColorHelper` | Color manipulation and conversions (HEX, RGB). |
| **Compare** | `CompareHelper`, `VersionHelper` | Value and semantic version comparison utilities. |
| **Date** | `DateHelper`, `DatePeriodHelper`, `BusinessDayHelper` | Powerful date parsing, manipulation, and period calculations. |
| **File** | `FileHelper`, `PathHelper` | Filesystem operations, path handling, and metadata. |
| **Form** | `FormValidationMessageHelper` | Symfony Form error extraction and management. |
| **Hash** | `HashHelper`, `TokenHelper` | Secure hash generation and token utilities. |
| **Json** | `JsonHelper` | Safe JSON encoding and decoding. |
| **Locale** | `LocaleHelper`, `CountryHelper` | Geographic and linguistic data, country codes. |
| **Network** | `IpHelper`, `UrlHelper` | IP manipulation (anonymization, ranges) and URL handling. |
| **Number** | `NumberHelper`, `MoneyHelper` | Formatting numbers, currency, and byte sizes. |
| **Object** | `CloneHelper`, `ObjectHelper` | Deep cloning and dynamic property access. |
| **Reflection** | `ReflectionHelper`, `AttributeHelper`, `EnumHelper` | Advanced reflection for classes, attributes, and Enums. |
| **String** | `StringHelper`, `HtmlHelper`, `TextHelper` | String manipulation, slugging, masking, and HTML cleaning. |
| **Validator** | `ValidatorHelper` | Helpers for Symfony validation component. |
| **Misc** | `RetryHelper` | Miscellaneous utilities like retry logic. |

### Service Helpers
*DI-injectable, `final readonly` where stateless.*

- **`ClockAwareDateHelper`**: Time-sensitive operations using PSR-20 Clock.
- **`DoctrineHelper`**: Doctrine ORM metadata and entity manager shortcuts.
- **`MemoryHelper`**: Real-time memory usage tracking and formatting.
- **`RequestHelper`**: Convenient wrapper for Symfony's Request objects.
- **`TimerHelper`**: Precise code execution timing and measurement.

### Enums
- **`ByteUnit`**: Constants for data size units (B, KB, MB, etc.).
- **`ComparisonOperator`**: Common comparison operators for flexible logic.
- **`DateFormat`**: Standardized date and time format strings.

---

## 💡 Quick Examples

### Static Helpers

```php
use Danilovl\HelperUtils\Helper\Date\DateHelper;
use Danilovl\HelperUtils\Helper\String\StringHelper;
use Danilovl\HelperUtils\Helper\Network\IpHelper;

// Dates — explicit "now" for testability
$age  = DateHelper::calculateAge(new DateTimeImmutable('1990-04-25'));
$past = DateHelper::isPast($someDate, $now);

// Strings
$slug = StringHelper::slugify('Привет, мир!');           // "privet-mir"
$mask = StringHelper::maskEmail('john.doe@example.com'); // "j******e@example.com"

// IP Utilities
$ok   = IpHelper::isIpInRange('192.168.1.5', '192.168.1.0/24');
$anon = IpHelper::anonymize('192.168.1.123');             // "192.168.1.0"
```

### Services (Dependency Injection)

```php
use Danilovl\HelperUtils\Service\RequestHelper;
use Danilovl\HelperUtils\Service\ClockAwareDateHelper;

public function __construct(
    private RequestHelper $requestHelper,
    private ClockAwareDateHelper $clock,
) {}

$ip  = $this->requestHelper->getClientIp();
$now = $this->clock->now(); // Easily mockable
```

---

## 🧪 Quality Assurance

We maintain high testing standards to ensure reliability.

```bash
composer tests
```

The package currently includes **397 unit tests** and **766 assertions**.

### Test Coverage

| Group | Status |
|:---|:---:|
| Array, Color, Compare, Date, File, Hash, Json, Locale, Network, Number, Object, Reflection, String, Validator, Misc | ✅ Full |
| Service (Request, Clock, Doctrine, Memory, Timer) | ✅ Full |
---

## 📜 Design Principles

- **Immutability**: Preference for `\DateTimeImmutable`.
- **Explicitness**: "Now"-dependent methods accept an explicit timestamp.
- **Safety**: Methods follow the `doSomething` (throws) / `trySomething` (returns null) pattern.
- **Non-Invasive**: Wraps powerful libraries (Symfony, Doctrine) rather than reinventing wheels.
- **Finality**: Classes are `final` to prevent fragile inheritance.

## 📄 License

The HelperUtils is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

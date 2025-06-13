# phpstan-readonly-enforcing
PHPStan rule to enforce readonly usage on properties and classes when safe to do so.

## Description
This PHPStan rule analyzes code detecting properties and classes that can be safely declared as `readonly`. The goal is to help improving code safety and predictability by leveraging PHP's immutable properties feature.

Use it in static analysis to easily spot where you can apply `readonly` and make the code more robust.

## Features
- Detects properties and classes that can be safely declared as `readonly`.
- Ensures that `readonly` properties are not overwritten.
- Supports both traditional and PHP 8.0+ [promoted properties](https://www.php.net/manual/it/language.oop5.decon.php#language.oop5.decon.constructor.promotion).

## Limitations
- **Does NOT support traits yet:** The rule currently does not handle properties or classes using traits, so results may be inaccurate in those cases, but it still be usable.

## Installation
Add the rule to your PHPStan setup via composer (or include the file directly if standalone):

```bash
composer require --dev sal/phpstan-readonly-enforcing
```

## Configuration
Add the rule to your `phpstan.neon`:
```neon
services:
    -
        class: Sal\PHPStanReadonlyEnforcing\Rules\ReadonlyEnforcingRule
        tags:
            - phpstan.rules.rule
```

## Example
```php
<?php

class Test
{
    private string $name;  // If never modified after construction, can be readonly
    private readonly string $email; // Same here
}

class SecondTest // Both properties are readonly, so the class can be readonly
{
    private readonly string $name;
    private readonly string $email;
}

readonly class ThirdTest
{
    public function __construct(
        private readonly string $name,
        private readonly string $email,
    ) {
        $this->name = 'test'; // Error, cannot be overwritten
    }
}
```

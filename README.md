# PasswordPolice

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Password policy enforcement made easy.

## Install

Via Composer

``` bash
$ composer require stadly/password-police
```

## Usage

``` php
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\PolicyException;
use Stadly\PasswordPolice\CaseConverter\LowerCase as LowerCaseConverter;
use Stadly\PasswordPolice\CaseConverter\UpperCase as UpperCaseConverter;
use Stadly\PasswordPolice\Rule\Digit as DigitRule;
use Stadly\PasswordPolice\Rule\Dictionary;
use Stadly\PasswordPolice\Rule\HaveIBeenPwned;
use Stadly\PasswordPolice\Rule\Length as LengthRule;
use Stadly\PasswordPolice\Rule\LowerCase as LowerCaseRule;
use Stadly\PasswordPolice\Rule\UpperCase as UpperCaseRule;

$policy = new Policy();
$policy->addRules(new LengthRule(8));       // Password must be at least 8 characters long.
$policy->addRules(new LowerCaseRule());     // Password must contain lower case letters.
$policy->addRules(new UpperCaseRule());     // Password must contain upper case letters.
$policy->addRules(new DigitRule());         // Password must contain digits.
$policy->addRules(new HaveIBeenPwned());    // Password must not be exposed in data breaches.
$pspell = Pspell::fromLocale('en', new LowerCaseConverter(), new UpperCaseConverter());
$policy->addRules(new Dictionary($pspell)); // Password must not contain dictionary words.

try {
    $policy->enforce('password');
    // The password adheres to the policy.
} catch (PolicyException $exception) {
    // The password does not adhere to the policy.
    // Use the exception to show an appropriate message to the user.
}
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email magnar@myrtveit.com instead of using the issue tracker.

## Credits

- [Magnar Ovedal Myrtveit][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/stadly/password-police.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Stadly/PasswordPolice/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/Stadly/PasswordPolice.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Stadly/PasswordPolice.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/stadly/password-police.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/stadly/password-police
[link-travis]: https://travis-ci.org/Stadly/PasswordPolice
[link-scrutinizer]: https://scrutinizer-ci.com/g/Stadly/PasswordPolice/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/Stadly/PasswordPolice
[link-downloads]: https://packagist.org/packages/stadly/password-police
[link-author]: https://github.com/Stadly
[link-contributors]: ../../contributors

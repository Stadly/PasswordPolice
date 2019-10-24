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
composer require stadly/password-police
```

## Usage

``` php
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\Rule\DictionaryRule;
use Stadly\PasswordPolice\Rule\HaveIBeenPwnedRule;
use Stadly\PasswordPolice\Rule\LengthRule;
use Stadly\PasswordPolice\WordList\Pspell;

$policy = new Policy();

// Add rules to the password policy. See the Rules section below.
$policy->addRules(
    new LengthRule(8),                              // Password must be at least 8 characters long.
    new HaveIBeenPwnedRule(),                       // Password must not be exposed in data breaches.
    new DictionaryRule(Pspell::fromLocale('en'))    // Password must not be a word from the dictionary.
);

$password = 'password';

$validationErrors = $policy->validate($password);
if ($validationErrors !== []) {
    // The password is not in compliance with the policy.
    foreach ($validationErrors as $validationError) {
        // Show validation message to the user.
        echo $validationError->getMessage();
    }
}
```

### Rules

Password policy rules specify the password requirements, and are used to determine whether a password is in compliance with the policy or not.

#### Length

The length rule sets lower and upper limits to the password length.

``` php
use Stadly\PasswordPolice\Rule\LengthRule;

$rule = new LengthRule(8);              // Password must be at least 8 characters long.
$rule = new LengthRule(8, 32);          // Password must be between 8 and 32 characters long.
```

#### Lower case letters

The lower case rule sets lower and upper limits to the number of lower case letters.

``` php
use Stadly\PasswordPolice\Rule\LowerCaseRule;

$rule = new LowerCaseRule();            // Password must contain lower case letters.
$rule = new LowerCaseRule(3);           // Password must contain at least 3 lower case letters.
$rule = new LowerCaseRule(3, 5);        // Password must contain between 3 and 5 lower case letters.
```

#### Upper case letters

The upper case rule sets lower and upper limits to the number of upper case letters.

``` php
use Stadly\PasswordPolice\Rule\UpperCaseRule;

$rule = new UpperCaseRule();            // Password must contain upper case letters.
$rule = new UpperCaseRule(3);           // Password must contain at least 3 upper case letters.
$rule = new UpperCaseRule(3, 5);        // Password must contain between 3 and 5 upper case letters.
```

#### Digits

The digit rule sets lower and upper limits to the number of digits.

``` php
use Stadly\PasswordPolice\Rule\DigitRule;

$rule = new DigitRule();                // Password must contain digits.
$rule = new DigitRule(3);               // Password must contain at least 3 digits.
$rule = new DigitRule(3, 5);            // Password must contain between 3 and 5 digits.
```

#### Symbols

The symbol rule sets lower and upper limits to the number of symbols. The characters considered symbols are specified when creating the rule. Note that this rule counts the number of symbols, and not the number of distinct symbols. That `Hello!!!` contains one symbol three times, while `Hello!?&` contains three different symbols makes no difference—they both contain three symbols.

``` php
use Stadly\PasswordPolice\Rule\SymbolRule;

$rule = new SymbolRule('!#%&?');        // Password must contain symbols (!, #, %, &, or ?).
$rule = new SymbolRule('!#%&?', 3);     // Password must contain at least 3 symbols.
$rule = new SymbolRule('!#%&?', 3, 5);  // Password must contain between 3 and 5 symbols.
```

#### Have I Been Pwned

The Have I Been Pwned rule sets lower and upper limits to the number of times the password has been exposed in data breaches. This rule uses the [Have I Been Pwned](https://haveibeenpwned.com/Passwords) service. Passwords are never sent to the service. Instead [k-Anonymity](https://en.wikipedia.org/wiki/K-anonymity) is used to make the solution secure.

``` php
use Stadly\PasswordPolice\Rule\HaveIBeenPwnedRule;

$rule = new HaveIBeenPwnedRule();       // Password must not be exposed in data breaches.
$rule = new HaveIBeenPwnedRule(5);      // Password must not be exposed in data breaches more than 5 times.
$rule = new HaveIBeenPwnedRule(5, 3);   // Password must be exposed in data breachs between 3 and 5 times.
```

#### Dictionary

The dictionary rule enforces that the password is not contained in a word list.

[Formatters](#formatters) can optionally be applied to the password before checking the word list. This makes it possible to decode for example [leetspeak](#leetspeak-decoder), so that the password `p4ssw0rd` would match a word list containing the word `password`, or to [split the password](#substring-generator) into multiple words, so that the password `SomeCombinedWords` would match a word list containing the word `Combined`.

``` php
use Stadly\PasswordPolice\Formatter\LeetspeakDecoder;
use Stadly\PasswordPolice\Rule\DictionaryRule;
use Stadly\PasswordPolice\WordList\Pspell;

$wordList = Pspell::fromLocale('en');
$rule = new DictionaryRule($wordList, [new LeetspeakDecoder()]);
```

##### Word lists

The dictionary rule requires a word list. Currently, [Pspell](#pspell) is the only available word list. Support for other word lists can easily be implemented.

###### Pspell

The pspell word list uses [Pspell](https://www.php.net/manual/en/book.pspell.php), which can be built into php.

[Formatters](#formatters) can optionally be applied to the password before checking the word list. This is useful because the php version of pspell is case-sensitive. By using for example the [lower case converter](#lower-case-converter), the password `PaSsWoRd` would match a word list containing the word `password`.

``` php
use Stadly\PasswordPolice\Formatter\Capitalizer;
use Stadly\PasswordPolice\Formatter\LowerCaseConverter;
use Stadly\PasswordPolice\WordList\Pspell;

$wordList = Pspell::fromLocale('en', [new LowerCaseConverter(), new Capitalizer()]);
```

#### Guessable data

The guessable data rule enforces that the password doesn’t contain data that can easily be guessed. Easily guessable data is specified when creating the rule. It is possible to specify additional easily guessable data for each password. This way the guessable data rule can both prevent general easily guessable data like the service name from being used in any password, and also prevent users from using personal easily guessable data like their own name or birthday in their password.

To specify easily guessable data for a password, the password must be a `Password` object instead of a `string`.

[Formatters](#formatters) can optionally be applied to the password before validation. This makes it possible to decode for example [leetspeak](#leetspeak-decoder), so that the password `S74d1y` would match the easily guessable data `Stadly`. Note that the guessable data rule checks if the password contains easily guessable data, not that it matches it, so there is no need to use a formatter to split the password into [multiple words](#substring-generator), as may be useful with the [dictionary rule](#dictionary).

``` php
use Stadly\PasswordPolice\Formatter\LeetspeakDecoder;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Rule\GuessableDataRule;

// Easily guessable data for any password.
$globalGuessableData = [
    'company',
];
$rule = new GuessableDataRule($globalGuessableData, [new LeetspeakDecoder()]);

// Additional easily guessable data for this password.
$passwordGuessableData = [
    'first name',
    'spouse',
    new DateTimeImmutable('birthday'),
];
// Use a Password object instead of a string in order to specify the easily guessable data.
$password = new Password('password', $passwordGuessableData));
```

##### Date formatters

To check if a password contains an easily guessable date, the [guessable data rule](#guessable-data) must know the different formats that a date can have. This is the job of the date formatters. Custom date formatters can easily be implemented. A default date formatter is used when no date formatter is sepcified for the guessable data rule.

``` php
use Stadly\PasswordPolice\Rule\GuessableDataRule;

$dateFormatter = new MyCustomDateFormatter();

// Easily guessable data.
$guessableData = [
    new DateTimeImmutable('2018-08-04'),
];
$rule = new GuessableDataRule($guessableData, [], $dateFormatter);
```

#### No reuse

The no reuse rule prevents previously used passwords from being used again.

For the rule to know the previously used passwords, former passwords must be specified. To specify former passwords, the password must be a `Password` object instead of a string.

``` php
use Stadly\PasswordPolice\FormerPassword;
use Stadly\PasswordPolice\HashFunction\PasswordHasher;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Rule\NoReuseRule;

$hashFunction = new PasswordHasher();

$rule = new NoReuseRule($hashFunction);     // Passwords can never be reused.
$rule = new NoReuseRule($hashFunction, 5);  // The 5 most recently used password cannot be reused.

// Former passwords. The most recent one should be the current password.
$formerPasswords = [
    new FormerPassword(new DateTimeImmutable('2017-06-24'), 'hash of password'),
    new FormerPassword(new DateTimeImmutable('2018-08-04'), 'hash of password'),
    new FormerPassword(new DateTimeImmutable('2018-08-18'), 'hash of password'),
];
// Use a Password object instead of a string in order to specify former passwords.
$password = new Password('password', [], $formerPasswords));
```

##### Hash functions

Passwords should always be stored as secure hashes, making it impossible to determine the raw password from its stored representation. In order to check if a password matches a previously used password, the [no reuse rule](#no-reuse) must use the same algorithm as was used to create the password hash. This is the job of the hash functions, allowing comparison between raw passwords and hashed passwords. Currently, there is only one available hash function, supporting the built-in [php password hashing algorithms](#password-hasher). Support for other hash functions can easily be implemented.

###### Password hasher

The password hasher uses [Password Hashing Functions](https://www.php.net/manual/en/ref.password.php), which is built into php. If you use [`password_hash`](https://www.php.net/manual/en/function.password-hash.php) to store hashes of passwords, this is the right choice for the [no reuse rule](#no-reuse).

#### Change with interval

The change with interval rule sets lower and upper limits to how often a password can be changed. A lower limit on the time from one password change to the next prevents passwords from being changed too often. This may be useful combined with the [no reuse rule](#no-reuse) enforcing that for example the 5 most recent passwords cannot be reused, since it prevents the user from just changing the password 5 times and then back to the original password. An upper limit on the time from one password change to the next enforces that passwords are changed on a regular basis.

For the rule to know when the password has been changed, former passwords must be specified. To specify former passwords, the password must be a `Password` object instead of a string.

``` php
use Stadly\PasswordPolice\FormerPassword;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Rule\ChangeWithIntervalRule;

// There must be at least 24 hours between password changes.
$rule = new ChangeWithIntervalRule(new DateInterval('PT24H'));

// There must be at most 30 days between password changes.
$rule = new ChangeWithIntervalRule(new DateInterval('PT0S'), new DateInterval('P30D'));

// Former passwords. The most recent one should be the current password.
$formerPasswords = [
    new FormerPassword(new DateTimeImmutable('2017-06-24')),
    new FormerPassword(new DateTimeImmutable('2018-08-04')),
    new FormerPassword(new DateTimeImmutable('2018-08-18')),
];
// Use a Password object instead of a string in order to specify former passwords.
$password = new Password('password', [], $formerPasswords));
```

#### Not set in interval

The not set in interval rule enforces that the password was not set during the specified time period. This may be useful for example in the case of a data breach, after which all passwords should be changed, or for other security incidents, when only passwords set during the period of the security incident must be changed.

For the rule to know when the password was set, the current password must be specified as a former password. To specify former passwords, the password must be a `Password` object instead of a string.

``` php
use Stadly\PasswordPolice\FormerPassword;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Rule\NotSetInIntervalRule;

// Password must have been set after 2019-02-10.
$rule = new NotSetInIntervalRule(new DateTimeImmutable('2019-02-10'));

// Password cannot have been set between 2019-02-10 and 2019-02-13.
$rule = new NotSetInIntervalRule(new DateTimeImmutable('2019-02-13'), new DateTimeImmutable('2019-02-10'));

// Former passwords. The most recent one should be the current password.
$formerPasswords = [
    new FormerPassword(new DateTimeImmutable('2017-06-24')),
    new FormerPassword(new DateTimeImmutable('2018-08-04')),
    new FormerPassword(new DateTimeImmutable('2018-08-18')),
];
// Use a Password object instead of a string in order to specify former passwords.
$password = new Password('password', [], $formerPasswords));
```

#### Conditional rule

The conditional rule is used to only conditionally apply another rule. The rule to apply conditionally, along with a condition function must be specified. The condition function should take a password (either `string` or `Password` object) as the only argument, and return `true` or `false`. If the condition function returns false, this rule does nothing. Otherwise, the specified rule is applied. This is useful for example to only apply the [Have I Been Pwned rule](#have-i-been-pwned) periodically. For example, to check at most once a month whether a password has been included in a data breach, instead of checking it every time the password is used.

``` php
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Rule\ConditionalRule;

/**
 * @param Password|string $password
 * @return bool
 */
$conditionFunction = function($password): bool {
    return true; // Whether the rule should be applied.
}

$rule = new ConditionalRule($ruleToApplyConditionally, $conditionFunction);
```

### Formatters

Formatters are used to manipulate strings, and may be used in combination with the [dictionary](#dictionary) and [guessable data](#guessable-data) rules and with the [pspell](#pspell) word list. Formatters can be [chained](#chaining) so that the result of one formatter is fed into another formatter (the formatters are run in series). Formatters can also be [combined](#combiner), so that the results of multiple formatters are combined into one (the formatters are run in parallel).

#### Converters

Converter formatters can convert characters in a string into other characters.

##### Capitalizer

The capitalizer converts the first character to upper case, and the rest to lower case.

``` php
use Stadly\PasswordPolice\Formatter\Capitalizer;

$formatter = new Capitalizer();
```

##### Leetspeak decoder

The leetspeak decoder decodes character sequences that can be interpreted as leetspeak. All decoding combinations are included in the result, so formatting the string `1337` results in the strings `1337`, `L337`, `1E37`, `13E7`, `133T`, `LE37`, `L3E7`, `L33T`, `1EE7`, `1E3T`, `13ET`, `LEE7`, `LE3T`, `L3ET`, `1EET`, `LEET`.

``` php
use Stadly\PasswordPolice\Formatter\LeetspeakDecoder;

$formatter = new LeetspeakDecoder();
```

##### Lower case converter

The lower case converter converts all characters to lower case.

``` php
use Stadly\PasswordPolice\Formatter\LowerCaseConverter;

$formatter = new LowerCaseConverter();
```

##### Mixed case converter

The mixed case converter converts all characters to both lower case and upper case. All combinations are included in the result, so formatting the string `fOo` results in the strings `foo`, `Foo`, `fOo`, `foO`, `FOo`, `FoO`, `fOO`, `FOO`.

``` php
use Stadly\PasswordPolice\Formatter\MixedCaseConverter;

$formatter = new MixedCaseConverter();
```

##### Upper case converter

The upper case converter converts all characters to upper case.

``` php
use Stadly\PasswordPolice\Formatter\UpperCaseConverter;

$formatter = new UpperCaseConverter();
```

#### Splitters

Splitter formatters can extract parts of a string.

##### Substring generator

The substring generator generates all substrings. A minimum and maximum length for the substrings can be specified. Substring shorter than the minimum or longer than the maximum are not included in the result. The result only includes unique strings.

``` php
use Stadly\PasswordPolice\Formatter\SubstringGenerator;

// Ignore substring shorter than 3 characters or longer than 25 character.
$formatter = new SubstringGenerator(3, 25);
```

##### Truncator

The truncator truncates strings to a maximum length.

``` php
use Stadly\PasswordPolice\Formatter\Truncator;

$formatter = new Truncator(25); // Truncate strings so they contain no more than 25 characters.
```

#### Filters

Filter formatters can filter out certain strings.

##### Length filter

The length filter filters out strings that are shorter or longer than the limits.

``` php
use Stadly\PasswordPolice\Formatter\LengthFilter;

$formatter = new LengthFilter(3);       // Filter out strings shorter than 3 characters.
$formatter = new LengthFilter(0, 25);   // Filter out strings longer than 25 characters.
$formatter = new LengthFilter(3, 25);   // Filter out strings shorter than 3 or longer than 25 characters.
```

#### Combiner

The formatter combiner combines the results from multiple formatters into one (the formatters are run in parallel). Unformatted strings are also included in the result by default, but can be excluded. The result only includes unique strings.

``` php
use Stadly\PasswordPolice\Formatter\Combiner;
use Stadly\PasswordPolice\Formatter\LowerCaseConverter;
use Stadly\PasswordPolice\Formatter\UpperCaseConverter;

$lower = new LowerCaseConverter();
$upper = new UpperCaseConverter();

$formatter = new Combiner($lower, $upper);          // Lower case, upper case and unformatted strings.
$formatter = new Combiner($lower, $upper, false);   // Lower case and upper case strings.
```

#### Chaining

Formatters can be chained so that the result of one formatter is fed into another formatter (the formatters are run in series).

``` php
use Stadly\PasswordPolice\Formatter\LeetspeakDecoder;
use Stadly\PasswordPolice\Formatter\SubstringGenerator;

$formatter = new LeetspeakDecoder();

// First decode leetspeak, and then generate all substrings.
$formatter->setNext(new SubstringGenerator());
```

### Rule weights

All rules have an associated weight. The default weight is 1. By using weights, it is possible to differenciate between hard rules that cannot be circumvented, and rules that are merely intended as advice and may be ignored.

#### Rule weights when testing a rule or policy

When testing a rule or policy, an optional weight can be specified. Rules with lower weights than the testing weight are ignored.

``` php
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\Rule\DigitRule;
use Stadly\PasswordPolice\Rule\LengthRule;

$policy = new Policy();

$policy->addRules(new LengthRule(8, null, 1));  // Rule weight: 1.
$policy->addRules(new DigitRule(1, null, 2));   // Rule weight: 2.

$password = '123';

$policy->test($password, 1);    // False, since the password is too short.
$policy->test($password, 2);    // True, since the length rule is ignored.
```

#### Rule weights when validating a rule or policy

When validating a rule or policy, all rules are validated, regardless of their weight. Each validation error contains the weight of the broken rule, which can be used to ignore validation errors of low weight.

``` php
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\Rule\DigitRule;
use Stadly\PasswordPolice\Rule\LengthRule;

$policy = new Policy();

$policy->addRules(new LengthRule(8, null, 1));  // Rule weight: 1.
$policy->addRules(new DigitRule(1, null, 2));   // Rule weight: 2.

$password = '123';

$validationErrors = $policy->validate($password);
foreach ($validationErrors as $validationError) {
    // Ignore validation errors of weight lower than or equal to 1.
    if ($validationError->getWeight() > 1) {
        // Show validation message to the user.
        echo $validationError->getMessage();
    }
}
```

#### Constraints

In addition to specifying different weights to different rules, most rules can contain  multiple constraints with different weights. This makes it possible to create rules with strict constraints having low weights and looser constrains with higher weights.

#### Constraint weights when testing a rule or policy

When testing a rule or policy, an optional weight can be specified. Rule constraints with lower weights than the testing weight are ignored.

``` php
use Stadly\PasswordPolice\Rule\LengthRule;

$rule = new LengthRule(12, null, 1);    // Constraint weight: 1.
$rule->addConstraint(8, null, 2);       // Constraint weight: 2.

$password = 'password';

$rule->test($password, 1);  // False, since the password is too short.
$rule->test($password, 2);  // True, since the strict constraint is ignored.
```

#### Constraint weights when validating a rule or policy

When validating a rule or policy, all rule constraints are validated, regardless of their weight. Each validation error contains the weight of the unsatisfied rule constraint, which can be used to ignore validation errors of low weight.

``` php
use Stadly\PasswordPolice\Rule\LengthRule;

$rule = new LengthRule(12, null, 1);    // Constraint weight: 1.
$rule->addConstraint(8, null, 2);       // Constraint weight: 2.

$password = 'password';

$validationErrors = $policy->validate($password);
foreach ($validationErrors as $validationError) {
    // Ignore validation errors of weight lower than or equal to 1.
    if ($validationError->getWeight() > 1) {
        // Show validation messages to the user.
        echo $validationError->getMessage();
    }
}
```

#### Example usage of weight

With version 2 of the [Have I Been Pwned](https://haveibeenpwned.com/Passwords) service, the number of times a password appears in data breaches was introduced. When writing about the new release, Troy Hunt gave an example of [how this number could be utilized](https://www.troyhunt.com/ive-just-launched-pwned-passwords-version-2/#eachpasswordnowhasacountnexttoit):

> Having visibility to the prevalence means, for example, you might outright block every password that’s appeared 100 times or more and force the user to choose another one (there are 1,858,690 of those in the data set), strongly recommend they choose a different password where it’s appeared between 20 and 99 times (there’s a further 9,985,150 of those), and merely flag the record if it’s in the source data less than 20 times.

Such a password policy can be implemented by creating 3 rule constraints with different weights:

``` php
use Stadly\PasswordPolice\Rule\HaveIBeenPwnedRule;

// Weight 1 when password has appeared in data breaches 100 times or more.
$rule = new HaveIBeenPwnedRule(99, 0, 1);

// Weight 0 when password has appeared in data breaches between 20 and 99 times.
$rule->addConstraint(19, 0, 0);

// Weight -1 when password has appeared in data breaches between 1 and 19 times.
$rule->addConstraint(0, 0, -1);
```

When validating a password, the weight of the validation error can be used to determine which action to take:

``` php
$validationErrors = $policy->validate($password);
foreach ($validationErrors as $validationError) {
    if ($validationError->getWeight() === 1) {
        // Reject the password.
    } elseif ($validationError->getWeight() === 0) {
        // Recommend choosing a different password.
    } elseif ($validationError->getWeight() === -1) {
        // Flag the password.
    }
}
```

### Best practices for password policies

General guidelines for good password policies:

- Require passwords to be at least 8 characters [long](#length).
- Do not allow passwords that have been exposed in [data breaches](#have-i-been-pwned).
- Do not allow passwords that can be found in a [dictionary](#dictionary).
- Do not allow passwords to include words that are [easy to guess](#guessable-data), such as the service name or the user’s name.
- Do not allow passwords with repetitive or sequential characters (e.g. “aaaaaa”, “1234”, “abcd”, or “qwerty”).
- Do not require combinations of [lower case letters](#lower-case-letters), [upper case letters](#upper-case-letters), [digits](#digits), and [symbols](#symbols).
- Do not require passwords to be [changed periodically](#change-with-interval).

You can read more about password policy recommendations in [NIST SP 800-63B](https://nvlpubs.nist.gov/nistpubs/SpecialPublications/NIST.SP.800-63b.pdf), section 5.1.1 and Appendix A.

#### When to validate passwords

There are two events in which a password can be validated agains a password policy:
1. When the password is set.
2. When the password is used.

Passwords should always be stored as secure hashes, making it impossible to determine the raw password from its stored representation. Therefore, the raw password is only available when it is being set or used, and hence the password can be validated only then. The only exception is password policy rules that do not need the password, such as the [change with interval rule](#change-with-interval) and the [not set in interval rule](#not-set-in-interval).

It is recommended to validate passwords both when they are set and when they are used, but the rules to apply are usually different in the two cases.

##### Validating passwords that are being set

When the password is being set is the right time to validate the password format, such as [length](#length), [lower case letters](#lower-case-letters), [upper case letters](#upper-case-letters), [digits](#digits), and [symbols](#symbols). In addition, the password content should be validated, using rules such as [Have I Been Pwned](#have-i-been-pwned), [dictionary](#dictionary), and [guessable data](#guessable-data). Rules that consider former passwords should also be checked, such as [no reuse](#no-reuse) and [change with interval](#change-with-interval) with lower limit.

##### Validating passwords that are being used

When the password is being used, there is no need to validate the format of the password. Assuming that the password policy has not changed, the password will pass validation when it is used if it passed validation when it was set. When the password is used, the password only needs to be validated agains rules whose validation outcome can change without the password being changed. Such rules are for example [Have I Been Pwned](#have-i-been-pwned) (a formerly valid password can become invalid after a new databreach), [change with interval](#change-with-interval) with upper limit, and [not set in interval](#not-set-in-interval).

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
composer test
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

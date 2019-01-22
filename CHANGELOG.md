# Changelog

All notable changes to `PasswordPolice` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [Unreleased](https://github.com/Stadly/PasswordPolice/compare/v0.13.0...HEAD)

### Added
- Possible to specify guessable data for the rule, that applies to all passwords.
- Word formatter generating substrings of the word.
- Word formatter filtering words by length.
- Word formatter filtering unique words.
- Word formatters can be chained. The output from one word formatter is used as input for the next word formatter in the chaine.
- Word formatter combining the results from multiple word formatters.

### Changed
- Word converters have been renamed word formatters.
- The convert method of word formatters has been renamed apply.
- The apply method of word formatters take multiple words instead of a single word.
- Word length can no longer be specified for the dictionary rule. Use the length filter word formatter instead.
- The dictionary rule, guessable data rule, and pspell word list no longer filter unique words automatically. Use the unique filter word formatter instead.
- The pspell word list takes an array of word formatters instead of a variadic list.
- Renamed `Count` constraint to `CountConstraint`.
- Renamed `Date` constraint to `DateConstraint`.
- Renamed `DateInterval` constraint to `DateIntervalConstraint`.
- Renamed `Position` constraint to `PositionConstraint`.
- Renamed `PasswordHash` hash function to `PasswordHasher`.
- Renamed `Capitalize` word formatter to `Capitalizer`.
- Renamed `Leetspeak` word formatter to `LeetDecoder`.
- Renamed `LowerCase` word formatter to `LowerCaseConverter`.
- Renamed `MixedCase` word formatter to `MixedCaseConverter`.
- Renamed `UpperCase` word formatter to `UpperCaseConverter`.
- Renamed `ChangeDate` rule to `ChangeOnDateRule`.
- Renamed `ChangeInterval` rule to `ChangeWithIntervalRule`.
- Renamed `CharacterClass` rule to `CharacterClassRule`.
- Renamed `Dictionary` rule to `DictionaryRule`.
- Renamed `Digit` rule to `DigitRule`.
- Renamed `GuessableData` rule to `GuessableDataRule`.
- Renamed `HaveIBeenPwned` rule to `HaveIBeenPwnedRule`.

### Fixed
- Nothing

### Deprecated
- Nothing

### Removed
- Whether to check all substrings or not can no longer be specified for dictionary rules. Use the substring word converter instead.

### Security
- Nothing

## [v0.13.0](https://github.com/Stadly/PasswordPolice/compare/v0.12.0...v0.13.0) - 2018-12-28

### Added
- Date constraint.
- Rule enforcing that passwords are changed after a specific date.

### Changed
- Compound date intervals are joined more naturally, such as "3 hours, 5 minutes and 6 seconds" instead of "3 hours 5 minutes 6 seconds".
- Rename `Change` rule to `ChangeInterval`.
- Rename `Date` constraint to `DateInterval`.
- The dates of former passwords must be immutable.

## [v0.12.0](https://github.com/Stadly/PasswordPolice/compare/v0.11.0...v0.12.0) - 2018-12-28

### Changed
- Remove `Interface` suffix from interfaces.
- Move interfaces one level up in the namespace hierarchy.
- Rename `TestException` to `Exception`.

## [v0.11.0](https://github.com/Stadly/PasswordPolice/compare/v0.10.0...v0.11.0) - 2018-12-21

### Added
- Constraint for counts in rules.
- Constraint for dates in rules.
- Constraint for positions in rules.
- Upper case rules can have multiple constraints.
- Lower case rules can have multiple constraints.
- Password length rules can have multiple constraints.
- Digit rules can have multiple constraints.
- Symbol rules can have multiple constraints.
- Character class rules can have multiple constraints.
- Have I Been Pwned? rules can have multiple constraints.
- Password change rules can have multiple constraints.
- No reuse rules can have multiple constraints.
- Guessable data rules are weighted.
- Dictionary rules are weighted.
- Possible to set a lower weight limit when testing rules.
- Weight of violated constraint is available in rule exception.
- It is now possible to validate that a password is in compliance with a rule. If not, a validation error is returned.
- It is now possible to validate that a password is in compliance with a policy. An array of validation errors is returned. The array is empty if the password is in compliance with the policy.

### Changed
- Minimum constraint of password change rule is never null.
- Password change rule cannot be constructed with null minimum constraint.
- Possible to construct unconstrained rules.
- Not possible to get message from rule.
- The first-constraint of reuse rules is 0-indexed instead of 1-indexed.
- Guessable data constructor is no longer variadic, but takes an array of word converters instead.
- Dictionary constructor is no longer variadic, but takes an array of word converters instead.

### Removed
- Rules can no longer be enforced. Use password validation instead.
- Policies can no longer be enforced. Use password validation instead.

## [v0.10.0](https://github.com/Stadly/PasswordPolice/compare/v0.9.0...v0.10.0) - 2018-12-14

### Added
- Rule enforcing the use of symbols in passwords.

### Changed
- Improved exception messages.
- The character class rule can not be used directly anymore. Use the symbol rule instead.

## [v0.9.0](https://github.com/Stadly/PasswordPolice/compare/v0.8.0...v0.9.0) - 2018-12-10

### Changed
- Use HTTP Factory Discovery instead of HTTPlug Discovery to discover HTTP Client implementations. The removes the HTTPlug 2.0 dependency making the library incompatible with projects using HTTPlug 1.0.
- Translators must implement `Symfony\Contracts\Translation\LocaleAwareInterface`, since `getLocale()` has been removed from `Symfony\Contracts\Translation\TranslatorInterface`.

## [v0.8.0](https://github.com/Stadly/PasswordPolice/compare/v0.7.0...v0.8.0) - 2018-12-06

### Added
- Interface for character converters.
- Leetspeak character converter.
- Word converter creating all combinations of upper case and lower case letters in words.
- Possible to use word converters in dictionaries. Useful for converting leetspeak to normal characters before checking the word list.
- Possible to use word converters in guessable data rules. Useful for converting leetspeak to normal characters before comparing to the guessable data.

### Changed
- Case converters return a traversable with strings instead of a single string.
- Case converters and character converters are combined into word converters.
- Short form of just year is no longer recognized as a guessable date.

## [v0.7.0](https://github.com/Stadly/PasswordPolice/compare/v0.6.0...v0.7.0) - 2018-12-04

### Added
- Possible for dictionary rules to check wheter the password is a dictionary word, and not only whether it contains dictionary words.

## [v0.6.0](https://github.com/Stadly/PasswordPolice/compare/v0.5.0...v0.6.0) - 2018-12-04

### Added
- Rule enforing that passwords are not changed too often. This is useful for example when combined with a rule enforcing that the 5 most recent passwords cannot be reused, since it prevents the user from just changing the password 5 times and then back to the original password.
- Rule enforcing that passwords must be changed on a regular basis.

## [v0.5.0](https://github.com/Stadly/PasswordPolice/compare/v0.4.0...v0.5.0) - 2018-11-30

### Added
- Interface for hash functions.
- Possible to specify former passwords.
- Rule enforcing that former passwords are not reused.
- Hash function implementation of `password_hash`.

### Changed
- Specify the maximum number of appearances in breaches before the minimum in Have I Been Pwned?
- Renamed Dictionary methods `getMin` and `getMax` to `getMinWordLength` and `getMaxWordLength`.
- Translators must implement `Symfony\Contracts\Translation\TranslatorInterface` instead of `Symfony\Component\Translation\TranslatorInterface`, since the latter is deprecated.

## [v0.4.0](https://github.com/Stadly/PasswordPolice/compare/v0.3.0...v0.4.0) - 2018-11-28

### Added
- Rules accept Password object in addition to string.
- Rule enforcing that passwords don't contain easily guessable data.

## [v0.3.0](https://github.com/Stadly/PasswordPolice/compare/v0.2.0...v0.3.0) - 2018-11-28

### Added
- Possibility to specify a translator to use for all translations.
- If no translator is specified, a default translator is created automatically.
- Rule enforcing that passwords don't contain words from a dictionary.
- Converters for changing the case of letters in words.
- Pspell can be used as word list for dictionaries.

### Changed
- Translator no longer needs to be specified when enforcing rules.
- Exceptions are now final.
- Let tests mock interfaces instead of depend on implementations.
- RuntimeException is thrown instead of LogicException when HTTP client or HTTP request factory could not be found.
- RuntimeException is thrown instead of TestException when word list cannot be used.

## [v0.2.0](https://github.com/Stadly/PasswordPolice/compare/v0.1.0...v0.2.0) - 2018-11-22

### Added
- Rule using the service [Have I Been Pwned?](https://haveibeenpwned.com) to check if the password has been exposed in data breaches.

## v0.1.0 - 2018-11-15

### Added
- Password policies.
- Rule enforcing password length.
- Rule enforcing the use of lower case letters in passwords.
- Rule enforcing the use of upper case letters in passwords.
- Rule enforcing the use of digits in passwords.
- Rule enforcing the use of custom character classes in passwords.

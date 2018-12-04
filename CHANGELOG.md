# Changelog

All notable changes to `PasswordPolice` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [Unreleased](https://github.com/Stadly/PasswordPolice/compare/v0.6.0...HEAD)

### Added
- Nothing

### Changed
- Nothing

### Fixed
- Nothing

### Deprecated
- Nothing

### Removed
- Nothing

### Security
- Nothing

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

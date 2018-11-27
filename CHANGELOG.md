# Changelog

All notable changes to `PasswordPolice` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [Unreleased](https://github.com/Stadly/PasswordPolice/compare/v0.2.0...HEAD)

### Added
- Possibility to specify a translator to use for all translations.
- If no translator is specified, a default translator is created automatically.
- Rule enforcing that passwords don't contain words from a dictionary.

### Changed
- Translator no longer needs to be specified when enforcing rules.
- Exceptions are now final.
- Let tests mock interfaces instead of depend on implementations.
- RuntimeException is thrown instead of LogicException when HTTP client or HTTP request factory could not be found.
- RuntimeException is thrown instead of TestException when word list cannot be used.

### Fixed
- Nothing

### Deprecated
- Nothing

### Removed
- Nothing

### Security
- Nothing

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

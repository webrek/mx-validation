# Changelog

All notable changes to `webrek/mx-validation` are documented here. The
format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and the
project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2026-06-29

### Added

- `Rfc::fromName()` and `Curp::fromName()` to generate an identifier from a
  person's name and birth date, applying the SAT/RENAPO initials rules (particle
  removal, the María/José skip, the inconvenient-word filter), the name-hash
  homoclave (RFC) and the check digit. The homoclave is verified against the
  SAT's published example (`GODE561231GR8`).
- `Support\MexicanName` with the shared name-parsing helpers.

## [1.0.0] - 2026-06-29

### Added

- Value objects with real check-digit verification for **RFC**, **CURP**,
  **CLABE** and **NSS**, each with `isValid()`, `tryParse()`, `parse()` and
  parsing accessors (persona física/moral, sex, birth date, entity, bank, …).
- Validation rules registered as both string rules (`rfc`, `curp`, `clabe`,
  `nss`, `codigo_postal`) and rule objects, with Spanish messages.
- Eloquent casts (`RfcCast`, `CurpCast`, `ClabeCast`, `NssCast`) that store a
  normalized string and hydrate the value object.
- A Faker provider generating structurally valid identifiers for factories and
  seeders, auto-registered on the container's generator.
- The SAT generic RFCs (`XAXX010101000`, `XEXX010101000`) are recognised as
  valid, and CURP entity / CLABE bank codes are resolved to names.
- Supports Laravel 12 and 13 on PHP 8.2+.

[Unreleased]: https://github.com/webrek/mx-validation/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/webrek/mx-validation/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/webrek/mx-validation/releases/tag/v1.0.0

# Changelog

All notable changes to `lalalili/subscription-filament` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- CI (PHP 8.3/8.4) and tag-triggered release workflows; baseline release
  discipline (pest + phpstan via `composer test` / `composer analyse`).
- `vcs` repository for `lalalili/subscription-core` so CI resolves the dependency
  when the local sibling path override is absent.

## [0.1.1]

- Initial Filament admin UI layer for `subscription-core` (consumed by `aitehub`).

# Changelog

All notable changes to `lalalili/subscription-filament` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.2] - 2026-06-27

### Added

- `SubscriptionResource`：顯示定期定額欄位（`is_recurring`、已扣款期數、連續失敗、`gwsr`、下次扣款日）、
  新增「定期定額」篩選與列動作「取消訂閱」。取消透過 `SubscriptionCore\Contracts\SubscriptionCanceller`
  連動 host 金流（未綁定時退回僅標記本地取消）。

### Added (infra)

- CI (PHP 8.3/8.4) and tag-triggered release workflows; baseline release
  discipline (pest + phpstan via `composer test` / `composer analyse`). CI checks
  out the `subscription-core` sibling so the `../subscription-core` path
  dependency resolves.

## [0.1.1]

- Initial Filament admin UI layer for `subscription-core` (consumed by `aitehub`).

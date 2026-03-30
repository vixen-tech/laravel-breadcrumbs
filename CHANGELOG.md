# Changelog

All changes to the `laravel-breadcrumbs` package will be documented in this file.

## 3.1.0 • 2026-03-30
- Added support for Laravel 13
- Simplified `$path` — it is now a plain string; route name resolution and `$params` have been removed
- Added `$extra` parameter to `Breadcrumb` for attaching arbitrary data
- Breadcrumbs without a `$path` now have `null` path and `active = false` (previously used the current URL)

## 3.0.3 • 2025-05-31
- Added support for Laravel 12

## 3.0.1 • 2022-09-02
### Fixed
- Bug where breadcrumbs were not rendered.
- HTML where a link was not rendered inside a `<li>`.

## 3.0.0 • 2022-03-06

Initial release.

## < 3.0

For v2 and v1, check the [old repository](https://github.com/atorscho/laravel-breadcrumbs).

# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Added

- `AnimaliaTheme` Filament 4/5 panel plugin with a fluent API: `primaryColor()`, `sidebar()`, `density()`, `radius()`, `font()`, `restyle()` and `withoutPrecompiledCss()`.
- Vendored AnimaliaDS tokens, Filament colour palettes built from them, and semantic light and dark tokens rendered as `--ani-*` custom properties, with WCAG AA contrast checks on the semantic tokens.
- Precompiled core stylesheet and seven restyle groups (buttons, forms, tables, navigation, overlays, feedback, layout), registered as Filament assets. All CSS uses logical properties, so RTL layouts work.
- Repeater, builder, key-value and callout components restyled from the tokens, with the same bordered card edge as sections and the toast status accent on callouts.
- Visible keyboard focus indicators on buttons, fields, toggles and links, and readable contrast on dark-mode overlays (modals, dropdowns and notifications).
- `density()` adjusts default-size buttons, table cells and section spacing.
- Vite mode: skip the precompiled links and import the CSS sources into a Filament theme.
- Workbench demo panels with a kitchen-sink page, users resource and stats widget.
- Playwright visual regression suite with baselines.
- Visual CI uploads the diff images of a failed run, one artifact per Filament major.
- GitHub Actions workflows for tests (PHP 8.2 and 8.4, Filament 4 and 5) and visual regression.
- Script to sync tokens from the pinned upstream AnimaliaDS repositories.
- README in English and Brazilian Portuguese, and a NOTICE with the AnimaliaDS MIT attribution.

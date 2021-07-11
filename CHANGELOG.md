# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 5.1.0

⚠️ This version contains breaking changes.

### Changed
* Use Doctrine database connection instead of TYPO3_DB
* Require `vidi`-extension version `4.0`

### Removed
* `\Fab\Media\Cache\CacheService::findPagesWithSoftReferences`

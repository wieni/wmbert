# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.7.0] - 2019-12-19
### Added
- Add coding standard fixers
- Add license
- Add issue & pull request templates
- Add php & drupal/core requirements to composer.json
- Add changelog

### Changed
- Automatically change the handler of entity reference fields when choosing the wmbert field widget
  ([#2](https://github.com/wieni/wmbert/issues/2))
- Make the _Same language only_ and _Number of results_ options
  configurable through the interface
- Move the _Disable selection of parent entity_ setting from the form
  display to the field
- Update .gitignore
- Update module description
- Update README & documentation
- Normalize & re-indent composer.json
- Coding style fixes

### Removed
- Remove Drupal composer repository from composer.json

## [1.6.1] - 2019-11-22
### Added
- Add core_version_requirement parameter to wmbert.info.yml

## [1.6.0] - 2019-10-22
### Added
- Add `#type => 'wmbert'` to the render array

## [1.5.0] - 2019-05-15
### Added
- Add entity reference label formatters

## [1.4.0] - 2019-04-15
### Added
- Add option to the selection handler to choose the amount of results

## [1.3.0] - 2019-03-15
### Added
- Add option to the selection handler to filter entities on language

## [1.2.8] - 2019-03-15
### Deprecated
- Deprecated
  `EntityReferenceListFormatterPluginBase::getTranslatedEntity`. Use `EntityRepositoryInterface::getTranslationFromContext`
  instead

## [1.2.7] - 2019-03-14
### Changed
- Use the entity repository to get translated entities

## [1.2.6] - 2019-03-12
### Changed
- Added a more abstract EntityReferenceSelection plugin with deriver

## [1.2.5] - 2019-03-07
### Changed
- Change the settings summary

## [1.2.4] - 2019-02-04
### Added
- Add settings summary for form display page

## [1.2.3] - 2018-12-10
### Fixed
- Fix issue when trying to load non-existent entity

## [1.2.2] - 2018-12-10
### Fixed
- Fix issue when trying to load non-existent entity

## [1.2.1] - 2018-12-06
### Fixed
- Fix issue when trying to load non-existent entity

## [1.2.0] - 2018-11-09
### Added
- Pass parent entity to list formatters

### Changed
- Show label in the same language of the parent entity

## [1.1.4] - 2018-10-25
### Changed
- Only show weight if field is multiple

## [1.1.3] - 2018-10-22
### Fixed
- Don't disable parent entity selection when referencing different
  entity type

## [1.1.2] - 2018-09-24
### Fixed
- Fix issue with select widget

## [1.1.1] - 2018-09-24
### Added
- Add node selection handler

### Changed
- Improve ignoring certain entities

## [1.1.0] - 2018-09-19
### Added
- Add setting to disable selection of parent entity

## [1.0.2] - 2018-09-19
### Fixed
- Fix AJAX error caused by method with return type

## [1.0.1] - 2018-09-18
### Added
- Add composer.json

## [1.0.0] - 2018-09-18
Initial release

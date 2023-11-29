# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.11.8] - 2023
- Add update script

## [1.11.7] - 2022-01-13
### Added
- Add option to disable drag and drop

## [1.11.6] - 2021-11-30
### Fixed
- Stop clearing the search query from the autocomplete field if there are no results

## [1.11.5] - 2021-11-02
### Fixed
- Fix sorting when using the 'Same language only' option

## [1.11.4] - 2021-10-01
### Fixed
- Fix warning with multi level selects

## [1.11.3] - 2021-09-29
### Changed
- Refactor code to make it more extensible

## [1.11.2] - 2021-09-10
### Fixed
- Fix html entities appearing in select widget

## [1.11.1] - 2021-07-20
### Fixed
- Fix foreach() argument must be of type array|object in `WmBert->massageFormValues()`

## [1.11.0] - 2021-07-03
### Added
- Add option to only reference published entities

## [1.10.0] - 2021-04-14
### Added
- Add support for autocreating entities

### Fixed
- Fix `same_language_only` using interface language instead of content language

## [1.9.7] - 2021-03-04
### Added
- Add support for sorting options by entity label instead of specific field

## [1.9.6] - 2021-02-26
### Added
- Add _Entity title (with edit link)_ and _Entity title and bundle (with edit link)_ list formatters

### Changed
- Add Composer 2 dev dependency

### Fixed
- Remove excessive margin when the widget is not wrapped by a fieldset

## [1.9.5] - 2021-01-20
### Added
- Add hook documentation

### Changed
- Add the entity to the entity selection handler settings. Improves the _Disable parent entity selection_ functionality.
- When creating a new node, publish it so it can be referenced

## [1.9.4] - 2020-10-19
### Fixed
- Fix broken Remove button

## [1.9.3] - 2020-10-19
### Changed
- Fix widget table styles

## [1.9.2] - 2020-10-19
### Changed
- Fix widget table styles not applying

## [1.9.1] - 2020-10-19
### Changed
- Add header to 'Entity title' list formatter

## [1.9.0] - 2020-10-19
### Added
- Add 'Entity title and publishing status' list formatter

### Changed
- Restore widget to always being a table

## [1.8.2] - 2020-07-31
### Fixed
- Fix issue when adding a new entity

## [1.8.1] - 2020-05-25
### Fixed
- Fix issues with & increase minimum core version to Drupal 8.6

## [1.8.0] - 2020-03-25
### Added
- Add plugin definition alter hooks

### Fixed
- Make plugin bases implement `ContainerFactoryPluginInterface`

## [1.7.2] - 2020-03-02
### Fixed
- Fix weight column showing if only one entity is selected

## [1.7.1] - 2020-02-12
### Changed
- Remove maintainers section & update security email address in README
- Update .gitignore

### Fixed
- Fix error on entity presave when field does not exist anymore
- Fix error when selection handler doesn't have an entity

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

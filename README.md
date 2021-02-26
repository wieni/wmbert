wmbert
======================

[![Latest Stable Version](https://poser.pugx.org/wieni/wmbert/v/stable)](https://packagist.org/packages/wieni/wmbert)
[![Total Downloads](https://poser.pugx.org/wieni/wmbert/downloads)](https://packagist.org/packages/wieni/wmbert)
[![License](https://poser.pugx.org/wieni/wmbert/license)](https://packagist.org/packages/wieni/wmbert)

> A more user-friendly entity reference list field widget for Drupal 8.

## Who's Bert?
**B**etter **E**ntity **R**eference **T**able. Also, a golden yellow Muppet character on the long running children's television show Sesame Street.

## Why?
The default entity reference widget is lacking: there's no obvious way
to remove items once added and it's not easy to change the way
entities are listed.

## Installation

This module requires PHP 7.1 and Drupal 8 or higher. It can be installed
using Composer:

```bash
 composer require wieni/wmbert
```

## How does it work?
### Field widget
This module provides a field widget. For more information about how to
change field widgets in content entry forms, check the [official
documentation](https://www.drupal.org/docs/user_guide/en/structure-widgets.html).

The field widget has several configuration options to change its
behaviour:

#### List formatter plugin
Changes the way the referenced entities are formatted in the table. Out
of the box, these implementations are provided:
- [_Entity title_](src/Plugin/EntityReferenceListFormatter/Title.php)
- [_Entity title (with edit link)_](src/Plugin/EntityReferenceListFormatter/TitleWithEditLink.php)
- [_Entity title and bundle_](src/Plugin/EntityReferenceListFormatter/TitleBundle.php).
- [_Entity title and bundle (with edit link)_](src/Plugin/EntityReferenceListFormatter/TitleBundleWithEditLink.php).
- [_Entity title and publishing status_](src/Plugin/EntityReferenceListFormatter/TitlePublishing.php).

Custom implementations can be provided through plugins with the
`EntityReferenceListFormatter` annotation.

#### Add entities selection
Changes the type of widget:
- _Autocomplete_: Options are loaded on demand by typing a search term.
  Works best for very large result sets.
- _Select_: Options are loaded all at once and can be chosen through a
  dropdown.
- _Radios_: Options are loaded all at once and can be chosen through
  radio buttons.

#### Disable duplicate selection
Makes sure the same entity cannot be referenced more than once in the
same widget.

#### Disable remove
Hides the button to remove individual table items.

#### Add a wrapper (fieldset)
Changes whether the widget is wrapped in a fieldset.

### Reference method
This module provides an entity reference method with some additional
features. This method is automatically enabled when changing the form
display of a field to the wmbert widget, but can also be changed
manually on the field edit page.

The reference method has several configuration options to change its
behaviour:

#### Label formatter plugin
Changes the way entities are formatted in search results. Out of the
box, two implementations are provided:
[_Entity title_](src/Plugin/EntityReferenceLabelFormatter/Title.php) and
[_Entity title and bundle_](src/Plugin/EntityReferenceLabelFormatter/TitleBundle.php).
Custom implementations can be provided through plugins with the
`EntityReferenceLabelFormatter` annotation.

#### Number of results
The number of suggestions that will be listed. Use 0 to remove the limit.

#### Same language only
Only include entities with the same language as the active content
language.

#### Disable selection of parent entity
When the widget is rendered in an entity form, this option makes sure
the entity of the form cannot be referenced in the widget.

See also the list of
[contributors](https://github.com/wieni/wmmailable/contributors) who
participated in this project.

## Changelog
All notable changes to this project will be documented in the
[CHANGELOG](CHANGELOG.md) file.

## Security
If you discover any security-related issues, please email
[security@wieni.be](mailto:security@wieni.be) instead of using the issue
tracker.

## License
Distributed under the MIT License. See the [LICENSE](LICENSE.md) file
for more information.

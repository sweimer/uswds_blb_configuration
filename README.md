# USWDS Layout Builder Configuration

## INTRODUCTION

This module is a fork of the [Bootstrap Layout Builider](https://www.drupal.org/project/bootstrap_layout_builder) module +
[Bootstrap Styles](https://www.drupal.org/project/bootstrap_styles) module. It will utilize USWDS specific classes and
configuration.

This provides configuration and integration of Bootstrap layout builder to work
within the [USWDS](https://designsystem.digital.gov/) framework.

* For a full description of the module, visit the project page:
  https://drupal.org/project/uswds_blb_configuration

* To submit bug reports and feature suggestions, or to track changes:
  https://drupal.org/project/issues/uswds_blb_configuration

## REQUIREMENTS

This module requires the following modules outside of Drupal core:

* USWDS framework's https://designsystem.digital.gov/

## Recommended Modules/Themes

* [USWDS - United States Web Design System Base](https://www.drupal.org/project/uswds_base)
  * Simple theme that provides just templates
* [USWDS Paragraph Components](https://www.drupal.org/project/uswds_paragraph_components)
  * Provides custom paragraph bundles that match up with USWDS components can
  * be used as custom block types to be used in layout builder.

### Layout Builder workarounds

One issue with layout builder is the lack of space on the off canvas sidebar.

Some workarounds
* [Paragraph Blocks](https://www.drupal.org/project/paragraph_blocks)
  * Allows users to create the components on the edit screen. And they get
    turned into blocks to be used in layout builder
* [Layout Builder iFrame Modal](https://www.drupal.org/project/layout_builder_iframe_modal)
  * Renders the layout builder sidebar in a centered iframe.

## INSTALLATION

* Install as you would normally install a contributed Drupal module. Visit:
  https://www.drupal.org/node/1897420 for further information.
* Verify installation by visiting /admin/structure/paragraphs_type and seeing
  your new Paragraph bundles.

## CONFIGURATION

* Go to /admin/config/uswds-layout-builder/breakpoints is
  the main configuration page for
  * Breakpoints
  * Layouts
  * Styles

## MAINTAINERS

* [smustgrave](https://www.drupal.org/u/smustgrave)

## Shout-outs
To the maintainers of [Bootstrap Layout Builider](https://www.drupal.org/project/bootstrap_layout_builder) module +
[Bootstrap Styles](https://www.drupal.org/project/bootstrap_styles) who did most of the leg work allowing me to fork.

To the maintainers of [Claro Media Library theme](https://www.drupal.org/project/claro_media_library_theme)

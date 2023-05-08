<?php

/**
 * @file
 * Functions for getting Media Library templates used in USWDS.
 */

/**
 * Implements hook_theme().
 */
function uswds_blb_configuration_media_library_theme($existing, $type, $theme, $path) {
  return [
    'container__media_library_content' => [
      'base hook' => 'container',
    ],
    'form__media_library_add_form_upload' => [
      'base hook' => 'form',
    ],
    'form__views_exposed_form_media_library_widget' => [
      'base hook' => 'form',
    ],
    'form__views_form_media_library_widget' => [
      'base hook' => 'form',
    ],
    'form_element_label__edit_media_library_select' => [
      'base hook' => 'form_element_label',
    ],
    'form_element_media_library' => [
      'base hook' => 'form_element',
    ],
    'input__edit_media_library' => [
      'base hook' => 'input',
    ],
    'input__submit_uswds' => [
      'base hook' => 'input_submit',
    ],
    'input__textfield' => [
      'base hook' => 'input_textfield',
    ],
    'media__media_library' => [
      'base hook' => 'media',
    ],
    'media_library_item' => [
      'render element' => 'element',
    ],
    'media_library_wrapper' => [
      'render element' => 'element',
    ],
    'views_view__media_library' => [
      'base hook' => 'views',
    ],
    'views_view_unformatted__media_library' => [
      'base hook' => 'views',
    ],
  ];
}

/**
 * Implements hook_theme_suggestions_alter().
 */
function uswds_blb_configuration_media_library_theme_suggestions_alter(array &$suggestions, array $variables, $hook) {

  if (str_contains($hook, 'input')) {
    $test = 0;
  }
  if (isCurrentPageLayoutBuilder()) {
    $original_hook = $variables['theme_hook_original'];
    $name = $variables['element']['#name'] ?? '';
    switch ($hook) {
      case 'container':
        $suggestions[] = 'container__media_library_content';
        break;

      case 'form':
        $suggestions[] = 'form__views_exposed_form_media_library_widget';
        break;

      case 'form_element':
        $suggestions[] = 'form_element_media_library';
        break;

      case 'form_element_label':
        $suggestions[] = 'form_element_label__edit_media_library_select';
        break;

      case 'input':
        if ($original_hook === 'input__textfield') {
          $suggestions[] = 'input__textfield';
        }
        elseif ($original_hook === 'input__checkbox') {
          $suggestions[] = 'input__edit_media_library';
        }
        elseif ($original_hook === 'input__submit' && $name === 'upload_upload_button') {
          $suggestions[] = 'input__submit_uswds';
        }

        break;

      case 'views_view':
        $suggestions[] = 'views_view__media_library';
        break;

      case 'views_view_unformatted':
        $suggestions[] = 'views_view_unformatted__media_library';
        break;

      default:
        break;
    }
  }
}

/**
 * Implements hook_theme_suggestion_HOOK_alter().
 */
//function uswds_blb_configuration_media_library_theme_suggestions_container_alter(array &$suggestions, array $variables) {
//  if (isCurrentPageLayoutBuilder() && !in_array('container__media_library_content', $suggestions)) {
//    $suggestions[] = 'container__media_library_content';
//  }
//}


/**
 * Implements hook_theme_suggestion_HOOK_alter().
 */
//function uswds_blb_configuration_media_library_theme_suggestions_form_alter(array &$suggestions, array $variables) {
//  if (isCurrentPageLayoutBuilder() && !in_array('form__views_exposed_form_media_library_widget', $suggestions)) {
//    $suggestions[] = 'form__views_exposed_form_media_library_widget';
//  }
//}

/**
 * Implements hook_theme_suggestion_HOOK_alter().
 */
//function uswds_blb_configuration_media_library_theme_suggestions_form_element_label_alter(array &$suggestions, array $variables) {
//  if (isCurrentPageLayoutBuilder() && !in_array('form_element_label__edit_media_library_select', $suggestions)) {
//    $suggestions[] = 'form_element_label__edit_media_library_select';
//  }
//}

/**
 * Implements hook_theme_suggestion_HOOK_alter().
 */
//function uswds_blb_configuration_media_library_theme_suggestions_form_element_alter(array &$suggestions, array $variables) {
//  if (isCurrentPageLayoutBuilder() && !in_array('form_element_media_library', $suggestions)) {
//    $suggestions[] = 'form_element_media_library';
//  }
//}

/**
 * Implements hook_theme_suggestion_HOOK_alter().
 */
//function uswds_blb_configuration_media_library_theme_suggestions_input_textfield_alter(array &$suggestions, array $variables) {
//  if (isCurrentPageLayoutBuilder() && !in_array('input__edit_media_library', $suggestions)) {
//    $suggestions[] = 'input__edit_media_library';
//  }
//  if (isCurrentPageLayoutBuilder() && !in_array('input__textfield', $suggestions)) {
//    $suggestions[] = 'input__textfield';
//  }
//}

/**
 * Implements hook_theme_suggestion_HOOK_alter().
 */
//function uswds_blb_configuration_media_library_theme_suggestions_input_submit_alter(array &$suggestions, array $variables) {
//  if (isCurrentPageLayoutBuilder() && !in_array('input__submit_uswds', $suggestions)) {
//    $suggestions[] = 'input__submit_uswds';
//  }
//}

/**
 * Implements hook_theme_suggestion_HOOK_alter().
 */
function uswds_blb_configuration_media_library_theme_suggestions_media_library_alter(array &$suggestions, array $variables) {
  if (isCurrentPageLayoutBuilder() && !in_array('media__media_library', $suggestions)) {
    $suggestions[] = 'media__media_library';
    $suggestions[] = 'media_library_item';
    $suggestions[] = 'media_library_wrapper';
  }
}

/**
 * Implements hook_theme_suggestion_HOOK_alter().
 */
//function uswds_blb_configuration_media_library_theme_suggestions_views_view_alter(array &$suggestions, array $variables) {
//  if (isCurrentPageLayoutBuilder() && !in_array('views_view__media_library', $suggestions)) {
//    $suggestions[] = 'views_view__media_library';
//  }
//}

/**
 * Implements hook_theme_suggestions_HOOK_alter().
 */
//function uswds_blb_configuration_media_library_theme_suggestions_views_view_unformatted_alter(array &$suggestions, array $variables) {
//  if (isCurrentPageLayoutBuilder() && !in_array('views_view_unformatted__media_library', $suggestions)) {
//    $suggestions[] = 'views_view_unformatted__media_library';
//  }
//}

// Helper function

/**
 * Checks if current path is layout builder.
 *
 * @return bool
 *   If current page is layout builder
 */
function isCurrentPageLayoutBuilder(): bool {
  $current_path = \Drupal::service('path.current')->getPath();
  if (preg_match('/layout_builder\/configure/', $current_path)) {
    return TRUE;
  }
  return FALSE;
}

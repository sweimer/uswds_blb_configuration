/**
 * @file
 * Behaviors shadow plugin group.
 */

(function ($, Drupal, once) {

  // Shadow preview box.
  Drupal.behaviors.shadowPreview = {
    attach: function (context) {

      let previewElement = $('.uswds-shadow-preview [data-uswds-element="uswds_preview-element"]');

      // Refresh preview classes.
      function refreshShadowClasses(el) {

        let shadow_classes = '';

        // Setup our shadow classes.
        $('input.uswds-field-box-shadow').each(function() {
          if ($(this).is(':checked') && $(this).val() !== '_none') {
            shadow_classes += $(this).val() + ' ';
          }
        });

        // Remove all classes.
        previewElement.removeClass();

        // Then add our selected class.
        previewElement.addClass(shadow_classes);
      }

      refreshShadowClasses();

      // Refresh the box shadow classes on change.
      let input_triggers = [
        'input[class^="uswds-field-box-shadow"]',
      ];

      $.each(input_triggers, function (index, value) {
        $(value, context).on('change', function() {
          refreshShadowClasses();
        });
      });

      // Toggle our bg color mode.
      function togglePreviewBackgroundColor(el) {
        let previewBgElement = el.closest('[data-uswds-element="uswds_preview-box"]');
        previewBgElement.attr('data-uswds-mode', previewBgElement.attr('data-uswds-mode') === 'light' ? 'dark' : 'light');
      }

      $('.uswds-shadow-preview .uswds-toggle-switch', context).on('change', function() {
        togglePreviewBackgroundColor($(this));
      });

    }
  };

})(jQuery, Drupal, once);

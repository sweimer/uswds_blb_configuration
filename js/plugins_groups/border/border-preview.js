/**
 * @file
 * Behaviors border plugin group.
 */

(function ($, Drupal, once) {
  // Border preview box.
  Drupal.behaviors.borderPreview = {
    attach: function (context,settings) {
      let border_width = settings.uswds_blb_configuration.border.border_width;
      let rounded_corners = settings.uswds_blb_configuration.border.rounded_corners;
      let directions = ['left', 'top', 'right', 'bottom'];
      let corners = ['top_left', 'top_right', 'bottom_left', 'bottom_right'];

      // Refresh preview Classes.
      function refreshPreviewClasses() {
        let border_classes = '';

        // Border style.
        $('input.uswds-field-border-style').each(function() {
          if ($(this).is(':checked') && $(this).val() !== '_none') {
            border_classes += $(this).val() + ' ';
          }
        });

        // Border width.
        let border_width_val = $('input.uswds-field-border-width').val();
        let border_width_class = border_width.border_width[border_width_val];
        if (border_width_class !== '_none') {
          border_classes += border_width_class + ' ';
        }

        // Border color.
        $('input.uswds-field-border-color').each(function() {
          if ($(this).is(':checked') && $(this).val() !== '_none') {
            border_classes += $(this).val() + ' ';
          }
        });

        // Loop through the directions.
        for (let i = 0; i < directions.length; i++) {
          // Border style.
          $('input.uswds-field-border-style-' + directions[i]).each(function() {
            if ($(this).is(':checked') && $(this).val() !== '_none') {
              border_classes += $(this).val() + ' ';
            }
          });

          // Border width
          border_width_val = $('input.uswds-field-border-width-' + directions[i]).val();
          if (border_width_val) {
            border_width_class = border_width['border_' + directions[i] + '_width'][border_width_val];
            if (border_width_class !== '_none') {
              border_classes += border_width_class + ' ';
            }
          }

          // Border color.
          $('input.uswds-field-border-color-' + directions[i]).each(function() {
            if ($(this).is(':checked') && $(this).val() !== '_none') {
              border_classes += $(this).val() + ' ';
            }
          });
        }

        // Rounded corners
        let rounded_corners_val = $('input.uswds-field-rounded-corners').val();
        let rounded_corners_class = rounded_corners.rounded_corners[rounded_corners_val];
        if (rounded_corners_class !== '_none') {
          border_classes += rounded_corners_class + ' ';
        }

        // Loop through the corners.
        for (let i = 0; i < corners.length; i++) {
          rounded_corners_val = $('input.uswds-field-rounded-corner-' + corners[i]).val();
          if (rounded_corners_val) {
            rounded_corners_class = rounded_corners['rounded_corner_' + corners[i]][rounded_corners_val];
            if (rounded_corners_class !== '_none') {
              border_classes += rounded_corners_class + ' ';
            }
          }
        }

        // Remove all classes.
        $('#uswds-border-preview').removeClass();
        // Then add the round corner classes.
        $('#uswds-border-preview').addClass(border_classes);
      }

      refreshPreviewClasses();

      // Refresh the border classes on change.
      let input_triggers = [
        'input[class^="uswds-field-border-style"]',
        'input[class^="uswds-field-border-width"]',
        'input[class^="uswds-field-border-color"]',
        'input[class^="uswds-field-rounded-corner"]'
      ];

      $.each(input_triggers, function (index, value) {
        $(value, context).on('change', function() {
          $(this).parents('.fieldset-wrapper').addClass('style-selected');
          refreshPreviewClasses();
        });
      });

    }
  };

})(jQuery, Drupal, once);

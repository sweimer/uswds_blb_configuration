/**
 * @file
 * Behaviors spacing plugin group.
 */

(function ($, Drupal, once) {
  "use strict";

  // Spacing preview box.
  Drupal.behaviors.spacingPreview = {
    attach: function (context,settings) {
      var spacing = settings.uswds_blb_configuration.spacing;
      var breakpoints = settings.uswds_blb_configuration.breakpoints;

      var padding_box_shadow = $('.spacing-preview .padding-box').css('box-shadow');
      var margin_box_shadow = $('.spacing-preview .margin-box').css('box-shadow');
      var box_shadow = '0 0 0 1.5rem';

      // Padding.
      function calcPadding() {
        var padding_val = $('input.uswds-field-padding').val();
        var padding_left_val = $('input.uswds-field-padding-left').val();
        var padding_top_val = $('input.uswds-field-padding-top').val();
        var padding_right_val = $('input.uswds-field-padding-right').val();
        var padding_bottom_val = $('input.uswds-field-padding-bottom').val();
        var padding_classes = '';
        var padding_class = spacing.padding_classes_options.padding[padding_val];
        if (padding_class != '_none') {
          padding_classes += padding_class + ' ';
        }
        var padding_left_class = spacing.padding_classes_options.padding_left[padding_left_val];
        if (padding_left_class != '_none') {
          padding_classes += padding_left_class + ' ';
        }
        var padding_top_class = spacing.padding_classes_options.padding_top[padding_top_val];
        if (padding_top_class != '_none') {
          padding_classes += padding_top_class + ' ';
        }
        var padding_right_class = spacing.padding_classes_options.padding_right[padding_right_val];
        if (padding_right_class != '_none') {
          padding_classes += padding_right_class + ' ';
        }
        var padding_bottom_class = spacing.padding_classes_options.padding_bottom[padding_bottom_val];
        if (padding_bottom_class != '_none') {
          padding_classes += padding_bottom_class + ' ';
        }

        // Responsive.
        padding_classes += getActiveBreakpointPaddingClasses();

        // Remove all classes.
        $('#uswds_spacing_preview_calc').removeClass();
        // Then add the padding classes.
        $('#uswds_spacing_preview_calc').addClass(padding_classes);

        $('.spacing-preview .padding-left').text(parseInt($('#uswds_spacing_preview_calc').css('padding-left')));
        $('.spacing-preview .padding-top').text(parseInt($('#uswds_spacing_preview_calc').css('padding-top')));
        $('.spacing-preview .padding-right').text(parseInt($('#uswds_spacing_preview_calc').css('padding-right')));
        $('.spacing-preview .padding-bottom').text(parseInt($('#uswds_spacing_preview_calc').css('padding-bottom')));
      }

      // Responsive.
      function getActiveBreakpointPaddingClasses() {
        var padding_classes = '';
        var active_breakpoint = $('.uswds_responsive_spacing input:checked').val();

        if (typeof active_breakpoint !== 'undefined') {
          var i;
          var padding_classes = '';
          for (i = 0; i < breakpoints.length; i++) {
            if (active_breakpoint == breakpoints[i]) {
              var padding_val = $('input.uswds-field-padding-' + breakpoints[i]).val();
              var padding_left_val = $('input.uswds-field-padding-left-' + breakpoints[i]).val();
              var padding_top_val = $('input.uswds-field-padding-top-' + breakpoints[i]).val();
              var padding_right_val = $('input.uswds-field-padding-right-' + breakpoints[i]).val();
              var padding_bottom_val = $('input.uswds-field-padding-bottom-' + breakpoints[i]).val();
              var padding_class = spacing.padding_classes_options['padding_' + breakpoints[i]][padding_val];
              if (padding_class != '_none') {
                padding_classes += padding_class + ' ';
              }
              var padding_left_class = spacing.padding_classes_options['padding_left_' + breakpoints[i]][padding_left_val];
              if (padding_left_class != '_none') {
                padding_classes += padding_left_class + ' ';
              }
              var padding_top_class = spacing.padding_classes_options['padding_top_' + breakpoints[i]][padding_top_val];
              if (padding_top_class != '_none') {
                padding_classes += padding_top_class + ' ';
              }
              var padding_right_class = spacing.padding_classes_options['padding_right_' + breakpoints[i]][padding_right_val];
              if (padding_right_class != '_none') {
                padding_classes += padding_right_class + ' ';
              }
              var padding_bottom_class = spacing.padding_classes_options['padding_bottom_' + breakpoints[i]][padding_bottom_val];
              if (padding_bottom_class != '_none') {
                padding_classes += padding_bottom_class + ' ';
              }
            }
          }
        }

        return padding_classes;
      }

      // Margin.
      function calcMargin() {
        var margin_val = $('input.uswds-field-margin').val();
        var margin_left_val = $('input.uswds-field-margin-left').val();
        var margin_top_val = $('input.uswds-field-margin-top').val();
        var margin_right_val = $('input.uswds-field-margin-right').val();
        var margin_bottom_val = $('input.uswds-field-margin-bottom').val();

        var margin_classes = '';
        var margin_class = spacing.margin_classes_options.margin[margin_val];
        if (margin_class != '_none') {
          margin_classes += margin_class + ' ';
        }
        var margin_left_class = spacing.margin_classes_options.margin_left[margin_left_val];
        if (margin_left_class != '_none') {
          margin_classes += margin_left_class + ' ';
        }
        var margin_top_class = spacing.margin_classes_options.margin_top[margin_top_val];
        if (margin_top_class != '_none') {
          margin_classes += margin_top_class + ' ';
        }
        var margin_right_class = spacing.margin_classes_options.margin_right[margin_right_val];
        if (margin_right_class != '_none') {
          margin_classes += margin_right_class + ' ';
        }
        var margin_bottom_class = spacing.margin_classes_options.margin_bottom[margin_bottom_val];
        if (margin_bottom_class != '_none') {
          margin_classes += margin_bottom_class + ' ';
        }

        // Remove all classes.
        $('#uswds_spacing_preview_calc').removeClass();
        // Then add the margin classes.
        $('#uswds_spacing_preview_calc').addClass(margin_classes);

        $('.spacing-preview .margin-left').text(parseInt($('#uswds_spacing_preview_calc').css('margin-left')));
        $('.spacing-preview .margin-top').text(parseInt($('#uswds_spacing_preview_calc').css('margin-top')));
        $('.spacing-preview .margin-right').text(parseInt($('#uswds_spacing_preview_calc').css('margin-right')));
        $('.spacing-preview .margin-bottom').text(parseInt($('#uswds_spacing_preview_calc').css('margin-bottom')));
      }

      // Calculate the padding on load.
      calcPadding();
      // Calculate the maring on load.
      calcMargin();

      // Padding Actions
      // Calculate the padding on change.
      $('input[class^="uswds-field-padding"], .uswds_responsive_spacing input', context).on('change', function() {
        calcPadding();
      });

      // On focus, gray out margin box so we can "focus" on changing padding values.
      $('input[class^="uswds-field-padding"]', context).on('focus', function() {
        var panel_bg = $(this).parents('details').find('summary').css('background-color');
        var inactive_box_shadow = box_shadow + ' ' + panel_bg;
        $('.spacing-preview .margin-box').css('box-shadow', inactive_box_shadow);
        $('.spacing-preview .padding-box').addClass('uswds-adjusting');
      });

      // On focusout, turn it back to our original colour.
      $('input[class^="uswds-field-padding"]', context).on('focusout', function() {
        $('.spacing-preview .margin-box').css('box-shadow', margin_box_shadow);
        $('.spacing-preview .padding-box').removeClass('uswds-adjusting');
      });


      // Margin Actions
      // Calculate the margin on change.
      $('input[class^="uswds-field-margin"]', context).on('change', function() {
        calcMargin();
      });

      // On focus, gray out margin box so we can "focus" on changing padding values.
      $('input[class^="uswds-field-margin"]', context).on('focus', function() {
        var panel_bg = $(this).parents('details').find('summary').css('background-color');
        var inactive_box_shadow = box_shadow + ' ' + panel_bg;
        $('.spacing-preview .padding-box').css('box-shadow', inactive_box_shadow);
        $('.spacing-preview .margin-box').addClass('uswds-adjusting');
      });

      // On focusout, turn it back to our original colour.
      $('input[class^="uswds-field-margin"]', context).on('focusout', function() {
        $('.spacing-preview .padding-box').css('box-shadow', padding_box_shadow);
        $('.spacing-preview .margin-box').removeClass('uswds-adjusting');
      });


    }
  };

})(jQuery, Drupal, once);

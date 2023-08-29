/**
 * @file
 * Behaviors Text Color plugin layout builder form scripts.
 */

(function ($, Drupal, once) {
  // Text color.
  Drupal.behaviors.textColorLayoutBuilderForm = {
    attach: function (context) {

      once('blb_text-color',".fieldgroup.field-text-color input[type=radio]", context).forEach(function (value) {
        $(value).next('label').addClass($(value).val());

        // Attach the color as a background color to the label AFTER adding the class.
        if ($(value).val() !== '_none') {
          let label_color = $(value).next('label').css('color');
          $(value).next('label').css('background-color', label_color);

          // Set a contrast class, so we can see our checkmarks on light vs. dark backgrounds.
          let bgColor = $(value).next('label').css('background-color');
          let bgColorHex = rgb2hex(bgColor);
          let bgColorContrast = getContrast(bgColorHex);
          $(value).next('label').addClass('uswds_yiq-' + bgColorContrast);
        }
      });

      $(".fieldgroup.field-text-color .fieldset-wrapper label", context).on('click', function () {

        $(this).parents('.fieldset-wrapper').find('label').removeClass('active');
        if ($(this).hasClass('_none')) {
          $(this).parents('.fieldset-wrapper').removeClass('style-selected');
        }
      });

      // Custom solution for bootstrap 3 & Bario drupal theme issues.
      $(".fieldgroup.field-text-color .fieldset-wrapper input:radio", context).each(function () {
        $(this).closest('.radio').find('label').addClass($(this).val());
        let checked = $(this).prop("checked");
        if (typeof checked !== typeof 'undefined' && checked !== false) {
          $(this).closest('.radio').find('label').addClass('active');
        }
      });
    }
  };

})(jQuery, Drupal, once);

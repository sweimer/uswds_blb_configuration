/**
 * @file
 * Behaviors Bootstrap styles tabs scripts.
 */

(function ($, _, Drupal, drupalSettings) {
  "use strict";

  Drupal.behaviors.bootstrapStylesTabs = {
    attach: function (context) {

      $('#uswds_nav-tabs li a', context).each(function() {
        $(this).on('click', function () {
          $('#uswds_nav-tabs li a', context).removeClass('active');
          $(this).toggleClass('active');
          var href = $(this).attr('data-target');
          if(href && $('#uswds_tabContent').length) {
            $('.uswds_tab-pane', context).removeClass('active');
            $('.uswds_tab-pane--' + href, context).addClass('active');
          }
        });
      })

    }
  };

})(window.jQuery, window._, window.Drupal, window.drupalSettings);

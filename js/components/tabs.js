/**
 * @file
 * Behaviors Bootstrap styles tabs scripts.
 */

// eslint-disable-next-line func-names
(function ($, _, Drupal, drupalSettings) {
  Drupal.behaviors.bootstrapStylesTabs = {
    attach(context) {
      // eslint-disable-next-line func-names
      $("#uswds_nav-tabs li a", context).each(function () {
        // eslint-disable-next-line func-names
        $(this).on("click", function () {
          $("#uswds_nav-tabs li a", context).removeClass("active");
          $(this).toggleClass("active");
          const href = $(this).attr("data-target");
          if (href && $("#uswds_tabContent").length) {
            $(".uswds_tab-pane", context).removeClass("active");
            $(`.uswds_tab-pane--${href}`, context).addClass("active");
          }
        });
      });
    },
  };
})(window.jQuery, window._, window.Drupal, window.drupalSettings);

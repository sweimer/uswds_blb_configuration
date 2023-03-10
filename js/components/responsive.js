/**
 * @file
 * Behaviors Bootstrap styles responsive preview scripts.
 */

// eslint-disable-next-line func-names
(function ($, Drupal, once) {
  Drupal.behaviors.uswdsStylesResponsiveTooltip = {
    attach(context) {
      $(window).on("dialog:beforecreate", (event, dialog, $element) => {
        if (Drupal.offCanvas.isOffCanvas($element)) {
          // Allows us to attach tooltips to radio option labels.
          $("svg[data-uswds-responsive-tooltip-label]").each(function () {
            const placement = $(this).attr("data-uswds-tooltip-placement")
              ? $(this).attr("data-uswds-tooltip-placement")
              : "top";
            const label = $(this).attr("data-uswds-responsive-tooltip-label")
              ? $(this).attr("data-uswds-responsive-tooltip-label")
              : "";

            if (!$(this).siblings(".uswds_tooltip").length) {
              $(this).after(
                `<div class="uswds_tooltip test" data-placement="${placement}" role="tooltip">${label}</div>`
              );
            }
          });
        }
      });
    },
  };
})(jQuery, Drupal, once);

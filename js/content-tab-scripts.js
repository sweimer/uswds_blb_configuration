/**
 * @file
 * Behaviors Layout Builder Blocks content tab scripts.
 */

(function ($, _, Drupal, drupalSettings) {
  Drupal.behaviors.LayoutBuilderBlocksContentTab = {
    attach: function (context) {

      // Move the original block fields to content tab.
      $('form.layout-builder-configure-block > :not(#uswds_ui):not(.form-submit)').each(function() {
        $('form.layout-builder-configure-block > #uswds_ui > #uswds_tabContent > .uswds_tab-pane--content').append($(this));
      });

    }
  };

})(window.jQuery, window._, window.Drupal, window.drupalSettings);

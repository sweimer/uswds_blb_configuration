/**
 * @file
 * Behaviors of Boostrap Layout Builder local video background.
 */

(($, _, Drupal) => {
  Drupal.behaviors.backgroundMediaBuildLocalVideoBG = {
    attach() {
      let doit;
      function videoHeight() {
        // Set the height of the background video.
        // eslint-disable-next-line func-names
        $(".background-local-video").each(function () {
          // eslint-disable-next-line func-names
          $(this).height(function () {
            return $(this).find(".video-content > div").outerHeight();
          });
        });
      }

      $(window).resize(() => {
        clearTimeout(doit);
        doit = setTimeout(videoHeight, 100);
      });
    },
  };
})(window.jQuery, window._, window.Drupal, window.drupalSettings);

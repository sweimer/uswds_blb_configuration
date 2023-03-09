/**
 * @file
 * Behaviors Bootstrap styles responsive preview scripts.
 */

(function ($, Drupal, once) {
  "use strict";

  // Do all the things to kill our previewer.
  $(document).on("keyup", function (e) {
    if (e.key == "Escape") {
      if ($('.uswds-responsive-preview-wrapper').length) {
        destroyPreviewer();
      }
    }
  });

  /**
   *
   */
  function setTempStore(key, value) {
    $.ajax({
      url: Drupal.url('uswds_blb_configuration/ajax/temp_store/set'),
      type: 'POST',
      data: {
        key: key,
        value: value
      },
      dataType: 'json'
    });
  }

  /**
   * Load up our responsive plugin previewer.
   */
  function loadPreviewer(currentDevice) {
    var devices = {
      'desktop': '1400',
      'laptop': '1199',
      'tablet': '768',
      'mobile': '460',
    };

    // Solve highlight after refresh the section.
    var highlightId = $('form.layout-builder-configure-section').attr('data-layout-builder-target-highlight-id');
    if (highlightId) {
      $('[data-layout-builder-highlight-id="' + highlightId + '"]').addClass(
        'is-layout-builder-highlighted',
      );
    }

    // Close any old dialogs.
    destroyPreviewer();

    if (currentDevice == 'all') {
      return;
    }

    var $clonedDom = $('html').clone();

    var $iframeWrapper = $('' +
      '<div id="uswds-responsive-preview-wrapper" class="uswds-responsive-preview-wrapper">' +
      '<div class="uswds-responsive-preview-scroll-track">' +
      '<div class="uswds-responsive-preview-scroll-pane">' +
      '<div class="uswds-responsive-preview-container">' +
      '</div>' +
      '</div>' +
      '</div>' +
      '</div>');

    var $iframe = $('<iframe id="uswds-responsive-preview" width="100%" height="100%" frameborder="0" scrolling="auto" allowtransparency="true"></iframe>')
      .on('load', function () {
        var highlightedSection = $(this).contents().find("#uswds-preview-highlighted");
        this.contentWindow.scrollTo(0, highlightedSection.offset().top);
      });

    // append the iframe to our deepest div
    $iframeWrapper.find('.uswds-responsive-preview-container').append($iframe);

    // append the whole previewer to the window
    $($iframeWrapper).appendTo('body');

    // set the device width
    $iframeWrapper.find('.uswds-responsive-preview-container').css({
      'width': devices[currentDevice],
    });

    // Add Id selector to the highlighted section.
    $clonedDom.find('.is-layout-builder-highlighted').attr('id', 'uswds-preview-highlighted');

    var removedClasses = [
      'layout-builder',
      'layout-builder__region',
      'layout-builder-block',
      'is-layout-builder-highlighted',
      'js-off-canvas-dialog-open',
    ];

    for (var i = 0; i < removedClasses.length; i++) {
      $clonedDom.find('.' + removedClasses[i]).removeClass(removedClasses[i]);
    }

    var removedElementsBySelector = [
      '.layout-builder__add-section',
      '.layout-builder__add-block',
      '.layout-builder__link',
      '.layout-builder-form',
      '.ui-dialog',
      '.ui-widget-overlay',
    ];

    for (i = 0; i < removedClasses.length; i++) {
      $clonedDom.find(removedElementsBySelector[i]).remove();
    }

    // Remove style attr with its padding.
    $clonedDom.find('.dialog-off-canvas-main-canvas').removeAttr('style');

    // Write the iframe content.
    var $filterdHtml = $clonedDom.html();
    var $iframeDoc = $iframe[0].contentDocument || $iframe[0].contentWindow.document;
    $iframeDoc.write($filterdHtml);
    $iframe.contents().find('.uswds-ui-widget-overlay').remove();
    $iframeDoc.close();

    setPreviewerSize();
  }

  /**
   * Close our responsive plugin previewer.
   */
  function destroyPreviewer() {
    $('#uswds-responsive-preview-wrapper').remove();
  }

  /**
   * Sets the viewport width and height dimensions on the envModel.
   */
  function setPreviewerSize() {
    if ($('#drupal-off-canvas').length > 0) {
      var viewportWidth = document.documentElement.clientWidth;
      var offcanvasWidth = $('#drupal-off-canvas').css('width');
      var toolbarHeight = $('.ui-dialog-off-canvas').css('top');

      $('.uswds-responsive-preview-container').css('max-width', (viewportWidth - offcanvasWidth));
      $('.uswds-responsive-preview-scroll-pane').css({
        'padding-right': offcanvasWidth,
        'padding-top': toolbarHeight
      });
    }
  }

  // Init all of our responsive previewer stuff.
  Drupal.behaviors.bootstrapStylesResponsivePreview = {
    attach: function (context) {

      // Listen to uswds_responsive device click.
      once("input_uswds_responsive", "input.uswds_responsive", context).forEach(function (value, i) {
        value.onclick = function () {
          var currentDevice = $(this).val();
          // set it in the temp store.
          setTempStore('active_device', currentDevice);
          loadPreviewer(currentDevice);
        };
      });

      // Close our previewer if the offcanvas menu is closed.
      once("click-ui-dialog-titlebar-close", document, context).forEach(function (value, i) {
        value.onclick = function () {
          destroyPreviewer();
        }
      });

      // Our resize handler.
      $(window).on('load resize', Drupal.debounce(function () {
        setPreviewerSize();
      }, 300));
    }
  };


  /**
   * Refresh responsive command.
   */
  Drupal.AjaxCommands.prototype.uswds_refresh_responsive = function (ajax, response, status) {
    var currentDevice = response.data.active_device;
    if (currentDevice != 'all') {
      $('input.uswds_responsive[value=' + currentDevice + ']').click();
    }
  };

  $(window).on({
    'dialog:beforecreate': function dialogBeforecreate(event, dialog, $element) {
      if ($element.is('#drupal-off-canvas')) {
        // Reset the active device to all.
        setTempStore('active_device', 'all');
      }
    },
    'dialog:beforeclose': function dialogBeforeclose(event, dialog, $element) {
      if ($element.is('#drupal-off-canvas')) {
        // Reset the active device to all.
        setTempStore('active_device', 'all');
      }
    }
  });

  Drupal.behaviors.bootstrapStylesResponsiveTooltip = {
    attach: function (context) {

      // Allows us to attach tooltips to radio option labels.
      once('uswds-svg-tooltips', "svg[data-uswds-tooltip-label]", context).forEach(function (value, i) {
        var placement = $(value).attr('data-uswds-tooltip-placement') ? $(value).attr('data-uswds-tooltip-placement') : 'top';
        var label = $(value).attr('data-uswds-tooltip-label') ? $(value).attr('data-uswds-tooltip-label') : '';
        $(value).after('<div class="uswds_tooltip" data-placement="' + placement + '" role="tooltip">' + label + '</div>');
      });

    }
  };

})(jQuery, Drupal, once);

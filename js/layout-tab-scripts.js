/**
 * @file
 * Behaviors USWDS Layout Builder general scripts.
 */

(function ($, Drupal, once) {
  // Configure Section.
  Drupal.behaviors.bootstrapLayoutBuilderConfigureSection = {
    attach(context) {
      // Graphical Layout Columns
      $(".uswds_breakpoint_cols", context).each(function () {
        const numOfCols = 12;
        // .custom-control, .custom-radio to solve Bario issues.
        once(
          "custom-control-custom-radio",
          $(this).find(".form-item, .custom-control, .custom-radio"),
          context
        ).forEach(function (elem, i) {
          const cols = $(elem).find("input").val().replace("blb_col_", "");
          const colsConfig = cols.split("_");
          const colsLabel = $(elem).find("label");
          let col_classes = "uswds_breakpoint_col";
          const checked = $(elem).find("input").prop("checked");
          if (typeof checked !== typeof 'undefined' && checked !== false) {
            col_classes += " uswds-selected";
          }

          // Wrap our radio labels and display as a tooltip.
          colsLabel.wrapInner(
            '<div class="uswds_tooltip uswds_tooltip-lg"></div>'
          );

          // Provide a graphical representation of the columns via some nifty divs styling.
          $.each(colsConfig, function (index, value) {
            const width = (value / numOfCols) * 100;
            $("<div />", {
              text: `${width.toFixed(0)}%`,
              style: `width:${width}%;`,
              class: col_classes,
            })
              .appendTo(colsLabel)
              .on("click", function () {
                $(this)
                  .parents(".uswds_breakpoint_cols")
                  .find(".uswds_breakpoint_col")
                  .removeClass("uswds-selected");
                $(this)
                  .parents(".uswds_breakpoint_cols")
                  .find("input")
                  .prop("checked", false);
                $(this)
                  .parents("label")
                  .parent()
                  .find("input")
                  .prop("checked", true);
                $(this)
                  .parents("label")
                  .find(".uswds_breakpoint_col")
                  .addClass("uswds-selected");
              });
          });
        });
      });

      // Auto-sized textareas.
      $("textarea.blb-auto-size", context).each(function () {
        this.setAttribute(
          "style",
          `height:${this.scrollHeight}px;overflow-y:hidden;min-height:60px!important;padding:.65rem 1rem;`
        );
      });
    },
  };
})(jQuery, Drupal, once);

/**
 * @file
 * tabledrag.js overrides and functionality extensions.
 */

(($, Drupal) => {
  /**
   * Extends core's Tabledrag functionality.
   *
   * @type {Drupal~behavior}
   */
    /**
     * Add an asterisk or other marker to the changed row.
     *
     * @todo this may be removable as part of https://drupal.org/node/3084910
     */

    /**
     *
     * @see Drupal.tableDrag.prototype.row.prototype.indent
     *
     * @todo this may be removable as part of https://drupal.org/node/3083044
     */


  $.extend(
    Drupal.theme,
    /** @lends Drupal.theme */ {
      /**
       * Constructs the table drag changed marker.
       *
       * @return {string}  /**
       * Constructs the table drag handle.
       *
       * @return {string}
       *   A string representing a DOM fragment.
       */
      tableDragHandle: () =>
        `<a href="#" title="${Drupal.t(
          'Drag to re-order',
        )}" class="tabledrag-handle js-tabledrag-handle"></a>`,

      /**
       * The button for toggling table row weight visibility.
       *
       * @return {string}
       *   HTML markup for the weight toggle button and its container.
       */
      tableDragToggle: () =>
        `<div class="tabledrag-toggle-weight-wrapper" data-drupal-selector="tabledrag-toggle-weight-wrapper">
            <button type="button" class="link action-link tabledrag-toggle-weight" data-drupal-selector="tabledrag-toggle-weight"></button>
            </div>`,

      /**
       * Constructs contents of the toggle weight button.
       *
       * @param {boolean} show
       *   If the table weights are currently displayed.
       *
       * @return {string}
       *  HTML markup for the weight toggle button content.
       */
      toggleButtonContent: (show) => {
        const classes = [
          'action-link',
          'action-link--extrasmall',
        ];
        let text = '';
        if (show) {
          classes.push('action-link--icon-hide');
          text = Drupal.t('Hide row weights');
        } else {
          classes.push('action-link--icon-show');
          text = Drupal.t('Show row weights');
        }
        return `<span class="${classes.join(' ')}">${text}</a>`;
      },

      /**
       * Constructs the wrapper for the initial content of the drag cell.
       *
       * @return {string}
       *   A string representing a DOM fragment.
       */
      tableDragCellContentWrapper() {
        return '<div class="tabledrag-cell-content__item"></div>';
      },

      /**
       * Constructs the wrapper for the whole table drag cell.
       *
       * @return {string}
       *   A string representing a DOM fragment.
       */
      tableDragCellItemsWrapper() {
        return '<div class="tabledrag-cell-content"></div>';
      },
    },
  );
})(jQuery, Drupal);

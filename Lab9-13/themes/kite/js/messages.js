(function ($) {
  Drupal.behaviors.closeMessage = {
    attach: function (context, settings) {
      // Close the status messages when the close button is clicked.
      $('.messages__wrapper').on('click', '.button--dismiss', function () {
        $(this).closest('.messages__wrapper').fadeOut();
      });
    }
  };
})(jQuery);
(function ($) {


jQuery(document).ready(function($) {
  $('.full-page-search .search-icon a').click(function(e) {
    e.preventDefault(); // Prevent default link behavior
    $('.full-page-search .search-box').fadeIn(); // Show the search box
  });
  $('.full-page-search .search-box-close').click(function() {
    $('.full-page-search .search-box').fadeOut(); // Hide the search box
  });
});


jQuery(document).ready(function ($) {
  // Mobile menu.
  $('.mobile-menu').click(function () {
    $(this).next('.primary-menu-wrapper').toggleClass('active-menu');
  });
  $('.close-mobile-menu').click(function () {
    $(this).closest('.primary-menu-wrapper').toggleClass('active-menu');
  });
});

// Drupal.behaviors.mobileMenuToggle = {
//    attach: function (context, settings) {
//       const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
//       const mobileMenu = document.querySelector('.mobile-menu');
//       if (mobileMenuToggle) {
//         mobileMenuToggle.addEventListener('click', function() {
//           mobileMenu.classList.toggle('is-active');
//           console.log('here');
//         });
//       }
//       console.log('jd');
//    }
// }
/* Toggle the mobile menu when the button is clicked */

Drupal.behaviors.mobileMenuToggle = {
   attach: function (context, settings) {
document.querySelector('.navbar-toggle').addEventListener('click', function() {
  document.querySelector('.navbar-collapse').classList.toggle('show');
});
   }
  }

})(jQuery);

const cursor = document.querySelector('.cursor');

document.addEventListener('mousemove', e => {
    cursor.setAttribute("style", "top: " + (e.pageY - 10) + "px; left: " + (e.pageX - 10) + "px;")
});

document.addEventListener('click', e => {
    cursor.classList.add("expand");
    setTimeout(() => {
        cursor.classList.remove("expand");
    }, 500);
    console.log('cursor');  
});
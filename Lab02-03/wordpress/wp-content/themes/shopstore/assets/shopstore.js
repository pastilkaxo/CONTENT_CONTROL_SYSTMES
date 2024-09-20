;(function($) {

   'use strict'
        var isMobile = {
            Android: function() {
                return navigator.userAgent.match(/Android/i);
            },
            BlackBerry: function() {
                return navigator.userAgent.match(/BlackBerry/i);
            },
            iOS: function() {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i);
            },
            Opera: function() {
                return navigator.userAgent.match(/Opera Mini/i);
            },
            Windows: function() {
                return navigator.userAgent.match(/IEMobile/i);
            },
            any: function() {
                return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
            }
        }; // is Mobile

       
		var back_to_top_scroll = function() {
			
			$('#backToTop').on('click', function() {
				$("html, body").animate({ scrollTop: 0 }, 500);
				return false;
			});
			
			$(window).scroll(function() {
				if ( $(this).scrollTop() > 100 ) {
					
					$('#backToTop').addClass('active');
				} else {
				  
					$('#backToTop').removeClass('active');
				}
				
			});
			
		}; // Responsive Menu   
		

		//Trap focus inside mobile menu modal
		//Based on https://codepen.io/eskjondal/pen/zKZyyg	
		var trapFocusInsiders = function(elem) {
			
				
			var tabbable = elem.find('select, input, textarea, button, a').filter(':visible');
			
			var firstTabbable = tabbable.first();
			var lastTabbable = tabbable.last();
			/*set focus on first input*/
			firstTabbable.focus();
			
			/*redirect last tab to first input*/
			lastTabbable.on('keydown', function (e) {
			   if ((e.which === 9 && !e.shiftKey)) {
				   e.preventDefault();
				   
				   firstTabbable.focus();
				  
			   }
			});
			
			/*redirect first shift+tab to last input*/
			firstTabbable.on('keydown', function (e) {
				if ((e.which === 9 && e.shiftKey)) {
					e.preventDefault();
					lastTabbable.focus();
				}
			});
			
			/* allow escape key to close insiders div */
			elem.on('keyup', function(e){
			  if (e.keyCode === 27 ) {
				elem.hide();
			  };
			});
			
		};

		var focus_to = function(action,element) {

			$(action).keyup(function (e) {
			    e.preventDefault();
				var code = e.keyCode || e.which;
				if(code == 13) { 
					$(element).focus();
				}
			});		
			
		};
         
    // Dom Ready
    $(function() {
		
        back_to_top_scroll();
       
		if( $(".rd-navbar").length){
			$('.rd-navbar').RDNavbar({
				stickUpClone: false,
                stickUpOffset: 220
				
			});
			
		}
		if( $('.woocommerce-ordering .orderby').length ){
			$('.woocommerce-ordering .orderby').customSelect();
		}
		if( $('.owlGallery,.gallery-media ul.wp-block-gallery').length ){
			$(".owlGallery,.gallery-media ul.wp-block-gallery").owlCarousel({
				
				stagePadding: 0,
				loop: true,
				autoplay: true,
				autoplayTimeout: 2000,
				margin: 10,
				nav: false,
				dots: false,
				smartSpeed: 2000,
				responsive: {
					0: {
						items: 1
					},
					600: {
						items: 1
					},
					1000: {
						items: 1
					}
				}
			});
		}
       
		   /* -- image-popup */
				if( $('.image-popup').length ){
					 $('.image-popup').magnificPopup({
						closeBtnInside : true,
						type           : 'image',
						mainClass      : 'mfp-with-zoom'
					});
				}
				/*------------------ Menu Focus for desktop -----------*/
				$(".rd-navbar-static .rd-navbar-nav li > a").keyup(function (e) {
				    e.preventDefault();
					$('.rd-navbar-static .rd-navbar-nav').addClass('focus-active');
				});

				$('.rd-navbar-static .rd-navbar-nav li > a').on('mouseenter mouseleave', function(event) {
					//$('.rd-navbar-static .rd-navbar-nav').removeClass('focus-active');
				});

				if( $('.rd-navbar-static .rd-navbar-nav li > a').length ){
					$( ".rd-navbar-static .rd-navbar-nav li > a" ).keyup(function() {
						
						$( ".rd-navbar-static .rd-navbar-nav li").removeClass('focus');
						
						$(this).parents('li').addClass('focus');
						
					});
				}
				/*------------------ Menu Focus for desktop -----------*/

				
				if( $('.rd-navbar-fixed .rd-navbar-nav li > a').length ){
				$( ".rd-navbar-fixed .rd-navbar-nav li > a" ).keyup(function() {
					
					$(this).parent('li').prev('li').removeClass('opened');	
					
					if( $(this).parents('li.rd-navbar-submenu').length ){
					
						$(this).parent('li').addClass('opened');
					}
					
				});
				}
				
				$( ".rd-navbar-toggle.toggle-original" ).keyup(function() {
					$(this).addClass('active');
					$('.rd-navbar-nav-wrap.toggle-original-elements').addClass('active');
				});
				
				$('#static_header_banner,#content').on('keydown', function(event) {
				
					$('.rd-navbar-static .rd-navbar-nav li.menu-item-has-children').removeClass('opened').removeClass('focus');
					$('.rd-navbar-toggle.toggle-original').removeClass('active');
					$('.rd-navbar-nav-wrap.toggle-original-elements').removeClass('active');
				
				});

			$( 'body' ).on( 'click', '.store-commerce-header .btn-mega,.shop-store-navbar .btn-mega', function(e) {
				 e.preventDefault();
				if (jQuery(window).width() < 700) {
					$(this).parent('#mega-menu').find('ul.menu').toggleClass('responsve_cat');
				}  
			});

				
    });

})(jQuery);
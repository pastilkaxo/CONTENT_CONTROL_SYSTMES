/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */
jQuery(document).ready(function($){
    $.amazStore = {
        init: function () {
            this.focusForCustomShortcut();
        },
        focusForCustomShortcut: function (){
            var fakeShortcutClasses = [
                'amaz_store_top_slider_section',
                'amaz_store_category_tab_section',
                'amaz_store_product_slide_section',
                'amaz_store_cat_slide_section',
                'amaz_store_product_slide_list',
                'amaz_store_product_cat_list',
                'amaz_store_brand',
                'amaz_store_ribbon',
                'amaz_store_banner',
                'amaz_store_highlight',
                'amaz_store_product_big_feature',
                'amaz_store_1_custom_sec',
                'amaz_store_2_custom_sec',
                'amaz_store_3_custom_sec',
                'amaz_store_4_custom_sec',
            ];
            fakeShortcutClasses.forEach(function (element){
                $('.customize-partial-edit-shortcut-'+ element).on('click',function (){
                   wp.customize.preview.send( 'amaz-store-customize-focus-section', element );
                });
            });
        }
    };
    $.amazStore.init();
    // color
    $.amazStoreColor = {
        init: function () {
            this.focusForCustomShortcutColor();
        },
        focusForCustomShortcutColor: function (){
            var fakeShortcutClasses = [
                'amaz-store-top-slider-color',
                'amaz-store-cat-slider-color',
                'amaz-store-ribbon-color',
            ];
            fakeShortcutClasses.forEach(function (element){
                $('.customize-partial-edit-shortcut-'+ element).on('click',function (){
                   wp.customize.preview.send( 'amaz-store-customize-focus-color-section', element );
                });
            });
        }
    };
    $.amazStoreColor.init();
});
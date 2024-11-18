<section class="u-align-center u-clearfix u-container-align-center u-section-1" id="sec-334f">
  <div class="u-clearfix u-sheet u-valign-middle u-sheet-1"><!--products--><!--products_options_json--><!--{"type":"Recent","source":"","tags":"","count":""}--><!--/products_options_json-->
    <?php $showSecondImage = true; ?><div class="u-expanded-width u-products u-products-1" data-site-sorting-prop="created" data-site-sorting-order="desc" data-items-per-page="6" data-products-datasource="site">
      <div class="has-categories-listbox u-list-control"><!--products_categories_filter-->
        <div class="u-categories-listbox"><!--products_categories_filter_select-->
          <select class="u-border-2 u-border-grey-30 u-input u-select-categories">
            <?php
        $optionTemplate = '<option value="[[value]]">[[content]]</option>';
        echo getCategoriesFilterOptions($categories, $optionTemplate);
     ?>
    
            
            
            
            
            
            
          </select><!--/products_categories_filter_select-->
          <svg class="u-caret u-caret-svg" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="16px" height="16px" viewBox="0 0 16 16" style="fill:currentColor;" xml:space="preserve"><polygon class="st0" points="8,12 2,4 14,4 "></polygon></svg>
        </div><!--/products_categories_filter-->
      </div>
      <div class="u-repeater u-repeater-1">
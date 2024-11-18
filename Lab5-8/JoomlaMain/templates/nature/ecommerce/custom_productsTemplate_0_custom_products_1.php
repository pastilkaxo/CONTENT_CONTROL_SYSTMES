<?php
                                    $countItems = 6;
                                    $templateOrder  = $itemIndex % $countItems;
                                ?><?php if ($templateOrder == 0) : ?><!--product_item-->
        <div class="u-align-center u-container-align-center u-container-style u-products-item u-repeater-item u-white u-repeater-item-1" data-product-id="4">
          <div class="u-container-layout u-similar-container u-valign-top u-container-layout-1"><!--product_image-->
            <?php if ($image0) : ?><img alt="" class="u-expanded-width u-image u-image-contain u-image-default u-product-control u-image-1" src="<?php echo $image0; ?>"><?php else: ?><div class="none-post-image" style="display: none;"></div><?php endif; ?>
                <?php if ($showSecondImage && count($galleryImages) > 1) : ?>
                    <img src="<?php echo $galleryImages[1]; ?>" class="u-product-second-image">
                <?php endif; 
                ?><!--/product_image--><!--product_title-->
            <?php if ($title0): ?><h4 class="u-align-center u-product-control u-text u-text-default u-text-1">
              <?php if ($titleLink0): ?><a class="u-product-title-link" href="<?php echo $titleLink0; ?>"><?php endif; ?><?php echo $title0; ?><?php if ($titleLink0): ?></a><?php endif; ?>
            </h4><?php endif; ?><!--/product_title--><!--product_price-->
            <?php $addZeroCents = true ?><div class="u-align-center u-product-control u-product-price u-product-price-1" data-add-zero-cents="true">
              <div class="u-price-wrapper u-spacing-10"><!--product_old_price-->
                <?php if ($productOldPrice0['price'] && !$productOldPrice0['callForPrice']): ?><div class="u-old-price" style="text-decoration: line-through !important;"><?php echo $addZeroCents ? $productOldPrice0['priceWithZeroCents'] : $productOldPrice0['price']; ?></div><?php endif; ?><!--/product_old_price--><!--product_regular_price-->
                
        <?php 
            if ($productRegularPrice0['price'] && !$productRegularPrice0['callForPrice']): ?>
                <div class="u-price u-text-palette-2-base" style="font-size: 1.25rem; font-weight: 700;"><?php echo $addZeroCents ? $productRegularPrice0['priceWithZeroCents'] : $productRegularPrice0['price']; ?></div>
            <?php endif; 
            if ($productRegularPrice0['callForPrice']): ?>
                <div class="u-price u-text-palette-2-base" style="font-size: 1.25rem; font-weight: 700;">
                <?php echo $productRegularPrice0['callForPrice']; ?>                
                </div>
            <?php endif;
        ?><!--/product_regular_price-->
              </div>
            </div><!--/product_price--><?php
$clickTypeProductbutton = 'add-to-cart';
$contentProductbutton = 'Добавить в корзину';
?>

            <a data-product-id="<?php echo $productId0; ?>" data-product="<?php echo $productJson0; ?>" href="#" class="u-align-center u-border-2 u-border-grey-25 u-btn u-btn-rectangle u-button-style u-none u-product-control u-text-body-color u-btn-1 u-add-to-cart-link" data-product-button-click-type="add-to-cart" data-product-id=""><!--product_button_content-->Добавить в корзину<!--/product_button_content--></a>
          </div>
        </div><!--/product_item--><?php endif; ?><?php if ($templateOrder == 1) : ?><!--product_item-->
        <div class="u-align-center u-container-align-center u-container-style u-products-item u-repeater-item u-white u-repeater-item-2" data-product-id="5">
          <div class="u-container-layout u-similar-container u-valign-top u-container-layout-2"><!--product_image-->
            <?php if ($image0) : ?><img alt="" class="u-expanded-width u-image u-image-contain u-image-default u-product-control u-image-2" src="<?php echo $image0; ?>"><?php else: ?><div class="none-post-image" style="display: none;"></div><?php endif; ?>
                <?php if ($showSecondImage && count($galleryImages) > 1) : ?>
                    <img src="<?php echo $galleryImages[1]; ?>" class="u-product-second-image">
                <?php endif; 
                ?><!--/product_image--><!--product_title-->
            <?php if ($title0): ?><h4 class="u-align-center u-product-control u-text u-text-default u-text-2">
              <?php if ($titleLink0): ?><a class="u-product-title-link" href="<?php echo $titleLink0; ?>"><?php endif; ?><?php echo $title0; ?><?php if ($titleLink0): ?></a><?php endif; ?>
            </h4><?php endif; ?><!--/product_title--><!--product_price-->
            <?php $addZeroCents = true ?><div class="u-align-center u-product-control u-product-price u-product-price-2" data-add-zero-cents="true">
              <div class="u-price-wrapper u-spacing-10"><!--product_old_price-->
                <?php if ($productOldPrice0['price'] && !$productOldPrice0['callForPrice']): ?><div class="u-old-price" style="text-decoration: line-through !important;"><?php echo $addZeroCents ? $productOldPrice0['priceWithZeroCents'] : $productOldPrice0['price']; ?></div><?php endif; ?><!--/product_old_price--><!--product_regular_price-->
                
        <?php 
            if ($productRegularPrice0['price'] && !$productRegularPrice0['callForPrice']): ?>
                <div class="u-price u-text-palette-2-base" style="font-size: 1.25rem; font-weight: 700;"><?php echo $addZeroCents ? $productRegularPrice0['priceWithZeroCents'] : $productRegularPrice0['price']; ?></div>
            <?php endif; 
            if ($productRegularPrice0['callForPrice']): ?>
                <div class="u-price u-text-palette-2-base" style="font-size: 1.25rem; font-weight: 700;">
                <?php echo $productRegularPrice0['callForPrice']; ?>                
                </div>
            <?php endif;
        ?><!--/product_regular_price-->
              </div>
            </div><!--/product_price--><?php
$clickTypeProductbutton = 'add-to-cart';
$contentProductbutton = 'Добавить в корзину';
?>

            <a data-product-id="<?php echo $productId0; ?>" data-product="<?php echo $productJson0; ?>" href="#" class="u-align-center u-border-2 u-border-grey-25 u-btn u-btn-rectangle u-button-style u-none u-product-control u-text-body-color u-btn-2 u-add-to-cart-link" data-product-button-click-type="add-to-cart" data-product-id=""><!--product_button_content-->Добавить в корзину<!--/product_button_content--></a>
          </div>
        </div><!--/product_item--><?php endif; ?><?php if ($templateOrder == 2) : ?><!--product_item-->
        <div class="u-align-center u-container-align-center u-container-style u-products-item u-repeater-item u-white u-repeater-item-3" data-product-id="6">
          <div class="u-container-layout u-similar-container u-valign-top u-container-layout-3"><!--product_image-->
            <?php if ($image0) : ?><img alt="" class="u-expanded-width u-image u-image-contain u-image-default u-product-control u-image-3" src="<?php echo $image0; ?>"><?php else: ?><div class="none-post-image" style="display: none;"></div><?php endif; ?>
                <?php if ($showSecondImage && count($galleryImages) > 1) : ?>
                    <img src="<?php echo $galleryImages[1]; ?>" class="u-product-second-image">
                <?php endif; 
                ?><!--/product_image--><!--product_title-->
            <?php if ($title0): ?><h4 class="u-align-center u-product-control u-text u-text-default u-text-3">
              <?php if ($titleLink0): ?><a class="u-product-title-link" href="<?php echo $titleLink0; ?>"><?php endif; ?><?php echo $title0; ?><?php if ($titleLink0): ?></a><?php endif; ?>
            </h4><?php endif; ?><!--/product_title--><!--product_price-->
            <?php $addZeroCents = true ?><div class="u-align-center u-product-control u-product-price u-product-price-3" data-add-zero-cents="true">
              <div class="u-price-wrapper u-spacing-10"><!--product_old_price-->
                <?php if ($productOldPrice0['price'] && !$productOldPrice0['callForPrice']): ?><div class="u-old-price" style="text-decoration: line-through !important;"><?php echo $addZeroCents ? $productOldPrice0['priceWithZeroCents'] : $productOldPrice0['price']; ?></div><?php endif; ?><!--/product_old_price--><!--product_regular_price-->
                
        <?php 
            if ($productRegularPrice0['price'] && !$productRegularPrice0['callForPrice']): ?>
                <div class="u-price u-text-palette-2-base" style="font-size: 1.25rem; font-weight: 700;"><?php echo $addZeroCents ? $productRegularPrice0['priceWithZeroCents'] : $productRegularPrice0['price']; ?></div>
            <?php endif; 
            if ($productRegularPrice0['callForPrice']): ?>
                <div class="u-price u-text-palette-2-base" style="font-size: 1.25rem; font-weight: 700;">
                <?php echo $productRegularPrice0['callForPrice']; ?>                
                </div>
            <?php endif;
        ?><!--/product_regular_price-->
              </div>
            </div><!--/product_price--><?php
$clickTypeProductbutton = 'add-to-cart';
$contentProductbutton = 'Добавить в корзину';
?>

            <a data-product-id="<?php echo $productId0; ?>" data-product="<?php echo $productJson0; ?>" href="#" class="u-align-center u-border-2 u-border-grey-25 u-btn u-btn-rectangle u-button-style u-none u-product-control u-text-body-color u-btn-3 u-add-to-cart-link" data-product-button-click-type="add-to-cart" data-product-id=""><!--product_button_content-->Добавить в корзину<!--/product_button_content--></a>
          </div>
        </div><!--/product_item--><?php endif; ?><?php if ($templateOrder == 3) : ?><!--product_item-->
        <div class="u-align-center u-container-align-center u-container-style u-products-item u-repeater-item u-white u-repeater-item-4" data-product-id="6">
          <div class="u-container-layout u-similar-container u-valign-top u-container-layout-4"><!--product_image-->
            <?php if ($image0) : ?><img alt="" class="u-expanded-width u-image u-image-contain u-image-default u-product-control u-image-4" src="<?php echo $image0; ?>"><?php else: ?><div class="none-post-image" style="display: none;"></div><?php endif; ?>
                <?php if ($showSecondImage && count($galleryImages) > 1) : ?>
                    <img src="<?php echo $galleryImages[1]; ?>" class="u-product-second-image">
                <?php endif; 
                ?><!--/product_image--><!--product_title-->
            <?php if ($title0): ?><h4 class="u-align-center u-product-control u-text u-text-default u-text-4">
              <?php if ($titleLink0): ?><a class="u-product-title-link" href="<?php echo $titleLink0; ?>"><?php endif; ?><?php echo $title0; ?><?php if ($titleLink0): ?></a><?php endif; ?>
            </h4><?php endif; ?><!--/product_title--><!--product_price-->
            <?php $addZeroCents = true ?><div class="u-align-center u-product-control u-product-price u-product-price-4" data-add-zero-cents="true">
              <div class="u-price-wrapper u-spacing-10"><!--product_old_price-->
                <?php if ($productOldPrice0['price'] && !$productOldPrice0['callForPrice']): ?><div class="u-old-price" style="text-decoration: line-through !important;"><?php echo $addZeroCents ? $productOldPrice0['priceWithZeroCents'] : $productOldPrice0['price']; ?></div><?php endif; ?><!--/product_old_price--><!--product_regular_price-->
                
        <?php 
            if ($productRegularPrice0['price'] && !$productRegularPrice0['callForPrice']): ?>
                <div class="u-price u-text-palette-2-base" style="font-size: 1.25rem; font-weight: 700;"><?php echo $addZeroCents ? $productRegularPrice0['priceWithZeroCents'] : $productRegularPrice0['price']; ?></div>
            <?php endif; 
            if ($productRegularPrice0['callForPrice']): ?>
                <div class="u-price u-text-palette-2-base" style="font-size: 1.25rem; font-weight: 700;">
                <?php echo $productRegularPrice0['callForPrice']; ?>                
                </div>
            <?php endif;
        ?><!--/product_regular_price-->
              </div>
            </div><!--/product_price--><?php
$clickTypeProductbutton = 'add-to-cart';
$contentProductbutton = 'Добавить в корзину';
?>

            <a data-product-id="<?php echo $productId0; ?>" data-product="<?php echo $productJson0; ?>" href="#" class="u-align-center u-border-2 u-border-grey-25 u-btn u-btn-rectangle u-button-style u-none u-product-control u-text-body-color u-btn-4 u-add-to-cart-link" data-product-button-click-type="add-to-cart" data-product-id=""><!--product_button_content-->Добавить в корзину<!--/product_button_content--></a>
          </div>
        </div><!--/product_item--><?php endif; ?><?php if ($templateOrder == 4) : ?><!--product_item-->
        <div class="u-align-center u-container-align-center u-container-style u-products-item u-repeater-item u-white u-repeater-item-5" data-product-id="6">
          <div class="u-container-layout u-similar-container u-valign-top u-container-layout-5"><!--product_image-->
            <?php if ($image0) : ?><img alt="" class="u-expanded-width u-image u-image-contain u-image-default u-product-control u-image-5" src="<?php echo $image0; ?>"><?php else: ?><div class="none-post-image" style="display: none;"></div><?php endif; ?>
                <?php if ($showSecondImage && count($galleryImages) > 1) : ?>
                    <img src="<?php echo $galleryImages[1]; ?>" class="u-product-second-image">
                <?php endif; 
                ?><!--/product_image--><!--product_title-->
            <?php if ($title0): ?><h4 class="u-align-center u-product-control u-text u-text-default u-text-5">
              <?php if ($titleLink0): ?><a class="u-product-title-link" href="<?php echo $titleLink0; ?>"><?php endif; ?><?php echo $title0; ?><?php if ($titleLink0): ?></a><?php endif; ?>
            </h4><?php endif; ?><!--/product_title--><!--product_price-->
            <?php $addZeroCents = true ?><div class="u-align-center u-product-control u-product-price u-product-price-5" data-add-zero-cents="true">
              <div class="u-price-wrapper u-spacing-10"><!--product_old_price-->
                <?php if ($productOldPrice0['price'] && !$productOldPrice0['callForPrice']): ?><div class="u-old-price" style="text-decoration: line-through !important;"><?php echo $addZeroCents ? $productOldPrice0['priceWithZeroCents'] : $productOldPrice0['price']; ?></div><?php endif; ?><!--/product_old_price--><!--product_regular_price-->
                
        <?php 
            if ($productRegularPrice0['price'] && !$productRegularPrice0['callForPrice']): ?>
                <div class="u-price u-text-palette-2-base" style="font-size: 1.25rem; font-weight: 700;"><?php echo $addZeroCents ? $productRegularPrice0['priceWithZeroCents'] : $productRegularPrice0['price']; ?></div>
            <?php endif; 
            if ($productRegularPrice0['callForPrice']): ?>
                <div class="u-price u-text-palette-2-base" style="font-size: 1.25rem; font-weight: 700;">
                <?php echo $productRegularPrice0['callForPrice']; ?>                
                </div>
            <?php endif;
        ?><!--/product_regular_price-->
              </div>
            </div><!--/product_price--><?php
$clickTypeProductbutton = 'add-to-cart';
$contentProductbutton = 'Добавить в корзину';
?>

            <a data-product-id="<?php echo $productId0; ?>" data-product="<?php echo $productJson0; ?>" href="#" class="u-align-center u-border-2 u-border-grey-25 u-btn u-btn-rectangle u-button-style u-none u-product-control u-text-body-color u-btn-5 u-add-to-cart-link" data-product-button-click-type="add-to-cart" data-product-id=""><!--product_button_content-->Добавить в корзину<!--/product_button_content--></a>
          </div>
        </div><!--/product_item--><?php endif; ?><?php if ($templateOrder == 5) : ?><!--product_item-->
        <div class="u-align-center u-container-align-center u-container-style u-products-item u-repeater-item u-white u-repeater-item-6" data-product-id="6">
          <div class="u-container-layout u-similar-container u-valign-top u-container-layout-6"><!--product_image-->
            <?php if ($image0) : ?><img alt="" class="u-expanded-width u-image u-image-contain u-image-default u-product-control u-image-6" src="<?php echo $image0; ?>"><?php else: ?><div class="none-post-image" style="display: none;"></div><?php endif; ?>
                <?php if ($showSecondImage && count($galleryImages) > 1) : ?>
                    <img src="<?php echo $galleryImages[1]; ?>" class="u-product-second-image">
                <?php endif; 
                ?><!--/product_image--><!--product_title-->
            <?php if ($title0): ?><h4 class="u-align-center u-product-control u-text u-text-default u-text-6">
              <?php if ($titleLink0): ?><a class="u-product-title-link" href="<?php echo $titleLink0; ?>"><?php endif; ?><?php echo $title0; ?><?php if ($titleLink0): ?></a><?php endif; ?>
            </h4><?php endif; ?><!--/product_title--><!--product_price-->
            <?php $addZeroCents = true ?><div class="u-align-center u-product-control u-product-price u-product-price-6" data-add-zero-cents="true">
              <div class="u-price-wrapper u-spacing-10"><!--product_old_price-->
                <?php if ($productOldPrice0['price'] && !$productOldPrice0['callForPrice']): ?><div class="u-old-price" style="text-decoration: line-through !important;"><?php echo $addZeroCents ? $productOldPrice0['priceWithZeroCents'] : $productOldPrice0['price']; ?></div><?php endif; ?><!--/product_old_price--><!--product_regular_price-->
                
        <?php 
            if ($productRegularPrice0['price'] && !$productRegularPrice0['callForPrice']): ?>
                <div class="u-price u-text-palette-2-base" style="font-size: 1.25rem; font-weight: 700;"><?php echo $addZeroCents ? $productRegularPrice0['priceWithZeroCents'] : $productRegularPrice0['price']; ?></div>
            <?php endif; 
            if ($productRegularPrice0['callForPrice']): ?>
                <div class="u-price u-text-palette-2-base" style="font-size: 1.25rem; font-weight: 700;">
                <?php echo $productRegularPrice0['callForPrice']; ?>                
                </div>
            <?php endif;
        ?><!--/product_regular_price-->
              </div>
            </div><!--/product_price--><?php
$clickTypeProductbutton = 'add-to-cart';
$contentProductbutton = 'Добавить в корзину';
?>

            <a data-product-id="<?php echo $productId0; ?>" data-product="<?php echo $productJson0; ?>" href="#" class="u-align-center u-border-2 u-border-grey-25 u-btn u-btn-rectangle u-button-style u-none u-product-control u-text-body-color u-btn-6 u-add-to-cart-link" data-product-button-click-type="add-to-cart" data-product-id=""><!--product_button_content-->Добавить в корзину<!--/product_button_content--></a>
          </div>
        </div><!--/product_item--><?php endif; ?>
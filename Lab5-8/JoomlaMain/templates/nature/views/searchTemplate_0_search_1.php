<?php
                                        $countItems = 3;
                                        $templateOrder  = $itemIndex % $countItems;
                                        ?><?php if ($templateOrder == 0) : ?><!--blog_post-->
        <div class="u-align-left u-blog-post u-container-align-left u-container-style u-repeater-item u-search-result">
          <div class="u-container-layout u-similar-container u-container-layout-1"><!--blog_post_header-->
            <?php if ($title0): ?><h4 class="u-blog-control u-text u-text-palette-1-base u-text-2">
              <?php if ($titleLink0): ?><a class="u-post-header-link" href="<?php echo $titleLink0; ?>"><?php endif; ?><?php echo $title0; ?><?php if ($titleLink0): ?></a><?php endif; ?>
            </h4><?php endif; ?><!--/blog_post_header--><!--blog_post_content-->
            <div class="u-blog-control u-post-content u-text u-text-3"><?php echo $content0; ?></div><!--/blog_post_content-->
          </div>
        </div><!--/blog_post--><?php endif; ?><?php if ($templateOrder == 1) : ?><!--blog_post-->
        <div class="u-align-left u-blog-post u-container-align-left u-container-style u-repeater-item u-search-result">
          <div class="u-container-layout u-similar-container u-container-layout-2"><!--blog_post_header-->
            <?php if ($title0): ?><h4 class="u-blog-control u-text u-text-palette-1-base u-text-4">
              <?php if ($titleLink0): ?><a class="u-post-header-link" href="<?php echo $titleLink0; ?>"><?php endif; ?><?php echo $title0; ?><?php if ($titleLink0): ?></a><?php endif; ?>
            </h4><?php endif; ?><!--/blog_post_header--><!--blog_post_content-->
            <div class="u-blog-control u-post-content u-text u-text-5"><?php echo $content0; ?></div><!--/blog_post_content-->
          </div>
        </div><!--/blog_post--><?php endif; ?><?php if ($templateOrder == 2) : ?><!--blog_post-->
        <div class="u-align-left u-blog-post u-container-align-left u-container-style u-repeater-item u-search-result">
          <div class="u-container-layout u-similar-container u-container-layout-3"><!--blog_post_header-->
            <?php if ($title0): ?><h4 class="u-blog-control u-text u-text-palette-1-base u-text-6">
              <?php if ($titleLink0): ?><a class="u-post-header-link" href="<?php echo $titleLink0; ?>"><?php endif; ?><?php echo $title0; ?><?php if ($titleLink0): ?></a><?php endif; ?>
            </h4><?php endif; ?><!--/blog_post_header--><!--blog_post_content-->
            <div class="u-blog-control u-post-content u-text u-text-7"><?php echo $content0; ?></div><!--/blog_post_content-->
          </div>
        </div><!--/blog_post--><?php endif; ?>
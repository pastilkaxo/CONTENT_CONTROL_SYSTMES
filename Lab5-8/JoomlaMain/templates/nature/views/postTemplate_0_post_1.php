<section class="u-align-center u-clearfix u-section-1" id="sec-4ea9">
  <div class="u-clearfix u-sheet u-valign-middle-md u-valign-middle-sm u-valign-middle-xs u-sheet-1"><!--post_details--><!--post_details_options_json--><!--{"source":""}--><!--/post_details_options_json--><!--blog_post-->
    <div class="u-container-style u-expanded-width u-post-details u-post-details-1">
      <div class="u-container-layout u-valign-middle u-container-layout-1"><!--blog_post_image-->
        <?php if ($image0) : ?><img alt="<?php echo $altImage0; ?>" class="u-blog-control u-expanded-width u-image u-image-default u-image-1" src="<?php echo $image0; ?>"><?php else: ?><div class="none-post-image" style="display: none;"></div><?php endif; ?><!--/blog_post_image--><!--blog_post_header-->
        <?php if ($title0): ?><h2 class="u-blog-control u-text u-text-1">
          <?php if ($titleLink0): ?><a class="u-post-header-link" href="<?php echo $titleLink0; ?>"><?php endif; ?><?php echo $title0; ?><?php if ($titleLink0): ?></a><?php endif; ?>
        </h2><?php endif; ?><!--/blog_post_header--><!--blog_post_metadata-->
        <?php if (count($metadata0) > 0): ?>
<div class="u-blog-control u-metadata u-metadata-1"><!--blog_post_metadata_date-->
          <?php if (isset($metadata0['date'])): ?><span class="u-meta-date u-meta-icon"><?php echo $metadata0['date']; ?></span><?php endif; ?><!--/blog_post_metadata_date--><!--blog_post_metadata_category-->
          <?php if (isset($metadata0['category'])): ?><span class="u-meta-category u-meta-icon"><?php echo $metadata0['category']; ?></span><?php endif; ?><!--/blog_post_metadata_category--><!--blog_post_metadata_comments-->
          <!--/blog_post_metadata_comments-->
        </div>
<?php endif; ?><!--/blog_post_metadata--><!--blog_post_content-->
        <div class="u-align-justify u-blog-control u-post-content u-text u-text-2"><?php echo $content0; ?></div><!--/blog_post_content-->
      </div>
    </div><!--/blog_post--><!--/post_details-->
  </div>
</section>

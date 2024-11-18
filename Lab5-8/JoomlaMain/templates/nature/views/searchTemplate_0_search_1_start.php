<section class="u-clearfix u-section-1" id="sec-da20">
  <div class="u-clearfix u-sheet u-sheet-1">
    <h2 class="u-align-center u-text u-text-1"> Search Results</h2>
    <form action="<?php echo $searchFormData['action']; ?>" method="get" class="u-border-1 u-border-grey-30 u-search u-search-left u-white u-search-1">
      <button class="u-search-button" type="submit">
        <span class="u-search-icon u-spacing-10">
          <svg class="u-svg-link" preserveAspectRatio="xMidYMin slice" viewBox="0 0 56.966 56.966"><use xlink:href="#svg-6a57"></use></svg>
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="svg-6a57" x="0px" y="0px" viewBox="0 0 56.966 56.966" style="enable-background:new 0 0 56.966 56.966;" xml:space="preserve" class="u-svg-content"><path d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23  s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92  c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17  s-17-7.626-17-17S14.61,6,23.984,6z"></path></svg>
        </span>
      </button>
      <input class="u-search-input" type="search" name="q" value="<?php echo $searchFormData['input']; ?>" placeholder="Search"><input type="hidden" name="view" value="search">
<input type="hidden" name="option" value="com_finder">

    </form><?php echo $searchFormData['totalText']; ?><?php
$blogJson = '{"type":"Recent","source":"","tags":"","count":"3"}';
?>

    <div class="u-blog u-expanded-width u-layout-grid u-search-results u-blog-1" data-items-per-page="1" data-max-items="3">
      <div class="u-list-control"></div>
      <div class="u-repeater u-repeater-1">
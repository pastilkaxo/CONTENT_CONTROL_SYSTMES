<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      type="img/x-icon"
      href="/img/60instagramhighlighticons09_112040.ico"
      rel="icon"
    />

    <title><?php bloginfo('name');?></title>
	<?php wp_head();?>
  </head>
  <body style=" background-image: url('<?php echo get_template_directory_uri();?>./assets/img/background.png')">
    <header>
      <div class="container">
        <section class="head">
          <svg
            class="cart"
            id="cart-button"
            width="40px"
            height="40px"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M2 3H4.5L6.5 17H17C18.1046 17 19 17.8954 19 19C19 20.1046 18.1046 21 17 21C15.8954 21 15 20.1046 15 19M9 5H21.0001L19.0001 11M18 14H6.07141M11 19C11 20.1046 10.1046 21 9 21C7.89543 21 7 20.1046 7 19C7 17.8954 7.89543 17 9 17C10.1046 17 11 17.8954 11 19Z"
              stroke="#000000"
              stroke-width="1.5"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>

          <img class="logo" src="<?php echo get_template_directory_uri()?>./assets/img/Logo.svg" />
        </section>

        <section class="body">
          <h1><?php bloginfo('description')?></h1>
        </section>

        <nav>
    <?php wp_nav_menu(array(
		'echo' => true,
		'items_wrap'      => '<ul class="%2$s" style="display:flex; justify-content:center;list-style-type:none;gap:50px; align-items:center;margin:0; flex-wrap:wrap;">%3$s</ul>',
		'container' => false,
        'theme_location' => 'menu-1',
        'menu_class'     => 'primary-menu',
    )); ?>

        </nav>
      </div>
    </header>
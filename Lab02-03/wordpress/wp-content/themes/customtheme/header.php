<?php
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Vladislav Lemiasheuski</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <!-- <link href="<?php echo get_template_directory_uri();?>/css/styles.css" rel="stylesheet" /> -->
<?php
wp_head();
?>
    </head>
    <body>
        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="#!">Добробыт</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
					<?php wp_nav_menu(
						[				
	'container' => false,
	'container_class' => '',
	'menu_class'      => 'menu d-flex navbar-brand',
	'menu_id'         => '',
	'echo'            => true,
	'items_wrap'      => '<ul class="%2$s" style="list-style-type:none;gap:50px; font-size:20px; align-items:center;">%3$s</ul>',
	'theme_location' => 'menu-1',
	'depth'           => 0,
]
					);?>
                </div>
            </div>
        </nav>
        <!-- Header - set the background image for the header in the line below-->
        <header class="py-5 bg-image-full" style="background-image: url('https://source.unsplash.com/wfh8dDlNFOk/1600x900')">
            <div class="text-center my-5">
                <img class="img-fluid rounded-circle mb-4" src="<?php echo get_template_directory_uri();?>/./assets/img/portfolio/cabin.png" alt="..." />
                <h1 class="text-black fs-3 fw-bolder"><?php bloginfo('name'); ?></h1>
                <p class="text-black-50 mb-0"><?php bloginfo('description');?></p>
            </div>
        </header>
        <!-- Content section-->
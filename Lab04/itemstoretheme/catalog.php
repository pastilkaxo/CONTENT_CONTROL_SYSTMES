<?php 
/*
Template Name: Catalog Custom
*/
get_header();?>

    <div class="container">
      <main>
         
        <section class="search">


            <span class="categories">
               <p>Samsung</p>
               <p>Xiaomi</p>
               <p>Atlant</p>
            </span>
          

            <div class="find-input">
                <form class="search-container">
                    <input type="text" id="search-bar" placeholder="Что вы ищите?">
                    <a href="#"><img class="search-icon" src="http://www.endlessicons.com/wp-content/uploads/2012/12/search-icon.png"></a>
                  </form>
                </body>
            </div>
 

 
        </section>

        <section class="cards">
            <div class="card" style="display:none;">
            <img class="item-img" src="<?php echo get_template_directory_uri(); ?>/assets/img/стиралка.jpg"/>
            <p class="item-name">Стиральная машина с сушкой Gorenje</p>
             <div class="item-get">
              <p class="price">
                150$   </p>
              <img class="button" src="<?php echo get_template_directory_uri(); ?>/assets/img/Button.svg" />
             </div>
          </div>
          <div class="card">
            <img class="item-img" src="<?php echo get_template_directory_uri(); ?>/assets/img/стиралка.jpg"/>
            <p class="item-name">Стиральная машина с сушкой Gorenje</p>
             <div class="item-get">
              <p class="price">
                150$   </p>
              <img class="button" src="<?php echo get_template_directory_uri(); ?>/assets/img/Button.svg" />
             </div>
          </div>
          <div class="card">
            <img class="item-img" src="<?php echo get_template_directory_uri(); ?>/assets/img/кран.jpeg"/>
            <p class="item-name">Смеситель для раковины AM.PM</p>
            <div class="item-get">
              <p class="price">
                2$   </p>
              <img class="button" src="<?php echo get_template_directory_uri(); ?>/assets/img/Button.svg" />
             </div>
          </div>
          <div class="card">
            <img class="item-img" src="<?php echo get_template_directory_uri(); ?>/assets/img/чайник.jpg"/>
            <p class="item-name">Чайник Tefal</p>
            <div class="item-get">
              <p class="price">
                9$   </p>
              <img class="button" src="<?php echo get_template_directory_uri(); ?>/assets/img/Button.svg" />
             </div>
          </div>
          <div class="card">
            <img class="item-img" src="<?php echo get_template_directory_uri(); ?>/assets/img/аэрогриль.jpg"/>
            <p class="item-name">Аэрогриль Endever Skyline</p>
            <div class="item-get">
              <p class="price">
                50$   </p>
              <img class="button" src="<?php echo get_template_directory_uri(); ?>/assets/img/Button.svg" />
             </div>
          </div>
          
        </section>
        <section class="cards">
            <div class="card">
              <img class="item-img" src="<?php echo get_template_directory_uri(); ?>/assets/img/телек.jpg"/>
              <p class="item-name">Телевизор Samsung</p>
               <div class="item-get">
                <p class="price">
                  250$   </p>
                <img class="button" src="<?php echo get_template_directory_uri(); ?>/assets/img/Button.svg" />
               </div>
            </div>
            <div class="card">
              <img class="item-img" src="<?php echo get_template_directory_uri(); ?>/assets/img/робот.jpg"/>
              <p class="item-name">Робот-пылесос SAMSUNG</p>
              <div class="item-get">
                <p class="price">
                  150$   </p>
                <img class="button" src="<?php echo get_template_directory_uri(); ?>/assets/img/Button.svg" />
               </div>
            </div>
            <div class="card">
              <img class="item-img" src="<?php echo get_template_directory_uri(); ?>/assets/img/холодильник2.jpg"/>
              <p class="item-name">Холодильник Samsung</p>
              <div class="item-get">
                <p class="price">
                  170$   </p>
                <img class="button" src="<?php echo get_template_directory_uri(); ?>/assets/img/Button.svg" />
               </div>
            </div>
            <div class="card">
              <img class="item-img" src="<?php echo get_template_directory_uri(); ?>/assets/img/холодильник.jpg"/>
              <p class="item-name">Холодильник ATLANT</p>
              <div class="item-get">
                <p class="price">
                  160$   </p>
                <img class="button" src="<?php echo get_template_directory_uri(); ?>/assets/img/Button.svg" />
               </div>
            </div>
            
          </section>
           <section class="cards">
            <div class="card">
              <img class="item-img" src="<?php echo get_template_directory_uri(); ?>/assets/img/parogenerator.png"/>
              <p class="item-name">Парогенератор Electrolux</p>
              <div class="item-get">
                <p class="price">
                 19$ </p>
                <img class="button" src="<?php echo get_template_directory_uri(); ?>/assets/img/Button.svg" />
               </div>
            </div>
           </section>
    
      </main>
    </div>
<?php get_footer();?>
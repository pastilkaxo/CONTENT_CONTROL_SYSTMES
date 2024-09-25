<?php get_header();?>
<?php the_post();?>
    <div class="container">

      <main>
        <section class="popular_slider">
          <img class="for_cell" src="<?php echo get_template_directory_uri()?>./assets/img/18_1678702144.jpg" />

          <div class="card">
            <section class="slider-card" style="display:none;z-index:10;">
              <img class="item-photo" src="<?php echo get_template_directory_uri()?>./assets/img/parogenerator.png" />
              <p class="item-name">Парогенератор</p>
              <div class="gets">
                <div class="cost">
                  <span>30$</span>
                  <p class="price">19$</p>
                </div>

                <img class="button" src="<?php echo get_template_directory_uri()?>./assets/img/Button.svg" />
              </div>
            </section>
            <section class="slider-card">
              <img class="item-photo" src="<?php echo get_template_directory_uri()?>./assets/img/parogenerator.png" />
              <p class="item-name">Парогенератор</p>
              <div class="gets">
                <div class="cost">
                  <span>30$</span>
                  <p class="price">19$</p>
                </div>

                <img class="button" src="<?php echo get_template_directory_uri()?>./assets/img/Button.svg" />
              </div>
            </section>
          </div>
        </section>

        <section class="cards">
              <div class="card" style="display:none;">
            <img class="item-img" src="<?php echo get_template_directory_uri(); ?>./assets/img/стиралка.jpg"/>
            <p class="item-name">Стиральная машина с сушкой Gorenje</p>
             <div class="item-get">
              <p class="price">
                150$   </p>
              <img class="button" src="<?php echo get_template_directory_uri(); ?>./assets/img/Button.svg" />
             </div>
          </div>
          <div class="card">
            <img class="item-img" src="<?php echo get_template_directory_uri()?>./assets/img/стиралка.jpg" />
			 <p class="item-name">Стиральная машина с сушкой Gorenje</p>
            <div class="item-get">
              <p class="price">150$</p>
              <img class="button" src="<?php echo get_template_directory_uri()?>./assets/img/Button.svg" />
            </div>
          </div>
          <div class="card">
            <img class="item-img" src="<?php echo get_template_directory_uri()?>./assets/img/кран.jpeg" />
            <p class="item-name">Смеситель для раковины AM.PM</p>
            <div class="item-get">
              <p class="price">2$</p>
              <img class="button" src="<?php echo get_template_directory_uri()?>./assets/img/Button.svg" />
            </div>
          </div>
          <div class="card">
            <img class="item-img" src="<?php echo get_template_directory_uri()?>./assets/img/чайник.jpg" />
            <p class="item-name">Чайник Tefal</p>
            <div class="item-get">
              <p class="price">9$</p>
              <img class="button" src="<?php echo get_template_directory_uri()?>./assets/img/Button.svg" />
            </div>
          </div>
          <div class="card">
            <img class="item-img" src="<?php echo get_template_directory_uri()?>./assets/img/аэрогриль.jpg" />
            <p class="item-name">Аэрогриль Endever Skyline</p>
            <div class="item-get">
              <p class="price">50$</p>
              <img class="button" src="<?php echo get_template_directory_uri()?>./assets/img/Button.svg" />
            </div>
          </div>
        </section>
      </main>
    </div>

    <section class="info">
      <div class="container">
        <section class="information">
          <section class="text">
            <h1>Не знаешь что выбрать ?</h1>

            <p>
              Оставляй заявку прямо сейчас и мы поможем тебе принять лучшее
              решение
            </p>

            <button id="call">Обратный зовнок</button>
          </section>

          <section class="cart_setter">
            <input id="get-name" type="text" placeholder="Название товара" />
            <input id="get-cost" type="text" placeholder="Стоимость" />
            <button id="get-card">Добавить в очередь</button>
          </section>
        </section>
      </div>
    </section>

<?php get_footer();?>

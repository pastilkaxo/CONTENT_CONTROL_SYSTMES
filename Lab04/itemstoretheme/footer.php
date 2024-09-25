    <footer>
      <section>
        <div>
          <h3 id="adHead">Адрес:</h3>
          <p id="address">г.Минск</p>

          <div class="contacts">
            <a href="https://www.instagram.com/vladik_vodopadik25/">
              <svg
                width="800px"
                height="800px"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M16.19 2H7.81C4.17 2 2 4.17 2 7.81V16.18C2 19.83 4.17 22 7.81 22H16.18C19.82 22 21.99 19.83 21.99 16.19V7.81C22 4.17 19.83 2 16.19 2ZM12 15.88C9.86 15.88 8.12 14.14 8.12 12C8.12 9.86 9.86 8.12 12 8.12C14.14 8.12 15.88 9.86 15.88 12C15.88 14.14 14.14 15.88 12 15.88ZM17.92 6.88C17.87 7 17.8 7.11 17.71 7.21C17.61 7.3 17.5 7.37 17.38 7.42C17.26 7.47 17.13 7.5 17 7.5C16.73 7.5 16.48 7.4 16.29 7.21C16.2 7.11 16.13 7 16.08 6.88C16.03 6.76 16 6.63 16 6.5C16 6.37 16.03 6.24 16.08 6.12C16.13 5.99 16.2 5.89 16.29 5.79C16.52 5.56 16.87 5.45 17.19 5.52C17.26 5.53 17.32 5.55 17.38 5.58C17.44 5.6 17.5 5.63 17.56 5.67C17.61 5.7 17.66 5.75 17.71 5.79C17.8 5.89 17.87 5.99 17.92 6.12C17.97 6.24 18 6.37 18 6.5C18 6.63 17.97 6.76 17.92 6.88Z"
                  fill="#000000"
                />
              </svg>
            </a>
            <a href="https://t.me/vladislav2025">
              <svg
                width="800px"
                height="800px"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M12 4C10.4178 4 8.87103 4.46919 7.55544 5.34824C6.23985 6.22729 5.21447 7.47672 4.60897 8.93853C4.00347 10.4003 3.84504 12.0089 4.15372 13.5607C4.4624 15.1126 5.22433 16.538 6.34315 17.6569C7.46197 18.7757 8.88743 19.5376 10.4393 19.8463C11.9911 20.155 13.5997 19.9965 15.0615 19.391C16.5233 18.7855 17.7727 17.7602 18.6518 16.4446C19.5308 15.129 20 13.5823 20 12C20 9.87827 19.1571 7.84344 17.6569 6.34315C16.1566 4.84285 14.1217 4 12 4ZM15.93 9.48L14.62 15.67C14.52 16.11 14.26 16.21 13.89 16.01L11.89 14.53L10.89 15.46C10.8429 15.5215 10.7824 15.5715 10.7131 15.6062C10.6438 15.6408 10.5675 15.6592 10.49 15.66L10.63 13.66L14.33 10.31C14.5 10.17 14.33 10.09 14.09 10.23L9.55 13.08L7.55 12.46C7.12 12.33 7.11 12.03 7.64 11.83L15.35 8.83C15.73 8.72 16.05 8.94 15.93 9.48Z"
                  fill="#000000"
                />
              </svg>
            </a>
          </div>
        </div>

        <div>
          <h3 id="phHead">Номер телефона:</h3>
          <p id="phone">+375 (29) 307-47-00</p>
        </div>

        <div>
          <h3 id="wHead">График работы:</h3>

          <p id="work-time">9.00-20.00</p>
          <p id="wkd">Без выходных</p>
        </div>
      </section>
    </footer>

    <div id="cart-modal" class="modal">
      <div id="cart" class="modal-content">
        <span class="close">&times;</span>
        <h2 id="namec">Корзина</h2>
        <span id="cart-list">Корзина пуста</span>
        <div class="cart-foot">
          <button id="clear-button">Очистить корзину</button>
          <button class="zakaz">Оформить</button>
        </div>
      </div>
    </div>

    <div id="myModal" class="modal">
      <div class="modal-content-form">
        <span class="modal-close">&times;</span>
        <h1>Оформить помощь</h1>
        <form class="mod-form">
          <legend>Ваше имя:</legend>
          <input type="text" />
          <legend>Номер телефона:</legend>
          <input type="text" />
          <legend>Почта:</legend>
          <input type="email" />
          <legend>Опишите проблему:</legend>
          <input type="text" class="desk-prob" />
        </form>
        <div class="mod-footer">
          <button class="help-btn">Запрос</button>
        </div>
      </div>
    </div>
	<?php wp_footer();?>
	<script src="<?php echo get_template_directory_uri(); ?>/js/itemInfo.js"></script>
  </body>
</html>

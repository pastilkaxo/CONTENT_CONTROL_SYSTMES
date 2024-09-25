<?php 
/*
Template Name: Description Custom
*/
get_header();?>
       <main>
        <div class="container">
          <h1 class="help-sum">Описание товара</h1>
           <section class="item-info">
                <div class="lead">
                  <h1 class="item-name">#</h1>
                   <div class="bot">
                    <img class="item-img" src="<?php echo get_template_directory_uri(); ?>/assets/img/заглушка.jpg">
                       <div class="text-info">
                        <h5>Цена: $$$</h5>
                        <p class="item-price"></p>
                        <h5>Основные характеристики: </h5>
                        <p id="charact">Максимальная мощность: 2000 Вт <br>
                          Рабочая поверхность: Resilium, Анодированная<br>
                          Емкость резервуара для воды: 1200 мл<br>
                          Давление пара: 6<br>
                          Паровой удар: Да</p>
                       </div>
                   </div>
                </div>
           </section>
             
        </div>

       </main>
   
<?php get_footer(); ?>
  
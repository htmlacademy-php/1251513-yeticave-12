    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <!--заполните этот список из массива категорий-->
            <? foreach ($categories_arr as $category): ?>
            <li class="promo__item promo__item--boards">
                <a class="promo__link" href="pages/all-lots.html"><?=xss_protection($category) ;?>></a>
            </li>
            <? endforeach;?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <!--заполните этот список из массива с товарами-->
            <?foreach ($items_arr as $item): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=$item['url']; ?>" width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=xss_protection($item['category']); ?>></span>
                    <h3 class="lot__title"><a class="text-link" href="pages/lot.html"><?=xss_protection($item['name']); ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?=price_format($item['price']); ?></span>
                        </div>
                        <? $time_arr=get_dt_range($item['expiry_date']);
                            $red_flag = '';
                           if($time_arr[0] == '00') $red_flag = 'timer--finishing' ;
                         ?>
                        
                        <div class="lot__timer timer <?=$red_flag?>">
                        <? print($time_arr[0].':'.$time_arr[1])?>
                        </div>
                    </div>
                </div>
            </li>
            <?endforeach; ?>
        </ul>
    </section>
<main>
    <nav class="nav">
      <ul class="nav__list container">
          <?php foreach($categories_arr as $category): ?>
        <li class="nav__item">
          <a href="index.php?category=<?=$category['code']?>"><?=xss_protection($category['name'])?></a>
        </li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <div class="container">
      <section class="lots">
        <h2>Результаты поиска по запросу «<span><?=xss_protection($query); ?></span>»</h2>
        <?php if(count($items_arr) == 0): ?>
          <h2>Ничего не найдено по вашему запросу</h2>
        <?php endif; ?>
        <ul class="lots__list">
        <?php foreach ($items_arr as $item): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=$item['url']; ?>" width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=xss_protection($item['category']); ?></span>
                    <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=$item['id']?>"><?=xss_protection($item['name']); ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?=price_format($item['price']); ?></span>
                        </div>
                        <?php $time_arr=get_dt_range($item['expiry_date']);
                            $red_flag = $time_arr[0] == '00'?'timer--finishing':'';
                            ?>

                        <div class="lot__timer timer <?=$red_flag?>">
                        <?php print($time_arr[0].':'.$time_arr[1])?>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
      </section>
      <?php if($paginationListNumber > 1): ?>
      <ul class="pagination-list">
        <li class="pagination-item pagination-item-prev"><a <?php if ($position != 1): ?> href="search.php?search=<?=$query?>&page=<?=$position-1?>" <?php endif; ?>>Назад</a></li>
        <?php for ($i = 1; $i <= $paginationListNumber; $i++): ?>
          <li class="pagination-item <?php if ($position == $i): ?> pagination-item-active <?php endif; ?>"><a <?php if ($position != $i): ?>href="search.php?search=<?=$query?>&page=<?=$i?>"<?php endif;?>><?=$i?></a></li>  
            <?php if($i < $position - 2): ?><?php $i = $position - 3; ?>...<?php endif;?>
            <?php if($i > $position + 1 && $i < $paginationListNumber): ?><?php $i = $paginationListNumber - 1; ?>...<?php endif;?>
        <?php endfor?>
        <li class="pagination-item pagination-item-next"><a <?php if ($position != $paginationListNumber): ?> href="search.php?search=<?=$query?>&page=<?=$position+1?>" <?php endif; ?>>Вперед</a></li>
      </ul>
      <?php endif; ?>
    </div>
  </main>
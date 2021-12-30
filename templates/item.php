<main>
<nav class="nav">
      <ul class="nav__list container">
          <?php foreach ($categories_arr as $category) :?>
        <li class="nav__item">
          <a href="index.php?category=<?=$category['code']?>"><?=xssProtection($category['name'])?></a>
        </li>
          <?php endforeach ;?>
      </ul>
    </nav>
<section class="lot-item container">
    <h2><?=$item_name?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?=$img_path?>" width="730" height="548" alt="Сноуборд">
            </div>
            <p class="lot-item__category">Категория: <span><?=xssProtection($category_name)?></span></p>
            <p class="lot-item__description"><?=$description?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state">
                <?php $time_arr=getDateRange($completion_date);
                $red_flag = $time_arr[0] == '00'?'timer--finishing':'';
                ?>
                <div class="lot-item__timer timer <?=$red_flag?> ">
                    <?php print($time_arr[0].':'.$time_arr[1])?>
                </div>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost"><?=priceFormat($current_price)?></span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка <span><?=priceFormat($min_bid)?></span>
                    </div>
                </div>
                <?php if ($display_lot_item_form_flag) :?>
                <form class="lot-item__form" action="lot.php?id=<?=$id?>" method="post" autocomplete="off">
                    <p class="lot-item__form-item form__item
                    <?php if (isset($error)) :?>
                    form__item--invalid
                    <?php endif; ?>">
                        <label for="cost">Ваша ставка</label>
                        <input id="cost" type="text" name="cost" placeholder="
                        <?php if (!isset($bid)) : ?>
                            <?= priceFormat($min_bid) ?>
                        <?php else :?>
                            <?= $bid ?>
                        <?php endif; ?>">
                        <span class="form__error">
                            <?php if (isset($error)) :?>
                                <?=$error?>

                            <?php endif; ?>
                        </span>
                    </p>
                    <button type="submit" class="button">Сделать ставку</button>
                </form>
                <?php endif; ?>
            </div>

            <?php if (count($bid_history) > 0) : ?>
            <div class="history">
              <h3>История ставок (<span><?=count($bid_history); ?></span>)</h3>
              <table class="history__list">
                <?php foreach ($bid_history as $bid) : ?>
                <tr class="history__item">
                  <td class="history__name"><?=xssProtection($bid['name']);?></td>
                  <td class="history__price"><?=xssProtection($bid['price']);?></td>
                  <td class="history__time"><?=xssProtection(getBidDate($bid['date']));?></td>
                </tr>
                <?php endforeach; ?>
              </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
</main>

<main>
<nav class="nav">
      <ul class="nav__list container">
          <?php foreach ($categories_arr as $category) : ?>
        <li class="nav__item">
          <a href="index.php?category=<?=$category['code']?>"><?=xssProtection($category['name'])?></a>
        </li>
          <?php endforeach; ?>
      </ul>
    </nav>
<section class="rates container">
      <h2>Мои ставки</h2>
      <table class="rates__list">
        <?php foreach ($bets_arr as $bet_arr) : ?>
            <?php
            $bet_status = 'active';
            $rates_timer_status = '';
            $rates_item_status ='';
            $time_arr=getDateRange($bet_arr['completion_date']);
            if ($user_id == $bet_arr['winner_id']) {
                $bet_status = 'win';
                $rates_item_status ='rates__item--win';
                $rates_timer_status = 'timer--win';
            } elseif ($time_arr[0] == '00' && $time_arr[1] == '00') {
                $bet_status = 'over';
                $rates_item_status ='rates__item--end';
                $rates_timer_status = 'timer--end';
            } elseif ($time_arr[0] == '00') {
                $rates_timer_status = 'timer--finishing';
            }
            ?>
        <tr class="rates__item <?=$rates_item_status; ?>">
          <td class="rates__info">
            <div class="rates__img">
              <img src="<?=$bet_arr['img_path']?>" width="54" height="40" alt="
              <?=xssProtection($bet_arr['category_name']); ?>">
            </div>
            <h3 class="rates__title"><a href="lot.php?id=<?=$bet_arr['id']?>">
                    <?=xssProtection($bet_arr['item_name']); ?></a></h3>
            <?php if ($bet_status == 'win') : ?>
              <p><?=xssProtection($bet_arr['contact']); ?></p>
            <?php endif; ?>
          </td>
          <td class="rates__category">
             <?=xssProtection($bet_arr['category_name']); ?>
          </td>
          <td class="rates__timer">
            <div class="timer <?=$rates_timer_status?>">
            <?php if ($bet_status == 'win') : ?>
              Ставка выиграла
            <?php elseif ($bet_status == 'over') : ?>
              Торги окончены
            <?php else : ?>
                <?php print($time_arr[0].':'.$time_arr[1])?>
            <?php endif; ?>
          </div>
          </td>
          <td class="rates__price">
            <?=priceFormat($bet_arr['price']); ?>
          </td>
          <td class="rates__time">
            <?=xssProtection(getBidDate($bet_arr['date']));?>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
    </section>
</main>

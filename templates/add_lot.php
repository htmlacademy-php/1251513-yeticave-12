<main>
<nav class="nav">
     <ul class="nav__list container">
        <?php foreach ($categories_arr as $category) : ?>
            <li class="nav__item">
                <a href="index.php?category=<?=$category['code']?>"><?=xssProtection($category['name']); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<form class="form form--add-lot container form--invalid" action="add.php" method="post" enctype="multipart/form-data">
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item <?php if (isset($form_errors['lot-name'])) :
            ?>form__item--invalid<?php
                               endif;?>">
            <label for="lot-name">Наименование <sup>*</sup></label>
            <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота"
                   value="<?=xssProtection($incoming_data['lot-name']); ?>">
            <span class="form__error">
                <?php if (isset($form_errors['lot-name'])) :?>
                    <?= $form_errors['lot-name'] ;?>
                <?php endif ;?>
            </span>
        </div>
        <div class="form__item <?php if (isset($form_errors['category'])) :
            ?>form__item--invalid<?php
                               endif;?>">
            <label for="category">Категория <sup>*</sup></label>
              <select id="category" name="category">
                <option>Выберите категорию</option>
                <?php foreach ($categories_arr as $category) : ?>
                  <option <?php if ($category['name'] == $incoming_data['category']) :
                        ?> selected<?php
                          endif;?>><?=xssProtection($category['name']); ?></option>
                <?php endforeach; ?>
              </select>
            <span class="form__error"><?php if (isset($form_errors['category'])) :
                ?><?=$form_errors['category'];?><?php
                                      endif; ?></span>
        </div>
      </div>
      <div class="form__item form__item--wide <?php if (isset($form_errors['message'])) :
            ?>form__item--invalid<?php
                                              endif;?>">
        <label for="message">Описание <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите описание лота">
            <?=xssProtection($incoming_data['message']); ?>
        </textarea>
        <span class="form__error"><?php if (isset($form_errors['message'])) :
            ?><?=$form_errors['message'];?><?php
                                  endif; ?></span>
      </div>
      <div class="form__item form__item--file <?php if (isset($form_errors['lot-img'])) :
            ?>form__item--invalid<?php
                                              endif;?>">
        <label>Изображение <sup>*</sup></label>
        <div class="form__input-file">
          <input class="visually-hidden" name="lot-img" type="file" id="lot-img" value="">
          <label for="lot-img">
            Добавить
          </label>
        </div>
        <span class="form__error"><?php if (isset($form_errors['lot-img'])) :
            ?><?=$form_errors['lot-img'];?><?php
                                  endif; ?></span>
      </div>
      <div class="form__container-three">
        <div class="form__item form__item--small <?php if (isset($form_errors['lot-rate'])) :
            ?>form__item--invalid<?php
                                                 endif;?>">
          <label for="lot-rate">Начальная цена <sup>*</sup></label>
          <input id="lot-rate" type="text" name="lot-rate" placeholder="0"
                 value="<?=xssProtection($incoming_data['lot-rate']); ?>">
          <span class="form__error"><?php if (isset($form_errors['lot-rate'])) :
                ?><?=$form_errors['lot-rate']?><?php
                                    endif;?></span>
        </div>
        <div class="form__item form__item--small <?php if (isset($form_errors['lot-step'])) :
            ?>form__item--invalid<?php
                                                 endif;?>">
          <label for="lot-step">Шаг ставки <sup>*</sup></label>
          <input id="lot-step" type="text" name="lot-step" placeholder="0"
                 value="<?=xssProtection($incoming_data['lot-step']); ?>">
          <span class="form__error"><?php if (isset($form_errors['lot-step'])) :
                ?><?=$form_errors['lot-step']?><?php
                                    endif;?></span>
        </div>
        <div class="form__item <?php if (isset($form_errors['lot-date'])) :
            ?>form__item--invalid<?php
                               endif;?>">
          <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
          <input class="form__input-date" id="lot-date" type="text" name="lot-date"
                 placeholder="Введите дату в формате ГГГГ-ММ-ДД"
                 value="<?=xssProtection($incoming_data['lot-date']); ?>">
          <span class="form__error"><?php if (isset($form_errors['lot-date'])) :
                ?><?=$form_errors['lot-date']?><?php
                                    endif;?></span>
        </div>
    </div>
    <span class="form__error form__error--bottom"><?php if (count($form_errors) > 0) :
        ?>Пожалуйста, исправьте ошибки в форме.<?php
                                                  endif; ?></span>
    <button type="submit" name="submit" class="button">Добавить лот</button>
</form>
</main>

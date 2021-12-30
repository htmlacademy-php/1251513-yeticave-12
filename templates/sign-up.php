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
    <form class="form container <?php if (count($form_errors) > 0) :
        ?>form--invalid<?php
                                endif; ?>"
          action="registration.php" method="post" autocomplete="off"> <!-- form
    --invalid -->
      <h2>Регистрация нового аккаунта</h2>
      <div class="form__item <?php if (isset($form_errors['email'])) :
            ?>form__item--invalid<?php
                             endif; ?>">
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="
        <?=xssProtection($incoming_data['email']); ?>">
        <span class="form__error"><?=$form_errors['email'];?></span>
      </div>
      <div class="form__item <?php if (isset($form_errors['password'])) :
            ?>form__item--invalid<?php
                             endif; ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value="
        <?=xssProtection($incoming_data['password']); ?>">
        <span class="form__error"><?=$form_errors['password'];?></span>
      </div>
      <div class="form__item <?php if (isset($form_errors['name'])) :
            ?>form__item--invalid<?php
                             endif; ?>">
        <label for="name">Имя <sup>*</sup></label>
        <input id="name" type="text" name="name" placeholder="Введите имя" value="
        <?=xssProtection($incoming_data['name']); ?>">
        <span class="form__error"><?=$form_errors['name'];?></span>
      </div>
      <div class="form__item <?php if (isset($form_errors['message'])) :
            ?>form__item--invalid<?php
                             endif; ?>">
        <label for="message">Контактные данные <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться">
            <?=xssProtection($incoming_data['message']); ?></textarea>
        <span class="form__error"><?=$form_errors['message'];?></span>
      </div>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <button type="submit" class="button" name="submit">Зарегистрироваться</button>
      <a class="text-link" href="login.php">Уже есть аккаунт</a>
    </form>
  </main>

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
    <form class="form container <?php if(count($form_errors) > 0):?>form--invalid<?php endif; ?>" action="login.php" method="post"> 
      <h2>Вход</h2>
        <div class="form__item <?php if(isset($form_errors['email'])):?>form__item--invalid<?php endif; ?>">
          <span class="form__error form__error--bottom">Вы ввели неверный email/пароль</span>
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?=xss_protection($incoming_data['email']); ?>">
        <span class="form__error"><?=$form_errors['email'];?></span>
      </div>
      <div class="form__item form__item--last <?php if(isset($form_errors['password'])):?>form__item--invalid<?php endif; ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?=xss_protection($incoming_data['password']); ?>">
        <span class="form__error"><?=$form_errors['password'];?></span>
      </div>
      <button type="submit" class="button" name="submit">Войти</button>
  </form>
</main>
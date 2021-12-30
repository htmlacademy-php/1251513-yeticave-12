<main>
<nav class="nav">
      <ul class="nav__list container">
          <?php foreach ($categories_arr as $category) :?>
        <li class="nav__item">
          <a href="index.php?category=<?=$category['code']?>"><?=xssProtection($category['name'])?></a>
        </li>
          <?php endforeach; ?>
      </ul>
    </nav>
    <section class="lot-item container">
        <h2>404 Страница не найдена</h2>
        <p>Данной страницы не существует на сайте.</p>
    </section>
</main>

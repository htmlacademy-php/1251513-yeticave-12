INSERT INTO
        category (name, code)
VALUES
       ('Доски и лыжи', 'boards'),
       ('Крепления', 'attachment'),
       ('Ботинки', 'boots'),
       ('Одежда', 'clothing'),
       ('Инструменты', 'tools'),
       ('Разное', 'other');


INSERT INTO
        user (registration_date, email, name, password, contacts)
VALUES
       ('2021-03-20 10:00:00', '1@eml.ru', 'Alexandra', 'password', 'contacts'),
       ('2021-03-20 10:30:00', '2@eml.ru', 'Kristal', 'password', 'contacts');


INSERT INTO
    item (date, name, description,img_path, start_price, completion_date,bid_step, author_id, category_id)
VALUES
        ('2021-03-20 13:00:00', '2014 Rossignol District Snowboard', '2014 Rossignol District Snowboard',
        'img/lot-1.jpg', 10999, '2021-03-23', 100, 1, 1),
        ('2021-03-20 12:00:00', 'DC Ply Mens 2016/2017 Snowboard', 'DC Ply Mens 2016/2017 Snowboard',
         'img/lot-2.jpg', 159999, '2021-03-24', 100, 1, 1),
        ('2021-03-20 15:00:00','Крепления Union Contact Pro 2015 года размер L/XL', 'Крепления Union Contact Pro 2015 года размер L/XL',
         'img/lot-3.jpg', 8000, '2021-03-25', 100, 2, 2),
        ('2021-03-21 12:00:00', 'Ботинки для сноуборда DC Mutiny Charocal', 'Ботинки для сноуборда DC Mutiny Charocal',
         'img/lot-4.jpg', 10999, '2021-03-26', 100, 2, 3),
        ('2021-03-21 14:00:00', 'Куртка для сноуборда DC Mutiny Charocal', 'Куртка для сноуборда DC Mutiny Charocal',
         'img/lot-5.jpg', 7500, '2021-03-27', 100, 1, 4),
        ('2021-03-21 16:00:00', 'Маска Oakley Canopy', 'Маска Oakley Canopy',
         'img/lot-6.jpg', 5400, '2021-05-12', 100, 2, 6);

INSERT INTO
    bid (date, price, user_id, item_id)
VALUES
       ('2021-03-23 10:00:00', 6000, 1, 6),
       ('2021-03-23 15:00:00', 6500, 1, 6);


# получить все категории
SELECT name FROM category;

# получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение,
# текущую цену, название категории;
SELECT i.name, i.start_price, img_path, MAX(d.start_price), c.name category_name FROM item i LEFT JOIN category c on c.id = i.category_id
    JOIN (SELECT id, start_price FROM item UNION SELECT item_id, price FROM bid) d ON d.id = i.id WHERE i.winner_id IS NULL
    GROUP BY i.id, i.date ORDER BY i.date DESC LIMIT 3;

# показать лот по его id. Получите также название категории, к которой принадлежит лот;
SELECT i.name, start_price, img_path, c.name category_name FROM item i JOIN category c on c.id = i.category_id WHERE i.id = 2;

# обновить название лота по его идентификатору;
UPDATE item SET name = 'DC Ply Mens 2016/2017 Snowboard' WHERE id = 2;

# получить список ставок для лота по его идентификатору с сортировкой по дате.
SELECT * FROM bid WHERE item_id =6 ORDER BY date DESC;

CREATE DATABASE yeticave DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE category(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR (128),
    code VARCHAR (128)
);

CREATE TABLE user(
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_date DATETIME,
    email VARCHAR(128),
    name VARCHAR(128),
    password VARCHAR(128),
    contacts TEXT
);

CREATE TABLE item(
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATETIME,
    name VARCHAR(128),
    description TEXT,
    img_path VARCHAR(256),
    start_price INT,
    completion_date DATE,
    bid_step INT,
    author_id INT,
    winner_id INT,
    category_id INT,
    CONSTRAINT fk_ItemUserAuthor FOREIGN KEY (author_id) REFERENCES user(id),
    CONSTRAINT fk_ItemUserWinner FOREIGN KEY (winner_id) REFERENCES user(id),
    CONSTRAINT fk_ItemCategory FOREIGN KEY (category_id) REFERENCES category(id)
);

CREATE TABLE bid(
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATETIME,
    price INT,
    user_id INT,
    item_id INT,
    CONSTRAINT fk_BidUser FOREIGN KEY (user_id) REFERENCES user(id),
    CONSTRAINT fk_BidItem FOREIGN KEY (item_id) REFERENCES item(id)
);

CREATE FULLTEXT INDEX item_ft_search ON item(name, description);

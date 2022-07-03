CREATE DATABASE IF NOT EXISTS library_project;

use library_project;
CREATE TABLE IF NOT EXISTS author (
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    name VARCHAR(50) NOT NULL,
    information VARCHAR(255) NOT NULL
);
CREATE TABLE IF NOT EXISTS reader(
   id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
   name VARCHAR(50) NOT NULL,
   telephone varchar(11) UNIQUE NOT NULL,
   email VARCHAR(50) NOT NULL UNIQUE,
   cccd VARCHAR(20) UNIQUE NOT NULL,
   lever VARCHAR(20) NOT NULL,
   password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS book(
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    name varchar(255) NOT NULL,
    amount INT NOT NULL,
    category varchar (255) NOT NULL,
    price INT NOT NULL,
    description TEXT NOT NULL,
    image LONGBLOB
);

CREATE TABLE IF NOT EXISTS admin(
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    telephone varchar(11) NOT NULL,
    lever varchar(20) NOT NULL,
    password varchar(255) NOT NULL
);
CREATE TABLE IF NOT EXISTS orderbook(
    id int AUTO_INCREMENT PRIMARY KEY not null,
    createdAt timestamp DEFAULT now() not null,
    expiryAt timestamp  DEFAULT now() NOT NULL,
    reader_id int NOT NULL,
    status varchar(100) NOT NULL,
    FOREIGN KEY (reader_id) REFERENCES reader(id)
);
CREATE TABLE IF NOT EXISTS settimeorder(
    id int AUTO_INCREMENT PRIMARY KEY not null,
    createdAt timestamp DEFAULT now() not null,
    timeSet timestamp  DEFAULT now() NOT NULL,
    reader_id int NOT NULL,
    FOREIGN KEY (reader_id) REFERENCES reader(id)
);

CREATE TABLE IF NOT EXISTS own(
    author_id int NOT NULL,
    book_id int NOT NULL,
    PRIMARY KEY (author_id, book_id),
    FOREIGN KEY (author_id) REFERENCES author(id),
    FOREIGN KEY (book_id) REFERENCES book(id)
);
CREATE TABLE IF NOT EXISTS listbookorder(
    order_id int NOT NULL,
    book_id int NOT NULL ,
    PRIMARY KEY (order_id, book_id),
    FOREIGN KEY (order_id) REFERENCES orderbook(id),
    FOREIGN KEY (book_id) REFERENCES book(id)
);
CREATE TABLE IF NOT EXISTS listbooksettimeorder(
    storder_id int NOT NULL,
    book_id int NOT NULL,
    PRIMARY KEY (storder_id, book_id),
    FOREIGN KEY (storder_id) REFERENCES settimeorder(id),
    FOREIGN KEY (book_id) REFERENCES book(id)
);
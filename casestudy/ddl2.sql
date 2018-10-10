create database casestudy_2 default character set utf8mb4;
use casestudy_2

DROP TABLE if exists likes;
DROP TABLE if exists articles;
DROP TABLE if exists users;

CREATE TABLE users (
  id               bigint AUTO_INCREMENT,
  name             text,
  create_timestamp timestamp,
  update_timestamp timestamp,
  PRIMARY KEY (id)
) engine=InnoDB;

CREATE TABLE articles (
  id               bigint AUTO_INCREMENT,
  author_id        bigint,
  title            text,
  content          text,
  like_count       int unsigned default 0,
  create_timestamp timestamp,
  update_timestamp timestamp,
  PRIMARY KEY (id)
) engine=InnoDB;

CREATE TABLE likes (
  id               bigint AUTO_INCREMENT,
  article_id       bigint,
  user_id          bigint,
  create_timestamp timestamp DEFAULT '0000-00-00',
  PRIMARY KEY (id),
  UNIQUE KEY (user_id, article_id)
) engine=InnoDB;


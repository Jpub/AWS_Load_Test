DROP TABLE if exists likes;
DROP TABLE if exists articles;
DROP TABLE if exists users;

CREATE TABLE users (
  id               bigint AUTO_INCREMENT,
  name             text,
  create_timestamp timestamp DEFAULT CURRENT_TIMESTAMP,
  update_timestamp timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) engine=InnoDB;

CREATE TABLE articles (
  id               bigint AUTO_INCREMENT,
  author_id        bigint NOT NULL,
  title            text,
  content          text,
  like_count       int unsigned default 0,
  create_timestamp timestamp DEFAULT CURRENT_TIMESTAMP,
  update_timestamp timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE
) engine=InnoDB;

CREATE TABLE likes (
  id               bigint AUTO_INCREMENT,
  article_id       bigint NOT NULL,
  user_id          bigint NOT NULL,
  create_timestamp timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY (user_id, article_id),
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE
) engine=InnoDB;
/* 地点登録 */
CREATE TABLE `register_map` (
  `register_map_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id`        INT             NOT NULL,
  `lat`             DOUBLE          NOT NULL,
  `lng`             DOUBLE          NOT NULL,
  `address`         VARCHAR(255)    NOT NULL,
  `place`           VARCHAR(50)     NOT NULL,
  `description`     VARCHAR(1000),
  `num`             INT,
  `type`            INT,
  `parking`         INT,
  `feed`            INT,
  `friendly`        INT,
  `image`           VARCHAR(100),
  `views`           BIGINT          NOT NULL,
  `status_flag`     INT             NOT NULL
  COMMENT '1またはnullで表示、2で非表示、3で論理削除',
  `created_at`      INT,
  `updated_at`      INT,
  PRIMARY KEY (`register_map_id`)/*,
  CONSTRAINT `register_map_fk01` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)*/
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = '登録地点マスタ';

/* 各地点のコメント */
CREATE TABLE `comment` (
  `comment_id`      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `register_map_id` BIGINT UNSIGNED NOT NULL,
  `users_id`        INT             NOT NULL,
  `comment`         VARCHAR(1000)   NOT NULL,
  `image`           VARCHAR(100),
  `status_flag`     INT             NOT NULL,
  `created_at`      INT,
  `updated_at`      INT,
  PRIMARY KEY (`comment_id`),
  CONSTRAINT `comment_fk01` FOREIGN KEY (`register_map_id`) REFERENCES `register_map` (`register_map_id`)/*,
  CONSTRAINT `comment_fk02` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)*/
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = '各地点のコメント';

/* マイ地点 */
CREATE TABLE `my_point` (
  `my_point_id`     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `register_map_id` BIGINT UNSIGNED NOT NULL,
  `users_id`        INT             NOT NULL,
  `created_at`      INT,
  `updated_at`      INT,
  PRIMARY KEY (`my_point_id`),
  CONSTRAINT `my_point_fk01` FOREIGN KEY (`register_map_id`) REFERENCES `register_map` (`register_map_id`),
  /*CONSTRAINT `my_point_fk02` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`),*/
  CONSTRAINT `my_point_uk01` UNIQUE(`register_map_id`, `users_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = 'マイ地点';

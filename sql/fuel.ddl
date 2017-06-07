CREATE TABLE `register_map` (
  `register_map_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id`        INT             NOT NULL,
  `lat`             DOUBLE          NOT NULL,
  `lng`             DOUBLE          NOT NULL,
  `address`         VARCHAR(255)    NOT NULL,
  `place`           VARCHAR(50)     NOT NULL,
  `desc`            VARCHAR(1000),
  `num`             INT,
  `type`            INT,
  `parking`         INT,
  `feed`            INT,
  `image`           VARCHAR(100),
  `views`           BIGINT,
  `created_at`      INT,
  `updated_at`      INT,
  PRIMARY KEY (`register_map_id`),
  CONSTRAINT `register_map_fk01` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT = '登録地点マスタ';
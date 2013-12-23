CREATE TABLE `mod_log_user` (
    `id`            INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
    `aa_inst_id`    INT(11) UNSIGNED    NOT NULL,
    `auth_uid`      BIGINT(20) UNSIGNED NULL DEFAULT '0',
    `auth_uid_temp` VARCHAR(32)         NOT NULL DEFAULT '0',
    `scope`         VARCHAR(255)        NOT NULL,
    `data`          TEXT                NOT NULL,
    `code`          INT(11) UNSIGNED    NOT NULL DEFAULT '0',
    `agend_id`      INT(11) UNSIGNED    NULL DEFAULT NULL,
    `ip`            INT(11) UNSIGNED    NULL DEFAULT NULL,
    `date_added`    TIMESTAMP           NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
)
    COLLATE ='utf8_general_ci'
    ENGINE =InnoDB;
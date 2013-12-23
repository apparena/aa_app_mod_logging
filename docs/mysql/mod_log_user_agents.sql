CREATE TABLE `mod_log_user_agents` (
    `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `hash_id` VARCHAR(32)      NOT NULL,
    `data`    TEXT             NOT NULL,
    PRIMARY KEY (`id`)
)
    COLLATE ='utf8_general_ci'
    ENGINE =InnoDB;
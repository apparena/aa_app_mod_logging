CREATE TABLE `mod_log_adminpanel` (
    `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `hash`       VARCHAR(32)      NOT NULL,
    `aa_inst_id` INT(11) UNSIGNED NOT NULL,
    `scope`      VARCHAR(100)     NOT NULL,
    `value`      VARCHAR(255)     NOT NULL,
    `counter`    INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `date_added` TIMESTAMP        NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `hash` (`hash`)
)
    COLLATE ='latin1_swedish_ci'
    ENGINE =InnoDB;
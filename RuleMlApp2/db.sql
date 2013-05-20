 CREATE TABLE `Knowledgebase` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ruleset` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
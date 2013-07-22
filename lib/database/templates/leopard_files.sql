DROP TABLE IF EXISTS `{$db_prefix}leopard_files`;

CREATE TABLE `{$db_prefix}leopard_files` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `path` varchar(128) NOT NULL COMMENT 'Relative path from webroot to file',
  `md5` varchar(33) NOT NULL COMMENT 'MD5 sum of file contents',
  `package` varchar(64) NOT NULL COMMENT 'Package this file belongs to',
  `created` datetime NOT NULL,
  `dependencies` varchar(1024) NOT NULL COMMENT 'Serialized array with dependency list',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


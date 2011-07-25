CREATE TABLE IF NOT EXISTS `votes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ownerModel` varchar(50) NOT NULL,
  `ownerId` int(10) unsigned NOT NULL,
  `voterId` int(10) unsigned DEFAULT NULL,
  `voterIP` varchar(15) NOT NULL,
  `voterUserAgent` varchar(450) NOT NULL,
  `score` int(11) NOT NULL,
  `timeAdded` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ownerModel` (`ownerModel`,`ownerId`,`score`),
  KEY `ownerModel_3` (`ownerModel`),
  KEY `ownerId` (`ownerId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
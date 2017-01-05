CREATE DATABASE  `useddomaintools` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `domains` (
  `id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `domainname` VARCHAR(80) NOT NULL,
  `expiredatetime` DATETIME NOT NULL,
  `Status` INT(10) NOT NULL,
  `httpcode` INT(3) NOT NULL,
  `mozcheck` INT(11) NOT NULL,
  `insertdatetime` DATETIME NOT NULL,
  `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `memo` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domainName` (`domainName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cookies` (
  `id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` VARCHAR(128) NOT NULL,
  `flag` TINYINT(4) NOT NULL,
  `path` VARCHAR(128) NOT NULL,
  `secureflag` TINYINT(4) NOT NULL,
  `expires` INT(11) NOT NULL,
  `cookiename` VARCHAR(128) NOT NULL,
  `cookievalue` TEXT NOT NULL,
  `useragent` VARCHAR(128) NOT NULL,
  `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `checksites` (
  `id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned NOT NULL,
  `colname` varchar(128) NOT NULL,
  `coltype` varchar(8) NOT NULL,
  `colvalue` varchar(128) NULL,
  `colnum` INT(11) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `checkdomains` (
  `id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned NOT NULL,
  `colname` varchar(128) NOT NULL,
  `coltype` varchar(8) NOT NULL,
  `colvalue` varchar(128) NULL,
  `colnum` INT(11) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


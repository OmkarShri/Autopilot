SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `auto_auth` (
  `id` int(11) NOT NULL,
  `user` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(200) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `auto_auth` (`id`, `user`, `password`) VALUES
(2, 'admin', '21232f297a57a5a743894a0e4a801fc3');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `auto_competitor` (
  `id` int(11) NOT NULL,
  `agent_id` text COLLATE utf8_unicode_ci,
  `competitor` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `product` int(100) NOT NULL,
  `status` varchar(200) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `auto_competitor` (`id`, `agent_id`, `competitor`, `product`, `status`) VALUES
(15, '1020', 'Linio', 0, 'yes'),
(18, '1023:1024', 'Alkomprar', 0, 'yes'),
(12, '1015:1016', 'Falabella', 0, 'yes'),
(17, '1021:1022', 'Ktronix', 0, 'yes');

-- --------------------------------------------------------


CREATE TABLE IF NOT EXISTS `auto_rulemanager` (
  `ruleid` int(100) NOT NULL,
  `name` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `ruledesc` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(200) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `auto_rulemanager` (`ruleid`, `name`, `ruledesc`, `type`) VALUES
(67, 'Test Rule - Cost 10%', 'Test Rule - Cost 10%', 'cost'),
(43, '$1000 Off Above - 2nd competitor', '$1000 Off Above - 2nd competitor', 'competitor'),
(68, 'Cost based rule (5%+) ', 'Cost based rule (5%+) ', 'cost'),
(70, '2nd price', '2nd price', 'competitor');

-- --------------------------------------------------------


CREATE TABLE IF NOT EXISTS `auto_rule_competitor` (
  `ruleid` int(11) NOT NULL,
  `level` text COLLATE utf8_unicode_ci NOT NULL,
  `topcomp` int(50) NOT NULL,
  `rate` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `mode` text COLLATE utf8_unicode_ci NOT NULL,
  `gmode` text COLLATE utf8_unicode_ci NOT NULL,
  `grate` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `gcostmode` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `auto_rule_competitor` (`ruleid`, `level`, `topcomp`, `rate`, `mode`, `gmode`, `grate`, `gcostmode`) VALUES
(70, 'B', 2, '5000', 'fixed', 'setprice', '2', 'percent'),
(43, 'A', 2, '1000', 'fixed', 'currentprice', '', '0');

-- --------------------------------------------------------


CREATE TABLE IF NOT EXISTS `auto_rule_cost` (
  `ruleid` int(50) NOT NULL,
  `rate` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `mode` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `auto_rule_cost` (`ruleid`, `rate`, `mode`) VALUES
(68, '5', 'percent'),
(67, '10', 'percent');

-- --------------------------------------------------------


CREATE TABLE IF NOT EXISTS `web` (
  `web_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `created_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `auto_auth`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `auto_competitor`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `auto_rulemanager`
  ADD PRIMARY KEY (`ruleid`);

ALTER TABLE `auto_rule_competitor`
  ADD PRIMARY KEY (`ruleid`);

ALTER TABLE `auto_rule_cost`
  ADD PRIMARY KEY (`ruleid`);

ALTER TABLE `web`
  ADD PRIMARY KEY (`web_id`);

ALTER TABLE `auto_auth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
ALTER TABLE `auto_competitor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
ALTER TABLE `auto_rulemanager`
  MODIFY `ruleid` int(100) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=71;
ALTER TABLE `web`
  MODIFY `web_id` int(11) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

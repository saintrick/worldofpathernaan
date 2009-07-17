CREATE TABLE IF NOT EXISTS `cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
INSERT INTO `cities` (`name`, `description`, `price`) VALUES
('Des Moines', 'A small city in the heart of Iowa.', 100),
('Moraine', 'A small city in Ohio, USA', 100),
('Paris', 'The capital of France, home of the eiffle tower. ', 300);

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `prefix` varchar(255) NOT NULL,
  `suffix` varchar(255) NOT NULL,
  `permission` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO `groups` (`name`, `prefix`, `suffix`, `permission`) VALUES
('Registered Members', '', '', 1),
('Administrator', '<font color="red"><b>', '</b></font>', 90),
('Owner', '<font color="blue"><b>', '</b></font>', 100);


CREATE TABLE IF NOT EXISTS `mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `senddate` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `from` int(11) NOT NULL,
  `body` text NOT NULL,
  `status` enum('unread','read','deleted') NOT NULL DEFAULT 'unread',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `settings` (
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `settings` (`name`, `value`) VALUES
('gamename', 'Pathernaan'),
('captcha', 'enabled');

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `rank` int(11) NOT NULL DEFAULT '1',
  `city` int(11) NOT NULL DEFAULT '1',
  `cash` int(11) NOT NULL DEFAULT '150',
  `eng` int(11) NOT NULL DEFAULT '50',
  `meng` int(11) NOT NULL DEFAULT '50',
  `regdate` int(11) NOT NULL,
  `last_active` int(11) NOT NULL DEFAULT '-1',
  `register_ip` int(11) NOT NULL,
  `last_ip` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(256) NOT NULL,
  `password` text NOT NULL,
  `email` text NOT NULL,
  `need_email` int(11) NOT NULL,
  `language` text NOT NULL,
  `activated_time` int(11) NOT NULL,
  `activated_add_time` int(11) NOT NULL,
  `tariff_time` int(11) NOT NULL,
  `archives_time_info` text NOT NULL,
  `users_limit` int(11) NOT NULL,
  `users_online` text NOT NULL,
  `email_send_time` int(11) NOT NULL,
  `ip_old` text NOT NULL,
  `ip_new` text NOT NULL,
  `last_enter_time` int(11) NOT NULL,
  `last_update_time` int(11) NOT NULL,
  `services` text NOT NULL,
  `type` text NOT NULL,
  `note` text NOT NULL,
  `banned` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `type` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `sid` varchar(256) NOT NULL,
  `alias` varchar(256) NOT NULL,
  `title` text NOT NULL,
  `boxinfo` mediumtext NOT NULL,
  `note` text NOT NULL,
  `html_header` text NOT NULL,
  `html_footer` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `codes`
--

CREATE TABLE IF NOT EXISTS `codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_time` int(11) NOT NULL,
  `activated_time` int(11) NOT NULL,
  `activated_add_time` int(11) NOT NULL,
  `code` text NOT NULL,
  `user_id` text NOT NULL,
  `service` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `buttons`
--

CREATE TABLE IF NOT EXISTS `buttons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_time` int(11) NOT NULL,
  `activated_add_time` int(11) NOT NULL,
  `link` text NOT NULL,
  `service` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
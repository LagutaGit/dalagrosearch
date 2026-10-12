CREATE TABLE IF NOT EXISTS `app_auth_tokens` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `token_hashed` varchar(255) NOT NULL,
  `user_ip` varchar(64) NOT NULL,
  `date_expiry` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_token` (`token`),
  KEY `idx_user_ip` (`user_ip`),
  KEY `idx_date_expiry` (`date_expiry`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE app_reports_sections ADD report_right3 varchar(64) DEFAULT '' NOT NULL;
ALTER TABLE app_reports_sections ADD report_right4 varchar(64) DEFAULT '' NOT NULL;
ALTER TABLE app_reports_sections ADD report_right5 varchar(64) DEFAULT '' NOT NULL;
ALTER TABLE app_reports_sections ADD report_right6 varchar(64) DEFAULT '' NOT NULL;

CREATE TABLE IF NOT EXISTS `app_widgets` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_by` int(10) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `type` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `subtitle` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL DEFAULT '',
  `users_groups` text NOT NULL DEFAULT '',
  `notes` text NOT NULL DEFAULT '',
  `display_chart` varchar(64) NOT NULL,
  `chart_type` varchar(64) NOT NULL,
  `chart_height` smallint(6) NOT NULL,
  `text_color` varchar(16) NOT NULL,
  `bg_color` varchar(16) NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_hide_labels` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

CREATE TABLE IF NOT EXISTS `app_widgets_calc` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sort_order` smallint(6) NOT NULL DEFAULT 0,
  `type` varchar(64) NOT NULL,
  `entities_id` int(10) UNSIGNED NOT NULL,
  `formula` varchar(255) NOT NULL,
  `color` varchar(16) NOT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0,
  `number_format` varchar(16) NOT NULL,
  `number_prefix` varchar(16) NOT NULL,
  `number_suffix` varchar(16) NOT NULL,
  `widget_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_widget_id` (`widget_id`),
  KEY `idx_entity_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_widgets_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

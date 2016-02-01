/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

CREATE TABLE IF NOT EXISTS `#__booking_admin` (
  `id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT '0',
  `title_before` varchar(20) NOT NULL DEFAULT '',
  `firstname` varchar(35) NOT NULL DEFAULT '',
  `middlename` varchar(35) NOT NULL DEFAULT '',
  `surname` varchar(35) NOT NULL DEFAULT '',
  `title_after` varchar(20) NOT NULL DEFAULT '',
  `company` varchar(70) NOT NULL DEFAULT '',
  `company_id` varchar(20) NOT NULL,
  `vat_id` varchar(20) NOT NULL,
  `street` varchar(35) NOT NULL DEFAULT '',
  `city` varchar(35) NOT NULL DEFAULT '',
  `country` varchar(35) NOT NULL DEFAULT '',
  `zip` varchar(10) NOT NULL DEFAULT '',
  `telephone` varchar(20) NOT NULL DEFAULT '',
  `fax` varchar(20) NOT NULL DEFAULT '',
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fields` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_google_calendar` (
  `id` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `modified` datetime NOT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS #__booking_occupancy_type (
  id int(11) NOT NULL AUTO_INCREMENT,
  `subject` int(11) NOT NULL,
  title varchar(100) NOT NULL,
  `type` tinyint(1) NOT NULL,
  PRIMARY KEY (id),
  KEY subject_id (`subject`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `info` text NOT NULL,
  `alias` varchar(255) NOT NULL,
  `pay` tinyint(1) NOT NULL DEFAULT '1',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `asset_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`,`checked_out`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` int(11) NOT NULL DEFAULT '0',
  `value` float NOT NULL DEFAULT '0',
  `deposit` float NOT NULL DEFAULT '0',
  `deposit_type` tinyint(1) NOT NULL DEFAULT '1',
  `deposit_multiply` tinyint(4) NOT NULL DEFAULT '0',
  `price_capacity_multiply` tinyint(4) NOT NULL DEFAULT '1',
  `deposit_capacity_multiply` tinyint(4) NOT NULL DEFAULT '1',
  `deposit_include_supplements` tinyint(1) NOT NULL DEFAULT '0',
  `price_standard_occupancy_multiply` tinyint(1) NOT NULL DEFAULT '1', 
  `price_extra_occupancy_multiply` tinyint(1) NOT NULL DEFAULT '0',
  `deposit_standard_occupancy_multiply` tinyint(1) NOT NULL DEFAULT '1',
  `deposit_extra_occupancy_multiply` tinyint(1) NOT NULL DEFAULT '0',
  `volume_discount` text NOT NULL,
  `occupancy_price_modifier` TEXT NOT NULL,
  `rezervation_type` int(11) NOT NULL DEFAULT '0',
  `date_up` date NOT NULL DEFAULT '0000-00-00',
  `date_down` date NOT NULL DEFAULT '0000-00-00',
  `time_up` time NOT NULL DEFAULT '00:00:00',
  `time_down` time NOT NULL DEFAULT '00:00:00',
  `cancel_time` INT DEFAULT NULL,
  `monday` tinyint(4) NOT NULL DEFAULT '0',
  `tuesday` tinyint(4) NOT NULL DEFAULT '0',
  `wednesday` tinyint(4) NOT NULL DEFAULT '0',
  `thursday` tinyint(4) NOT NULL DEFAULT '0',
  `friday` tinyint(4) NOT NULL DEFAULT '0',
  `saturday` tinyint(4) NOT NULL DEFAULT '0',
  `sunday` tinyint(4) NOT NULL DEFAULT '0',
  `week` tinyint(4) NOT NULL DEFAULT '0',
  `custom_color` VARCHAR(20) DEFAULT NULL,
  `time_range` tinyint(4) NOT NULL DEFAULT '0',
  `head_piece` int(11) NOT NULL DEFAULT '0',
  `tail_piece` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_reservation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `title_before` varchar(20) NOT NULL DEFAULT '',
  `firstname` varchar(35) NOT NULL DEFAULT '',
  `middlename` varchar(35) NOT NULL DEFAULT '',
  `surname` varchar(35) NOT NULL DEFAULT '',
  `title_after` varchar(20) NOT NULL DEFAULT '',
  `more_names` TEXT NOT NULL,
  `company` varchar(70) NOT NULL DEFAULT '',
  `company_id` varchar(20) NOT NULL,
  `vat_id` varchar(20) NOT NULL,
  `street` varchar(35) NOT NULL DEFAULT '',
  `city` varchar(35) NOT NULL DEFAULT '',
  `country` varchar(35) NOT NULL DEFAULT '',
  `zip` varchar(10) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `telephone` varchar(20) NOT NULL DEFAULT '',
  `fax` varchar(20) NOT NULL DEFAULT '',
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `paid` tinyint(4) NOT NULL DEFAULT '0',
  `payment_method_id` varchar(50) NOT NULL,
  `payment_method_name` varchar(50) NOT NULL,
  `payment_type` TINYINT(1) NOT NULL DEFAULT '1',
  `payment_method_info` text NOT NULL,
  `note` text NOT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fields` longtext NOT NULL,
  `book_time` datetime DEFAULT NULL,
  `follow_up_sent` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customer` (`customer`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_reservation_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL DEFAULT '0',
  `rtype` tinyint(4) NOT NULL DEFAULT '0',
  `subject` int(11) NOT NULL DEFAULT '0',
  `subject_title` varchar(255) NOT NULL,
  `sub_subject` int(11) NOT NULL DEFAULT '0',
  `sub_subject_title` varchar(255) NOT NULL,
  `from` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `to` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `capacity` int(11) NOT NULL DEFAULT '0',
  `more_names` TEXT NOT NULL,
  `occupancy` TEXT NOT NULL,
  `price` float NOT NULL DEFAULT '0',
  `deposit` float NOT NULL DEFAULT '0',
  `fullPrice` float NOT NULL DEFAULT '0',
  `fullPriceSupplements` float NOT NULL DEFAULT '0',
  `provision` float NOT NULL DEFAULT '0',
  `fullDeposit` float NOT NULL DEFAULT '0',
  `tax` float NOT NULL DEFAULT '0',
  `cancel_time` int(11) DEFAULT NULL,
  `message` varchar(255) NOT NULL,
  `period_time_up` time NOT NULL,
  `period_time_down` time NOT NULL,
  `period_type` tinyint(1) NOT NULL,
  `period_recurrence` int(11) NOT NULL,
  `period_monday` tinyint(1) NOT NULL,
  `period_tuesday` tinyint(1) NOT NULL,
  `period_wednesday` tinyint(1) NOT NULL,
  `period_thursday` tinyint(1) NOT NULL,
  `period_friday` tinyint(1) NOT NULL,
  `period_saturday` tinyint(1) NOT NULL,
  `period_sunday` tinyint(1) NOT NULL,
  `period_month` tinyint(2) NOT NULL,
  `period_week` tinyint(1) NOT NULL,
  `period_day` tinyint(1) NOT NULL,
  `period_date_up` date NOT NULL,
  `period_end` tinyint(1) NOT NULL,
  `period_occurrences` int(11) NOT NULL,
  `period_date_down` date NOT NULL,
  `period_total` int(11) NOT NULL,
  `google_calendar_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reservation_id` (`reservation_id`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_reservation_period` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_item_id` int(11) NOT NULL,
  `from` datetime NOT NULL,
  `to` datetime NOT NULL,
  `google_calendar_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reservation_item_id` (`reservation_item_id`),
  KEY `from` (`from`,`to`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_reservation_supplement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ordering` int(11) NOT NULL,
  `reservation` int(11) NOT NULL,
  `supplement` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` tinyint(4) NOT NULL,
  `value` varchar(255) NOT NULL,
  `paid` tinyint(4) NOT NULL,
  `price` float NOT NULL DEFAULT '0',
  `fullPrice` float NOT NULL DEFAULT '0',
  `surcharge_value` FLOAT NOT NULL DEFAULT '0',
  `surcharge_label` VARCHAR(255) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT '0',
  `boxsCount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `reservation` (`reservation`),
  KEY `supplement` (`supplement`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_reservation_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` int(11) NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL DEFAULT '',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `capacity_unit` int(11) NOT NULL DEFAULT '0',
  `time_unit` int(11) NOT NULL DEFAULT '0',
  `gap_time` int(11) NOT NULL DEFAULT '0',
  `dynamic_gap_time` tinyint(1) NOT NULL DEFAULT '0',
  `special_offer` tinyint(4) NOT NULL DEFAULT '0',
  `min` int(11) NOT NULL DEFAULT '0',
  `max` int(11) NOT NULL DEFAULT '0',
  `fix` int(11) NOT NULL DEFAULT '0',
  `fix_from` text NOT NULL,
  `book_fix_past` TINYINT NOT NULL DEFAULT '0',
  `fix_multiply` TINYINT NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL DEFAULT '0',
  `template` int(11) NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL DEFAULT '',
  `alias` varchar(100) NOT NULL DEFAULT '',
  `introtext` text NOT NULL,
  `fulltext` text NOT NULL,
  `total_capacity` int(11) NOT NULL DEFAULT '0',
  `show_occupancy` tinyint(1) NOT NULL DEFAULT '1',
  `standard_occupancy_max` int(11) NOT NULL DEFAULT '0',
  `standard_occupancy_min` int(11) NOT NULL DEFAULT '0',
  `extra_occupancy_max`	int(11) NOT NULL DEFAULT '0',
  `extra_occupancy_min`	int(11) NOT NULL DEFAULT '0',
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `featured` tinyint(4) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` tinyint(4) NOT NULL DEFAULT '0',
  `hits` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `use_pdf_ticket` tinyint(1) NOT NULL,
  `pdf_ticket_template` text NOT NULL,
  `pdf_ticket_width` int(11) NOT NULL,
  `pdf_ticket_heigth` int(11) NOT NULL,
  `pdf_ticket_border` tinyint(1) NOT NULL,
  `pdf_ticket_font` varchar(50) NOT NULL,
  `pdf_ticket_font_size` int(11) NOT NULL,
  `pdf_ticket_format` varchar(50) NOT NULL,
  `pdf_ticket_availability` tinyint(1) NOT NULL,
  `google_calendar` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__booking_supplement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ordering` int(11) NOT NULL,
  `subject` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` tinyint(4) NOT NULL,
  `options` text NOT NULL,
  `empty` tinyint(4) NOT NULL,
  `paid` tinyint(4) NOT NULL,
  `price` text NOT NULL,
  `member_discount` text NOT NULL,
  `surcharge_value` FLOAT NOT NULL DEFAULT '0',
  `surcharge_label` VARCHAR(255) NOT NULL,
  `capacity_multiply` tinyint(4) NOT NULL DEFAULT '1',
  `capacity_max` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `capacity_min` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `unit_multiply` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_template` (
  `id` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `xml` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_template_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `value` (`value`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_creditcards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL,
  `card_type` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `card_number` varchar(255) NOT NULL,
  `sec_code` varchar(255) NOT NULL,
  `exp_month` int(2) NOT NULL,
  `exp_year` int(2) NOT NULL,
  `pay_type` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_user_config` (
  `user_id` int(11) NOT NULL,
  `payments` text NOT NULL DEFAULT '',
  `config` text NOT NULL DEFAULT '',
  `calendar` text NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__booking_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(100) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `sms` text NOT NULL,
  `mode` tinyint(1) NOT NULL DEFAULT '0',
  `usage` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `checked_out` (`checked_out`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `checked_out` (`checked_out`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `#__booking_article` (`id`, `title`, `text`, `checked_out`, `checked_out_time`) VALUES
(1, 'Terms of Contract', '', 0, '0000-00-00 00:00:00') ON DUPLICATE KEY UPDATE `id` = 1;

INSERT INTO `#__booking_article` (`id`, `title`, `text`, `checked_out`, `checked_out_time`) VALUES
(2, 'Terms of Privacy', '', 0, '0000-00-00 00:00:00') ON DUPLICATE KEY UPDATE `id` = 2;

CREATE TABLE IF NOT EXISTS `#__booking_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `pick_up` tinyint(1) NOT NULL DEFAULT 0,
  `drop_off` tinyint(1) NOT NULL DEFAULT 0,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `checked_out` (`checked_out`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_closingday` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `text` text NOT NULL,
  `date_up` date NOT NULL,
  `date_down` date NOT NULL,
  `monday` tinyint(1) NOT NULL DEFAULT '1',
  `tuesday` tinyint(1) NOT NULL DEFAULT '1',
  `wednesday` tinyint(1) NOT NULL DEFAULT '1',
  `thursday` tinyint(1) NOT NULL DEFAULT '1',
  `friday` tinyint(1) NOT NULL DEFAULT '1',
  `saturday` tinyint(1) NOT NULL DEFAULT '1',
  `sunday` tinyint(1) NOT NULL DEFAULT '1',
  `time_up` time NOT NULL DEFAULT '00:00:00',
  `time_down` time NOT NULL DEFAULT '00:00:00',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `color` varchar(6) NOT NULL DEFAULT '',
  `show` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `from` (`date_up`,`date_down`),
  KEY `checked_out` (`checked_out`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__booking_closingday_subject` (
  `closingday_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  UNIQUE KEY `exception_id` (`closingday_id`,`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


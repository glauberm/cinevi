/**
 * @version		$Id$
 * @package		ARTIO Booking 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

INSERT INTO `#__booking_email` (`id`, `subject`, `body`, `mode`, `checked_out`, `checked_out_time`) VALUES
(1, 'Your registration successfully saved.', '<p>Dear customer,<br /><br />your registration was successfully delivered. <br /><br />Registration date: {REGISTRATION DATE}<br />Username: {USERNAME}<br />Password: {PASSWORD}<br />E-mail: {E-MAIL}<br />Name: {NAME}<br />Company: {COMPANY}<br />Address: {ADDRESS}<br />Telephone: {TELEPHONE}<br />Fax: {FAX} <br /><br />Best regards <br /><br />This E-mail was generate automatically.</p>', 1, 400, '2013-03-13 11:29:41'),
(2, 'New registration successfully saved.', '<p>New customer registered<br /><br />Registration date: {REGISTRATION DATE}<br />Username: {USERNAME}<br />Password: {PASSWORD}<br />E-mail: {E-MAIL}<br />Name: {NAME}<br />Company: {COMPANY}<br />Address: {ADDRESS}<br />Telephone: {TELEPHONE}<br />Fax: {FAX} <br /><br />This E-mail was generate automatically.</p>', 1, 0, '0000-00-00 00:00:00'),
(3, 'Your reservation successfully saved', '<p>Dear Customer,<br /><br />your Reservation was successfully saved.<br /><br />{OBJECTS}<br />Object: {OBJECT TITLE}<br />Reservation Date: {DATE}<br />Quantity: {QUANTITY}<br />Price: {PRICE}<br />Deposit: {DEPOSIT}<br />{SUPPLEMENTS}<br />{/OBJECTS}<br />Full Price: {FULLPRICE}<br />Full Deposit: {FULLDEPOSIT}<br /><br />Customer: {CUSTOMER}<br />Address: {ADDRESS}<br />Company: {COMPANY}<br />E-mail: {EMAIL}<br />Telephone: {TELEPHONE}<br />Fax: {FAX}<br /><br />Best regards.<br /><br />This E-mail was generated automatically.</p>', 1, 0, '0000-00-00 00:00:00'),
(4, 'New reservation received', '<p>New Reservation received.<br /><br />{OBJECTS}<br />Object: {OBJECT TITLE}<br />Reservation Date: {DATE}<br />Quantity: {QUANTITY}<br />Price: {PRICE}<br />Deposit: {DEPOSIT}<br />{SUPPLEMENTS}<br />{/OBJECTS}<br />Full Price: {FULLPRICE}<br />Full Deposit: {FULLDEPOSIT}<br /><br />Customer: {CUSTOMER}<br />Address: {ADDRESS}<br />Company: {COMPANY}<br />E-mail: {EMAIL}<br />Telephone: {TELEPHONE}<br />Fax: {FAX}<br /><br /><br />This E-mail was generated automatically.</p>', 1, 0, '0000-00-00 00:00:00'),
(5, 'Your reservation changed status', '<p>Dear Customer,<br /><br />status of your reservation was changed to {STATUS}<br /><br />Reservation info:<br /><br />{OBJECTS}<br />Object: {OBJECT TITLE}<br />Reservation Date: {DATE}<br />Quantity: {QUANTITY}<br />Price: {PRICE}<br />Deposit: {DEPOSIT}<br />{SUPPLEMENTS}<br />{/OBJECTS}<br />Full Price: {FULLPRICE}<br />Full Deposit: {FULLDEPOSIT}<br /><br />Customer: {CUSTOMER}<br />Address: {ADDRESS}<br />Company: {COMPANY}<br />E-mail: {EMAIL}<br />Telephone: {TELEPHONE}<br />Fax: {FAX}<br /><br />Best regards.<br /><br />This E-mail was generated automatically.</p>', 1, 0, '0000-00-00 00:00:00');


INSERT INTO `#__booking_price` (`subject`, `value`, `deposit`, `rezervation_type`, `date_up`, `date_down`, `time_up`, `time_down`, `monday`, `tuesday`, `wednesday`, `thursday`, `friday`, `saturday`, `sunday`) VALUES
('%B2B%', 75, 5, '%R2R%', '2010-12-16', '2020-12-31', '00:00:00', '00:00:00', 1, 1, 1, 1, 1, 1, 1);

INSERT INTO `#__booking_reservation_type` (`id`, `subject`, `title`, `type`, `description`, `capacity_unit`, `time_unit`, `gap_time`, `special_offer`) VALUES
('%R1R%', '%B1B%', 'Daily', 2, '', 0, 0, 0, 0),
('%R2R%', '%B2B%', 'Daily', 2, '', 0, 0, 0, 0);

INSERT INTO `#__booking_subject` (`id`, `parent`, `template`, `title`, `alias`, `introtext`, `fulltext`, `total_capacity`, `state`, `ordering`, `publish_up`, `publish_down`, `access`, `hits`, `checked_out`, `checked_out_time`, `params`) VALUES
('%B1B%', 0, '%T1T%', 'Car', 'car', '', '', 0, 1, 1, '2010-12-16 08:43:00', '0000-00-00 00:00:00', 0, 0, 0, '0000-00-00 00:00:00', 'image=car1541.jpg'),
('%B2B%', '%B1B%', '%T1T%', 'Škoda Octavia Combi 2,0 TDI', 'skoda-octavia-combi-2-tdi-1', 'The Škoda Octavia Combi has enjoyed extraordinary popularity among  drivers for many years. Therefore, all the model''s versions were built  with regard to modern technology and customers’ wishes.<br /><br />The new  Škoda Octavia Combi offers not only all the benefits of the previous  generation, such as quality workmanship, timeless design and voluminous  luggage space, but also has new design elements, new technical elements  and a greater degree of safety. It can satisfy varied wishes and needs.  It is classic and modern. Still unique, yet different.\r\n<div id="ctl00_PlaceHolderMain_ctl04_ctl00_ctl00_divTextArea" class="andromedaCRColumnTextArea2">\r\n<div id="ctl00_PlaceHolderMain_ctl04_ctl00_ctl00_backgroundImageDisplay">\r\n<div id="ctl00_PlaceHolderMain_ctl04_ctl00_ctl00_divHeader" class="andromedaCRTextHeader2"><strong>Self-confident appearance</strong></div>\r\n<div id="ctl00_PlaceHolderMain_ctl04_ctl00_ctl00_divText" class="andromedaCRColumnText2 lexicon">\r\n<p><strong>The dynamic front </strong>with a dominant mask and newly-shaped headlights, the <strong>elegant lines </strong>of the chassis and the <strong>robust rear bumper </strong>with reflectors in an integrated spoiler give the new Octavia an even more confident appearance.<br /><br /><strong>The redesigned bumper </strong>optically  expands the front of the car and at the top smoothly follows on from  the newly designed grill on the opening for the radiator. When viewed  from the front, the <strong>front headlights with the word Octavia</strong> on a decorative inside bar will certainly attract attention.<br /><br /><strong>The new wing mirrors </strong>bring not only a greater useful surface, but also have a function that electronically folds them towards the body. The <strong>rear lights </strong>are equipped with new covers, but, after being lit up, retain the C-shape typical for Škoda.<br /><br /><strong>The palette of colours </strong>is supplemented by a pair of quite new metallic shades. The&nbsp;<strong>Aqua Blue </strong>and <strong>Arctic Green </strong>brilliantly reflect contemporary trends in the world of colours.</p>\r\n<div id="ctl00_PlaceHolderMain_ctl04_ctl00_ctl01_divTextArea" class="andromedaCRColumnTextArea2">\r\n<div id="ctl00_PlaceHolderMain_ctl04_ctl00_ctl01_backgroundImageDisplay">\r\n<div id="ctl00_PlaceHolderMain_ctl04_ctl00_ctl01_divHeader" class="andromedaCRTextHeader2"><strong>Advanced technology</strong></div>\r\n<div id="ctl00_PlaceHolderMain_ctl04_ctl00_ctl01_divText" class="andromedaCRColumnText2 lexicon">\r\n<p>The new Octavia Combi develops all the excellent properties of  its predecessor and gives space for the wide use of advanced  technologies.<br /><br /><strong>The progressive safety systems</strong>, but also the new elements, materials, quality of workmanship and arrangement of the spacious interior, together with <strong>great handling</strong>, enhance the feeling of comfort and safety.<br /><br />The new Octavia Combi now offers a quite <strong>new design for three- and four-spoke steering wheels</strong>, and the car <strong>radio</strong> and satellite <strong>navigation</strong> are also definitely worth mentioning. <strong>The instrument panel</strong>, with&nbsp;the Maxi DOT display and white under-lighting, provides perfectly legible data directly in the driver’s eye line.<br /><br />The new Octavia Combi can also boast a wide range of <strong>more powerful and economical engines</strong>, as well as a range of modern mechanical and automatic <strong>transmissions</strong>.</p>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>', '', 1, 1, 1, '2010-12-16 08:49:00', '0000-00-00 00:00:00', 0, 515, 62, '2011-01-13 12:31:16', 'image=octavia-combi-gray-anthracite.jpg\r\nimages=oc-combi-1.jpg;oc-combi-2.jpg;oc-combi-3.jpg;oc-combi-4-1.jpg;oc-combi-5.jpg;oc-combi-7.jpg;oc-combi-8.jpg;oc-combi-9.jpg;oc-combi-6.jpg');

INSERT INTO `#__booking_template` (`id`, `params`, `xml`) VALUES
('%T1T%', 'calendars=monthly', '<?xml version="1.0" encoding="utf-8"?>\n<form name="Car" id="'%T1T%'">\n	<fields name="params"><fieldset>\n		<field objects="1" object="1" name="1" type="checkbox" default="" label="Airbags" description="" searchable="1" filterable="1" icon="" />\n		<field objects="1" object="1" name="2" type="checkbox" default="" label="Radio" description="" searchable="1" filterable="1" icon="" />\n		<field objects="1" object="1" name="3" type="checkbox" default="" label="Air Conditioning" description="" searchable="1" filterable="1" icon="" />\n		<field objects="1" object="1" name="4" type="list" default="" label="Transmission" description="" searchable="1" filterable="1" icon="">\n			<option value="4-gear man." />\n			<option value="5-gear man." />\n			<option value="6-gear man." />\n			<option value="7-gear man." />\n			<option value="4-gear automatic" />\n			<option value="5-gear automatic" />\n			<option value="6-gear automatic" />\n			<option value="7-gear automatic" />\n		</field>\n		<field objects="1" object="1" name="5" type="list" default="" label="Passengers" description="" searchable="1" filterable="1" icon="">\n			<option value="1" />\n			<option value="2" />\n			<option value="3" />\n			<option value="4" />\n			<option value="5" />\n			<option value="6" />\n			<option value="7" />\n			<option value="8" />\n		</field>\n		<field objects="1" object="1" name="6" type="text" default="" label="Luggage Compartment" description="" searchable="1" filterable="0" icon="" />\n		<field objects="1" object="1" name="7" type="list" default="" label="Classification" description="" searchable="1" filterable="1" icon="">\n			<option value="Compact" />\n			<option value="Mid-size" />\n			<option value="Mid-size luxury" />\n			<option value="Full-size" />\n			<option value="Full-size luxury" />\n		</field>\n	</fieldset></fields>\n</form>');

CREATE TABLE `#__booking_template_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `1` tinyint(4) NOT NULL,
  `2` tinyint(4) NOT NULL,
  `3` tinyint(4) NOT NULL,
  `4` varchar(255) NOT NULL,
  `5` varchar(255) NOT NULL,
  `6` varchar(255) NOT NULL,
  `7` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `#__booking_template_1` (`id`, `1`, `2`, `3`, `4`, `5`, `6`, `7`) VALUES
('%B1B%', 0, 0, 0, '4-gear man.', '', '', ''),
('%B2B%', 1, 1, 1, '6-gear man.', '5', '605-1665 l', 'Mid-size');

INSERT INTO `#__booking_template_value` (`value`) VALUES
('Airbags'),
('Radio'),
('Air Conditioning'),
('Transmission'),
('4-degrees mech.'),
('5-degrees mech.'),
('6-degrees mech.'),
('7-degrees mech.'),
('4-degrees automat'),
('5-degrees automat'),
('6-degrees automat'),
('7-degrees automat'),
('4-gear man.'),
('5-gear man.'),
('6-gear man.'),
('7-gear man.'),
('4-gear automatic'),
('5-gear automatic'),
('6-gear automatic'),
('7-gear automatic'),
('Passengers'),
('Luggage Compartment (min/max)'),
('Luggage Compartment'),
('Spectators Capacity'),
('Dimensions (m)'),
('Beds'),
('Bathroom'),
('Yes'),
('No'),
('Toilet'),
('Minibar'),
('Television'),
('Restaurant (distance)'),
('Classification'),
('Compact'),
('Mid-size'),
('Full-size'),
('Mid-size luxury'),
('Full-size luxury');

INSERT INTO `#__booking_supplement` (`subject`, `title`, `description`, `type`, `options`, `empty`, `paid`, `price`) VALUES
('%B2B%', 'Color', '', 1, 'Black\r\nSilver\r\nWhite\r\nBlue', 0, 0, ''),
('%B2B%', 'GPS', '', 2, '', 0, 1, '30'),
('%B2B%', 'Seat', '', 1, 'Leather\r\nSuede', 1, 1, '20'),
('%B4B%', 'Color', '', 1, 'Black\r\nSilver\r\nWhite\r\nBlue', 0, 0, ''),
('%B2B%', 'Player', '', 1, 'CD\r\nCD/DVD', 1, 2, '10\r\n15');


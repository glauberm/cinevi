/**
 * @version		$Id$
 * @package		ARTIO Booking 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

DROP VIEW IF EXISTS `#__booking_template_value_view`;
CREATE VIEW `#__booking_template_value_view` AS
SELECT `value`.`id` AS `id`,`translation`.`language_id` AS `language`,`translation`.`value` AS `value` 
FROM (
	`#__booking_template_value` `value`
	LEFT JOIN `#__jf_content` `translation` ON ((`translation`.`reference_id` = `value`.`id`))
) 
WHERE 
(
	(`translation`.`reference_table` = 'booking_template_value') 
	AND 
	(`translation`.`reference_field` = 'value')
);
<?php 

/**
 * @version	$Id$
 * @package   	ARTIO Booking
 * @subpackage	modules/mod_booking_items
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license  	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link      	http://www.artio.net Official website
 */

defined('_JEXEC') or die;

class modBookingItemsHelper
{

	/**
	 * Get bookable item list
	 * @param JRegistry $params module configuration
	 * @return array
	 */
	public function getItems($params) 
	{
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$date = JFactory::getDate();
		$user = JFactory::getUser();
		
		$query->select('s.id, s.title, s.alias, s.introtext, s.params');
		$query->from('#__booking_subject AS s');
		$query->leftJoin('#__booking_subject AS k ON s.id = k.parent'); // check if item has children
		
		if ($params->get('show_price')) { // show minimal item price
			$query->select('MIN(p.value) AS price');
			$query->leftJoin('#__booking_price AS p ON p.subject = s.id');
			$query->group('s.id');
		}
		
		switch ($params->get('ordering', '1')) {
			case '0': // featured item first
				$query->order('s.featured DESC, s.title ASC');
				break;
			case '1': // newest item first
				$query->order('s.publish_up DESC, s.title ASC');
				break;
			default:
			case '2': // alphabet
				$query->order('s.title ASC');
				break;
			case '3': // random 
				$query->order('RAND()');
				break;
			case '4': // popular item first
				$query->order('s.hits DESC, s.title ASC');
				break;
		}
		
		$query->where('s.state = 1');
		$query->where('(s.publish_up = ' . $query->quote($db->getNullDate()) . ' OR s.publish_up <= ' . $query->quote($date->toSql()).')');
		$query->where('(s.publish_down = ' . $query->quote($db->getNullDate()) . ' OR s.publish_down >= ' . $query->quote($date->toSql()).')');
		$query->where('s.access IN (' . implode(', ', $user->getAuthorisedViewLevels()) . ')');
		$query->where('k.id IS NULL'); // not show parent item
		
		if ($params->get('parent_item')) // show some parent children only
			$query->where('s.parent = ' . (int) $params->get('parent_item'));
		
		$items = $db->setQuery($query, 0, $params->get('number_items', 5))->loadObjectList();
		
		foreach ($items as $item) {
			
			$item->link = JRoute::_('index.php?option=com_booking&view=subject&id=' . $item->id . ':' . $item->alias) . '#calendar';
			
			if ($params->get('show_desc', 1)) // crop introtext
				$item->introtext = AHtml::getReadmore($item->introtext, $params->get('desc_length', 100));
			
			if ($params->get('show_image', 1)) {
				$item->params = new JRegistry($item->params);
				$item->thumb = AImage::thumb(BookingHelper::getIPath($item->params->get('image')), $params->get('thumb_width', 80), $params->get('thumb_height', 80));
			}
		}
		
		return $items;
	}
	
	/**
	 * Import Booking framework
	 */
	public function import()
	{
		require_once(JPath::clean(JPATH_ADMINISTRATOR . '/components/com_booking/defines.php'));
		require_once(JPath::clean(JPATH_ADMINISTRATOR . '/components/com_booking/helpers/booking.php'));
		require_once(JPath::clean(JPATH_ADMINISTRATOR . '/components/com_booking/helpers/factory.php'));
		require_once(JPath::clean(JPATH_ADMINISTRATOR . '/components/com_booking/helpers/html.php'));
		require_once(JPath::clean(JPATH_ADMINISTRATOR . '/components/com_booking/helpers/image.php'));
		require_once(JPath::clean(JPATH_ADMINISTRATOR . '/components/com_booking/helpers/importer.php'));
		JHtml::stylesheet('modules/mod_booking_items/assets/css/general.css');
	}
}
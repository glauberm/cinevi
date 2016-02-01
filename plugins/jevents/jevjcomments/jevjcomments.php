
<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

// Lets load the language file
$lang = JFactory::getLanguage();
$lang->load("plg_jevjcomments", JPATH_ADMINISTRATOR);

class plgJEventsjevjcomments extends JPlugin
{


	function onDisplayCustomFields(&$row){

		//We set JEvents as default component to avoid breaking old installations
		if( in_array('jevents',$this->params->get('whenenabled',array('jevents'))) )
		{
			if (file_exists(JPATH_SITE.'/components/com_jcomments/jcomments.php')) {
				require_once(JPATH_SITE.'/components/com_jcomments/jcomments.php');
				$row->_jcomments = JComments::showComments($row->ev_id(), 'com_jevents', $row->content());
				return $row->_jcomments;
			}
		}
	}
	function onLocationDisplay(&$location){

		if( in_array('jevlocations',$this->params->get('whenenabled',array())) )
		{
			if (file_exists(JPATH_SITE.'/components/com_jcomments/jcomments.php')) {
				require_once(JPATH_SITE.'/components/com_jcomments/jcomments.php');
				$location->_jcomments = JComments::showComments($location->loc_id, 'com_jevlocations', $location->description);
				return $location->_jcomments;
			}
		}
	}
	static function fieldNameArray($layout='detail')
	{
		if ($layout != "detail")
			return array();
		$labels = array();
		$values = array();
		$labels[] = JText::_("JEV_JCOMMENTS", true);
		$values[] = "JEV_JCOMMENTS_ENABLE";

		$return = array();
		$return['group'] = JText::_("JEV_JCOMMENTS", true);
		$return['values'] = $values;
		$return['labels'] = $labels;

		return $return;

	}
        static function substitutefield($row, $code)
	{
		if ($code == "JEV_JCOMMENTS_ENABLE")
		{
			if(isset($row->_jcomments))
			{
				return $row->_jcomments;
			}
			else if(isset($row->_jevlocation->_jcomments))
			{
				return $row->_jevlocation->_jcomments;
			}
		}

	}

}

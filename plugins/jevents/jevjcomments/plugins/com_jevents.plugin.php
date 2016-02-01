<?php

class jc_com_jevents extends JCommentsPlugin 
{
	function getTitles($ids)
	{
		$db =  JCommentsFactory::getDBO();
		$db->setQuery( 'SELECT id, title FROM #__jevents_events WHERE id IN (' . implode(',', $ids) . ')' );
		return $db->loadObjectList('id');
	}

	function getObjectTitle($id)
	{
		$db =  JCommentsFactory::getDBO();
		$db->setQuery( 'SELECT summary, rp_id FROM #__jevents_repetition as rpt left join  #__jevents_vevdetail as det on det.evdet_id=rpt.eventdetail_id WHERE rp_id = ' . $id );
		$res = $db->loadResult();
		return $res;
	}

	function getObjectLink($id)
	{
		$link = JRoute::_( 'index.php?option=com_jevents&task=icalrepeat.detail&evid=' . $id );

		return $link;
	}

	function getObjectOwner($id) {

		$db =  JCommentsFactory::getDBO();
		$db->setQuery( 'SELECT created_by, rp_id FROM #__jevents_repetition as rpt left join  #__jevents_vevent as ev on ev.ev_id=rpt.eventid WHERE rp_id = ' . $id );
		$userid = $db->loadResult();
		
		return $userid;
	}

	function getCategories($filter = '') {

		$db =  JCommentsFactory::getDBO();

		$query = "SELECT c.id AS `value`, c.title AS `text`"
			. "\n FROM #__categories AS c"
			. (($filter != '') ? "\n WHERE c.id IN ( ".$filter." )" : '')
			. "\n ORDER BY c.title"
			;
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		return $rows;
	}
}

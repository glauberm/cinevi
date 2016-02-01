<?php

class jc_com_jevlocations extends JCommentsPlugin
{
	function getTitles($ids)
	{
		$db =  JCommentsFactory::getDBO();
		$db->setQuery( 'SELECT id, title FROM #__jevlocations WHERE id IN (' . implode(',', $ids) . ')' );
		return $db->loadObjectList('id');
	}

	function getObjectTitle($id)
	{
		$db =  JCommentsFactory::getDBO();
		$db->setQuery( 'SELECT id,title FROM #__jevlocations as loc WHERE loc.loc_id = ' . $id );
		$res = $db->loadResult();
		return $res;
	}

	function getObjectLink($id)
	{
		$link = JRoute::_( 'index.php?option=com_jevlocations&view=locations&layout=detail&loc_id=' . $id );

		return $link;
	}

	function getObjectOwner($id) {

		$db =  JCommentsFactory::getDBO();
		$db->setQuery( 'SELECT created_by FROM #__jevlocations as loc WHERE loc.loc_id = ' . $id );
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

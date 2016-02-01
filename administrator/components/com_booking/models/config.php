<?php

/**
 * Config model. Support for database operations.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  models
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

// import needed Joomla! libraries
jimport('joomla.application.component.model');
// import needed JoomLIB helpers
AImporter::helper('model');
AImporter::helper('artio-update');
AImporter::model('admin');

class BookingModelConfig extends AModel
{

    /**
     * Set all Joomla Super administrators as Booking administrators.
     * 
     * @return void
     */
    function setAdmins()
    {
    	$model = new BookingModelAdmin();
        //$this->_db->setQuery('SELECT `id` FROM `#__users` WHERE `usertype` = \'' . JUSER_SUPER_ADMINISTRATOR . '\'');
    	$q = "SELECT m.`user_id` FROM `#__user_usergroup_map` as m LEFT JOIN `#__usergroups` as g ON m.group_id = g.id WHERE g.`title` = '" . JUSER_SUPER_ADMINISTRATOR . "'";
    	$this->_db->setQuery($q);
        $model->setAsAdmin($this->_db->loadAssocList('user_id','user_id'));
    }
}

?>
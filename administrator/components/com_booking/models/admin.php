<?php

/**
 * Admin model. Support for database operations.
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

//import needed Joomla! libraries
jimport('joomla.application.component.model');
//import needed JoomLIB helpers
AImporter::helper('booking', 'model');
//import needed tables
AImporter::table('admin');

class BookingModelAdmin extends AModel
{
    
    /**
     * Main table
     * 
     * @var TableAdmin
     */
    var $_table;

    function __construct()
    {
        parent::__construct();
        $this->_table = $this->getTable('admin');
    }

    function setAsAdmin($cids)
    {
        foreach ($cids as $id) {
            if (! $this->_table->load($id)) {
                $query = 'INSERT INTO ' . $this->_table->getTableName() . ' (`id`) VALUES (' . $id . ')';
                $this->_db->setQuery($query);
                if (! $this->_db->query()) {
                    return false;
                }
            }
        }
        return true;
    }

    function setAsNoAdmin($cids)
    {
        foreach ($cids as $id) {
            if ($this->_table->load($id)) {
                if (! $this->_table->delete($id)) {
                    return false;
                }
            }
        }
        return true;
    }
}

?>
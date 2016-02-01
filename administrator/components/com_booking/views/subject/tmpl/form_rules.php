<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Subject rules edit form template
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage 	views
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */



?>
<div class="width-100">
	<fieldset class="adminform">
    	<legend><?php echo JText::_('JCONFIG_PERMISSIONS_LABEL'); ?></legend>
    	<?php echo $this->get('rules'); ?>
    </fieldset>
</div>
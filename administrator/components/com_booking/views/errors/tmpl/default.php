<?php

/**
 * Subject edit form template.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  views
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');


BookingHelper::setSubmenu(1);

JToolBarHelper::title(JText::_(COMPONENT_NAME).": ".JText::_('Errors'), 'alert');

//enable or disable debug mode button based on actual state(session)
if($this->debugActive < time())
	JToolBarHelper::apply('enableDebug','Enable Debug');
else
	JToolBarHelper::unpublish('disableDebug','Disable Debug');

JToolBarHelper::custom('refreshDatabase', 'refresh', 'refresh', 'REFRESH_DATABASE', false);

//button for deleting log files
if(!empty($this->files))
	JToolBarHelper::trash('deleteAllLogs','Delete Logs',false);

JToolBarHelper::divider();
if (JFactory::getUser()->authorise('core.admin', 'com_booking'))
	JToolBarHelper::preferences('com_booking');

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
    <?php 
        echo JHtml::_('tabs.start', 'tabone');
    
        echo JHtml::_('tabs.panel', JText::_('Error settings'), 'settings');
	?>
	<div class="ieHelper">&nbsp;</div>	
	<?php    
	    echo $this->loadTemplate('files');
	    
	    echo JHtml::_('tabs.end');
	?>
	
	<input type="hidden" name="option" value="<?php echo OPTION; ?>"/>
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_ERRORS; ?>"/>
	<input type="hidden" name="view" value="errors"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="lifetime" value="600"/>
	<?php echo JHTML::_('form.token'); ?>
</form>

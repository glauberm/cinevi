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

/* @var $this BookingViewSubject */

$this->subject->id ? AHtml::title('Item', 'object', $this->subject->title) : JToolBarHelper::title(JText::_('NEW_ITEM'), 'object');
JToolBarHelper::save();
JToolBarHelper::apply();

if ($this->subject->id) {
	JToolBarHelper::custom('copy','copy.png','copy_f2.png','Copy');
}
JToolBarHelper::cancel();

JHTML::_('behavior.modal');


$config = AFactory::getConfig();

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
    <?php 
        echo JHtml::_('tabs.start', 'tabone', $this->tabsParams);
        echo JHtml::_('tabs.panel', JText::_('DETAILS'), 'main');
	?>
	<div class="ieHelper">&nbsp;</div>
	<?php 	
		echo $this->loadTemplate('details');
	    echo JHtml::_('tabs.panel', JText::_('CAPACITY_OCCUPANCY'), 'capacity-occupancy');
	?>
	<div class="ieHelper">&nbsp;</div>
	<?php 	
		echo $this->loadTemplate('capacity_occupancy');
		echo JHtml::_('tabs.panel', JText::_('PROPERTIES'), 'properties');	    
	?>
	<div class="ieHelper">&nbsp;</div>	
	<?php    
	    echo $this->loadTemplate('properties');
	    echo JHtml::_('tabs.panel', JText::_('RESERVATION_SETTINGS'), 'reservation-types');
	?>
	<div class="ieHelper">&nbsp;</div>	
	<?php    
	    echo $this->loadTemplate('reservation-types');
	    echo JHtml::_('tabs.panel', JText::_('PRICES'), 'prices');
	?>
	<div class="ieHelper">&nbsp;</div>	
	<?php    
	    echo $this->loadTemplate('prices');
	    if ($config->usingPrices) {
	    	echo JHtml::_('tabs.panel', JText::_('DISCOUNTS'), 'tabdiscount');
	?>
			<div class="ieHelper">&nbsp;</div>
	<?php
	    	echo $this->loadTemplate('discount');
            if ($config->useProvisions) {
                echo JHtml::_('tabs.panel', JText::_('PROVISIONS'), 'tabprovisions');
	?>
                <div class="ieHelper">&nbsp;</div>
	<?php                
                echo $this->loadTemplate('provision');
            }
        }
	    echo JHtml::_('tabs.panel', JText::_('SUPPLEMENTS'), 'tabsupplements');
	?>
	<div class="ieHelper">&nbsp;</div>
	<?php    
	    echo $this->loadTemplate('supplements');
	    echo JHtml::_('tabs.panel', JText::_('GOOGLE_MAPS'), 'tabgoogle');
	?>
	<div class="ieHelper">&nbsp;</div>
	<?php    
	    echo $this->loadTemplate('google');
	    echo JHtml::_('tabs.panel', JText::_('JCONFIG_PERMISSIONS_LABEL'), 'tabrules');
	?>    
	<div class="ieHelper">&nbsp;</div>
	<?php
	    echo $this->loadTemplate('rules');
	    	    echo JHtml::_('tabs.end');
	?>
	<input type="hidden" name="option" value="<?php echo OPTION; ?>"/>
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_SUBJECT; ?>"/>
	<input type="hidden" name="boxchecked" value="1"/>
	<input type="hidden" name="cid[]" value="<?php echo $this->subject->id; ?>" id="cid"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="templateTask" value="<?php echo $this->subject->newTemplate ? 'saveAsNew' : ''; ?>"/>
	<input type="hidden" name="hits" value="<?php echo $this->subject->hits; ?>"/>
	<?php echo JHTML::_('form.token'); ?>
</form>
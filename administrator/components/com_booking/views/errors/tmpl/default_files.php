<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Subject-details edit form template
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  views
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */


?>
<div class="width-100">
	<fieldset class="adminform">
    	<legend><?php echo JText::_('FILES'); ?></legend>
    	<div class="col">
    		<table class="admintable width-100">
    		<?php if($this->debugActive){?>
    		<tr>
				<td class="key"><label><?php echo JText::_('Debug mode lifetime until'); ?></label></td>
				<td>
					<?php
						echo AHtml::date($this->debugActive, ADATE_FORMAT_LONG);
					?>
				</td>
			</tr>
			<?php } else {?>
			<tr>
				<td class="key"><label><?php echo JText::_('Debug mode disabled'); ?></label></td>
				<td></td>
			</tr>
			<?php }?>
    		<?php foreach($this->files as $file){ ?>
    			<tr>
    				<td class="key"><label><?php echo $file['file']; ?></label></td>
    				<td><?php echo $file['content']; ?></td>
    			</tr>
    			<?php } ?>
    		</table>
    	</div>
    	<div class="clr"></div>
    </fieldset>
</div>   
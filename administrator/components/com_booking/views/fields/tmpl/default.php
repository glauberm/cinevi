<?php

/**
 * View page to display and editing extra fileds of customer registration.
 *
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  views
 * @copyright	Copyright (C) 2012 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */


defined('_JEXEC') or die('Restricted access');

/* @var $this BookingViewFields */

ADocument::addDomreadyEvent('window.parent.document.getElementById("' . addslashes($this->fid) . '").value = "' . addslashes($this->value) . '";');
?>
<form id="fields" name="fields" method="post" action="<?php echo JRoute::_('index.php?option=com_booking&view=fields&tmpl=component', false); ?>">
	<?php if ($this->field) { ?>
        <div id="toolbar" class="btn-group">
			<button class="btn" id="save"><?php echo JText::_('FIELD_SAVE'); ?></button>
			<button class="btn" id="cancel"><?php echo JText::_('FIELD_CANCEL'); ?></button>
		</div>
		<table>
			<tr>
				<th><?php echo JText::_('FIELD_NAME'); ?></th>
				<td><input type="text" name="title" value="<?php echo $this->field['title']; ?>" /></td>
			</tr>
			<tr>
				<th><?php echo JText::_('FIELD_TYPE'); ?></th>
				<td>
					<?php echo JHtml::_('select.genericlist', $this->types, 'type', '', 'value', 'text', $this->field['type']); ?>
				</td>
			</tr>			
			<tr>
				<th><?php echo JText::_('FIELD_OPTIONS'); ?></th>
				<td><textarea rows="4" cols="40" name="options"><?php echo $this->field['options']; ?></textarea></td>
			</tr>			
			<tr>
				<th><?php echo JText::_('FIELD_REQUIRED'); ?></th>
				<td><?php echo JHTML::_('select.genericlist', array(JHTML::_('select.option', 1, JText::_('FIELD_OPTIONAL')), JHTML::_('select.option', 2, JText::_('FIELD_COMPULSORY'))), 'required', '', 'value', 'text', $this->field['required']); ?></td>
			</tr>
			<tr>
				<th><?php echo JText::_('FIELD_TEMPLATE'); ?></th>
				<td>
					<?php foreach ($this->templates as $template) { 
					        /* @var $template ATemplate */ 
					    $checked = in_array($template->id, $this->field['template']) || $this->id == -1; ?>
					    <input type="checkbox" name="template[]" id="template<?php echo $template->id; ?>" value="<?php echo $template->id; ?>" <?php if ($checked) { ?>checked="checked"<?php } ?> />
					    <label style="display: inline-block;" for="template<?php echo $template->id; ?>"><?php echo $template->name; ?></label>
					<?php } ?>
				</td>
			</tr>	
			<tr>
				<th><?php echo JText::_('FIELD_SPECIAL'); ?></th>
                <td><input type="checkbox" name="special" id="special" value="1" <?php if ($this->field['special']) { ?>checked="checked"<?php } ?> /></td>
			</tr>            
		</table>
		<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	<?php } else { ?>
		<div id="toolbar" class="btn-group">
			<button class="btn" id="add"><?php echo JText::_('FIELD_ADD'); ?></button>
			<?php if (count($this->fields)) { ?>
				<button class="btn" id="edit"><?php echo JText::_('FIELD_EDIT'); ?></button>
				<button class="btn" id="remove"><?php echo JText::_('FIELD_REMOVE'); ?></button>
			<?php } ?>
		</div>
		<?php if (count($this->fields)) { ?>
		<table>
			<tr>
				<th class="checkbox">&nbsp;</th>
				<th class="name"><?php echo JText::_('FIELD_NAME'); ?></th>
				<th class="required"><?php echo JText::_('FIELD_REQUIRED'); ?></th>				
				<th class="template"><?php echo JText::_('FIELD_TEMPLATE'); ?></th>
                <th class="type"><?php echo JText::_('FIELD_TYPE'); ?></th>
			</tr>
			<?php foreach ($this->fields as $id => $field) { ?>
				<tr>
					<td>
						<input type="checkbox" name="cid[]" value="<?php echo $id; ?>" />
					</td> 
					<td><?php echo $field['title']; ?></td>
					<td>
<?php
            if ($field['required'] == 1) {
                echo JText::_('FIELD_OPTIONAL');
            } elseif ($field['required'] == 2) {
                echo JText::_('FIELD_COMPULSORY');
            }
?>
					</td>
					<td>
					    <?php $templates = array(); 
                            $all = true;
                            foreach ($this->templates as $template) {
					            if (in_array($template->id, JArrayHelper::getValue($field, 'template', array(), 'array')))
					                $templates[] = $template->name;
                                else
                                    $all = false;
                            }
					        echo $all ? JText::_('JALL') : implode(', ', $templates);
					     ?>
					</td>
					<td>
<?php
            if (JArrayHelper::getValue($field, 'type') == 'radio') {
                echo JText::_('FIELD_RADIO');
            } elseif (JArrayHelper::getValue($field, 'type') == 'select') { ?>
                <span class="hasTip" title="<?php echo $field['title']; ?>::<?php echo nl2br(json_decode($field['options'])); ?>">
                    <?php echo JText::_('FIELD_SELECT'); ?>
                </span>
            <?php } else {
                echo JText::_('FIELD_STRING');
            }
?>
					</td>
				</tr>
			<?php } ?>
		</table>
	<?php }
}
?>
	<input type="hidden" name="op" value="" />
</form>
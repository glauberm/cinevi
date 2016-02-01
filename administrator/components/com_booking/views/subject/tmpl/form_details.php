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

$mask = $this->escape(JText::_('NEW_TEMPLATE_NAME'));
ADocument::addScriptPropertyDeclaration('TemplateNameMask', $mask);

?>
<div class="width-100">
	<fieldset class="adminform">
    	<legend><?php echo JText::_('ITEM'); ?></legend>
    	<div class="col width-50">
    		<table class="admintable width-100">
    			<tr>
    				<td class="key"><label for="title"><?php echo JText::_('TITLE'); ?><span class="star"> *</span></label></td>
    				<td><input class="inputbox required" style="width: 100%" type="text" name="title" id="title" value="<?php echo $this->subject->title; ?>" size="60" /></td>
    			</tr>
    			<tr>
    				<td class="key">
    					<label for="alias" class="hasTip" title="<?php echo $this->escape(JText::_('ALIAS')) . '::' . $this->escape(JText::_('ALIAS_INFO')); ?>"><?php echo JText::_('ALIAS'); ?></label>
    				</td>
    				<td><input class="inputbox" style="width: 100%" type="text" name="alias" id="alias" value="<?php echo $this->subject->alias; ?>" size="60" /></td>
    			</tr>
    			<tr>
    				<td class="key"><label><?php echo JText::_('TEMPLATE'); ?><span class="star"> *</span></label></td>
    				<td>
        				<table class="admintable" id="templateName" style="width: 100%"<?php echo $this->subject->newTemplate ? ' style="display: none"' : ''; ?>>
        					<tr>
        						<td valign="top">
        						<?php AImporter::helper('user');
        						if(!AUser::onlyOwner()):?>
        				            <a href="<?php echo JRoute::_(ARoute::edit(CONTROLLER_TEMPLATE, $this->subject->template)); ?>" title=""><?php echo $this->template->name; ?></a>
        				    	<?php else:?>
        				    		<?php echo $this->template->name; ?>
        				    	<?php endif;?>
        				    	</td>
        				    	<td align="right">
									<?php
										$bar = &JToolBar::getInstance('template');
										$bar->appendButton('ALink', 'move', 'Change', 'EditSubject.openChangeTemplate()');
										$bar->appendButton('ALink', 'new', 'New', 'EditSubject.openSaveAsNewTemplate()');
										if ($this->subject->template) {
											$bar->appendButton('ALink', 'edit', 'Rename', 'EditSubject.openRenameTemplate()');		
											$bar->appendButton('ALink', 'delete', 'Delete', 'EditSubject.openDeleteTemplate()');
										}
										if(!AUser::onlyOwner())
											echo $bar->render();
									
										/*
										$button = '<div class="btn-group pull-left">
													<button type="button" class="btn" title="' . JText::_('CHANGE') . '" onclick="EditSubject.openChangeTemplate(); return false;"><span class="icon-move"></span>Change</button>
													<button type="button" class="btn" title="' . JText::_('NEW') . '" onclick="EditSubject.openSaveAsNewTemplate(); return false;"><span class="icon-new"></span>New</button>';
										if ($this->subject->template) {
											$button = '<button type="button" class="btn" title="' . JText::_('RENAME') . '" onclick="EditSubject.openRenameTemplate(); return false;"><span class="icon-edit"></span>Rename</button>
													   <button type="button" class="btn" title="' . JText::_('DELETE') . '" onclick="EditSubject.openDeleteTemplate(); return false;"><span class="icon-delete"></span>Delete</button>';
										}
										$button .= '</div>';
										if(!AUser::onlyOwner())
											echo $button;
										*/
									?>
        				    	</td>
    						</tr>
    					</table>
						<fieldset class="adminform" id="saveAsNewTemplate" style="display: <?php echo $this->subject->newTemplate ? 'inline' : 'none'; ?>">
							<legend><?php echo JText::_('SAVE_AS_NEW_TEMPLATE'); ?></legend>
    				    	<input class="text_area" type="text" name="new_template_name" id="new_template_name" size="50" maxlength="255" value="<?php echo $mask; ?>" onclick="EditSubject.setTemplateNameContent()"/>
    				    	<div class="clr"></div>
    				    	<p class="tmpInfo"><?php echo JText::_('SAVE_AS_NEW_TEMPLATE_INFO'); ?></p>
    				    	<?php 
    				    		if ($this->subject->id || $this->templateHelper->haveTemplates()) {
									JToolBar::getInstance('saveasnewtemplate')->appendButton('ALink', 'cancel', 'Cancel', 'EditSubject.closeSaveAsNewTemplate()');
									echo JToolBar::getInstance('saveasnewtemplate')->render();
        				    	} 
        				    ?>
    				    </fieldset>
    				    <fieldset class="adminform" id="renameTemplate" style="display: none">
							<legend><?php echo JText::_('RENAME_TEMPLATE'); ?></legend>
    				    	<input class="text_area" type="text" name="template_rename" id="template_rename" size="50" maxlength="255" value="<?php echo $this->template->name; ?>"/>
    				    	<div class="clr"></div>
    				    	<p class="tmpInfo"><?php echo JText::_('RENAME_TEMPLATE_INFO'); ?></p>
    				    	<?php 
								JToolBar::getInstance('renametemplate')->appendButton('ALink', 'cancel', 'Cancel', 'EditSubject.closeRenameTemplate()');
								echo JToolBar::getInstance('renametemplate')->render();
							?>
    				    </fieldset>
    				    <fieldset class="adminform" id="changeTemplate" style="display: none;">
    				    	<legend><?php echo JText::_('CHANGE_TEMPLATE'); ?></legend>
    				    	<?php echo $this->templateHelper->getSelectBox('template', 'select template', $this->template->id, false); ?>
        				   	<?php 
        				   		JToolBar::getInstance('changetemplate')->appendButton('ALink', 'move', 'Change', 'EditSubject.changeTemplate()');
        				   		JToolBar::getInstance('changetemplate')->appendButton('ALink', 'cancel', 'Cancel', 'EditSubject.closeChangeTemplate()');
        				   		echo JToolBar::getInstance('changetemplate')->render();
        				   	?>
        				   	<div class="clr"></div>
        				   	<p class="tmpInfo"><?php echo JText::_('CHANGE_TEMPLATE_INFO'); ?></p>
    				    </fieldset>
    				    <fieldset class="adminform" id="deleteTemplate" style="display: none;">
    				    	<legend><?php echo JText::_('DELETE_TEMPLATE'); ?></legend>
        				    <?php 
        				   		JToolBar::getInstance('deletetemplate')->appendButton('ALink', 'delete', 'Delete', 'EditSubject.deleteTemplate()');
        				   		JToolBar::getInstance('deletetemplate')->appendButton('ALink', 'cancel', 'Cancel', 'EditSubject.closeDeleteTemplate()');
        				   		echo JToolBar::getInstance('deletetemplate')->render();
        				   	?>
        				    <div class="clr"></div>
        				    <p class="tmpInfo"><?php echo JText::_('DELETE_TEMPLATE_INFO'); ?></p>
    				    </fieldset>
    				</td>
    			</tr>
    			<tr>
    				<td class="key">
    					<label for="google_calendar" class="hasTip" title="<?php echo $this->escape(JText::_('GOOGLE_CALENDAR')) . '::' . $this->escape(JText::_('GOOGLE_CALENDAR_INFO')); ?>"><?php echo JText::_('GOOGLE_CALENDAR'); ?></label>
    				</td>
    				<td>
    					<select name="google_calendar" id="google_calendar">
    						<option value=""><?php echo JText::_('SELECT_GOOGLE_CALENDAR'); ?></option> 
    						<?php echo JHtml::_('select.options', $this->get('googlecalendarlist'), 'id', 'title', $this->subject->google_calendar); ?>
    					</select>
    				</td>
    			</tr>
    			<tr>
    				<td class="key">
    					<label for="parent" class="hasTip" title="<?php echo $this->escape(JText::_('PARENT')) . '::' . $this->escape(JText::_('PARENT_INFO')); ?>"><?php echo JText::_('PARENT'); ?><span class="star"> *</span></label>
    				</td>
    				<td><?php echo BookingHelper::getParentsSubjectSelectBox($this->subject->parent, $this->subject->id); ?></td>
    			</tr>
    			<tr>
    				<td class="key"><label>ID</label></td>
    				<td><?php echo $this->subject->id; ?></td>
    			</tr>
    			<tr>
    				<td class="key">
    					<label class="hasTip" title="<?php echo $this->escape(JText::_('FILES')) . '::' . $this->escape(JText::_('THESE_FILES_WILL_BE_SENT_TO_CUSTOMER_ALONG_WITH_RESERVATIN_E_MAIL_REMEMBER_TO_ENABLE_RESERVATION_E_MAILS_TO_CUSTOMER_IN_CONFIGURATION')); ?>"><?php echo JText::_('FILES'); ?></label>
    				</td>
    				<td>
    					<?php AImporter::tpl('files', $this->_layout, 'files'); ?>
    				</td>
    			</tr>
    		</table>	
    	</div>
    	<div class="col width-45" style="padding-left: 17px">
    		<table class="admintable width-100">
    			<tr>
    				<td class="key"><label><?php echo JText::_('PUBLISHED'); ?></label></td>
    				<td>
    					<fieldset class="radio btn-group">
    						<input type="radio" name="state" id="state0" value="<?php echo SUBJECT_STATE_UNPUBLISHED; ?>" class="inputRadio" <?php if ($this->subject->state == SUBJECT_STATE_UNPUBLISHED) { ?> checked="checked" <?php } ?> />
    						<label for="state0"><?php echo JText::_('JNO'); ?></label>
    						<input type="radio" name="state" id="state1" value="<?php echo SUBJECT_STATE_PUBLISHED; ?>" class="inputRadio" <?php if ($this->subject->state == SUBJECT_STATE_PUBLISHED) { ?> checked="checked" <?php } ?> />
    						<label for="state1"><?php echo JText::_('JYES'); ?></label>
    					</fieldset>
    				</td>
    			</tr>
    			<tr>
    				<td class="key"><label><?php echo JText::_('FEATURED'); ?></label></td>
    				<td>
    					<fieldset class="radio btn-group">
    						<input type="radio" name="featured" id="featured0" value="<?php echo SUBJECT_NOFEATURED; ?>" class="inputRadio" <?php if ($this->subject->featured == SUBJECT_NOFEATURED) { ?> checked="checked" <?php } ?> />
    						<label for="featured0"><?php echo JText::_('JNO'); ?></label>
    						<input type="radio" name="featured" id="featured1" value="<?php echo SUBJECT_FEATURED; ?>" class="inputRadio" <?php if ($this->subject->featured == SUBJECT_FEATURED) { ?> checked="checked" <?php } ?> />
    						<label for="featured1"><?php echo JText::_('JYES'); ?></label>
    					</fieldset>
    				</td>
    			</tr>
    			<tr>
    				<td class="key"><label for="publishUp"><?php echo JText::_('PUBLISH_UP'); ?></label></td>
    				<td>
    					<?php 
    						if (! ($publishUp = JString::trim($this->subject->publish_up)) && ! $this->subject->id)
    							$publishUp = AHtml::date('now', ADATE_FORMAT_LONG,0);
    						echo AHtml::getCalendar($publishUp, 'publish_up', 'publishUp', ADATE_FORMAT_LONG, ADATE_FORMAT_LONG_CAL, '',true, true); 
    					?>
    				</td>
    			</tr>
    			<tr>
					<td class="key">
						<label for="publishDown" class="hasTip" title="<?php echo $this->escape(JText::_('PUBLISH_INTERVAL')) . '::' . $this->escape(JText::_('PUBLISH_INTERVAL_INFO')); ?>"><?php echo JText::_('PUBLISH_DOWN'); ?></label>
					</td>
    				<td><?php echo AHtml::getCalendar($this->subject->publish_down, 'publish_down', 'publishDown', ADATE_FORMAT_LONG, ADATE_FORMAT_LONG_CAL); ?></td>    				
    			</tr>    					
    			<tr>
    				<td class="key">
    					<label for="TableSubject" class="hasTip" title="<?php echo $this->escape(JText::_('Ordering')) . '::' . $this->escape(JText::_('Ordering info')); ?>"><?php echo JText::_('Ordering'); ?></label>
    				</td>
    				<td><?php echo BookingHelper::getSubjectOrderingSelectBox($this->subject); ?></td>    				
    			</tr>
    			<tr>
    				<td class="key">
    					<label for="access" class="hasTip" title="<?php echo $this->escape(JText::_('ACCESS')) . '::' . $this->escape(JText::_('ACCESS_INFO')); ?>"><?php echo JText::_('ACCESS'); ?></label>
    				</td>
    				<td>
    					<?php
							if (ISJ16)
    							echo JHTML::_('access.level', 'access', $this->subject->access, '', false);
    						else
    							echo JHTML::_('list.accesslevel', $this->subject); 
    					?>
    				</td>    				
    			</tr>
    			<tr>
    				<td class="key">
    					<label class="hasTip" title="<?php echo $this->escape(JText::_('HITS')) . '::' . $this->escape(JText::_('HITS_INFO')); ?>"><?php echo JText::_('HITS'); ?></label>
    				</td>
    				<td>
    					<input class="text_area" type="text" name="hits_disabled" id="hits_disabled" size="5" disabled="disabled" value="<?php echo $this->subject->hits; ?>" />
    					<button id="hits_disabled_btn" onclick="return EditSubject.resetHits();"><?php echo JText::_('RESET'); ?></button>
    				</td>
    			</tr>    			
				<tr>
					<td class="key">
    					<label class="hasTip" title="<?php echo $this->escape(JText::_('META_KEYWORDS')) . '::' . $this->escape(JText::_('META_KEYWORDS_INFO')); ?>"><?php echo JText::_('META_KEYWORDS'); ?></label>
    				</td>
    				<td><textarea rows="2" cols="50" class="fullwidth" name="keywords" id="keywords"><?php echo $this->subject->keywords; ?></textarea></td>
				</tr>
				<tr>
					<td class="key">
    					<label class="hasTip" title="<?php echo $this->escape(JText::_('META_DESCRIPTION')) . '::' . $this->escape(JText::_('META_DESCRIPTION_INFO')); ?>"><?php echo JText::_('META_DESCRIPTION'); ?></label>
    				</td>
    				<td><textarea rows="5" cols="50" class="fullwidth" name="description" id="description"><?php echo $this->subject->description; ?></textarea></td>
				</tr>
    		</table>
    	</div>
    	<div class="clr"></div>
    </fieldset>
    <fieldset class="adminform">
    	<legend><?php echo JText::_('DESCRIPTION'); ?></legend>
    	<?php echo JFactory::getEditor()->display('text', $this->subject->text, '100%', 1, 1, 1); ?>
	</fieldset>    
    <fieldset class="adminform">
		<legend><?php echo JText::_('GALLERY'); ?></legend>
    	<?php AImporter::tpl('images', $this->_layout, 'images'); ?>
    </fieldset>
</div>   
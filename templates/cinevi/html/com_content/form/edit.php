<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.tabstate');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

// Create shortcut to parameters.
$params = $this->state->get('params');

// This checks if the editor config options have ever been saved. If they haven't they will fall back to the original settings.
$editoroptions = isset($params->show_publishing_options);

if (!$editoroptions)
{
	$params->show_urls_images_frontend = '0';
}

JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'article.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			" . $this->form->getField('articletext')->save() . "
			Joomla.submitform(task);
		}
	}
");
?>
<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
	<?php if ($params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_content&a_id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-vertical">
		<fieldset>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#editor" data-toggle="tab"><?php echo JText::_('COM_CONTENT_ARTICLE_CONTENT') ?></a></li>
				<?php if ($params->get('show_urls_images_frontend') ) : ?>
				<li><a href="#images" data-toggle="tab"><?php echo JText::_('COM_CONTENT_IMAGES_AND_URLS') ?></a></li>
				<?php endif; ?>
				<?php foreach ($this->form->getFieldsets('params') as $name => $fieldSet) : ?>
				<li><a href="#params-<?php echo $name; ?>" data-toggle="tab"><?php echo JText::_($fieldSet->label); ?></a></li>
				<?php endforeach; ?>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_CONTENT_PUBLISHING') ?></a></li>
			</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="editor">
					<?php echo $this->form->renderField('title'); ?>

					<?php echo $this->form->getInput('articletext'); ?>
				</div>
				<?php if ($params->get('show_urls_images_frontend')): ?>
				<div class="tab-pane" id="images">
					<?php echo $this->form->renderField('image_intro', 'images'); ?>
					<?php echo $this->form->renderField('image_intro_alt', 'images'); ?>
					<?php echo $this->form->renderField('image_intro_caption', 'images'); ?>
					<?php echo $this->form->renderField('float_intro', 'images'); ?>
					<?php echo $this->form->renderField('image_fulltext', 'images'); ?>
					<?php echo $this->form->renderField('image_fulltext_alt', 'images'); ?>
					<?php echo $this->form->renderField('image_fulltext_caption', 'images'); ?>
					<?php echo $this->form->renderField('float_fulltext', 'images'); ?>
				</div>
				<?php endif; ?>
				<?php foreach ($this->form->getFieldsets('params') as $name => $fieldSet) : ?>
					<div class="tab-pane" id="params-<?php echo $name; ?>">
						<?php foreach ($this->form->getFieldset($name) as $field) : ?>
							<?php echo $field->renderField(); ?>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
				<div class="tab-pane" id="publishing">
					<?php echo $this->form->renderField('catid'); ?>
					<?php echo $this->form->renderField('tags'); ?>

					<?php if (is_null($this->item->id)):?>
						<div class="form-group">
							<div class="control-label">
							</div>
							<div class="controls">
								<?php echo JText::_('COM_CONTENT_ORDERING'); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>

		<div class="text-left clearfix">
			<button type="button" class="btn btn-primary btn-lg" onclick="Joomla.submitbutton('article.save')">
				<span class="icon-ok"></span>&nbsp;&nbsp;<?php echo JText::_('JSAVE') ?>
			</button>

			<button type="button" class="btn btn-default btn-lg" onclick="Joomla.submitbutton('article.cancel')">
				<span class="icon-cancel"></span>&nbsp;&nbsp;<?php echo JText::_('JCANCEL') ?>
			</button>

			<?php if ($params->get('save_history', 0) && $this->item->id) : ?>
				<?php echo $this->form->getInput('contenthistory'); ?>
			<?php endif; ?>
		</div>

	</form>
</div>

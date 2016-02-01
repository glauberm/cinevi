<?php

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');

$this->form->reset( true );
$this->form->loadFile( dirname(__FILE__) . DS . "forms" . DS . "contact.xml");

if (isset($this->error)) : ?>
	<div class="contact-error alert alert-danger" role="alert">
		<?php echo $this->error; ?>
	</div>
<?php endif; ?>

<div class="contact-form">

	<p class="lead">Queremos ouvir de você, envie sua mensagem para o departamento! Todos os campos são obrigatórios.</p>

	<form id="contact-form" action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate">
		<fieldset>

			<div class="row">
				<div class="col-sm-8">
					<div class="form-group">
						<label class="control-label"><?php echo $this->form->getLabel('contact_name'); ?></label>
						<div class="controls"><?php echo $this->form->getInput('contact_name'); ?></div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-8">
					<div class="form-group">
						<label class="control-label"><?php echo $this->form->getLabel('contact_email'); ?></label>
						<div class="controls"><?php echo $this->form->getInput('contact_email'); ?></div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-10">
					<div class="form-group">
						<label class="control-label"><?php echo $this->form->getLabel('contact_subject'); ?></label>
						<div class="controls"><?php echo $this->form->getInput('contact_subject'); ?></div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label"><?php echo $this->form->getLabel('contact_message'); ?></label>
				<div class="controls"><?php echo $this->form->getInput('contact_message'); ?></div>
			</div>
			<?php if ($this->params->get('show_email_copy')) : ?>
				<div class="form-group">
					<label class="control-label"><?php echo $this->form->getLabel('contact_email_copy'); ?></label>
					<div class="controls"><?php echo $this->form->getInput('contact_email_copy'); ?></div>
				</div>
			<?php endif; ?>
			<?php // Dynamically load any additional fields from plugins. ?>
			<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
				<?php if ($fieldset->name != 'contact') : ?>
					<?php $fields = $this->form->getFieldset($fieldset->name); ?>
					<?php foreach ($fields as $field) : ?>
						<div class="form-group">
							<?php if ($field->hidden) : ?>
								<div class="controls">
									<?php echo $field->input; ?>
								</div>
							<?php else: ?>
								<label class="control-label">
									<?php echo $field->label; ?>
									<?php if (!$field->required && $field->type != "Spacer") : ?>
										<span class="optional"><?php echo JText::_('COM_CONTACT_OPTIONAL'); ?></span>
									<?php endif; ?>
								</label>
								<div class="controls"><?php echo $field->input; ?></div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endforeach; ?>

			<hr>

			<div class="form-actions">
				<button class="btn btn-primary btn-lg validate" type="submit"><?php echo JText::_('COM_CONTACT_CONTACT_SEND'); ?></button>
				<input type="hidden" name="option" value="com_contact" />
				<input type="hidden" name="task" value="contact.submit" />
				<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
				<input type="hidden" name="id" value="<?php echo $this->contact->slug; ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</fieldset>
	</form>
</div>

<?php

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
?>

<div id="sair">
<form action="<?php echo JRoute::_(htmlspecialchars(JUri::getInstance()->toString()), true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-inline">
<div class="clearfix">
<?php if ($params->get('greeting')) : ?>
	<div class="login-greeting pull-left">
		<i class="icon icon-user"></i>
	<?php if ($params->get('name') == 0) : {
		echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('name')));
	} else : {
		echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('username')));
	} endif; ?>
	</div>
<?php endif; ?>
	<div class="logout-button pull-right">
		<input type="submit" name="Submit" class="btn btn-link btn-xs" value="<?php echo JText::_('JLOGOUT'); ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</div>
</form>
</div>

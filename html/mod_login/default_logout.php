<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_users/helpers/route.php';

JHtml::_('behavior.keepalive');
?>
<div class="userdata-wrap">
	<form action="<?php echo JRoute::_(htmlspecialchars(JUri::getInstance()->toString(), ENT_COMPAT, 'UTF-8'), true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-inline">
		<div class="userdata">
			<div class="login-icon-wrap">
				<div class="login-icon">
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=profile&Itemid=' . UsersHelperRoute::getProfileRoute()); ?>">
						<span class="icon-user" title="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>"></span>
						<?php if ($params->get('greeting')) : ?>
							<span class="text-user login-greeting">
								<?php if ($params->get('name') == 0) : ?>
									<?php echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('name'), ENT_COMPAT, 'UTF-8')); ?>
								<?php else : ?>
									<?php echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('username'), ENT_COMPAT, 'UTF-8')); ?>
								<?php endif; ?>
							</span>
						<?php endif; ?>
					</a>
				</div>
				<div class="login-data">
					<a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_users&view=profile&Itemid=' . UsersHelperRoute::getProfileRoute()); ?>">Meu Perfil</a>
					<input type="submit" name="Submit" class="btn btn-inverse" value="<?php echo JText::_('JLOGOUT'); ?>" />
					<input type="hidden" name="option" value="com_users" />
					<input type="hidden" name="task" value="user.logout" />
					<input type="hidden" name="return" value="<?php echo $return; ?>" />
					<?php echo JHtml::_('form.token'); ?>
				</div>
			</div>
		</div>
	</form>
</div>

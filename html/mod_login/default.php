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
JHtml::_('bootstrap.tooltip');

?>
<div class="userdata-wrap">
	<form action="<?php echo JRoute::_(htmlspecialchars(JUri::getInstance()->toString()), true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-inline">
		<div class="userdata">
			<div class="login-icon-wrap">
				<div class="login-icon">
					<a class="no-style" href="<?php echo JRoute::_('index.php?option=com_users&view=login')?>">
						<span class="icon-users" title="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>"></span>
						<?php if ($params->get('pretext')) : ?>
							<span class="text-user"><?php echo $params->get('pretext'); ?></span>
						<?php endif; ?>
					</a>
				</div>
				<div class="login-data">

					<?php if (!$params->get('usetext')) : ?>
						<div class="input-prepend">
							<span class="add-on">
								<span class="icon-user hasTooltip" title="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>"></span>
								<label for="modlgn-username" class="element-invisible"><?php echo JText::_('MOD_LOGIN_VALUE_USERNAME'); ?></label>
							</span>
							<input id="modlgn-username" type="text" name="username" class="input-small" tabindex="0" size="18" placeholder="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>" />
						</div>
					<?php else: ?>
						<label for="modlgn-username"><?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?></label>
						<input id="modlgn-username" type="text" name="username" class="input-small" tabindex="0" size="18" placeholder="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>" />
					<?php endif; ?>
					<?php if (!$params->get('usetext')) : ?>
						<div class="input-prepend">
							<span class="add-on">
								<span class="icon-lock hasTooltip" title="<?php echo JText::_('JGLOBAL_PASSWORD') ?>">
								</span>
									<label for="modlgn-passwd" class="element-invisible"><?php echo JText::_('JGLOBAL_PASSWORD'); ?>
								</label>
							</span>
							<input id="modlgn-passwd" type="password" name="password" class="input-small" tabindex="0" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
						</div>
					<?php else: ?>
						<label for="modlgn-passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
						<input id="modlgn-passwd" type="password" name="password" class="input-small" tabindex="0" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
					<?php endif; ?>
					<?php if (count($twofactormethods) > 1): ?>
						<?php if (!$params->get('usetext')) : ?>
							<div class="input-prepend input-append">
								<span class="add-on">
									<span class="icon-star hasTooltip" title="<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>">
									</span>
										<label for="modlgn-secretkey" class="element-invisible"><?php echo JText::_('JGLOBAL_SECRETKEY'); ?>
									</label>
								</span>
								<input id="modlgn-secretkey" autocomplete="off" type="text" name="secretkey" class="input-small" tabindex="0" size="18" placeholder="<?php echo JText::_('JGLOBAL_SECRETKEY') ?>" />
								<span class="btn width-auto hasTooltip" title="<?php echo JText::_('JGLOBAL_SECRETKEY_HELP'); ?>">
									<span class="icon-help"></span>
								</span>
							</div>
						<?php else: ?>
							<label for="modlgn-secretkey"><?php echo JText::_('JGLOBAL_SECRETKEY') ?></label>
							<input id="modlgn-secretkey" autocomplete="off" type="text" name="secretkey" class="input-small" tabindex="0" size="18" placeholder="<?php echo JText::_('JGLOBAL_SECRETKEY') ?>" />
							<span class="btn width-auto hasTooltip" title="<?php echo JText::_('JGLOBAL_SECRETKEY_HELP'); ?>">
								<span class="icon-help"></span>
							</span>
						<?php endif; ?>
					<?php endif; ?>
					<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
						<label for="modlgn-remember" class="checkbox"><?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?><input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/></label>
					<?php endif; ?>
					<button type="submit" tabindex="0" name="Submit" class="btn btn-inverse"><?php echo JText::_('JLOGIN') ?></button>

					<?php $usersConfig = JComponentHelper::getParams('com_users'); ?>
					<ul class="unstyled">
						<?php if ($usersConfig->get('allowUserRegistration')) : ?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration&Itemid=' . UsersHelperRoute::getRegistrationRoute()); ?>">
								<?php echo JText::_('MOD_LOGIN_REGISTER'); ?></a>
							</li>
						<?php endif; ?>
						<li>
							<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind&Itemid=' . UsersHelperRoute::getRemindRoute()); ?>">
							<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
						</li>
						<li>
							<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset&Itemid=' . UsersHelperRoute::getResetRoute()); ?>">
							<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
						</li>
					</ul>
					<input type="hidden" name="option" value="com_users" />
					<input type="hidden" name="task" value="user.login" />
					<input type="hidden" name="return" value="<?php echo $return; ?>" />
					<?php echo JHtml::_('form.token'); ?>
					<?php if ($params->get('posttext')) : ?>
						<div class="posttext">
							<p><?php echo $params->get('posttext'); ?></p>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</form>
</div>

<?php

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_users/helpers/route.php';

$usersConfig = JComponentHelper::getParams('com_users');
?>

<span>
	<i class="icon icon-user"></i>Olá, Visitante! Faça <a href="<?php echo JRoute::_('index.php?option=com_users&view=login&Itemid=' . UsersHelperRoute::getLoginRoute()); ?>">login</a> ou <a href="<?php echo JRoute::_('index.php?option=com_users&view=registration&Itemid=' . UsersHelperRoute::getRegistrationRoute()); ?>">cadastre-se</a>.
</span>

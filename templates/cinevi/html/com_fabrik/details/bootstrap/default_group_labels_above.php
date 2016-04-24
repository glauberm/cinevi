<?php
/**
 * Bootstrap Details Template
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2015 fabrikar.com - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.1
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$element = $this->element;?>
<div class="form-group <?php echo $element->containerClass .' '. $element->span; ?>">
	<label class="fabrikLabel">
		<?php echo $element->label_raw;?>
	</label>

	<?php if ($this->tipLocation == 'above') : ?>
		<span class=""><?php echo $element->tipAbove ?></span>
	<?php endif ?>

	<div class="fabrikElement form-control-static">
		<?php echo $element->element;?>
	</div>

	<?php if ($this->tipLocation == 'side') : ?>
		<span class=""><?php echo $element->tipSide ?></span>
	<?php endif ?>

	<?php if ($this->tipLocation == 'below') :?>
		<span class=""><?php echo $element->tipBelow ?></span>
	<?php endif ?>
</div><!-- end control-group -->

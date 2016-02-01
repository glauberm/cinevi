<?php
 
/**
 * @version	$Id$
 * @package   	ARTIO Booking
 * @subpackage	modules/mod_booking_items
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license  	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link      	http://www.artio.net Official website
 */

defined('_JEXEC') or die();

?>

<ul class="mod_booking_items<?php echo $moduleclass_sfx; ?>">
	<?php for ($i = 0; $i < count($items); $i+= $params->get('number_columns')) { ?>
		<li>
			<?php for ($j = $i; $j < $i + $params->get('number_columns'); $j++) {
					if (!empty($items[$j])) { ?>
						<div class="item col<?php echo $params->get('number_columns'); ?>">
							<?php $item = $items[$j];
					      		require JModuleHelper::getLayoutPath('mod_booking_items', '_item'); ?>
							<div class="wrap"></div>
						</div>
			<?php }
			} ?>
			<div class="wrap"></div>
		</li>
	<?php } ?>
</ul>
<?php
/**
 * @version      $Id$
 * @package      ARTIO Booking
 * @subpackage   views
 * @copyright   Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author         ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */
defined('_JEXEC') or die;

$config = AFactory::getConfig();

if (!empty($config->rsExtra)) {
    foreach ($config->rsExtra as $i => $field) {
        ?>
        <tr class="row<?php echo ($i + 1) % 2; ?>">
            <td>{<?php echo JString::strtoupper($field['title']); ?>}</td>
            <td><?php echo $field['title']; ?></td>
        </tr>
        <?php
    }
}


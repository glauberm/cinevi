<?php 

/**
 * Subject detail properties template.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  views
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

$files = BookingHelper::getSubjectFiles($this->subject,array('onlyShow'=>true));

if (count($files)){ ?>
<h2 class="filesSubtitle"><?php echo JText::_('FILES'); ?></h2>

<?php 

foreach ($files as &$file)
	$file = JHTML::link($file->url,$file->filename,'target="_blank"');

echo implode('<br>',$files);

} ?>

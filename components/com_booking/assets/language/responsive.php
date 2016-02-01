<?php

/**
 * Language constants for site calendars
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  assets 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

$languages['LGJQueryIsNotLoaded'] = 'THIS_WEBSITE_NEEDS_JQUERY_FOR_RIGHT_FUNCTIONALITY';

$document = &JFactory::getDocument();
/* @var $document JDocument */

AImporter::helper('document');
ADocument::addLGScriptDeclaration($languages);

?>
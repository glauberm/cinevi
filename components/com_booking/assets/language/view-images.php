<?php

/**
 * Language constants for edit subject form javascript
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

$languages['LGAreYouSure'] = 'ARE_YOU_SURE';
$languages['LGAddDirname'] = 'ADD_DIRECTORY_NAME';
$languages['LGSelectToDefault'] = 'SELECT_IMAGE_WHICH_YOU_WANT_TO_SELECT_AS_DEFAULT';

AImporter::helper('document');
ADocument::addLGScriptDeclaration($languages);

?>
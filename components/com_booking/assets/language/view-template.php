<?php

/**
 * Language constants for edit template form javascript
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

//valid form before submit
$languages['LGErrAddTemplateName'] = 'ADD_TEMPLATE_NAME';

AImporter::helper('document');
ADocument::addLGScriptDeclaration($languages);

?>
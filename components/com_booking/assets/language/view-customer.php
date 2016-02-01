<?php

/**
 * Language constants for edit customer form javascript
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
$languages['LGErrAddCustomerFirstName'] = 'ADD_FIRST_NAME';
$languages['LGErrAddCustomerSurname'] = 'ADD_SURNAME';
$languages['LGErrAddCustomerUsername'] = 'ADD_USERNAME';
$languages['LGErrAddCustomerEmail'] = 'ADD_EMAIL';
$languages['LGErrAddValidCustomerEmail'] = 'ADD_VALID_EMAIL';
$languages['LGErrAddCustomerTelephone'] = 'ADD_TELEPHONE';
$languages['LGErrPasswordDoNotMatch'] = 'PASSWORDS_DO_NOT_MATCH';
$languages['LGErrAddPassword'] = 'ADD_PASSWORD';
$languages['LGErrSelectExistingUser'] = 'SELECT_EXISTING_USER';
$languages['LGErrSelectExistingOrAddNewUser'] = 'SELECT_EXISTING_OR_ADD_NEW_USER';

AImporter::helper('document');
ADocument::addLGScriptDeclaration($languages);

?>
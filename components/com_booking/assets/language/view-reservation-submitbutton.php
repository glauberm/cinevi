<?php

/**
 * Language constants for edit reservation form javascript.
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
$languages['LGErrAddReservationFirstName'] = 'ADD_FIRST_NAME';
$languages['LGErrAddReservationSurname'] = 'ADD_SURNAME';
$languages['LGErrAddReservationEmail'] = 'ADD_EMAIL';
$languages['LGErrAddReservationValidEmail'] = 'ADD_VALID_EMAIL';
$languages['LGErrAddReservationTelephone'] = 'ADD_TELEPHONE';
$languages['LGErrAddReservationCapacity'] = 'ADD_CAPACITY';
$languages['LGErrReservationCapacityMustBeInteger'] = 'CAPACITY_VALUE_MUST_BE_AN_INTEGER';
$languages['LGErrAddReservationCustomerRegistration'] = 'ADD_CUSTOMER_REGISTRATION';
$languages['LGErrAddReservationSubject'] = 'ADD_SUBJECT';
$languages['LGErrAddReservationSubjectTitle'] = 'ADD_SUBJECT_TITLE';
$languages['LGErrAddReservationFrom'] = 'ADD_FROM_DATE';
$languages['LGErrAddReservationTo'] = 'ADD_TO_DATE';
$languages['LGErrReservationInvalidInterval'] = 'RESERVATION_INVALID_INTERVAL';
$languages['LGErrAddReservationPrice'] = 'ADD_PRICE';
$languages['LGErrAddCaptcha'] = 'ADD_CAPTCHA';
$languages['LGErrAddRType'] = 'ADD_RTYPE';
$languages['LGErrAcceptContract'] = 'ACCEPT_TERMS_OF_CONTRACT';
$languages['LGErrAcceptPrivacy'] = 'ACCEPT_TERMS_OF_PRIVACY';
$languages['LGAreYouSure'] = 'ARE_YOU_SURE';
$languages['LGErrDisallowRemoveOnlyItem'] = 'DISALLOW_REMOVE_ONLY_ITEM';
$languages['URLRoot'] = JRoute::_('index.php');

AImporter::helper('document');
ADocument::addLGScriptDeclaration($languages);

?>
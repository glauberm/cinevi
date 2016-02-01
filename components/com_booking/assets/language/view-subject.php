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

$config = AFactory::getConfig();

$languages['LGAreYouSure'] = 'Are you sure?';

//valid form before submit
$languages['LGErrAddSubjectTitle'] = 'ADD_SUBJECT_TITLE';
$languages['LGErrAddSubjectTemplate'] = 'ADD_SUBJECT_TEMPLATE';
$languages['LGErrAddTemplateName'] = 'ADD_TEMPLATE_NAME';
$languages['LGErrTotalCapacityNoNumeric'] = 'TOTAL_CAPACITY_MUST_BE_NUMERIC';
$languages['LGErrMinimumCapacityInvalid'] = 'CAPACITY_MINIMUM_INVALID';
$languages['LGErrOccupancyMinimumInvalid'] = 'OCCUPANCY_MINIMUM_INVALID';
$languages['LGErrOccupancyIntervalInvalid'] = 'OCCUPANCY_INTERVAL_INVALID';
$languages['LGErrOccupancyMaximumInvalid'] = 'OCCUPANCY_MAXIMUM_INVALID';
$languages['LGErrOccupancyMissingType'] = 'OCCUPANCY_MISSING_TYPE';
$languages['LGErrAddReservationTypesTitles'] = 'ADD_RESERVATION_TYPES_TITLES';
$languages['LGErrSelectsDefaultTypesReservationTypes'] = 'SELECT_IF_RESERVATION_TYPES_ARE_HOURLY_OR_DAILY';
$languages['LGErrAddTimeUnit'] = 'ADD_TIME_UNIT_VALUE';
$languages['LGErrAddPricesValues'] = 'ADD_PRICES_VALUES';
$languages['LGErrSelectPricesReservationTypes'] = 'SELECT_PRICES_RESERVATION_TYPES';
$languages['LGErrAddPricesDates'] = 'ADD_PRICES_DATE_RANGE';
$languages['LGErrAddPricesTimes'] = 'ADD_PRICES_TIME_RANGE';
$languages['LGErrSelfAsParent'] = 'YOU_CANNOT_SET_SUBJECT_AS_SELF_PARENT';
$languages['LGErrSelectItems'] = 'SELECT_ITEMS_WHICH_YOU_WANT_REMOVE_BY_CHECK_CHECKBOX_ON_ROW_BEGIN';
$languages['LGErrAddSubjectMinLimit'] = 'ADD_SUBJECT_RESERVATION_MIN_INTERVAL';
$languages['LGErrAddRLimit'] = 'ADD_RESERVATION_LIMIT';
$languages['LGErrAddSupplementsTitles'] = 'ADD_SUPPLEMENTS_TITLES';
$languages['LGErrSelectSupplementsTypes'] = 'SELECT_SUPPLEMENTS_TYPES';
$languages['LGErrSelectSupplementsOptions'] = 'ADD_SUPPLEMENTS_OPTIONS';
$languages['LGErrAddSupplementsPrice'] = 'ADD_SUPPLEMENTS_PRICES';
$languages['LGErrPublishIntervalInvalid'] = 'PUBLISH_INTERVAL_INVALID';
$languages['LGErrPriceDateIntervalInvalid'] = 'PRICE_DATE_INTERVAL_INVALID';
$languages['LGErrPriceTimeIntervalInvalid'] = 'PRICE_TIME_INTERVAL_INVALID';
$languages['LGErrAddFixedScheduleFromTo'] = 'ADD_FIXED_SCHEDULE_FROM_AND_TO';
$languages['LGErrAddNightsBookingCheckInCheckOut'] = 'ADD_NIGHTS_BOOKING_CHECK_IN_AND_CHECK_OUT';
$languages['LGErrPriceTimeRangeOverMidnightInvalid'] = 'PRICE_TIME_RANGE_OVER_MIDNIGHT_INVALID';
$languages['LGErrAddCancelTime'] = 'ADD_CANCEL_TIME';

ADocument::addDomreadyEvent('EditSubject.usePricing = ' . $config->usingPrices . ';');

AImporter::helper('document');
ADocument::addLGScriptDeclaration($languages);

?>

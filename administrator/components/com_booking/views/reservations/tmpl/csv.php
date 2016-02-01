<?php

/**
 * Export reservations into CSV file and serve file to client
 *
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	models
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */
defined('_JEXEC') or die('Restricted access');

/* @var $this BookingViewReservations */

$app = JFactory::getApplication();
$config = AFactory::getConfig();

$tmp = JPath::clean($app->getCfg('tmp_path') . '/' . uniqid());

$handle = fopen($tmp, 'w');

if ($handle === false)
    $app->redirect(ARoute::browse(CONTROLLER_RESERVATION), JText::sprintf('UNABLE_WRITE_CSV', $tmp), 'notice');

$head[] = JText::_('RES_NUM');
$head[] = JText::_('CREATED');
$head[] = JText::_('JGLOBAL_FIELD_CREATED_BY_LABEL');
$head[] = JText::_('MODIFIED');
$head[] = JText::_('JGLOBAL_FIELD_MODIFIED_BY_LABEL');
$head[] = JText::_('CUSTOMER');
$head[] = JText::_('COMPANY');
$head[] = JText::_('COMPANY_ID');
$head[] = JText::_('VAT_ID');

$fields = array();
foreach ($this->items as $reservation) {
    $reservation->fields = unserialize($reservation->fields);
    foreach ($reservation->fields as $name => $data) {
        $fields[$name] = $data['title'];
    }
}

$head = array_merge($head, $fields);

$head[] = JText::_('ADDRESS');
$head[] = JText::_('PHONE');
$head[] = JText::_('EMAIL');
$head[] = JText::_('ITEM');
$head[] = JText::_('CAPACITY');
$head[] = JText::_('OCCUPANCY');
$head[] = JText::_('FROM');
$head[] = JText::_('TO');
if ($config->usingPrices != PRICES_NONE)
    $head[] = JText::_('PRICE');
if ($config->usingPrices == PRICES_WITH_DEPOSIT)
    $head[] = JText::_('DEPOSIT');
if ($config->usingPrices)
    $head[] = JText::_('PAYMENT_STATUS');
$head[] = JText::_('RESERVATION_STATUS');

$supplementList = array();
foreach ($this->reservedSupplements as $reservationId => $supplements) {
    foreach ($supplements as $supplement) {
        $supplementList[] = $supplement->title;
    }
}
$supplementList = array_unique($supplementList);
$head = array_merge($head, $supplementList);

fputcsv($handle, $head);

foreach ($this->items as $i => $reservation) {
    /* @var $reservation TableReservation */
    foreach ($this->reservedItems[$reservation->id] as $j => $reservedItem) {
        /* @var $reservedItem TableReservationItems */
        TableReservationItems::display($reservedItem);
        $row = array();
        $row[] = $reservation->id;
        $row[] = AHtml::date($reservation->created, ADATE_FORMAT_LONG);
        $row[] = $reservation->creator ? $reservation->creator : JText::_('UNREGISTERED_CUSTOMER');
        $row[] = AHtml::date($reservation->modified, ADATE_FORMAT_LONG);
        if ($reservation->modified)
            $row[] = $reservation->modifier ? $reservation->modifier : JText::_('UNREGISTERED_CUSTOMER');
        else
            $row[] = '';
        $row[] = BookingHelper::formatName($reservation);
        $row[] = $reservation->company;
        $row[] = $reservation->company_id;
        $row[] = $reservation->vat_id;

        foreach ($fields as $name => $title) {
            if (isset($reservation->fields[$name]['value'])) {
                $row[] = JText::_($reservation->fields[$name]['value']);
            } else {
                $row[] = '';
            }
        }

        $row[] = BookingHelper::formatAddress($reservation);
        $row[] = $reservation->telephone;
        $row[] = $reservation->email;
        $row[] = $reservedItem->subject_title;
        $row[] = $reservedItem->capacity;
        $occ = array();
        foreach ($reservedItem->occupancy as $oitem)
            $occ[] = JArrayHelper::getValue($oitem, 'title') . ': ' . JArrayHelper::getValue($oitem, 'count');
        $row[] = implode("\n", $occ);
        if ($reservedItem->rtype == RESERVATION_TYPE_PERIOD) {
            $row[] = AHtml::showRecurenceTimeframe($reservedItem) . ' ' . AHtml::showRecurencePattern($reservedItem);
            $row[] = '';
        } else {
            $row[] = AHtml::date($reservedItem->from, ADATE_FORMAT_NORMAL, 0) . ' ' . AHtml::date($reservedItem->from, ATIME_FORMAT_SHORT, 0);
            $row[] = AHtml::date($reservedItem->to, ADATE_FORMAT_NORMAL, 0) . ' ' . AHtml::date($reservedItem->to, ATIME_FORMAT_SHORT, 0);
        }
        if ($config->usingPrices)
            $row[] = JFilterOutput::cleanText(BookingHelper::displayPrice($reservation->reservationFullPrice));
        if ($config->usingPrices == PRICES_WITH_DEPOSIT)
            $row[] = JFilterOutput::cleanText(BookingHelper::displayPrice($reservation->reservationFullDeposit));
        if ($config->usingPrices)
            $row[] = BookingHelper::showReservationPaymentStateLabel($reservation->paid);
        $row[] = BookingHelper::showReservationStateLabel($reservation->state);

        foreach ($supplementList as $supplementName) {
            $scell = '';
            foreach ($this->reservedSupplements[$reservedItem->id] as $supplement) {
                if ($supplement->title == $supplementName && $supplement->value && $supplement->capacity) {
                    if ($supplement->type == SUPPLEMENT_TYPE_YESNO) {
                        $scell = $supplement->capacity;
                    } elseif ($supplement->type == SUPPLEMENT_TYPE_LIST) {
                        $scell = $supplement->value . ($supplement->capacity > 1 ? ' (' . $supplement->capacity . ')' : '');
                    }
                }
            }
            $row[] = $scell;
        }

        fputcsv($handle, $row);
    }
}

fclose($handle);

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Transfer-Encoding: 8bit');
header('Content-Disposition: attachment; filename=reservations.csv;');
header('Content-Length: ' . filesize($tmp));

echo JFile::read($tmp);

JFile::delete($tmp);
jexit();

<?php

/**
 * Export reservations into a XLS file and serve the file to the client
 *
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage models
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license    	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link       	http://www.artio.net Official website
 */
defined('_JEXEC') or die('Restricted access');

/* @var $this BookingViewReservations */

require_once (JPath::clean(JPATH_COMPONENT_SITE . '/assets/libraries/phpexcel/Classes/PHPExcel.php'));

$app = JFactory::getApplication();
$config = AFactory::getConfig();

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator(JText::_('BOOKING'))->setTitle(JText::_('RESERVATIONS'));
$objPHPExcel->setActiveSheetIndex(0);

$sheet = $objPHPExcel->getActiveSheet();
$sheet->freezePane('B2');
$rows = $sheet->getRowIterator();

$cells = $rows->current()->getCellIterator();

$cells->current()->setValue(JText::_('RES_NUM'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
$cells->next();
$cells->current()->setValue(JText::_('CREATED'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
$cells->next();
$cells->current()->setValue(JText::_('JGLOBAL_FIELD_CREATED_BY_LABEL'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
$cells->next();
$cells->current()->setValue(JText::_('MODIFIED'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
$cells->next();
$cells->current()->setValue(JText::_('JGLOBAL_FIELD_MODIFIED_BY_LABEL'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
$cells->next();
$cells->current()->setValue(JText::_('CUSTOMER'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
$cells->next();
$cells->current()->setValue(JText::_('COMPANY'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
$cells->next();
$cells->current()->setValue(JText::_('COMPANY_ID'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
$cells->next();
$cells->current()->setValue(JText::_('VAT_ID'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);

$fields = array();
foreach ($this->items as $reservation) {
    $reservation->fields = unserialize($reservation->fields);
    foreach ($reservation->fields as $name => $data) {
        $fields[$name] = $data['title'];
    }
}

foreach ($fields as $title) {
    $cells->next();
    $cells->current()->setValue($title);
    $sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
}

$cells->next();
$cells->current()->setValue(JText::_('ADDRESS'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
$cells->next();
$cells->current()->setValue(JText::_('PHONE'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
$cells->next();
$cells->current()->setValue(JText::_('EMAIL'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
$cells->next();
$cells->current()->setValue(JText::_('ITEM'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
$cells->next();
$cells->current()->setValue(JText::_('CAPACITY'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
$cells->next();
$cells->current()->setValue(JText::_('OCCUPANCY'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
$cells->next();
$cells->current()->setValue(JText::_('FROM'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
$cells->next();
$cells->current()->setValue(JText::_('TO'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);

if ($config->usingPrices != PRICES_NONE) {
    $cells->next();
    $cells->current()->setValue(JText::_('PRICE'));
    $sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
}
if ($config->usingPrices == PRICES_WITH_DEPOSIT) {
    $cells->next();
    $cells->current()->setValue(JText::_('DEPOSIT'));
    $sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
}
if ($config->usingPrices) {
    $cells->next();
    $cells->current()->setValue(JText::_('PAYMENT_STATUS'));
    $sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
}
$cells->next();
$cells->current()->setValue(JText::_('RESERVATION_STATUS'));
$sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);

$supplementList = array();
foreach ($this->reservedSupplements as $supplements) {
    foreach ($supplements as $supplement) {
        $supplementList[] = $supplement->title;
    }
}

$supplementList = array_unique($supplementList);
foreach ($supplementList as $supplementName) {
    $cells->next();
    $cells->current()->setValue($supplementName);
    $sheet->getColumnDimension($cells->current()->getColumn())->setAutoSize(true);
}

foreach ($this->items as $i => $reservation) {
    foreach ($this->reservedItems[$reservation->id] as $j => $reservedItem) {
        /* @var $reservedItem TableReservationItems */
        TableReservationItems::display($reservedItem);

        $rows->next();
        $cells = $rows->current()->getCellIterator();

        $cells->current()->setValue($reservation->id);
        $cells->next();
        $cells->current()->setValue(AHtml::date($reservation->created, ADATE_FORMAT_LONG));
        $cells->next();
        $cells->current()->setValue($reservation->creator ? $reservation->creator : JText::_('UNREGISTERED_CUSTOMER'));
        $cells->next();
        $cells->current()->setValue(AHtml::date($reservation->modified, ADATE_FORMAT_LONG));

        $cells->next();
        if ($reservation->modified) {
            $cells->current()->setValue($reservation->modifier ? $reservation->modifier : JText::_('UNREGISTERED_CUSTOMER'));
        } else {
            $cells->current()->setValue('');
        }

        $cells->next();
        $cells->current()->setValue(BookingHelper::formatName($reservation));
        $cells->next();
        $cells->current()->setValue($reservation->company);
        $cells->next();
        $cells->current()->setValue($reservation->company_id);
        $cells->next();
        $cells->current()->setValue($reservation->vat_id);

        foreach ($fields as $name => $title) {
            $cells->next();
            if (isset($reservation->fields[$name]['value'])) {
                $cells->current()->setValue(JText::_($reservation->fields[$name]['value']));
            } else {
                $cells->current()->setValue('');
            }
        }

        $cells->next();
        $cells->current()->setValue(BookingHelper::formatAddress($reservation));
        $cells->next();
        $cells->current()->setValue($reservation->telephone);
        $cells->next();
        $cells->current()->setValue($reservation->email);

        $cells->next();
        $cells->current()->setValue($reservedItem->subject_title);
        $cells->next();
        $cells->current()->setValue($reservedItem->capacity);

        $occ = array();
        foreach ($reservedItem->occupancy as $oitem) {
            $occ[] = JArrayHelper::getValue($oitem, 'title') . ': ' . JArrayHelper::getValue($oitem, 'count');
        }
        $cells->next();
        $cells->current()->setValue(implode("\n", $occ));

        if ($reservedItem->rtype == RESERVATION_TYPE_PERIOD) {
            $cells->next();
            $cells->current()->setValue(AHtml::showRecurenceTimeframe($reservedItem) . ' ' . AHtml::showRecurencePattern($reservedItem));
            $cells->next();
            $cells->current()->setValue('');
        } else {
            $cells->next();
            $cells->current()->setValue(AHtml::date($reservedItem->from, ADATE_FORMAT_NORMAL, 0) . ' ' . AHtml::date($reservedItem->from, ATIME_FORMAT_SHORT, 0));
            $cells->next();
            $cells->current()->setValue(AHtml::date($reservedItem->to, ADATE_FORMAT_NORMAL, 0) . ' ' . AHtml::date($reservedItem->to, ATIME_FORMAT_SHORT, 0));
        }

        if ($config->usingPrices) {
            $cells->next();
            $cells->current()->setValue(JFilterOutput::cleanText(BookingHelper::displayPrice($reservation->reservationFullPrice)));
        }
        if ($config->usingPrices == PRICES_WITH_DEPOSIT) {
            $cells->next();
            $cells->current()->setValue(JFilterOutput::cleanText(BookingHelper::displayPrice($reservation->reservationFullDeposit)));
        }
        if ($config->usingPrices) {
            $cells->next();
            $cells->current()->setValue(BookingHelper::showReservationPaymentStateLabel($reservation->paid));
        }
        $cells->next();
        $cells->current()->setValue(BookingHelper::showReservationStateLabel($reservation->state));

        foreach ($supplementList as $supplementName) {
            $cells->next();
            foreach ($this->reservedSupplements[$reservedItem->id] as $supplement) {
                if ($supplement->title == $supplementName && $supplement->value && $supplement->capacity) {
                    if ($supplement->type == SUPPLEMENT_TYPE_YESNO) {
                        $cells->current()->setValue($supplement->capacity);
                    } elseif ($supplement->type == SUPPLEMENT_TYPE_LIST) {
                        $cells->current()->setValue($supplement->value . ($supplement->capacity > 1 ? ' (' . $supplement->capacity . ')' : ''));
                    }
                }
            }
        }
    }
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . JText::_('Reservations') . '.xls"');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

$app->close();

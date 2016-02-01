<?php

/**
 * Print Manager Voucher as PDF
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

$app = JFactory::getApplication();

ob_start();
include(dirname(__FILE__) . '/voucher.php');
$html = ob_get_clean();

$fontfile = '';
if (!class_exists('TCPDF')) {// maybe Bookinginvoices are used
    require_once(JPath::clean(JPATH_COMPONENT_SITE . '/assets/libraries/tcpdf/tcpdf.php'));
} else { // load own font - Bookinginvoices cannot have such font
    $fontfile = JPath::clean(JPATH_COMPONENT_SITE . '/assets/libraries/tcpdf/fonts/freesans.php');
}

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

// set metadata of PDF file
$sitename = $app->getCfg('sitename');
$pdf->SetTitle($sitename);
$pdf->SetCreator($sitename);
$pdf->SetAuthor($sitename);
$pdf->SetKeywords($sitename);

$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$locale = JFactory::getLanguage()->getLocale();

$pdf->setLanguageArray(array('a_meta_charset' => 'UTF-8', 'a_meta_dir' => 'ltr', 'a_meta_language' => $locale[4], 'w_page' => JText::_('JPage')));
$pdf->setFontSubsetting(true);
$pdf->SetFont('freesans', '', 12, $fontfile, true);
$pdf->AddPage();
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

$filepath = JPath::clean($app->getCfg('tmp_path') . '/' . uniqid());

$pdf->Output($filepath, 'F');

ob_end_clean();

header('Content-Type: application/pdf; charset=UTF-8');
header('Content-Transfer-Encoding: 8bit');
header('Content-Disposition: attachment; filename="reservation#' . $this->reservation->id . '.pdf";');
$fileSize = @filesize($filepath);
if ($fileSize) {
    header('Content-Length: ' . $fileSize);
}
echo JFile::read($filepath);
JFile::delete($filepath);
$app->close();

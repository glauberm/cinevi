<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  plugins/search
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

class plgcciDEALPlatformCustomPaymentBookingccideal extends JPlugin
{
	/**
	 * New event in cciDEAL 2.5.0
	 * 
	 * @param string $orderNumber
	 * @param string $component
	 * @param string $status "paid" or "cancelled"
	 */
	function OnPaymentUpdateStatus($orderNumber,$component,$status)
	{
		if ($component!='booking' || !$orderNumber)
			return ;
		
		$helpers = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_booking' . DS . 'helpers' . DS;
	
	     // Find Bookit importer    
	    if (file_exists(($importer = ($helpers . 'importer.php'))))
	        include_once ($importer);
	    else
	        return false;
	
	    // Import needed Bookit framework    
	    AImporter::defines();
	    AImporter::helper('booking','model','controller','html');
	    AImporter::model('reservation');
	    AImporter::table('reservation','reservationitems','subject','customer');
	    AImporter::controller('reservation');
	    
	    $modelRes = new BookingModelReservation();
	    $controllerRes = new BookingControllerReservation();
	    
	    $orderID = preg_replace('#[^0-9]#','',$orderNumber);
	    
	    //check if order exists
	    list($fullPrice,$fullDeposit) = BookingHelper::countOverallPrice($orderID);
	    
	    if (!$fullPrice && !$fullDeposit) //no payment / no reservation
	    	return false;
	    
	    //determine operation
	    $operation = null;
	    $msg=null;
	    $notice=false;
	    
	    //load language for messages
		$lang = JFactory::getLanguage();
		$lang->load('com_booking',JPATH_SITE);
			
	    if ($status=="paid"){ //success
			if (preg_match('#\d+-deposit$#i',$orderNumber)) { //successful deposit payment
				$operation=JText::_('RECEIVEDEPOSIT');
	    		$msg = 'Deposit received';}
			elseif (preg_match('#\d+-whole$#i',$orderNumber) || preg_match('#\d+-rest$#i',$orderNumber)){ 
				$operation='receive'; //suceesful all payment
	    		$msg = JText::_('RESERVATION_IS_RECEIVED');}
	    }
	    elseif ($status=="cancelled") {  //payment was cancelled
	    	
	    	$notice = true;
	    	if (preg_match('#-rest$#i',$orderNumber)){ //cancelled payment of rest, make it back to receive deposit
	    		$operation = 'receiveDeposit';
	    		$msg = JText::_('RESERVATION_PAYMENT_OF_RAMINING_PRICE_WAS_CANCELLED_STATE_WAS_CHANGED_TO_DEPOSIT_RECEIVED');}
	    	elseif (preg_match('#\d+-whole$#i',$orderNumber)){
	    		$operation='unreceive';//cancelled payment of all, make unreceived
	    		$msg = JText::_('RESERVATION_PAYMENT_WAS_CANCELLED');}
	    	elseif (preg_match('#\d+-deposit$#i',$orderNumber)){
	    		$operation='unreceive';//cancelled payment of deposit, make unreceived
	    		$msg = JText::_('DEPOSIT_PAYMENT_WAS_CANCELLED');}
	    }

	   	if ($operation)
		    if ($modelRes->$operation((array)$orderID))	//change Bookit payment state
				$controllerRes->changeStatusInfo($orderID,$operation); //send mail about change state. also at this point will be attached invoice to mail if conditions are met.

		//display message
		if ($msg){
			$msg.='.';
			$mainframe = JFactory::getApplication();
			
			if ($mainframe->isSite()){ //add link to reservation
				$reservationUrl = JRoute::_(ARoute::view(VIEW_RESERVATION, null, null, array('cid' => $orderID)));
				$msg.='<br><a href="'.$reservationUrl.'">'.JText::_('DISPLAY_RESERVATION').'</a>';
			}
			
			$msg = '<div style="text-align:center;padding:5px;">'.$msg.'</div>';
			
			$notice ? JError::raiseNotice(0,$msg) : $mainframe->enqueueMessage($msg);
		}

	   	return true;
	}
}

?>
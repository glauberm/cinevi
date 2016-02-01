<?php

/**
 * The plugin cooperates with cciDEAL Platform to support iDEAL online payments. The plugin is important for new versions of cciDEAL Platform which do not work with the original plugin cciDEALPlatformCustomPayment/bookingccideal.
 * 
 * @package		 ARTIO Booking
 * @subpackage  payments
 * @copyright	 Copyright (C) 2014 ARTIO LTD. All rights reserved.
 * @author 		 ARTIO LTD, http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link         http://www.artio.net Official website
 */
defined('_JEXEC') or die;

JLog::addLogger(array('text_file' => 'plg_bookingpayment_ccideal.php'), JLog::ALL, array('plg_bookingpayment_ccideal'));

class PlgSystemBookingccideal extends JPlugin {

    /**
     * After cciDEAL platform is finished.
     */
    public function onAfterRender() {
        $app = JFactory::getApplication();

        if ($app->isSite() && $app->input->get('option') == 'com_ccidealplatform') { // return to the platform after payment

            $data = $app->input->get('Data', null, 'string');
            $matches = array();

            if ($data && preg_match_all('/([^|=]+)=([^|]+)/', $data, $matches, PREG_SET_ORDER)) {
                
                JLog::add('Response: ' . print_r($_REQUEST, true), JLog::INFO, 'plg_bookingpayment_ccideal');
                
                foreach ($matches as $match) {
                    if ($match[1] == 'transactionReference') { // cciDEAL response
                        $db = JFactory::getDbo();
                        $query = $db->getQuery(true);

                        $table = $db->replacePrefix('#__ccidealplatform_payments');
                        $tableList = $db->getTableList();

                        if (in_array($table, $tableList)) {

                            $query->select('order_id') // get the transaction
                                    ->from('#__ccidealplatform_payments')
                                    ->where('trans_id = ' . $db->q($match[2]))
                                    ->where('extension = ' . $db->q('booking'));

                            $orderId = $db->setQuery($query)->loadResult(); // search reservation

                            JLog::add('Order ID: ' . $orderId, JLog::INFO, 'plg_bookingpayment_ccideal');

                            if ($orderId) {

                                $match = array();
                                if (preg_match('/[1-9]+[0-9]*\-([a-z]+)/', $orderId, $match)) {
                                    switch ($match[1]) { // check payment type (full, deposit ...)
                                        case 'whole':
                                        case 'rest':
                                        default:
                                            $paid = 'receive';
                                            break;
                                        case 'deposit':
                                            $paid = 'receiveDeposit';
                                            break;
                                    }
                                    // go to Booking to finalise payment
                                    $route = 'index.php?option=com_booking&controller=reservation&task=payment&type=ccideal&paid=' . $paid . '&cid[]=' . (int) $orderId;
                                    JLog::add('Route: ' . $route, JLog::INFO, 'plg_bookingpayment_ccideal');
                                    $app->redirect($route);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}

<?php

/**
 * View customer form.
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

//import needed Joomla! libraries
jimport('joomla.application.component.view');

if (! class_exists('JUser')) {
    jimport('joomla.user.user');
}

//import needed models
AImporter::model('customer', 'subject');
//import needed JoomLIB helpers
AImporter::helper('booking', 'request', 'utils');
//import needed assets
if (IS_SITE) {
    AImporter::joomlaJS();
}
AImporter::js('view-customer', 'view-customer-submitbutton');
//import custom icons
AHtml::importIcons();

class BookingViewCustomer extends JViewLegacy
{

    /**
     * Prepare to display page.
     * 
     * @param string $tpl name of used template
     */
    function display($tpl = null)
    {
    	if (IS_ADMIN && !JFactory::getUser()->authorise('booking.view.customers', 'com_booking'))
    		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
    	
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        $model = new BookingModelCustomer();
        
        if (IS_ADMIN) {
            $model->setId(ARequest::getCid());
        } elseif (IS_SITE) {
            $model->setIdByUserId();
        }
        
        $customer = &$model->getObject();
        
        if ($customer) {
            $customerUser = new JUser($customer->user);
            
            $startSubjectId = JRequest::getInt('startSubjectId');
            
            if ($startSubjectId) {
                $subjectModel = new BookingModelSubject();
                $subjectModel->setId($startSubjectId);
                $subject = &$subjectModel->getObject();
                $this->assignRef('subject', $subject);
            }
            
            if ($this->getLayout() == 'form') {
                $this->_displayForm($tpl, $customer, $customerUser);
                return;
            }
            
            $document->setTitle(BookingHelper::formatName($customer));
            
            $params = JComponentHelper::getParams(OPTION);
            /* @var $params JRegistry */
            
            $this->assignRef('customer', $customer);
            $this->assignRef('user', $customerUser);
            $this->assignRef('params', $params);
            parent::display($tpl);
            return;
        }
        JError::raise(E_ERROR, 500, 'Customer not found');
    }

    /**
     * Prepare to display page.
     * 
     * @param string $tpl name of used template
     * @param TableCustomer $customer
     * @param JUser $user
     */
    function _displayForm($tpl, $customer, $user)
    {
    	if (IS_ADMIN && !JFactory::getUser()->authorise('booking.edit.customer', 'com_booking'))
    		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
    	
        $document = &JFactory::getDocument();
        /* @var $document JDocument */
        
        $error = JRequest::getInt('error');
        $data = JRequest::get('post');
        

        if ($error) {
            $customer->bind($data);
            $user->bind($data);
        }
        
        if (! $customer->id && ! $error) {
            $customer->init();
        }
        
        JFilterOutput::objectHTMLSafe($customer);
        JFilterOutput::objectHTMLSafe($user);
        
        $document->setTitle($customer->id ? BookingHelper::formatName($customer) : JText::_('CREATE_NEW_CUSTOMER_REGISTRATION'));
        
        $params = JComponentHelper::getParams(OPTION);
        /* @var $params JParameter */
        
        $this->assignRef('customer', $customer);
        $this->assignRef('user', $user);
        $this->assignRef('params', $params);
        parent::display($tpl);
    }
}

?>
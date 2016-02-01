<?php

/**
 * Customer controller.
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  controllers
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

//import needed JoomLIB helpers
AImporter::helper('booking', 'controller', 'parameter', 'request', 'smsservice', 'httphelper', 'utils');

class BookingControllerCustomer extends AController
{
    
    /**
     * Main model
     * 
     * @var BookingModelCustomer
     */
    var $_model;

    function __construct($config = array())
    {
        parent::__construct($config);
        if (! class_exists('BookingModelCustomer')) {
            AImporter::model('customer');
        }
        $this->_model = new BookingModelCustomer();
        $this->_controllerName = CONTROLLER_CUSTOMER;
    }

    /**
     * Display default view - customers list	
     */
    function display()
    {
        switch ($this->getTask()) {
        	case 'block':
            case 'trash':
            case 'restore':
                $this->state($this->getTask());
                break;
            case 'detail':
                JRequest::setVar('view', 'customer');
                break;
            default:
                JRequest::setVar('view', 'customers');
                break;
        }
        parent::display();
    }

    /**
     * Display browse customers page into element window.
     */
    function element()
    {
        $this->display();
    }

    /**
     * Open editing form page.
     */
    function editing()
    {
        parent::editing('customer');
    }

    /**
     * Cancel edit operation. Check in customer and redirect to customers list. 
     */
    function cancel()
    {
        parent::cancel('CUSTOMER_EDITING_CANCELED');
    }

    /**
     * Save customer.
     * 
     * @param boolean $apply true state on edit page, false return to browse list
     */
    function save($apply = false)
    {
        JRequest::checkToken() or jexit('Invalid Token');
        
        if (IS_ADMIN && !JFactory::getUser()->authorise('booking.edit.customer', 'com_booking'))
    		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        
        $mainframe = &JFactory::getApplication();
        /* @var $mainframe JApplication */
        $user = &JFactory::getUser();
        /* @var $user JUser */
        $config = &AFactory::getConfig();

        if ($mainframe->isSite() && !BookingHelper::controlCaptcha()) {
            $mainframe->enqueueMessage(JText::_('CAPTCHA_INVALID'), 'error');
            JRequest::setVar('error', 1);
            return $this->editing();
        }        
        
        $post = JRequest::get('post');
        
        if (IS_ADMIN)
            $post['id'] = ARequest::getCid();
        
        elseif (IS_SITE) {
            if ($user->id) {
                $this->_model->setIdByUserId();
                $post['id'] = $this->_model->getId();
            } else
                $post['id'] = 0;
        }
        
        $isNew = $post['id'] == 0;
        $id = $this->_model->store($post);
        
        if ($id !== false) {
            $mainframe->enqueueMessage(JText::_('SUCCESSFULLY_SAVED'), 'message');
            
            if (IS_SITE) {
                
                if ($isNew) {
                    
                    if (! $user->id)
                        $mainframe->login(array('password' => $post['password'] , 'username' => $post['username']), array('remember' => 1 , 'return' => ARoute::detail($this->_controllerName)));
                    
                    $user = &JFactory::getUser();
                    /* @var $user JUser */
                    
                    if ($config->mailingRegistrationClient) {
                    	$email = JModelLegacy::getInstance('Email', 'BookingModel')->getItem($config->mailingRegistrationClient);
                    	
                    	if ($user->email && ($email->usage == NOTIFY_EMAIL || $email->usage == NOTIFY_ALL))
                        	JFactory::getMailer()->sendMail(JFactory::getApplication()->getCfg('mailfrom'), JFactory::getApplication()->getCfg('fromname'), $user->email, $email->subject, $this->replaceEmailBody($email->body, $user, $post, $this->_model->_table), $email->mode);
                    	
                    	if ($post['telephone'] && ($email->usage == NOTIFY_SMS || $email->usage == NOTIFY_ALL))
                    		ASmsService::sendMessage($config->smsUsername, $config->smsApikey, AUtils::getLocalPhone($post['telephone']), $this->replaceEmailBody($email->sms, $user, $post, $this->_model->_table, true), $config->smsUnicode);
                    }
                    
                    if ($config->mailingRegistrationManager) {
                    	$email = JModelLegacy::getInstance('Email', 'BookingModel')->getItem($config->mailingRegistrationManager);
                    	
                    	if ($config->mailingManager && ($email->usage == NOTIFY_EMAIL || $email->usage == NOTIFY_ALL))
                    		JFactory::getMailer()->sendMail(JFactory::getApplication()->getCfg('mailfrom'), JFactory::getApplication()->getCfg('fromname'), $config->mailingManager, $email->subject, $this->replaceEmailBody($email->body, $user, $post, $this->_model->_table), $email->mode);
                    	
                    	if ($config->mailingManagerPhone && ($email->usage == NOTIFY_SMS || $email->usage == NOTIFY_ALL))
                    		ASmsService::sendMessage($config->smsUsername, $config->smsApikey, AUtils::getLocalPhone($config->mailingManagerPhone), $this->replaceEmailBody($email->sms, $user, $post, $this->_model->_table, true), $config->smsUnicode);                    }
                }
                
                if (($startSubjectId = JRequest::getInt('startSubjectId')))
                    $customParams['startSubjectId'] = $startSubjectId;
                else
                    $customParams = array();
                
                if (JRequest::getString('return') == 'reservation')
                	JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_booking&view=reservation&layout=form'));
                else
                	ARequest::redirectDetail($this->_controllerName, null, $customParams);
            
            } elseif ($apply)
                ARequest::redirectEdit($this->_controllerName, $id);
            
            else
                ARequest::redirectList($this->_controllerName);
        
        } else {
            JRequest::setVar('error', 1);
            
            foreach ($this->_model->_errors as $error) {
                $language = &JFactory::getLanguage();
                /* @var $language JLanguage */
                $language->load('com_users', JPATH_ADMINISTRATOR);
                $mainframe->enqueueMessage(JText::_($error), 'error');
            }
            
            $this->editing();
        }
    }

    /**
     * Prepare registration e-mail body.
     * 
     * @param string $body e-mail body
     * @param JUser $user Joomla! user 
     * @param array $post request data
     * @param TableCustomer $customer
     * 
     * @return string 
     */
    function replaceEmailBody($body, &$user, &$post, &$customer, $cleanup = false)
    {
        $body = str_replace('{REGISTRATION DATE}', AHtml::date($user->registerDate, ADATE_FORMAT_LONG), $body);
        $body = str_replace('{USERNAME}', $user->username, $body);
        $body = str_replace('{PASSWORD}', $post['password'], $body);
        $body = str_replace('{EMAIL}', $user->email, $body);
        $body = str_replace('{NAME}', BookingHelper::formatName($customer), $body);
        $body = str_replace('{COMPANY}', $customer->company, $body);
        $body = str_replace('{ADDRESS}', BookingHelper::formatAddress($customer), $body);
        $body = str_replace('{TELEPHONE}', $customer->telephone, $body);
        $body = str_replace('{FAX}', $customer->fax, $body);        
        $fields = is_string($customer->fields) ? @unserialize($customer->fields) : $customer->fields;
        if (is_array($fields)) {
            foreach ($fields as $field) {
                if ($field['value'] == 'jyes' || $field['value'] == 'jno') {
                    $field['value'] = JText::_ ($field['value']);
                }
                $body = str_replace('{' . JString::strtoupper($field['title']) . '}', $field['value'], $body);
            }
        }
        
		$body .= "\n\n" . BookingHelper::get();
		
        
        return $cleanup ? str_replace(array("\r\n\r\n", "\n\r\n\r", "\n"), array("\r\n", "\n\r", "\n"), JFilterOutput::cleanText($body)) : $body;
    }

    /**
     * Get customer filter suggest by AJAX
     */
    function suggest() 
    {
    	die(json_encode($this->_model->suggest(JRequest::getString('request'))));
    }
    
    /**
     * Get customer data by AJAX
     */
    function ajax()
    {
        $this->_model->_id = JRequest::getInt('id');
        die(json_encode($this->_model->getObject()));
    }
    
    /**
     * Get user data by AJAX
     */
    function getUserData() {
        $user = JFactory::getUser(JRequest::getInt('id'));
        $data = array('name' => $user->name, 'username' => $user->username, 'email' => $user->email);
        die(json_encode($data));
    }
}

?>
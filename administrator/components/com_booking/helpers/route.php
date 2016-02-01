<?php

/**
 * Defined component routes
 * 
 * @version		$Id$
 * @package		ARTIO JoomLIB
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class ARoute
{

    /**
     * URL root prefix
     * 
     * @return string URL fragment
     */
    function root()
    {
        $Itemid = '';
        if (IS_SITE) {
        	$Itemid = JRequest::getInt('Itemid');
        	if (!$Itemid){
            	$mainframe = &JFactory::getApplication();
            	/* @var $mainframe JApplication */
            	$menu = &$mainframe->getMenu();
            	/* @var $menu JMenuSite */
            	$active = &$menu->getActive();
            	if (is_object($active)) {
                	$Itemid = '&Itemid=' . $active->id;
            	} else {
            		$Itemid = '';
            	}
        	} else {
        		$Itemid = '&Itemid=' . $Itemid;
        	}
        }        
        return 'index.php?option=' . OPTION . $Itemid;
    }

    /**
     * Get route to browse list items.
     * 
     * @param string $controller items controller name
     * @param boolean $element add params to open element window
     * @return string URL
     */
    function browse($controller, $element = false, $extra = '')
    {
        return ARoute::root() . ARoute::controller($controller) . ($element ? ARoute::element() : '') . $extra;
    }

    /**
     * URL to edit item
     * 
     * @param string $controller item scontroller name
     * @param $id item ID
     * @return string URL
     */
    function edit($controller, $id = null, $customParams = array())
    {
        return ARoute::root() . ARoute::controller($controller) . ARoute::task('edit') . ARoute::id($id) . ARoute::customUrl($customParams, false);
    }

    /**
     * URL to view detail item
     * 
     * @param string $controller item scontroller name
     * @param $id item ID
     * @return string URL
     */
    function detail($controller, $id = null, $customParams = array())
    {
        return ARoute::root() . ARoute::controller($controller) . ARoute::task('detail') . ARoute::id($id) . ARoute::customUrl($customParams, false);
    }

    /**
     * URL to view frontend page.
     * 
     * @param string $view
     * @param mixed $id
     * @param string $alias entity title alias
     * @param array $customParams next custome URL parameters 
     * @return string URL
     */
    function view($view, $id = null, $alias = null, $customParams = array())
    {
        return ARoute::root() . '&view=' . $view . ARoute::simpleId($id, $alias) . ARoute::customUrl($customParams, false);
    }
    
    /**
     * URL to view with concrete layotu.
     * 
     * @param string $view
     * @param string $layout
     * @return string URL
     */
    function view2layout($view, $layout)
    {
    	return ARoute::root() . ($view ? '&view=' . $view : '') . '&layout=' . $layout;
    }

    /**
     * URL part with controller param
     * 
     * @param string $name controller name
     * @return string URL fragment
     */
    function controller($name)
    {
        return '&controller=' . $name;
    }

    /**
     * URL part with task param
     * 
     * @param string $task task name
     * @return string URL fragment
     */
    function task($task)
    {
        return '&task=' . $task;
    }

    /**
     * URL part with id param like array
     * 
     * @param string $id id value
     * @return string URL fragment
     */
    function id($id)
    {
        return $id ? '&cid[]=' . $id : '';
    }

    /**
     * URL part with id param
     * 
     * @param string $id id value
     * @param string $alias entity title alias
     * @return string URL fragment
     */
    function simpleId($id, $alias)
    {
        if ($id) {
            return '&id=' . $id . ($alias ? (':' . $alias) : '');
        }
        return '';
    }

    /**
     * Add params for open element window.
     */
    function element()
    {
        return '&task=element&tmpl=component';
    }

    /**
     * Get user edit route to standard Joomla! users component.
     * 
     * @param int $id user ID
     * @return String URL
     */
    function editUser($id = null)
    {
        if (IS_ADMIN) {
            return 'index.php?option=com_users&task=user.edit&id=' . $id;
        }
        return JRoute::_('index.php?option=com_users&view=profile');
    }

    /**
     * Get login use route.
     * 
     * @return String URL
     */
    function loginUser()
    {
        return JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JURI::current()));
    }

    /**
     * Get logout user route.
     * 
     * @return String URL
     */
    function logoutUser()
    {
        return JRoute::_('index.php?option=com_users&task=user.logout&' . JSession::getFormToken() . '=1');
    }

    /**
     * Create custom URL from given params.
     * 
     * @param array $params where key is param name and value param value
     * @param boolean add live site URL root
     * @return string URL
     */
    function customUrl($params, $root = true)
    {
        $url = $root ? ARoute::root() : '';
        foreach ($params as $param => $value) {
            if (is_array($value)) {
                $count = count($value);
                for ($i = 0; $i < $count; $i ++) {
                    $url .= '&' . $param . '[]=' . $value[$i];
                }
            } else {
                $url .= '&' . $param . '=' . $value;
            }
        
        }
        return $url;
    }

    function config()
    {
        return JURI::root() . 'administrator/index.php?option=com_config&controller=component&component=' . OPTION . '&path=';
    }

    /**
     * URL to view with layout specified.
     * 
     * @param string $view
     * @param string $layout
     * @return string URL
     */
    function viewlayout($view, $layout)
    {
        return ARoute::root() . '&view=' . $view . '&layout=' . $layout;
    }

    /**
     * Convert special HTML chars.
     * 
     * @param string $url
     * @return string
     */
    function convertUrl($url)
    {
        return str_replace('&amp;', '&', $url);
    }

    /**
     * Get URL to save payment result
     * 
     * @param string $type payment method type alias
     */
    function payment($type, $id, $paid)
    {
        return ARoute::root() . ARoute::controller(CONTROLLER_RESERVATION) . ARoute::task('payment') . '&type=' . $type . '&paid=' . $paid . '&cid[]=' . $id . '&hash=' . md5(session_id());
    }
    
    /**
     * Safe URL by replacing some chars by htmlentities.
     * 
     * @param string $url
     * @return string
     */
    function safeURL($url) 
    {
    	return str_replace('&', '&amp;', $url);
    }
    
    function quickBookBackLink()
    {
		$active = JFactory::getApplication()->getMenu()->getItem(JRequest::getInt('Itemid'));
    	if (is_object($active)) {
    		$juri = JURI::getInstance($active->link);
    		if ($juri->getVar('option') == 'com_booking' && $juri->getVar('view') == 'quickbook')
    			return $active->link . '&Itemid=' . $active->id;
    	}
    	return null;
    }
    
    function redirectionAfterReservation($noRoute = false, $reservationId = null, $sessionIdMd5 = null)
    {
    	switch(AFactory::getConfig()->redirectionAfterReservation) {
    		
    		case REDIRECTION_AFTER_RESERVATION_THANKYOU_PAGE:
    			$route = ARoute::view(VIEW_RESERVATION) . ARoute::id($reservationId) . (JFactory::getUser()->get('id') === 0 ? ARoute::customUrl(array('session' => $sessionIdMd5 ? $sessionIdMd5 : md5(session_id())), false) : '');
    			break;
    		
    		case REDIRECTION_AFTER_RESERVATION_LATEST_SUBJECT:
    			$route = JFactory::getApplication()->getUserState('com_booking.object.last', 'index.php');
    			$noRoute = true;
    			break;
    			
    		case REDIRECTION_AFTER_RESERVATION_SUBJECT_LIST:
    			$route = ARoute::view(VIEW_SUBJECTS);
    			break;
    			
    		case REDIRECTION_AFTER_RESERVATION_RESERVATION_LIST:
    			$route = ARoute::view(VIEW_RESERVATIONS);
    			break;
    			
    		case REDIRECTION_AFTER_RESERVATION_HOMEPAGE:
    		default:
    			$route = 'index.php';
    			break;
    			
    		case REDIRECTION_AFTER_RESERVATION_MENU_ITEM:
    			$item = JFactory::getApplication()->getMenu()->getItem(AFactory::getConfig()->redirectionAfterReservationMenuItem);
    			if ($item)
    				$route = $item->link . '&Itemid=' . $item->id;
    			else 
    				$route = 'index.php';
    			break;
    			
    		case REDIRECTION_AFTER_RESERVATION_CUSTOM_URL:
    			$route = AFactory::getConfig()->redirectionAfterReservationCustomUrl;
    			$noRoute = true;
    			break;
    	}
    	
    	return $noRoute ? $route : JRoute::_($route);
    }
    
    function redirectionBackReservation()
    {
    	switch(AFactory::getConfig()->redirectionBackReservation) {
    
    		case REDIRECTION_BACK_RESERVATION_LATEST_SUBJECT:
    			$route = JFactory::getApplication()->getUserState('com_booking.object.last', 'index.php');
    			break;
    			 
    		case REDIRECTION_BACK_RESERVATION_SUBJECT_LIST:
    			$route = ARoute::view(VIEW_SUBJECTS);
    			break;
    			 
    		case REDIRECTION_BACK_RESERVATION_RESERVATION_LIST:
    			$route = ARoute::view(VIEW_RESERVATIONS);
    			break;
    			 
    		case REDIRECTION_BACK_RESERVATION_HOMEPAGE:
    		default:
    			$route = 'index.php';
    			break;
    	}
    	 
    	return JRoute::_($route);
    }
}

?>

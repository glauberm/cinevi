<?php
/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	views
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */
defined('_JEXEC') or die;

/* @var $this BookingViewEmail */
?>
<style type="text/css">
    textarea#jform_sms.inputbox {
        margin: 0 10px 0 0;
    }
    #adminForm .adminlist {
        display: inline-block;
        width: auto;
    }
    #adminForm .adminlist td, 
    #adminForm .adminlist th {
        white-space: nowrap;
        width: 1%;
    }
    #adminForm .adminlist td.sub {
        text-indent: 30px;
    }
    #jform_sms {
        width: 250px;
    }
</style>
<form action="<?php echo JRoute::_('index.php?option=com_booking&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div>
        <fieldset <?php if (!ISJ3) { ?>class="adminform"<?php } ?>>
            <legend>
                <?php echo JText::_('DETAILS'); ?>
            </legend>
            <table class="table table-striped">
                <tr>
                    <td><?php echo $this->form->getLabel('subject'); ?></td>
                    <td><?php echo $this->form->getInput('subject'); ?></td>
                </tr>
                <tr>
                    <td><?php echo $this->form->getLabel('usage'); ?></td>
                    <td><?php echo $this->form->getInput('usage'); ?></td>
                </tr>
                <tr>
                    <td><?php echo $this->form->getLabel('mode'); ?></td>
                    <td><?php echo $this->form->getInput('mode'); ?></td>
                </tr>
                <tr>
                    <td valign="top">
                        <?php echo $this->form->getLabel('sms'); ?>
                        <div class="clr"></div>
                        <?php echo $this->form->getInput('sms');
                        ?>
                    </td>
                    <td>
                        <?php echo $this->form->getLabel('body'); ?>
                        <div class="clr"></div>
                        <?php echo $this->form->getInput('body');
                        ?>
                    </td>
                </tr>
            </table>					
        </fieldset>
    </div>
    <div>
        <input type="hidden" name="task" value="" /> 
        <input type="hidden" name="return" value="<?php echo JRequest::getCmd('return'); ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
    <?php
    echo JHtml::_('sliders.start', 'tabone', array('allowAllClose' => true, 'useCookie' => true));
    echo JHtml::_('sliders.panel', JText::_('REGISTRATION_MARKS'), 'registration-marks');
    ?>
    <fieldset>
        <table class="adminlist table-striped table">
            <thead>
                <tr>
                    <th><?php echo JText::_('TEMPLATE_MARK'); ?></th>
                    <th><?php echo JText::_('TEMPLATE_MARK_DESC'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{REGISTRATION DATE}</td>
                    <td><?php echo JText::_('JDATE'); ?></td>
                </tr>
                <tr class="row1">
                    <td>{USERNAME}</td>
                    <td><?php echo JText::_('JGLOBAL_USERNAME'); ?></td>
                </tr>
                <tr>
                    <td>{PASSWORD}</td>
                    <td><?php echo JText::_('JGLOBAL_PASSWORD'); ?></td>
                </tr>
                <tr class="row1">
                    <td>{EMAIL}</td>
                    <td><?php echo JText::_('JGLOBAL_EMAIL'); ?></td>
                </tr>
                <tr>
                    <td>{NAME}</td>
                    <td><?php echo JText::_('NAME'); ?></td>
                </tr>
                <tr class="row1">
                    <td>{COMPANY}</td>
                    <td><?php echo JText::_('COMPANY'); ?></td>
                </tr>
                <tr>
                    <td>{ADDRESS}</td>
                    <td><?php echo JText::_('ADDRESS'); ?></td>
                </tr>
                <tr class="row1">
                    <td>{TELEPHONE}</td>
                    <td><?php echo JText::_('TELEPHONE'); ?></td>
                </tr>
                <tr>
                    <td>{FAX}</td>
                    <td><?php echo JText::_('FAX'); ?></td>
                </tr>
                <?php echo $this->loadTemplate('fields'); ?>
            </tbody>
        </table>
    </fieldset>
    <?php echo JHtml::_('sliders.panel', JText::_('RESERVATION_MARKS'), 'reservation-marks'); ?>
    <fieldset>
        <table class="adminlist table-striped table">
            <thead>
                <tr>
                    <th><?php echo JText::_('TEMPLATE_MARK'); ?></th>
                    <th><?php echo JText::_('TEMPLATE_MARK_DESC'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{STATUS}</td>
                    <td><?php echo JText::_('STATUS_CHANGE_STATUS_E_MAIL_ONLY'); ?></td>
                </tr>
                <tr class="row1">
                    <td>{OBJECTS}</td>
                    <td><?php echo JText::_('ENCLOSING_MARKS_FOR_RESERVED_OBJECT'); ?>
                    </td>
                </tr>
                <tr>
                    <td class="sub">{OBJECT TITLE}</td>
                    <td><?php echo JText::_('RESERVED_OBJECT_TITLE'); ?></td>
                </tr>
                <tr class="row1">
                    <td class="sub">{DATE}</td>
                    <td><?php echo JText::_('RESERVATION_DATE'); ?></td>
                </tr>
                <tr>
                    <td class="sub">{DATE_FROM}</td>
                    <td><?php echo JText::_('RESERVATION_DATE_UP'); ?></td>
                </tr>
                <tr class="row1">
                    <td class="sub">{DATE_TO}</td>
                    <td><?php echo JText::_('RESERVATION_DATE_DOWN'); ?></td>
                </tr>				
                <tr>
                    <td class="sub">{DATE_TIME_FROM}</td>
                    <td><?php echo JText::_('RESERVATION_DATE_AND_TIME_UP'); ?></td>
                </tr>
                <tr class="row1">
                    <td class="sub">{DATE_TIME_TO}</td>
                    <td><?php echo JText::_('RESERVATION_DATE_AND_TIME_DOWN'); ?></td>
                </tr>
                <tr>
                    <td class="sub">{PRICE}</td>
                    <td><?php echo JText::_('FULL_OBJECT_PRICE_WITHOUT_SUPPLEMENTS'); ?>
                    </td>
                </tr>
                <tr class="row1">
                    <td class="sub">{PRICEWITHSUPPLEMENTS}</td>
                    <td><?php echo JText::_('FULL_OBJECT_PRICE_WITH_SUPPLEMENTS'); ?></td>
                </tr>
                <tr>
                    <td class="sub">{DEPOSIT}</td>
                    <td><?php echo JText::_('FULL_OBJECT_DEPOSIT'); ?></td>
                </tr>
                <tr class="row1">
                    <td class="sub">{TAX}</td>
                    <td><?php echo JText::_('TAX'); ?></td>
                </tr>
                <tr>
                    <td class="sub">{SUPPLEMENTS}</td>
                    <td><?php echo JText::_('SUPPLEMENTS_LIST'); ?></td>
                </tr>
                <tr class="row1">
                    <td class="sub">{QUANTITY}</td>
                    <td><?php echo JText::_('RESERVED_OBJECT_CAPACITY'); ?></td>
                </tr>
                <tr>
                    <td class="sub">{MESSAGE}</td>
                    <td><?php echo JText::_('RESERVED_OBJECT_MESSAGE'); ?></td>
                </tr>
                <tr class="row1">
                    <td class="sub">{OCCUPANCY}</td>
                    <td><?php echo JText::_('Occupancy'); ?></td>
                </tr>				
                <tr>
                    <td class="sub">{TIMEFRAME}</td>
                    <td><?php echo JText::_('PERIODIC_RESERVATION_TIMEFRAME'); ?></td>
                </tr>
                <tr class="row1">
                    <td class="sub">{RECURRENCE PATTERN}</td>
                    <td><?php echo JText::_('PERIODIC_RESERVATION_RECURRENCE_PATTERN'); ?></td>
                </tr>
                <tr>
                    <td class="sub">{RANGE OF RECURRENCE}</td>
                    <td><?php echo JText::_('PERIODIC_RESERVATION_RANGE_OF_RECURRENCE'); ?></td>
                </tr>
                <tr class="row1">
                    <td class="sub">{RECURRENCE TOTAL}</td>
                    <td><?php echo JText::_('PERIODIC_RESERVATION_RECURRENCE_TOTAL'); ?></td>
                </tr>
                <tr>
                    <td class="sub">{MORE_NAMES}</td>
                    <td><?php echo JText::_('MORE_CUSTOMERS'); ?></td>
                </tr>									                                
                <tr>
                    <td>{/OBJECTS}</td>
                    <td></td>
                </tr>
                <tr class="row1">
                    <td>{ID}</td>
                    <td><?php echo JText::_('RESERVATION_ID_BODY_EMAIL'); ?></td>
                </tr>
                <tr>
                    <td>{CREATED}</td>
                    <td><?php echo JText::_('RESERVATION_CREATED'); ?></td>
                </tr>
                <tr class="row1">
                    <td>{STATUS}</td>
                    <td><?php echo JText::_('ORDER_STATUS'); ?></td>
                </tr>
                <tr>
                    <td>{FULLPRICE}</td>
                    <td><?php echo JText::_('OVERALL_RESERVATION_PRICE'); ?></td>
                </tr>
                <tr class="row1">
                    <td>{FULLDEPOSIT}</td>
                    <td><?php echo JText::_('OVERALL_RESERVATION_DEPOSIT'); ?></td>
                </tr>
                <tr>
                    <td>{FULLTAX}</td>
                    <td><?php echo JText::_('FULL_TAX'); ?></td>
                </tr>
                <tr class="row1">
                    <td>{CUSTOMER}</td>
                    <td><?php echo JText::_('CUSTOMER_FULL_NAME'); ?></td>
                </tr>
                <tr>
                    <td>{TITLE_BEFORE}</td>
                    <td><?php echo JText::_('TITLE_BEFORE'); ?></td>
                </tr>
                <tr class="row1">
                    <td>{FIRSTNAME}</td>
                    <td><?php echo JText::_('FIRSTNAME'); ?></td>
                </tr>
                <tr>
                    <td>{MIDDLENAME}</td>
                    <td><?php echo JText::_('MIDDLENAME'); ?></td>
                </tr>
                <tr class="row1">
                    <td>{SURNAME}</td>
                    <td><?php echo JText::_('SURNAME'); ?></td>
                </tr>
                <tr>
                    <td>{TITLE_AFTER}</td>
                    <td><?php echo JText::_('TITLE_AFTER'); ?></td>
                </tr>									
                <tr>
                    <td>{MORE_NAMES}</td>
                    <td><?php echo JText::_('MORE_CUSTOMERS'); ?></td>
                </tr>									                
                <tr class="row1">
                    <td>{COMPANY}</td>
                    <td><?php echo JText::_('COMPANY'); ?></td>
                </tr>
                <tr>
                    <td>{EMAIL}</td>
                    <td><?php echo JText::_('JGLOBAL_EMAIL'); ?></td>
                </tr>
                <tr class="row1">
                    <td>{ADDRESS}</td>
                    <td><?php echo JText::_('FULL_ADDRESS'); ?></td>
                </tr>
                <tr>
                    <td>{STREET}</td>
                    <td><?php echo JText::_('STREET'); ?></td>
                </tr>
                <tr class="row1">
                    <td>{CITY}</td>
                    <td><?php echo JText::_('CITY'); ?></td>
                </tr>
                <tr>
                    <td>{COUNTRY}</td>
                    <td><?php echo JText::_('COUNTRY'); ?></td>
                </tr>
                <tr class="row1">
                    <td>{ZIP}</td>
                    <td><?php echo JText::_('ZIP'); ?></td>
                </tr>								
                <tr>
                    <td>{TELEPHONE}</td>
                    <td><?php echo JText::_('TELEPHONE'); ?></td>
                </tr>
                <tr class="row1">
                    <td>{FAX}</td>
                    <td><?php echo JText::_('FAX'); ?></td>
                </tr>
                <tr>
                    <td>{PAYMENT}</td>
                    <td><?php echo JText::_('PAYMENT_METHOD'); ?></td>
                </tr>
                <tr class="row1">
                    <td>{PAYMENT_INFO}</td>
                    <td><?php echo JText::_('PAYMENT_METHOD_INFO'); ?></td>
                </tr>
                <tr>
                    <td>{NOTE}</td>
                    <td><?php echo JText::_('CUSTOMER_NOTE'); ?></td>
                </tr>
                <?php echo $this->loadTemplate('fields'); ?>
            </tbody>
        </table>
    </fieldset>
</form>
<?php
/**
 * @package        ARTIO Booking
 * @subpackage		views
 * @copyright	  	Copyright (C) 2014 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */
defined('_JEXEC') or die;

$config = AFactory::getConfig();
$user = JFactory::getUser();

/* @var $this BookingViewOverview */

echo $this->loadTemplate('js');
?>
<div class="<?php echo $this->css; ?>" id="bookingOverview">
    <?php if ($this->h1) { ?>
        <h1><?php echo $this->h1; ?></h1>
    <?php } ?>
    <div class="header">
        <a href="<?php echo $this->weekRoute; ?>" class="switchView">
            <?php echo JText::_('WEEK_VIEW'); ?>
        </a>        
        <h2><?php echo $this->current->title; ?></h2>
        <div class="navigator">
            <span class="prevMonth" id="prevMonth">&lt;&lt;</span>
            <span class="prevDay" id="prevDay">&lt;</span>
            <strong class="currentDay">
                <?php echo $this->navigator->currentDay; ?>
            </strong>
            <span class="nextDay" id="nextDay">&gt;</span>
            <span class="nextMonth" id="nextMonth">&gt;&gt;</span>
        </div>
        <ul class="parents">
            <?php foreach ($this->hourlyParents as $parent) { ?>
                <li id="parent<?php echo $parent->id; ?>" class="parent">
                    <?php echo $parent->title; ?>
                </li>
            <?php } ?>
        </ul>
        <div class="clr"></div>
    </div>
    <table class="items">
        <thead>
            <tr class="schedule">
                <td><?php echo JText::sprintf('WEEK_NUMBER', $this->navigator->currentWeek); ?></td>
                <?php foreach ($this->hourlySchedule as $hour) { ?>
                    <td><?php echo $hour; ?></td>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->children as $children) { ?>
                <tr>
                    <td class="title">
                        <?php if ($this->params->get('refer_item_detail')) { ?>
                            <a href="<?php echo JRoute::_(ARoute::view(VIEW_SUBJECT, $children->id, $children->alias)) ?>">
                                <?php echo $children->title; ?>
                            </a>
                            <?php
                        } else {
                            echo $children->title;
                        }
                        ?>
                    </td>
                    <?php
                    foreach ($this->hourlySchedule as $i => $hour) {
                        ?>
                        <td class="hour">
                            <?php if (!$i && !empty($this->hourlyReservations[$children->id])) { ?>
                                <div class="reservations">
                                    <?php foreach ($this->hourlyReservations[$children->id] as $reservation) {
                                        if ($user->authorise('booking.reservations.manage', 'com_booking.subject.' . $reservation->subject)) { ?>
                                            <a class="reservation hasTooltip" up="<?php echo $reservation->up; ?>" down="<?php echo $reservation->down; ?>" title="<?php echo JHtml::tooltipText(BookingHelper::formatName($reservation), ($config->showNoteInCalendar ? JString::trim($reservation->message.' '.$reservation->note) : '')); ?>" href="<?php echo JRoute::_(ARoute::view(VIEW_RESERVATION, null, null, array('cid[]' => $reservation->rid))); ?>">
                                                <?php echo BookingHelper::formatName($reservation); ?>
                                            </a>
                                        <?php } else { ?>
                                            <span class="reservation hasTooltip" up="<?php echo $reservation->up; ?>" down="<?php echo $reservation->down; ?>" title="<?php echo JHtml::tooltipText(BookingHelper::formatName($reservation), ($config->showNoteInCalendar ? JString::trim($reservation->message.' '.$reservation->note) : '')); ?>">
                                                <?php echo BookingHelper::formatName($reservation); ?>
                                            </span>                                   
                                    <?php } 
                                    } ?>
                                </div>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
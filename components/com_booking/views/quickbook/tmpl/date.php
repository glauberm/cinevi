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

defined('_JEXEC') or die('Restricted access');

ob_clean();

$config = AFactory::getConfig();

$this->setting = new BookingCalendarSetting();
$this->days = &BookingHelper::getMonthlyCalendar($this->subject, $this->setting, 1, false);

?>
<table id="quickbook" class="category">
	<thead>
		<tr>
			<th class="previous" onclick="QuickBook.month(<?php echo $this->setting->previousMonth; ?>, <?php echo $this->setting->previousYear; ?>)" title="<?php echo JText::_('PREVIOUS_MONTH'); ?>">&lt;</th>
			<th class="month" colspan="5"><?php echo $this->setting->monthName; ?>
			</th>
			<th class="next" id="next" onclick="QuickBook.month(<?php echo $this->setting->nextMonth; ?>, <?php echo $this->setting->nextYear; ?>)" title="<?php echo JText::_('NEXT_MONTH'); ?>">&gt;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
		<?php 
			foreach ($this->days->calendar as $i => $day) {
				/* @var $day BookingDay */
                $style = $text = '';
				$day->off = date('m', $day->Uts) != $this->setting->month;
				if ($day->off) {
					$class = ' off';
					$title = '';
				} elseif($day->engaged) {
					$class = ' special';
					$title = JText::_('DAY_ENGAGED');
				} elseif($day->closed) {
                    foreach ($day->boxes as $box) {
                        if (!$box->closed) {
                            $day->closed = false;
                            break;
                        }
                    }
                    if ($day->closed) {
                        if ($day->closignDayShow) {
                            $text = '<br>'.$day->closingDayTitle;
                        } else {
                            $class = ' special hasTip';
                            $title = $this->escape($day->closingDayTitle).'::'.$this->escape($day->closignDayText);
                        }
                        if ($day->closignDayColor) {
                            $style = 'style="background-color: #'.$day->closignDayColor.'"';
                        }
                    } else {
                        $class = '';
                        $title = JText::_('BOOK_HOUR_THIS_DAY');
                    }
				} else { 
					$class = '';
					$title = JText::_('BOOK_HOUR_THIS_DAY');
				}
		?>
				<td class="day<?php echo $class; ?>" title="<?php echo $title; ?>">
					<?php if (!$day->off && !$day->closed) { ?>
					 	<?php if (!$day->engaged) { ?>
							<a id="d<?php echo $day->Uts; ?>" href="javascript:QuickBook.day(<?php echo date('j', $day->Uts); ?>, <?php echo date('m', $day->Uts); ?>, <?php echo date('Y', $day->Uts); ?>, 'd<?php echo $day->Uts; ?>')"><?php echo date('j', $day->Uts); ?></a>
						<?php } else { ?>
							<a href="javascript:void(0)"><?php echo date('j', $day->Uts); ?></a>
						<?php } ?>
					<?php } else { ?>
						<span  <?php echo $style; ?>><?php echo date('j', $day->Uts).$text; ?></span>
					<?php } ?>
				</td>
		<?php		
				if ($i % 7 == 6 && count($this->days->calendar) > $i + 1) { // end of week and next week is coming
		?>
					</tr><tr>
		<?php 
				}
			} 
		?>
		</tr>
	</tbody>
</table>
<?php die(); ?>
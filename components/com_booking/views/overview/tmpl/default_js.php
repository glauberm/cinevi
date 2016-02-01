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

/* @var $this BookingViewOverview */
?>
<script type="text/javascript">
    // <![CDATA[
    try {
        window.addEvent('domready', function() {
            BookingOverview.firstHour = '<?php echo addslashes(reset($this->hourlySchedule)); ?>';
            BookingOverview.lastHour = '<?php echo addslashes(end($this->hourlySchedule)); ?>';
            BookingOverview.navigateURL = '<?php echo addslashes(JRoute::_('index.php?option=com_booking&view=overview&tmpl=component&ajax=1&layout=' . $this->getLayout() . '&Itemid=' . JRequest::getInt('Itemid'), false)); ?>';
            BookingOverview.currentDate = '<?php echo addslashes($this->navigator->currentDate); ?>';
            BookingOverview.singleWeek = <?php echo $this->singleWeek ? 'true' : 'false'; ?>;
            BookingOverview.init();

            document.id('prevMonth').addEvent('click', function() {
                BookingOverview.navigate('<?php echo addslashes($this->navigator->prevMonth); ?>', <?php echo (int) $this->current->id; ?>)
            });
            if (!BookingOverview.singleWeek) {
                document.id('prevDay').addEvent('click', function() {
                    BookingOverview.navigate('<?php echo addslashes($this->navigator->prevDay); ?>', <?php echo (int) $this->current->id; ?>)
                });
                document.id('nextDay').addEvent('click', function() {
                    BookingOverview.navigate('<?php echo addslashes($this->navigator->nextDay); ?>', <?php echo (int) $this->current->id; ?>)
                });
            } else {
                document.id('prevWeek').addEvent('click', function() {
                    BookingOverview.navigate('<?php echo addslashes($this->navigator->prevWeek); ?>', <?php echo (int) $this->current->id; ?>)
                });
                document.id('nextWeek').addEvent('click', function() {
                    BookingOverview.navigate('<?php echo addslashes($this->navigator->nextWeek); ?>', <?php echo (int) $this->current->id; ?>)
                });
            }
            document.id('nextMonth').addEvent('click', function() {
                BookingOverview.navigate('<?php echo addslashes($this->navigator->nextMonth); ?>', <?php echo (int) $this->current->id; ?>)
            });
            document.getElements('#bookingOverview *[id^=parent]').each(function(e) {
                var match = e.id.match(/^parent([1-9]+[0-9]*)$/);
                if (match) {
                    e.addEvent('click', function() {
                        BookingOverview.navigate('<?php echo $this->navigator->currentDate; ?>', match[1])
                    });
                }
            });
        });
    } catch (e) {
        console.log(e.message);
    }
    // ]]>
</script>
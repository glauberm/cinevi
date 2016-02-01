<?php
/**
 * Default Manager Voucher
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

$config = AFactory::getConfig();
?>
<h1><?php echo JText::sprintf('RESERVATION_NUM', $this->reservation->id); ?></h1>
<?php
$name = BookingHelper::formatName($this->reservation);
$company = JString::trim($this->reservation->company);

if ($name || $company || count($this->reservation->fields) || $this->reservation->company_id || $this->reservation->vat_id) {
    ?>

    <h2><?php echo JText::_('CUSTOMER'); ?></h2>
    <table>
        <?php
        if ($config->fieldsPosition == 0) {
            foreach ($this->getCustomFields() as $field) {
                ?>		 
                <tr>
                    <td><?php echo $field['title']; ?>: </td>
                    <td><?php echo AUtils::getArrayValue($this->reservation->fields, $field['name'] . '.value'); ?></td>
                </tr>
                <?php
            }
        }
        if ($name) {
            ?>
            <tr>
                <td><?php echo JText::_('NAME'); ?>: </td>
                <td><?php echo $name; ?></td>
            </tr>
            <?php
        }
        if ($company) {
            ?>
            <tr>
                <td><?php echo JText::_('COMPANY'); ?>: </td>
                <td><?php echo $company; ?></td>
            </tr>
            <?php
        }
        if ($this->reservation->company_id) {
            ?>
            <tr>
                <td><?php echo JText::_('COMPANY_ID'); ?>: </td>
                <td><?php echo $this->reservation->company_id; ?></td>
            </tr>
            <?php
        }
        if ($this->reservation->vat_id) {
            ?>
            <tr>
                <td><?php echo JText::_('VAT_ID'); ?>: </td>
                <td><?php echo $this->reservation->vat_id; ?></td>
            </tr>
            <?php
        }
        if ($config->fieldsPosition == 1) {
            foreach ($this->getCustomFields() as $field) {
                ?>		 
                <tr>
                    <td><?php echo $field['title']; ?>: </td>
                    <td><?php echo AUtils::getArrayValue($this->reservation->fields, $field['name'] . '.value'); ?></td>
                </tr>
                <?php
            }
        }
        ?>
    </table>

    <?php if (!empty($this->reservation->more_names)) { ?>

        <h2><?php echo JText::_('MORE_CUSTOMERS'); ?></h2>
        <table>
            <?php foreach ($this->reservation->more_names as $name) { ?>
                <tr>
                    <td></td>
                    <td><?php echo $name; ?></td>
                </tr>
            <?php } ?>
        </table>

        <?php
    }
}

foreach ($this->reservedItems as $reservedItem) {
    TableReservationItems::display($reservedItem);
    $subject = $this->subjects[$reservedItem->subject];
    $capacity = $subject->display_capacity || $subject->total_capacity > 1 || $reservedItem->capacity > 1;
    ?>
    <h2><?php
        echo $reservedItem->subject_title;
        if ($this->isAdmin) {
            echo $reservedItem->sub_subject_title;
        }
        ?></h2>
    <p><?php echo $subject->introtext . ' ' . $subject->fulltext; ?></p>
    <table>
        <?php if ($capacity) { ?>
            <tr>	
                <td><?php echo JText::_('CAPACITY'); ?>: </td>
                <td><?php echo number_format($reservedItem->capacity, 0, '', ' '); ?></td>
            </tr>
            <?php
        }
        foreach ($reservedItem->occupancy as $occupancy) {
            ?>
            <tr>
                <td><?php echo $occupancy['title']; ?>: </td>
                <td><?php echo $occupancy['count']; ?></td>
            </tr>
            <?php
        }
        foreach ($reservedItem->supplements as $supplement) {
            /* @var $supplement TableReservationSupplement */
            if ($supplement->type != SUPPLEMENT_TYPE_MANDATORY) {
                ?>				
                <tr>
                    <td><?php echo $supplement->title; ?>: </td>
                    <td><?php
                        if ($supplement->type == SUPPLEMENT_TYPE_YESNO) {
                            echo $supplement->value ? JText::_('JYES') : JText::_('JNO');
                        } elseif ($supplement->type == SUPPLEMENT_TYPE_LIST) {
                            echo $supplement->value;
                        }
                        if ($supplement->capacity > 1) {
                            echo ' (' . JText::_('CAPACITY') . ' ' . $supplement->capacity . ')';
                        }
                        ?></td>
                </tr>
                <?php
            }
        }
        if ($reservedItem->message) {
            ?>
            <tr>	
                <td><?php echo JText::_('Message'); ?></td>
                <td><?php echo htmlspecialchars($reservedItem->message); ?></td>
            </tr>
        <?php } ?>
    </table>

    <?php
}

$adrress = BookingHelper::formatAddress($this->reservation);
$email = BookingHelper::getEmailLink($this->reservation);
$telephone = JString::trim($this->reservation->telephone);
$fax = JString::trim($this->reservation->fax);
$note = JString::trim($this->reservation->note);

if ($adrress || $email || $telephone || $fax || $note) {
    ?>
    <h2><?php echo JText::_('CONTACT'); ?></h2>
    <table>
        <?php
        if ($adrress) {
            ?>
            <tr>	
                <td><?php echo JText::_('ADRRESS'); ?>: </td>
                <td><?php echo $adrress; ?></td>
            </tr>
            <?php
        }
        if ($email) {
            ?>
            <tr>	
                <td><?php echo JText::_('EMAIL'); ?>: </td>
                <td><?php echo $email; ?></td>
            </tr>
            <?php
        }
        if ($telephone) {
            ?>
            <tr>	
                <td><?php echo JText::_('PHONES'); ?>: </td>
                <td><?php echo $telephone; ?></td>
            </tr>
            <?php
        }
        if ($fax) {
            ?>
            <tr>	
                <td><?php echo JText::_('FAX'); ?>: </td>
                <td><?php echo $fax; ?></td>
            </tr>
            <?php
        }
        if ($note) {
            ?>
            <tr>	
                <td><?php echo JText::_('NOTE'); ?>: </td>
                <td><?php echo $note; ?></td>
            </tr>
            <?php
        }
        ?>		    			
    </table>
    <?php
}

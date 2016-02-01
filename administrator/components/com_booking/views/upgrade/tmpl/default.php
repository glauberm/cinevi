<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('CHECK_UPDATES'), 'upgrade');

JToolBarHelper::divider();
if (JFactory::getUser()->authorise('core.admin', 'com_booking'))
	JToolBarHelper::preferences('com_booking');

BookingHelper::setSubmenu(7);

$params = JComponentHelper::getParams(OPTION);

$artioDownloadId = JString::trim($params->get('artioDownloadId'));

$this->regInfo = $this->get('RegisteredInfo');

$this->newVer = $this->get('NewestVersion');
$data = JInstaller::parseXMLInstallFile(MANIFEST);
$this->oldVer = $data['version'];

$needConfirm = ($artioDownloadId && (is_null($this->regInfo) || ($this->regInfo->code != 10)));

$downloadPaid = true;

ADocument::addScriptPropertyDeclaration('BookingNeedConfirm', $needConfirm ? true : false);
ADocument::addScriptPropertyDeclaration('BookingTxtConfirm', JText::_('YOU_WILL_OBTAIN_THE_NON_PAID_VERSION_OF_BOOKIT_ARE_YOU_SURE_YOU_WANT_TO_USE_THE_AUTOMATIC_UPGRADE_FROM_SERVER'));

?>
<div class="width-100">
<fieldset class="adminform">
	<legend><?php echo JText::_('CHECK_UPDATES'); ?></legend>
	<table class="adminform">
		<tr>
    		<th colspan="2"><?php echo JText::_('VERSION_INFO'); ?></th>
		</tr>
		<tr>
    		<td width="20%"><?php echo JText::_('INSTALLED_VERSION').':'; ?></td>
    		<td><?php echo $this->oldVer; ?></td>
		</tr>
		<tr>
    		<td><?php echo JText::_('NEWEST_VERSION').':'; ?></td>
    		<td><?php echo $this->newVer; ?></td>
		</tr>
		<?php 
			if ($this->oldVer < $this->newVer && $this->newVer != '?.?.?') {
				$changelog = JString::trim(JFile::read('http://www.artio.cz/updates/joomla/booking2/changelog'));
				if ($changelog) {
		?>
					<tr>
						<td><?php echo JText::_('CHANGES_IN_LAST_VERSION').':'; ?></td>
    					<td><?php echo $changelog; ?></td>
    				</tr>
		<?php			
				}
			}
		?>
	</table>
<?php $available = false; ?>
<?php if ($artioDownloadId) { ?>
    <table class="adminform">
    	<tr>
        	<th colspan="2"><?php echo JText::_('REGISTRATION_INFO'); ?></th>
    	</tr>
    	<?php if (is_null($this->regInfo)) { ?>
	        <tr>
	            <td colspan="2"><?php echo JText::_('COULD_NOT_RETRIEVE_REGISTRATION_INFORMATION'); ?></td>
	        </tr>
        <?php } else if ($this->regInfo->code == 90) { ?>
        	<tr>
            	<td colspan="2"><?php echo JText::_('DOWNLOAD_ID_WAS_NOT_FOUND_IN_OUR_DATABASE'); ?></td>
        	</tr>
        <?php } else {
        	$regTo = $this->regInfo->name;
        	if (! empty($this->regInfo->company))
            	$regTo .= ', ' . $this->regInfo->company;
        	if ($this->regInfo->code == 10)
            	$available = true;
        ?>
        <tr>
            <td width="20%"><?php echo JText::_('REGISTERED_TO'); ?>:</td>
            <td><?php echo $regTo; ?></td>
        </tr>
        <?php
	        if ($this->regInfo->code == 10 || $this->regInfo->code == 30) {
	            $dateText = JText::_('FREE_UPGRADES_AVAILABLE_UNTIL');
	        } elseif ($this->regInfo->code == 20) {
	            $dateText = JText::_('FREE_UPGRADES_EXPIRED');
	        }
        ?>
        <tr>
            <td><?php echo $dateText; ?>:</td>
            <td><?php echo $this->regInfo->date; ?></td>
        </tr>
        <?php } ?>
    </table>
    <?php } ?>

<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm">
<?php
	if ((strnatcasecmp($this->newVer, $this->oldVer) > 0) ||
		(strnatcasecmp($this->newVer, substr($this->oldVer, 0, strpos($this->oldVer, '-'))) == 0) ||
		($this->newVer == "?.?.?"))
	{
        $btnText = JText::_('UPGRADE_FROM_ARTIO_SERVER');
	} elseif ($this->newVer == $this->oldVer) {
    	$btnText = JText::_('REINSTALL_FROM_ARTIO_SERVER');
	}
	
	if ($available)
	{
?>
	    <table class="adminform">
	        <tr>
	            <th><?php echo $btnText; ?></th>
	        </tr>
	        <tr>
	            <td>
	                   <?php
	                   if ($this->newVer == '?.?.?') {
	                       echo JText::_('SERVER_NOT_AVAILABLE');
	                   } else { ?>
	                       <input class="button" type="button" value="<?php echo $btnText; ?>" onclick="BookingUpdate.submitbutton()" />
	                   <?php } ?>
	            </td>
	        </tr>
	    </table>
	<?php } ?>
		<table class="adminform">
			<tr>
		    	<th colspan="2"><?php echo JText::_('UPLOAD_PACKAGE_FILE'); ?></th>
			</tr>
			<tr>
		    	<td width="120">
		        	<label for="install_package"><?php echo JText::_('PACKAGE_FILE'); ?>:</label>
		    	</td>
		    	<td>
		        	<input class="input_box" id="install_package" name="install_package" type="file" size="57" />
		        	<input class="button" type="submit" value="<?php echo JText::_('UPLOAD_FILE'); ?> &amp; <?php echo JText::_('INSTALL'); ?>" />
		    	</td>
			</tr>
		</table>

	<input type="hidden" name="option" value="<?php echo OPTION; ?>" />
	<input type="hidden" name="task" value="doUpgrade" />
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_UPGRADE; ?>" />
	<input type="hidden" name="fromserver" value="0" />
	<input type="hidden" name="ext" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>
</fieldset>
</div>

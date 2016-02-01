<?php
/**
 * View component configuration 
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


JHTML::_('behavior.modal');
//JHTML::_('behavior.mootools');
JHTML::_('behavior.framework');
JHtml::script('administrator/components/com_booking/assets/colorpicker/jscolor.js');
bookingHelper::upgradeModal(); //we need better modal to open queezebox from hidden div

/* @var $this BookingViewConfig */
JToolBarHelper::title(JText::_(COMPONENT_NAME).": ".JText::_('CONFIGURATION'), 'configuration');

JToolBarHelper::save();
JToolBarHelper::apply();
JToolBarHelper::cancel();

BookingHelper::setSubmenu(4);

/*
$tabsParams['startOffset'] = isset($_COOKIE['startOffset']) ? $_COOKIE['startOffset'] : 0;
$pane = &JPanel::getInstance('tabs', $tabsParams, true);
*/

ADocument::addDomreadyEvent('ViewConfig.setEvents(true);');

$array = $this->params->toArray();

$this->form = new JForm('form');

//load config and mask it as form
$xmlstr = file_get_contents(CONFIG); //Storing the xml file content to $xmlstr
$xmlstr = str_replace('<config','<form',$xmlstr);
$xmlstr = str_replace('</config>','</form>',$xmlstr);

//$xmlstr = str_replace(' name="',' name="params',$xmlstr);
$xmlstr = str_replace('<fields name="config">','<fields name="params">',$xmlstr);

$xml = new SimpleXMLElement($xmlstr);

$this->form->load($xml);
//$this->form->bind($array);
foreach($array as $k=>$v)
{
	$this->form->setValue($k, 'params', $v);
}
$this->form->setValue('asset_id', null, $array['asset_id']);

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">	
	<div>
	
	<!-- compatability with parameter.js (because is not called JParameter.render()) -->
	<table><tbody id="paramlist"><tr><td></td></tr></tbody></table>

			<!--  <legend><?php echo JText::_('COMPONENT_SETTINGS'); ?></legend>		-->
			<?php
				echo JHtml::_('tabs.start', 'tabone', array('useCookie' => true));
				foreach ($this->form->getFieldsets() as $name=>$fieldset) {
					
					//if cant edit acl, dont show tab
					if("permissions" == strtolower($name))
						if(!JFactory::getUser()->authorise('core.admin', 'com_booking'))
							continue;
					
					//if isnt admin, dont show artio download id
					if("registration" == strtolower($name)){
						AImporter::model('userconfig');
						if(AUser::onlyOwner())
							continue;
					}
					
					echo JHtml::_('tabs.panel', JText::_($fieldset->label), $name);
			?>
				<div class="ieHelper">&nbsp;</div>
				<div>
				<table class="admintable config">
			<?php
					foreach ($this->form->getFieldset($fieldset->name) as $field) {
						/* @var $field JFormField */
						if ($field->__get('labelClass') == 'hide') {
			?>
							<tr id="<?php echo $field->id;?>_tr">
								<td colspan="2">
									<?php echo $field->input; ?>
								</td>
							</tr>		
			<?php		
						} else { 
			?> 
							<tr id="<?php echo $field->id;?>_tr">
								<td valign="top" class="key">
									<?php echo $field->label; ?>
								</td>
								<td>
									<?php echo $field->input; ?>
								</td>
							</tr>
		  	<?php 
		  				}
			        }   
			?>
				</table>
				</div>
			<?php	
				}
				echo JHtml::_('tabs.end');
			?>
			<div class="clr"></div>

	</div>
	
	<script type="text/javascript">
		function display_formats()
		{
			SqueezeBox.initialize();
			SqueezeBox.open(document.id('supported_time_formats'),{handler: 'adopt'});
		}
	</script>
	
	<div style="display:none">
		<div id="supported_time_formats">
			<![CDATA[
				<?php echo AHTML::getSupportedTimeFormats(); ?>
			]]>
		</div>
	</div>
	
	<input type="hidden" name="option" value="<?php echo OPTION; ?>" />
	<input type="hidden" name="controller" value="<?php echo CONTROLLER_CONFIG; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>
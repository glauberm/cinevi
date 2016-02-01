<?php

/**
 * Google services edit form template.
 * 
 * @package		ARTIO Booking
 * @subpackage  views
 * @copyright	Copyright (C) ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 */

defined('_JEXEC') or die('Restricted access');

/* @var $this BookingViewSubject */

ADocument::addDomreadyEvent('EditSubject.prepareGoogleMaps()');

?>
<div class="width-100">
	<fieldset class="adminform">
    	<legend class="hasTip" title="<?php echo $this->escape(JText::_('GOOGLE_MAPS')) . '::' . $this->escape(JText::_('GOOGLE_MAPS_INFO')); ?>">
    		<?php echo JText::_('GOOGLE_MAPS'); ?>
    	</legend>
    	<div class="col width-50">
    		<table class="admintable width-100">
				<tr>
    				<td class="key"><label for="google_maps"><?php echo JText::_('TYPE'); ?></label></td>
    				<td>
    				<?php 
						$options = array(JHTML::_('select.option', JText::_('JOFF'), 'off'));    				
						$options[] = JHTML::_('select.option', JText::_('ADDRESS'), 'address');
						$options[] = JHTML::_('select.option', strip_tags(JText::_('CODE')), 'code');
						echo JHTML::_('select.genericlist', $options, 'google_maps', null, 'text', 'value', $this->subject->google_maps);
    				?>
    				</td>
    			</tr>
    			<tr>
    				<td class="key"><label for="google_maps_display"><?php echo JText::_('DISPLAY'); ?></label></td>
    				<td>
    				<?php 
						$options = array(JHTML::_('select.option', JText::_('ON_PAGE'), 'page'));    				
						$options[] = JHTML::_('select.option', JText::_('AT_LIGHTBOX'), 'lightbox');
						echo JHTML::_('select.genericlist', $options, 'google_maps_display', null, 'text', 'value', $this->subject->google_maps_display);
    				?>
    				</td>
    			</tr>
    			<tr>
    				<td class="key"><label for="google_maps_address"><?php echo JText::_('ADDRESS'); ?></label></td>
    				<td>
    					<textarea name="google_maps_address" id="google_maps_address" style="width: 400px" rows="2" cols="10"><?php echo $this->subject->google_maps_address; ?></textarea>
    				</td>
    			</tr>
    			<tr>
    				<td class="key"><label for="google_maps_width"><?php echo JText::_('WIDTH'); ?></label></td>
    				<td>
    					<input type="text" name="google_maps_width" id="google_maps_width" value="<?php echo $this->subject->google_maps_width; ?>" /> 
    				</td>
    			</tr>
    			<tr>
    				<td class="key"><label for="google_maps_heigth"><?php echo JText::_('HEIGHT'); ?></label></td>
    				<td>
    					<input type="text" name="google_maps_heigth" id="google_maps_heigth" value="<?php echo $this->subject->google_maps_heigth; ?>" /> 
    				</td>
    			</tr>
    			<tr>
    				<td class="key"><label for="google_maps_zoom"><?php echo JText::_('ZOOM'); ?></label></td>
    				<td>
    					<?php 
    						if (!($this->subject->google_maps_zoom > 0 && $this->subject->google_maps_zoom < 21))
    							$this->subject->google_maps_zoom = 17;
    						$options = array();
    						for ($i = 20; $i > 0; $i--) $options[] = JHTML::_('select.option', $i, $i);    				
							echo JHTML::_('select.genericlist', $options, 'google_maps_zoom', '', 'text', 'value', $this->subject->google_maps_zoom);
    					?>
    				</td>
    			</tr>
    			<tr>
    				<td class="key"><label for="google_maps_code"><?php echo JText::_('CODE'); ?></label></td>
    				<td>
    					<textarea name="google_maps_code" id="google_maps_code" style="width: 400px" rows="10" cols="60"><?php echo $this->escape($this->subject->google_maps_code); ?></textarea>
    				</td>
    			</tr>
    		</table>
    	</div>
    	<div>
    	<?php
    	if ($this->subject->google_maps == 'address') { // search map through real address
			require_once JPATH_COMPONENT_SITE . '/assets/libraries/googlemaps/GoogleMapCurl.php';
			require_once JPATH_COMPONENT_SITE . '/assets/libraries/googlemaps/JSMin.php';
			$googleMap = new GoogleMapCurlAPI();
			$googleMap->width = $this->subject->google_maps_width . 'px';
			$googleMap->height = $this->subject->google_maps_heigth . 'px';
			$googleMap->zoom = $this->subject->google_maps_zoom; 
			$googleMap->addMarkerByAddress($this->subject->google_maps_address);
			$doc = JFactory::getDocument();
			/* @var $doc JDocumentHTML */
			$doc->addCustomTag($googleMap->getHeaderJS() . $googleMap->getMapJS()); // add map js into page head
			echo $googleMap->printOnLoad() . $googleMap->printMap() . $googleMap->printSidebar(); 
		} elseif ($this->subject->google_maps == 'code') // display predefined map
			echo $this->subject->google_maps_code;
    	?>
    	</div>
    </fieldset>
</div>
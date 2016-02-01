<?php 

/**
 * Subject detail properties template.
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

// check localisation
$isObjectList = $this instanceof BookingViewSubjects;
$isObjecDetail = $this instanceof BookingViewSubject;

// check if is something to do, there is at least one param to display on object's list or object detail
$display = $icons = false;
if(is_array($this->propertiesParams)){
	foreach ($this->propertiesParams as $key => $param) {	
		$this->propertiesParams[$key][PARAM_DISPLAY] = ($param[PARAM_OBJECTS] && $isObjectList) || ($param[PARAM_OBJECT] && $isObjecDetail);
		if ($this->propertiesParams[$key][PARAM_DISPLAY]) {
			$display = true; // one param to display
			if ($param[PARAM_ICON]) // check if there is at least one param with icon
				$icons = true; // one param with icon
		} 
	}
}

if ($display) {
	 
	$rpath = AImage::getRIPath($config->templatesIcons);
			
	if ($this->displayProperties == DISPLAY_PROPERTIES_ICON && $icons) { // list of icons - only if one of visible properties has icon
?>
		<div class="properties">
			<div class="icons">
<?php			
				foreach ($this->propertiesParams as $param) {
					if ($param[PARAM_DISPLAY] && $param[PARAM_ICON]) {
						$titles = array();
						$label = JString::trim(ATemplate::translateParam($param[PARAM_LABEL]));
						$value = JString::trim(ATemplate::displayParamValue($param));
						if ($label) $titles[] = $this->escape($label);
						if ($value) $titles[] = $this->escape($value);
						if (empty($titles)) {
?>					
							<span class="icon"><img src="<?php echo $rpath . $icon; ?>" alt="" /></span>
<?php
						} else {
?>
							<span class="icon hasTip" title="<?php echo implode('::', $titles); ?>"><img src="<?php echo $rpath . $param[PARAM_ICON]; ?>" alt="" /></span>
<?php							
						}
					}
				}
?>
			</div>
		</div>
<?php						
	} elseif($this->displayProperties == DISPLAY_PROPERTIES_TABLE) { // table layout
?>
		<div class="properties">
      		<table>
<?php							
				foreach ($this->propertiesParams as $param) {
					if ($param[PARAM_DISPLAY]) { 
?>				 		
						<tr>
<?php					 
							if ($icons) { 
?>						
								<th>
<?php						 
					    			if ($param[PARAM_ICON]) {
?>					    
										<img src="<?php echo $rpath . $param[PARAM_ICON]; ?>" alt="" />
<?php						 
									}
?>
								</th>
<?php									
							}
?>					 
							<th><?php echo ATemplate::translateParam($param[PARAM_PARAMLABEL]); ?></th> 
							<td><?php echo ATemplate::displayParamValue($param); ?></td> 
						</tr>
<?php					 
					}
				}
?>
			</table>
		</div>
<?php 
	} elseif($this->displayProperties == DISPLAY_PROPERTIES_TEXTS) {
?>
		<div class="properties">
			<ul>
<?php		
				foreach ($this->propertiesParams as $param) {
					if ($param[PARAM_DISPLAY]) {
?>
						<li>
							<span class="title"><?php echo ATemplate::translateParam($param[PARAM_LABEL]); ?>:</span> 
							<span class="text"><?php echo ATemplate::displayParamValue($param); ?></span>
						</li>
<?php		
					}
				}
?>
			</ul>
		</div>
<?php			
	}
} 
?>	
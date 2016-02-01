<?php

/**
 * Subjects list template.
 * 
 * @package		ARTIO Booking
 * @copyright	Copyright (C) ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

/* @var $this BookingViewSubjects */

$titleDisplaySubject = JText::_('DISPLAY_OBJECT_S');
$reserve = JText::_('MAKE_RESERVATION');

$config = AFactory::getConfig();
/* @var $config BookingConfig */
$language = JFactory::getLanguage();

$this->addTemplatePath(JPATH_COMPONENT_SITE . '/views/subject/tmpl');

if ($config->popupType == 'shadowbox') {
	BookingHelper::importShadowBox();
}

?>
<form name="list" id="list" action="<?php echo JRoute::_(ARoute::view(VIEW_SUBJECTS, JRequest::getVar('id'))); ?>" method="post">
	<?php if ($this->subject) { // parent subject title and description ?>
		<?php if (JString::trim($this->subject->title)) { ?>
			<h1><?php echo $this->subject->title; ?></h1>
		<?php } ?>
		<?php if (JString::trim($this->subject->introtext)) { ?>
			<div class="introtext"><?php echo $this->subject->introtext; ?></div>
		<?php } ?>
	<?php 
		}
		if ($config->locations == 1) {
			echo AHtml::locations(true);
		}
		if ($config->displayFilter && count($this->filterables) > 0) {
	?>
			<div class="filter">
				<div class="toolbar">
					<span class="label"><?php echo JText::_('FILTER'); ?></span>
					<span class="submit" onclick="document.id('list').submit();" title="<?php echo JText::_('SUBMIT'); ?>"><img src="<?php echo JURI::base().'/components/com_booking/assets/images/icon-r-apply.png'; ?>" alt="<?php echo JText::_('SUBMIT'); ?>" /></span>
					<span class="reset"  onclick="document.id('list').reset.value=1;document.id('list').submit();" title="<?php echo JText::_('RESET'); ?>"><img src="<?php echo JURI::base().'/components/com_booking/assets/images/icon-r-cancel.png'; ?>" alt="<?php echo JText::_('RESET'); ?>" /></span>
				</div>
	<?php 
			foreach ($this->filterables as $templateFilterables) {
				if (count($templateFilterables)) {
	?>
					<div class="items">
              			<?php foreach ($templateFilterables as $filterable) { ?>
							<div class="item">
								<label for="<?php echo $filterable[PARAM_REQUESTNAME]; ?>"><?php echo ATemplate::translateParam($filterable[PARAM_PARAMLABEL]); ?></label>
								<?php 
									if ($filterable[PARAM_TYPE] == 'list') {
										$options = array();
										$options[] = JHTML::_('select.option', '', JText::_('SELECT'));
										foreach ($filterable[PARAM_OPTIONS] as $option)
											$options[] = JHTML::_('select.option', $option[0], $option[1]);
										echo JHTML::_('select.genericlist', $options, $filterable[PARAM_REQUESTNAME], 'onchange="this.form.submit()"', 'value', 'text', $filterable[PARAM_REQUESTVALUE]);
									} elseif ($filterable[PARAM_TYPE] == 'text') {
								?>										
										<input type="text" name="<?php echo $filterable[PARAM_REQUESTNAME]; ?>" id="<?php echo $filterable[PARAM_REQUESTNAME]; ?>" onchange="this.form.submit()" value="<?php echo $this->escape($filterable[PARAM_REQUESTVALUE]); ?>" />
								<?php 
									} elseif ($filterable[PARAM_TYPE] == 'checkbox') {  
								?>						
										<input class="checkbox" type="checkbox" name="<?php echo $filterable[PARAM_REQUESTNAME]; ?>" id="<?php echo $filterable[PARAM_REQUESTNAME]; ?>" onclick="this.form.submit()" value="1" <?php echo $filterable[PARAM_REQUESTVALUE] == 1 ? 'checked="checked"' : ''; ?> />
								<?php
									} elseif ($filterable[PARAM_TYPE] == 'radio') {
										foreach ($filterable[PARAM_OPTIONS] as $i => $option) {
								?>
											<input type="radio" name="<?php echo $filterable[PARAM_REQUESTNAME]; ?>" id="<?php echo ($id = 'r' . $i . $filterable[PARAM_REQUESTNAME]); ?>" value="<?php echo $this->escape($option[0]); ?>" <?php echo ($filterable[PARAM_REQUESTVALUE] == $option[0] ? 'checked="checked"' : ''); ?> onclick="this.form.submit()" />
											<label for="<?php echo $id; ?>"><?php echo $option[1]; ?></label>
								<?php
										}		
									} elseif ($filterable[PARAM_TYPE] == 'textarea') {
								?>											
										<textarea name="<?php echo $filterable[PARAM_REQUESTNAME]; ?>" id="<?php echo $filterable[PARAM_REQUESTNAME]; ?>" onchange="this.form.submit()" rows="5" cols="40"><?php echo $filterable[PARAM_REQUESTVALUE]; ?></textarea>
								<?php } ?>
							</div>
						<?php } ?>
						<div class="clr"></div>
					</div>
	<?php 
				}
			} // the end foreach
	?>
		</div>
	<?php } // the end of filter box ?>
	<div class="subjectsList">
		<?php 
			$count = count($this->items);
			if ($count) {
                $custom = array();
                if ($this->lists['date_from'])
                    $custom['pre_from'] = $this->lists['date_from'];
                if ($this->lists['date_to'])
                    $custom['pre_to'] = $this->lists['date_to'];                
				for ($i = 0; $i < $count; $i++) {
					$subject = $this->subjectTable;
					/* @var $subject TableSubject */
					if(array_key_exists($i,$this->items) && $this->items[$i])
						$subject->bind($this->items[$i]);
					$subject->children = $this->items[$i]->children;
					$title = $this->escape(sprintf($titleDisplaySubject, $subject->title));
					$view = $this->modelSubject->haveChilds($subject->id) && $config->listStyle == 0 && $config->parentsBookable != 2 ? VIEW_SUBJECTS : VIEW_SUBJECT;
					$urlView = JRoute::_(ARoute::view($view, $subject->id, $subject->alias, $custom));
					$urlBook = $config->parentsBookable ? JRoute::_(ARoute::view(VIEW_SUBJECT, $subject->id, $subject->alias, $custom)) : $urlView;
					$thumb = null;
		?>
					<div class="subject">
						<?php if ($config->showFlagFeatured && $subject->featured) { ?>
								<strong class="featured"><?php echo JText::_('FEATURED'); ?></strong>
						<?php
							}
							if ($config->enableResponsive && $config->popupType == 'shadowbox' && $subject->image) { 
								$thumb = AImage::thumb(BookingHelper::getIPath($subject->image), $config->subjectThumbWidth, $config->subjectThumbHeight);
								if ($thumb) {
						?>
								<div class="image-right">
									<a class="preview" href="<?php echo $thumb; ?>" rel="shadowbox" title="<?php echo $subject->title; ?>">
										<?php echo JText::_('SHOW_IMAGE'); ?>
									</a>
								</div>
						<?php 
								}
							} elseif ($config->displayThumbs && $subject->image) {
								$thumb = AImage::thumb(BookingHelper::getIPath($subject->image), $config->thumbWidth, $config->thumbHeight);
								if ($thumb) {
						?>
								<div class="image">
									<a class="preview hasTip" href="<?php echo $urlView; ?>" title="<?php echo $title; ?>">
										<img src="<?php echo $thumb; ?>" alt="" />
									</a>
								</div>
						<?php 
								}
							}
							if ($config->displaySubjectsProperties != DISPLAY_PROPERTIES_OFF) {
								$template = $this->templateHelper->getTemplateById($subject->template);
							/*
								$params = json_decode($template->loadObjectParams($subject->id));								
								$params = (array)$params;
								//var_Dump($params);
								$param = null;
								foreach($params as $k=>$v)
								{
									$param[(int)$k] = (string)$v;
								}
								$params = $param;
								
								$f = null;
								if(is_object($template->parser) && $template->parser->fields)
									$f = $template->parser->fields->fieldset->field;
															
								if($f)
								{
									$paramindex = 0;
									foreach($f as $k=>$v)
									{
										//TODO xml node - correct indexing
										$name = (string)$v['name'];
										$template->parser->fields->fieldset->field[$paramindex] = $params[$name];
										$paramindex++;
									}
								}
								*/
	
								$properties = new AParameter($template->loadObjectParams($subject->id), null, $template->parser);
								
        						//$properties = new AParameter($template->loadObjectParams($subject->id), null, $template->parser);
        						$this->propertiesParams = $properties->loadParamsToFields();
        						$this->displayProperties = $config->displaySubjectsProperties;
        						echo $this->loadTemplate('properties');
							} 
						?>
						<h2>
							<a href="<?php echo $urlView; ?>" title="<?php echo $title; ?>" class="hasTip">
								<?php echo $subject->title; ?>
							</a>
						</h2>
						<?php 
							if ($config->displayReadmore) { 
                                if ($config->cropReadmore) {
						?>
                                    <p class="readmore"><?php echo AHtml::getReadmore($subject->introtext, $config->readmoreLength); ?></p>
						<?php
                                } else {
                                    echo $subject->introtext;
                                }
							}
							if ($config->subjectsCalendar && (!$subject->children || $config->parentsBookable)) { // calendars only for non-parents objects
						?>
								<div class="clr"></div><div class="calendars">
									<?php 
    									foreach ($this->months as $cal => $month) {	
    										$calid = 'subject' . $subject->id . 'calendar' . $cal; // unique ID for every instance of calendar
    										// set new instance with ID and skin
    										$js = 'var ' . $calid . ' = new dhtmlXCalendarObject("' . $calid . '","' . $config->subjectsCalendarSkin . '");' . "\n"; 
    										// translate calendar
    										$lang = JString::substr($language->getTag(), 0, 2);
											$js .= $calid . '.lang = "' . $lang . '";' . "\n";
											$js .= $calid . '.langData = {' . "\n";
											$js .= '"' . $lang . '": {' . "\n";
											$js .= 'dateformat: "' . JText::_('DATE_FORMAT_LC4') . '",' . "\n";
											$js .= 'monthesFNames: ["' . JText::_('January', true) . '","' . JText::_('February', true) . '","' . JText::_('March', true) . '","' . JText::_('April', true) . '","' . JText::_('May', true) . '","' . JText::_('June', true) . '","' . JText::_('July', true) . '","' . JText::_('August', true) . '","' . JText::_('September', true) . '","' . JText::_('October', true) . '","' . JText::_('November', true) . '","' . JText::_('December', true) . '"],' . "\n";
											$js .= 'monthesSNames: ["' . JText::_('JANUARY_SHORT', true) . '","' . JText::_('FEBRUARY_SHORT', true) . '","' . JText::_('MARCH_SHORT', true) . '","' . JText::_('APRIL_SHORT', true) . '","' . JText::_('MAY_SHORT', true) . '","' . JText::_('JUNE_SHORT', true) . '","' . JText::_('JULY_SHORT', true) . '","' . JText::_('AUGUST_SHORT', true) . '","' . JText::_('SEPTEMBER_SHORT', true) . '","' . JText::_('OCTOBER_SHORT', true) . '","' . JText::_('NOVEMBER_SHORT', true) . '","' . JText::_('DECEMBER_SHORT', true) . '"],' . "\n";
											$js .= 'daysFNames: ["' . JText::_('SUNDAY', true) . '","' . JText::_('MONDAY', true) . '","' . JText::_('TUESDAY', true) . '","' . JText::_('WEDNESDAY', true) . '","' . JText::_('THURSDAY', true) . '","' . JText::_('FRIDAY', true) . '","' . JText::_('SATURDAY', true) . '"],' . "\n";
											$js .= 'daysSNames: ["' . JText::_('SUN', true) . '","' . JText::_('MON', true) . '","' . JText::_('TUE', true) . '","' . JText::_('WED', true) . '","' . JText::_('THU', true) . '","' . JText::_('FRI', true) . '","' . JText::_('SAT', true) . '"]' . "\n";
											$js .= '}};' . "\n";
    										$js .= $calid . '.hideTime();' . "\n"; // no time
    										if (!empty($this->insensitiveDays[$subject->id])) // disable insensitive (reserved) days 	
    											$js .= $calid . '.setInsensitiveDays(([' . implode(', ', $this->insensitiveDays[$subject->id]) . ']));' . "\n";
    										$js .= $calid . '.setWeekStartDay(' . ($config->firstDaySunday ? 7 : 1) . ');' . "\n";
    										
    										$js .= $calid . '.show();' . "\n"; // display immediately
    										$js .= $calid . '._drawMonth(new Date(' . $month->lstMon . '));' . "\n"; // set displayed month
    										$js .= $calid . '._showSelector = function() { return; };' . "\n"; // disable month and year selector
    										$js .= $calid . '._drawMonth = function() { return; };' . "\n"; // disable month changing
    										$js .= $calid . '._updateCellStyle = function() { return; };' . "\n"; // disable days highlighting
    										ADocument::addDomreadyEvent($js);
											// anchor for element
									?>
											<div id="<?php echo $calid; ?>" class="calendar"></div>
									<?php
    									}	
									?>
									<div class="clr"></div>
								</div>
							<?php 
								}
								if ($config->subjectsWeek) {
									$setting = new BookingCalendarSetting();
									$week = BookingHelper::getWeekCalendar($subject, $setting, $config->subjectsWeekDeep);
									// test if calendar is empty
									$empty = true; 
									foreach ($week->calendar as $day) { 
										if (!empty($day->boxes)) {
											$empty = false;
											break;
										}
									}
									if (!$empty) { 
							?>
										<div class="week"> 
							<?php
											foreach ($week->calendar as $day) {
												/* @var $day BookingDay */
							?>
												<ul class="day">
													<li class="date"><?php echo AHtml::date($day->date, ADATE_FORMAT_NICE); ?></li>
							<?php 
														foreach ($day->boxes as $box) {
															/* @var $box BookingTimeBox */
															$isReserved = false;
															foreach ($box->services as $service) {
																/* @var $service BookingService */
																if (!$service->canReserve) {
																	$isReserved = true;
																	break;	
																}
															}
							?>
															<li class="hour<?php if ($isReserved) { ?> reserved<?php } ?>"><?php echo $box->fromTime . ' - ' . $box->toTime; ?></li>
							<?php
														} 
							?>
												</ul>
							<?php
										}
							?>
										<div class="clr"></div>
									</div>
						<?php
								}
							}
							if ($config->buttonBookit) {
						?>	
								<div class="clr"></div>
								<div class="bookit">
									<a href="<?php echo $urlBook; ?>" title="" class="button"><?php echo $reserve; ?></a>
								</div> 
						<?php
							}
							if ($thumb || ($displayConfig = $config->displaySubjectsProperties) != DISPLAY_PROPERTIES_OFF) { 
						?>
							<div class="clr"></div>
						<?php } ?>
					</div>
		<?php 
				} 
				if ($config->displayPagination == 1 || ($config->displayPagination === 'auto' && $this->pagination->total > $this->pagination->minLimit)) {	
		?>
					<div class="subjectListPagination pagination">
						<?php echo $this->pagination->getListFooter().(ISJ3 ? $this->pagination->getLimitBox() : ''); ?>
					</div>
					<div class="clr"></div>
		<?php
				}
			} else { 
		?>
				<div class="emptySubjectsList"><?php echo JText::_('NO_ITEMS_FOUND'); ?></div>
		<?php } ?>
	</div>
	<input type="hidden" name="<?php echo SESSION_TESTER; ?>" value="1" />
	<input type="hidden" name="reset" value="0" />
</form>
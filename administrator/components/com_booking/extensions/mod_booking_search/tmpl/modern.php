<?php
/**
 * Extended search module.
 *
 * @package		ARTIO Booking
 * @subpackage  modules
 * @copyright	Copyright (C) 2012 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @link        http://www.artio.net Official website
 */
/* @var $params JRegistry */

defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$doc = JFactory::getDocument();

$doc->addScript(JURI::root() . 'modules/mod_booking_search/assets/js/scripts.js?' . $manifest['version']);
$doc->addScriptDeclaration('var LGInvalidDateRange = "' . JText::_('INVALID_DATE_RANGE', true) . '";');

$url = 'index.php?option=com_booking&view=subjects';
$itemid = $params->get('itemid');
if ($itemid) {
    $url .= '&Itemid=' . $itemid;
}

JHtml::_('formbehavior.chosen', 'select');
?>
<form name="bookingSearch" id="bookingSearch" method="post" action="<?php echo JRoute::_($url); ?>" class="form-inline">
    <?php if ($params->get('date_range', 1)) { ?>
        <div class="control-group ">
            <?php echo JHtml::calendar($app->getUserStateFromRequest('booking_search_date_from', 'date_from'), 'date_from', 'bookingSearchDateFrom', ($params->get('time_range', 0) ? ADATE_FORMAT_LONG_CAL : ADATE_FORMAT_NORMAL_CAL), 'placeholder="' . htmlspecialchars(JText::_('CHECK_OUT')) . '" class="input-small"'); ?>
        </div>
        <div class="control-group ">
            <?php echo JHtml::calendar($app->getUserStateFromRequest('booking_search_date_to', 'date_to'), 'date_to', 'bookingSearchDateTo', ($params->get('time_range', 0) ? ADATE_FORMAT_LONG_CAL : ADATE_FORMAT_NORMAL_CAL), 'placeholder="' . htmlspecialchars(JText::_('CHECK_IN')) . '" class="input-small"'); ?>
        </div>
        <input type="hidden" name="date_type" value="<?php echo $timeRange ? 'datetime' : 'date'; ?>" />
    <?php } else { ?>
        <input type="hidden" name="date_from" id="bookingSearchDateFrom" value="" />
        <input type="hidden" name="date_to" id="bookingSearchDateTo" value="" />
        <?php
    }
    if ($params->get('price_range', 0)) {
        ?>
        <div class="control-group ">
            <input type="text" name="price_from" id="bookingSearchPriceFrom" value="<?php echo htmlspecialchars($app->getUserStateFromRequest('booking_search_price_from', 'price_from')); ?>" placeholder="<?php echo htmlspecialchars(JText::_('PRICE_FROM')); ?>" class="input-mini" />
            <span class="dash">&ndash;</span>
            <input type="text" name="price_to" id="bookingSearchPriceTo" value="<?php echo htmlspecialchars($app->getUserStateFromRequest('booking_search_price_to', 'price_to')); ?>" placeholder="<?php echo JText::_('PRICE_TO2'); ?>" class="input-mini" />
        </div>
    <?php } else { ?>
        <input type="hidden" name="price_from" id="bookingSearchPriceFrom" value="" />
        <input type="hidden" name="price_to" id="bookingSearchPriceTo" value="" />
        <?php
    }
    if ($params->get('template_area', 1)) {
        ?>
        <div class="control-group ">
            <?php echo $stemplates; ?>
        </div>
    <?php } else {
        ?>
        <input type="hidden" name="template_area" id="template_area" value="" />
    <?php } if ($params->get('required_capacity', 0)) { ?>
        <div class="control-group ">
            <input type="text" name="required_capacity" id="bookingSearchCapacity" value="<?php echo htmlspecialchars($app->getUserStateFromRequest('booking_search_required_capacity', 'required_capacity')); ?>" placeholder="<?php echo htmlspecialchars(JText::_('QUANTITY')); ?>" class="input-mini" />
        </div>
    <?php } else { ?>
        <input type="hidden" name="required_capacity" id="bookingSearchCapacity" value="" />
        <?php
    }
    if ($params->get('featured') === '2') {
        ?>	
        <div class="control-group ">
            <label class="control-label"><?php echo htmlspecialchars(JText::_('FEATURED_ONLY')); ?></label>	
            <div class="radio btn-group">
                <input type="radio" name="featured" id="bookingFeatured" value="1" <?php if ($app->getUserStateFromRequest('booking_search_featured', 'featured')) { ?>checked="checked"<?php } ?> />
                <label for="bookingFeatured"><?php echo htmlspecialchars(JText::_('JYES')); ?></label>
                <input type="radio" name="featured" id="bookingNoFeatured" value="0" <?php if (!$app->getUserStateFromRequest('booking_search_featured', 'featured')) { ?>checked="checked"<?php } ?> />
                <label for="bookingNoFeatured"><?php echo htmlspecialchars(JText::_('JNO')); ?></label>        
            </div>    
        </div>
    <?php } elseif ($params->get('featured') === '1') { ?>
        <input type="hidden" name="featured" value="1" />
    <?php } else { ?>
        <input type="hidden" name="featured" value="0" />
    <?php } ?>
    <input type="hidden" name="category" value="<?php echo $params->get('category'); ?>" />
    <?php
    if ($params->get('locations', 0)) {
        echo AHtml::locations(true, null, false, false);
    }
    if ($params->get('properties', 0)) {
        foreach ($searchables as $searchable) {
            ?>
            <div class="control-group ">
                <?php
                if ($searchable[PARAM_TYPE] == 'list') {
                    $options = array();
                    $options[] = JHTML::_('select.option', '', '&ndash; ' . ATemplate::translateParam($searchable['node']['label']) . ' &ndash;');
                    foreach ($searchable[PARAM_OPTIONS] as $option) {
                        $options[] = JHTML::_('select.option', $option[0], $option[1]);
                    }
                    echo JHTML::_('select.genericlist', $options, $searchable[PARAM_REQUESTNAME], 'class="input-medium"', 'value', 'text', $searchable[PARAM_REQUESTVALUE]);
                } elseif ($searchable[PARAM_TYPE] == 'text') {
                    ?>
                    <input type="text" name="<?php echo $searchable[PARAM_REQUESTNAME]; ?>" id="<?php echo $searchable[PARAM_REQUESTNAME]; ?>" value="<?php echo htmlspecialchars($searchable[PARAM_REQUESTVALUE], ENT_QUOTES); ?>" class="input-medium" placeholder="<?php echo htmlspecialchars(ATemplate::translateParam($searchable['node']['label'])); ?>" />
                <?php } elseif ($searchable[PARAM_TYPE] == 'checkbox') { ?>                                
                    <div class="control-group ">                        
                        <label class="control-label"><?php echo htmlspecialchars(ATemplate::translateParam($searchable['node']['label'])); ?></label>	
                        <div class="radio btn-group">
                            <input type="radio" name="<?php echo $searchable[PARAM_REQUESTNAME]; ?>" id="<?php echo $searchable[PARAM_REQUESTNAME]; ?>s" value="1" <?php if ($searchable[PARAM_REQUESTVALUE]) { ?>checked="checked"<?php } ?> />
                            <label for="<?php echo $searchable[PARAM_REQUESTNAME]; ?>s"><?php echo htmlspecialchars(JText::_('JYES')); ?></label>
                            <input type="radio" name="<?php echo $searchable[PARAM_REQUESTNAME]; ?>" id="<?php echo $searchable[PARAM_REQUESTNAME]; ?>No" value="0" <?php if (!$searchable[PARAM_REQUESTVALUE]) { ?>checked="checked"<?php } ?> />
                            <label for="<?php echo $searchable[PARAM_REQUESTNAME]; ?>No"><?php echo htmlspecialchars(JText::_('JNO')); ?></label>        
                        </div>    
                    </div>
                <?php } elseif ($searchable[PARAM_TYPE] == 'radio') {
                    ?>
                    <label class="control-label"><?php echo htmlspecialchars(ATemplate::translateParam($searchable['node']['label'])); ?></label>
                    <div class="radio btn-group">
                        <?php foreach ($searchable[PARAM_OPTIONS] as $i => $option) {
                            ?>
                            <input type="radio" name="<?php echo $searchable[PARAM_REQUESTNAME]; ?>" id="<?php echo ($id = 'r' . $i . $searchable[PARAM_REQUESTNAME]); ?>" value="<?php echo htmlspecialchars($option[0]); ?>" <?php if ($searchable[PARAM_REQUESTVALUE] == $option[0]) { ?>checked="checked"<?php } ?> />
                            <label for="<?php echo $id; ?>"><?php echo $option[1]; ?></label>
                        <?php }
                        ?>
                    </div>
                <?php }
                ?>
            </div>
            <?php
        }
    }
    ?>
    <div class="toolbar btn-group">
        <div class="btn btn-primary" id="bookingSearchSubmit"><?php echo htmlspecialchars($params->get('submit', JText::_('JSEARCH_FILTER_SUBMIT'))); ?></div>
        <?php if ($params->get('reset', 0)) { ?>
            <div class="btn btn-danger" id="bookingSearchReset"><?php echo htmlspecialchars(JText::_('JSEARCH_FILTER_CLEAR')); ?></div>
            <?php
        } if ($params->get('login', 0)) {
            $item = $app->getMenu()->getItem($params->get('login_itemid'));
            if ($item) {
                ?>
                <div class="btn btn-success" id="bookingSearchLogin" rel="<?php echo JRoute::_($item->link . '&Itemid=' . $item->id); ?>"><?php echo htmlspecialchars(JText::_('JLOGIN')); ?></div>
                <?php
            }
        }
        ?>
    </div>
    <input type="hidden" name="booking_search" id="bookingSearchTogler" value="1" />
    <input type="hidden" name="<?php echo SESSION_TESTER; ?>" value="1" />
</form>
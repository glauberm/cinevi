<?php
/**
 * Override of Icon rending for fontawesome templates
 */

defined('JPATH_BASE') or die;

$d = $displayData;
$props = isset($d->properties) ? $d->properties : '';

/**
 * Handle cases where additional classes are in the $d->icon string, like the calendar
 * uses "icon-clock timeButton".  Also handle multiple icon-foo, like "icon-spinner icon-spin"
 */

$iconParts = explode(' ', trim($d->icon));
$spareParts = array();

foreach ($iconParts as $key => $part) {
	if (!strstr($part, 'icon-')) {
		unset($iconParts[$key]);
		$spareParts[] = $part;
	}
	else if (empty($part)) {
		unset($iconParts[$key]);
	}
}

/**
 * Now test for any icon names that need changing to achieve Font Awesomeness.
 */

foreach ($iconParts as $key => $part)
{

	$test = str_replace('icon-', '', trim($part));

	switch ($test) {
		case 'list-view':
			$iconParts[$key] = 'icon-list';
			break;
		case 'feed':
			$iconParts[$key] = 'icon-rss';
			break;
		case 'picture':
			$iconParts[$key] = 'icon-picture';
			break;
		case 'delete':
			$iconParts[$key] = 'icon-times';
			break;
		case 'expand-2':
			$iconParts[$key] = 'icon-expand';
			break;
		case 'clock':
			$iconParts[$key] = 'icon-clock';
			break;
		case 'question-sign':
			$iconParts[$key] = 'icon-question';
			break;
		case 'next':
 			$iconParts[$key] = 'icon-arrow-right-4';
 			break;
 		case 'previous':
 			$iconParts[$key] = 'icon-arrow-right-4';
 			break;
		case 'eye-open':
			$iconParts[$key] = 'icon-eye-open';
			break;
		case 'lightning':
			$iconParts[$key] = 'icon-flash';
			break;
		default :
			$iconParts[$key] = str_replace('icon-', 'icon-', $part);
			break;
	}
}

$d->icon = implode(' ', $iconParts);

/**
 * Add any additional non-icon classes back
 */

if (!empty($spareParts))
{
	$d->icon .= ' ' . implode(' ', $spareParts);
}

?>

<span data-isicon="true" class="icon <?php echo $d->icon;?>" <?php echo $props;?>></span>

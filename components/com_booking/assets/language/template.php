<?php

/**
 * Language constants for template javascript
 * 
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  assets 
 * @copyright	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 		ARTIO s.r.o., http://www.artio.net
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

//create dialog
$languages['LGChoose'] = 'CHOOSE_PROPERTY_TYPE';

$languages['LGChooseTextBox'] = 'TEXT_BOX';
$languages['LGChooseTextarea'] = 'TEXT_AREA';
$languages['LGChooseEditor'] = 'TEXT_AREA_EDITOR';
$languages['LGEditorShow'] = 'EDITOR_SHOW';
$languages['LGChooseSelectBox'] = 'SELECT_BOX';
$languages['LGChooseRadio'] = 'RADIO_BUTTONS';
$languages['LGChooseCheckBox'] = 'CHECK_BOX';

$languages['LGCreate'] = 'CREATE';

//main window
$languages['LGTitle'] = 'TITLE';
$languages['LGApply'] = 'JAPPLY';
$languages['LGCancel'] = 'CANCEL';
$languages['LGConfig'] = 'CONFIG';
$languages['LGTrash'] = 'TRASH';
$languages['LGSearchable'] = 'SEARCHABLE';
$languages['LGFilterable'] = 'FILTERABLE';
$languages['LGObjects'] = 'OBJECT_S';
$languages['LGObject'] = 'OBJECT';
$languages['LGIcon'] = 'ICON';

//select-one property
$languages['LGOptions'] = 'OPTIONS';
$languages['LGSelectOneInfo'] = 'ADD_OPTIONS_EVERY_MUST_BEGIN_ON_NEW_LINE';
$languages['LGErrAddOptions'] = 'ADD_AT_LEAST_TWO_OPTIONS';

$languages['LGErrAddTitle'] = 'ADD_TITLE';
$languages['LGAreYouSure'] = 'ARE_YOU_SURE';

AImporter::helper('document');
ADocument::addLGScriptDeclaration($languages);

?>
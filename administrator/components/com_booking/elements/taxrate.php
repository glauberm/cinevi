<?php

/**
 * Table of tax rate list (title, value) with toolbar (new, delete) for using in JForm.
 *
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage  	elements
 * @copyright		Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

defined('_JEXEC') or die('Restricted access');

class JFormFieldTaxRate extends JFormField
{

	public $type = 'TaxRate';

	/**
	 * (non-PHPdoc)
	 * @see JFormField::getInput()
	 */
	protected function getInput()
	{
		// setup toolbar
		JToolBar::getInstance('jelementtaxrate')->appendButton('Link', 'new', 'JTOOLBAR_NEW', 'javascript:void(0);');
		JToolBar::getInstance('jelementtaxrate')->appendButton('Link', 'delete', 'JTOOLBAR_DELETE', 'javascript:void(0);');
		
		// show toolbar on the left
		$code = '<div style="float: left">' . JToolBar::getInstance('jelementtaxrate')->render() . '</div>';
		
		// show head of table of tax rate list
		$code .= '
			  <table id="jelementtaxrate-table" width="1%" style="clear: both">
				<thead>
			      <tr>
				    <th width="1%"></th>
				    <th width="1%" nowrap="nowrap">' . JText::_('TITLE') . '</th>
				    <th width="1%" nowrap="nowrap">' . JText::_('TAX_VALUE_PERCENT') . '</th>
			      </tr>
				</thead>
				<tbody id="jelementtaxrate-tbody">';
		
		// show existing tax rate list
		if (is_array($this->value))
			for ($i = 0; $i < count($this->value); $i += 2) // even item is title, odd item is value 
				$code .= '<tr>'.$this->_getRow(JArrayHelper::getValue($this->value, $i), JArrayHelper::getValue($this->value, $i + 1)) . '</tr>';
		
		// show footer of table
		$code .= '
				</tbody>
			  </table>';
		
		// show javascript to add, delete tax rate
		$code .= '
			<script type="text/javascript">
				//<![CDATA[
				window.addEvent("domready", function() {
				
					if (document.id("jelementtaxrate-tbody").getElements("tr").length == 0) { // hide table if is empty
						document.id("jelementtaxrate-table").setStyle("display", "none");
						document.id("jelementtaxrate-delete").setStyle("display", "none"); // hide tool delete - there is nothing to delete
					}
					
					document.id("jelementtaxrate-new").addEvent("click", function() { // add new tax rate
						document.id("jelementtaxrate-table").setStyle("display", ""); // show table
						document.id("jelementtaxrate-delete").setStyle("display", ""); // show tool delete
				
						var tr = new Element("tr"); // new table row for tax rate
						tr.set("html", "' . addslashes($this->_getRow('', '')) . '").inject(document.id("jelementtaxrate-tbody"));
						return false; // disable form submit
					});
				
					document.id("jelementtaxrate-delete").addEvent("click", function() { // delete tax rate
						document.id("jelementtaxrate-tbody").getElements("input[type^=checkbox]").each(function(e, i) { // process checkboxes of tax rate list
							if (e.checked) // delete
								e.getParent().getParent().destroy();
						});
				
						if (document.id("jelementtaxrate-tbody").getElements("tr").length == 0) { // hide table if empty
							document.id("jelementtaxrate-table").setStyle("display", "none");
							document.id("jelementtaxrate-delete").setStyle("display", "none"); // hide tool delete - there is nothing to delete
						}
						return false;
					});
				});
				//]]>
			</script>';
		
		return $code;
	}
	
	private function _getRow($title, $value) 
	{
		return '<td><input type="checkbox" /></td><td><input type="text" name="' . htmlspecialchars($this->name) . '" value="' . htmlspecialchars($title) . '" size="30" /></td><td><input type="text" name="' . htmlspecialchars($this->name) . '" value="' . htmlspecialchars($value) . '" size="1" /></td>';
	}
}
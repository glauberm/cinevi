/**
 * @version $Id$
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

var Supplements = {
		
	changedSelect : function(supplementId) {
		
		select = document.getElementById('supplements'+supplementId);
		empty = true;
		
		if (select.selectedIndex>=0){
			option = select.options[select.selectedIndex];
			if (option.value!='')
				empty=false;
		}
		if (!empty)
			this.showCapacity(supplementId);
		else
			this.hideCapacity(supplementId);

	},
	changedCheckBox : function(supplementId) {
		
		box = document.getElementById('supplements'+supplementId);
		if (box.checked)
			this.showCapacity(supplementId);
		else
			this.hideCapacity(supplementId);

	},
	showCapacity : function(supplementId)
	{
		select = document.getElementById('supplements_capacity'+supplementId);
		if (select)
			select.style.display='inline';
	}, 
	hideCapacity : function(supplementId)
	{
		select = document.getElementById('supplements_capacity'+supplementId);
		if (select){
			select.style.display='none';
			if (select.tagName.toLowerCase()=='select')
				select.selectedIndex = -1;
			else
				select.value = 0;
		}
	}
}
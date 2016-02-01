/**
 * Javascript for list of extra fields.
 * 
 * @package ARTIO Booking
 * @subpackage assets
 * @copyright Copyright (C) 2012 ARTIO s.r.o.. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link http://www.artio.net Official website
 */

window.addEvent('domready', function() {
	if ($$('#fields #toolbar #add')) {
		$$('#fields #toolbar #add').addEvent('click', function() {
			document.id('fields').op.value = 'add';
			document.id('fields').submit();
		});
	}
	if ($$('#fields #toolbar #edit')) {
		$$('#fields #toolbar #edit').addEvent('click', function() {
            if (document.getElements('#fields input[type=checkbox][name^=cid]:checked').length > 0) {
                document.id('fields').op.value = 'edit';
                document.id('fields').submit();
            } else {
                alert(LGSelectField);            
                return false;
            }
		});
	}
	if ($$('#fields #toolbar #remove')) {
		$$('#fields #toolbar #remove').addEvent('click', function() {
            if (document.getElements('#fields input[type=checkbox][name^=cid]:checked').length > 0) {
                document.id('fields').op.value = 'remove';
                document.id('fields').submit();
            } else {
                alert(LGSelectField);            
                return false;
            }
		});
	}
	if ($$('#fields #toolbar #save')) {
		$$('#fields #toolbar #save').addEvent('click', function() {
			document.id('fields').op.value = 'save';
			document.id('fields').submit();
		});
	}
	if ($$('#fields #toolbar #cancel')) {
		$$('#fields #toolbar #cancel').addEvent('click', function() {
			document.id('fields').op.value = 'cancel';
			document.id('fields').submit();
		});
	}
});
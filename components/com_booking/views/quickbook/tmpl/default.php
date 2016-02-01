<?php

/**
 * @version		$Id$
 * @package		ARTIO Booking
 * @subpackage		views
 * @copyright	  	Copyright (C) 2010 ARTIO s.r.o.. All rights reserved.
 * @author 			ARTIO s.r.o., http://www.artio.net
 * @license     	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @link        	http://www.artio.net Official website
 */

?>

<h1>
	<?php echo $this->menu->params->get('h1'); ?>
</h1>
<div id="step1" <?php if (count($this->children) == 1) { ?>
	style="display: none;" <?php } ?>>

	<h2>
		<strong>1</strong>
		<?php echo $this->parent? $this->parent->title : ""; ?>
	</h2>
	<select id="object" autocomplete="off">
		<option value="">
			<?php echo $this->menu->params->get('select'); ?>
		</option>
		<?php foreach ($this->children as $children) { ?>
		<option value="<?php echo $children->id ?>"
		<?php if (count($this->children) == 1) { ?> selected="selected"
		<?php } ?>>
			<?php echo $children->title; ?>
		</option>
		<?php } ?>
	</select>
	<script type="text/javascript">
				//<![CDATA[
				window.addEvent('domready', function() {
					document.id('step2').setStyle('display', 'none');
					document.id('step3-1').setStyle('display', 'none');
					document.id('step3-2').setStyle('display', 'none');
					document.id('object').addEvent('change', function() {
						if (this.value != '') {
							QuickBook.object(this.value);
						} else {
							document.id('step2').setStyle('display', 'none');
							document.id('step3-1').setStyle('display', 'none');
							document.id('step3-2').setStyle('display', 'none');
						}							
					});
					<?php if (count($this->children) == 1) { ?>
						QuickBook.object(<?php echo reset($this->children)->id; ?>);
					<?php } ?>
				});
				var QuickBook = {
					object : function(id) {
						new Request({
					    	url: '<?php echo JRoute::_('index.php?option=com_booking&view=quickbook&layout=date'); ?>',
					    	method: 'get',
					    	data: {
								'id': id
					    	},
					    	onSuccess: function(html) {
					        	document.id('month').set('html', html);
					        	document.id('step2').setStyle('display', 'block');
								QuickBook.initToolTip();
					    	}
					    }).send();
					},
					month : function(month, year) {
						new Request({
							url: '<?php echo JRoute::_('index.php?option=com_booking&view=quickbook&layout=date'); ?>',
							method: 'get',
							data: {
								'month': month,
								'year': year,
								'id': document.id('object').value
							},
							onSuccess: function(html) {
						   		document.id('month').set('html', html);
                                document.id('step3-1').setStyle('display', 'none');
                                document.id('step3-2').setStyle('display', 'none');
						   		QuickBook.initToolTip();
							}
						}).send();
					},
					day : function(day, month, year, id) {
						new Request({
							url: '<?php echo JRoute::_('index.php?option=com_booking&view=quickbook&layout=day'); ?>',
							method: 'get',
							data: {
								'day': day,
								'month': month,
								'year': year,
								'id': document.id('object').value
							},
							onSuccess: function(html) {
					   			document.id('day').set('html', html);
					   			if (QuickBook.lastDay && document.id(QuickBook.lastDay))
					   				document.id(QuickBook.lastDay).removeClass('selected');
					   			document.id(id).addClass('selected');
					   			QuickBook.lastDay = id;
					   			document.id('step3-1').setStyle('display', 'block');
					   			document.id('step3-2').setStyle('display', 'block');
                                document.id('boxIds').value = '';
					   			QuickBook.initToolTip();
							}
						}).send();
					},
					book : function(boxId, id) {
						document.id('boxIds').value = boxId;
						document.id('subject').value = document.id('object').value;
						if (this.lastId && document.id(this.lastId))
							document.id(this.lastId).removeClass('selected');
						document.id(id).addClass('selected');
						this.lastId = id;
					},
					checkout : function() {
						if (document.id('boxIds').value == '') {
							document.id('error').setStyle('display', 'block');
							return false;
						}
						return true;
					},
					initToolTip : function() {
						$$('.hasTip').each(function(el) {
							var title = el.get('title');
							if (title) {
								var parts = title.split('::', 2);
								el.store('tip:title', parts[0]);
								el.store('tip:text', parts[1]);
							}
						});
						var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});
					}
				}
				//]]>
			</script>
</div>
<div id="step2">
	<h2>
		<strong><?php echo count($this->children) == 1 ? 1 : 2; ?> </strong>
		<?php echo JText::_('DATE'); ?>
	</h2>
	<div id="month"></div>
</div>
<div id="step3-1">
	<h2>
		<strong><?php echo count($this->children) == 1 ? 2 : 3; ?> </strong>
		<?php echo JText::_('TIME'); ?>
	</h2>
	<div id="day"></div>	
</div>
<div id="step3-2">
	<span><?php echo JText::_('FINISH_BOOK'); ?> </span>
	<form name="book" method="post"
		action="<?php echo JRoute::_('index.php?option=com_booking&controller=reservation&task=add_checkout'); ?>">
		<p id="error" style="display: none">
			<?php echo JText::_('SELECT_TIME'); ?>
		</p>
		<button type="submit" class="btn button checkout"
			onclick="return QuickBook.checkout()">
			<span><span><?php echo JText::_('BOOK_IT'); ?> </span> </span>
		</button>
		<input type="hidden" name="boxIds[0][]" id="boxIds" value="" /> <input
			type="hidden" name="subject[0]" id="subject" value="" /> <input
			type="hidden" name="ctype[0]" value="daily" />
	</form>
</div>

<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_random_image
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="img-responsive random-image<?php echo $moduleclass_sfx ?>">
<?php if ($link) : ?>
<a href="<?php echo $link; ?>">
<?php endif; ?>
	<div class="hidden" id="this-img"><?php echo JHtml::_('image', $image->folder . '/' . $image->name, $image->name, array()); ?></div>
<?php if ($link) : ?>
</a>
<?php endif; ?>
</div>

<!-- Adicionado por Glauber Mota (glaubernm@gmail.com). -->

<img class="img-responsive" id="random-img"></img>
<span id="random-img-caption"></span>

<script type="text/javascript">
		
	var description = [
	  "<?php echo JUri::root(true); ?>/images/banners/fantasma1-mini.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/fantasma2-mini.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/gil1-mini.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/gil2-mini.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/gil3-mini.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/ocaso1-mini.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/ocaso2-mini.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/ossobreviventes1-mini.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/ossobreviventes2-mini.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/trevas1-mini.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/trevas2-mini.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/trevas3-mini.jpg"
	];

	var size = description.length
	var x = Math.floor(size*Math.random())
	document.getElementById('random-img').src=description[x];
	
	switch (x) {
		case 0:
			document.getElementById('background-image').style.backgroundImage = "url('<?php echo JUri::root(true); ?>/images/banners/fantasma1.jpg')";
			document.getElementById('random-img-caption').innerHTML = "Fantasma Vestido de Palhaço (Alessandra Stropp, 2013)";
			break;
		case 1:
			document.getElementById('background-image').style.backgroundImage = "url('<?php echo JUri::root(true); ?>/images/banners/fantasma2.jpg')";
			document.getElementById('random-img-caption').innerHTML = "Fantasma Vestido de Palhaço (Alessandra Stropp, 2013)";
			break;
		case 2:
			document.getElementById('background-image').style.backgroundImage = "url('<?php echo JUri::root(true); ?>/images/banners/gil1.jpg')";
			document.getElementById('random-img-caption').innerHTML = "Gil (Daniel Nolasco, 2014)";
			break;
		case 3:
			document.getElementById('background-image').style.backgroundImage = "url('<?php echo JUri::root(true); ?>/images/banners/gil2.jpg')";
			document.getElementById('random-img-caption').innerHTML = "Gil (Daniel Nolasco, 2014)";
			break;
		case 4:
			document.getElementById('background-image').style.backgroundImage = "url('<?php echo JUri::root(true); ?>/images/banners/gil3.jpg')";
			document.getElementById('random-img-caption').innerHTML = "Gil (Daniel Nolasco, 2014)";
			break;
		case 5:
			document.getElementById('background-image').style.backgroundImage = "url('<?php echo JUri::root(true); ?>/images/banners/ocaso1.jpg')";
			document.getElementById('random-img-caption').innerHTML = "Ocaso (Bruno Roger, 2014)";
			break;
		case 6:
			document.getElementById('background-image').style.backgroundImage = "url('<?php echo JUri::root(true); ?>/images/banners/ocaso2.jpg')";
			document.getElementById('random-img-caption').innerHTML = "Ocaso (Bruno Roger, 2014)";
			break;
		case 7:
			document.getElementById('background-image').style.backgroundImage = "url('<?php echo JUri::root(true); ?>/images/banners/ossobreviventes1.jpg')";
			document.getElementById('random-img-caption').innerHTML = "Os Sobreviventes (Daniel Nolasco, 2014)";
			break;
		case 8:
			document.getElementById('background-image').style.backgroundImage = "url('<?php echo JUri::root(true); ?>/images/banners/ossobreviventes2.jpg')";
			document.getElementById('random-img-caption').innerHTML = "Os Sobreviventes (Daniel Nolasco, 2014)";
			break;
		case 9:
			document.getElementById('background-image').style.backgroundImage = "url('<?php echo JUri::root(true); ?>/images/banners/trevas1.jpg')";
			document.getElementById('random-img-caption').innerHTML = "Trevas (Will Domingos, 2014)";
			break;
		case 10:
			document.getElementById('background-image').style.backgroundImage = "url('<?php echo JUri::root(true); ?>/images/banners/trevas2.jpg')";
			document.getElementById('random-img-caption').innerHTML = "Trevas (Will Domingos, 2014)";
			break;
		case 11:
			document.getElementById('background-image').style.backgroundImage = "url('<?php echo JUri::root(true); ?>/images/banners/trevas3.jpg')";
			document.getElementById('random-img-caption').innerHTML = "Trevas (Will Domingos, 2014)";
			break;
		default:
			document.getElementById('background-image').style.backgroundImage = "url('<?php echo JUri::root(true); ?>/images/banners/gil1.jpg')";
			document.getElementById('random-img-caption').innerHTML = "Gil (Daniel Nolasco, 2014)";
	}
	
</script>

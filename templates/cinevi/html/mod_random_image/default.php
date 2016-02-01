<?php

defined('_JEXEC') or die;
?>

<div class="img-responsive" id="random-img"></div>
<span id="random-img-caption"></span>

<script type="text/javascript">

	var description = [
	  "<?php echo JUri::root(true); ?>/images/banners/fantasma1.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/fantasma2.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/gil1.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/gil2.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/gil3.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/ocaso1.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/ocaso2.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/ossobreviventes1.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/ossobreviventes2.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/trevas1.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/trevas2.jpg",
	  "<?php echo JUri::root(true); ?>/images/banners/trevas3.jpg"
	];

	var size = description.length
	var x = Math.floor(size*Math.random())
	document.getElementById('random-img').style.backgroundImage = "url("+description[x]+")";

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

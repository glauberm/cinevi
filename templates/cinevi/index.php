<?php

defined('_JEXEC') or die('Restricted access');

$app             = JFactory::getApplication();
$doc             = JFactory::getDocument();
$user            = JFactory::getUser();
$this->language  = $doc->language;
$this->direction = $doc->direction;

// Getting params from template
$params = $app->getTemplate(true)->params;

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->get('sitename');

// Add Stylesheets
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/bootstrap.min.css');
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/estilos.css');
$doc->addStyleSheet('http://fonts.googleapis.com/css?family=Buenard:400,700');
$doc->addStyleSheet($this->baseurl.'/media/jui/css/icomoon.css');

?>

<!DOCTYPE html>
<html xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>

	<jdoc:include type="head" />
	<!--<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/estilos.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/estilos.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/media/jui/css/icomoon.css" type="text/css" />-->

</head>
<body class="site <?php echo $option
	. ' view-' . $view
	. ($layout ? ' layout-' . $layout : ' no-layout')
	. ($task ? ' task-' . $task : ' no-task')
	. ($itemid ? ' itemid-' . $itemid : '');
?>">

	<div id="background-image" class="hidden-xs"></div>

	<!-- Body -->
	<div class="body">

		<!--  Dispositivos Pequenos -->
		<div id="faixa-logo-xs" class="hidden-sm display-xs hidden-md hidden-lg">
			<header class="header" role="banner">
				<div class="header-inner text-center clearfix">
					<a class="brand" href="<?php echo $this->baseurl; ?>/">
						<img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/img/logo.png" alt="Departamento de Cinema e Vídeo">
					</a>
				</div>
			</header>
		</div>

		<div id="botao-menu-xs" class="hidden-sm display-xs hidden-md hidden-lg">
			<div class="container">
				<button type="button" class="tcon tcon-menu--arrow tcon-menu--arrow360left" aria-label="toggle menu">
					<span class="tcon-menu__lines" aria-hidden="true"></span>
					<span class="tcon-visuallyhidden">Menu</span>
				</button>
			</div>
		</div>

		<div id="menu-xs" class="hidden-sm display-xs hidden-md hidden-lg">
			<!-- Position-7 -->
			<?php if ($this->countModules('menu')) : ?>
				<jdoc:include type="modules" name="menu" style="xhtml" />
			<?php endif; ?>
		</div>
		<!--  ./Dispositivos Pequenos -->

		<!-- Login -->
		<div id="login-sm" class="container hidden-xs text-right">
			<jdoc:include type="modules" name="login" style="xhtml" />
		</div>
		<!-- ./Login -->

		<div class="container" id="main-container">

			<!--  Dispositivos Médios -->
			<div id="botao-menu-sm" class="hidden-xs display-sm hidden-md hidden-lg">
				<div class="container">
					<button type="button" class="tcon tcon-menu--arrow tcon-menu--arrow360left" aria-label="toggle menu">
						<span class="tcon-menu__lines" aria-hidden="true"></span>
						<span class="tcon-visuallyhidden">Menu</span>
					</button>
				</div>
			</div>

			<div id="faixa-logo-sm" class="hidden-xs display-sm hidden-md hidden-lg">
				<header class="header" role="banner">
					<div class="header-inner text-center clearfix">
						<a class="brand" href="<?php echo $this->baseurl; ?>/">
							<img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/img/logo.png" alt="Departamento de Cinema e Vídeo">
						</a>
					</div>
				</header>
			</div>

			<div id="menu-sm" class="hidden-xs display-sm hidden-md hidden-lg">
				<!-- Position-7 -->
				<?php if ($this->countModules('menu')) : ?>
					<jdoc:include type="modules" name="menu" style="xhtml" />
				<?php endif; ?>
			</div>
			<!--  ./Dispositivos Médios -->

			<!-- Principal -->
			<div class="row">
				<!-- Menu Esquerda (Desktop) -->
				<aside class="col-md-3 hidden-xs hidden-sm" id="barra-lateral">

					<header class="header" role="banner">
						<div class="header-inner text-center clearfix">
							<a class="brand" href="<?php echo $this->baseurl; ?>/">
								<img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/img/logo.png" alt="Departamento de Cinema e Vídeo">
							</a>
						</div>
					</header>

					<div class="header-search">
						<jdoc:include type="modules" name="busca" style="none" />
					</div>

					<nav role="navigation">
						<jdoc:include type="modules" name="left" style="none" />
					</nav>

				</aside>
				<!-- ./Menu Esquerda -->

				<!-- Principal -->
				<main class="col-md-9" id="content" role="main">
				<div class="row">

					<!-- Centro -->
					<div class="col-sm-8" id="centro">
						<!-- Banner -->
						<div class="hidden-xs">
							<jdoc:include type="modules" name="banner" style="xhtml" />
						</div>

						<!-- Breadcrumbs -->
						<div class="hidden-xs">
							<jdoc:include type="modules" name="breadcrumb" style="xhtml" />
						</div>

						<!-- Alertas -->
						<jdoc:include type="message" />

						<!-- Topo -->
						<?php if ($this->countModules('top')) : ?>
							<jdoc:include type="modules" name="top" style="none" />
						<?php endif; ?>

						<!-- Conteúdo -->
						<jdoc:include type="component" />

						<!-- Módulo Extra -->
						<?php if ($this->countModules('bottom')) : ?>
							<jdoc:include type="modules" name="bottom" style="xhtml" />
						<?php endif; ?>
					</div>
					<!-- ./Centro -->

					<!-- Direita -->
					<div class="col-sm-4" id="direita">
						<?php if ($this->countModules('right')) : ?>
							<jdoc:include type="modules" name="right" style="xhtml" />
						<?php endif; ?>
					</div>
					<!-- ./Direita -->

				</div>
				</main>
				<!-- ./Principal -->
			</div>
			<!-- ./Principal -->

		</div><!-- /.container -->

		<!-- Rodapé .sm -->
			<footer class="display-sm display-lg display-md hidden-xs" role="contentinfo">
				<small>&copy; <?php echo date('Y'); ?> Departamento de Cinema e Vídeo</small>
			</footer>
		<!-- ./Rodapé .sm -->

		<!-- Footer xs -->
		<footer class="footer hidden-sm hidden-lg hidden-md display-xs" role="contentinfo">
			<div class="container">
				<p class="pull-right">
					<a href="#top" id="back-top">
						<?php echo JText::_('TPL_PROTOSTAR_BACKTOTOP'); ?>
					</a>
				</p>
				<small>
					&copy; <?php echo date('Y'); ?> <?php echo $sitename; ?>
				</small>
			</div>
		</footer>

	</div><!-- /.body -->

	<!--JavaScript-->

	<!--[if lt IE 9]>
		<script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script>
	<![endif]-->
	<?php
		JHtml::_('bootstrap.framework');
		$doc->addScript($this->baseurl . '/templates/' . $this->template . '/js/transformicon.js');
		$doc->addScript($this->baseurl . '/templates/' . $this->template . '/js/jquery.mask.min.js');
		$doc->addScript($this->baseurl . '/templates/' . $this->template . '/js/scripts.js');
	?>

	<jdoc:include type="modules" name="debug" style="none" />
</body>
</html>

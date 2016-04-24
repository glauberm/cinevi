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
// @TODO: Baixar Buenard e carregar localmente
$doc->addStyleSheet('http://fonts.googleapis.com/css?family=Buenard:400,700');
$doc->addStyleSheet($this->baseurl.'/media/jui/css/icomoon.css');

?>

<!DOCTYPE html>
<html xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
	<jdoc:include type="head" />
	<!-- FAVICONS -->
	<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $this->baseurl . "/templates/" . $this->template; ?>/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="<?php echo $this->baseurl . "/templates/" . $this->template; ?>/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $this->baseurl . "/templates/" . $this->template; ?>/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $this->baseurl . "/templates/" . $this->template; ?>/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $this->baseurl . "/templates/" . $this->template; ?>/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $this->baseurl . "/templates/" . $this->template; ?>/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $this->baseurl . "/templates/" . $this->template; ?>/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $this->baseurl . "/templates/" . $this->template; ?>/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $this->baseurl . "/templates/" . $this->template; ?>/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $this->baseurl . "/templates/" . $this->template; ?>/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="<?php echo $this->baseurl . "/templates/" . $this->template; ?>/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $this->baseurl . "/templates/" . $this->template; ?>/favicon-16x16.png">
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
						<img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/img/logo2.png" alt="Departamento de Cinema e Vídeo">
					</a>
				</div>
			</header>
		</div>
		<div id="faixa-menu-xs" class="hidden-sm display-xs hidden-md hidden-lg">
			<div class="clearfix">
				<div id="botao-menu-xs" class="hidden-sm display-xs hidden-md hidden-lg pull-left">
					<button type="button" aria-label="Menu">
						<span class="icon-menu-3"></span>
					</button>
				</div>
				<div id="botao-login-xs" class="hidden-sm display-xs hidden-md hidden-lg pull-right">
					<jdoc:include type="modules" name="login" style="xhtml" />
				</div>
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
				<button type="button" aria-label="Menu">
					<span class="">Menu</span>
				</button>
			</div>

			<div id="faixa-logo-sm" class="hidden-xs display-sm hidden-md hidden-lg">
				<header class="header" role="banner">
					<div class="header-inner text-center clearfix">
						<a class="brand" href="<?php echo $this->baseurl; ?>/">
							<img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/img/logo2.png" alt="Departamento de Cinema e Vídeo">
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

						<br class="invisible"/>

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
				<div class="container text-center">
					<p>&copy; <?php echo date('Y'); ?> <?php echo $sitename; ?><br/>Desenvolvido por <a href="mailto:glaubernm@gmail.com">Glauber Mota</a></p>
				</div>
			</footer>
		<!-- ./Rodapé .sm -->

		<!-- Footer xs -->
		<footer class="footer hidden-sm hidden-lg hidden-md display-xs" role="contentinfo">
			<div class="container text-center">
				<div class="clearfix">
					<div class="pull-left">
						&copy; <?php echo date('Y'); ?> <?php echo $sitename; ?>
					</div>
					<div class="pull-right">
						Desenvolvido por <a href="mailto:glaubernm@gmail.com">Glauber Mota</a>
					</div>
				</div>
			</div>
		</footer>

	</div><!-- /.body -->

	<!--JavaScript-->

	<!--[if lt IE 9]>
		<script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script>
	<![endif]-->
	<?php
		JHtml::_('bootstrap.framework');
		$doc->addScript($this->baseurl . '/templates/' . $this->template . '/js/jquery.mask.min.js');
		$doc->addScript($this->baseurl . '/templates/' . $this->template . '/js/scripts.js');
	?>

	<jdoc:include type="modules" name="debug" style="none" />
</body>
</html>

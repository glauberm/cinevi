<?php

defined('_JEXEC') or die;

$app             = JFactory::getApplication();
$doc             = JFactory::getDocument();
$user            = JFactory::getUser();
$this->language  = $doc->language;
$this->direction = $doc->direction;

// Output as HTML5
$doc->setHtml5(true);

// Getting params from template
$params = $app->getTemplate(true)->params;

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->get('sitename');

if($task == "edit" || $layout == "form" )
{
	$fullWidth = 1;
}
else
{
	$fullWidth = 0;
}

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');

$doc->addScriptVersion($this->baseurl . '/templates/' . $this->template . '/js/template.js');
$doc->addScriptVersion($this->baseurl . '/templates/' . $this->template . '/js/banner.js');

// Add Stylesheets
$doc->addStyleSheetVersion($this->baseurl . '/templates/' . $this->template . '/css/template.css');

// Use of Google Font
if ($this->params->get('googleFont'))
{
	$doc->addStyleSheetVersion($this->baseurl . '/templates/' . $this->template . '/css/'.$this->params->get('googleFontName').'.css');
	$doc->addStyleDeclaration("
	h1, h2, h3, h4, h5, h6, .site-title {
		font-family: '" . str_replace('+', ' ', $this->params->get('googleFontName')) . "','Times', serif;
	}");
}

// Template color
if ($this->params->get('templateColor'))
{
	$doc->addStyleDeclaration("
	body.site {
		background-color: " . $this->params->get('templateBackgroundColor') . ";
	}
	a {
		color: " . $this->params->get('templateColor') . ";
	}
	.nav-list > .active > a,
	.nav-list > .active > a:hover,
	.dropdown-menu li > a:hover,
	.dropdown-menu .active > a,
	.dropdown-menu .active > a:hover,
	.nav-pills > .active > a,
	.nav-pills > .active > a:hover {
		background: " . $this->params->get('templateColor') . ";
	}");
}

// Adiciona fonte
$doc->addStyleSheetVersion($this->baseurl . '/templates/' . $this->template . '/css/Merriweather.css');
// Check for a custom CSS file
$userCss = JPATH_SITE . '/templates/' . $this->template . '/css/user.css';

if (file_exists($userCss) && filesize($userCss) > 0)
{
	$this->addStyleSheetVersion($this->baseurl . '/templates/' . $this->template . '/css/user.css');
}

// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);

// Adjusting content width
if ($this->countModules('direita') && $this->countModules('direita2'))
{
	$spanMain = "span6";
	$spanDireita = "span3";
	$spanDireita2 = $spanDireita;
}
elseif ($this->countModules('direita') && !$this->countModules('direita2'))
{
	$spanMain = "span8";
	$spanDireita = "span4";
}
elseif (!$this->countModules('direita') && $this->countModules('direita2'))
{
	$spanMain = "span8";
	$spanDireita2 = "span4";
}
else
{
	$span = "span12";
}

// Logo file or site title param
if ($this->params->get('logoFile'))
{
	$logo = '<img src="' . JUri::root() . $this->params->get('logoFile') . '" alt="' . $sitename . '" />';
}
elseif ($this->params->get('sitetitle'))
{
	$logo = '<span class="site-title" title="' . $sitename . '">' . htmlspecialchars($this->params->get('sitetitle'), ENT_COMPAT, 'UTF-8') . '</span>';
}
else
{
	$logo = '<span class="site-title" title="' . $sitename . '">' . $sitename . '</span>';
}
?>
<!DOCTYPE html>
<!-- ************************************************************ -->
<!-- Desenvolvido por Glauber Mota <https://github.com/glauberm/> -->
<!-- ************************************************************ -->
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
	<link rel="icon" type="image/png" sizes="192x192"  href="<?php echo $this->baseurl . "/templates/" . $this->template; ?>/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $this->baseurl . "/templates/" . $this->template; ?>/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="<?php echo $this->baseurl . "/templates/" . $this->template; ?>/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $this->baseurl . "/templates/" . $this->template; ?>/favicon-16x16.png">
	<!--[if lt IE 9]><script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script><![endif]-->
</head>
<body class="site <?php echo $option
	. ' view-' . $view
	. ($layout ? ' layout-' . $layout : ' no-layout')
	. ($task ? ' task-' . $task : ' no-task')
	. ($itemid ? ' itemid-' . $itemid : '')
	. ($params->get('fluidContainer') ? ' fluid' : '');
	echo ($this->direction == 'rtl' ? ' rtl' : '');
?>">

	<div id="background-image" class="background-image"></div>

	<jdoc:include type="modules" name="login" style="xhtml" />

	<!-- Body -->
	<div class="body">
		<div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">
			<div class="row-fluid fullHeight">
				<div class="span3">
					<div class="barra-esquerda">
						<!-- Header -->
						<header class="header" role="banner">
							<div class="header-inner clearfix">
								<a class="brand" href="<?php echo $this->baseurl; ?>/">
									<?php echo $logo; ?>
									<?php if ($this->params->get('sitedescription')) : ?>
										<?php echo '<div class="site-description hidden">' . htmlspecialchars($this->params->get('sitedescription'), ENT_COMPAT, 'UTF-8') . '</div>'; ?>
									<?php endif; ?>
								</a>
								<div class="header-search">
									<jdoc:include type="modules" name="busca" style="xhtml" />
								</div>
							</div>
						</header>
						<?php if ($this->countModules('menu')) : ?>
							<nav class="navigation" role="navigation">
								<div class="navbar">
									<button class="btn btn-block btn-primary collapsed visible-phone" data-toggle="collapse" data-target=".nav-collapse">
										Menu
									</button>
								</div>
								<div class="nav-collapse">
									<jdoc:include type="modules" name="menu" style="none" />
								</div>
							</nav>
						<?php endif; ?>
					</div>
				</div>
				<div class="span9">
					<div class="row-fluid">
						<main id="content" role="main" class="<?php echo $spanMain; ?>">
							<!-- Banner -->
							<jdoc:include type="modules" name="banner" style="xhtml" />
							<br/>
							<!-- Begin Content -->
							<jdoc:include type="modules" name="cima" style="xhtml" />
							<jdoc:include type="message" />
							<jdoc:include type="component" />
							<jdoc:include type="modules" name="baixo" style="xhtml" />
							<!-- End Content -->
							<hr class="invisible"/>
						</main>
						<?php if ($this->countModules('direita')) : ?>
							<div id="aside" class="barra-direita <?php echo $spanDireita; ?>">
								<!-- Begin Right Sidebar -->
								<jdoc:include type="modules" name="direita" style="xhtml" />
								<!-- End Right Sidebar -->
							</div>
						<?php endif; ?>
						<?php if ($this->countModules('direita2')) : ?>
							<!-- Begin Sidebar -->
							<div id="sidebar" class="barra-direita2 <?php echo $spanDireita2; ?>">
								<div class="sidebar-nav">
									<jdoc:include type="modules" name="direita2" style="xhtml" />
								</div>
							</div>
							<!-- End Sidebar -->
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Footer -->
	<footer class="footer" role="contentinfo">
		<div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">
			<br/>
			<div class="text-center">
				<jdoc:include type="modules" name="rodape" style="xhtml" />
				<p>
					&copy; <?php echo date('Y'); ?> <?php echo $sitename; ?>
				</p>
				<a class="pull-right" href="#top" id="back-top">
					Voltar ao Topo
				</a>
			</div>
			<hr class="invisible" />
		</div>
	</footer>
	<jdoc:include type="modules" name="debug" style="none" />
</body>
</html>

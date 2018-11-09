<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app             = JFactory::getApplication();
$doc             = JFactory::getDocument();
$user            = JFactory::getUser();
$this->language  = $doc->language;
$this->direction = $doc->direction;

// Output document as HTML5.
if (is_callable(array($doc, 'setHtml5')))
{
	$doc->setHtml5(true);
}

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

// Logo file or site title param
if ($params->get('logoFile'))
{
	$logo = '<img src="' . JUri::root() . $params->get('logoFile') . '" alt="' . $sitename . '" />';
}
elseif ($params->get('sitetitle'))
{
	$logo = '<span class="site-title" title="' . $sitename . '">' . htmlspecialchars($params->get('sitetitle')) . '</span>';
}
else
{
	$logo = '<span class="site-title" title="' . $sitename . '">' . $sitename . '</span>';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta charset="utf-8" />
	<title><?php echo $this->title; ?> <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/template.css" rel="stylesheet" />
	<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/user.css" rel="stylesheet" />
	<?php // Use of Google Font ?>
	<?php if ($params->get('googleFont')) : ?>
		<link href="<?php echo $this->baseurl . '/templates/' . $this->template . '/css/Merriweather.css' ?>" rel="stylesheet" />
		<link href="<?php echo $this->baseurl . '/templates/' . $this->template . '/css/'.$params->get('googleFontName').'.css' ?>" rel="stylesheet" />
		<style>
			h1, h2, h3, h4, h5, h6, .site-title {
				font-family: '<?php echo str_replace('+', ' ', $params->get('googleFontName')) ?>', Times, serif;
			}
		</style>
	<?php endif; ?>
	<?php if ($app->get('debug_lang', '0') == '1' || $app->get('debug', '0') == '1') : ?>
		<link href="<?php echo JUri::root(true); ?>/media/cms/css/debug.css" rel="stylesheet" />
	<?php endif; ?>
	<?php // If Right-to-Left ?>
	<?php if ($this->direction == 'rtl') : ?>
		<link href="<?php echo JUri::root(true); ?>/media/jui/css/bootstrap-rtl.css" rel="stylesheet" />
	<?php endif; ?>
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
	<?php // Template color ?>
	<?php if ($params->get('templateColor')) : ?>
		<style>
			body.site {
				background-color: <?php echo $params->get('templateBackgroundColor'); ?>
			}
			a {
				color: <?php echo $params->get('templateColor'); ?>;
			}
			.navbar-inner, .nav-list > .active > a, .nav-list > .active > a:hover, .dropdown-menu li > a:hover, .dropdown-menu .active > a, .dropdown-menu .active > a:hover, .nav-pills > .active > a, .nav-pills > .active > a:hover {
				background: <?php echo $params->get('templateColor'); ?>;
			}
			.navbar-inner {
				-moz-box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
				-webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
				box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
			}
		</style>
	<?php endif; ?>
	<script src="<?php echo $this->baseurl . '/media/jui/js/jquery.min.js';?>"></script>
	<script src="<?php echo $this->baseurl . '/templates/' . $this->template . '/js/banner.js';?>"></script>
	<!--[if lt IE 9]><script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script><![endif]-->
</head>
<body class="site <?php echo $option
	. ' view-' . $view
	. ($layout ? ' layout-' . $layout : ' no-layout')
	. ($task ? ' task-' . $task : ' no-task')
	. ($itemid ? ' itemid-' . $itemid : '')
	. ($params->get('fluidContainer') ? ' fluid' : '');
?>">

<div id="background-image" class="background-image"></div>

<?php echo $doc->getBuffer('modules', 'login', array('style' => 'xhtml')); ?>

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
							</a>
							<div class="header-search">
								<?php echo $doc->getBuffer('modules', 'busca', array('style' => 'xhtml')); ?>
							</div>
						</div>
					</header>
					<nav class="navigation" role="navigation">
						<div class="navbar">
							<button class="btn btn-block btn-large btn-primary collapsed visible-phone" data-toggle="collapse" data-target=".nav-collapse">
								Menu
							</button>
						</div>
						<div class="nav-collapse">
							<?php echo $doc->getBuffer('modules', 'menu', array('style' => 'xhtml')); ?>
						</div>
					</nav>
				</div>
			</div>
			<div class="span9">
				<div class="row-fluid">
					<main id="content" role="main" class="span8">
						<!-- Banner -->
						<?php echo $doc->getBuffer('modules', 'banner', array('style' => 'xhtml')); ?>
						<br/>
						<!-- Begin Content -->
						<h1 class="page-header"><?php echo JText::_('JERROR_LAYOUT_PAGE_NOT_FOUND'); ?></h1>
						<div class="well">
							<div class="row-fluid">
								<div class="span6">
									<p><strong><?php echo JText::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></strong></p>
									<p><?php echo JText::_('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></p>
									<ul>
										<li><?php echo JText::_('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
										<li><?php echo JText::_('JERROR_LAYOUT_MIS_TYPED_ADDRESS'); ?></li>
										<li><?php echo JText::_('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
										<li><?php echo JText::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
									</ul>
								</div>
								<div class="span6">
									<?php if (JModuleHelper::getModule('search')) : ?>
										<p><strong><?php echo JText::_('JERROR_LAYOUT_SEARCH'); ?></strong></p>
										<p><?php echo JText::_('JERROR_LAYOUT_SEARCH_PAGE'); ?></p>
										<?php echo $doc->getBuffer('module', 'search'); ?>
									<?php endif; ?>
									<p><?php echo JText::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?></p>
									<p><a href="<?php echo $this->baseurl; ?>/index.php" class="btn"><span class="icon-home"></span> <?php echo JText::_('JERROR_LAYOUT_HOME_PAGE'); ?></a></p>
								</div>
							</div>
							<hr />
							<p><?php echo JText::_('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?></p>
							<blockquote>
								<span class="label label-inverse"><?php echo $this->error->getCode(); ?></span> <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8');?>
							</blockquote>
							<?php if ($this->debug) : ?>
								<?php echo $this->renderBacktrace(); ?>
							<?php endif; ?>
						</div>
						<!-- End Content -->
						<hr class="invisible"/>
					</main>
					<div id="aside" class="barra-direita span4">
						<!-- Begin Right Sidebar -->
						<?php echo $doc->getBuffer('modules', 'direita', array('style' => 'xhtml')); ?>
						<!-- End Right Sidebar -->
					</div>
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
			<?php echo $doc->getBuffer('modules', 'rodape', array('style' => 'xhtml')); ?>
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
<?php echo $doc->getBuffer('modules', 'debug', array('style' => 'none')); ?>
</body>
</html>

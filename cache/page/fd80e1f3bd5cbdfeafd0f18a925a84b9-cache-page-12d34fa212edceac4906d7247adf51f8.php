<?php die("Access Denied"); ?>#x#a:3:{s:4:"body";s:28850:"<!DOCTYPE html>
<!-- ************************************************************ -->
<!-- Desenvolvido por Glauber Mota <https://github.com/glauberm/> -->
<!-- ************************************************************ -->
<html lang="pt-br" dir="ltr">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta charset="utf-8" />
	<meta name="keywords" content="cinevi,departamento,cinema,vídeo,uff" />
	<meta name="description" content="Site do Departamento de Cinema e Vídeo da Universidade Federal Fluminense." />
	<meta name="generator" content="Joomla! - Open Source Content Management" />
	<title>Departamento de Cinema e Vídeo - UFF - Departamento de Cinema e Vídeo</title>
	<link href="/cinevi2/index.php?format=feed&amp;type=rss&amp;lang=br" rel="alternate" type="application/rss+xml" title="RSS 2.0" />
	<link href="/cinevi2/index.php?format=feed&amp;type=atom&amp;lang=br" rel="alternate" type="application/atom+xml" title="Atom 1.0" />
	<link href="http://www.cinevi.uff.br//cinevi2/index.php?lang=br" rel="canonical" />
	<link href="/cinevi2/templates/cinevi/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
	<link rel="stylesheet" href="/cinevi2/media/cms/css/debug.css" />
	<link rel="stylesheet" href="/cinevi2/templates/cinevi/css/template.css?a6af3f919ab6cdbfdc6862409d08794c" />
	<link rel="stylesheet" href="/cinevi2/templates/cinevi/css/Buenard.css?a6af3f919ab6cdbfdc6862409d08794c" />
	<link rel="stylesheet" href="/cinevi2/templates/cinevi/css/Merriweather.css?a6af3f919ab6cdbfdc6862409d08794c" />
	<link rel="stylesheet" href="/cinevi2/templates/cinevi/css/user.css?a6af3f919ab6cdbfdc6862409d08794c" />
	<link rel="stylesheet" href="/cinevi2/media/jui/css/chosen.css" />
	<link rel="stylesheet" href="/cinevi2/media/com_finder/css/finder.css" />
	<style>

	h1, h2, h3, h4, h5, h6, .site-title {
		font-family: 'Buenard','Times', serif;
	}
	body.site {
		background-color: #93979a;
	}
	a {
		color: #006886;
	}
	.nav-list > .active > a,
	.nav-list > .active > a:hover,
	.dropdown-menu li > a:hover,
	.dropdown-menu .active > a,
	.dropdown-menu .active > a:hover,
	.nav-pills > .active > a,
	.nav-pills > .active > a:hover {
		background: #006886;
	}
	</style>
	<script src="/cinevi2/media/jui/js/jquery.js"></script>
	<script src="/cinevi2/media/jui/js/jquery-noconflict.js"></script>
	<script src="/cinevi2/media/jui/js/jquery-migrate.js"></script>
	<script src="/cinevi2/media/system/js/caption-uncompressed.js"></script>
	<script src="/cinevi2/media/jui/js/bootstrap.js"></script>
	<script src="/cinevi2/templates/cinevi/js/template.js?a6af3f919ab6cdbfdc6862409d08794c"></script>
	<script src="/cinevi2/templates/cinevi/js/banner.js?a6af3f919ab6cdbfdc6862409d08794c"></script>
	<script src="/cinevi2/media/jui/js/chosen.jquery.js"></script>
	<script src="/cinevi2/media/jui/js/jquery.autocomplete.js"></script>
	<script>
jQuery(window).on('load',  function() {
				new JCaption('img.caption');
			});jQuery(function($){ $(".hasTooltip").tooltip({"html": true,"container": "body"}); });jQuery(function($){ $(".hasPopover").popover({"html": true,"placement": "top","trigger": "hover focus","container": "body"}); });
		jQuery(document).ready(function (){
			jQuery('.advancedSelect').chosen({
    "disable_search_threshold": 10,
    "search_contains": true,
    "allow_single_deselect": true,
    "placeholder_text_multiple": "Digite ou selecione algumas op\u00e7\u00f5es",
    "placeholder_text_single": "Selecione uma op\u00e7\u00e3o",
    "no_results_text": "Sem resultados correspondentes"
});
		});
	
jQuery(document).ready(function() {
	var value, searchword = jQuery('#mod-finder-searchword137');

		// Get the current value.
		value = searchword.val();

		// If the current value equals the default value, clear it.
		searchword.on('focus', function ()
		{
			var el = jQuery(this);

			if (el.val() === 'Pesquisar...')
			{
				el.val('');
			}
		});

		// If the current value is empty, set the previous value.
		searchword.on('blur', function ()
		{
			var el = jQuery(this);

			if (!el.val())
			{
				el.val(value);
			}
		});

		jQuery('#mod-finder-searchform137').on('submit', function (e)
		{
			e.stopPropagation();
			var advanced = jQuery('#mod-finder-advanced137');

			// Disable select boxes with no value selected.
			if (advanced.length)
			{
				advanced.find('select').each(function (index, el)
				{
					var el = jQuery(el);

					if (!el.val())
					{
						el.attr('disabled', 'disabled');
					}
				});
			}
		});
	var suggest = jQuery('#mod-finder-searchword137').autocomplete({
		serviceUrl: '/cinevi2/index.php?option=com_finder&amp;task=suggestions.suggest&amp;format=json&amp;tmpl=component&amp;lang=br',
		paramName: 'q',
		minChars: 1,
		maxHeight: 400,
		width: 300,
		zIndex: 9999,
		deferRequestBy: 500
	});});window.setInterval(function(){var r;try{r=window.XMLHttpRequest?new XMLHttpRequest():new ActiveXObject("Microsoft.XMLHTTP")}catch(e){}if(r){r.open("GET","/cinevi2/index.php?option=com_ajax&format=json",true);r.send(null)}},300000);
	</script>

	<!-- FAVICONS -->
	<link rel="apple-touch-icon" sizes="57x57" href="/cinevi2/templates/cinevi/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/cinevi2/templates/cinevi/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/cinevi2/templates/cinevi/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/cinevi2/templates/cinevi/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/cinevi2/templates/cinevi/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/cinevi2/templates/cinevi/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/cinevi2/templates/cinevi/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/cinevi2/templates/cinevi/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/cinevi2/templates/cinevi/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/cinevi2/templates/cinevi/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/cinevi2/templates/cinevi/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/cinevi2/templates/cinevi/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/cinevi2/templates/cinevi/favicon-16x16.png">
	<!--[if lt IE 9]><script src="/cinevi2/media/jui/js/html5.js"></script><![endif]-->
</head>
<body class="site com_content view-featured no-layout no-task itemid-101">

	<div id="background-image" class="background-image"></div>

			<div class="moduletable">
						<div class="userdata-wrap">
	<form action="http://localhost/cinevi2/index.php?lang=br" method="post" id="login-form" class="form-inline">
		<div class="userdata">
			<div class="login-icon-wrap">
				<div class="login-icon">
					<a class="no-style" href="/cinevi2/index.php?option=com_users&amp;view=login&amp;lang=br">
						<span class="icon-users" title="Nome de Usuário"></span>
													<span class="text-user">Acesso</span>
											</a>
				</div>
				<div class="login-data">

											<div class="input-prepend">
							<span class="add-on">
								<span class="icon-user hasTooltip" title="Nome de Usuário"></span>
								<label for="modlgn-username" class="element-invisible">Nome de Usuário</label>
							</span>
							<input id="modlgn-username" type="text" name="username" class="input-small" tabindex="0" size="18" placeholder="Nome de Usuário" />
						</div>
																<div class="input-prepend">
							<span class="add-on">
								<span class="icon-lock hasTooltip" title="Senha">
								</span>
									<label for="modlgn-passwd" class="element-invisible">Senha								</label>
							</span>
							<input id="modlgn-passwd" type="password" name="password" class="input-small" tabindex="0" size="18" placeholder="Senha" />
						</div>
																					<label for="modlgn-remember" class="checkbox">Lembrar-me<input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/></label>
										<button type="submit" tabindex="0" name="Submit" class="btn btn-inverse">Entrar</button>

										<ul class="unstyled">
													<li>
								<a href="/cinevi2/index.php?option=com_users&amp;view=registration&amp;Itemid=186&amp;lang=br">
								Criar uma Conta</a>
							</li>
												<li>
							<a href="/cinevi2/index.php?option=com_users&amp;view=remind&amp;Itemid=&amp;lang=br">
							Esqueceu seu usuário?</a>
						</li>
						<li>
							<a href="/cinevi2/index.php?option=com_users&amp;view=reset&amp;Itemid=&amp;lang=br">
							Esqueceu sua senha?</a>
						</li>
					</ul>
					<input type="hidden" name="option" value="com_users" />
					<input type="hidden" name="task" value="user.login" />
					<input type="hidden" name="return" value="aHR0cDovL2xvY2FsaG9zdC9jaW5ldmkyL2luZGV4LnBocD9sYW5nPWJy" />
					<input type="hidden" name="746c288912d0e3bc8f851b22fc0e3449" value="1" />									</div>
			</div>
		</div>
	</form>
</div>
		</div>
	

	<!-- Body -->
	<div class="body">
		<div class="container">
			<div class="row-fluid fullHeight">
				<div class="span3">
					<div class="barra-esquerda">
						<!-- Header -->
						<header class="header" role="banner">
							<div class="header-inner clearfix">
								<a class="brand" href="/cinevi2/">
									<img src="http://localhost/cinevi2/images/logo2.png" alt="Departamento de Cinema e Vídeo" />																			<div class="site-description hidden">Site do Departamento de Cinema e Vídeo do curso de Cinema da Universidade Federal Fluminense (UFF). Aqui você vai encontrar notícias, eventos, informações de contato e muito mais relacionado ao curso, ao departamento e à universidade.</div>																	</a>
								<div class="header-search">
											<div class="moduletable">
						
<form id="mod-finder-searchform137" action="/cinevi2/index.php?option=com_finder&amp;view=search&amp;Itemid=184&amp;lang=br" method="get" class="form-search">
	<div class="finder">
		<div class="input-append">
			<input type="text" name="q" id="mod-finder-searchword137" class="search-query" size="255" value="" placeholder="Pesquisar..."/>		</div>

						<input type="hidden" name="option" value="com_finder" /><input type="hidden" name="view" value="search" /><input type="hidden" name="Itemid" value="184" /><input type="hidden" name="lang" value="br" />	</div>
</form>
		</div>
	
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
									<ul class="nav menu">
<li class="item-101 default current active"><a href="/cinevi2/index.php?option=com_content&amp;view=featured&amp;Itemid=101&amp;lang=br" >Home</a></li><li class="item-121 deeper parent"><a href="/cinevi2/index.php?option=com_content&amp;view=category&amp;id=9&amp;Itemid=121&amp;lang=br" >Curso</a><ul class="nav-filho"><li class="item-125"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=2&amp;Itemid=125&amp;lang=br" >História</a></li><li class="item-128"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=3&amp;Itemid=128&amp;lang=br" >Bacharelado</a></li><li class="item-126"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=4&amp;Itemid=126&amp;lang=br" >Licenciatura</a></li><li class="item-127"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=5&amp;Itemid=127&amp;lang=br" >Administrativo</a></li></ul></li><li class="item-114 deeper parent"><span class="nav-header ">Espaço Discente</span>
<ul class="nav-filho"><li class="item-224"><a href="/cinevi2/index.php?option=com_fabrik&amp;view=list&amp;listid=8&amp;Itemid=224&amp;lang=br" >Projetos</a></li><li class="item-115"><a href="/cinevi2/index.php?option=com_fabrik&amp;view=form&amp;formid=8&amp;Itemid=115&amp;lang=br" >Cadastrar Projeto</a></li><li class="item-117"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=52&amp;Itemid=117&amp;lang=br" >Documentos</a></li></ul></li><li class="item-209 deeper parent"><span class="nav-header ">Almoxarifado</span>
<ul class="nav-filho"><li class="item-220"><a href="/cinevi2/index.php?option=com_fabrik&amp;view=list&amp;listid=6&amp;Itemid=220&amp;lang=br" >Equipamentos</a></li><li class="item-210"><a href="/cinevi2/index.php?option=com_fabrik&amp;view=form&amp;formid=6&amp;Itemid=210&amp;lang=br" >Cadastrar Equipamento</a></li><li class="item-222"><a href="/cinevi2/index.php?option=com_fabrik&amp;view=form&amp;formid=7&amp;Itemid=222&amp;lang=br" >Solicitar Equipamento</a></li></ul></li><li class="item-124"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=7&amp;Itemid=124&amp;lang=br" >Comissões</a></li><li class="item-120"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=6&amp;Itemid=120&amp;lang=br" >Contatos</a></li><li class="item-264"><a href="/cinevi2/index.php?option=com_tags&amp;view=tag&amp;id[0]=2&amp;types[0]=2&amp;Itemid=264&amp;lang=br" >Comissão de Produção</a></li><li class="item-265"><a href="/cinevi2/index.php?option=com_tags&amp;view=tag&amp;layout=list&amp;id[0]=2&amp;types[0]=2&amp;Itemid=265&amp;lang=br" >Comissões</a></li></ul>

								</div>
							</nav>
											</div>
				</div>
				<div class="span9">
					<div class="row-fluid">
						<main id="content" role="main" class="span8">
							<!-- Banner -->
									<div class="moduletable">
						<div class="random-image">
	<img src="/cinevi2/images/banners-mini/gil3.jpg" alt="gil3.jpg" width="100" height="27" /></div>
		</div>
	
							<br/>
							<!-- Begin Content -->
							
							<div id="system-message-container">
	</div>

							<div class="blog-featured" itemscope itemtype="https://schema.org/Blog">

	
		
		<div class="items-row cols-1 row-0 row-fluid">
					<div class="item column-1 span12"
				itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
			

	<h2 class="item-title" itemprop="name">
			<a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=117:urgente-aviso-sobre-inscricoes-em-disciplinas-gcv&amp;catid=8&amp;Itemid=183&amp;lang=br" itemprop="url">
			Informe sobre inscrição presencial bacharelado de cinema e audiovisual		</a>
		</h2>


	
<div class="icons">
	
					<div class="btn-group pull-right">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <span class="icon-cog"></span><span class="caret"></span> </a>
								<ul class="dropdown-menu">
											<li class="print-icon"> <a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=117:urgente-aviso-sobre-inscricoes-em-disciplinas-gcv&amp;catid=8&amp;Itemid=183&amp;tmpl=component&amp;print=1&amp;layout=default&amp;page=&amp;lang=br" title="Imprimir o artigo < Informe sobre inscrição presencial bacharelado de cinema e audiovisual >" onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;" rel="nofollow"><span class="icon-print"></span>Imprimir</a> </li>
																<li class="email-icon"> <a href="/cinevi2/index.php?option=com_mailto&amp;tmpl=component&amp;template=cinevi&amp;link=6fa15b370a0c585478ea9e6a6d6ef94100fbd4ff&amp;lang=br" title="Envie este link a um amigo" onclick="window.open(this.href,'win2','width=400,height=350,menubar=yes,resizable=yes'); return false;" rel="nofollow"><span class="icon-envelope"></span>Email</a> </li>
														</ul>
			</div>
		
	</div>





<p>Caros estudantes,</p>
<p>Comunicamos aqui o calendário de inscrição presencial para 2016.2.</p>
<p>Os alunos que não puderem comparecer nas datas marcadas poderão fazer as inscrições por meio de procurador (munido de procuração simples assinada pelo estudante) ou a partir do dia 29/08 (começo do ajuste UFF). Qualquer dúvida, basta entrar em contato pelos emails <span id="cloak444a4f124d7dd129f15bd7b27c37e4e3">Este endereço de email está sendo protegido de spambots. Você precisa do JavaScript ativado para vê-lo.</span><script type='text/javascript'>
				document.getElementById('cloak444a4f124d7dd129f15bd7b27c37e4e3').innerHTML = '';
				var prefix = '&#109;a' + 'i&#108;' + '&#116;o';
				var path = 'hr' + 'ef' + '=';
				var addy444a4f124d7dd129f15bd7b27c37e4e3 = '&#101;l&#105;&#97;nn&#101;.&#105;v&#111;' + '&#64;';
				addy444a4f124d7dd129f15bd7b27c37e4e3 = addy444a4f124d7dd129f15bd7b27c37e4e3 + 'gm&#97;&#105;l' + '&#46;' + 'c&#111;m';
				var addy_text444a4f124d7dd129f15bd7b27c37e4e3 = '&#101;l&#105;&#97;nn&#101;.&#105;v&#111;@gm&#97;&#105;l.c&#111;m ';document.getElementById('cloak444a4f124d7dd129f15bd7b27c37e4e3').innerHTML += '<a ' + path + '\'' + prefix + ':' + addy444a4f124d7dd129f15bd7b27c37e4e3 + '\'>'+addy_text444a4f124d7dd129f15bd7b27c37e4e3+'<\/a>';
		</script>e/ou <a href="mailto:ggx@vm.uff.br."><span id="cloakd7c620a6fba419223b4a83777b5c627d">Este endereço de email está sendo protegido de spambots. Você precisa do JavaScript ativado para vê-lo.</span><script type='text/javascript'>
				document.getElementById('cloakd7c620a6fba419223b4a83777b5c627d').innerHTML = '';
				var prefix = '&#109;a' + 'i&#108;' + '&#116;o';
				var path = 'hr' + 'ef' + '=';
				var addyd7c620a6fba419223b4a83777b5c627d = 'ggx' + '&#64;';
				addyd7c620a6fba419223b4a83777b5c627d = addyd7c620a6fba419223b4a83777b5c627d + 'vm' + '&#46;' + '&#117;ff' + '&#46;' + 'br';
				var addy_textd7c620a6fba419223b4a83777b5c627d = 'ggx' + '&#64;' + 'vm' + '&#46;' + '&#117;ff' + '&#46;' + 'br';document.getElementById('cloakd7c620a6fba419223b4a83777b5c627d').innerHTML += '<a ' + path + '\'' + prefix + ':' + addyd7c620a6fba419223b4a83777b5c627d + '\'>'+addy_textd7c620a6fba419223b4a83777b5c627d+'<\/a>';
		</script>.</a></p>
<p>Agradecemos a compreensão de todos.</p>
<p>Mauro Duque Estrada e Elianne Ivo</p>
<p><strong>Inscrição Presencial Curso Bacharelado Cinema e Audiovisual</strong></p>
<ul>
<li>24/08 - Inscrição calouros (conforme informado pela coordenação)</li>
<li>24/08 - de 13 às 17 hs - Inscrição de ingressantes 2015.2</li>
<li>25/08 - de 9 às 13 hs - Inscrição de ingressantes 2015.1 e 2014</li>
<li>25/08 - de 13 às 17 hs - Inscrição de ingressantes 2013</li>
<li>26/08 - de 9 às 13 hs - Inscrição de ingressantes antes de 2013</li>
<li>26/08 - de 13 às 17 hs - Inscrição de ingressantes antes de 2013</li>
</ul>



			</div>
			
			
		</div>
		
	
		
		<div class="items-row cols-1 row-1 row-fluid">
					<div class="item column-1 span12"
				itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
			

	<h2 class="item-title" itemprop="name">
			<a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=116:resultado-da-eleicao-da-coordenacao-de-bacharelado-em-cinema-e-audiovisual&amp;catid=8&amp;Itemid=183&amp;lang=br" itemprop="url">
			Resultado da eleição da Coordenação de Bacharelado em Cinema e Audiovisual		</a>
		</h2>


	
<div class="icons">
	
					<div class="btn-group pull-right">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <span class="icon-cog"></span><span class="caret"></span> </a>
								<ul class="dropdown-menu">
											<li class="print-icon"> <a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=116:resultado-da-eleicao-da-coordenacao-de-bacharelado-em-cinema-e-audiovisual&amp;catid=8&amp;Itemid=183&amp;tmpl=component&amp;print=1&amp;layout=default&amp;page=&amp;lang=br" title="Imprimir o artigo < Resultado da eleição da Coordenação de Bacharelado em Cinema e Audiovisual >" onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;" rel="nofollow"><span class="icon-print"></span>Imprimir</a> </li>
																<li class="email-icon"> <a href="/cinevi2/index.php?option=com_mailto&amp;tmpl=component&amp;template=cinevi&amp;link=a529ea7826acd24255ae43680d82ddde843c2ee6&amp;lang=br" title="Envie este link a um amigo" onclick="window.open(this.href,'win2','width=400,height=350,menubar=yes,resizable=yes'); return false;" rel="nofollow"><span class="icon-envelope"></span>Email</a> </li>
														</ul>
			</div>
		
	</div>





<!-- [if gte mso 9]><xml>
 <o:OfficeDocumentSettings>
  <o:AllowPNG/>
 </o:OfficeDocumentSettings>
</xml><![endif]-->
<p class="MsoNormal">A comissão Eleitoral designada pela DTS IACS nº 14, de 17 de maio de 2016, publicada no BS nº 084, de 23 de maio de 2016, no uso de suas atribuições vem comunicar e tornar público à comunidade acadêmica o resultado geral da consulta eleitoral para escolha da Coordenação e Vice-coordenação do Bacharelado em Cinema e audiovisual. A única chapa inscrita, a chapa um, foi constituída pelos professores Mauro Duque Estrada Moderno (para coordenação) e Elianne Ivo Barroso (para vice-coordenação).</p>



	
<p class="readmore">
	<a class="btn" href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=116:resultado-da-eleicao-da-coordenacao-de-bacharelado-em-cinema-e-audiovisual&amp;catid=8&amp;Itemid=183&amp;lang=br" itemprop="url">
		<span class="icon-chevron-right"></span>
		Leia mais...	</a>
</p>



			</div>
			
			
		</div>
		
	

	<div class="pagination">
		<ul class="pagination-list"><li class="disabled"><a><span class="icon-first"></span></a></li><li class="disabled"><a><span class="icon-previous"></span></a></li><li class="active hidden-phone"><a>1</a></li><li class="hidden-phone"><a title="2" href="/cinevi2/index.php?limitstart=2&amp;lang=br" class="pagenav">2</a></li><li class="hidden-phone"><a title="3" href="/cinevi2/index.php?limitstart=4&amp;lang=br" class="pagenav">3</a></li><li class="hidden-phone"><a title="4" href="/cinevi2/index.php?limitstart=6&amp;lang=br" class="pagenav">4</a></li><li class="hidden-phone"><a title="5" href="/cinevi2/index.php?limitstart=8&amp;lang=br" class="pagenav">...</a></li><li class="hidden-phone"><a title="6" href="/cinevi2/index.php?limitstart=10&amp;lang=br" class="pagenav">6</a></li><li class="hidden-phone"><a title="7" href="/cinevi2/index.php?limitstart=12&amp;lang=br" class="pagenav">7</a></li><li class="hidden-phone"><a title="8" href="/cinevi2/index.php?limitstart=14&amp;lang=br" class="pagenav">8</a></li><li class="hidden-phone"><a title="9" href="/cinevi2/index.php?limitstart=16&amp;lang=br" class="pagenav">9</a></li><li class="hidden-phone"><a title="10" href="/cinevi2/index.php?limitstart=18&amp;lang=br" class="pagenav">10</a></li><li><a title="Próximo" href="/cinevi2/index.php?limitstart=2&amp;lang=br" class="pagenav"><span class="icon-next"></span></a></li><li><a title="Fim" href="/cinevi2/index.php?limitstart=22&amp;lang=br" class="pagenav"><span class="icon-last"></span></a></li></ul>					<p class="counter">
				Página 1 de 12			</p>
			</div>

</div>

									<div class="moduletable">
							<h3>Links Úteis</h3>
						<div class="bannergroup">

	<div class="banneritem">
																																																															<a
							href="/cinevi2/index.php?option=com_banners&amp;task=click&amp;id=1&amp;lang=br" target="_blank"
							title="Universidade Federal Fluminense">
							<img
								src="http://localhost/cinevi2/images/links-uteis/uff.jpg"
								alt="Universidade Federal Fluminense"
								width ="64"								height ="64"							/>
						</a>
																<div class="clr"></div>
	</div>

</div>
		</div>
	
							<!-- End Content -->
							<hr class="invisible"/>
						</main>
													<div id="aside" class="barra-direita span4">
								<!-- Begin Right Sidebar -->
										<aside class="moduletable">
						<ul class="nav menu">
<li class="item-153 active deeper parent"><a href="/cinevi2/index.php?option=com_contact&amp;view=category&amp;id=18&amp;Itemid=153&amp;lang=br" >Corpo Docente</a><ul class="unstyled"><li class="item-152"><a href="/cinevi2/index.php?option=com_contact&amp;view=contact&amp;id=1&amp;Itemid=152&amp;lang=br" ><img src="/cinevi2/images/professores/1.jpg" alt="Aída Marques" /><span class="image-title">Aída Marques</span></a></li><li class="item-155"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=12&amp;Itemid=155&amp;lang=br" ><img src="/cinevi2/images/professores/3.jpg" alt="Antônio Moreno" /><span class="image-title">Antônio Moreno</span></a></li><li class="item-157"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=14&amp;Itemid=157&amp;lang=br" >Daniel Pinna</a></li><li class="item-156"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=13&amp;Itemid=156&amp;lang=br" >Cezar Migliorin</a></li><li class="item-158"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=15&amp;Itemid=158&amp;lang=br" >Elianne Ivo</a></li><li class="item-159"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=16&amp;Itemid=159&amp;lang=br" >Eliany Salvatierra</a></li><li class="item-160"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=17&amp;Itemid=160&amp;lang=br" >Fabían Núñez</a></li><li class="item-161"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=18&amp;Itemid=161&amp;lang=br" >Felipe Muanis</a></li><li class="item-162"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=19&amp;Itemid=162&amp;lang=br" >Fernando Morais</a></li><li class="item-163"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=20&amp;Itemid=163&amp;lang=br" >Hadija Chalupe</a></li><li class="item-164"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=21&amp;Itemid=164&amp;lang=br" >Heloísa Toledo</a></li><li class="item-165"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=22&amp;Itemid=165&amp;lang=br" >Índia Mara Martins</a></li><li class="item-166"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=23&amp;Itemid=166&amp;lang=br" >João Luiz Leocádio</a></li><li class="item-167"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=24&amp;Itemid=167&amp;lang=br" >João Luiz Vieira</a></li><li class="item-228"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=114&amp;Itemid=228&amp;lang=br" >Mariana Baltar</a></li><li class="item-169"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=26&amp;Itemid=169&amp;lang=br" >Marina Tedesco</a></li><li class="item-170"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=27&amp;Itemid=170&amp;lang=br" >Maurício de Bragança</a></li><li class="item-171"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=28&amp;Itemid=171&amp;lang=br" >Rafael de Luna</a></li><li class="item-173"><a href="/cinevi2/index.php?option=com_content&amp;view=article&amp;id=30&amp;Itemid=173&amp;lang=br" >Tunico Amâncio</a></li></ul></li></ul>
		</aside>
			<aside class="moduletable no-lead">
						<div class="newsflash no-lead">
			
	

<hr class="invisible" />
<p><a href="www.rascunho.uff.br/" title="Revista Rascunho"><img src="images/rascunho.jpg" alt="Revista Rascunho" class="img-responsive" style="width: 100%;" /></a></p>
<p>A revista Rascunho foi criada em 2008, pelos professores do Departamento de Cinema e Vídeo da Universidade Federal Fluminense, com o objetivo de divulgar os melhores trabalhos de conclusão de curso dos alunos de graduação.</p>
<p>A revista publica exclusivamente monografias defendidas na disciplina da graduação em Cinema e Audiovisual “Projeto Experimental” que tenham sido aprovadas com nota máxima e indicadas para publicação pela banca examinadora. Esses textos são ainda avaliados por pareceristas escolhidos preferencialmente dentre o corpo discente do Programa de Pós-Graduação em Comunicação, Imagem e Informação da UFF.</p>
<p><a href="http://www.rascunho.uff.br/" class="btn btn-default btn-small" title="Revista Rascunho">Leia a Revista</a></p>

	</div>
		</aside>
	
								<!-- End Right Sidebar -->
							</div>
																	</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Footer -->
	<footer class="footer" role="contentinfo">
		<div class="container">
			<br/>
			<div class="text-center">
				
				<p>
					&copy; 2016 Departamento de Cinema e Vídeo				</p>
				<a class="pull-right" href="#top" id="back-top">
					Voltar ao Topo
				</a>
			</div>
			<hr class="invisible" />
		</div>
	</footer>
	
</body>
</html>
";s:13:"mime_encoding";s:9:"text/html";s:7:"headers";a:5:{i:0;a:2:{s:4:"name";s:12:"Content-Type";s:5:"value";s:24:"text/html; charset=utf-8";}i:1;a:2:{s:4:"name";s:7:"Expires";s:5:"value";s:29:"Wed, 17 Aug 2005 00:00:00 GMT";}i:2;a:2:{s:4:"name";s:13:"Last-Modified";s:5:"value";s:29:"Wed, 31 Aug 2016 08:03:13 GMT";}i:3;a:2:{s:4:"name";s:13:"Cache-Control";s:5:"value";s:62:"no-store, no-cache, must-revalidate, post-check=0, pre-check=0";}i:4;a:2:{s:4:"name";s:6:"Pragma";s:5:"value";s:8:"no-cache";}}}
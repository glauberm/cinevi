/* Glauber Mota (glaubernm@gmail.com) */

(function($)
{
	$(document).ready(function()
	{

		/* Menu */

		/* grudar no topo */
		$(window).scroll( function () {
			if ($(window).scrollTop() > $('#faixa-logo-xs').height()) {
				$('#faixa-menu-xs').addClass('fixed');
			} else {
				$('#faixa-menu-xs').removeClass('fixed');
			}
		});

		/* Menu Lateral */
		$('#botao-menu-sm').click(function ()
		{
			$('#menu-sm').animate({
				left: "0"
			}, 400, function() {
				$(this).addClass('aberto');
				$('#botao-menu-sm').addClass('ativo');
				$('body').css('overflow', 'hidden');
			});
			$('#menu-sm .moduletable ul li').click(function() {
				$('#menu-sm').animate({
					left: "-100%"
				}, 400, function() {
					$(this).removeClass('aberto');
					$('body').css('overflow', 'auto');
				});
			});
		});

		$('#botao-menu-xs').click(function ()
		{
			$('#menu-xs').animate({
				left: "0"
			}, 400, function() {
				$(this).addClass('aberto');
				$('body').css('overflow', 'hidden');
			});

			$('#menu-xs .moduletable ul li').click(function() {
				$('#menu-xs').animate({
					left: "-100%"
				}, 400, function() {
					$(this).removeClass('aberto');
					$('body').css('overflow', 'auto');
				});
			});
		});

		/* Máscaras dos inputs */
		$('.telefone').mask('(00) 00000-0000');
		$('.matricula').mask('000000000');
		$('.periodo-entrada').mask('0000/0');

		/* Navbars */
		$('.nav-pills li a').click(function () {
			$(this).parent().addClass('active');
		});

		/* Busca */
		$('#barra-lateral .header-search .form-control').focus(function () {
			$(this).parent().parent().parent().parent('.header-search').css('opacity','1');
		});

		$('#barra-lateral .header-search .form-control').focusout(function () {
			$(this).parent().parent().parent().parent('.header-search').css('opacity','');
		});

		/* Fix: Fabrik Form */
		$('textarea.fabrikinput').addClass('form-control');
		$('fieldset.fabrikGroup .row-fluid').removeClass('row-fluid').addClass('row');
	})
})(jQuery);

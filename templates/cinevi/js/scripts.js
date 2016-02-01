/* Glauber Mota (glaubernm@gmail.com) */

(function($)
{
	$(document).ready(function()
	{

		/* Menu */

		/* transformicons */
		transformicons.add('.tcon');

		/* grudar no topo */

		$(window).scroll( function () {
			if ($(window).scrollTop() > $('#faixa-logo-xs').height()) {
				$('#botao-menu-xs').addClass('fixed');
			} else {
				$('#botao-menu-xs').removeClass('fixed');
			}
		});

		/* Menu Lateral */

		$('#botao-menu-sm').click(function () {

			if($('#menu-sm').hasClass('aberto'))
			{
				$('#menu-sm').animate({
					left: "-100%"
				}, 400, function() {
					$(this).removeClass('aberto');
					$('#botao-menu-sm').removeClass('ativo');
					$('body').css('overflow', 'auto');
				});

			} else
			{
				$('#menu-sm').animate({
					left: "0"
				}, 400, function() {
					$(this).addClass('aberto');
					$('#botao-menu-sm').addClass('ativo');
					$('body').css('overflow', 'hidden');
				});
			}

		});

		$('#botao-menu-xs').click(function () {

			if($('#menu-xs').hasClass('aberto'))
			{
				$('#menu-xs').animate({
					left: "-100%"
				}, 400, function() {
					$(this).removeClass('aberto');
					$('#botao-menu-xs').css('backgroundColor','#006888');
					$('body').css('overflow', 'auto');
				});

			} else
			{

				$('#botao-menu-xs').css('backgroundColor','transparent');

				$('#menu-xs').animate({
					left: "0"
				}, 400, function() {
					$(this).addClass('aberto');
					$('body').css('overflow', 'hidden');
				});
			}

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

	})
})(jQuery);

/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.2
 */

(function($)
{
	$(document).ready(function()
	{
		$('*[rel=tooltip]').tooltip()

		// Turn radios into btn-group
		$('.radio.btn-group label').addClass('btn');
		$(".btn-group label:not(.active)").click(function()
		{
			var label = $(this);
			var input = $('#' + label.attr('for'));

			if (!input.prop('checked')) {
				label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
				if (input.val() == '') {
					label.addClass('active btn-primary');
				} else if (input.val() == 0) {
					label.addClass('active btn-danger');
				} else {
					label.addClass('active btn-success');
				}
				input.prop('checked', true);
			}
		});
		$(".btn-group input[checked=checked]").each(function()
		{
			if ($(this).val() == '') {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-primary');
			} else if ($(this).val() == 0) {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-danger');
			} else {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-success');
			}
		});
		
		/* Adicionado por Glauber Mota (glaubernm@gmail.com) */
		
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
		
	})
})(jQuery);

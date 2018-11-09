(function($){
	$(document).ready(function()
	{
		var src = $('.random-image img').attr('src');
        $('#background-image').css('background-image','url("'+src+'"');

		src = src.replace('/images/banners/', '');
		src = src.replace('.jpg', '');

		for(i=1;i<=20;i++) {

			while( src.indexOf(i+'++++') >= 0) {
				src = src.replace(i+'++++', ' ');
			}
		}
		while( src.indexOf('+') >= 0) {
			src = src.replace('+', ' ');
		}
		while( src.indexOf('-') >= 0) {
			src = src.replace('-', ' ');
		}
		while( src.indexOf('_') >= 0) {
			src = src.replace('_', ' ');
		}
		while( src.indexOf('%20') >= 0) {
			src = src.replace('%20', ' ');
		}
		src = src.trim();
		$('.random-image').append('<span class="random-image-caption">'+src+'</span>');

		// Still FabrikForm
		$('.fb_el_cine7_copias_finais_de_filmes___FotosStill').prepend('<span class="still-dica">Atenção! Tenha em mente que estes arquivos serão usados na imagem do topo e do fundo do site e <strong>os nomes dos arquivos serão utilizados como legenda</strong>. Com isto em mente, <strong>renomeie os arquivos, colocando o nome do filme como nome de arquivo e retirando qualquer caractere especial (cedilha, acentos) antes de enviá-los</strong>.</span>');
	})
})(jQuery);

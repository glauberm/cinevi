(function($){
	$(document).ready(function()
	{
		var src = $('.random-image img').attr('src');
        $('#background-image').css('background-image','url("'+src+'"');

		src = src.replace('/images/banners/', '');
		src = src.replace('.jpg', '');

		while( src.indexOf('+') >= 0) {
			src = src.replace('+', ' ');
		}
		src = src.trim();
		$('.random-image').append('<span class="random-image-caption">'+src+'</span>');
	})
})(jQuery);

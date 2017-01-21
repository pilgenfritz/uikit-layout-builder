$(document).ready(function()
{
	var $window_mapa = $('#window-mapa'),
		mapa_once_open = false;
	
	/* abre mapa */
	$('span.ver-mapa').on('click', function(){
		$window_mapa.addClass('showing');
		if(!mapa_once_open){
			var coordenadas = $(this).data('coordenadas');
			$window_mapa.append('<div id="map-canvas" data-coordenadas="'+coordenadas+'"></div>')
			loadScript();	
		}
		
	});

	/* fecha mapa */
	$('#window-mapa .fechar-mapa').on('click', function(){
		$window_mapa.removeClass('showing');		
		mapa_once_open = true;
	});

	var menuFixedOnScreen = false;
	$(window).on('scroll', function(){
		var pos = $(this).scrollTop();
		if(pos > 80){
			if(!menuFixedOnScreen){
				$('nav.uk-navbar').addClass('scroll')
				menuFixedOnScreen = true;	
			}
		}else{
			if(menuFixedOnScreen){
				$('nav.uk-navbar').removeClass('scroll')		
				menuFixedOnScreen = false;
			}
		}
	})

});
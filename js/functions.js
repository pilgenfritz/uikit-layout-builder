function getMobile(){
	if(!((/Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i).test(navigator.userAgent || navigator.vendor || window.opera)) && $(window).width() > 1024){
		return false;
	}else{
		return true;
	}
}
function setAlturaSlider(altura, largura){ //Define a altura do slider quando for 100% da tela
	if(altura > largura){
		altura = largura;
	}
	$('#slider.full .slick-slide, #slider.full').css('height', altura);
	/*$('#main').css('top', alt);*/
}
function initSlider(){ // Inicia plugin de slider
	$('.slider').slick({
		dots: true
	});
}

//Google Maps async
function GoogleMapsload() {
  var script = document.createElement('script');
  script.type = 'text/javascript';
  script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&' +
      'callback=GoogleMapsInit&key=AIzaSyB_y5AX__5-gftCOUh4_S85H3b1y1Y66p4';
  document.body.appendChild(script);
}

function GoogleMapsInit()
{
  var getCodnt = $('#map-canvas').attr('data-coordenadas');
  var Coordenadas = getCodnt.split(",");
  var Latitude = Coordenadas[0];
  var Longitude = Coordenadas[1];

  var mapOptions = {
    zoom: 16,
    scrollwheel: false,
    navigationControl: false,
    mapTypeControl: false,
    /*scaleControl: false,
    draggable: false,*/
    center: new google.maps.LatLng(Latitude,Longitude),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

  var map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

      var image = 'img/default/map-local.png';
      var myLatLng = new google.maps.LatLng(Latitude,Longitude);
      var beachMarker = new google.maps.Marker({
          position: myLatLng,
          map: map,
          icon: image
      });
}
function verificaLargura(width){ //verifica largura da tela
	var dispositivo ='';
	if(width > 1024){
		dispositivo = 'desktop';
	}else{
		if(width <= 1024 && width > 768){
			dispositivo = 'tablet-horizontal';
		}else if(width <= 768 && width > 560){
			dispositivo = 'tablet-vertical';
		}else{
			dispositivo = 'mobile';
		}
	}
	return dispositivo;
}
function setSrc(img, larguraTela){
	srcDesk = img.data('desktop');
	srcMob = img.data('mobile');
	if(larguraTela >= 1025){
		img.attr('src', srcDesk);
	}else{
		img.attr('src', srcMob);
	}
}
function goToTarget(target){
	$('html, body').animate({
	    scrollTop: target
	}, 1000);
}
function AnimateCSS(target,x) {
	$(target).removeClass().addClass(x + ' animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
	  $(this).removeClass();
	});
}
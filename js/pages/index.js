$(document).ready(function()
{
	var largura = $(window).width();
	var altura = $(window).height();

	//atrasar carregamento de imagens
	if (typeof lazy !== 'undefined' && $.isFunction(lazy))
	{
		$("img.lazy").lazy();
	}
	

	//remove loader quando página carregada
	$(window).on('load', function(){
		/*setTimeout(function() {
			setAlturaSlider(altura, largura);
		}, 200);*/
	});

	$('.circle-line .item').on('click', function(){
		var $parent = $(this).parent(),
			rotatePlus = $(this).data('rotation-plus'),
			rotateMinus = $(this).data('rotation-minus'),
			currentRotation = $parent.rotationDegrees();

			if((Math.abs(currentRotation) - rotatePlus) < (Math.abs(currentRotation) - Math.abs(rotateMinus))){
				rotationCircle = rotateMinus;
			}else{
				rotationCircle = rotatePlus;
			}

			fnRotationCircle($(this), $parent, rotationCircle)
			changeTextCenter($(this))
			
	})

	function fnRotationCircle(target, parent, rotationCircle){
		$('.circle-line .item').removeClass('active')
		target.addClass('active clicked')
		var tlCircle = new TimelineMax()
			tlCircle
				.to(parent, 1, {rotation: rotationCircle})
				.to(parent.find('.item'), 0.1, {rotation: -rotationCircle, scale:0.5 }, 0)
				.to(target, 0.5, {scale: 1}, 0)
	}

	function changeTextCenter(target){
		var textId = target.data('id'),
			$parent = $('.explain')

		var tlText = new TimelineMax()
			tlText
				.to($parent.find('h2'), 0.5, {autoAlpha:0, y:"-30%" })
				.to($parent.find('.frase-'+textId), 0.5, {autoAlpha: 1, y:"-50%"})
	}

	/*var grid = UIkit.grid($('#grid'), {
		gutter : 20,
		animation: true,
		duration: 200,
		controls: '#filter',
		filter: 'filter-a, filter-b'
	});	*/

	//define a algura do header, verificando se o menu está sobre o slider
	/*if($('#main-menu').hasClass('over_slider')) var altura = $(window).height();
	else var altura = ($(window).height() - $('#main-menu').height());*/
	initSlider();

	//vai para a próxima seção quando clica na setinha do slider
	/*$('.arrow-down').on('click', function(){
		goToTarget(altura);
	});*/

	//Carousel
	/*$('#crsl-produtos').owlCarousel({
		responsive:{
            0:{
                items: 1
            },
            480:{
                items: 2
            },
            769:{
            	items: 3
            }
        },
        nav: true,
        margin: 10,
		navText: [
    		"<i class='fa fa-angle-left fa-3x nav-produtos nav-left'></i>",
    		"<i class='fa fa-angle-right fa-3x nav-produtos nav-right'></i>"
    	]
	});*/

	//do when window resizes
	$(window).on('resize', function(){
		/*newWidth = $(window).width();
		newHeight = $(window).height();
		
		setAlturaSlider(newHeight, newWidth);*/
	});

});

(function ($) {
    $.fn.rotationDegrees = function () {
         var matrix = this.css("-webkit-transform") ||
    this.css("-moz-transform")    ||
    this.css("-ms-transform")     ||
    this.css("-o-transform")      ||
    this.css("transform");
    if(typeof matrix === 'string' && matrix !== 'none') {
        var values = matrix.split('(')[1].split(')')[0].split(',');
        var a = values[0];
        var b = values[1];
        var angle = Math.round(Math.atan2(b, a) * (180/Math.PI));
    } else { var angle = 0; }
    return angle;
   };
}(jQuery));

/*function getNearestNumber(a, n){
    if((l = a.length) < 2)
        return l - 1;
    for(var l, p = Math.abs(a[--l] - n); l--;)
        if(p < (p = Math.abs(a[l] - n)))
            break;
    return l + 1;
}*/

var triggerHookPos;
if($(window).width() >= 1024){
	triggerHookPos = 0.5;
}else{
	triggerHookPos = 1;
}

		/*var tlSobre = new TimelineMax();
			tlSobre.to(".section-sobre .img-sobre", 3, {autoAlpha: 1})
				   .to(".section-sobre h1", 1, {y:0, autoAlpha: 1},0)
				   .to(".section-sobre h3", 1, {y:0, autoAlpha: 1},0)
				   .to(".section-sobre h2", 1, {y:0, autoAlpha: 1},0)
				   .to(".section-sobre .rd-sobre-text", 1, {y:0, autoAlpha: 1}, "-=2")*/

				   

		// build scene
		/*var sceneSobre = new ScrollMagic.Scene({
							triggerElement: ".section-sobre",
							triggerHook: triggerHookPos,
							duration: 0
						})
						.setTween(tlSobre)
						.addTo(controller);*/
 		
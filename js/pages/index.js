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
		setTimeout(function() {
			setAlturaSlider(altura, largura);
		}, 200);
	});

	var grid = UIkit.grid($('#grid'), {
		gutter : 20,
		animation: true,
		duration: 200,
		controls: '#filter',
		filter: 'filter-a, filter-b'
	});	

	//define a algura do header, verificando se o menu está sobre o slider
	/*if($('#main-menu').hasClass('over_slider')) var altura = $(window).height();
	else var altura = ($(window).height() - $('#main-menu').height());*/
	initSlider();

	//vai para a próxima seção quando clica na setinha do slider
	/*$('.arrow-down').on('click', function(){
		goToTarget(altura);
	});*/

	//Carousel
	$('#crsl-produtos').owlCarousel({
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
	});

	//do when window resizes
	$(window).on('resize', function(){
		newWidth = $(window).width();
		newHeight = $(window).height();
		
		setAlturaSlider(newHeight, newWidth);
	});

});

var triggerHookPos;
if($(window).width() >= 1024){
	triggerHookPos = 0.5;
}else{
	triggerHookPos = 1;
}

		var tlSobre = new TimelineMax();
			tlSobre.to(".section-sobre .img-sobre", 3, {autoAlpha: 1})
				   .to(".section-sobre h1", 1, {y:0, autoAlpha: 1},0)
				   .to(".section-sobre h3", 1, {y:0, autoAlpha: 1},0)
				   .to(".section-sobre h2", 1, {y:0, autoAlpha: 1},0)
				   .to(".section-sobre .rd-sobre-text", 1, {y:0, autoAlpha: 1}, "-=2")

				   

		// build scene
		var sceneSobre = new ScrollMagic.Scene({
							triggerElement: ".section-sobre",
							triggerHook: triggerHookPos,
							duration: 0
						})
						.setTween(tlSobre)
						//.addIndicators({name: "tween css class"}) // add indicators (requires plugin)
						.addTo(controller);
 		
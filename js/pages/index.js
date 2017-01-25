var controller = new ScrollMagic.Controller();
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
			var tween = TweenLite.to('#main-img', 1.5, {scale: 1})
		}, 500);
	});

	$('.circle-line .item').on('click', function(){
		var $parent1 = $('.circle-line.circle-1'),
			$parent2 = $('.circle-line.circle-2'),
			/*rotatePlus = $(this).data('rotation-plus'),
			rotateMinus = $(this).data('rotation-minus'),*/
			currentRotation1 = $parent1.rotationDegrees();
			currentRotation2 = $parent2.rotationDegrees();

			/*if((Math.abs(currentRotation) - rotatePlus) < (Math.abs(currentRotation) - Math.abs(rotateMinus))){
				rotationCircle = rotateMinus;
			}else{
				rotationCircle = rotatePlus;
			}*/

			rotationCircle1 = currentRotation1 - 45;
			rotationCircle2 = currentRotation2 - 45;

			fnRotationCircle($(this), $parent1, $parent2, rotationCircle1, rotationCircle2)
			changeTextCenter($(this))
			
	})

	function fnRotationCircle(target, parent1, parent2, rotationCircle1, rotationCircle2){
		$('.circle-line .item').removeClass('active')
		target.addClass('active clicked')
		var tlCircle = new TimelineMax()
			tlCircle
				/*.to(parent1, 1, {rotation: rotationCircle1})
				.to(parent2, 1, {rotation: rotationCircle2},0)*/
				/*.to(parent1.find('.item'), 0.1, {rotation: -rotationCircle1, scale:0.5 }, 0)
				.to(parent2.find('.item'), 0.1, {rotation: -rotationCircle2, scale:0.5 }, 0)*/
				.to('.circle-line .item', 0.5, {scale:0.5})
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

var triggerHookPos;
if($(window).width() >= 1024){
	triggerHookPos = 0.5;
}else{
	triggerHookPos = 1;
}

		
		var tlSobre = TweenLite.to("section#about .wrap-img-sobre", 2, {x: "0px", y:"0px"})
			/*var tlSobre = new TimelineMax();
			tlSobre.to("section#about .wrap-img-sobre", 2, {x: "0px", y:"0px"})*/

		// build scene
		var sceneSobre = new ScrollMagic.Scene({
							triggerElement: "section#about",
							triggerHook: 1,
							duration: 500
						})
						.setTween(tlSobre)
						.addTo(controller);


		var tlExpertises = new TimelineMax();
			tlExpertises.to("section#judice .circle-line", 0.5, {autoAlpha: 1})
						.staggerTo("section#judice .circle-line .item", 1, {autoAlpha: 1}, 0.1, "-=0.2")
						.to("section#judice .explain", 1, {autoAlpha:1}, "-=0.8")

		// build scene
		var sceneExpertises = new ScrollMagic.Scene({
							triggerElement: "section#judice",
							triggerHook: triggerHookPos,
							duration: 0
						})
						.setTween(tlExpertises)
						.addTo(controller);


		var tlAreas = new TimelineMax();
			tlAreas.to("section#areas-de-atuacao h1", 0.5, {autoAlpha: 1, y:0})
						.staggerTo("section#areas-de-atuacao ul li", 1, {autoAlpha: 1, y:"0px"}, 0.15)
						.to("section#areas-de-atuacao .link-more", 1, {autoAlpha:1})

		// build scene
		var sceneAreas = new ScrollMagic.Scene({
							triggerElement: "section#areas-de-atuacao",
							triggerHook: triggerHookPos,
							duration: 0
						})
						.setTween(tlAreas)
						.addTo(controller);	

		var tlDiferenciais = new TimelineMax();
			tlDiferenciais.to("section#diferenciais h1", 0.5, {autoAlpha: 1, y:0})
						.staggerTo("section#diferenciais ul li", 1, {autoAlpha: 1, x:"0px"}, 0.1)

		// build scene
		var sceneDiferenciais = new ScrollMagic.Scene({
							triggerElement: "section#diferenciais",
							triggerHook: triggerHookPos,
							duration: 0
						})
						.setTween(tlDiferenciais)
						.addTo(controller);								
 		
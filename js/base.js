$(document).ready(function()
{
	//Google Maps Async
	if($("#map-canvas").length) GoogleMapsload();

	//loader
	$(window).on('load', function(){
		$('body').removeClass('loading');
		$('#loader').remove();
	});

	if($('.tm-section-background-image').length) {
		$('.tm-section-background-image').each(function() {
			$(this).backstretch($(this).data('source'));
		});
	}

	backstretchResize()
	
	function backstretchResize(){
		$('.back-figure').each(function(index){
			$parent = $(this);
			backSrc = $(this).find('img').attr('src');
			$(this).backstretch(backSrc)
			$parent.find('.back-img').hide();
		})
	}

	//fake placeholder
	$('form.validar').on('click keyup change', 'input, textarea', function(){
		$parent = $(this).parent();
		$parent.find('label').addClass('active');
	});


	//jquery validate form .validar
	$("form.validar").on('submit', function(){
		$form = $(this);
		$(this).validate({
			submitHandler: function(form){
				var formId = $form.attr('id');
				var formAction = $form.attr('action');
				$form.addClass('loading');
				$.ajax({
				  url: formAction,
				  type: 'POST',
				  data: new FormData(document.getElementById(formId)),
				  processData: false,
				  contentType: false,
				  success:function(data){
				    $form.hide();
				    /*console.log(data);*/
				    if(data) $form.parent().find('.mail-enviado, .mail-enviado .enviado').removeClass('hide');
				    else $form.parent().find('.mail-enviado, .mail-enviado .erro').removeClass('hide');
				  }
				});
			}
		});
	})
	/*$("form.validar").each(function(){
		$(this).validate({
			submitHandler: function(form){
				var formId = $(form).attr('id');
				var formAction = $('form#' + formId).attr('action');
				$('form#' + formId).addClass('loading');
				$.ajax({
				  url: formAction,
				  type: 'POST',
				  data: new FormData(document.getElementById(formId)),
				  processData: false,
				  contentType: false,
				  success:function(data){
				    $('form#' + formId).hide();
				    console.log(data);
				    if(data) $('form#' + formId).parent().find('.mail-enviado, .mail-enviado .enviado').removeClass('hide');
				    else $('form#' + formId).parent().find('.mail-enviado, .mail-enviado .erro').removeClass('hide');
				  }
				});
			}
		});	
	});*/
	largura = $(window).width();
	$('img.dual-img').each(function(){
		setSrc($(this), largura);
	});

	$(window).on('resize', function(){
		newWidth = $(window).width();
		$('img.dual-img').each(function(){
			setSrc($(this), newWidth);
		});
	});
    
	//jquery maskedinput
	if($().mask)
	{
		$("input.mask.telefone").mask("(99) 9999.9999?9");
		$("input.mask.cep").mask("99999-999");
		$("input.mask.data").mask("99/99/9999");
		$("input.mask.cpf").mask("999.999.999-99");
		$("input.mask.cnpj").mask("99.999.999/9999-99");
	}

	//cep auto complete
	$("input.auto.cep").on('keyup',function(){
		var value = $(this).val();
		if(value.match(/\d/g).length == 8)
		{
			getAutoCompleteCEP(value);
		}
	});

	function getAutoCompleteCEP(cep) {
		$.getScript("http://cep.agenciaready.com.br/busca.php?cep=" + cep, function()
		{
			if(resultadoCEP["tipo_logradouro"] != '' && resultadoCEP["resultado"])
			{
				$("input[name='endereco']").val(unescape(resultadoCEP["tipo_logradouro"]) + " " + unescape(resultadoCEP["logradouro"]));
				$("input[name='endereco']").parent().find('label.plholder').addClass('active')
				$("input[name='bairro']").val(unescape(resultadoCEP["bairro"]));
				$("input[name='bairro']").parent().find('label.plholder').addClass('active')
				$("input[name='cidade']").val(unescape(resultadoCEP["cidade"]));
				$("input[name='cidade']").parent().find('label.plholder').addClass('active')
				$("input[name='uf']").val(unescape(resultadoCEP["uf"]));
				$("input[name='uf']").parent().find('label.plholder').addClass('active')
				$("input[name='numero']").focus();
			}	
		});
	}

	//animação logo ready
	$('#copy .ready').on('mouseenter',function(){ AnimateCSS(this,'pulse'); }) //pulse no logo da ready

});
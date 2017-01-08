$(document).ready(function()
{
	checkConteudo();
	checkAltura();
	checkLink();

	$('input[type="checkbox"]#checkTipo').on('change',function(){
		var chkData = $('#checkTipo').val();
		if(chkData == 'Imagens'){
			if(this.checked){var newTipo = 'I';}else{var newTipo = 'V';}
		}else{
			if(this.checked){var newTipo = 'V';}else{var newTipo = 'I';}
		}
		$.get("index.php?on=slider&in=updateTipo",{'newTipo': newTipo}, function(data){ location.reload(); });
	});

	$('input[type="radio"][name="tipo"]').on('change',function(){
		checkConteudo();
	});

	$('input[type="radio"][name="altura"]').on('change',function(){
		checkAltura();
	});

	$('input[type="checkbox"]#do_link').on('change',function(){
		checkLink();
	});
});

function checkConteudo()
{
	if($('input[type="radio"][name="tipo"]:checked').val() == 'T')
	{
		$('.texto-options').fadeIn();
		$('section#fs-video').fadeOut();
		$('section#fs-imagem').fadeIn();
	}
	else if($('input[type="radio"][name="tipo"]:checked').val() == 'V')
	{
		$('section#fs-video').fadeIn();
		$('section#fs-imagem').fadeOut();
		$('.texto-options').fadeOut();		
	}
	else
	{
		$('section#fs-video').fadeOut();
		$('section#fs-imagem').fadeIn();
		$('.texto-options').fadeOut();		
	}
}

function checkAltura()
{
	if($('input[type="radio"][name="altura"]:checked').val() == 'E')
	{
		$('#alturaEspecifica').fadeIn();
	}
	else
	{
		$('#alturaEspecifica').fadeOut();
	}
}

function checkLink()
{
	if($('input[type="checkbox"]#do_link').is(":checked"))
	{
		$('.do_link').fadeIn();
	}
	else
	{
		$('.do_link').fadeOut();
	}
}
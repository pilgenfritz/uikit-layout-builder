$(document).ready(function()
{
	//create slug a partir do título
	$('#nome').on('keyup',function(event){
		var result = slug($('#nome').val(),'_');
		if($('#tabela').hasClass('vazio')) $('#tabela').val(result);
	});

	//preencher tabela
	$('#modulo').on('keyup',function(event){
		var valor = $(this).val();
		if($('#tabela').hasClass('vazio')) $('#tabela').val(valor);
	});
});
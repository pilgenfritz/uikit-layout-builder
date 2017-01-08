$(document).ready(function()
{
	//filtros
	$('#selecao_categoria').on('change',function(event) {
		var str = "";
	    $( "#selecao_categoria option:selected" ).each(function() {
	      str += $( this ).val() + " ";
	    });
	    window.location.href='faq/cat' + str;
	});

	$('.pergunta h2').on('click',function(){
		$('.pergunta p').hide();
		$(this).parent().find('p').show();
	});

});	
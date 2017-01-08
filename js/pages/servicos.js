$(document).ready(function()
{
	//filtros
	$('#selecao_categoria').on('change',function(event) {
		var str = "";
	    $( "#selecao_categoria option:selected" ).each(function() {
	      str += $( this ).val() + " ";
	    });
	    window.location.href='servicos/cat' + str;
	});

});	
$(document).ready(function()
{
	//filtros
	$('#aid').change(function(event) {
		var str = "";
	    $( "#aid option:selected" ).each(function() {
	      str = $(this).val();
	    });
	    window.location.href='index.php?on=logs&in=listar&aid=' + str + '&modulo=' + $('select#modulo').val() + '&acao=' + $('select#acao').val();
	});

	$('#modulo').change(function(event) {
		var str = "";
	    $( "#modulo option:selected" ).each(function() {
	      str += $(this).val();
	    });
	    window.location.href='index.php?on=logs&in=listar&modulo=' + str + '&aid=' + $('select#aid').val() + '&acao=' + $('select#acao').val();
	});

	$('#acao').change(function(event) {
		var str = "";
	    $( "#acao option:selected" ).each(function() {
	      str += $(this).val();
	    });
	    window.location.href='index.php?on=logs&in=listar&acao=' + str + '&aid=' + $('select#aid').val() + '&modulo=' + $('select#modulo').val();
	});
	
	//graficos
	$('.graficos-div-control a').on('click',function(){
		$(this).addClass('hide');
		if($(this).hasClass('mostrar'))
		{
			$(this).parent().find('.ocultar').removeClass('hide');
			$('.row.panel.graficos').removeClass('hide');
			if($(this).hasClass('primeiro')){
				$(this).removeClass('primeiro')
				drawChart();
			}
		}else
		{
			$(this).parent().find('.mostrar').removeClass('hide');
			$('.row.panel.graficos').addClass('hide');
		}
	});
});
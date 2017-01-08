$(document).ready(function()
{
	$(window).load(function() {
		if($(".se-pre-con")) $(".se-pre-con").fadeOut("slow");;
	});

	var modulo = getParameterByName('on');

	var altura = window.innerHeight;

	$('.inner-wrap').css('min-height', altura);

	$('span.remove-mod').on('click',function(){
		var modulo = $(this).attr('data-modulo');
		var liParent = $(this).parent();
		$.get('index.php?on=modulos&in=disableMod&mod=' + modulo,function(){
			liParent.hide();
		});
	});

	$('button#zerar-banco').on('click',function(){
		window.location.href='index.php?on=' + modulo + '&in=truncate&print=Y';
	});

	/*$('img.svg').each(function(){
		var $img = $(this);
		var imgID = $img.attr('id');
		var imgClass = $img.attr('class');
		var imgURL = $img.attr('src');

		$.get(imgURL, function(data) {
		    var $svg = $(data).find('svg'); // Get the SVG tag, ignore the rest
		    if(typeof imgID !== 'undefined') { $svg = $svg.attr('id', imgID); } // Add replaced image's ID to the new SVG
		    if(typeof imgClass !== 'undefined') { $svg = $svg.attr('class', imgClass+' replaced-svg'); } // Add replaced image's classes to the new SVG
		    $svg = $svg.removeAttr('xmlns:a'); // Remove any invalid XML tags as per http://validator.w3.org
		    $img.replaceWith($svg); // Replace image with new SVG
		}, 'xml');
    });*/

    $('.set-star').click(function(){
    	var id = $(this).attr('data-id');

    	if($(this).hasClass('fa-star')){ $(this).removeClass('fa-star').addClass('fa-star-o'); var status = 'N'; }
    	else{ $(this).removeClass('fa-star-o').addClass('fa-star'); var status = 'Y'; }

    	if($(this).attr('data-table')) var table = $(this).attr('data-table');
    	else var table = getParameterByName('on');
    	
    	$.get("index.php?on=set-star&table=" + table + "&status=" + status + "&id=" + id);
    });

    $('.set-active').click(function(){
    	var id = $(this).attr('data-id');

    	if($(this).hasClass('fa-toggle-on')){ $(this).removeClass('fa-toggle-on').addClass('fa-toggle-off'); var status = 'N'; $(this).parent().parent().addClass('inactive'); }
    	else{ $(this).removeClass('fa-toggle-off').addClass('fa-toggle-on'); var status = 'Y'; $(this).parent().parent().removeClass('inactive'); }

    	if($(this).attr('data-table')) var table = $(this).attr('data-table');
    	else var table = getParameterByName('on');
    	console.log("index.php?on=set-active&table=" + table + "&status=" + status + "&id=" + id);
    	$.get("index.php?on=set-active&table=" + table + "&status=" + status + "&id=" + id);
    });

	//mods - filtros
    $('#filterPages').change(function(event) {
        var str = "";
        var campoName = $(this).attr('name');
        $( "#filterPages option:selected" ).each(function() {
          str += $( this ).val() + " ";
        });
        window.location.href='index.php?on=' + modulo + '&in=listar&' + campoName + '=' + str;
    });

    $('.layout-options.header_slider_height').hide();
    $('.layout-options.header_full #header_full').on('click', function(){
    	if($(this).is(':checked')){
    		$('.layout-options.header_slider_height').hide();
    	}else{
    		$('.layout-options.header_slider_height').show();
    		$( "#header_slider_height" ).focus();
    	}
    });
    $('#footer_mapa').on('click', function(){
    	if($(this).is(':checked')){
    		$('.layout-options.footer_mapa_size').show();
    	}else{
    		$('.layout-options.footer_mapa_size').hide();
    	}
    });



    /*setTimeout(function(){
    	$('.alert-box').css('marginTop','-300px').delay(300).fadeOut();
    },4000);*/

	//mods - ocultar descrição ao selecionar mais de uma imagem
	$('input[type="file"]#imagem').on('change',function(){
	    var numFiles = $('input[type="file"]#imagem')[0].files.length;
	    if(numFiles > 1)
	    {
	      $('#descricao.row').hide();
	    }
	});

	//mods - update ordem main() (lista de TRs)
	$( "#tabela_menu tbody" ).sortable({
	    update: function( event, ui )
	    {
	      var neworder = new Array();
	      $('#tabela_menu tbody tr').each(function()
	      {    
	          neworder.push($(this).attr("id"));
	      });
	      var postFunc = $(this).parent().attr('data-postFunc');
	      if(postFunc == '' || postFunc == undefined) var postFunc = 'updatemenu';
	      console.log(neworder);
	      console.log(postFunc);
	      $.post("index.php?on=" + modulo + "&in=" + postFunc + "&print=Y",{'neworder': neworder},function(data){});
	    }
	  });
	$("#tabela_menu tbody tr").disableSelection();

	//mods - update ordem main() (lista de imagens/logos)
	$( "#tabela_menu_img  tbody div.row" ).sortable({
	    update: function( event, ui )
	    {
	      var neworder = new Array();
	      $('#tabela_menu_img tbody div.row .columns.crop').each(function()
	      {    
	          neworder.push($(this).attr("id"));
	      });
	      console.log(neworder);
	      $.post("index.php?on=" + modulo + "&in=updatemenu&print=Y",{'neworder': neworder},function(data){});
	    }
	  });
	$("#tabela_menu_img tbody div.row .columns.crop").disableSelection();

	//mods - update ordem galeria
	$( "#tabela_imagens tbody div.row" ).sortable({
      update: function( event, ui )
      {
        var neworder = new Array();
        $('#tabela_imagens tbody div.row .columns.crop').each(function()
        {    
            neworder.push($(this).attr("id"));
        });
        $.post("index.php?on=" + modulo + "&in=updateordem_img&print=Y",{'neworder': neworder},function(data){});
        console.log(neworder);
      }
    });
    $("#tabela_imagens tbody div.row .columns.crop").disableSelection();

    //mods - instrucoes
	$('.instrucoes .fechar').on('click',function(){
		$('.row.instrucoes').addClass('hide');
	});

	//Highlight
	hljs.configure({tabReplace: ' '}); // 4 spaces
	hljs.initHighlightingOnLoad();

	//datepicker
	$( ".datepicker" ).datepicker();

	//colpicker
	$('.picker').colpick({
		layout:'hex',
		submit:0,
		colorScheme:'dark',
		onChange:function(hsb,hex,rgb,el,bySetColor) {
			$(el).css('border-color','#'+hex);
			// Fill the text box just if the color was set using the picker, and not the colpickSetColor function.
			if(!bySetColor) $(el).val(hex);
		}
	}).keyup(function(){
		$(this).colpickSetColor(this.value);
	});

	//show code
	$('.open-code').on('click',function(){
		if($(this).hasClass('opened'))
		{
			$(this).removeClass('opened');
			$(this).parent().find('.tabs.show-code, .tabs-content.show-code').css('display','none');
		}else
		{
			$(this).addClass('opened');
			$(this).parent().find('.tabs.show-code, .tabs-content.show-code').css('display','block');
		}
	});

	//options
	$('.mod-options label input[type="checkbox"]').on('change',function(){
		if($(this).is(':checked')) var mostrar = 'Y'; else var mostrar = 'N';
		var campo = $(this).attr('name');
		var modulo = getParameterByName('on');
		$.get('index.php?on=modulos&in=push_options&modulo=' + modulo + '&campo=' + campo + '&mostrar=' + mostrar,function(){
			//window.location.reload();
		});
	});

	//editar imagem
	$('.image-box ul#image-options .trocar-imagem').on('click',function()
	{
		var divTop = $(this).parent().parent().parent().parent();
		divTop.addClass('alterar-aberto');
		$('.tooltip').hide();
	});

	//editar imagem - cancel
	$('.image-box .trocar-imagem-cancel').on('click',function()
	{
		var divTop = $(this).parent();
		divTop.find('input[type="file"]').val('');
		divTop.removeClass('alterar-aberto');
	})

	//editar arquivo
	$('.file-box ul#file-options .trocar-arquivo').on('click',function()
	{
		var divTop = $(this).parent().parent().parent().parent();
		divTop.addClass('alterar-aberto');
		$('.tooltip').hide();
	});

	//editar arquivo - cancel
	$('.file-box .trocar-arquivo-cancel').on('click',function()
	{
		var divTop = $(this).parent();
		divTop.find('input[type="file"]').val('');
		divTop.removeClass('alterar-aberto');
	})

	// efeito do link "topo"
	$("#back-to-top").hide();
	$("#back-to-top").click(function() {
		$('html, body').animate({
			scrollTop: $("#locker-top").offset().top
		}, 1000);
    });
	$(window).scroll(function()
	{
		if ($(window).scrollTop() > 100) $("#back-to-top").fadeIn();
		else $("#back-to-top").fadeOut();
	});
});

//datapicker
$.datepicker.regional['pt-BR'] = {
    closeText: 'Fechar',
    prevText: '< anterior',
    nextText: 'pr&oacute;ximo >',
    currentText: 'Hoje',
    monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho',
    'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun',
    'Jul','Ago','Set','Out','Nov','Dez'],
    dayNames: ['Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sabado'],
    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
    dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
    weekHeader: 'Sm',
    dateFormat: 'dd/mm/yy',
    firstDay: 0,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: ''};
$.datepicker.setDefaults($.datepicker.regional['pt-BR']);

//pegar valor da URL
function getParameterByName(name)
{
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

var slug = function(str,spaces) {
    var $slug = '';
    var trimmed = $.trim(str);
    $slug = trimmed.replace(/[^a-z0-9-]/gi, spaces).
    replace(/-+/g, spaces).
    replace(/^-|-$/g, '');
    return $slug.toLowerCase();
}

function hideIfOtherIsChecked(elemIdA,elemIdB)
{
	if($('input[type="checkbox"]#' + elemIdA).is(":checked"))
	{
		$('#' + elemIdB).fadeIn();
	}
	else
	{
		$('#' + elemIdB).fadeOut();
	}
}

function isEmpty(elemId)
{
	var value = $.trim($(elemId).val());

	if(value.length > 0){
		return false;
	}else
	{
		return true;
	}
}
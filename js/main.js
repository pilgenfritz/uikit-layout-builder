$(document).ready(function(){
	var finalHtml = '';


	/*$('#radio-tipo').on('change', function(){
		var tipo = $(this).find("input[type='radio']:checked").val()
		
		if(tipo == 'select'){
			$('.wrap-options').removeClass('uk-hidden')
		}else{
			$('.wrap-options').addClass('uk-hidden')
		}
	})*/

	var htmlDrag = '<li class="uk-nestable-item">'+
			'<div class="uk-icon-arrows item-draggable"></div>'+
        	'</li>';

	$('.uk-nestable').on('start.uk.nestable', function(ev, obj){
		value = $('#columns').val()
		arrValue = value.split(' ')

		var html = '<div class="uk-grid">';
		for(var i = 0; i < arrValue.length; i++){
			html += '<div class="uk-width-'+arrValue[i]+'"></div>';
		}
		html += '</div>';

		finalHtml += html;

		obj.find('li').html(html)

		$('ul.nestable-left').html(htmlDrag)
	})

	$('button').on('click', function(){
		alert(finalHtml)
	})




	$('.uk-nestable').on('stop.uk.nestable', function(ev, obj, type){
		console.log(type.find('div').attr('data-id'))

	})
	

	/*$('button').on('click', function(){
		$('.uk-form-controls').each(function(){
			var valor = $(this).find('input.uk-input').val(),
				columns = '';

			if(valor != '' && !$(this).hasClass('used') ){
				arrValor = valor.split(',');
				if(arrValor.length > 0 && arrValor[0] !=''){
					for (var i =  0; i < arrValor.length; i++) {
						columns += '<div class="uk-width-'+arrValor[i].trim()+'"></div>';
					}

					var html =	'<div class="uk-container uk-container-center">'+
								'<div class="uk-grid">'+
								columns+
								'</div>'+
								'</div>';
				}
				
				$('#wrap-result .uk-placeholder').each(function(){
					if($(this).hasClass('empty-placeholder')){
						$(this).html(html)
						$(this).removeClass('empty-placeholder')
						return false;
					}
				})	
				$(this).addClass('used')
			}

		})
	})*/

/*	$('.wrap-options i').on('click', function(){
		var option = '<input type="text" class="uk-input" name="options[]" placeholder="Digite a opção"/>';
		$('.wrap-options-ch').append(option)
	})*/

/*	$('button').on('click', function(){
		var $result = $('#result'), 
		    tamanho = $('#tamanho').val(),
		    tipo = $("#radio-tipo input[type='radio']:checked").val(),
		    required = $("#radio-required input[type='radio']:checked").val(),
		    label = $('#input_label').val(),
		    id = createSlug(label),
        	arrOptions = $('.wrap-options-ch').find('input'),
        	strOptions = '';

        	if(arrOptions[0].value){
				for (var i = 0; i < arrOptions.length; ++i) {
					if (typeof arrOptions[i].value !== "undefined") {
					    var valueOp = createSlug(arrOptions[i].value);
					    strOptions += '<option value="'+valueOp+'">'+arrOptions[i].value+'</option> \n';
					}
				}
        	}
		    
		    switch(tipo){
		    	case 'input' : strInput = '<input type="text" name="'+id+'" required="'+required+'" title="Por favor, digite o '+label+'" />';
		    	break;
		    	case 'textarea' : strInput = '<textarea name="'+id+'" id="'+id+'"></textarea>';
		    	break;
		    	case 'select' : strInput = '<select name="'+id+'" id="'+id+'">\n'+strOptions+'</select>';
		    	break;
		    	default : strInput = '<input type="text" name="'+id+'" required="'+required+'" title="Por favor, digite o '+label+'" />';  
		    }
		    

		    strFinal += '<div class="uk-width-'+tamanho+' uk-position-relative">\n'
		    + '<label for="'+id+'" class="lbl-plholder">'+label+'</label>\n'
		    + strInput + '\n'
		    + ''
		    + ''
		    + '</div>\n'
		    + '\n';

		    $result.val(strFinal)


	})*/

/*	function createSlug(label){
		var id = label.toLowerCase(),
        	id = id.replace(/[^a-zA-Z0-9]+/g,'_');

        return id;	
	}*/
})	

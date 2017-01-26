$(document).ready(function(){
	var strFinal = '';


	$('#radio-tipo').on('change', function(){
		var tipo = $(this).find("input[type='radio']:checked").val()
		
		if(tipo == 'select'){
			$('.wrap-options').removeClass('uk-hidden')
		}else{
			$('.wrap-options').addClass('uk-hidden')
		}
	})

	$('.wrap-options i').on('click', function(){
		var option = '<input type="text" class="uk-input" name="options[]" placeholder="Digite a opção"/>';
		$('.wrap-options-ch').append(option)
	})

	$('button').on('click', function(){
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


	})

	function createSlug(label){
		var id = label.toLowerCase(),
        	id = id.replace(/[^a-zA-Z0-9]+/g,'_');

        return id;	
	}
})	

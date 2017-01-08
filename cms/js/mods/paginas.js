$(document).ready(function()
{
	$('#nome').on('keyup',function(event){
        if(!$('#page').hasClass('preenchido') || isEmpty('#page'))
        {
            var result = slug($('#nome').val(),'-');
            $('#page').val(result);
        }
    });

    $('#main-menu').nestable({
        group: 1,
        maxDepth: 2 //quantidade de níveis
    })
    .on('change', function(){
        var list   = $('#main-menu').length ? $('#main-menu') : $($('#main-menu').target);

        if (window.JSON) {
        	listOrder = window.JSON.stringify(list.nestable('serialize'));
            //console.log(listOrder);//, null, 2));
            $.post("index.php?on=paginas&in=updateMenuOrder&print=Y",{'neworder': listOrder},function(data){ console.log(data); });
        } else {
            console.log('JSON browser support required.');
        }
    });

    $('#other-pages').nestable({
        group: 2,
        maxDepth: 5 //quantidade de níveis
    })
    .on('change', function(){
        var list   = $('#other-pages').length ? $('#other-pages') : $($('#other-pages').target);

        if (window.JSON) {
            listOrder = window.JSON.stringify(list.nestable('serialize'));
            $.post("index.php?on=paginas&in=updateMenuOrder&print=Y",{'neworder': listOrder},function(data){ console.log(data); });
        } else {
            console.log('JSON browser support required.');
        }
    });

    $('#inactive-pages').nestable({
        group: 3,
        maxDepth: 5 //quantidade de níveis
    })
    .on('change', function(){
        var list   = $('#inactive-pages').length ? $('#inactive-pages') : $($('#inactive-pages').target);

        if (window.JSON) {
        	listOrder = window.JSON.stringify(list.nestable('serialize'));
            $.post("index.php?on=paginas&in=updateMenuOrder&print=Y",{'neworder': listOrder},function(data){ console.log(data); });
        } else {
            console.log('JSON browser support required.');
        }
    });

    $('.dd i.fa-trash').on('click',function(){
        $(this).parent().find('.confirm').show();
        $(this).remove();
    });

});
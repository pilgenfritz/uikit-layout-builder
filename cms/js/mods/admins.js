$(document).ready(function()
{
	checkSuper();
	$('input[type="checkbox"]#super').on('change',function(){
		checkSuper();
	});
});

function checkSuper()
{
	if($('input[type="checkbox"]#super').is(":checked"))
	{
		$('#permissoes.row').fadeOut();
	}
	else
	{
		$('#permissoes.row').fadeIn();
	}
}
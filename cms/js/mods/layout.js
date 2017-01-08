$(document).ready(function()
{
	esconder_slider_full();

	$('#header_full').on('change',esconder_slider_full);
});

function esconder_slider_full()
{
	if($('#header_full').is(':checked'))
	{
		$('.row.layout-options.header_slider_height').hide();
	}else
	{
		$('.row.layout-options.header_slider_height').show();
	}
}
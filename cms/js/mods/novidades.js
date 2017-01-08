$(document).ready(function()
{
  $('#input-tags').selectize({
    plugins: ['remove_button'],
    delimiter: ',',
    persist: false,
    create: function(input) {
      return {
        value: input,
        text: input
      }
    },
    render: {
      item: function(data, escape) {
        return '<div>' + escape(data.text) + '</div>';
      }
    },
    onDelete: function(values) {
      return confirm(values.length > 1 ? 'Tem certeza que deseja remover ' + values.length + ' itens?' : 'Tem certeza que deseja remover o item"' + values[0] + '"?');
    }
  });
	
});
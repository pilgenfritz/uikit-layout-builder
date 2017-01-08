$(document).ready(function(){

  $('#content').infinitescroll({
    navSelector   : "#next:last",
    nextSelector  : "a#next:last",
    itemSelector  : "#content div",
    debug         : true,
    dataType      : 'html',
    maxPage       : 50,
    // prefill    : true,
    // path       : ["http://nuvique/infinite-scroll/test/index", ".html"]
    path: function(index){
      return "pages/blog.html.php?page=" + index;
    }
    // behavior   : 'twitter',
    // appendCallback : false, // USE FOR PREPENDING
    // pathParse  : function( pathStr, nextPage ){ return pathStr.replace('2', nextPage ); }
    }, function(newElements, data, url){
    //USE FOR PREPENDING
    // $(newElements).css('background-color','#ffef00');
    // $(this).prepend(newElements);
    //
    //END OF PREPENDING
    // window.console && console.log('context: ',this);
    // window.console && console.log('returned: ', newElements);  
  });
    
  /*$('a .proxima-pagina').on('click',function(e){
    e.preventDefault();
    
    var linkNextPag = $(this).attr('href');

    $(this).hide();

    $.get( linkNextPag, function( data ) {
      $( ".row.bg-twitter>.columns.eight" ).append( data );
    });

  });*/

/*console.log(jQuery);
console.log(jQuery.infinitescroll);
console.log(jQuery.fn.jquery);*/

});

 /* (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/pt_BR/all.js#xfbml=1&appId=156021571219680";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));*/
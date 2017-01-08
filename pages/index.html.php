<?php

/* headers, includes, classes, meta tags */

  $Dados = New Dados;
  $Layout = New Layout;


$ukgrid_parser.='
  <div class="uk-container uk-container-center">
    <ul id="filter" class="uk-subnav">
      <li data-uk-filter=""><a href="">no filter</a></li>
      <li data-uk-filter="filter-a"><a href="">Filter a</a></li>
      <li data-uk-filter="filter-b"><a href="">Filter b</a></li>
    </ul>
    <div id="grid" class="uk-grid uk-margin-large uk-grid-width-small-1-1 uk-grid-width-medium-1-2 uk-grid-width-large-1-4">';
for ($i=0; $i < 16 ; $i++) { 
  if($i % 2 == 0){
    $dim = '300X300';
    $filter = 'filter-a';
  }else if($i % 3 == 0){
    $dim = '300X450';
    $filter = 'filter-b';
  }else{
    $dim = '300X150';
    $filter = 'filter-a, filter-b';
  }
    $ukgrid_parser.='
        <div data-uk-filter="'.$filter.'">
            <img class="uk-border-rounded" src="http://placehold.it/'.$dim.'"/>
        </div>';      
}
$ukgrid_parser.='      
    </div>
  </div>';



/* Exemplo de Carousel */
$crslExample_parser .= '
  <ul id="crsl-produtos">';
$rr = mysql_query("SELECT * FROM produtos WHERE active='Y' ORDER by ordem");    
while($arr = mysql_fetch_array($rr))
{
  $crslExample_parser .= '
    <li>
      <img src="img/produtos/'.$arr['img1'].'" alt="'.$arr['titulo'].'"/>
    </li>';
}  
$crslExample_parser .= '
  </ul>';

//parsing
$arr = get_defined_vars();
foreach ($arr as $key => $value){
  $isParser = explode('_', $key);
  if($isParser[1] == 'parser'){
      Parser::__alloc($key,$$key);            
  }
}
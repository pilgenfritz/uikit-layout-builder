<?php

/* headers, includes, classes, meta tags */

  $Dados = New Dados;
  $Layout = New Layout;

/* Escrevendo documento  */

list($texto_parser) = mysql_fetch_row(mysql_query("SELECT texto FROM textos WHERE id='1'"));

//galeria de fotos
$galeria_parser = ''; $c=0;
$rr = mysql_query("SELECT * FROM imagens WHERE cid='2' ORDER by ordem");
$total = mysql_num_rows($rr);
while($arr = mysql_fetch_array($rr))
{
  $c++; if($total == $c) $class="end";
  $galeria_parser .= '
  <div class="col-xs-6 col-md-3 ' . $class . '">
    <a href="img/imagens/' . $arr['img2'] . '" class="fancybox thumbnail" rel="galeria_id2">
      <img src="img/imagens/' . $arr['img1'] . '" alt="' . $arr['titulo'] . '" title="' . $arr['titulo'] . '"/>
    </a>
  </div>';
}

//parsing
$arr = get_defined_vars();
foreach ($arr as $key => $value){
  $isParser = explode('_', $key);
  if($isParser[1] == 'parser'){
      Parser::__alloc($key,$$key);            
  }
}
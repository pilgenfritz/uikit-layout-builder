<?php

/* headers, includes, classes, meta tags */

  $dados = New Dados;
  $setup = New Setup;
  $layout = New Layout;

/* escrevendo documento */

if($setup->ModuloOptionsCheck('videos','categoria'))
{
    //selecionando categoria
    if(!empty($_GET['cid'])) $cid = $_GET['cid'];
    else list($cid) = mysql_fetch_row(mysql_query("SELECT id FROM videos_cat ORDER BY titulo"));

    //escrevendo select
    $linha_select = '
      <div class="row">
          <div class="col-lg-12 select">
            <p>Selecione a categoria</p>
            <select name="selecao_categoria" id="selecao_categoria">';

            $rr = mysql_query("SELECT * FROM videos_cat ORDER by titulo");
            $total = mysql_num_rows($rr);
            while($arr = mysql_fetch_array($rr))
            {
              if($cid == $arr['id']) $selected=' selected'; else unset($selected);
                $linha_select .= '
                <option value="/' . $arr['id'] . '/' . $dados->create_slug($arr['titulo']) . '" ' . $selected . '>' . $arr['titulo'] . '</option>';
            }

            $linha_select .= '
            </select>
          </div>
      </div>';

    if($total == 1) unset($linha_select); //se não tiver mais de uma categoria
    Parser::__alloc("linha_select",$linha_select);
}

//listando vídeos
$galeria = ''; $c=0;
if($setup->ModuloOptionsCheck('videos','categoria')) $where = "&& cid='" . $cid . "'";

$rr = mysql_query("SELECT * FROM videos WHERE active='Y' " . $where . " ORDER by ordem");
$total = mysql_num_rows($rr);
if($total > 0)
{
  $galeria = '
  <div class="row">';
    while($arr = mysql_fetch_array($rr))
    {
      $c++; if($total == $c) $class="end";
      $galeria .= '
      <div class="col-lg-6 ' . $class . '">
        <figure>
          <a href="//www.youtube.com/embed/' . $dados->extrair_youtube_code($arr['youtube']) . '?autoplay=1&rel=0" class="fancybox fancybox.iframe">
            <img src="img/videos/' . $arr['img1'] . '" alt="' . $arr['titulo'] . '" />
            <i class="fa fa-play-circle-o" aria-hidden="true"></i>
          </a>
        </figure>
      </div>';
    }
  $galeria .= '
  </div>';
}
else
{
  $galeria = '
  <div class="row">
    <div class="col-lg-5 col-lg-centered center sem-registros">
      <p>Nenhum v&iacute;deo para a categoria selecionada.</p>
    </div>
  </div>';
}
Parser::__alloc("galeria",$galeria);
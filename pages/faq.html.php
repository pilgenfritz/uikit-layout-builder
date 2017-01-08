<?php

/* headers, includes, classes, meta tags */

  $dados = New Dados;
  $layout = New Layout;
  $setup = New Setup;

/* escrevendo documento */

if($setup->ModuloOptionsCheck('faqs','categoria'))
{
    if(is_numeric($_GET['cid'])) $cid = $_GET['cid'];
    elseif(is_numeric($GET['id'])) list($cid) = mysql_fetch_row(mysql_query("SELECT cid FROM faqs WHERE id='" . $_GET['id'] . "' LIMIT 1"));
    else list($cid) = mysql_fetch_row(mysql_query("SELECT id FROM faqs_cat ORDER BY ordem"));

    //select
      $linha_select = '
      <div class="row">
        <div class="col-md-12 select">';
        if(!is_numeric($_GET['id']))
        {
          $linha_select .= '
          <p>Selecione a categoria</p>
          <select name="selecao_categoria" id="selecao_categoria">';
          $rr = mysql_query("SELECT * FROM faqs_cat ORDER by ordem");
          while($arr = mysql_fetch_array($rr))
          {
              if($cid == $arr['id']) $selected=' selected'; else unset($selected);
              $titulo = explode('.',$arr['titulo']);
              if(is_numeric($titulo[0])) $titulo = $titulo[1] . $titulo[2]; else $titulo = $titulo[0] . $titulo[1];
              $linha_select .= '
              <option value="/' . $arr['id'] . '/' . $dados->create_slug($arr['titulo']) . '" ' . $selected . '>' . $titulo . '</option>';
          }
          $linha_select .= '
          </select>';
        }
        $linha_select .= '
        </div>
      </div>';
    Parser::__alloc("linha_select",$linha_select); 
}

$resume = ''; $c=0;
if($setup->ModuloOptionsCheck('faqs','categoria')) $where = "&& cid='" . $cid . "'";

$rr = mysql_query("SELECT * FROM faqs WHERE active='Y' " . $where . " ORDER by ordem");
$total = mysql_num_rows($rr);
if($total > 0)
{
    while($arr = mysql_fetch_array($rr))
    {
      if(!empty($arr['texto']) || !empty($arr['pdf']) || !empty($arr['youtube']) || mysql_num_rows(mysql_query("SELECT id FROM faqs_imagens WHERE cid='" . $arr['id'] . "' LIMIT 1")) > 0) $show_link = true; 
      
      if($show_link) $link = '<a href="faqs/' . $arr['id'] . '/' . $dados->create_slug($arr['titulo']) . '">';

      if(!empty($arr['img1']))
      {
        $img = '
            ' . $link . '
              <img src="img/faqs/' . $arr['img1'] . '" alt="' . $arr['titulo'] . '" />';
            if($show_link) $img .= '</a>';
        unset($class_crop);
      }
      else{ $class_crop = 'empty'; $img = '<p>Sem foto</p>'; }

      $resume .= '
      <div class="row">';
      if($setup->ModuloOptionsCheck('faqs','imagem'))
      {
        $resume .= ' 
        <div class="col-md-4">
          <div class="crop ' . $class_crop . '">
            ' . $img . '
          </div>
        </div>
        <div class="col-md-8 pergunta">';
      }else
      {
        $resume .= '
        <div class="col-md-8 pergunta">';
      }
        $resume .= '
            <h2>' . $arr['titulo'] . '</h2>
            <p>' . $arr['texto'] . '</p>
        </div>
      </div>';
    }
}else
{
  $resume =
  '
  <div class="row">
    <div class="col-lg-12 text-center sem-registros">
      <p>Nenhuma pergunta na categoria selecionada.</p>
    </div>
  </div>
  ';
}
    
Parser::__alloc("resume",$resume);
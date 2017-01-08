<?php

/* headers, includes, classes, meta tags */

  $dados = New Dados;
  $setup = New Setup;
  $layout = New Layout;

/* escrevendo documento */

if($setup->ModuloOptionsCheck('produtos','categoria'))
{
    //selecionando se entrou por algum ID
    if(is_numeric($_GET['cid'])) $cid = $_GET['cid'];
    elseif(is_numeric($GET['id'])) list($cid) = mysql_fetch_row(mysql_query("SELECT cid FROM produtos WHERE id='" . $_GET['id'] . "' && active='Y' LIMIT 1"));
    else list($cid) = mysql_fetch_row(mysql_query("SELECT id FROM produtos_cat ORDER BY ordem"));

    //select
      $linha_select = '
      <div class="row">
        <div class="col-lg-12 select">';
        if(!is_numeric($_GET['id']))
        {
          $linha_select .= '
          <p>Selecione a categoria</p>
          <select name="selecao_categoria" id="selecao_categoria">';
          $rr = mysql_query("SELECT * FROM produtos_cat ORDER by ordem");
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

//se não estiver acessando um produto, mostrar a lista
if(!is_numeric($_GET['id']))
{
    $resume = ''; $c=0;
    if($setup->ModuloOptionsCheck('produtos','categoria')) $where = "&& cid='" . $cid . "'";

    $rr = mysql_query("SELECT * FROM produtos WHERE active='Y' " . $where . " ORDER by ordem");
    $total = mysql_num_rows($rr);
    if($total > 0)
    {
        while($arr = mysql_fetch_array($rr))
        {
          if(!empty($arr['texto']) || !empty($arr['pdf']) || !empty($arr['youtube']) || mysql_num_rows(mysql_query("SELECT id FROM produtos_imagens WHERE cid='" . $arr['id'] . "' LIMIT 1")) > 0) $show_link = true; 
          
          if($show_link) $link = '<a href="produtos/' . $arr['id'] . '/' . $dados->create_slug($arr['titulo']) . '">';

          if(!empty($arr['img1']))
          {
            $img = '
                ' . $link . '
                  <img src="img/produtos/' . $arr['img1'] . '" alt="' . $arr['titulo'] . '" />';
                if($show_link) $img .= '</a>';
            unset($class_crop);
          }
          else{ $class_crop = 'empty'; $img = '<p>Sem foto</p>'; }

          $resume .= '
          <div class="row">
            <div class="col-lg-4">
              <figure class="' . $class_crop . '">
                ' . $img . '
              </figure>
            </div>
            <div class="col-lg-8">
              ' . $link . '
                <h2>' . $arr['titulo'] . '</h2>
                <p>' . $dados->cortar_palavras($arr['texto'],'30') . '</p>';
                if($show_link) 
                {
                  $resume .= '
                    <span class="ler-mais">Saiba mais</span>';
                }
                if($show_link) $resume .= '
              </a>';
                $resume .= '
            </div>
          </div>';
        }
    }else
    {
      $resume =
      '
      <div class="row">
        <div class="col-lg-5 col-lg-centered center sem-registros">
          <p>Nenhum produto na categoria selecionada.</p>
        </div>
      </div>
      ';
    }
}
else //se estiver acessando algum ID
{
  $Produto = mysql_fetch_array(mysql_query("SELECT * FROM produtos WHERE id='" . $_GET['id'] . "'"));

  $resume = '
  <section id="resumo">
      <div class="row">
        <div class="col-lg-9">
          <h2>' . $Produto['titulo'] . '</h2>
        </div>
        <div class="col-lg-3 text-right">
          <a href="produtos/cat/' . $Produto['cid'] . '">voltar</a>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-3">
          <figure>
            <img src="img/produtos/' . $Produto['img1'] . '" alt="' . $Produto['titulo'] . '" />
          </figure>
        </div>
        <div class="col-lg-9">
          ' . $Produto['texto'] . '
        </div>
      </div>
    </section>';

    if($Produto['pdf'] != '')
    {
      $resume .= '
        <div class="row">
          <div class="col-lg-12">
            <ul id="download">
              <li>
                <img src="img/default/pdf-icon.png" alt="Download do PDF" />
              </li>
              <li>
                <a href="files/produtos/' . $Produto['pdf'] . '" target="_blank"><strong>Ver PDF</strong><br/>Faça aqui o download em PDF que preparamos para você.</a>
              </li>
            </ul>
          </div>
        </div>
      ';
    }

    if($Produto['youtube'] != '')
    {
      $youtube = str_replace('width="420" height="315"','width="900" height="675"',$Produto['youtube']);
      $youtube = str_replace('" frameborder','?autoplay=1&controls=0&rel=0" frameborder',$youtube);
      $resume .= '
        <section id="youtube">
          <div class="row">
            <div class="col-lg-12 text-center">' . $youtube . '</div>
          </div>
        </section>
      ';
    }

    $c=0;
    $rr = mysql_query("SELECT * FROM produtos_imagens WHERE cid='" . $_GET['id'] . "' ORDER by ordem");
    $total_galeria = mysql_num_rows($rr);
    if($total_galeria > 0)
    {
      $resume .= '
      <section id="galeria">
        <div class="row">
          <div class="col-lg-12 dados">
            <h3>Galeria</h3>
          </div>
        </div>
        <div class="row">';
        
        while($arr = mysql_fetch_array($rr))
        {
          $c++; if($total_galeria == $c) $class="end";
          $resume .= '
          <div class="col-lg-4 ' . $class . '">
            <figure>
              <a href="img/produtos/' . $arr['img2'] . '" class="various" rel="galeria_id2">
                <img src="img/produtos/' . $arr['img1'] . '" alt="' . $arr['titulo'] . '" />
              </a>
            </figure>
          </div>';
        }

      $resume .= '
        </div>
      </section>';
    }
    
}
Parser::__alloc("resume",$resume);
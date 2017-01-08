<?php

/* headers, includes, classes, meta tags */

  $dados = New Dados;
  $layout = New Layout;

/* escrevendo documento */

if(!is_numeric($_GET['id'])) //se estiver acessando sem ID
{
    //galeria
    $galeria = '
    <div class="row">'; $c=0;
    $rr = mysql_query("SELECT * FROM clientes WHERE active='Y' ORDER by ordem");
    $total = mysql_num_rows($rr);
    while($arr = mysql_fetch_array($rr))
    {
      $c++; if($total == $c) $class=" end";
      $img = '
      <figure>        
        <img src="img/clientes/' . $arr['img1'] . '" alt="' . $arr['titulo'] . '" />
      </figure>';
      $galeria .= '
      <div class="col-lg-3' . $class . '">';
          if(!empty($arr['texto']))
          {
            $galeria .= '
            <a href="clientes/' . $arr['id'] . '/' . $dados->create_slug($arr['titulo']) . '">'
               . $img
            . '</a>';
          }else $galeria .= $img;

          $galeria .= '
      </div>';
    } 
    $galeria .= '
    </div>';
}
else
{
  //dados do registro
  $Cliente = mysql_fetch_array(mysql_query("SELECT * FROM clientes WHERE id='" . $_GET['id'] . "' && active='Y'"));

  //writing
  $galeria = '
  <section id="resumo">
      <div class="row clientes voltar text-right">
        <div class="col-lg-12">
          <a href="clientes">Voltar</a>
        </div>
      </div>
      <div class="row dados-cliente">
        <div class="col-lg-3 logo">
          <div class="box">
            <img src="img/clientes/' . $Cliente['img1'] . '" alt="' . $Cliente['titulo'] . '" />
          </div>
        </div>
        <div class="col-lg-9 dados">
          <h1>' . $Cliente['titulo'] . '</h1>';

          if(strlen($Cliente['cidade']) > 2)
          {
            $galeria .= '
            <h2>' . $Cliente['cidade'] . '</h2>';
          }
          
          $galeria .= $Cliente['texto'];

          if(strlen($Cliente['servicos']) > 10)
          {
            $galeria .= '
            <h3>Serviços realizados</h3>
            ' . $Cliente['servicos'];
          }

          $galeria .= '
        </div>
      </div>
    </section>';
    if(strlen($Cliente['depoimento']) > 10)
    {
      $galeria .= '
      <section id="depoimento">
        <div class="row">
          <div class="col-lg-3">
            <h2>Depoimento</h2>
          </div>
          <div class="col-lg-9">
            ' . $Cliente['depoimento'] . '
          </div>
        </div>
      </section>';
    }

    //galeria de fotos
    $rr = mysql_query("SELECT * FROM clientes_imagens WHERE cid='" . $_GET['id'] . "' ORDER by ordem");
    $total_galeria = mysql_num_rows($rr);

    if($total_galeria > 0)
    {
        $galeria .= '
        <section id="galeria">
          <div class="row">
            <h2>Galeria</h2>';
            $c=0;
            while($arr = mysql_fetch_array($rr))
            {
              $c++; if($total_galeria == $c) $class="end";
              $galeria .= '
              <div class="col-lg-4 ' . $class . '">
                <figure>
                  <a href="img/clientes/' . $arr['img2'] . '" class="various" rel="galeria_id' . $_GET['id'] . '">
                    <img src="img/clientes/' . $arr['img1'] . '" alt="' . $arr['titulo'] . '" />
                  </a>
                </figure>
              </div>';
            }
            $galeria .= '
          </div>
        </section>';
    }
}
//resume
Parser::__alloc("galeria",$galeria);
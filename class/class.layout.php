<?php
class Layout
{
  function __construct()
  {
      global $Setup;
      $config = $Setup->GeneralConfigVar();
  }

	public function gerar_menu($code)
	{
		$rr = mysql_query("SELECT * FROM  config_pages WHERE menu='Y' && ativa='Y' ORDER by menu_order");
		while($arr = mysql_fetch_array($rr))
		{
      if($arr['anchor'] == 'Y') $arr['page'] = '#' . $arr['page'];
      if($arr['page'] == $_GET['p']) $class = 'active';

			$menu .= str_replace(array('[link]','[nome]','[target]','[class]'),array($arr['page'],$arr['nome'],$arr['target'],$class),$code);
		}
		return $menu;
	}

	public function page_header()
	{
		  list($titulo) = mysql_fetch_row(mysql_query("SELECT nome FROM config_pages WHERE page='" . $_GET['p'] . "' LIMIT 1"));

	    $titulo_h1 = '
      <div class="page-header">
        <div class="container">
          <h1>' . $titulo . '</h1>
        </div>
      </div>';

	    return $titulo_h1;
	}

  public function slider($class, $setas, $bolinhas)
  {
      global $Setup;
      $config = $Setup->GeneralConfigVar();

      list($slider_option) = mysql_fetch_row(mysql_query("SELECT valor FROM slider_option"));

      if($slider_option == 'V') $slider = $this->slider_video($class); 
      else $slider = $this->slider_images($class, $setas, $bolinhas);

      return $slider;
  }

  public function slider_images($class, $setas, $bolinhas)
  {
      global $Setup;
      $config = $Setup->GeneralConfigVar();

      $slider = '
      <!-- Image Slider -->
      <div id="slider" class="' . $class;
      $slider .=' ' . $setas;
      $slider .=' ' . $bolinhas;
      $slider .= '">
      <div class="slider">';
      $rr = mysql_query("SELECT * FROM slider WHERE (tipo='I' OR tipo='T') && active='Y' ORDER BY ordem");
      while($arr = mysql_fetch_array($rr))
      {
          $slider .= '
          <div>';
            if($arr['do_link'] == 'Y' && !empty($arr['link'])) $slider .= '<a href="' . $arr['link'] . '" target="' . $arr['target'] . '">';
            $slider .= '
            <div class="lente"></div>
            <img data-mobile="img/slider/' . $arr['img_mobile1'] . '"
                 data-desktop="img/slider/' . $arr['img2'] . '"
                 class="dual-img" src="" alt="' . $arr['titulo'] . '" 
             />';
            if($arr['tipo'] == 'T' && !empty($arr['titulo']))
            {
              if(!empty($arr['cor']))  $color = ' style="color:#' . str_replace('#','',$arr['cor']) . ';"';
              else unset($color);

              $slider .= '
              <div class="slide-caption';
                $slider .= ' ' . $arr['alinhamento'];
                $slider .= ' ' . $arr['caixa_vertical'];
                $slider .= ' ' . $arr['caixa_horizontal'];               
                $slider .= '">
                  <h2' . $color . '>' . $arr['titulo'] . '</h2>';
                  if(!empty($arr['texto'])){
                    $slider .= '<p' . $color . '>'.$arr['texto'].'</p>';
                  }
              $slider .= '</div>';
            }

            if($arr['do_link'] == 'Y' && !empty($arr['link'])) $slider .= '</a>';
            
            $slider .= '
          </div>';
      }
      $slider .= '
      </div>
      <div class="arrow-down show-for-large-up">
        <img src="img/default/arrow-down.png" alt=""/>
      </div>
    </div>';

    return $slider;
  }

  public function slider_video($class)
  {
      list($video_capa,$video_mp4,$video_ogv,$video_webm,$frase) = mysql_fetch_row(mysql_query("SELECT img_capa,video_mp4,video_ogv,video_webm,frase FROM slider WHERE tipo='V'"));
      $slider_video .= '
      <!-- SLIDER -->
      <div id="slider" class="' . $class . '">';
        $slider_video.='
        <div class="lente"></div>
        <div class="frase">
          ' . $frase . '
        </div>
        <video autoplay loop poster="img/slider/' . $video_capa . '" id="bgvid">
          <source src="files/slider/' . $video_mp4 . '" type="video/mp4">
          <source src="files/slider/' . $video_ogv . '" type="video/ogg">
          <source src="files/slider/' . $video_webm . '" type="video/webm">
        </video>
        <div class="arrow-down show-for-large-up">
          <img src="img/default/arrow-down.png" alt=""/>
        </div>
      </div>';
      return $slider_video;
  }
  
  public function social()
  { 
     $social = '
     <ul class="social">';
     $rr = mysql_query("SELECT * FROM  config_general WHERE chave LIKE 'social-%' ORDER by id");
     while($arr = mysql_fetch_array($rr))
     {
        $social .= '
        <li>
          <a href="' . $arr['valor'] . '" target="_blank">
            <i class="uk-icon-medium uk-icon-'.str_replace('social-','',$arr['chave']).'" aria-hidden="true"></i>
          </a>
        </li>';
     }
     $social .= '
     </ul>';
    return $social;
  }

  public function footer_map($coordenadas, $columns)
  {
    
    if($columns != '100') $map.='<div class="row"><div class="large-'.$columns.' columns">';
      $map.='<div id="map-canvas" data-coordenadas="' . $coordenadas . '"></div>';
    if($columns != '100')$map.='</div></div>';
    
    return $map;
  }

  public function footer_copy()
  {
      $copy ='
      <section id="copy">
        <div class="row">
          <div class="col-sm-8 col-lg-6">
            <p>
            &copy; Copyrights 2016.<br>
            Todos os direitos reservados.
            </p>
          </div>
          <div class="col-sm-4 col-lg-6 text-right">
            <a href="http://www.agenciaready.com.br" target="_blank">
              <img src="img/logo-ready.png" alt="Criação de Websites em Porto Alegre" class="ready">
            </a>
          </div>
        </div>
      </section>';
      return $copy;
  }
}

$Setup = New Setup;
$Dados = New Dados;
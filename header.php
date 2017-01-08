<?php
/* 
 * headers, includes, classes
 * * * * * * * * * * * * * * * * * * * * * * */
  $Setup = New Setup;
  $Dados = New Dados;
  $Layout = New Layout;

/* 
 * writing meta tags, getting config data
 * * * * * * * * * * * * * * * * * * * * * * */ 
  
  //getting config data
  $config = $Setup->GeneralConfigVar();

  //<title> / og:
  Parser::__alloc("meta_titles", $Setup->meta_titles());

  //<style>
  Parser::__alloc("stylesheets", $Setup->stylesheets());

  //<google services>
  Parser::__alloc("google_services", $Setup->google_services());

  //.js
  Parser::__alloc("javascripts", $Setup->javascripts());

/* 
 * writting document
 * * * * * * * * * * * * * * * * * * * * * * */

  /* Menu fixo: navbar-fixed-top
     Menu estático: navbar-static-top */
     
  //documentos @ https://getbootstrap.com/components/#navbar
  //more examples @ https://getbootstrap.com/examples/navbar-fixed-top/

  $navbar = '
        <nav class="uk-navbar">
          <ul class="uk-navbar-nav uk-hidden-small">
            ' . $Layout->gerar_menu('<li><a href="[link]" target="[target]" class="[class]">[nome]</a></li>') . '
          </ul>
          <a href="#my-id" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>
        </nav>
        <div id="my-id" class="uk-offcanvas">
          <img src="img/logo.png" alt="' . $config['company'] . '" />  
        </div>            
  ';

  Parser::__alloc("navbar", $navbar);

  $loader = //others @ tobiasahlin.com/spinkit
    '<div id="loader">
      <div class="spinner">
          <div class="bounce1"></div> <div class="bounce2"></div> <div class="bounce3"></div>
      </div>';
      if($_GET['p'] == 'index') $loader .= '<img class="loader-logo" src="img/logo.png" alt="" />';
    $loader .= '</div>';

  Parser::__alloc("loader", $loader);

  //tag h1 or slider
  if($_GET['p'] != 'index') $header = $Layout->page_header();
  else {
    
    $class = 'full';
    $setas = 'arrows-side';
    $bolinhas = 'bullets-left';
    
    $header = '<header>' . $Layout->slider($class, $setas, $bolinhas) . '</header>';
  }

  Parser::__alloc("header", $header);
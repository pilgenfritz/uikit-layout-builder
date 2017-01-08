<?php

/* headers, includes, classes, meta tags */
  $dados = New Dados;
  $layout = New Layout;
  $Setup = New Setup;
  $config = $Setup->GeneralConfigVar();

/* escrevendo documento */

$frase = $dados->getTexto('9',false,false);

$form_parser .= '
<div class="container">
  <div class="row">
    <div class="col-lg-4">
      <h3>' . $config['company-fone'] . '</h3>
      <h5>' . $config['company-contato'] . '</h5>
      ' . $config['company-address'] . '
    </div>
    <div class="col-lg-8">
     <form id="contato1" action="pages/loads/envia-form.php" class="validar">
       <i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i>
       <input type="hidden" name="default-subject" value="Contato enviado pelo website" />
       <input type="text" name="campo-controle" id="campo-controle" value="" />
        <fieldset>
          <div class="row">
            <div class="col-lg-6">
              <input type="text" name="nome" id="nome" value="" placeholder="Nome" required title="Informe o seu nome." />
            </div>
            <div class="col-lg-6">
              <input type="email" name="email" id="email" value="" placeholder="E-mail" required title="Informe o seu e-mail para contato." />
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <input type="text" name="telefone" id="telefone" value="" placeholder="Telefone" class="mask telefone" required title="Informe o seu telefone." />
            </div>
            <div class="col-lg-6">
              <input type="text" name="empresa" id="empresa" value="" placeholder="Empresa" required title="Informe sua empresa." />
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <input type="text" name="assunto" id="assunto" value="" placeholder="Assunto" />
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <textarea name="mensagem" id="mensagem" placeholder="Mensagem" required title="Digite uma mensagem."></textarea>
            </div>
          </div>
          <div class="row enviar">
            <div class="col-lg-4">
              <button class="btn btn-default" type="submit">ENVIAR E-MAIL</button>
            </div>
            <div class="col-lg-8 text-right">
              '.$frase.'
            </div>
          </div>
        </fieldset>
      </form>
       <div class="row mail-enviado hide text-center">
         <div class="col-lg-12">
           <div class="enviado hide">
             <i class="fa fa-envelope-o" aria-hidden="true"></i>
             <h3>O e-mail foi enviado com sucesso.</h3>
           </div>
           <div class="erro hide">
             <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
             <h3>Ocorreu um erro no envio.</h3>
           </div>
         </div>
       </div>
    </div>
  </div>
</div>';
  

$form2_parser.='
<div class="container">
  <div class="row">
    <div class="col-lg-8">
     <form id="contato2" action="pages/loads/envia-form.php" class="validar">
       <i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i>
       <input type="hidden" name="default-subject" value="Contato enviado pelo website" />
       <input type="text" name="campo-controle" id="campo-controle" value="" />
        <fieldset>
          <div class="row">
            <div class="col-lg-6">
              <input type="text" name="nome" id="nome" value="" placeholder="Nome" required title="Informe o seu nome." />
            </div>
            <div class="col-lg-6">
              <input type="email" name="email" id="email" value="" placeholder="E-mail" required title="Informe o seu e-mail para contato." />
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <input type="text" name="telefone" id="telefone" value="" placeholder="Telefone" class="mask telefone" required title="Informe o seu telefone." />
            </div>
            <div class="col-lg-6">
              <input type="text" name="empresa" id="empresa" value="" placeholder="Empresa" required title="Informe sua empresa." />
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <input type="text" name="assunto" id="assunto" value="" placeholder="Assunto" />
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <textarea name="mensagem" id="mensagem" placeholder="Mensagem" required title="Digite uma mensagem."></textarea>
            </div>
          </div>
          <div class="row enviar">
            <div class="col-lg-8">
              '.$frase.'
            </div>
            <div class="col-lg-4 text-right">
              <button class="btn btn-default" type="submit">ENVIAR E-MAIL</button>
            </div>
          </div>
        </fieldset>
      </form>
       <div class="row mail-enviado hide text-center">
         <div class="col-lg-12">
           <div class="enviado hide">
             <i class="fa fa-envelope-o" aria-hidden="true"></i>
             <h3>O e-mail foi enviado com sucesso.</h3>
           </div>
           <div class="erro hide">
             <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
             <h3>Ocorreu um erro no envio.</h3>
           </div>
         </div>
       </div>
    </div>
    <div class="col-lg-4">
      <h3>' . $config['company-fone'] . '</h3>
      <h5>' . $config['company-contato'] . '</h5>
      ' . $config['company-address'] . '
    </div>
  </div>
</div>';

$form3_parser .= '
<div class="container">
  <div class="row">
    <div class="col-lg-12">
     <form id="contato3" action="pages/loads/envia-form.php" class="validar">
       <i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i>
       <input type="hidden" name="default-subject" value="Contato enviado pelo website" />
       <input type="text" name="campo-controle" id="campo-controle" value="" />
        <fieldset>
          <div class="row">
            <div class="col-lg-6">
              <input type="text" name="nome" id="nome" value="" placeholder="Nome" required title="Informe o seu nome." />
              <input type="email" name="email" id="email" value="" placeholder="E-mail" required title="Informe o seu e-mail para contato." />
              <input type="text" name="telefone" id="telefone" value="" placeholder="Telefone" class="mask telefone" required title="Informe o seu telefone." />
              <input type="text" name="empresa" id="empresa" value="" placeholder="Empresa" required title="Informe sua empresa." />
              <input type="text" name="assunto" id="assunto" value="" placeholder="Assunto" />
            </div>
            <div class="col-lg-6">
              <textarea name="mensagem" id="mensagem" placeholder="Mensagem" required title="Digite uma mensagem."></textarea>
            </div>
          </div>
          <div class="row enviar">
            <div class="col-lg-6">
              '.$frase.'
            </div>
            <div class="col-lg-6">
              <button class="btn btn-default" type="submit">ENVIAR E-MAIL</button>
            </div>
          </div>
        </fieldset>
      </form>
       <div class="row mail-enviado hide text-center">
         <div class="col-lg-12">
           <div class="enviado hide">
             <i class="fa fa-envelope-o" aria-hidden="true"></i>
             <h3>O e-mail foi enviado com sucesso.</h3>
           </div>
           <div class="erro hide">
             <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
             <h3>Ocorreu um erro no envio.</h3>
           </div>
         </div>
       </div>
    </div>
  </div>
</div>';


$form4_parser .= '
<div class="container">
  <div class="row">
    <div class="col-lg-4 col-lg-offset-4 text-center ">
      <h3>' . $config['company-fone'] . '</h3>
      <h5>' . $config['company-contato'] . '</h5>
      ' . $config['company-address'] . '
    </div>
    <div class="col-lg-8 col-lg-offset-2">
     <form id="contato4" action="pages/loads/envia-form.php" class="validar">
       <i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i>
       <input type="hidden" name="default-subject" value="Contato enviado pelo website" />
       <input type="text" name="campo-controle" id="campo-controle" value="" />
        <fieldset>
          <div class="row">
            <div class="col-lg-6">
              <input type="text" name="nome" id="nome" value="" placeholder="Nome" required title="Informe o seu nome." />
              <input type="email" name="email" id="email" value="" placeholder="E-mail" required title="Informe o seu e-mail para contato." />
              <input type="text" name="assunto" id="assunto" value="" placeholder="Assunto" />
            </div>
            <div class="col-lg-6">
              <textarea name="mensagem" id="mensagem" placeholder="Mensagem" required title="Digite uma mensagem."></textarea>
            </div>
          </div>
          <div class="row enviar">
            <div class="col-lg-6">
              '.$frase.'
            </div>
            <div class="col-lg-6">
              <button class="btn btn-default" type="submit">ENVIAR E-MAIL</button>
            </div>
          </div>
        </fieldset>
      </form>
       <div class="row mail-enviado hide text-center">
         <div class="col-lg-12">
           <div class="enviado hide">
             <i class="fa fa-envelope-o" aria-hidden="true"></i>
             <h3>O e-mail foi enviado com sucesso.</h3>
           </div>
           <div class="erro hide">
             <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
             <h3>Ocorreu um erro no envio.</h3>
           </div>
         </div>
       </div>
    </div>
  </div>
</div>';

$form5_parser .= '
<div class="container">
  <div class="row">
    <div class="col-lg-10 col-lg-offset-1">
     <form id="contato5" action="pages/loads/envia-form.php" class="validar">
       <i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i>
       <input type="hidden" name="default-subject" value="Contato enviado pelo website" />
       <input type="text" name="campo-controle" id="campo-controle" value="" />
        <fieldset>
          <div class="row">
            <div class="col-lg-6">
              <input type="text" name="nome" id="nome" value="" placeholder="Nome" required title="Informe o seu nome." />
            </div>
            <div class="col-lg-6">
              <input type="email" name="email" id="email" value="" placeholder="E-mail" required title="Informe o seu e-mail para contato." />
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <input type="text" name="assunto" id="assunto" value="" placeholder="Assunto" />
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <textarea name="mensagem" id="mensagem" placeholder="Mensagem" required title="Digite uma mensagem."></textarea>
            </div>
          </div>
          <div class="row enviar">
            <div class="col-lg-6">
              '.$frase.'
            </div>
            <div class="col-lg-4">
              <button class="btn btn-default" type="submit">ENVIAR E-MAIL</button>
            </div>
          </div>
        </fieldset>
      </form>
       <div class="row mail-enviado hide text-center">
         <div class="col-lg-12">
           <div class="enviado hide">
             <i class="fa fa-envelope-o" aria-hidden="true"></i>
             <h3>O e-mail foi enviado com sucesso.</h3>
           </div>
           <div class="erro hide">
             <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
             <h3>Ocorreu um erro no envio.</h3>
           </div>
         </div>
       </div>
    </div>
  </div>
</div>';

$arr = get_defined_vars();
foreach ($arr as $key => $value){
  $isParser = explode('_', $key);
  if($isParser[1] == 'parser'){
      Parser::__alloc($key,$$key);            
  }
}

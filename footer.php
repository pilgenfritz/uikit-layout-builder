<?php
/* headers, includes, classes */

  $Setup = New Setup;
  $Dados = New Dados;
  $Layout = New Layout;
  
  $config = $Setup->GeneralConfigVar();
  /*$config['company-fone']
  $config['company-contato']
  $config['company-address']
  $Layout->social();
  $Layout->footer_copy();*/

/* writting document */

/*  $footer .= '
  <footer>
            <form id="contato-footer" action="pages/loads/envia-form.php" enctype="multipart/form-data" class="validar">
             <i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i>
             <input type="hidden" name="MAX_FILE_SIZE" value="10000000000" />
             <input type="hidden" name="from" value="footer" />
             <input type="hidden" name="default-subject" value="Contato enviado pelo website" />
             <input type="text" name="campo-controle" id="campo-controle" value="" />

             <div class="uk-grid">
               <div class="uk-width-1-2">
                <label for="nome" class="lbl-plholder">Nome</label>
                <input type="text" name="nome" id="nome" required title="Informe seu nome." />
               </div>
               <div class="uk-width-1-2">
                <label for="email" class="lbl-plholder">E-mail</label>
                <input type="email" name="email" id="email" required title="Informe um e-mail válido." />
               </div>
             </div>

             <div class="uk-grid">
               <div class="uk-width-1-2">
                <label for="telefone" class="lbl-plholder">Telefone</label>
                 <input type="text" name="telefone" id="telefone" class="mask telefone" required title="Informe um telefone para contato." />
               </div>
               <div class="uk-width-1-2">
                <label for="assunto" class="lbl-plholder">Assunto</label>
                 <input type="text" name="assunto" id="assunto" required title="Informe o assunto para contato." />
               </div>
             </div>

             <div class="uk-grid">
               <div class="uk-width-1-1">
                <label for="mensagem" class="lbl-plholder">Mensagem</label>
                 <textarea name="mensagem" id="mensagem" required title="Insira uma mensagem."></textarea>
               </div>
             </div>

             <div class="uk-grid enviar">
               <div class="uk-width-1-4">
                 <button class="btn btn-default" type="submit">Enviar</button>
               </div>
               <div class="uk-width-3-4">
               </div>
             </div>
            </form>
            <div class="uk-grid mail-enviado text-center hide">
             <div class="uk-width-1-1">
               <div class="enviado hide">
                 <i class="fa fa-envelope-o" aria-hidden="true"></i>
                 <h3>O e-mail foi enviado com sucesso.</h3>
               </div>
               <div class="erro hide">
                 <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                 <h3>Ocorreu um erro no envio.</h3>
               </div>
             </div>
            </div>';*/

          /*$footer .= '
     </section>';*/

//     $footer.= $Layout->footer_map($config['google-maps-coordenadas'], '100');

     /*$footer .= 
  
  $footer .= '
  </footer>';*/


  $footer .= '
    <footer>
      <div class="overlay"></div>
      <div class="uk-container uk-container-center content uk-position-relative">
        <div class="uk-grid uk-margin-large">
          <div class="uk-width-1-1 uk-text-center">
            <h1>Contato</h1>
          </div>
        </div>
        <div class="uk-grid">
          <div class="uk-width-large-1-3 uk-margin-top uk-text-center">
            <h3>Porto Alegre / RS</h3>
            <p>
               Cristovão Pereira, 99 / 204 </br>
               Passo D\'Areia </br>
               CEP: 91030-420
            </p>
          </div>
          <div class="uk-width-large-1-3 uk-margin-top uk-text-center">
            <h3>+55 (51) 3013.5536</h3>
            <p>cerva@cervaadvocacia.com.br</p>
          </div>
          <div class="uk-width-large-1-3 uk-margin-top uk-text-center">
            <div class="btn-footer btn-email">
              Enviar E-mail
            </div>
            <div class="btn-footer btn-bottom">
              Trabalhe Conosco
            </div>
          </div>
        </div>
      </div>
      '.$Layout->footer_map($config['google-maps-coordenadas'], '100').'
    </footer>
  ';

Parser::__alloc("footer", $footer);
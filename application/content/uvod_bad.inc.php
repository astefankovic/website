<?php 
  // cesta k adresari se sablonama - od index.php
	$loader = new Twig_Loader_Filesystem('public/sablony');
	$twig = new Twig_Environment($loader); // takhle je to bez cache  
     
  //nacteni sablony stranky
	$template = $twig->loadTemplate('sablona_basic.html');   	
  $template_params = array();
  $template_params["nadpis1"] = "Špatný pokus o přihlášení!";
	$template_params["obsah"] = '
                                    <div class="panel panel-danger">
                                                <div class="panel-heading">
                                                   <h2 class="panel-title"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span> Zkuste se přihlásit znovu, pokud ještě účet nemáte, můžete se registrovat.</h2>
                                                </div>
                                                <div class="panel-body">
                                                  <form role="form" method="POST" action="/web/application/config/sign.php">
                                                   <div class="col-md-6"> 
                                                     <div class="alert alert-warning" role="alert">
                                                     <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>
                                                    Nemáte ještě účet? Registrujte se 
                                                    <a class="btn btn-info" type="button" href="?page=registration">Chci se zaregistrovat <span class="glyphicon glyphicon-user" aria-hidden="true"></span></a>
                                                    </div> 
                                                    <br>
                                                    Přezdívka: <input type="text" placeholder="Přezdívka" class="form-control" id="prezdivka" name="nick">
                                                    <br>	                  
                                                    Heslo: <input type="password" placeholder="Heslo" class="form-control" name ="pass">   
                                                    <br>
                                                    <input type="submit" class="btn btn-success" value="Odeslat">
                                                    </div>
                                                 </form> 
                                              </div>
                                    </div>
  
                          ';                  
  
	echo $template->render($template_params); 
 
?>

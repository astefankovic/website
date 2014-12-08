<?php       
	// cesta k adresari se sablonama - od index.php
	$loader = new Twig_Loader_Filesystem('public/sablony');
	$twig = new Twig_Environment($loader); // takhle je to bez cache 
  
	// sablona
	$template = $twig->loadTemplate('sablona_basic.html');         
  
  $template_params = array();
  $template_params["nazev_stranky"] = "eKuchařka - kontakt";
  $template_params["nadpis1"] = "Kontakt";
	$template_params["obsah"] =  '<address>
                                  <strong>Albert Štefankovič</strong><br>          
                                  <abbr title="Mobilní telefon">mob.:</abbr> +420 123 456 789<br> 
                                  <abbr title="e-mailová adresa">e-mail:</abbr> <a href="mailto:#">astefankovic@gmail.com</a>        
                                </address>';    
	
  echo $template->render($template_params);  
?>

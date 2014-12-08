<?php   
  session_start();    
  
  // nacteni konfigurace a funkci
  require 'application/config/config.inc.php';  // nacteni konfigurace
	require 'application/config/functions.inc.php';  // pomocne funkce
	
	// nacist objekty - soubory .class.php
	require 'application/core/app.class.php';  // drzi hlavni funkcionalitu cele aplikace, obsahuje routing = navigovani po webu
	require 'application/core/db.class.php'; // zajisti pristup k db a spolecne metody pro dalsi pouziti
	require 'application/core/uzivatele.class.php';  // zajisti pristup ke konkretnim db tabulkam - objekt vetsinou zajisti pristup k cele sade souvisejicich tabulek
  require 'application/core/recepty.class.php'; 
  require 'application/core/ingredience.class.php';  
  require 'application/core/ingredience_receptu.class.php'; 
  require 'application/core/foto.class.php';
  require 'application/core/hodnoceni.class.php';
  
  // nacteni twigu
	require_once 'public/twig-master/lib/Twig/Autoloader.php';
	Twig_Autoloader::register();     
  
      $wrongPage = '<div class="alert alert-danger alert-dismissible fade in text-center" role="alert">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                        <h4>Ajajaj! Dostali jste se na neexistující stránku.</h4>
                          <p>Zkuste se vrátit pomocí horního menu, nebo tlačítka níže.</p>
                          <p>
                            <a href="?page=uvod" class="btn btn-danger btn-lg" role="button">Jít domů &raquo;</a>
                          </p>
                      </div>
                    '; 	 
  
      $page = @$_REQUEST["page"];   //hodnota page = -> nazev stranky
    	if ($page == "") $page = "uvod";
    	
    	// povolene stranky
    	$povolene_stranky = array("uvod", "contact", "recipes", "recipes_alphabet", "registration", "user_settings", "users_recipes", "users_alphabet", "change_recipes", "uvod_bad", "new_recipes", "nejlepsi_hodnoceni");
    	
      //pokud se uzivatel snazi dostat na neexistujici page
    	if (!in_array($page, $povolene_stranky))
    	{
    		echo $wrongPage;
    	}
    	
    	// nacist obsah
    	$nazev_souboru = "application/content/$page.inc.php";     	
    
    	// zpracovat soubor a nacist do promenne
    	$obsah = phpWrapperFromFile($nazev_souboru);
    	
    	  // vypis a twig
        
        //vypsat spravne menu
        if(isset($_REQUEST["id"]))
          {
            if(is_numeric($_REQUEST["id"])&&$page == "recipes")
              vypismenu("recipes");
            elseif(is_numeric($_REQUEST["id"])&&$page == "user_settings")   
              vypismenu("users_alphabet");
            elseif(is_numeric($_REQUEST["id"])&&$page == "change_recipes")   
              vypismenu("change_recipes");
            else
              $obsah = "Nothing.";
          }
        else          
    		  vypismenu($page);
           
    		echo $obsah; 
           
?>
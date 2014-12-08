<?php 
  // cesta k adresari se sablonama - od index.php
	$loader = new Twig_Loader_Filesystem('public/sablony');
	$twig = new Twig_Environment($loader); // takhle je to bez cache  
   
          $jmeno = "";
          $prijmeni = "";
          $nick = "";
          $pass = "";
          $pass2 = "";
          
  $template = $twig->loadTemplate('sablona_basic.html');
  $template_params = array();   
         
  if (empty($_POST))  //uživatel je na stránce poprvé
  {
        //nacteniformulare
        $template_params["nadpis1"] = "Registrace nového uživatele";
      	$template_params["obsah"] = '
                                      Registrace je důležitá pro vkládání Vašich receptů. Zadané údaje si zapamatujte, budete je potřebovat pro přihlášení.
                                      <br>'.vratRegForm($jmeno, $prijmeni, $nick, $pass, $pass2, 'disabled = ""').'        
                                    ';
  }
  
  else  //byl vyplneny formular
    {  
        // start the application
        	$app = new app(); 	
        	// pripojit k db
        	$app->Connect(); 	
        	// pripojeni k db
          $db_connection = $app->GetConnection();
          
          // vytvorit objekt, ktery mi poskytne pristup k DB a vlozit mu connector k DB          
        	$uzivatele = new uzivatele($db_connection);

          //promenne slouzici ke spravnemu vyhodnoceni formu
          @$jmeno = test_input($_POST["regName"]);
          @$prijmeni = test_input($_POST["reg2Name"]);
          @$nick = test_input($_POST["regNick"]);
          @$pass = test_input($_POST["regPass"]);
          @$pass2 = test_input($_POST["regPass2"]);
          $allOK = 0;
          //chybove vypisy
          $stringWarnigValuesUser = "";
          $stringWarnigValuesPass = "";
          $stringWarnigValuesDB = "";         
                   
          $overeniNicku = $uzivatele->GetUzivatelOnlyByNick($nick);
           
          if ($jmeno!=NULL && $prijmeni!=NULL && $nick!=NULL && $pass!=NULL)
          {
            if(strlen($pass)<6)
              $stringWarnigValuesPass = "Heslo musí být minimálně 6 znaků dlouhé, jedná se o Vaši bezpečnost. ";
            elseif($pass != $pass2)      
              $stringWarnigValuesPass .= "Heslo a heslo pro kontrolu se musí shodovat.";
            elseif($overeniNicku != NULL)
              $stringWarnigValuesDB = "Je nám líto, ale Vámi zvolené uživatelské heslo již někdo používá, zkuste zadat nové.";
            else
                try
                {
                   $uzivatele->InsertUzivatel($jmeno, $prijmeni, $nick, $pass);
                   $allOK = 1;
                }
                catch(Exception $e)
                {
                  $stringWarnigValuesDB = "Omlouváme se, chyba je na naší straně. Zkuste registraci zopakovat za chvíli.";
                }
          }
          else
          {
            $stringWarnigValuesUser = "Vyplňte prosím své";
            if ($jmeno==NULL)
            {
              $stringWarnigValuesUser .= ' "jméno"';
            }
            if ($prijmeni==NULL)
            {
             $stringWarnigValuesUser .= ' "příjmení"';
            }
            if ($nick==NULL)
            {
              $stringWarnigValuesUser .= ' "uživatelské jméno"';
            }
            if ($pass==NULL)
            {
             $stringWarnigValuesUser .= ' "heslo."';
            }
         
          }
     
        //pokud ok
        if($allOK == 1)
        {
          $template_params["nadpis1"] = "Registrace nového uživatele";
          $template_params["obsah"] = '
                                              <div class="panel panel-success">
                                                <div class="panel-heading">
                                                  <h2 class="panel-title">Registrace proběhla úspěšně</h2>
                                                </div>
                                                <div class="panel-body">
                                                  Výborně, vše je připraveno. Nyní můžete eKuchařku využívat naplno, stačí se přihlásit pomocí uživatelského menu v pravém horním rohu. <a href="?page=uvod">Přejít na úvodní stránku.</a>
                                                </div>
                                              </div>
                                          ';
         }
         else
         {
          $template_params["nadpis1"] = "Registrace nového uživatele";
          $template_params["obsah"] = '
                                              <div class="panel panel-danger">
                                                <div class="panel-heading">
                                                  <h2 class="panel-title">Registrace neproběhla úspěšně</h2>
                                                </div>
                                                <div class="panel-body">
                                                  '.$stringWarnigValuesUser.'
                                                  <br>
                                                  '.$stringWarnigValuesPass.'
                                                  '.$stringWarnigValuesDB.'
                                                  <br>
                                                  '.vratRegForm($jmeno, $prijmeni, $nick, $pass, $pass2, 'disabled = ""').'
                                                </div>
                                              </div>
                                          ';
         }
        
        
    }            
  
	
  
  
  echo $template->render($template_params); 
 
?>

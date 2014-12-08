<?php 
  // cesta k adresari se sablonama - od index.php
	$loader = new Twig_Loader_Filesystem('public/sablony');
	$twig = new Twig_Environment($loader); // takhle je to bez cache  
  
          // start the application
        	$app = new app(); 	
        	// pripojit k db
        	$app->Connect(); 	
        	// pripojeni k db
          $db_connection = $app->GetConnection();
          
          // vytvorit objekt, ktery mi poskytne pristup k DB a vlozit mu connector k DB          
        	$uzivatele = new uzivatele($db_connection);
          
          //promenne nesouci informace o ukladanem
          $jmeno = "";
          $prijmeni = "";
          $nick = "";
          $pass = "";
          $pass2 = "";
          $prihlasenyBool = 0;
          $admin = 0;
           
           //slouzi k rozpoznani jestli chci menit sam sebe, nebo jestli chce admin menit nekoho
          if(isset($_SESSION["id"]))   //uzivatel je prihlasen
          { 
            $userNick = $_SESSION["id"]; //nick uzivatele -> z teto stranky chodi neAdmini
              
              $userID = @$_REQUEST["id"];   //hodnota id = -> id uzivatele ktery se ma prepsat -> tento odkaz muze generovat jen admin z tabulky uzivatelu
            
            $uzivatelAll = $uzivatele->GetUzivatelOnlyByNick($userNick);
            $admin = $uzivatelAll["admin"]; //potrebuju zjistit opravneni prihlaseneho
    	      
            if ($userID != "" && $admin == 1)  //overeni jestli je admin -> zachovani pristupu do editace uzivatelu jen pro admina
            {  
               //$userNick = $_SESSION["id"];          
              $uzivatelAll = $uzivatele->GetUzivatelByID($userID) ;
              
              $jmeno = $uzivatelAll["jmeno"];
              $prijmeni = $uzivatelAll["prijmeni"];
              $nick = $uzivatelAll["prezdivka"];
              $pass =  $uzivatelAll["heslo"];
              $pass2 =  $uzivatelAll["heslo"];
              $adminEditovany =  $uzivatelAll["admin"];
              $prihlasenyBool = 1;
            }
            else     //nepristupuje z tabulky uzivatelu nebo neni admin
            {              
              $jmeno = $uzivatelAll["jmeno"];
              $prijmeni = $uzivatelAll["prijmeni"];
              $nick = $uzivatelAll["prezdivka"];
              $pass =  $uzivatelAll["heslo"];
              $pass2 =  $uzivatelAll["heslo"];
              $prihlasenyBool = 1;
            }  
          }
          
     
  $template = $twig->loadTemplate('sablona_basic.html');
  $template_params = array();   
         
  if (empty($_POST))  //uživatel je na stránce poprvé
  {
        if ($prihlasenyBool == 1 && $admin == 1 && $userID != "")  //prihlasen admin
        {  
          $checked = "";
          if($adminEditovany == 1) //zaskrtnuti checkboxu pokud je editovany uzivate admin
          {
            $checked = "checked";
          }
              
          //nacteniformulare
          $template_params["nadpis1"] = "Údaje uživatele ".$nick;
        	$template_params["obsah"] = '
                                        Milý administrátore, zde můžete upravit registrační údaje uživatelů.
                                        <br>'.vratRegForm($jmeno, $prijmeni, NULL, $pass, $pass2, @$checked).'        
                                      ';   //vratRegForm vrati cely formular s vyplnenymi udaji usera ktereho chceme editovat
        }
        elseif($prihlasenyBool == 1)   //prihlasen neAdmin
        { 
          $userNick = $_SESSION["id"]; 
         //nacteniformulare
          $template_params["nadpis1"] = "Moje údaje";
        	$template_params["obsah"] = '
                                        Milý uživateli '.$userNick.', zde si můžete upravit své registrační údaje.
                                        <br>'.vratRegForm($jmeno, $prijmeni, NULL, $pass, $pass2, 'disabled = ""').'        
                                      ';   //vrati vyplneni formular 
        }
         
        else   //neprihlaseny uzivatel
        {
           //nacteniformulare
          $template_params["nadpis1"] = "Milý nepřihlášený uživateli";
        	$template_params["obsah"] = '
                                        Aby jste mohl aktivně používat eKuchařku, budete se muset přihlásit.
                                               
                                      ';
        }
  }
  
  else  //byl vyplneny formular
  {             
          //promenne slouzici ke spravnemu vyhodnoceni formu
          $jmeno = test_input($_POST["regName"]);
          $prijmeni = test_input($_POST["reg2Name"]);
          $pass = test_input($_POST["regPass"]);
          $pass2 = test_input($_POST["regPass2"]);
          $admin = 0; //checkbox byl zaskrtnut - chci aby byl usr adminem
          
          
          if (isset($_POST["checkAdmin"]))
          {
            $admin = 1;
          }
          
          $allOK = 0;
          //chybove vypisy
          $stringWarnigValuesUser = "";
          $stringWarnigValuesPass = "";
          $stringWarnigValuesDB = "";         
                   
          //$overeniNicku = $uzivatele->GetUzivatelOnlyByNick($nick);
          //osetreni formulare 
          if ($jmeno!=NULL && $prijmeni!=NULL && $pass!=NULL)
          {
            if(strlen($pass)<6)
              $stringWarnigValuesPass = "Heslo musí být minimálně 6 znaků dlouhé, jedná se o Vaši bezpečnost. ";
            elseif($pass != $pass2)      
              $stringWarnigValuesPass .= "Heslo a heslo pro kontrolu se musí shodovat.";
            else
                try
                {
                   //update podle id z prezdivky - zajisteno i ?id v url                 
                  $user_ID = $uzivatele->GetUzivatelOnlyByNick($nick)["id_uzivatel"];
                  $uzivatele->UpdateUzivatel($user_ID, $jmeno, $prijmeni, $pass);                 
                  
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
            if ($pass==NULL)
            {
             $stringWarnigValuesUser .= ' "heslo."';
            }
         
          }
     
        //pokud byl formular ok
        if($allOK == 1)
        {
          $template_params["nadpis1"] = "Moje údaje";
          $template_params["obsah"] = '
                                              <div class="panel panel-success">
                                                <div class="panel-heading">
                                                  <h2 class="panel-title">Změna Vašich údajů proběhla úspěšně</h2>
                                                </div>
                                                <div class="panel-body">
                                                  Blahopřejeme, právě jste si změnili svůj profil. <a href="?page=uvod">Přejít na úvodní stránku.</a>
                                                </div>
                                              </div>
                                          ';
         }
         elseif($userID != "")  //pokud formular OK nebyl a vyplnil jej admin
         {
          if (isset($_POST["checkAdmin"]))
          {
            $checked = "checked";
          }
          $template_params["nadpis1"] = "Údaje uživatele ".$nick;
          $template_params["obsah"] = '
                                              <div class="panel panel-danger">
                                                <div class="panel-heading">
                                                  <h2 class="panel-title">Změna údajů neproběhla úspěšně</h2>
                                                </div>
                                                <div class="panel-body">
                                                  '.$stringWarnigValuesUser.'
                                                  <br>
                                                  '.$stringWarnigValuesPass.'
                                                  '.$stringWarnigValuesDB.'
                                                  <br>
                                                  '.vratRegForm($jmeno, $prijmeni, NULL, $pass, $pass2, @$checked).'
                                                </div>
                                              </div>
                                          ';
         }
        else  //pokud form OK nebyl a vyplnil jej neAdmin
         {
          $template_params["nadpis1"] = "Údaje uživatele ".$nick;
          $template_params["obsah"] = '
                                              <div class="panel panel-danger">
                                                <div class="panel-heading">
                                                  <h2 class="panel-title">Změna údajů neproběhla úspěšně</h2>
                                                </div>
                                                <div class="panel-body">
                                                  '.$stringWarnigValuesUser.'
                                                  <br>
                                                  '.$stringWarnigValuesPass.'
                                                  '.$stringWarnigValuesDB.'
                                                  <br>
                                                  '.vratRegForm($jmeno, $prijmeni, NULL, $pass, $pass2, 'disabled = ""').'
                                                </div>
                                              </div>
                                          ';
         }
        
    
    }            
  

  echo $template->render($template_params); 
 
?>

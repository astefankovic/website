<?php  
	// cesta k adresari se sablonama - od index.php
	$loader = new Twig_Loader_Filesystem('public/sablony');
	$twig = new Twig_Environment($loader); // takhle je to bez cache     

	// nacist danou sablonu z adresare
	$template = $twig->loadTemplate('sablona_basic.html');
  
  //vypise se neprihlasenemu
  $template_params["nadpis1"] = "Milý nepřihlášený uživateli";
  $template_params["obsah"] =  'Aby jste mohl aktivně používat eKuchařku, budete se muset přihlásit.'; 
  
	if(isset($_SESSION["id"]))   //uzivatel je prihlasen
  { 
    	$template_params["nadpis1"] = "Milý přihlášený uživateli";
      $template_params["obsah"] =  'Tohle území je mimo tvé pravomoce.';   
      
           // start the application
        	$app = new app(); 	
        	// pripojit k db
        	$app->Connect(); 	
        	// pripojeni k db
          $db_connection = $app->GetConnection();
          // vytvorit objekt, ktery mi poskytne pristup k DB a vlozit mu connector k DB
        	$uzivatele = new uzivatele($db_connection);
          
          $uzivatele_data = $uzivatele->LoadAlluzivatele();
          
          $userNick = $_SESSION["id"]; //nick uzivatele -> potřebuju zjistit opravneni
          
          $uzivatelAll = $uzivatele->GetUzivatelOnlyByNick($userNick);
          $admin = $uzivatelAll["admin"]; //potrebuju zjistit opravneni prihlaseneho
          
           if ($admin == 1)  //overeni jestli je admin -> zachovani pristupu do editace uzivatelu jen pro admina
            { 	      
             
                 // render vrati data pro vypis nebo display je vypise
              	// v poli jsou data pro vlozeni do sablony  
                $template_params = array();
                $template_params["nadpis1"] = "Uživatelé seřazení podle abecedy";
              	$template_params["obsah"] =  'Po kliknutí na příslušný řádek můžete upravit registrační údaje jednotlivých uživatelů.';   
                                                   
                  $template_params["tabulka_obsah"] =  '<table class="table table-hover">
                                                            <tr>
                                                              <th>Přezdívka</th>
                                                              <th>Jméno</th>
                                                              <th>Příjmení</th>
                                                              <th>Admin</th>                                                 
                                                            </tr>'; 
                                                                                                                  
                 
                  $pocet = count($uzivatele_data); 
                  //vypise jiz serazene recepty do tabulky
                  For($i = 0; $i < $pocet; $i++)
                  {   
                      $id_uzivatele =  $uzivatele_data[$i]["id_uzivatel"];
                                                   
                      @$template_params["tabulka_paticka"] .= '                                                
                                                                <tr class="clickableRow" href="?page=user_settings&id='.$id_uzivatele.'">
                                                                  <td><a href="?page=user_settings&id='.$id_uzivatele.'">'.$uzivatele_data[$i]["prezdivka"].'</a></td>
                                                                  <td>'.$uzivatele_data[$i]["jmeno"].'</td>  
                                                                  <td>'.$uzivatele_data[$i]["prijmeni"].'</td>  
                                                                  <td>'.$uzivatele_data[$i]["admin"].'</td>                                                  
                                                              </tr>
                                                              
                                                              ';
                      if ($i == $pocet-1)
                      {   
                        $template_params["tabulka_paticka"] .= '</table>';
                      }
                   }  
       
       }
  }
  
       
    	echo $template->render($template_params);
?>

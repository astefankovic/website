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
          $recepty = new recepty($db_connection);
          $ingredinece_receptu = new ingredience_receptu($db_connection);
          $ingredineceC = new ingredience($db_connection);
          $foto = new foto($db_connection);   
          
          //promenne nesouci informace o ukladanem receptu
          $nazev = "";   
          $nazevG = "";
          $postup = "";
          $narocnost = "";
          $doba_pripravy = "";
          $uzivatel_id = "";
          $datum_pridani = ""; //date('Y-m-d H:i:s');
          $vytvoritNovy = 1;
          $userNick = "";
          $admin = 0;

           //vyhodnoceni jestli je user prihlasen a jestli chce vytvořit nový, nebo upravit stavajici
          if(isset($_SESSION["id"]))   //uzivatel je prihlasen
          { 
            $userNick = $_SESSION["id"]; //nick uzivatele -> z teto stranky chodi neAdmini
            $uzivatelAll = $uzivatele->GetUzivatelOnlyByNick($userNick);
            $admin = $uzivatelAll["admin"]; //uchovani jetsli je user admin 
      
            $receptID = @$_REQUEST["id"];   //hodnota id = -> id receptu ktery chci menit
            
          }

  $template = $twig->loadTemplate('sablona_basic.html');
  $template_params = array();   
         
  if (empty($_POST))  //uživatel je na stránce poprvé
  {
        if ($userNick != "")  //prihlaseny uzivatel chce vytvorit recept
        {
              //nacteni formulare
              $template_params["nadpis1"] = "Vytvoření nového receptu";
            	$template_params["obsah"] = vratChangeForm("","", "", "", "", "", "", 0); 
        }  
         
        else   //neprihlaseny uzivatel
        {
              //nacteniformulare
              $template_params["nadpis1"] = "Milý nepřihlášený uživateli";
            	$template_params["obsah"] = 'Aby jste mohl aktivně používat eKuchařku, budete se muset přihlásit.';
        }
  }
  
  else  //byl vyplneny formular
  {             
          //promenne slouzici ke spravnemu vyhodnoceni formu
          $nazev = test_input($_POST["Name"]);
          $ingredience = $_POST['ingredience'];
          $mnozstvi = $_POST['mnozstvi'];
          $postup = test_input_textarea($_POST["postup"]);
          $narocnost = test_input($_POST["narocnost"]);
          $doba_pripravy = test_input($_POST["doba_pripravy"]);
          $fotky = ($_FILES['photo']["name"]);
          $pocetFotek = 0;
          
          if($fotky[0] != "")
            $pocetFotek = count($fotky);
        
          $allOK = 0;
          $stringWarnigValues = "Znovu vyberte fotografie. ";
          $stringWarnigValuesDB = ""; 
          $stringWarnigPhoto = "";
          
           if ($userNick != "")  //prihlaseny uzivatel chce odeslat novy recept
           {                    
                if ($nazev!=NULL && $ingredience!=NULL && $mnozstvi!=NULL && $postup!=NULL && $narocnost!=NULL && $doba_pripravy!=NULL && is_numeric ($doba_pripravy)) //vsechna pole jsou vyplnena
                {
                     $exist = $recepty->GetRecipesByName($nazev);
                                      
                
                     if($exist == NULL)//dany nazev receptu neexistuje
                         {                                                       
                              $datum_pridani = date('Y-m-d H:i:s');
                              $userID = $uzivatele->GetUzivatelOnlyByNick($userNick)["id_uzivatel"]; //id usera ktery pridava
                              $idPhoto = "";
                              $img = "";                                
                              
                              //vlozeni receptu
                              $recepty->InsertRecipes($nazev, $postup, $narocnost, $doba_pripravy, $userID, $datum_pridani);
                              
                              //vlozei novych ingredienci
                              $nazvyVsechIngredienci = $ingredineceC->LoadAllNamesIngredience();
                              
                              for($i = 0; $i < count($nazvyVsechIngredienci); $i++)
                              {
                                  $nazvyVsechIngredienci[$i] = $ingredineceC->LoadAllNamesIngredience()[$i]["nazev"];                   
                              }
                             
                              //vyplave pole s ingred ktere jeste neexistuji
                              $noveIngredNoIndex = array_diff($ingredience, $nazvyVsechIngredienci); //array diff bere indexy z puvodniho pole -> hodnoty v [7][9]
                              $noveIngred = array_values($noveIngredNoIndex);  //reindexuje pole od 0           
                              
                              for($i = 0; $i < count($noveIngred); $i++)
                              {
                                  $ingredineceC->InsertIngredience($noveIngred[$i]);       
                              }
                              
                              //vlozeni ingredienci receptu
                              $ingredReceptuID = array();
                              for($i = 0; $i < count($ingredience); $i++)
                              {
                                  $ingredReceptuID[$i] = $ingredineceC -> GetIngredienceByName($ingredience[$i]);     
                              }
                              
                              $nowexistID = $recepty->GetRecipesByName($nazev)["id_recept"];
                                                         
                              for($i = 0; $i < count($ingredReceptuID); $i++)
                              {                  
                                 $ingredinece_receptu->InsertIngredienceReceptu($nowexistID, $ingredReceptuID[$i]["id_ingredience"], $mnozstvi[$i]) ;
                              } 
                              
                              for($i = 0; $i < $pocetFotek; $i++)
                              {                  
                                  $foto->InsertPhoto($nowexistID);
                              } 
                               
                               //idPhoto slouzi jako pole nazvu pro fotky na upload
                               $fotoAll = $foto->GetFotoByIDReceptu($nowexistID);
                              
                               for($i = 0; $i < count($fotoAll); $i++)
                               {                  
                                   $idPhoto[$i] = $fotoAll[$i]["id_foto"];
                               }                                 
                              
                               if(count($idPhoto) != 0)
                               {
                                  $img = photoUpload($nowexistID, $idPhoto);
                               }
                                
                                if($img != 1)
                                {
                                  $stringWarnigPhoto = $img;
                                }
                                  
                              $allOK = 1;                              
                      } //if exist
                      else
                      {
                          $stringWarnigValuesDB .= 'Recept s tímto názvem již existuje.';
                      }
                  } //ifnull
                
                elseif(!is_numeric ($doba_pripravy))                
                  $stringWarnigValues .= 'Do kolonky "Doba přípravy" napište pouze číslo vyjadřující počet minut potřebných k vytvoření receptu.';
                else
                  $stringWarnigValues .= 'Všechny kolonky musí být vyplněné.';
                  
                 
          }
      
           //pokud byl formular ok
          if($allOK == 1)
          {
          
              $template_params["nadpis1"] = "Uložení receptu ".$nazev;
              $template_params["obsah"] = '
                                                  <div class="panel panel-success">
                                                    <div class="panel-heading">
                                                      <h2 class="panel-title">Nový recept byl úspěšně uložen</h2>
                                                    </div>
                                                    <div class="panel-body">
                                                      Blahopřejeme, Váš recept byl úspěšně uložen. <a href="?page=recipes_alphabet">Vyhledat recept.</a>
                                                      <br>'.$stringWarnigPhoto.'
                                                    </div>
                                                  </div>
                                              ';
          }
          else //pokud formular OK nebyl
          {
            $invisible = "";          
            $template_params["nadpis1"] = "Uložení receptu";
            $template_params["obsah"] = '
                                                <div class="panel panel-danger">
                                                  <div class="panel-heading">
                                                    <h2 class="panel-title">Uložení nového receptu neproběhlo úspěšně</h2>
                                                  </div>
                                                  <div class="panel-body">
                                                    '.$stringWarnigValues.$stringWarnigValuesDB.$stringWarnigPhoto.'
                                                    <br>
                                                     '.vratChangeForm($nazev, $ingredience, $mnozstvi, $postup, $narocnost, $doba_pripravy, NULL).'
                                                  </div>
                                                </div>
                                            ';
         }

        
    
    }            
  
  
  echo $template->render($template_params); 
 
?>

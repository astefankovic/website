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
            
            
            if ($receptID != "")  //chci jen upravovat
            {              
                $receptAll = $recepty -> GetRecipesByID($receptID) ; //data o receptu
                $fotoAll = $foto -> GetFotoByIDReceptu($receptID) ; //data o receptu
            
                $nazev = $receptAll["nazev"]; 
                $recept_id = $receptAll["id_recept"]; 
                $postup = $receptAll["postup"];
                $narocnost = $receptAll["narocnost"];
                $doba_pripravy = $receptAll["doba_pripravy_min"];
                $uzivatel_id = $receptAll["uzivatel"];
               // print_r($fotoAll);
                
                $ingredienceIDALL = "";   //vsechny IDcka ingredienci receptu
                $ingredienceAll = "";  //vsechny udaje o danych ingredienci recepu
                $ingredience = "";   //pole nazvu ingredienci
                $mnozstvi = "";   //pole mnozstvi jednotlivych ingredienci
                
                $tvurceReceptuAll = $uzivatele->GetUzivatelByID($uzivatel_id);
                $tvurceReceptuNick =  $tvurceReceptuAll["prezdivka"]; 
                
                $ingredinece_receptuAll =  $ingredinece_receptu->GetIngredienceReceptuByID($recept_id);
                //ze smycky vylezou 2 pole - mnozstvi a ingredience
                for($i = 0; $i < count($ingredinece_receptuAll); $i++)
                {
                   $mnozstvi[$i] =  $ingredinece_receptuAll[$i]["mnozstvi"] ;
                   $ingredienceIDALL[$i] =  $ingredinece_receptuAll[$i]["ingredience_id"] ;
                   $ingredienceAll[$i] = $ingredineceC->GetIngredienceByID($ingredienceIDALL[$i]);
                   $ingredience[$i] =  $ingredienceAll[$i]["nazev"] ;                  
                }
            
            }
              
          }

  $template = $twig->loadTemplate('sablona_basic.html');
  $template_params = array();   
         
  if (empty($_POST))  //uživatel je na stránce poprvé
  {  
        if($uzivatel_id != "" && $userNick != "")   //prihlaseny uzivatel chce upravit recept
        {  
              
              
               //nacitani fotek
               $uploads = "public/img/";  //slozka se slozkama podle id receptu
               $fileName = $receptID."/"; //slozka pro dany recept
                
                //projdu celou slozku receptu a vysypu do pole nazvy vsech souboru
                    if ($dir = opendir($uploads.$fileName)) 
                    {
                    	$images = array();
                    	while (false !== ($file = readdir($dir))) 
                      {
                    		if ($file != "." && $file != "..")
                        {
                    			$images[] = $file; 
                    		}
                    	}
                    	closedir($dir);
                    }
                
               // $pocetFotek = count($images); //pocet fotek ktere jsou realne dostupne
                   
                   $fotoPole = "";
                   $i = 0;
                   foreach($images as $image) 
                   {                                       
                          $fotoPole[$i] = $uploads.$fileName.$image;
                          $i++;                          
                  
                   }//foreach  
                  //session_start(); 
                  $_SESSION["pf"]= $fotoPole;
                   
             //      print_r($fotoPole) ;
              
              
              if($tvurceReceptuNick == $userNick || $admin == 1)
              {
                //nacteniformulare
                $template_params["nadpis1"] = "Editace receptu ".$nazev;
              	$template_params["obsah"] = '
                                              Pokud chcete svůj přidaný recept ještě něčím obohatit, jste na správně cestě.
                                              <br>                
                                            '.vratChangeForm($nazev, $ingredience, $mnozstvi, $postup, $narocnost, $doba_pripravy, $fotoPole).'        
                                                    
                                            ';   //vrati vyplneni formular 
              }
              else
              {        
                //nacteniformulare
                $template_params["nadpis1"] = "Milý přihlášený uživateli";
              	$template_params["obsah"] = 'Tohle území je mimo tvé pravomoce.';
                               
              }
        
         
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
          //$pocetNacetlychFotek = $_POST["pocetFoto"];
          $pocetFotek = 0;
          
          if($fotky[0] != "")
            $pocetFotek = count($fotky);
            
            
          
          $allOK = 0;
          $stringWarnigValues = "Znovu vyberte fotografie. ";
          $stringWarnigValuesDB = "";
          $stringWarnigPhoto = ""; 
          
           if ($userNick != "")  //prihlaseny uzivatel chce odeslat recept
           {  
                           
                if ($nazev!=NULL && $ingredience!=NULL && $mnozstvi!=NULL && $postup!=NULL && $narocnost!=NULL && $doba_pripravy!=NULL && is_numeric ($doba_pripravy)) //vsechna pole jsou vyplnena
                {     
                     //pokud dany nazev jiz existuje
                     $exist = $recepty->GetRecipesByName($nazev);
                    
                     //pokud upravuji recept s puvodnim nazvem -> muzu upravit nazev, ale nemusim
                     $aktualniAll = $recepty->GetRecipesByID($receptID);
                     $nazev_aktual = "";
                     
                      
                     if($aktualniAll["nazev"] == $nazev)
                     {
                        $nazev_aktual = 1;
                     }
                                      
                    
                     if($exist == NULL || $nazev_aktual == 1)//dany nazev receptu neexistuje, nebo upravuji dany recept a nechci nazev menit
                         {                     
                               $idPhoto = "";
                               $img = "";
                                                        
                              //update receptu                              
                              $recepty->UpdateRecept($receptID, $nazev, $postup, $narocnost, $doba_pripravy);
                              
                              //vlozeni novych ingredienci - chci vkladat nove, ne update
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
                              
                              //$nowexistID = $recepty->GetRecipesByName($nazev)["id_recept"];
                              $ingredinece_receptu->DeleteIngredienceReceptu($receptID);
                              
                              for($i = 0; $i < count($ingredReceptuID); $i++)
                              {     
                                  //echo  $receptID;                
                                  $ingredinece_receptu->InsertIngredienceReceptu($receptID, $ingredReceptuID[$i]["id_ingredience"], $mnozstvi[$i]);
                              }  
                                
                              $nazvyVsechFotekReceptu = $foto->GetFotoByIDReceptu($receptID);
                               for($i = 0; $i < count($nazvyVsechFotekReceptu); $i++)
                              {
                                 $nazvyVsechFotekReceptu = $nazvyVsechFotekReceptu;       
                              }
                              
                              //nactu co je v db a ulozim do pole jen id -> nazvy fotek
                              $fotoAllOld = $foto->GetFotoByIDReceptu($receptID);
                              for($i = 0; $i < count($fotoAllOld); $i++)
                              {
                                  $fotoAllOld[$i] = $fotoAllOld[$i]["id_foto"];                   
                              } 
                             
                             //vlozim nove fotky
                              for($i = 0; $i < $pocetFotek; $i++)
                              {                  
                                  $foto->InsertPhoto($receptID);
                              } 
                               
                               //idPhoto slouzi jako pole nazvu pro fotky na upload
                              //nactu co je v db po ulozeni novych a ulozim do pole 
                              $fotoAllNew = $foto->GetFotoByIDReceptu($receptID);
                              for($i = 0; $i < count($fotoAllNew); $i++)
                              {
                                  $fotoAllNew[$i] = $fotoAllNew[$i]["id_foto"];                   
                              } 
                              
                              //zjistim jestli neco pribylo a seradim si pole
                              $novefotoNoIndex = array_diff($fotoAllNew, $fotoAllOld); //array diff bere indexy z puvodniho pole -> hodnoty v [7][9]
                              $novefoto = array_values($novefotoNoIndex);  //reindexuje pole od 0
                              
                              //ulozim si id novych fotek
                               for($i = 0; $i < count($novefoto); $i++)
                               {                  
                                   $idPhoto[$i] = $novefoto[$i];
                               }                              
                              
                               //vlozim nove fotky
                               if(count($idPhoto) != 0)
                               {
                                  $img = photoUpload($receptID, $idPhoto);
                               }
                                
                                if($img != 1)
                                {
                                  $stringWarnigPhoto = $img;
                                }
                                
                              //echo($pocetNacetlychFotek); 
                               if(isset($_SESSION["pf"]))
                               {
                                  $poleFotek = $_SESSION["pf"];
                                  if($poleFotek != "")
                                  { 
                                    for($i = 0; $i < count($poleFotek); $i++)
                                    {
                                        if($_POST['options'.$i.''] == "delete")
                                        {
                                         
                                          unlink ($poleFotek[$i]);
                                          
                                          $nazevonly = substr(strrchr($poleFotek[$i], "/"), 1);                                           
                                          $pole=explode(".", $nazevonly);                                          
                                          $foto-> UpdateFoto($pole[0], 1);
                                        }               
                                    }   
                                  }
                              } 
                              // unset($_SESSION["pf"]);  
                            
                              //to samý fotky
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
                                                      <h2 class="panel-title">Recept byl úspěšně uložen</h2>
                                                    </div>
                                                    <div class="panel-body">
                                                      Blahopřejeme, Váš recept byl úspěšně uložen. <a href="?page=recipes&id='.$receptID.'">Zde</a> se můžete podívat na Váš nový recept.
                                                    </div>
                                                  </div>
                                              ';
          }
          else //pokud formular OK nebyl
          {
              $invisible = "";
               if($admin == 1)
               {
                //jestli recept patri userovi nebo jestli pristupuje admin
                $invisible = '<div class="form-group">
                                              <div class="col-sm-offset-0 col-sm-10">
                                                <div class="checkbox">
                                                  <label>
                                                    <input type="checkbox" name="invisible"> Zneviditelnit
                                                  </label>
                                                </div>
                                              </div>
                              </div>
                              ';
                              
                }
          
            $template_params["nadpis1"] = "Uložení receptu";
            $template_params["obsah"] = '
                                                <div class="panel panel-danger">
                                                  <div class="panel-heading">
                                                    <h2 class="panel-title">Uložení receptu neproběhlo úspěšně</h2>
                                                  </div>
                                                  <div class="panel-body">
                                                    '.$stringWarnigValues.'
                                                    <br>
                                                     '.vratChangeForm($nazev, $ingredience, $mnozstvi, $postup, $narocnost, $doba_pripravy, $invisible).'
                                                  </div>
                                                </div>
                                            ';
         }
 
        
    
    }            
  
  
  echo $template->render($template_params); 
 
?>

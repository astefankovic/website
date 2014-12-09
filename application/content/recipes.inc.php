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
        	$recepty = new recepty($db_connection);
          $ingredience = new ingredience($db_connection);
          $ingredience_receptu = new ingredience_receptu($db_connection);
          $uzivatele = new uzivatele($db_connection);
          $hodnoceni = new hodnoceni($db_connection);
          $foto = new foto($db_connection);
          $userNick = "";  
          
          if(isset($_SESSION["id"]))   //uzivatel je prihlasen
          {
            $userNick = $_SESSION["id"]; 
          }
          //promenne do twigu     
          $id = @$_REQUEST["id"];   //hodnota id = -> id receptu ktery se ma vypsat
    	      if ($id == "")
            {
             //pro nahodnevybrany recept
              $recepty_all = $recepty->LoadAllRecepty();   //pole receptu
              $pocetReceptu = count($recepty_all);
              $randomRecept = rand(0,$pocetReceptu-1);
               
              $idReceptu = $recepty_all[$randomRecept]["id_recept"];
              $nazevReceptu = $recepty_all[$randomRecept]["nazev"];
              $postup = $recepty_all[$randomRecept]["postup"]; 
              $tvurceReceptu = $recepty_all[$randomRecept]["uzivatel"]; 
            }
            else
            {
              //pro konkretni recept
              $idReceptu =  $id;
              $receptPodleID = $recepty->GetRecipesByID($idReceptu) ;
              
              $nazevReceptu = $receptPodleID["nazev"];
              $postup = $receptPodleID["postup"];
              $tvurceReceptu = $receptPodleID["uzivatel"];
            }           
           
          //pole ingredienci receptu - id a mnozstvi
          $ingredience_all = $ingredience_receptu->GetIngredienceReceptuByID($idReceptu);
          $pocetIngred =  count($ingredience_all);           
          
          //pocet fotek
          $foto_all = $foto->GetFotoByIDReceptu($idReceptu);  
         // print_r($foto_all);
          $pocetFotekDB = count($foto_all);
          
	// nacist danou sablonu z adresare
	$template = $twig->loadTemplate('sablona_recipes.html');
	
	// render vrati data pro vypis nebo display je vypise
	// v poli jsou data pro vlozeni do sablony                                 
  $template_params = array();
                       
   if($pocetFotekDB > 0) //overeni jestli uzivatel nejakou fotku pridal
   {
          //nacitani fotek
         $uploads = "public/img/";  //slozka se slozkama podle id receptu
         $fileName = $idReceptu."/"; //slozka pro dany recept
          
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
          
          $pocetFotek = count($images); //pocet fotek ktere jsou realne dostupne
          
          //zalozeni carouselu
          $template_params["carousel"] =  '
                                            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                                            <ol class="carousel-indicators"> 
                                          ';            
                      //predpripravy potrebny pocet itemu
                        for($y = 0; $y < $pocetFotek; $y++)
                        {
                          if ($y == 0)   //zalozeni carouselu
                          { 
                            $template_params["carousel"] .= '<li data-target="#carousel-example-generic" data-slide-to="'.$y.'" class="active"></li>';
                          }               
                          else
                          { 
                            $template_params["carousel"] .=  '<li data-target="#carousel-example-generic" data-slide-to="'.$y.'"></li>';
                          }      
                        }                                        
                                                            
          $template_params["carousel"] .= '</ol><div class="carousel-inner">';  
          
          $i = 0;  
          foreach($images as $image) 
          {
          	//echo $uploads.$fileName.$image."<br>";
            //unlink($uploads.$fileName.$image);
        
                        if ($i == 0)   //prvni fotka - active class
                        {                                       
                          $template_params["carousel"] .=   '
                                                               <div class="item active">
                                                                  <img src="'.$uploads.$fileName.$image.'" alt=" obrazek">
                                                                  <div class="carousel-caption">
                                                                    <h3>'.$nazevReceptu.'</h3>
                                                                  </div>
                                                                </div>
                                                            ';
                        }
                        else
                        {
                           $template_params["carousel"] .=   '
                                                               <div class="item">
                                                                  <img src="'.$uploads.$fileName.$image.'" alt=" obrazek v prezentaci">
                                                                  <div class="carousel-caption">
                                                                    <h3>'.$nazevReceptu.'</h3>
                                                                  </div>
                                                                </div>
                                                            ';
                        }
                        
                        $i++;
                  
          }//foreach   
       
          //ukonceni carouselu
          $template_params["carousel"] .=  '    </div>
                                                          <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                                                            <span class="glyphicon glyphicon-chevron-left"></span>
                                                          </a>
                                                          <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                                                            <span class="glyphicon glyphicon-chevron-right"></span>
                                                          </a>
                                                        </div> 
                                                    ';
         
    }     
    //pri zadne fotce vytiskne univerzalni obrazek
    else
    {
       $template_params["carousel"] =  '   <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                                            <ol class="carousel-indicators">
                                              <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                                            </ol>
                                            <div class="carousel-inner">
                                              <div class="item active">
                                                   <img  class="img-responsive center-block" data-src="holder.js/1140x500/auto/#555:#333/text:Fotografie nebyla přidána :-(" alt="Nepridana fotografie">
                                                   <div class="carousel-caption">
                                                    <h3>'.$nazevReceptu.'</h3>
                                                  </div>
                                              </div>
                                            </div>
                                          </div>
                                            ';   
    }   
    $template_params["nadpis1"] = $nazevReceptu;
    
      //smycka ktera vytiskne ingredience do tabulky
      For($i = 0; $i < $pocetIngred; $i++)
      {   
        //nazvy ingredienci
        $nazev_ingredience = $ingredience -> GetIngredienceByID($ingredience_all[$i]["ingredience_id"]);          
                  
        if ($i == 0)   //zalozeni tabulky
        {                  
          @$template_params["ingred"] = '<table class="table table-hover">
                                           <tr>
                                            <th>Ingredience</th>
                                            <th>Množství</th>                                                
                                           </tr>';  
        }    
                    
        //vytiskne vsechny ingredience                             
        @$template_params["ingred"] .= '<tr>
                                         <td>'.$nazev_ingredience["nazev"].'</td>
                                         <td>'.$ingredience_all[$i]["mnozstvi"].'</td>                                                  
                                       </tr>';
        if ($i == $pocetIngred-1) //konec tabulky
        {   
          $template_params["ingred"] .= '</table>';
        }
      }
      
      
  
  $template_params["postup"] =  $postup;
  
  
  if(isset($tvurceReceptu)) 
  {
      $uzivateleAll = $uzivatele ->  GetUzivatelByID($tvurceReceptu);
      $jmenoTvurceReceptu =  $uzivateleAll["prezdivka"];
      
      $template_params["uzivatel"] =  $jmenoTvurceReceptu;
      
      if($userNick == $jmenoTvurceReceptu || (isset($_SESSION["admin"]) && $_SESSION["admin"] == 1))
        {
          $template_params["upravit"] =  '. <a href="?page=change_recipes&amp;id='.$idReceptu.'">Upravit</a> tento recept.';
        }
  }
  
  if (empty($_POST))  //uživatel je na stránce poprvé
  {   
    if($userNick != "" && isset($_REQUEST["id"]))
        {
              $value = "";
              
                 $hodnota1 = "";
                 $hodnota2 = "";
                 $hodnota3 = "";
                 $hodnota4 = "";
                 $hodnota5 = "";
                 $hodnota6 = "";
              
              $uzivateleAll = $uzivatele -> GetUzivatelOnlyByNick($userNick);
              $idHodnoticiho =  $uzivateleAll["id_uzivatel"];
        
              $hodnoceneReceptyUzivatele = $hodnoceni -> LoadAllHodnoceniUsera($idHodnoticiho)   ;
              for($i = 0;$i < count($hodnoceneReceptyUzivatele);$i ++)
              {
                   if($hodnoceneReceptyUzivatele[$i]["recept_id"] == $idReceptu)
                      $value = $hodnoceneReceptyUzivatele[$i]["hodnota"] ;
                  
              }
           
           if($value != "")
           {       
                  switch ($value) 
                  {
                    case 1:
                       $hodnota1 = 'selected="selected"';
                        break;
                    case 2:
                        $hodnota2 = 'selected="selected"';
                        break;
                    case 3:
                       $hodnota3 = 'selected="selected"';
                        break;
                    case 4:
                       $hodnota4 = 'selected="selected"';
                        break;
                    case 5:
                       $hodnota5 = 'selected="selected"';
                        break;
                    default:
                      $hodnota6 = 'selected="selected"';
                  }
            }
            else
            {
              $hodnota6 = 'selected="selected"';
            }
        
          $template_params["hodnoceni"] = '     
                                                <form class="form form-horizontal" role="form" method="POST">
                                                <div class="row">   
                                                 <div class="col-md-2"> 
                                                      <select class="form-control glyphicon" name="hodnoceniU">
                                                          <option value = "1" '.$hodnota1.'>&#xe006;</option>
                                                          <option value = "2" '.$hodnota2.'>&#xe006;&#xe006;</option>
                                                          <option value = "3" '.$hodnota3.'>&#xe006;&#xe006;&#xe006;</option>
                                                          <option value = "4" '.$hodnota4.'>&#xe006;&#xe006;&#xe006;&#xe006;</option>
                                                          <option value = "5" '.$hodnota5.'>&#xe006;&#xe006;&#xe006;&#xe006;&#xe006;</option>
                                                          <option value = "6" '.$hodnota6.'>Vyberte</option>
                                                      </select>
                                                      
                                                    </div>
                                                    <button type="submit" name="fn" class="btn btn-success">Ohodnotit &raquo;</button>
                                                  </div>
                                              </form>
                                           ';  
         }    
   }
   else  //byl vyplneny formular
   {    
        
        if($userNick != "")
        {
            $hodnoceniUzivatelem = $_POST["hodnoceniU"];
            $hodnota;
             $value = ""; 
              $uzivateleAll = $uzivatele ->  GetUzivatelOnlyByNick($userNick);
              $idHodnoticiho =  $uzivateleAll["id_uzivatel"];
        
              $hodnoceneReceptyUzivatele = $hodnoceni -> LoadAllHodnoceniUsera($idHodnoticiho)   ;
              for($i = 0;$i < count($hodnoceneReceptyUzivatele);$i ++)
              {
                   if($hodnoceneReceptyUzivatele[$i]["recept_id"] == $idReceptu)
                      $value = $hodnoceneReceptyUzivatele[$i]["hodnota"] ;
                  
              }
              
        if($hodnoceniUzivatelem != 6)
        {
             switch ($hodnoceniUzivatelem) 
                    {
                      case 1:
                         $hodnota = 1;
                          break;
                      case 2:
                          $hodnota = 2;
                          break;
                      case 3:
                         $hodnota = 3;
                          break;
                      case 4:
                         $hodnota = 4;
                          break;
                      case 5:
                         $hodnota = 5;
                          break;
                    }
             
             
             
             if($value == "")
             {  
                  $hodnoceni ->  InsertHodnoceni($idHodnoticiho, $idReceptu, $hodnota);
             }  
             else
             {  
                
                  $hodnoceni ->   UpdateHodnoceni($idHodnoticiho, $idReceptu, $hodnota) ;
             }  
                //promenne slouzici ke spravnemu vyhodnoceni formu
              $template_params["hodnoceni"] = '     
                                                 <p class="bg-success">Děkujeme za Vaše hodnocení. <a href="?page=recipes&id='.$idReceptu.'">Změnit.</a></p>
                                           ';   
              header('Refresh: 5'); 
         }
         else
         {
             //promenne slouzici ke spravnemu vyhodnoceni formu
            $template_params["hodnoceni"] = '     
                                               <p class="bg-danger">Musíte vybrat počet hvězdiček, 1 hvězdička odpovídá nepoživatelnému jídlu, naopak 10 hvězdiček znamená jídlo z kvalitní restaurace. <a href="?page=recipes&id='.$idReceptu.'">Vybrat.</a></p>
                                         ';   
            header('Refresh: 15'); 
         
         }   
      }
   }
                                
 	echo $template->render($template_params);
?>
                       
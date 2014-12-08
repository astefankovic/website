<?php  
	// cesta k adresari se sablonama - od index.php
	$loader = new Twig_Loader_Filesystem('public/sablony');
	$twig = new Twig_Environment($loader); // takhle je to bez cache     

	// nacist danou sablonu z adresare
	$template = $twig->loadTemplate('sablona_basic.html');
	
	// render vrati data pro vypis nebo display je vypise
	// v poli jsou data pro vlozeni do sablony  
  $template_params = array();
  $template_params["nadpis1"] = "Deset nejlépe hodnocených receptů";
	$template_params["obsah"] =  'Zde můžete najít naši top10.';     
  
     // start the application
  	$app = new app(); 	
  	// pripojit k db
  	$app->Connect(); 	
  	// pripojeni k db
    $db_connection = $app->GetConnection();
    // vytvorit objekt, ktery mi poskytne pristup k DB a vlozit mu connector k DB
  	$recepty = new recepty($db_connection);
    $hodnoceni = new hodnoceni($db_connection);
          
    $hodnoceniAll = $hodnoceni->LoadAllHodnoceni(); 
    
    For($i = 0; $i < count($hodnoceniAll); $i++)
    {
        $hodnoceneRecepty[$i] = $hodnoceniAll[$i]["recept_id"];
    }
    
    $poctyHodnoceneRecepty = (array_count_values($hodnoceneRecepty));
    
    
    $hodnoceneRecepty = array_unique($hodnoceneRecepty);
    $hodnoceneRecepty = array_values($hodnoceneRecepty);
   
    
    For($i = 0; $i < count($hodnoceneRecepty); $i++)
    {
       $sum[$i]["recept"] =  $hodnoceneRecepty[$i];
       $sum[$i]["hodnoceni"] =  $hodnoceni->LoadSum($hodnoceneRecepty[$i])[0]["sum(hodnota)"]   ;
       //arsort($sum[$i][$hodnoceneRecepty[$i]]);
    }
 
    
 
    usort($sum, "cmp");  //cmp je fce ve functions , setridi pole podle velikosti

    $pocet = 10;
    
    if(10 > count($sum))
      $pocet = count($sum);    
     
   
                                            
    $template_params["tabulka_obsah"] =  '<table class="table table-hover">
                                              <tr>
                                                <th>Název</th>
                                                <th>Náročnost (1-10)</th>                                                 
                                                <th>Doba přípravy</th>
                                                <th>Datum přidání</th>                                                 
                                              </tr>'; 
                                                                                                    
   
     
    $template_params["tabulka_paticka"] = "";
    
    //vypise jiz serazene recepty do tabulky
    $i = 1;  
       For($j = 0; $j < $pocet; $j++)
        { 
          $recepty_data = $recepty-> GetRecipesByID($sum[$j]["recept"]);
          
          $celkoveHodnoceni = ""; 
          $pocetHodnoceni = ""; 
        
          $phpdate = strtotime( $recepty_data["datum_pridani"] );
          $mysqldate = date( 'd.m.Y', $phpdate );
          $id_receptu =  $recepty_data["id_recept"];
        /*  
          foreach ($poctyHodnoceneRecepty as $key => $value) 
          {
            if($key == $id_receptu)
                $pocetHodnoceni = $value;
            //echo 'numbers of '.$key.' equal '.$value.'<br/>';
          }
          
          $celkoveHodnoceni =  $sum[$j]["hodnoceni"];
        
          $stredniHodnota = $celkoveHodnoceni/$pocetHodnoceni; */
        
          $template_params["tabulka_paticka"] .= '                                                
                                                  <tr class="clickableRow" href="?page=recipes&id='.$id_receptu.'">
                                                    <td>'.$i.'. <a href="?page=recipes&id='.$id_receptu.'">'.$recepty_data["nazev"].'</a></td>
                                                    <td>'.$recepty_data["narocnost"].'</td>  
                                                    <td>'.$recepty_data["doba_pripravy_min"].' minut </td>  
                                                    <td>'.$mysqldate.'</td>                                                  
                                                </tr>
                                                
                                                ';
          if ($j == $pocet-1)
          {   
            $template_params["tabulka_paticka"] .= '</table>';
          }
          $i++;
        
        }

	echo $template->render($template_params);
?>

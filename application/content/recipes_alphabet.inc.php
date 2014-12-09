<?php  
	// cesta k adresari se sablonama - od index.php
	$loader = new Twig_Loader_Filesystem('public/sablony');
	$twig = new Twig_Environment($loader); // takhle je to bez cache     

	// nacist danou sablonu z adresare
	$template = $twig->loadTemplate('sablona_basic.html');
	
	// render vrati data pro vypis nebo display je vypise
	// v poli jsou data pro vlozeni do sablony  
  $template_params = array();
  $template_params["nadpis1"] = "Recepty seřazené podle abecedy";
	$template_params["obsah"] =  'Zde můžete najít přehledně seřazené recepty.';     
  
     // start the application
  	$app = new app(); 	
  	// pripojit k db
  	$app->Connect(); 	
  	// pripojeni k db
    $db_connection = $app->GetConnection();
    // vytvorit objekt, ktery mi poskytne pristup k DB a vlozit mu connector k DB
  	$recepty = new recepty($db_connection);
    
    $recepty_data = $recepty->LoadAllRecepty();
                                                
    $template_params["tabulka_obsah"] =  '<table class="table table-hover">
                                              <tr>
                                                <th>Název</th>
                                                <th>Náročnost (1-10)</th>
                                                <th>Doba přípravy</th>
                                                <th>Datum přidání</th>                                                 
                                              </tr>'; 
                                                                                                    
   
    $pocet = count($recepty_data); 
    $template_params["tabulka_paticka"] = "";
    //vypise jiz serazene recepty do tabulky
    For($i = 0; $i < $pocet; $i++)
    {             
        $phpdate = strtotime( $recepty_data[$i]["datum_pridani"] );
        $mysqldate = date( 'd.m.Y', $phpdate );
        $id_receptu =  $recepty_data[$i]["id_recept"];
                                     
        $template_params["tabulka_paticka"] .= '                                                
                                                  <tr class="clickableRow" href="?page=recipes&amp;id='.$id_receptu.'">
                                                    <td><a href="?page=recipes&amp;id='.$id_receptu.'">'.$recepty_data[$i]["nazev"].'</a></td>
                                                    <td>'.$recepty_data[$i]["narocnost"].'</td>  
                                                    <td>'.$recepty_data[$i]["doba_pripravy_min"].' minut </td>  
                                                    <td>'.$mysqldate.'</td>                                                  
                                                </tr>
                                                
                                                ';
        if ($i == $pocet-1)
        {   
          $template_params["tabulka_paticka"] .= '</table>';
        }
     }  
   
	echo $template->render($template_params);
?>

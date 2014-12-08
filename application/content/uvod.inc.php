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
          $foto = new foto($db_connection); 

         
          $posledniRecept = $recepty->LoadLastAddRecept();
       
          $idPrvnihoReceptu = $posledniRecept[0]["id_recept"];
          $nazevPrvnihoReceptu = $posledniRecept[0]["nazev"];
          $postupPrvnihoReceptu =  $posledniRecept[0]["postup"];
          $foto_all = $foto->GetFotoByIDReceptu($idPrvnihoReceptu);  
          
          $pocetFotek = count($foto_all);
          $cestaFoto = "";
          if($pocetFotek == 0)
              $cestaFoto = '<img  class="img-responsive center-block" data-src="holder.js/460x460/auto/#555:#333/text:Fotografie nebyla přidána" alt="Nepridana fotografie">';
          else
          {  
             //nacitani fotek
             $uploads = "public/img/";  //slozka se slozkama podle id receptu
             $fileName = $idPrvnihoReceptu."/"; //slozka pro dany recept
              
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
            $cesta = $images[0];
            $cestaFoto =   '<img src="'.$uploads.$fileName.$cesta.'" alt="img">';
           // $cestaFoto =   '<img src="public/img/'.$idPrvnihoReceptu.'/'.$idPrvnihoReceptu.'_0.jpg" alt="img">';
            //$cestaFoto =   '<img class="zoom-image" src="public/img/4/4_1.jpg" alt="img">';
         }   
        $postupPrvnihoReceptuRozdelen = substr($postupPrvnihoReceptu,0,280);

  //nacteni sablony stranky
	$template = $twig->loadTemplate('sablona_basic.html');   	
  $template_params = array();
  
  $template_params["nadpis1"] = "Vítejte v nejlepší eKuchařce!";
	$template_params["obsah"] = 'Projekt eKuchařka vznikl jako semestrální práce z předmětu KIV/WEB.';  
  $template_params["tlacitko"] = '<a href="?page=contact" class="btn btn-primary btn-lg" role="button">Mám nápad na vylepšení &raquo;</a>';
  $template_params["variabilni_obsah"] = '
                                                     <!-- START THE FEATURETTES -->  
                                                      <hr class="featurette-divider">
                                                      <div class="row featurette">
                                                        <div class="col-md-7">
                                                          <h2 class="featurette-heading"><a href="?page=recipes&id='.$idPrvnihoReceptu.'">Poslední přidaný recept. <span class="text-muted">'.$nazevPrvnihoReceptu.' </span></a></h2>
                                                          <hr>
                                                          <p class="lead">'.$postupPrvnihoReceptuRozdelen.'....</p>
                                                        </div>
                                                        
                                                        <div class="col-md-5">           
                                                            <!-- Bottom to top effect-->  
                                                                      <div class="ih-item square effect6 top_to_bottom">
                                                                      <a href="?page=recipes&id='.$idPrvnihoReceptu.'">
                                                                        <div class="img">
                                                                            '.$cestaFoto.'
                                                                        </div>
                                                                        <div class="info">
                                                                            <h3>'.$nazevPrvnihoReceptu.'</h3>
                                                                            <p>To si, ale pochutnáte.</p>
                                                                        </div>
                                                                      </a>
                                                                    </div>                                       
                                                            <!-- end Bottom to top-->         
                                                        </div>
                                                      </div>
                                                
                                                      <hr class="featurette-divider">
                                                
                                                   <!-- /END THE FEATURETTES --> 
                                          ';                  
  
	echo $template->render($template_params); 
 
?>

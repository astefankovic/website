<?php    

      //univerzalni, funguje vzdy
    	function phpWrapperFromFile($filename)
    	{
    		ob_start();
    	
    		if (file_exists($filename) && !is_dir($filename))
    		{
    			include($filename);
    		}
    	
    		// nacte to z outputu
    		$obsah = ob_get_clean();
    		return $obsah;
    	}  
          
      //vypise menu 
       function vypismenu($page)
       {                               
            // cesta k adresari se sablonama - od index.php
            $loader = new Twig_Loader_Filesystem('public/sablony');
            $twig = new Twig_Environment($loader); // takhle je to bez cache
       
             //nacteni menu
            $template_menu = $twig->loadTemplate('sablona_menu.html');	
          	// render vrati data pro vypis nebo display je vypise
          	// v poli jsou data pro vlozeni do sablony
            $template_menu_params = array();        
            
            /////////////////////////////////////////////
            $template_params["nazev_stranky"] = "asdjb"; 	
            
            if(isset($_SESSION["id"]))
            {
              $template_menu_params["uzivatel"] = $_SESSION['id'];
              $template_menu_params["prihlasit"] = '<a href="application/config/odhlasit.php">Odhlásit se </a>';
              
              if(isset($_SESSION["admin"]) && $_SESSION["admin"] == 1)
              {
                 $template_menu_params["uzivatele"] = '<li><a href="?page=users_alphabet">Tabulka uživatelů</a></li>';
              
              }
            }
            else
            {
              $template_menu_params["uzivatel"] = "Uživatel";
              $template_menu_params["prihlasit"] = '<a href="#myModal" data-toggle="modal" data-target="#myModal">Přihlásit se / Registrovat se</a>';
            } 
            $template_menu_params["$page"] = "class = active";                 
          	echo $template_menu->render($template_menu_params);
        }
        
        
        function vratRegForm($jmeno, $prijmeni, $nick, $pass, $pass2, $noAdmin)
        {
          if($nick == NULL && isset($_SESSION["id"]))
          {
              $nemozne = 'placeholder="Vaše uživatelské jméno nelze změnit." disabled=""';
          }
          else
          {
             $nemozne = 'placeholder="Vyberte si svou přezdívku"';
          }
         $form = '
                                    <form class="form form-horizontal" role="form" method="POST">
                                      <div class="form-group">
                                        <div class="col-md-4">
                                          <label for="exampleInputEmail1">Jméno</label>
                                          <input type="text" class="form-control" id="inputName" name="regName" placeholder="Zde napiště své jméno" value="'.$jmeno.'">
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <div class="col-md-4">
                                          <label for="exampleInputEmail1">Příjmení</label>
                                          <input type="text" class="form-control" id="input2Name" name="reg2Name" placeholder="Zde vyplňte své příjmení" value="'.$prijmeni.'">
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <div class="col-md-4">
                                          <label for="exampleInputEmail1">Uživatelské jméno</label>
                                          <input type="text" class="form-control" id="inputNick" name="regNick" '.$nemozne.' value="'.$nick.'">
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <div class="col-md-4">
                                          <label for="exampleInputPassword1">Heslo</label>
                                          <input type="password" class="form-control" id="inputPass" name="regPass" placeholder="Zvolte si heslo které obsahuje alespoň 5 znaků" value="'.$pass.'">
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <div class="col-md-4">
                                          <label for="exampleInputPassword1">Heslo pro kontrolu</label>
                                          <input type="password" class="form-control" id="inputPass2" name="regPass2" placeholder="Napište prosím heslo pro kontrolu" value="'.$pass2.'">
                                        </div>
                                      </div>
                                      <button type="submit" class="btn btn-primary btn-lg">Uložit údaje &raquo;</button>
                                    </form> 
                      ';
          return $form;
        }
        
          
        function vratChangeForm($nazev, $ingredience, $mnozstvi, $postup, $narocnost, $doba_pripravy, $fotky)
        { 
        //smycka na pocet ingred
          $pocet_ing  = count($ingredience); 
          $pocet_fotek = count($fotky);
          
          //$fotky[0] = "holder.js/300x300/auto/#555:#333/text:Fotografie nebyla přidána"; 
          $foto = "";
                          
          if($fotky != "") 
            {
              $foto =  '                         
                                                <div class="col-sm-6 col-md-4">
                                                  <div class="thumbnail">
                                                    <img src="'.$fotky[0].'" alt="...">
                                                    <div class="caption">
                                                      <div class="btn-group" data-toggle="buttons">
                                                        <label class="btn btn-default active">
                                                          <input type="radio" name="options0" id="ok" value="ok" autocomplete="off" checked>Ponechat <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                                        </label>
                                                        <label class="btn btn-default">
                                                          <input type="radio" name="options0" id="delete" value="delete" autocomplete="off">Smazat <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                                        </label>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>                               
                                                ';  
           }      
                     
        for($i = 1; $i<$pocet_fotek; $i++)
        {
          $add =                    '          <div class="col-sm-6 col-md-4">
                                                  <div class="thumbnail">
                                                    <img src="'.$fotky[$i].'" alt="...">
                                                    <div class="caption">
                                                      <div class="btn-group" data-toggle="buttons">
                                                        <label class="btn btn-default active">
                                                          <input type="radio" name="options'.$i.'" id="ok" value="ok" autocomplete="off" checked>Ponechat <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                                        </label>
                                                        <label class="btn btn-default">
                                                          <input type="radio" name="options'.$i.'" id="delete" value="delete" autocomplete="off">Smazat <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                                        </label>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>                                 
                                                '; 
           $foto .=  $add;                                   
                                                
      }                             
                             
            
              
          $Ing_mnz =  '                                   <div class="multi-field">
                                                              <div class="input-group">                                                    
                                                                <input type="text" class="form-control" name="ingredience[]" id="boxIngred1" placeholder="Zde napište název ingredience" value="">
                                                                <span class="input-group-btn"> 
                                                                  <button type="button" class="btn btn-danger remove-field" href="#">Odstranit <span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>                                                     
                                                                </span>                                                         
                                                              </div><!-- /input-group -->
                                                              <div class="input-group">
                                                              <span class="input-group-addon">Množství <span class="glyphicon glyphicon-hand-right" aria-hidden="true"></span></span>
                                                              <input type="text" class="form-control col-md-8" placeholder="i s jednotkou, např. 2lžíce" name="mnozstvi[]" id="boxMnozstvi1" value="">
                                                              </div><!-- /input-group -->
                                                           </div>   
                                                          </div>                               
                                                ';   
        if($ingredience != "") 
            {
              $Ing_mnz =  '                               <div class="multi-field">
                                                              <div class="input-group">                                                    
                                                                <input type="text" class="form-control" name="ingredience[]" id="boxIngred1" placeholder="Zde napište název ingredience" value="'.$ingredience[0].'">
                                                                <span class="input-group-btn"> 
                                                                  <button type="button" class="btn btn-danger remove-field" href="#">Odstranit <span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>                                                     
                                                                </span>                                                         
                                                              </div><!-- /input-group -->
                                                              <div class="input-group">
                                                              <span class="input-group-addon">Množství <span class="glyphicon glyphicon-hand-right" aria-hidden="true"></span></span>
                                                              <input type="text" class="form-control col-md-8" placeholder="i s jednotkou, např. 2lžíce" name="mnozstvi[]" id="boxMnozstvi1" value="'.$mnozstvi[0].'">
                                                              </div><!-- /input-group -->
                                                           </div>   
                                                          </div>                               
                                                ';  
           }      
                     
        for($i = 1; $i<$pocet_ing; $i++)
        {
          $add =                                '         <div class="multi-field">
                                                              <div class="input-group">                                                    
                                                                <input type="text" class="form-control" name="ingredience[]" id="boxIngred1" placeholder="Zde napište název ingredience" value="'.$ingredience[$i].'">
                                                                <span class="input-group-btn"> 
                                                                  <button type="button" class="btn btn-danger remove-field" href="#">Odstranit <span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>                                                     
                                                                </span>                                                         
                                                              </div><!-- /input-group -->
                                                              <div class="input-group">
                                                              <span class="input-group-addon">Množství <span class="glyphicon glyphicon-hand-right" aria-hidden="true"></span></span>
                                                              <input type="text" class="form-control col-md-8" placeholder="i s jednotkou, např. 2lžíce" name="mnozstvi[]" id="boxMnozstvi1" value="'.$mnozstvi[$i].'">
                                                              </div><!-- /input-group -->
                                                           </div>                                 
                                                '; 
           $Ing_mnz .=  $add;                                   
                                                
      }
         $form = '
                                       
                                         <div class="my-form">  
                                            <form class="form form-horizontal" role="form" method="POST" enctype="multipart/form-data">
                                            
                                              <div class="form-group">
                                                <div class="col-md-5">
                                                  <label for="inputName">Název</label>
                                                  <input type="text" class="form-control" id="inputName" name="Name" placeholder="Zde napište vhodný název receptu" value="'.$nazev.'">
                                                </div>
                                              </div>
                                              <div class="form-group">     
                                                   <div class="col-md-5"> 
                                                   <label for="boxMnozstvi1">Ingredience a množství</label>                                       
                                                       <div class="multi-field-wrapper">
                                                          <div class="multi-fields">
                                                        '.$Ing_mnz.'
                                                          <button class="btn btn-success add-field" href="#" type="button"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Další ingredience</button>
                                                      </div>   <!-- /multi-fields -->
                                                      
                                                  </div>                                    
                                              </div>
                                              <div class="form-group">
                                                  <div class="col-md-5">
                                                    <label for="postup">Postup</label>
                                                    <textarea class="ckeditor form-control" rows="3" name="postup" placeholder="Zde popiště jak dát recept dohromady." >'.$postup.'</textarea>
                                                  </div>
                                              </div>
                                              <div class="form-group">
                                                <div class="col-md-4">
                                                  <label for="narocnost">Náročnost (1 = začátečník, 10 = šéfkuchař)</label>
                                                  <input type="number" name="narocnost" value = "'.$narocnost.'" min="1" max="10">
                                                </div>
                                              </div>
                                              <div class="form-group">
                                                <div class="col-md-2">
                                                  <label for="doba_pripravy">Doba přípravy</label>
                                                  <input type="text" class="form-control" id="inputPass" name="doba_pripravy" placeholder="v minuátch" value="'.$doba_pripravy.'">
                                                </div>
                                              </div>
                                              <div class="row">
                                              '.$foto.'
                                              </div>
                                              <div class="form-group">
                                                <div class="col-md-5">
                                                  <label for="doba_pripravy">Fotografie</label>
                                                  <input type="file" name="photo[]" id="photo" multiple>
                                                </div>
                                              </div>
                                              <button type="submit" class="btn btn-primary btn-lg">Uložit data &raquo;</button>
                                            </form>
                                          </div>        
                                      '; 
          return $form;
        }
        
        function test_input($data) 
        {
              $data = trim($data);
              $data = stripslashes($data);
              $data = htmlspecialchars($data);
              return $data;
        }
        
         function test_input_textarea($data) 
        {
             // $data = trim($data);
              //$data = stripslashes($data);
              return $data;
        }
  
      	// specialni vypis
      	function printr($val)
      	{
      	  echo "<hr><pre>";
      	  print_r($val);
      	  echo "</pre><hr>";
      	}
        
             
    function photoUpload($idReceptu, $idPhoto)
    {
    
        $err = "";
          
    		$path = 'public/img/';
        $fileName = $idReceptu."/"; //slozka pro dany recept
    		$file_ext   = array('jpg','png','gif','bmp','JPG');	
     
        if (!file_exists($path.$fileName))
        {
          mkdir($path.$fileName);
        }
     
          		$photo_name = $_FILES['photo']['name'];
          		$photo_type = $_FILES['photo']['type'];
          		$photo_size = $_FILES['photo']['size'];
          		$photo_tmp  = $_FILES['photo']['tmp_name'];
          		$photo_error= $_FILES['photo']['error'];
            
              
              
              for($i = 0;$i < count($photo_size);$i++)
              {  
              
                  $tmp = explode('.',$photo_name[$i]);
              		$post_ext   = strtolower (end($tmp));
                  
              		//move_uploaded_file($photo_tmp,"uploads/".$photo_name);
              		if((($photo_type[$i] == 'image/jpeg') || ($photo_type[$i] == 'image/gif')||($photo_type[$i] == 'image/png') || ($photo_type[$i] == 'image/pjpeg')) &&($photo_size[$i] < 9000000) && in_array($post_ext,$file_ext))
                  {                    
                			if($photo_error[$i] > 0 )
                      {
                				$err = 'Chyba při nahrání fotky '.$photo_error[$i].'<br>Recept byl uložen, přidat fotografii můžete <a href="?page=change_recipes&id='.$idReceptu.'">zde</a>.';
                			}
                      else
                      {
                				//new photo name and encryption
                				$new_name = explode('.',$photo_name[$i]);
                    
                				//$new_photo_name = 'NazevReceptu_'.md5($new_name[0]).'.'.$new_name[1];
                			  $new_photo_name = $idPhoto[$i].'.'.$new_name[1];  
                        
                				//move to directory
                				if(move_uploaded_file($photo_tmp[$i],$path.$fileName.$new_photo_name))
                        {
                					$err = 1;
                				}
                			}
                      
                      
              		}
              		else
                  {
              			$err = ' <span class="glyphicon glyphicon-floppy-remove" aria-hidden="true"></span> Nahrávaná fotografie má nekorektní parametry. Jsou podporovány pouze soubory do velikosti 5MB s příponou .jpg, .png, .gif, .bmp. Recept byl uložen, přidat fotografii můžete <a href="?page=change_recipes&id='.$idReceptu.'">zde</a>.';
              		}             
            }
            
            return $err;
      
	}
        
        
        function reArrayFiles(&$file_post) 
        {
        
            $file_ary = array();
            $file_count = count($file_post['name']);
            $file_keys = array_keys($file_post);
        
            for ($i=0; $i<$file_count; $i++)
             {
                foreach ($file_keys as $key)
                {
                    $file_ary[$i][$key] = $file_post[$key][$i];
                }
            }
        
            return $file_ary;
        }


       function GetBetween($var1,$var2,$pool)
       {
        $temp1 = strpos($pool,$var1)+strlen($var1);
        $result = substr($pool,$temp1,strlen($pool));
        $dd=strpos($result,$var2);
        if($dd == 0)
        {
          $dd = strlen($result);
        }
        
        return substr($result,0,$dd);
        }
        
function cmp($a, $b)
{
    return $b['hodnoceni'] - $a['hodnoceni'];
}
      
?>
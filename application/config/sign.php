<?php
  session_start();
  
  require_once 'config.inc.php';
	require_once 'functions.inc.php';					// pomocne funkce
	
	// nacist objekty - soubory .class.php
	require_once '../core/app.class.php';			// drzi hlavni funkcionalitu cele aplikace, obsahuje routing = navigovani po webu
	require_once '../core/db.class.php';			// zajisti pristup k db a spolecne metody pro dalsi pouziti
	require_once '../core/uzivatele.class.php';		// zajisti pristup ke konkretnim db tabulkam - objekt vetsinou zajisti pristup k cele sade souvisejicich tabulek             
              
   if (!empty($_POST))
   {       
        $uzivatel =  $_POST["nick"];
        $heslo = $_POST["pass"];             
         
         // start the application
      	$app = new app();      	
      	// pripojit k db
      	$app->Connect();       	
      	// pripojeni k db
        $db_connection = $app->GetConnection();        
        // vytvorit objekt, ktery mi poskytne pristup k DB a vlozit mu connector k DB
        $uzivatele = new uzivatele($db_connection);          
   
        $vysledek = $uzivatele->GetUzivatelByNick($uzivatel, $heslo);
        $admin =  $vysledek["admin"];
       
        
        
        if ($vysledek=="") 
        {
          // $radek = mysql_fetch_array($vysledek);
            //$_SESSION["id"]= 0; 
             header("Location: ../../index.php?page=uvod_bad");
           //  echo "Uživatelské jméno a/nebo heslo nesouhlasí";
             exit;
        
          
        }   
        else 
        {             
          if($admin == 1)
          {
             // $radek = mysql_fetch_array($vysledek);
             $_SESSION["id"]= $vysledek["prezdivka"];
             $_SESSION["admin"] = $admin; 
             header("Location: ../../index.php");
             exit;
          }
          else      
          {
             // $radek = mysql_fetch_array($vysledek);
             $_SESSION["id"]= $vysledek["prezdivka"];
             $_SESSION["admin"] = $admin; 
             header("Location: ../../index.php");
             exit;  
          } 
        }                         
            
   }
?>
<?php 

// diky extends mohu pouzivat metody db - jako DBSelect ...
class uzivatele extends db
{
	// konstruktor
	public function uzivatele($connection)
	{
		// timto si nastavim pripojeni k DB, ktere jsem dostal od app()
		$this->connection = $connection;	
	}
	
	
	public function InsertUzivatel($jmeno, $prijmeni, $nick, $heslo)
	{
		  $item = array("id_uzivatel" => "", "jmeno" => $jmeno, "prijmeni" => $prijmeni, "prezdivka" => $nick, "admin" => "0", "heslo" => $heslo);
      
      $uzivatele = $this-> DBInsert("uzivatel", $item);
      
	}
  
	public function UpdateUzivatel($id_user, $jmeno, $prijmeni, $heslo)
	{
		  //$item = array('jmeno' => $jmeno, "prijmeni" => $prijmeni, "heslo" => $heslo);
      $item[0] = array("column" =>"jmeno", "value" => $jmeno);
      $item[1] = array("column" =>"prijmeni", "value" => $prijmeni);
      $item[2] = array("column" =>"heslo", "value" => $heslo);
      
      $where_array[] = array("column" =>"id_uzivatel", "value" => $id_user, "symbol" => "=");
      
      $uzivatele = $this-> DBUpdate("uzivatel", $item, $where_array);
      
	}
	
		
	public function GetUzivatelByID($uzivatel_id)
	{
  
		$table_name = "uzivatel";
		$select_columns_string = "*"; 
		$where_array[] = array("column" =>"id_uzivatel", "value" => $uzivatel_id, "symbol" => "=");
		$limit_string = "";
		
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$uzivatele = $this->DBSelectOne($table_name, $select_columns_string, $where_array, $limit_string);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $uzivatele;
	}
  
  public function GetUzivatelByNick($uzivatel_nick, $uzivatel_heslo)
	{
  
		$table_name = "uzivatel";
		$select_columns_string = "*"; 
		$where_array[0] = array("column" =>"prezdivka", "value" => $uzivatel_nick, "symbol" => "=");
    $where_array[1] = array("column" =>"heslo", "value" => $uzivatel_heslo, "symbol" => "=");
		$limit_string = "";
		
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$uzivatele = $this->DBSelectOne($table_name, $select_columns_string, $where_array, $limit_string);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $uzivatele;
	}
  
  public function GetUzivatelOnlyByNick($uzivatel_nick)
	{
  
		$table_name = "uzivatel";
		$select_columns_string = "*"; 
		$where_array[0] = array("column" =>"prezdivka", "value" => $uzivatel_nick, "symbol" => "=");
		$limit_string = "";      	
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$uzivatele = $this->DBSelectOne($table_name, $select_columns_string, $where_array, $limit_string);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $uzivatele;
	}
	
	
	public function LoadAlluzivatele()
	{
		$table_name = "uzivatel";
		$select_columns_string = "*"; 
		$where_array = array();
		$limit_string = "";
		$order_by_array[0] = array("column" => "prezdivka", "sort" => "ASC");
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$uzivatele = $this->DBSelectAll($table_name, $select_columns_string, $where_array, $limit_string, $order_by_array);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $uzivatele;
	}

}


?>
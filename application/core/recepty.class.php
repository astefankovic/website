<?php 

// diky extends mohu pouzivat metody db - jako DBSelect ...
class recepty extends db
{
	// konstruktor
	public function recepty($connection)
	{
		// timto si nastavim pripojeni k DB, ktere jsem dostal od app()
		$this->connection = $connection;	
	}
	
	
	public function InsertRecipes($nazev, $postup, $narocnost, $doba_pripravy, $uzivatel, $datum_pridani)
	{
		  $item = array("id_recept" => "", "nazev" => $nazev, "postup" => $postup, "narocnost" => $narocnost, "doba_pripravy_min" => $doba_pripravy, "uzivatel" => $uzivatel, "datum_pridani" => $datum_pridani);
      
      $uzivatele = $this-> DBInsert("recept", $item);
	}
	
	
  public function UpdateRecept($idRecpet, $nazev, $postup, $narocnost, $doba_pripravy)
	{
		  //$item = array('jmeno' => $jmeno, "prijmeni" => $prijmeni, "heslo" => $heslo);
      $item[0] = array("column" =>"nazev", "value" => $nazev);
      $item[1] = array("column" =>"postup", "value" => $postup);
      $item[2] = array("column" =>"narocnost", "value" => $narocnost);
      $item[3] = array("column" =>"doba_pripravy_min", "value" => $doba_pripravy);
      
      $where_array[] = array("column" =>"id_recept", "value" => $idRecpet, "symbol" => "=");
      
      $uzivatele = $this-> DBUpdate("recept", $item, $where_array);
      
	}
	
	
	public function GetRecipesByID($recept_id)
	{
  
		$table_name = "recept";
		$select_columns_string = "*"; 
		$where_array[] = array("column" =>"id_recept", "value" => $recept_id, "symbol" => "=");
		$limit_string = "";
		
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$recepty = $this->DBSelectOne($table_name, $select_columns_string, $where_array, $limit_string);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $recepty;
	}
  
  	
	public function GetRecipesByName($recept_name)
	{
  
		$table_name = "recept";
		$select_columns_string = "*"; 
		$where_array[] = array("column" =>"nazev", "value" => $recept_name, "symbol" => "=");
		$limit_string = "";
		
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$recepty = $this->DBSelectOne($table_name, $select_columns_string, $where_array, $limit_string);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $recepty;
	}
  
  
  	public function GetRecipesByUser($user_id)
	{
  
		$table_name = "recept";
		$select_columns_string = "*"; 
		$where_array[] = array("column" =>"uzivatel", "value" => $user_id, "symbol" => "=");
		$limit_string = "";
    $order_by_array[0] = array("column" => "nazev", "sort" => "ASC");
		
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$recepty = $this->DBSelectAll($table_name, $select_columns_string, $where_array, $limit_string, $order_by_array);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $recepty;
	}
  
	public function LoadAllRecepty()
	{
		$table_name = "recept";
		$select_columns_string = "*"; 
		$where_array = array();
		$limit_string = "";
		$order_by_array[0] = array("column" => "nazev", "sort" => "ASC");
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$recepty = $this->DBSelectAll($table_name, $select_columns_string, $where_array, $limit_string, $order_by_array);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $recepty;
	}
  
  	public function LoadLastAddRecept()
	{
		$table_name = "recept";
		$select_columns_string = "*"; 
		$where_array = array();
		$limit_string = "limit 1";
		$order_by_array[0] = array("column" => "id_recept", "sort" => "DESC");
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$recepty = $this->DBSelectAll($table_name, $select_columns_string, $where_array, $limit_string, $order_by_array);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $recepty;
	}
}


?>
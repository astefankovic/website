<?php 

// diky extends mohu pouzivat metody db - jako DBSelect ...
class ingredience extends db
{
	// konstruktor
	public function ingredience($connection)
	{
		// timto si nastavim pripojeni k DB, ktere jsem dostal od app()
		$this->connection = $connection;	
	}
	
	public function GetIngredienceByID($ingredience_id)
	{
  
		$table_name = "ingredience";
		$select_columns_string = "*"; 
		$where_array[] = array("column" =>"id_ingredience", "value" => $ingredience_id, "symbol" => "=");
		$limit_string = "";
		
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$ingredience = $this->DBSelectOne($table_name, $select_columns_string, $where_array, $limit_string);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $ingredience;
	}
  
  	public function GetIngredienceByName($ingredience_name)
	{
  
		$table_name = "ingredience";
		$select_columns_string = "id_ingredience"; 
		$where_array[] = array("column" =>"nazev", "value" => $ingredience_name, "symbol" => "=");
		$limit_string = "";
		
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$ingredience = $this->DBSelectOne($table_name, $select_columns_string, $where_array, $limit_string);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $ingredience;
	}
  
  public function InsertIngredience($nazev)
	{
		     
		  $item = array("id_ingredience" => "", "nazev" => $nazev);
      
      $ingredience = $this-> DBInsert("ingredience", $item);
	}  
    
	public function LoadAllNamesIngredience()
	{
		$table_name = "ingredience";
		$select_columns_string = "nazev"; 
		$where_array = array();
		$limit_string = "";
		$order_by_array = array();
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$ingredience = $this->DBSelectAll($table_name, $select_columns_string, $where_array, $limit_string, $order_by_array);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $ingredience;
	}
}


?>
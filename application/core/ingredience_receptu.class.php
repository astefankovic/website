<?php 

// diky extends mohu pouzivat metody db - jako DBSelect ...
class ingredience_receptu extends db
{
	// konstruktor
	public function ingredience_receptu($connection)
	{
		// timto si nastavim pripojeni k DB, ktere jsem dostal od app()
		$this->connection = $connection;	
	}
	
	
	public function InsertIngredienceReceptu($recept, $ingredience, $mnozstvi)
	{ 
		  $item = array("recept_id" => $recept, "ingredience_id" => $ingredience, "mnozstvi" => $mnozstvi);
      
      $ingredRecept = $this-> DBInsert("ingredience_receptu", $item);
	}
	
	
	public function DeleteIngredienceReceptu($recept_id)
	{
		  $item = array();
      
      $where_array[] = array("column" =>"recept_id", "value" => $recept_id, "symbol" => "=");
      
      $ingredRecept = $this-> DBDelete("ingredience_receptu", $item, $where_array);
	}
	
	
	public function GetIngredienceReceptuByID($recept_id)
	{
  
		$table_name = "ingredience_receptu";
		$select_columns_string = "*"; 
		$where_array[] = array("column" =>"recept_id", "value" => $recept_id, "symbol" => "=");
		$limit_string = "";
    $order_by_array = array();
		
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$ingredRecept = $this->DBSelectAll($table_name, $select_columns_string, $where_array, $limit_string, $order_by_array);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $ingredRecept;
	}

  

}


?>
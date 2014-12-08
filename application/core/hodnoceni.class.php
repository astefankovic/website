<?php 

// diky extends mohu pouzivat metody db - jako DBSelect ...
class hodnoceni extends db
{
	// konstruktor
	public function hodnoceni($connection)
	{
		// timto si nastavim pripojeni k DB, ktere jsem dostal od app()
		$this->connection = $connection;	
	}
	
	public function GeHodnoceniByID($recept_id)
	{
  
		$table_name = "hodnoceni";
		$select_columns_string = "*"; 
		$where_array[] = array("column" =>"recept_id", "value" => $recept_id, "symbol" => "=");
		$limit_string = "";
		
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$hodnoceni = $this->DBSelectOne($table_name, $select_columns_string, $where_array, $limit_string);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $hodnoceni;
	}
  
  	public function UpdateHodnoceni($uzivatel, $recept, $hodnota)
	{
		  //$item = array('jmeno' => $jmeno, "prijmeni" => $prijmeni, "heslo" => $heslo);
      $item[0] = array("column" =>"hodnota", "value" => $hodnota);
      
      $where_array[0] = array("column" =>"uzivatel_id", "value" => $uzivatel, "symbol" => "=");
      $where_array[1] = array("column" =>"recept_id", "value" => $recept, "symbol" => "=");
      
      $hodnoceni = $this-> DBUpdate("hodnoceni", $item, $where_array);
      
	}

  
  public function InsertHodnoceni($uzivatel, $recept, $hodnota)
	{
		     
		  $item = array("id_hdnoceni" => "", "uzivatel_id" => $uzivatel, "recept_id" => $recept, "hodnota" => $hodnota);
      
      $hodnoceni = $this-> DBInsert("hodnoceni", $item);
	}  
    
	public function LoadAllHodnoceniUsera($uzivatel)
	{
		$table_name = "hodnoceni";
		$select_columns_string = "*"; 
		$where_array[] = array("column" =>"uzivatel_id", "value" => $uzivatel, "symbol" => "=");
		$limit_string = "";
		$order_by_array[0] = array("column" => "hodnota", "sort" => "ASC");
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$hodnoceni = $this->DBSelectAll($table_name, $select_columns_string, $where_array, $limit_string, $order_by_array);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $hodnoceni;
	}
  
 
	public function LoadAllHodnoceni()
	{
		$table_name = "hodnoceni";
		$select_columns_string = "*"; 
		$where_array = array();
		$limit_string = "";
		$order_by_array[0] = array("column" => "hodnota", "sort" => "ASC");
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$hodnoceni = $this->DBSelectAll($table_name, $select_columns_string, $where_array, $limit_string, $order_by_array);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $hodnoceni;
	}
 public function LoadSum($recept)
	{
		$table_name = "hodnoceni";
		$select_columns_string = "sum(hodnota)"; 
		$where_array[0] = array("column" =>"recept_id", "value" => $recept, "symbol" => "=");
		$limit_string = "";
		$order_by_array[0] = array("column" => "recept_id", "sort" => "ASC");
	
		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
		$hodnoceni = $this->DBSelectAll($table_name, $select_columns_string, $where_array, $limit_string, $order_by_array);
		//printr($predmety);
		
		// tady jeste neco pripadne dochroupat - docist vsechna potrebna data
		
		// vratit data
		return $hodnoceni;
	}

  
}


?>
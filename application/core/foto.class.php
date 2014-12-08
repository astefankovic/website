<?php 

  // diky extends mohu pouzivat metody db - jako DBSelect ...
  class foto extends db
  {
  	// konstruktor
  	public function foto($connection)
  	{
  		// timto si nastavim pripojeni k DB, ktere jsem dostal od app()
  		$this->connection = $connection;	
  	}
    
  public function InsertPhoto($recept_id)
	{
		  $item = array("id_foto" => "", "recept_id" => $recept_id, "smazano" => "0");
      
      $foto = $this-> DBInsert("foto", $item);
	}
  
  public function UpdateFoto($id_foto, $value)
	{
		  //$item = array('jmeno' => $jmeno, "prijmeni" => $prijmeni, "heslo" => $heslo);
      $item[0] = array("column" =>"smazano", "value" => $value);
      
      $where_array[] = array("column" =>"id_foto", "value" => $id_foto, "symbol" => "=");
      
      
      $uzivatele = $this-> DBUpdate("foto", $item, $where_array);
      
	}
  	
  	public function GetFotoByIDReceptu($recept_id)
  	{       
  		$table_name = "foto";
  		$select_columns_string = "*"; 
  		$where_array[0] = array("column" =>"recept_id", "value" => $recept_id, "symbol" => "=");
      $where_array[1] = array("column" => "smazano", "value" => "0", "symbol" => "=");
  		$limit_string = "";
      $order_by_array = array();  		
  	
  		// vrati pole zaznamu v podobe asociativniho pole: sloupec = hodnota
  		$fotkyRecept = $this->DBSelectAll($table_name, $select_columns_string, $where_array, $limit_string, $order_by_array);
      
  		// vratit data
  		return $fotkyRecept;
  	}
  }
?>
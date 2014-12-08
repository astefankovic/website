<?php
class Uploader
{
	public function photoUpload()
  {
		$path = 'public/img/';
		$file_ext   = array('jpg','png','gif','bmp','JPG');
    $tmp = explode('.',$_FILES['photo']['name']);
		$post_ext   = end($tmp);
		$photo_name = $_FILES['photo']['name'];
		$photo_type = $_FILES['photo']['type'];
		$photo_size = $_FILES['photo']['size'];
		$photo_tmp  = $_FILES['photo']['tmp_name'];
		$photo_error= $_FILES['photo']['error'];
		//move_uploaded_file($photo_tmp,"uploads/".$photo_name);
		if((($photo_type == 'image/jpeg') || ($photo_type == 'image/gif')||($photo_type == 'image/png') || ($photo_type == 'image/pjpeg')) &&($photo_size < 2000000) && in_array($post_ext,$file_ext))
    {
  			if($photo_error > 0 )
        {
  				echo 'Chyba při nahrání fotky '.$photo_error;
  				exit;
  			}
        else
        {
  				echo $photo_name.' Uploaded !';
  			}
			if(file_exists($path.$photo_name))
      {
				echo 'There is '.$photo_name;
			}
      else
      {
				//new photo name and encryption
				$new_name = explode('.',$photo_name);
				//$photo_name = 'NazevReceptu_'.md5($new_name[0]).'.'.$new_name[1];
			  $photo_name = 'NazevReceptu_1.'.$new_name[1];  
          
				//move to directory
				if(move_uploaded_file($photo_tmp,$path.$photo_name)){
					 
					return $photo_name;
				}
			}
		}
		else
    {
			echo 'Nahrávaná fotografie má nekorektní parametry. Jsou podporovány pouze soubory s příponou .jpg, .png, .gif, .bmp.';
		}
	}
  
  	public function photoUpload2()
    {
    		$path = 'public/img/';
    		$file_ext   = array('jpg','png','gif','bmp','JPG');	
     
              
          		$photo_name = $_FILES['photo']['name'];
          		$photo_type = $_FILES['photo']['type'];
          		$photo_size = $_FILES['photo']['size'];
          		$photo_tmp  = $_FILES['photo']['tmp_name'];
          		$photo_error= $_FILES['photo']['error'];
            
              
              for($i = 0;$i < count($photo_size);$i++)
              {   
              
                  $tmp = explode('.',$photo_name[$i]);
              		$post_ext   = strtolower (end($tmp));
                  echo  $post_ext;
                  
              		//move_uploaded_file($photo_tmp,"uploads/".$photo_name);
              		if((($photo_type[$i] == 'image/jpeg') || ($photo_type[$i] == 'image/gif')||($photo_type[$i] == 'image/png') || ($photo_type[$i] == 'image/pjpeg')) &&($photo_size[$i] < 5000000) && in_array($post_ext,$file_ext))
                  {                    
                			if($photo_error[$i] > 0 )
                      {
                				echo 'Chyba při nahrání fotky '.$photo_error[$i];
                			}
                      else
                      {
                				//new photo name and encryption
                				$new_name = explode('.',$photo_name[$i]);
                        
                				$new_photo_name = 'NazevReceptu_'.md5($new_name[0]).'.'.$new_name[1];
                			  //$photo_name = 'NazevReceptu_1.'.$new_name[1];  
                        
                				//move to directory
                				if(move_uploaded_file($photo_tmp[$i],$path.$new_photo_name))
                        {
                					echo $photo_name[$i].' Uploadeed !';
                				}
                			}
                      
                      
              		}
              		else
                  {
              			echo 'Nahrávaná fotografie má nekorektní parametry. Jsou podporovány pouze soubory do velikosti 5MB s příponou .jpg, .png, .gif, .bmp.';
              		}
             
            }
      
	}
	 
	public function resizejpeg($dir, $newdir, $img, $max_w, $max_h, $th_w, $th_h)
  {
	    // set destination directory
	    if (!$newdir) $newdir = $dir;
	
	    // get original images width and height
	    list($or_w, $or_h, $or_t) = getimagesize($dir.$img);
	
	    // make sure image is a jpeg
	    if ($or_t == 2)
       {
	    
	        // obtain the image's ratio
	        $ratio = ($or_h / $or_w);
	
	        // original image
	        $or_image = imagecreatefromjpeg($dir.$img);
	
	        // resize image?
	        if ($or_w > $max_w || $or_h > $max_h) 
          {
	
	            // resize by height, then width (height dominant)
	            if ($max_h < $max_w) 
              {
	                $rs_h = $max_h;
	                $rs_w = $rs_h / $ratio;
	            }
	            // resize by width, then height (width dominant)
	            else 
              {
	                $rs_w = $max_w;
	                $rs_h = $ratio * $rs_w;
	            }
	
	            // copy old image to new image
	            $rs_image = imagecreatetruecolor($rs_w, $rs_h);
	            imagecopyresampled($rs_image, $or_image, 0, 0, 0, 0, $rs_w, $rs_h, $or_w, $or_h);
	        }
	        // image requires no resizing
	        else 
          {
	            $rs_w = $or_w;
	            $rs_h = $or_h;
	
	            $rs_image = $or_image;
	        }
	
	        // generate resized image
	        imagejpeg($rs_image, $newdir.$img, 100);
	
	        $th_image = imagecreatetruecolor($th_w, $th_h);
	
	        // cut out a rectangle from the resized image and store in thumbnail
	        $new_w = (($rs_w / 2) - ($th_w / 2));
	        $new_h = (($rs_h / 2) - ($th_h / 2));
	
	        imagecopyresized($th_image, $rs_image, 0, 0, $new_w, $new_h, $rs_w, $rs_h, $rs_w, $rs_h);
	
	        // generate thumbnail
	        imagejpeg($th_image, $newdir.'thumb_'.$img, 100);
	
	        return true;
	    } 
	
	    // Image type was not jpeg!
	    else 
      {
	        return false;
	    }
     }
    
    public function dumpPhoto($thumb,$dir)
    {
  		$thumb = 'thumb_'.$thumb;
  		$img   = $dir.$thumb;
  		$dump  = "<img src=$img />";
  		return $dump;
	  }
}	

?>

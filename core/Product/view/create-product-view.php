<?php

  echo "<form role='form' method='POST' action='admin.php?p=product&a=create_prod&c=add_product'>";
    // Category
    echo "<div class='row'>";
        echo "<div class='col-xs-6 col-sm-6 col-md-3 col-lg-2'>";
            echo "<div class='form-group>'>";
                echo "<label class='control-label'>{$aMsgPost[$lang]['lib_input_categories']}</label>";
                echo "<select class='form-control' name='id_cat'>";
                    foreach ($aCat as $aValCat) {
                        $iIdCat = $aValCat['id_cat'];
                        $iNameCat = $aValCat['cat_name'];
                        echo "<option value='$iIdCat'>$iNameCat</option>";
                    }
                echo "</select>";
            echo "</div>";
        echo "</div>";
    echo "</div>";

    // Name
    echo "<div class='row'>";
        echo "<div class='col-xs-6 col-sm-6 col-md-3 col-lg-4'>";
            echo "<div class='form-group'>";
                echo "<label class='control-label'>{$aMsgPost[$lang]['lib_input_name']}</label>";
                echo "<input type='text' class='form-control' name='name_prod' value='$prod_name'>";
            echo "</div>";
        echo "</div>";    
    echo "</div>";

    // Description
    echo "<div class='row'>";
        echo "<div class='col-xs-6 col-sm-6 col-md-3 col-lg-4'>";
            echo "<div class='form-group'>";
                echo "<label class='control-label'>{$aMsgPost[$lang]['lib_input_desc']}</label>";
                echo "<textarea class='form-control' rows='5' name='desc_prod'>$prod_desc</textarea>";
            echo "</div>";    
        echo "</div>";        
    echo "</div>";

    // Prices
    echo "<div class='row'>";
      echo "<div class='col-xs-6 col-sm-6 col-md-3 col-lg-10'>";
        echo "<label class='control-label'>{$aMsgPost[$lang]['lib_input_prices']}</label>";
            
        echo "<div class='my-form'>";
          echo "<p class='text-box'>";
            echo "<input type='text' name='name_price1' value='' id='box1' />";
            echo " <input type='text' name='price1' value='' id='box1' />";
            echo " <button id='add_price' class='btn btn-xs btn-success'> <span class='glyphicon glyphicon-plus'></span></button>";
          echo "</p>";
        echo "</div>";  
      echo "</div>";    
    echo "</div>"; 

        // Submit
        echo "<div class='row'>";
          echo "<div class='col-xs-6 col-sm-6 col-md-3 col-lg-4'>";
            echo "<br>";
            echo "<button class='btn btn-primary' type='submit'>{$aMsgPost[$lang]['lib_button_create']}</button>";  
          echo "</div>";    
        echo "</div>";            
      
  
echo "</form>";
              


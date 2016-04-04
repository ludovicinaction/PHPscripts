<?php
  echo "<form class='form-horizontal' role='form' action='admin.php?p=paypal&c=update_main' method='POST'>";
      echo "<legend>Payal settings</legend>";

        // User
        echo "<div class='form-group'>";
          echo "<label for='user' class='control-label col-xs-1'>API user :</label>";
            echo "<div class='col-xs-3'>";
              echo " <input class='form-control input-md' type='text' maxlength='70' id='user' name='user' value='{$aSettings['user']}'>";
            echo "</div>";
        echo "</div>";

        // Password
        echo "<div class='form-group'>";
          echo "<label for='pwd' class='control-label col-xs-1'>Password :</label>";
          echo "<div class='col-xs-2'>";
            echo " <input class='form-control input-md' type='text' maxlength='30' id='pwd' name='pwd' value='{$aSettings['pwd']}'>";
          echo "</div>";  
        echo "</div>";
        
        
        // Signature
        echo "<div class='form-group'>";
          echo "<label for='signature' class='control-label col-xs-1'>Signature :</label>";
          echo "<div class='col-xs-5'>";
            echo " <input class='form-control input-md' type='text' maxlength='70' id='signature' name='signature' value='{$aSettings['signature']}'>";
          echo "</div>";
        echo "</div>";

        // Endpoint      
        echo "<div class='form-group'>";
          echo "<label for='endpoint' class='control-label col-xs-1'>Endpoint :</label>";
          echo "<div class='col-xs-3'>";
            echo " <input class='form-control input-md' type='text' maxlength='50' id='endpoint' name='endpoint' value='{$aSettings['endpoint']}'>";
          echo "</div>";
        echo "</div>";
    
        // Paypal API version
        echo "<div class='form-group'>";
          echo "<label for='version' class='control-label col-xs-1'>API version:</label>";
          echo "<div class='col-xs-1'>";
            echo " <input class='form-control input-md' type='text' maxlength='6' id='version' name='version' value='{$aSettings['version']}'>";
          echo "</div>";
        echo "</div>";    

        // Money
        echo "<div class='form-group'>";
          echo "<label for='money' class='control-label col-xs-1'>Money :</label>";
          echo "<div class='col-xs-1'>";
            echo "<select  class='form-control' name='money' id='money'>";
            foreach ($aMoneyCodes as $money) {
              if ($money['code'] == $aSettings['money']) echo "<option value='{$money['code']}' selected>{$money['money_name']}</option>";
              else echo "<option value='{$money['code']}'>{$money['money_name']}</option>";
            }
            echo "</select>";          
          echo "</div>";
        echo "</div>";         

        // Sandbox or production choice
        echo "<div class='form-group'>";
          echo "<label for='choice' class='control-label col-xs-1'>Use :</label>";
          echo "<div class='col-xs-3'>";
            if ($prod) {
              echo "<label class='radio-inline'> <input type='radio' value='0' name='opt-use'>Sandbox</label>";
              echo "<label class='radio-inline'> <input type='radio' value='1'name='opt-use' checked>Production</label>";  

            }else{
              echo "<label class='radio-inline'> <input type='radio' value='0' name='opt-use' checked>Sandbox</label>";
              echo "<label class='radio-inline'> <input type='radio' value='1' name='opt-use'>Production</label>";
            }                  
          echo "</div>";
        echo "</div>";
   
      // Submit button
      echo "<div class='form-group'>"; 
          echo "<div class='col-xs-1'>";
          echo "</div>";
          echo "<div class='col-xs-2'>";
              echo "<button class='btn btn-primary' type='submit'>Save</button>";            
          echo "</div>";    
      echo "</div>";  

  echo "</form>";
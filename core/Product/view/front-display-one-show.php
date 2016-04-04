<?php
if (!isset($_SESSION['paypal_link'])) $paypal='#';
else $paypal = $_SESSION['paypal_link'];  


echo "<h2>{$aDataShow['name']}</h2>";

echo "<br><blockquote>{$aDataShow['description']}</blockquote>";

if (empty($_POST) || (!isset($_POST))) $bValid = FALSE;
else $bValid = TRUE;

echo "<form method='post' name='pricesform' action='buy_ticket.php?c=select&id=$id_prod'>";

echo "<table class='table'>";
  echo "<thead>";
    echo "<tr>";
      echo "<th class='col-lg-3'>Prices</th>";
      echo "<th class='col-lg-1'>Number of seats</th>";
      echo "<th class='col-lg-1'>Unit price</th>";
      echo "<th class='col-lg-offet-7'"; echo "</th>";
    echo "</tr>";
  echo "</thead>";


  foreach ($aDataPrices as $p => $aPrices) {
    $name = $aPrices['name']; 
    echo "<tr>";
      echo "<td>"; echo $name; echo "</td>";
      $sShowNameMin = substr($aDataShow['name'], 0, 15) . '-' . $name;
      echo "<input type='hidden' name='name$p' value='$sShowNameMin'>";
      
        //Seats
        echo "<td>";
          echo "<select name='nbseats$p' onchange='pricesform.submit();' class='form-control' id='sel1'>";
            if ($bValid){
              $sNbseats = "nbseats$p";
              $iNbseats = filter_var($_POST[$sNbseats], FILTER_SANITIZE_NUMBER_INT);
            }
            else $iNbseats = 0;            
             
            for ($i=0; $i < 50; $i++) { 
              if ($bValid){                  
                if ($iNbseats == $i) echo "<option selected>$i</option>";
                else echo "<option>$i</option>";
              }
              else echo "<option>$i</option>";          
            }            
          echo "</select>";  
        echo "</td>";
        
        //Price
        echo "<td>";
          $iCalcPrice = number_format($aPrices['incl_tax'], 2, ',', ' ');
          $aCalcPrice[$p][$iNbseats] = $iCalcPrice;
          echo $iCalcPrice;
          echo "<input type='hidden' name='price$p' value='$iCalcPrice'";          
        echo "</td>";
      echo "</form>";  
     
    echo "</tr>";
  }

  // 'Total' line
  echo "<tr>";
    echo "<td>";echo "</td>";
    echo "<td>"; echo "TOTAL"; echo "</td>";

    echo "<td>";  
      $total_format = 0;
      if ($bValid){
        foreach ($aCalcPrice as $prices) {
          foreach ($prices as $Nbseat => $price) {
            $p = strtr($price, "," , ".");
            $total_format +=  floatval($p) * $Nbseat;
          }
        }
       
      }
      echo number_format($total_format, 2, ',', ' ');    
    echo "</td>";    
  echo "</tr>";
  
  // 'Book and pay' line
  if ($total_format > 0) {
    echo "<tr>";
      echo "<td>"; echo "</td>";    
      echo "<td>"; echo "</td>";    
      echo "<td>"; echo "<button formaction='buy_ticket.php?c=pay' class='btn btn-md btn-primary'><span class='fa fa-ticket'> Pay and print ticket </span></button>"; echo "</td>";
    echo "</tr>";
  }  

  echo "<tbody>";
  echo "</tbody>";
echo "</table>";
echo "</form>";

if (isset($_POST)) {
  $_SESSION['total_order']= $total_format;
  
}

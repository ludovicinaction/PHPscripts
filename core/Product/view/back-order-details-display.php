<?php
  //display payer informations
  echo "<h2>{$aMsgPost[$lang]['lib_payer_info']}</h2>";
  echo "<p>";
    echo "<b>{$aDetailsData['payer']['firstname']} {$aDetailsData['payer']['lastname']}</b>";
  echo "</p>";

  echo "<p>";
  echo "{$aDetailsData['payer']['adresse']}";
  echo "</p>";

echo "<p>";
  echo "{$aDetailsData['payer']['zipcode']} {$aDetailsData['payer']['city']}";
echo "</p>";

  echo "<p>";
  echo "Email : {$aDetailsData['payer']['email']}";
  echo "</p>";

echo "<br><br>";
//Display order details
echo "<h2>{$aMsgPost[$lang]['lib_order_details']}</h2>";
echo "<table class='table'>";
	echo "<thead>";
		echo "<tr><th>{$aMsgPost[$lang]['lib_last_name']}</th>";
		echo "<th>{$aMsgPost[$lang]['lib_price']}</th>";
		echo "<th>{$aMsgPost[$lang]['lib_quantity']}</th></tr>";
	echo "</thead>";


  foreach ($aDetailsData['order_details'] as $value) {
    echo "<tr>";
      echo "<td>{$value['name']}</td>";
      echo "<td>{$value['price']}</td>";
      echo "<td>{$value['quantity']}</td>";      
    echo "</tr>";
  }

echo "</table>";
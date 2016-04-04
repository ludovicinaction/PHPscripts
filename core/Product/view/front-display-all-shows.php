<?php

echo "<table class='table'>";
  echo "<thead>";
    echo "<tr>";
      echo "<th>Shows</th>";
      echo "<th>Descriptions</th>";
      echo "<th>Book</th>";
    echo "</tr>";
  echo "</thead>";

  echo "<tbody>";

  foreach ($aDataTickets as $value) {
    $id_prod = $value['id_prod'];
    echo "<tr>";
      echo "<td>"; echo $value['name']; echo "</td>";
      echo "<td>"; echo $value['description']; echo "</td>";
      echo "<td>"; 
        echo "<a href='buy_ticket.php?c=select&id=$id_prod' class='btn btn-xs btn-primary'><span class='fa fa-ticket'> Book </span> </a>";
      echo "</td>";
    echo "</tr>";  
  }



  echo "</tbody>";
echo "</table>";

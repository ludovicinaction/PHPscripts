<?php
$aCatData = $oProd->getDataCategories();

echo "<form action='admin.php?p=product&a=order_admin&c=select_cat' method='POST'>";

echo "<div class='col-xs-12 col-sm-6 col-md-3 col-lg-4'>";  
  echo "<div class='form-group'>";
    echo "<label class='control-label'>{$aMsgPost[$lang]['lib_select_cat']}</label>";
      echo "<select class='form-control' name='category'>";
        foreach ($aCatData as $value) {
          echo "<option value='{$value['id_cat']}'>{$value['cat_name']}</option>";
        }
      echo "</select>";
  echo "</div>"; 
  echo "<button class='btn btn-primary' type='submit'>{$aMsgPost[$lang]['lib_view_button']}</button>";
echo "</div>";

echo "</form>";

echo "<div class='row'>";

if (intval($id_cat) > 0) {
  $aOrder = $oProd->ReadOrders(intval($id_cat));
  if (count($aOrder)==0) die("<p><br><br><br><br><br><br>{$aMsgPost[$lang]['no_found_msg']}</p>");
  echo "<table class='table table-hover'>";
    echo "<thead>";
      echo "<tr>";
        echo "<th>Date</th>";
        echo "<th>{$aMsgPost[$lang]['lib_first_name']}</th>";
        echo "<th>{$aMsgPost[$lang]['lib_last_name']}</th>";
        echo "<th>Total</th>";
        echo "<th>Transaction</th>";
        echo "<th>Status</th>";
        echo "<th>Details</th>";
      echo "</tr>";
    echo "</thead>";

    foreach ($aOrder as $value) {
      $id_order = $value['id_order'];
      $id_payer = $value['id_payer'];
      $id_cat = $value['product_cat'];
      $aPayerData = $oProd->getPayerInformations($id_payer);
      echo "<tr>";
        echo "<td>{$value['date_order']}</td>";
        echo "<td>{$aPayerData['firstname']}</td>";
        echo "<td>{$aPayerData['lastname']}</td>";
        echo "<td>{$value['amt']}</td>";
        echo "<td>{$value['id_transaction']}</td>";
        echo "<td>{$value['chekoutstatus']}</td>";
        echo "<td>";
          echo "<a href='admin.php?p=product&a=order_admin&c=details&id=$id_order&id_cat=$id_cat' target='_blank' class='btn-xs btn btn-success'><span class='glyphicon glyphicon-eye-open'></span> </a>";
        echo "</td>";
      echo "</tr>";

    }
  echo "</table>";

 echo "</div>"; 
}
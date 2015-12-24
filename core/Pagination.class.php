<?php

    /**
     * Pagination avec affichage bootstrap
     * La requête sql qui raméne les élément doit avoir dans sa clause where: LIMIT ' . (($cPage-1)*$parpage) . ', ' . $parpage;
     * $cPage : La page demandé par l'utilateur
     * $parpage : nombre d'élément à afficher par page
     *
     * @package COMMON
     * @author Ludovic <ludovicinaction@yahoo.fr>
     * @uses new Pagination($nbTotArt, $nbrParPage, 'blog_page.php');
     * <code>
     *   $oPagination = new Pagination($nbTotArt, $nbrParPage, 'blog.php');
     *   $oPagination->AffPagination(); 
     * </code>
     * @param int nbTotArt nombre total d'élément à afficher (raméné par la requête)
     * @param int $nbrParPage nombre d'élément que l'on désire voir afficher sur chaque page
     * @param string $pagination nom du fichier php à paginer.
     *
     * @todo : nbrParPage par gérer en backoffice.
     */
    class Pagination
    {
        
        public function AffPagination($nbTotArt, $nbrParPage, $pagination, $crit=''){
             $nbPage = ceil($nbTotArt / $nbrParPage); 
             //$nbPage = $nbTotArt / $nbrParPage; 
             $p_lien = $pagination;

            if (!isset($_GET['page'])) $p=1;
            else $p = (int) $_GET['page'];
             
             $p_suiv = $p+1;
             
             if ($p>1) $p_prec = $p-1;
             else $p_prec = 1;

            echo "<center>";
            echo "<nav>";
                echo "<ul class='pagination'>";
                    echo "<li>";
                        echo "<a href='$p_lien?page=$p_prec' aria-label='Previous'>";
                        echo "<span aria-hidden='false'>&laquo;</span>";
                        echo "</a>";
                    echo "</li>";

                    for ($i=1 ; $i<=$nbPage ; $i++){
                        if ($p == $i) echo "<li class='active'><a href='$p_lien?page=$i&$crit'> $i </a></li>";
                        else echo"<li><a href='$p_lien?page=$i&$crit'> $i </a></li>";
                        }

                    echo "<li>";
                        echo "<a href='$p_lien?page=$p_suiv' aria-label='Next'>";
                        echo "<span aria-hidden='false'>&raquo;</span>";
                        echo "</a>";
                    echo "</li>";
                echo "</ul>";
            echo "</nav>";
            echo "</center>";

        }
  }      
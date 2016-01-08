<?php

    /**
     * Pagination with bootstrap display
     * The sql query that returns the item must be in its where clause : LIMIT ' . (($cPage-1)*$parpage) . ', ' . $parpage;
     * $cPage : user page request
     * $parpage : items per page to display
     *
     * @package COMMON
     * @author Ludovic <ludovicinaction@yahoo.fr>
     * @uses new Pagination($nbTotArt, $nbrParPage, 'blog_page.php');
     * <code>
     *   $oPagination = new Pagination($nbTotArt, $nbrParPage, 'blog.php');
     *   $oPagination->DisplayPagination(); 
     * </code>
     * @param int nbTotArt total number of items to display (returned by the query)
     * @param int $nbrParPage Number of elements that we want to see displayed on each page
     * @param string $pagination Name of the php file to paginate.
     *
     * @todo : nbrParPage par gérer en backoffice.
     */
    class Pagination
    {
        
        public function DisplayPagination($nbTotArt, $nbrParPage, $pagination, $crit=''){
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
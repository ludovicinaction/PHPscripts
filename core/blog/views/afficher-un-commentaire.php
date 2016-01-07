<?php
/**
 * Affichage HTML d'un commentaire
 * @package BLOG
 * @category Vue du module "Blog"
 */

echo '<br /><br /><br /><br /><br />';
if ($t == 'com') echo htmlentities($aComm['texte_com']);
else echo htmlentities($aComm['texte_rep']);
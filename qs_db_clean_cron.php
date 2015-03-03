<?php	    		


/* ***************************************************************************************
Ce module est utilisé effectuer en background les opérations sur la BdD telles que :
	- nettoyage de la BdD
	- envoi de mails
	- vérification des dates sur les invitations
	- ...
*************************************************************************************** */


function qs_xxx() {

	/* On regarde qui est connecté */
	/* On regarde s'il existe des invitations créées par l'utilisateur connecté */
	global $current_user;
   	global $wpdb;
   	global $user_ID; 
    get_currentuserinfo(); /* L'identifiant de la personne connectée est $user_ID, son login : $user_login */
    
    /* On regarde si l'utilisateur connecté a créé des invitations, si oui nombre_invitations > 0 */
    $chaine_requete = 'SELECT *  FROM wp_qspread_invitation WHERE id_createur = "'.$user_ID.'"';
	$result_inv = $wpdb->get_results($chaine_requete);
	$nombre_invitations = $wpdb->num_rows;
	
	return($nombre_invitations);
	
}


?>
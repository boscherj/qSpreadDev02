<?php
/*
Template Name: QSpreadMailReceiptBDDShortCode
*/

	$path = TEMPLATEPATH ;
	$path .= "/qs_contantes.php";
	require_once($path); 

   	 global $wpdb;

	/* On met le statut à 3 dans la table des invités */
	$data_array = array('statut' => STATUT_CREATION_INVITATION_REPONSE_RECUE);
	$where_array = array(
		'email' => $_GET['email'],
		'invitation' => $_GET['invitation'],
	);

	$wpdb->update( 'wp_qspread_invite', 
					$data_array,
					$where_array
			); 
	
	/* On met à jour la réponse à la question binaire */
	$data_array = array('reponse_question_binaire' => $_GET['qs_binaire']);
	$wpdb->update( 'wp_qspread_invite', 
					$data_array,
					$where_array
			); 

	header("Location: http://qpread.com/qs_invitation_thx/"); 
?>

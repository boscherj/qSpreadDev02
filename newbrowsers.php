<?php

/*
 * Example PHP implementation used for the index.html example
 */

// Les données de la Table (lecture) sont transmises avec un GET
// Les données de l'éditeur sont transmises avec un POST
// Il faut nécécessairement qu'il y ait l'un ou l'autre

$qsuserid=0 ;
if ( isset($_GET['qsuserid']) ) $qsuserid=$_GET['qsuserid']; 
if ( isset($_POST['data']['parrain']) ) $qsuserid=$_POST['data']['parrain'] ; 
      
// DataTables PHP library
include( "lib/DataTables.php" );

// Alias Editor classes so they are easy to use
use
	DataTables\Editor,
	DataTables\Editor\Field,
	DataTables\Editor\Format,
	DataTables\Editor\Join,
	DataTables\Editor\Validate;


// Build our Editor instance and process the data coming from _POST
// Cf.  https://editor.datatables.net/tutorials/php_join
	
Editor::inst( $db, 'wp_qspread_invite' ) // Nom de la BdD et celui de la table
	->fields(		
		
		Field::inst( 'wp_qspread_invite.email')
			->validator( 'Validate::email_required', array(
 					 "message" => "Merci de saisir une adresse mail valide"
			)),
				
		Field::inst( 'wp_qspread_invite.invitation' )->validator( 'Validate::numeric_required' ),
		Field::inst( 'wp_qspread_invite.statut' ), // JB1
		Field::inst( 'wp_qspread_invite.parrain' ),		
		Field::inst( 'wp_qspread_invite.nom_invite' )->validator( 'Validate::required', array(
 					 "message" => "Merci de saisir un nom"
		) ),		

		Field::inst( 'wp_qspread_invite.date_invitation' ),
		// A VOIR https://editor.datatables.net/manual/php/formatters
		// ->getFormatter( 'Format::date_sql_to_format', Format::DATE_ISO_822 )
		// ->getFormatter( 'Format::date_format_to_sql', Format::DATE_ISO_822 ),
		
		Field::inst( 'wp_qspread_invite.reponse_question_binaire'),
		Field::inst( 'wp_qspread_invite.donnees_service_encours'),
		Field::inst( 'wp_qspread_invite.service'),
		
		Field::inst( 'wp_qspread_invite.invitation_transmise'),
		
		
		Field::inst( 'wp_qspread_invitation.nom_invitation' ),
		Field::inst( 'wp_qspread_statut.type_statut' ),
		
		/* Remplacement dans le tableau de bord de l'affichage du statut par celui de la réponse à la question */
		Field::inst( 'wp_qspread_reponse.txt_reponse' )
			
		
				
	)
	->where( "wp_qspread_invite.parrain", $qsuserid, '=') 
	->where( "wp_qspread_invite.invitation", 0, '!=') 
	
	->leftJoin( 'wp_qspread_invitation', 'wp_qspread_invitation.ID', '=', 'wp_qspread_invite.invitation' )
	
	->leftJoin( 'wp_qspread_statut', 'wp_qspread_statut.id', '=', 'wp_qspread_invite.statut' )
	->leftJoin( 'wp_qspread_reponse', 'wp_qspread_invite.reponse_question_binaire', '=', 'wp_qspread_reponse.code_reponse' )
	 
	->process( $_POST )
	// https://editor.datatables.net/docs/Editor-1.3.3/php/source-class-DataTables.Editor.html#147
	 // ->data()
	->json();

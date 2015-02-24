<?php
/*
Template Name: QSpreadHybridContactsDataTable
*/
?>
<?php


/* On regarde si l'utilisateur a le droit de visiter cette page */
	    $nbint = qs_check_user_manage_invitation() ;
	    // header( "Location: http://qpread.com/qs_warning/");
	    if ($nbint == 0) {
	    		header( "Location: http://qpread.com/qs_warning/");
	    }

$path = TEMPLATEPATH ;
$path .= "/qs_contantes.php";
require_once($path); 

session_start();



/* ***************************************************************************************

Function : qs_check_user_manage_invitation
Vérifie si l'utilisateur a soit créé une invitation soit a été invité à une invitation ouverte
c'est à dire qui lui permet d'inviter et de consulter pour cette invitation

**************************************************************************************** */
function qs_check_user_manage_invitation() {

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



/* ***************************************************************************************

Function : qs_liste_invitations
Recherche n'ID du fondateur de l'invitation de l'utilisateur connecté

**************************************************************************************** */

function qs_liste_invitations(&$les_invit) {



/* Pour inviter, il faut être soit le créateur de l'invitation, soit être un invité d'une invitation qui permet d'inviter 
   On recherche dans quelle situation se trouve l'utilisateur
   Cette page est réservée aux utilisateurs connectés - il faut avoir un compte pour inviter et il faut être connecté */


	  global $current_user;
   	  global $wpdb;
   	  global $user_ID; 
      get_currentuserinfo(); /* L'identifiant de la personne connectée est $user_ID, son login : $user_login */


/* L'utilisateur connecté peut-être le créateur des invitations */
/* On mémorise dans $les_invit les invitations */
/* On vérifie d'abord si c'est un créateur d'invitations */

$chaine_requete = 'SELECT *  FROM wp_qspread_invitation WHERE id_createur = "'.$user_ID.'"';

$result_inv = $wpdb->get_results($chaine_requete);

$nombre_invitations = $wpdb->num_rows;

for ($i = 0; $i < $wpdb->num_rows; $i++) {
    $les_invit[$i] = $result_inv[$i];

}



/* Ensuite on regarde si l'utilisateur connecté a été invité - on connait son $user_email */
$chaine_requete = 'SELECT *  FROM wp_qspread_invite WHERE invitation >0 AND email = "'.$current_user->user_email.'"';

$result_inv = $wpdb->get_results($chaine_requete);
$nb_fois_invites = $wpdb->num_rows;



for ($i = 0; $i < $nb_fois_invites; $i++) {
	
	$chaine_requete = 'SELECT *  FROM wp_qspread_invitation WHERE ID = "'.$result_inv[$i]->invitation.'"';	

	$aresult_inv = $wpdb->get_results($chaine_requete); 
	$les_invit[$i+$nombre_invitations] = $aresult_inv[0];
}

$nombre_invitations = $nombre_invitations + $nb_fois_invites ;

return($nombre_invitations);

}


/* ***************************************************************************************

Function : qs_affiche_tableau_datatable
Affiche le tableau de données

**************************************************************************************** */
function qs_affiche_tableau_datatable() {
	
   	  global $wpdb;
   	  global $user_ID; 
   	  global $user_login; 

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>qSpread Ajout des invités</title>
		
			<!-- includes for Editor operation JBS le 17 septembre 2014-->
		<style class="include" type="text/css">
			@import "//cdn.datatables.net/1.10.2/css/jquery.dataTables.css";
			@import "//cdn.datatables.net/tabletools/2.2.3/css/dataTables.tableTools.css";
			@import "http://qpread.com/DataTables-1.10.2/extensions/Editor-1.3.3/css/dataTables.editor.min.css";	
			@import "//cdn.datatables.net/responsive/1.0.1/css/dataTables.responsive.css";	
			@import "https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" ;
			@import "http://qpread.com/wp-content/plugins/my-custom-css/my_style.css" ;		
		</style>
	
	
		<!--  Juste pour le test -->
		<style type="text/css"> 
   			.monselected{ 
      			font-size:12px; 
      			color:red; 
   			} 
   			
   			.selected{ 
      			font-size:12px; 
      			color:green; 
   			} 
  	 	</style> 
  	 	<!--  Fin de Juste pour le test -->

	
		<script class="include" type="text/javascript" charset="utf-8" src="//code.jquery.com/jquery-1.11.1.min.js"></script>

		<script class="include" type="text/javascript" charset="utf-8" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
		<script class="include" type="text/javascript" charset="utf-8" src="//cdn.datatables.net/tabletools/2.2.3/js/dataTables.tableTools.min.js"></script>
		<script class="include" type="text/javascript" charset="utf-8" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
		<script class="include" type="text/javascript" charset="utf-8" src="http://qpread.com/DataTables-1.10.2/extensions/Editor-1.3.3/js/dataTables.editor.min.js"></script>
		<script class="include" type="text/javascript" charset="utf-8" src="//cdn.datatables.net/responsive/1.0.1/js/dataTables.responsive.js"></script>



			<script type="text/javascript" charset="utf-8" id="init-code">
			var editor; <!-- use a global for the submit and return data rendering in the examples -->
			
			<!-- Pour montrer comment passer une variable PHP au Javavscript -->
			<?php
	    			$php_var_date_invitation = current_time('mysql');
	    			
			?>
			<!-- Pour passer une variable PHP au Javavscript -->
			var js_date_invitation = "<?php echo $php_var_date_invitation; ?>";						
			var js_parrain = "<?php echo $user_ID; ?>";
			
			// ****************************************************************************************************************  
			// Function getXMLHttpRequest
			//
			//
			// **************************************************************************************************************** 
			
			function getXMLHttpRequest() {
			var xhr = null;
	
			if (window.XMLHttpRequest || window.ActiveXObject) {
				if (window.ActiveXObject) {
					try {
						xhr = new ActiveXObject("Msxml2.XMLHTTP");
					} catch(e) {
						xhr = new ActiveXObject("Microsoft.XMLHTTP");
					}
				} else {
					xhr = new XMLHttpRequest(); 
				}
			} else {
				alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
				return null;
			}
	
			return xhr;
			}
			// **************************************************************************************************************** 
			
			<?php 
				$nombre_invitations = qs_liste_invitations($les_invit);
			?>
			qs_invitation_en_cours = <?php echo $les_invit[0]->ID ?> ;	
			
			
			
			$(window).on("beforeunload", function() {     				
    				var qs_myURL = 'http://qpread.com/qs_lib_record_update?qs_action_number=<?php echo MAJ_BDD_INVITE ; ?>' ;   						
  					var xhr = getXMLHttpRequest();
  					xhr.open("GET", qs_myURL , false);
					xhr.send(null); 						
					
					$.get("http://qpread.com/qsmailenvoiservice?provider="+"<?php echo $_GET["provider"] ?>");	
			}) ;
			
 								
			$(document).ready(function() {	
				
				// L'éditeur n'est pas utile ici			
				
				// var MyTable = $('#qspread_service_mail').dataTable( {  
				   var MyTable = $('#qspread_service_mail').DataTable( {  
				   	
				   	// http://datatables.net/reference/option/rowCallback
				   	"rowCallback": function( row, data ) {
    						// console.log(row) ;
    						// console.log(data) ;
    						// console.log(data['email']) ;
    						
    						// if ( data['email'] == "jboscher@yahoo.com" ) {
      						//		$('td', row).addClass( "selected" );
      						//		
    						// }
    						
    						if ( data['statut'] >= "100" ) {
      								$('td', row).addClass( "selected" );
      								
    						}

    						
  					},
				   	
				   	// "processing": true,
				   	// "serverSide": true,
										
					// Les traductions du tableau 
					"oLanguage": { // http://datatables.net/usage/i18n -->
      						"sSearch": "Recherche :",
      						"sPrevious": "Previous page",
      						"sInfo": " _TOTAL_ enregistrement(s) (_START_ à _END_)",
      						"sZeroRecords": "Aucun enregistrement",
      						"oPaginate": {
        						"sNext": "Suivant",
        						"sPrevious": "Précédent"
     						 },
     						 "sEmptyTable": "Cliquez sur Ajout pour Ajouter vos données",
      						"sInfoEmpty": "Aucune donnée dans la table",

      						
    				}, //Fin de oLanguage 
    
					"sDom": "Tfrtip", // sDom initialisation parameter https://datatables.net/examples/basic_init/dom.html -->
					"sAjaxSource": "http://qpread.com/wp-content/themes/oneup/php/qs_add_user_service.php", // construct a table from an AJAX source https://datatables.net/examples/data_sources/ajax.html -->
					
					
					// JBS le 7/4/2014 Pour le Where
					// JBS On fait passer des données supplémentaires dans le GET
					// http://datatables.net/release-datatables/examples/server_side/custom_vars.html
					"fnServerParams": function ( aoData ) {
                          aoData.push( { "name": "qsuserid", "value": "<?php echo $user_ID; ?>" } );
                        }, 
                    

					
					// C'est normallement redondant avec fields (plus haut) mais si je l'enlève ça ne marche pas 
					"aoColumns": [
						{ "mData": "wp_qspread_invite.email" },  // The data property (mData in DataTables terminology) that is used to read from and write to the table https://editor.datatables.net/docs/current/Editor.models.field.html#dataProp
						{ "mData": "wp_qspread_invitation.nom_invitation" },
						// { "mData": "statut" },
						// { "mData": "parrain" },
					    { "mData": "wp_qspread_invite.nom_invite" }
						// { "mData": "date_invitation" }
						// { "mData": "reponse_question_binaire" },
					], <!-- Fin de aoColumns -->
					
					
					"oTableTools": { //  https://datatables.net/extras/tabletools/buttons -->
						"sRowSelect": "multi",
						"aButtons": [							
							// ------------------------ INVITER 
							{ "sExtends": "text", "sButtonText": "Inviter",														
									"fnClick": function ( nButton, oConfig, oFlash ) {											
											var oTT = TableTools.fnGetInstance( 'qspread_service_mail' );
						  					// console.log(oTT);						  
    					   					var aData = oTT.fnGetSelectedData();
    					   					// console.log(aData);

											 for(i=0 ; i<aData.length ; i++) {
  													// console.log(aData[i]['DT_RowId']);
  													str=aData[i]['DT_RowId'];
  													longueur=str.length;
  													// console.log(str);
  													// console.log(longueur)  									
  													qs_record_number = str.substr(4,longueur-4) ;
  													// console.log(qs_record_number);
   									
  													var qs_myURL = 'http://qpread.com/qs_lib_record_update?qs_record_number='  + qs_record_number + '&qs_action_number=<?php echo STATUT_INVITER ; ?>' + '&qs_invitation_en_cours=' + qs_invitation_en_cours ;
  									
  													var xhr = getXMLHttpRequest();
  													//xhr.onreadystatechange = function() {
    												//	if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
									    			//	}
													// };
													
													// http://www.w3schools.com/ajax/ajax_xmlhttprequest_send.asp
													//  false (synchronous)
  													xhr.open("GET", qs_myURL , false);
													xhr.send(null);  									
							 				}
							
											MyTable.ajax.reload(null, false	) ;											 
                    					}
                    				//  Fin fnClick                    				
							 }, //  Fin sExtends": "text", "sButtonText": "Inviter
							 
							 // ------------------------ NE PAS INVITER 
							 { "sExtends": "text", "sButtonText": "Ne plus Inviter",														
									"fnClick": function ( nButton, oConfig, oFlash ) {											
											var oTT = TableTools.fnGetInstance( 'qspread_service_mail' );
						  					// console.log(oTT);						  
    					   					var aData = oTT.fnGetSelectedData();

											 for(i=0 ; i<aData.length ; i++) {
  													// console.log(aData[i]['DT_RowId']);
  													str=aData[i]['DT_RowId'];
  													longueur=str.length;
  													qs_record_number = str.substr(4,longueur-4) ;
   													
   													var qs_myURL = 'http://qpread.com/qs_lib_record_update?qs_record_number='  + qs_record_number + '&qs_action_number=<?php echo STATUT_NE_PLUS_INVITER ; ?>' + '&qs_invitation_en_cours=' + qs_invitation_en_cours ;
   																						
  													var xhr = getXMLHttpRequest();
  													xhr.open("GET", qs_myURL , false);
													xhr.send(null);  									
							 				}
							
											MyTable.ajax.reload(null, false	) ;											 
                    					}
                    				//  Fin fnClick                    				
							 }, //  Fin sExtends": "text", "sButtonText": "Ne plus Inviter
							 
							 
							 
							 
							// { "sExtends": "editor_remove", "editor": editor, "sButtonText": "Supprime"},
							{ "sExtends": "select_all", "editor": editor, "sButtonText": "Tous" },
							{ "sExtends": "select_none", "editor": editor, "sButtonText": "Aucun" },
														
							{
								
                    					sExtends: 'collection',
                						sButtonText: "<?php echo $les_invit[0]->nom_invitation ?>",
										
										fnSelect : function ( nButton, oConfig, oFlash ) {											
						  							// console.log(nButton);
						  							// console.log(oConfig);
						  							
                    					},     
                						
                  						aButtons: [
                						
                						<?php 
											/* $nombre_invitations = qs_liste_invitations($les_invit); */
											for ($i = 0; $i < $nombre_invitations; $i++) { 
											?> 
											{
                       						 	sExtends: 'text',
                       						 	sButtonText: "<?php echo $les_invit[$i]->nom_invitation ?>",
                       						 	
                       						 	// When this option is set to true, the data gathered from the table will be 
                       						 	// from only the rows which are selected by the end user
                       						 	// http://datatables.net/extras/tabletools/button_options#bSelectedOnly
                        					 	bSelectedOnly: true,
                        					 	                   						
                   								fnClick: function ( nButton, oConfig, oFlash ) {											
						  							// console.log(nButton);
						  							// console.log(oConfig);
						  							
						  							qs_button_text = oConfig['sButtonText']; 
						  							// console.log(qs_button_text);
						  							
						  							$('#ToolTables_qspread_service_mail_4').html(qs_button_text);
						  							
						  							qs_invitation_en_cours = <?php echo $les_invit[$i]->ID ?> ;
						  							// console.log(qs_button_text);
                    							}     
                    					
                    						}
                    						<?php if ($i< ($nombre_invitations-1)) echo "," ?>											
										<?php } ?>

                   						]               				                 				                    			
               				 } /* ,
               				 Je ne sais pas encore si on doit garder le bouton Menu car les comportements sont 
               				 différents selon les navigateurs.
               				 La fermeture de la page n'est pas traitée par Safari sur Mac comme étant un événements 
               				 beforeunload et donc de ce fait les actions prévues ne sont pas lancées
               				  
               				 { "sExtends": "text", "sButtonText": "Menu",	editor: editor, 						
									"fnClick": function ( nButton, oConfig, oFlash ) {									
										document.location.href="http://qpread.com";
									}
							}
							*/
                               				 
						]
					} 					 
				} ); 

				

				// Juste pour le test
				$('#qspread_service_mail tbody').on( 'click', 'tr', function () {
						 $(this).toggleClass('monselected') ;	
						 
						  // ça marche
						  var oTT = TableTools.fnGetInstance( 'qspread_service_mail' );
						  // console.log(oTT);
						  
    					   var indexes = oTT.fnGetSelectedIndexes();
    					   // console.log(indexes);
    					   
    					   var aSelectedTrs = oTT.fnGetSelected();
    					   // console.log(aSelectedTrs);
    					   
    					   var aData = oTT.fnGetSelectedData();
    					   // console.log(aData);
    					   

    			} );     			
    			//  Fin de Juste pour le test
    			
    			
								
			} ); 
  				
		</script>

	</head>
	
	
	<body id="dt_example">
	<div class="section group qs_bkg_plus">
		
		<div id="container"> <!-- Cet id a de l'importance -->
		
			<div class="full_width big">Liste des invités de : <?php echo $user_login; ?></div>
			
			
				<div id="demo"> <!-- Cet id ne semble pas avoir d'importance -->
									
					<table id="qspread_service_mail" class="display responsive nowrap" cellspacing="0" width="80%">
							<thead>
								<tr>
									<th>email</th>
									<th>invitation</th>
									<!-- <th>statut</th> -->
									<!-- <th>parrain</th> -->
									<th>nom_invite</th>
									<!-- <th>date_invitation</th> -->
									<!-- <th>reponse_question_binaire</th> -->
								</tr>
							</thead>
					</table>
				</div> <!-- Demo -->
			</div> <!-- container -->	
	</div> <!-- section group-->
	
	</body>

</html>

<?php
}

/* ***************************************************************************************

Function : qs_affiche_formulaire_contacts
Affiche le formulaire

**************************************************************************************** */

function qs_affiche_formulaire_contacts() {
	
	  global $current_user;
   	  global $wpdb;
   	  global $user_ID; 
   	  global $user_login; 
   	  
    get_currentuserinfo(); /* L'identifiant de la personne connectée est $user_ID, son login : $user_login */

	$provider = @ trim( strip_tags( $_GET["provider"] ) );
	

	/* Récupération des contacts */
	$liste_contacts = qs_get_contacts_hybrid() ; 
	$php_var_date_invitation = current_time('mysql');
	
	/* Suppression des contacts sans invitation dans la BDD */
	$chaine_requete = 'DELETE FROM wp_qspread_invite WHERE invitation = 0 AND parrain = "'.$user_ID.'"';
	$result_inv = $wpdb->get_results($chaine_requete);

	/* Suppression des données en cours d'étude */
	$chaine_requete = 'DELETE FROM wp_qspread_invite WHERE donnees_service_encours	 = '. DONNEES_SERVICE_ENCOURS_EN_LECTURE. 'AND parrain = "'.$user_ID.'"';
	$result_inv = $wpdb->get_results($chaine_requete);
	
	
	if( ! count( $liste_contacts ) ){
		/* echo "Pas de contacts - diriger vers une autre page"; */
	}
	else { /* Il y a des contacts */

		/* Sauvegarde des contacts dans la BDD */	
		foreach( $liste_contacts as $item ){
   	 	
   	 	$wpdb->insert(wp_qspread_invite, array( 
						'email'  => $item["email"],
						'invitation'  => 0, 
						'statut'  => STATUT_TEMPORAIRE, 
						'parrain'  => $user_ID,
						'nom_invite'  => $item["name"],
						'date_invitation'  => $php_var_date_invitation,
						'reponse_question_binaire'  => 0,
						'donnees_service_encours' => DONNEES_SERVICE_ENCOURS_EN_LECTURE,
						'service_user_id'  => $item["id"],
						'service_profileURL'  => $item["profileURL"],
						'service'  => $provider
																
					) 
		); 
		}
	
	qs_affiche_tableau_datatable() ; 
	
	} /* Fin de else Il y a des contacts */


} 


/* ***************************************************************************************

Function : qs_get_contacts_hybrid
Récupère les contacts chez le provider

**************************************************************************************** */

function qs_get_contacts_hybrid() {

	// config and whatnot
	// dirname — Renvoie le nom du dossier parent
	// To get the directory of current included file: dirname(__FILE__); 
	
		
    $config = dirname(__FILE__) . '/hybridauth/config.php';
    require_once( "hybridauth/Hybrid/Auth.php" );


	// initialise hybridauth
	$hybridauth = new Hybrid_Auth( $config );
	
	// selected provider name 
	$provider = @ trim( strip_tags( $_GET["provider"] ) );
	
	
	// check if the user is currently connected to the selected provider
	if( !  $hybridauth->isConnectedWith( $provider ) ){ 
		// redirect him back to login page
			
		// header( "Location: qscontactshybrid?provider=".$provider);
		// header( "Location: http://qpread.com/wp-content/themes/oneup/test_contacts_hybrid_wp.php?provider=".$provider);
		
		// ATTENTION J'AI CHANGE CETTE LIGNE PAR LA SUIVANTE
		// header( "Location: http://qpread.com/qscontactshybrid?provider=".$provider);
		// header( "Location: http://qpread.com/qsloginhybrid?provider=".$provider);
		// try to authenticate the selected $provider
		
		// MODIFICATION IMPORTANTE ICI LE 14/1/2015
		
			$adapter = $hybridauth->authenticate( $provider );		
			if( !  $hybridauth->isConnectedWith( $provider ) ){ 	
				// n'arrive pas à se connecter 
				$adapter->logout();
				$hybridauth->redirect( "http://qpread.com/qscontactshybrid?provider=$provider" );
			}
	} 
	
	else { 
		 // echo "Je suis connecté";
		 // exit(0);
		
	}

	try{
		// call back the requested provider adapter instance 
		$adapter = $hybridauth->getAdapter( $provider );	
		
		$qs_token = $adapter->getAccessToken() ;
				
		$_SESSION['qs_access_token'] = $qs_token["access_token"] ;
		$_SESSION['qs_access_token_secret'] = $qs_token["access_token_secret"] ;
		
				
		// grab the user contacts list
		// http://hybridauth.sourceforge.net/userguide/Profile_Data_User_Contacts.html

		// identifier	 String	The Unique contact's ID on the connected provider. Usually an interger.
		// profileURL	 String	URL link to profile page on the IDp web site
		// webSiteURL	 String	User website, blog, web page,
		// photoURL	 String	URL link to user photo or avatar
		// displayName	 String	 User dispalyName provided by the IDp or a concatenation of first and last name.
		// description	 String	A short about_me or the last contact status
		// email	 String	User email. Not all of IDp garant access to the user email
		
		// Ici il faut un test sur Facebook

		$user_contacts = $adapter->getUserContacts();
		
		
			$return = array() ;					
			foreach( $user_contacts as $item ){
								
				$return[] = array(
				'id' => $item->identifier,
				'name' => $item->displayName,
				'profileURL' => $item->profileURL,
				'email' => $item->email
				) ;
			} 


			if( ! count( $user_contacts ) ){
				// echo "No contact found!";
				$adapter->logout(); 
				// header( "Location: http://qpread.com/wp-content/themes/oneup/test_login_hybrid_wp.php?provider=".$provider);
				// MODIFICATION LE 15/1/2015
				// header( "Location: http://qpread.com/qsloginhybrid?provider=".$provider);
				
				
				if ($provider == "Facebook")  {	
					echo "Vous êtes connecté(e) à Facebook </br>";
					echo "Invitez vos amis en cliquant sur le bouton ci-dessous </br>";
					echo "Laissez leu un message au sujet de votre invitation </br>";
					
					
					$nombre_invitations = qs_liste_invitations($les_invits);                     
                     ?>
  		
  					<script>
  					var qs_fb_invitation = "" ;
  					
  					function testtest(str) {
  							alert(str) ;
  							qs_fb_invitation = str ;
  						}	
  					</script>
  					
  					<?php if ($nombre_invitations>1) { ?>
				
                    <form>
						<select name="invitation"  onchange="testtest(this.value)">
								<option value="">Selectionner l'invitation :</option>
								<?php for ($i = 0; $i < $nombre_invitations; $i++) {
									?><option value="<?php echo $les_invits[$i]->ID ?>"><?echo $les_invits[$i]->nom_invitation?></option>
 								<? } ?>
						</select>
					</form>					
					<?php } else ?> <script> qs_fb_invitation = <?php echo $les_invits[0]->ID ; ?></script>
					
					<?php
				} 
				else {		
					echo "Pas de contacts - diriger vers une autre page";
				}
				
				?>
				<script>
     			 	window.fbAsyncInit = function() {
        				FB.init({
          					appId      : '318304868377713',
          					xfbml      : true,
          					version    : 'v2.1'
        				});
      				};

      				(function(d, s, id){
         				var js, fjs = d.getElementsByTagName(s)[0];
         				if (d.getElementById(id)) {return;}
         					js = d.createElement(s); js.id = id;
         					js.src = "//connect.facebook.net/en_US/sdk.js";
         					fjs.parentNode.insertBefore(js, fjs);
       				}(document, 'script', 'facebook-jssdk'));
       				
       				function share_link() { 
						FB.ui({
							method: 'send',
							name: 'qSpread',
							link: 'http://qpread.com/qsbddfbreceipt?invitation='+qs_fb_invitation+'&fb=true',
						});
					}
					
    			</script>
    			<hr />
				<input value="Click on me to share this page on Facebook" style="height:30px;" type="submit" onclick="share_link()" /><br /> 
<hr /> 
									
    			<?php
    
			}
			
	} // Fin du Try
		
	catch( Exception $e ){
		
		// if code 8 => Provider does not support this feature
		if( $e->getCode() == 8 ){
			echo "Provider does not support this feature";
		}
		else{
			// On retente
			$adapter->logout(); 
			header( "Location: http://qpread.com/qsloginhybrid?provider=".$provider);
		} 
	} // Fin du catch 
	
	return($return) ;
} // Fin de la fonction qs_get_contacts_hybrid() {


qs_affiche_formulaire_contacts(); 
?>
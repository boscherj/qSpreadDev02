<?php
/*
Template Name: QSpreaddatatableemail
*/
?>
<?php	    		


/* ***************************************************************************************
Ce module est utilisé pour présenter le Tableau de Bord et aussi pour ajouter un invité
dont on connait l'adresse email.

La page associée est qsinvitebymail
*************************************************************************************** */

	
	    			/* On regarde si l'utilisateur a le droit de visiter cette page */
	    			$nbint = qs_check_user_manage_invitation() ;
	    			// header( "Location: http://qpread.com/qs_warning/");
	    			if ($nbint == 0) {
	    				header( "Location: http://qpread.com/qs_warning/");
	    			}
?>

<?php get_header(); ?>

<?php
/* session_unset(); */

$path = TEMPLATEPATH ;
$path .= "/qs_contantes.php";
require_once($path); 


session_start();
	  global $current_user;
	  
   	  global $wpdb;
   	  global $user_ID;
      get_currentuserinfo(); /* L'identifiant de la personne connectée est $user_ID, son login : $user_login */

?>
<?php 

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

/* A CORRIGER si on autorise les utilisateurs à s'inviter eux-mêmes alors il faut modifier cette fonction */

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
/* Modif JBS le 9/10/2014 */
$chaine_requete = 'SELECT *  FROM wp_qspread_invitation WHERE id_createur = "'.$user_ID.'"';
$result_inv = $wpdb->get_results($chaine_requete);

$nombre_invitations = $wpdb->num_rows;


for ($i = 0; $i < $wpdb->num_rows; $i++) {
    $les_invit[$i] = $result_inv[$i];

}


/* Ensuite on regarde si l'utilisateur connecté a été invité - on connait son $user_email */
$chaine_requete = 'SELECT *  FROM wp_qspread_invite	WHERE email = "'.$current_user->user_email.'"';

$result_inv = $wpdb->get_results($chaine_requete);
$nb_fois_invites = $wpdb->num_rows;



for ($i = 0; $i < $nb_fois_invites; $i++) {
	
	$chaine_requete = 'SELECT *  FROM wp_qspread_invitation	WHERE ID = "'.$result_inv[$i]->invitation.'"';	

	$aresult_inv = $wpdb->get_results($chaine_requete); 
	$les_invit[$i+$nombre_invitations] = $aresult_inv[0];
}

$nombre_invitations = $nombre_invitations + $nb_fois_invites ;

return($nombre_invitations);

}

/* ***************************************************************************************

Fin de la Function : qs_liste_invitations

**************************************************************************************** */
?>
			
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>qSpread DataTable</title>
		
		<style class="include" type="text/css">
			@import "//cdn.datatables.net/1.10.2/css/jquery.dataTables.css";
			@import "//cdn.datatables.net/tabletools/2.2.3/css/dataTables.tableTools.css";
			@import "http://qpread.com/DataTables-1.10.2/extensions/Editor-1.3.3/css/dataTables.editor.min.css";
			@import "//cdn.datatables.net/responsive/1.0.1/css/dataTables.responsive.css";
			// @import "https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" ;
		</style>
		
		
		<script class="include" type="text/javascript" charset="utf-8" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
		<script class="include" type="text/javascript" charset="utf-8" src="//cdn.datatables.net/tabletools/2.2.3/js/dataTables.tableTools.min.js"></script>
		<script class="include" type="text/javascript" charset="utf-8" src="http://qpread.com/DataTables-1.10.2/extensions/Editor-1.3.3/js/dataTables.editor.min.js"></script>
		<script class="include" type="text/javascript" charset="utf-8" src="//cdn.datatables.net/responsive/1.0.1/js/dataTables.responsive.js"></script>

		
		
		<script type="text/javascript" charset="utf-8" id="init-code">
		
		var editor; 
		
			<?php
	    			$php_var_date_invitation = current_time('mysql');	    			
			?>
			
			// Ajout le 10/2/2015
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
			



			var js_date_invitation = "<?php echo $php_var_date_invitation; ?>";	
			// var qs_d1 = new Date();
			// var qs_n1 = qs_d1.getTime();
					
			var js_parrain = "<?php echo $user_ID; ?>";
			var js_donnees_service_encours = "<?php echo DONNEES_SERVICE_ENCOURS_TRAITEES ; ?>";
			var qs_invitation_transmise ;
 
 
 	// Ajout le 10/2/2015
 	// Pb ça ne marche pas avec Safari Mac lors d'un changement de page
 	// OK seulement lorsque je ferme la page
 	$(window).on("beforeunload", function() {     				
    				var qs_myURL = 'http://qpread.com/qs_lib_record_update?qs_action_number=999' ;   						
  					var xhr = getXMLHttpRequest();
  					xhr.open("GET", qs_myURL , false);
					xhr.send(null); 						
					
					$.get("http://qpread.com/qsmailenvoiservice?provider="+"<?php echo $_GET["provider"] ?>");	
	}) ;
	/*
	window.onbeforeunload = function (e) {
    var qs_myURL = 'http://qpread.com/qs_lib_record_update?qs_action_number=999' ;   						
  					var xhr = getXMLHttpRequest();
  					xhr.open("GET", qs_myURL , false);
					xhr.send(null); 						
					
					$.get("http://qpread.com/qsmailenvoiservice?provider="+"<?php echo $_GET["provider"] ?>");	
	};
	*/
 
	$(document).ready(function() {
    	editor = new $.fn.dataTable.Editor( {
        	ajax: "http://qpread.com/wp-content/themes/oneup/php/newbrowsers.php",
        	table: "#qspread_invite",
        	
        	i18n: { 
        					"create": {
            				"title":  "Créer une nouvelle entrée",
            				"submit": "Ajout"
        					},
        					"remove": {
            				"title":  "Suppression de l'enregistrement",
            				"submit": "Suppression",
            				"confirm": { 
        								"_": "Etes-vous sûr de vouloir détruire ces enregistrements ?",
        								"1": "Etes-vous sûr de vouloir détruire cet enregistrement ?",
  										  },
        					},
        					"edit": {
            				"title":  "Edition de l'enregistrement",
            				"submit": "Envoi"
        					},
        	},


        	fields: [ {
                label: "<b>EMAIL :</b>",
                name: "wp_qspread_invite.email"
            }, {
                label: "INVITATION :",
                name: "wp_qspread_invite.invitation",
                type: "select",							
				ipOpts: [
								<?php 
									$nombre_invitations = qs_liste_invitations($les_invit);
									for ($i = 0; $i < $nombre_invitations; $i++) { 
								?> 
									{ "label": "<?php echo $les_invit[$i]->nom_invitation ?>", "value": "<?php echo $les_invit[$i]->ID ?>" }, 
								<?php } ?>
   				]
            }, {
                label: "NOM INVITE :",
                name: "wp_qspread_invite.nom_invite"
            },{
                label: "<b>STATUT :</b>",
                name: "wp_qspread_invite.statut",
                type: "hidden",
                def: <?php echo STATUT_INVITER ?>
            },{
                label: "<b>PARRAIN :</b>",
                name: "wp_qspread_invite.parrain",
                type: "hidden",
                def: js_parrain
            },{
                label: "<b>DATE INVITATION :</b>",
                name: "wp_qspread_invite.date_invitation",
                type: "hidden",
                def: js_date_invitation
            },{
                label: "<b>REPONSE QUESTION BINAIRE :</b>",
                name: "wp_qspread_invite.reponse_question_binaire",
                type: "hidden",
                def: <?php echo QUESTION_BINAIRE_PAS_DE_REPONSE ?>
            },{
                label: "<b>DONNEES SERVICE EN COURS :</b>",
                name: "wp_qspread_invite.donnees_service_encours",
                type: "hidden",
                def: js_donnees_service_encours
            },
            // J'ai ajouté cette ligne le 10/2/2015 afin d'émettre un mail lors de la création
            // Je ne sais pas encore si c'est la bonne solution
            // En même temps, j'ai dû modifier le fichier newbrowsers.php 
            {
                label: "<b>INVITATION TRANSMISE :</b>",
                name: "wp_qspread_invite.invitation_transmise",
                type: "hidden",
                def: qs_invitation_transmise
            },
            {
                label: "<b>SERVICE :</b>",
                name: "wp_qspread_invite.service",
                type: "hidden",
                def: "<?php echo PROVIDER_email ?>"
            }                       
                   
            ],
            
             events: { 
             	onPreSubmit: function ( o ) {
                      		o.data.date_invitation = js_date_invitation ;
                      		// o.data.statut = 4 ; Je pense que la ligne ci-dessous n'est pas prise en compte  
                      		// je la supprime
                      		// voir http://datatables.net/forums/discussion/22028/how-to-pass-values-to-server-onpresubmit
                      		// qs_statut = 4 ; 
                       		o.data.parrain = js_parrain ;
                       		
                       		<?php $php_var_date_invitation = current_time('mysql');	?>
                       		// js_date_invitation = "<?php echo $php_var_date_invitation; ?>";	
                       		// o.data.date_invitation = "<?php echo $php_var_date_invitation; ?>";	
                       		// var qs_d2 = new Date();
                       		// var qs_n2 = qs_d2.getTime();
                       		// console.log( (qs_n2 - qs_n1) / 1000 ) ;  
							// o.data.date_invitation = "<?php echo $php_var_date_invitation; ?>" ;	
							// console.log(o.data.date_invitation) ; 
							
							// var d = new Date(o.data.date_invitation);
							// console.log(o) ; 
                       		
                      		if(o.action=="create"){
                      			// o.data.statut = 3 ; Je pense que la ligne ci-dessous n'est pas prise en compte  
                      			qs_statut = <?php echo STATUT_INVITER ?> ;                 			
                      			o.data.reponse_question_binaire = <?php echo QUESTION_BINAIRE_PAS_DE_REPONSE ?> ;
                      			qs_invitation_transmise = <?php echo INVITATION_TRANSMISE_NON ?> ;
                      			o.data.wp_qspread_invite.invitation_transmise = <?php echo INVITATION_TRANSMISE_NON ?> ;
                      		}
                      		
                      		if(o.action=="edit"){
                      			o.data.wp_qspread_invite.invitation_transmise = <?php echo INVITATION_TRANSMISE_NON ?> ;
                      		}
                      		
						 	return true;
           		} // Fin de onPreSubmit
           		
           	}
           				 
     	
    } );
 
 
    
    $('#qspread_invite').DataTable( {    	
		oLanguage: {       						
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

      						
    	}, 
        dom: "Tfrtip",
        scrollY: 300,
        paging: false,
        responsive: true,
        
        
        ajax: "http://qpread.com/wp-content/themes/oneup/php/newbrowsers.php",    	
    	
    	"fnServerParams": function ( aoData ) {
                          aoData.push( { "name": "qsuserid", "value": "<?php echo $user_ID; ?>" } );
                        }, 
        columns: [
        
        	{ data: "wp_qspread_invite.nom_invite" },
            { data: "wp_qspread_invite.email" },
            { data: "wp_qspread_invitation.nom_invitation" },
            { data: "wp_qspread_reponse.txt_reponse" } ,

            { data: "wp_qspread_invite.date_invitation" }
        ],
        tableTools: {
            sRowSelect: "multi",
            aButtons: [
                { sExtends: "editor_create", editor: editor, "sButtonText": "Ajout" },
                { 
                	sExtends: "editor_edit",   
                	editor: editor, 
                	sButtonText: "Modifie", 
                	// When this option is set to true, the data gathered from the table will be 
                       						 	// from only the rows which are selected by the end user
                       						 	// http://datatables.net/extras/tabletools/button_options#bSelectedOnly
                    bSelectedOnly: true,
                    
                    fnComplete: function ( nButton, oConfig, oFlash, sFlash ) {
                    	var oTT = TableTools.fnGetInstance( 'qspread_invite' );
    				    var aData = oTT.fnGetSelectedData();
                        if (aData.length > 1 ) 			
                        	alert("Les modifications ne peuvent se faire que sur un seul enregistrement à la fois") ;
                        if (aData.length == 0 ) 			
                        	alert("Merci de sélectionner la ligne à modifier") ;

          
                                            					 	
                }
              },  
                { sExtends: "editor_remove", editor: editor, "sButtonText": "Supprime" },
                
                { "sExtends": "text", "sButtonText": "Menu",	editor: editor, 						
									"fnClick": function ( nButton, oConfig, oFlash ) {									
										document.location.href="http://qpread.com";
									}
				}
                
            ] // fin de aButtons
        } // fin de tableTools
    } ); // fin de DataTable
} ); // fin de $(document).ready

			
		</script>


		<!-- Includes required for the example page's behaviour - not needed for Editor itself -->
		<!-- J'ai supprimé ici 2 imports ainsi que l'ajout de shCore.js et examples.js -->
		
		<style type="text/css">
			@import "http://qpread.com/DataTables-4/media/css/demo_page.css";
			

			
		</style>
		
		
	</head>
	
	
	<body id="dt_example">
	
	<div class="section group qs_bkg_plus">	
			<div class="full_width big">Liste des invités de : <?php echo $user_login; ?></div>
				
						<table id="qspread_invite"  class="display responsive nowrap" cellspacing="0" width="80%">
							<thead>
								<tr>
								
									<th>nom_invite</th>
									<th>email</th>
									<th>invitation</th>
									<th>réponse</th>
									<!-- <th>parrain</th> -->
									<th>date de l'invitation</th>
								</tr>
							</thead>
					</table>
		<!-- </div>container -->	
	</div> <!-- section group-->
	
	</body>

</html>


<?php get_footer(); ?>
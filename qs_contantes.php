<?php

/* Lorsque les contacts viennent d'être lus du service (par exemple Yahoo!) la valeur du champ donnees_service_encours 
de la table wp_qspread_invite prend 0, 
lorsque une invitation est enregistrée pour un utilisateur, donnees_service_encours prend 1,
lorsque toutes les données ont été lues et que l'utilisateur a validé sa décision en fermant la fenêtre, donnees_service_encours
prend 2.
*/

define("DONNEES_SERVICE_ENCOURS_EN_LECTURE", 0);
define("DONNEES_SERVICE_ENCOURS_LUES", 31);
define("DONNEES_SERVICE_ENCOURS_TRAITEES", 32);


define("SERVICE_email", 0);
define("SERVICE_YAHOO", 1);
define("SERVICE_GMAIL", 2);
define("SERVICE_TWITTER", 3);
define("SERVICE_FACEBOOK", 4);


/* Note : parfois statut et action sont similaires */
define("MAJ_BDD_INVITE", 999);

define("ETAT_INVITATION_ACTIVE", 100);

/* NOTE : Il faut que les valeurs du statut soit > 100 */
/* Ajout temporaire */
define("STATUT_TEMPORAIRE", 1);

/* L'invitation a été créée */
define("STATUT_INVITER", 101);

/* L'invitation a été transmise */
define("STATUT_NE_PLUS_INVITER", 102);

/* Une réponse à l'invitation a été reçue */
define("STATUT_CREATION_INVITATION_REPONSE_RECUE", 103);


/* Un message a été transmis à l'invité pour l'informer de l'invitation */
define("INVITATION_TRANSMISE_NON", 0);
define("INVITATION_TRANSMISE_OUI", 1);


/* Une réponse à l'invitation a été reçue */
/* Attention le fichier qsbddmailreceiptsc qui traite la réponse des invités est sous le format 
	Content Builder, il utilie la View qs_c et le Slide qSpread_C
	Ici les constantes ne sont pas utilisés mais uniquement des valeurs numériques
	donc si des modifications doivent être effectuées ici il faudra aussi les propager dans le slide Spread_C
*/
define("QUESTION_BINAIRE_PAS_DE_REPONSE", 0);
define("QUESTION_BINAIRE_OUI", 1);
define("QUESTION_BINAIRE_NON", 3);
define("QUESTION_BINAIRE_JENESAISPAS", 2);

?>

$(document).ready(function() 
{
  $('[data-toggle="tooltip"]').tooltip();
	
  if ( $("#datenaissance" ).length ) {
		$("#datenaissance" ).datepicker({
				changeMonth: true,
				changeYear: true,
				maxDate: "+1D",
				minDate: new Date(1900, 1, 1),
				showAnim: "slideDown",
				autoSize: true,
				firstDay : 1,
				showButtonPanel: true,
				dateFormat:"dd/mm/yy",
				yearRange: "-99:-15",
				closeText: 'Fermer',
				prevText: 'Précédent',
				nextText: 'Suivant',
				currentText: 'Aujourd\'hui',
				monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
				monthNamesShort: ['Janv.', 'Févr.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
				dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
				dayNamesShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
				dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
				weekHeader: 'Sem.'
			});

		if (eppnc!="")
			retourne_date_naissance();
		else
			$("#datenaissance" ).datepicker( "setDate", "-20Y" );
  }
	
  $('#btn_soumettre').click(function() {
	
		$('#uid').val( $.trim( $('#uid').val().toUpperCase() ));
		soumettre_demande();

  });

  $('#btn_compris').click(function() {
	  
	if (!$('#btn_compris').prop('checked'))
	  $('#btn_compris').prop('checked', true);
	  
	$('#btn_compris').prop('disabled', true); 

	$('#lien_quitus').html("<div class='alert alert-secondary' role='alert'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> Génération du lien de téléchargement en cours...</div>");

	var quituscrypt = $.trim($('#quituscrypt').val());

	// appel Ajax
	$.ajax({
			url: "genere_lien_quitus.php", 
			data: "quitus="+quituscrypt,
			type: "POST", 
			dataType: 'json',
			success: function(json) {
				$('#lien_quitus').html("<div class='alert alert-secondary' role='alert'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> Génération du lien de téléchargement en cours...</div>");
				switch (json.reponse)
				{
					case "OK":
					// $('#btn_compris').prop('readonly', true);					
					$('#btn_compris').prop('disabled', true);					
					$('#lien_quitus').html(json.message);
					break;
					
					case "KO":
					$('#lien_quitus').html("<div class='alert alert-danger' role='alert'>" + json.message + "</div>");
					$('#btn_compris').prop('disabled', false); 
					$('#btn_compris').prop('checked', false);
					break;

					default:
					$('#lien_quitus').html("<div class='alert alert-danger' role='alert'>Erreur : " + json.message + "</div>");
					$('#btn_compris').prop('disabled', false); 
					$('#btn_compris').prop('checked', false);

				}
			},
			beforeSend: function(){
				// debut animation pendant envoi
				$('#lien_quitus').html("<div class='alert alert-secondary' role='alert'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> Génération du lien de téléchargement en cours...</div>");
				$('#btn_compris').prop('disabled', true); 
			},
			complete: function(){
				// fin animation pendant envoi
			},
			error: function(){
				// fin animation pendant envoi
				$('#lien_quitus').html("<div class='alert alert-danger' role='alert'>Erreur inconnue, impossible de continuer.</div>");
				$('#btn_compris').prop('disabled', false); 
				$('#btn_compris').prop('checked', false);
			}
		
	});
	
  });
  
  $('#btn_verifier').click(function() {
	  
	    if (!$('#btn_verifier').prop('checked'))
		   $('#btn_verifier').prop('checked', true);
	  
	    $('#btn_verifier').prop('disabled', true); 
	    $('#uid').prop('readonly', true);
	    $('#codequitus').prop('readonly', true);
					
	    $('#information_quitus').html("<div class='alert alert-secondary' role='alert'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> Vérification en cours...</div>");
		
		var uid = $.trim($('#uid').val());
		var codequitus = $.trim($('#codequitus').val());
		
		// appel Ajax
		$.ajax({
			url: "test_quitus_valid.php", 
			data: "uid="+uid+"&codequitus="+codequitus,
			type: "POST", 
			dataType: 'json',
			success: function(json) {
				$('#information_quitus').html("<div class='alert alert-secondary' role='alert'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> Vérification en cours...</div>");
				switch (json.reponse)
				{
					case "OK":
					$('#uid').prop('readonly', true);
					$('#codequitus').prop('readonly', true)	;				
					$('#btn_verifier').prop('disabled', true);					
					$('#information_quitus').html(json.message);
					break;
					
					case "KO":
					$('#information_quitus').html("<div class='alert alert-danger' role='alert'>" + json.message + "</div>");
					$('#uid').prop('readonly', false);
					$('#codequitus').prop('readonly', false);				
					$('#btn_verifier').prop('disabled', false); 
					$('#btn_verifier').prop('checked', false);
					break;

					default:
					$('#information_quitus').html("<div class='alert alert-danger' role='alert'>Erreur : " + json.message + "</div>");
					$('#uid').prop('readonly', false);
					$('#codequitus').prop('readonly', false);
					$('#btn_verifier').prop('disabled', false); 
					$('#btn_verifier').prop('checked', false);

				}
			},
			beforeSend: function(){
				// debut animation pendant envoi
				$('#information_quitus').html("<div class='alert alert-secondary' role='alert'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> Vérification en cours...</div>");
				// $('#uid').prop('readonly', false);
				// $('#codequitus').prop('readonly', false);
				$('#btn_verifier').prop('disabled', true); 
			},
			complete: function(){
				// fin animation pendant envoi
			},
			error: function(){
				// fin animation pendant envoi
				$('#information_quitus').html("");
				$('#btn_verifier').prop('disabled', false); 
				$('#btn_verifier').prop('checked', false);
			}
		
	});
  });

  $('.chargement').hide();
  $('.chargement').css("visibility","hidden");

});

function envoi_mail() {
	
	$('#resultat').html("<div class='alert alert-secondary' role='alert'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> Envoi du mail en cours...</div>");
	
	var uid = $.trim($('#uid').val());
	var datenaissance = $.trim($('#datenaissance').val());
	
	// appel Ajax
	$.ajax({
		url: "envoi_mail.php", 
		data: "uid="+uid+"&datenaissance="+datenaissance,
		type: "POST", 
		dataType: 'json',
		success: function(json) {
			$('#resultat').html("<div class='alert alert-secondary' role='alert'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> Envoi du mail en cours...</div>");
			switch (json.reponse)
			{
				case "OK":
				$('#resultat').html("<div class='alert alert-success' role='alert'>" + json.message + "</div>");
				break;
				
				case "KO":
				$('#resultat').html("<div class='alert alert-danger' role='alert'>" + json.message + "</div>");
				break;

				default:
				$('#resultat').html("<div class='alert alert-danger' role='alert'>Erreur : " + json.message + "</div>");

			}
		},
		beforeSend: function(){
			// debut animation pendant envoi
			$('#resultat').html("<div class='alert alert-secondary' role='alert'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> Envoi du mail en cours...</div>");
				},
		complete: function(){
			// fin animation pendant envoi
			
		},
		error: function(){
			// fin animation pendant envoi
			$('#resultat').html("<div class='alert alert-danger' role='alert'><i class='fa fa-times' aria-hidden='true'></i> Erreur inconnue...</div>");
		}
		
	});

	return false; // j'empêche le navigateur de soumettre lui-même le formulaire
	
}

function soumettre_demande() {
	
	$('#information').html("<div class='alert alert-secondary' role='alert'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> Récupération des informations de votre compte lecteur en cours...</div>");

	var uid = $.trim($('#uid').val());
	var datenaissance = $.trim($('#datenaissance').val());
	
	// appel Ajax
	$.ajax({
		url: "information.php", 
		data: "uid="+uid+"&datenaissance="+datenaissance,
		type: "POST", 
		dataType: 'json',
		success: function(json) {
			$('#information').html("<div class='alert alert-secondary' role='alert'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> Récupération des informations de votre compte lecteur en cours...</div>");
			switch (json.reponse)
			{
				case "OK":
				$('#ent').hide();
				$('#ent').css("visibility","hidden");
				
				$('#uid').prop('readonly', true);
				$('#datenaissance').prop('readonly', true);
				$("#datenaissance").datepicker("destroy");
				
				$('#ou1').hide();
				$('#ou1').css("visibility","hidden");
				
				$('#ou2').hide();
				$('#ou2').css("visibility","hidden");
				
				$('#carte').hide();
				$('#carte').css("visibility","hidden");
				
				$('#soumettre').hide();
				$('#soumettre').css("visibility","hidden");
				
				$('#information').html("<div class='alert alert-success' role='alert'>" + json.message + "</div><div>" + json.message2 + "</div>");
				break;
				
				case "PBQUITUS":
				$('#ent').hide();
				$('#ent').css("visibility","hidden");
				
				$('#uid').prop('readonly', true);
				$('#datenaissance').prop('readonly', true);
				$("#datenaissance").datepicker("destroy");
				
				$('#ou1').hide();
				$('#ou1').css("visibility","hidden");
				
				$('#ou2').hide();
				$('#ou2').css("visibility","hidden");
				
				$('#carte').hide();
				$('#carte').css("visibility","hidden");
				
				$('#soumettre').hide();
				$('#soumettre').css("visibility","hidden");
				
				$('#information').html("<div class='alert alert-danger' role='alert'>" + json.message + "</div><div>" + json.message2 + "</div>");
				break;
				
				case "KO":
				$('#information').html("<div class='alert alert-danger' role='alert'>" + json.message + "</div>");
				break;

				default:
				$('#information').html("<div class='alert alert-danger' role='alert'>Erreur : " + json.message + "</div>");

			}
		},
		beforeSend: function(){
			// debut animation pendant envoi
			$('#information').html("<div class='alert alert-secondary' role='alert'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> Récupération des informations de votre compte lecteur en cours...</div>");
		},
		complete: function(){
			// fin animation pendant envoi
		},
		error: function(){
			// fin animation pendant envoi
			$('#information').html("<div class='alert alert-danger' role='alert'><i class='fa fa-times' aria-hidden='true'></i> Erreur inconnue, impossible de continuer.</div>");
		}
	
	});
		
}

function retourne_date_naissance() {
	
	$('#information').html("<div class='alert alert-secondary' role='alert'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> Vérification de votre date de naissance en cours...</div>");
	
	// appel Ajax
	$.ajax({
		url: "retourne_date_naissance.php", 
		data: "eppnc="+eppnc,
		type: "POST", 
		dataType: 'json',
		success: function(json) {
			$('#information').html("<div class='alert alert-secondary' role='alert'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> Vérification de votre date de naissance en cours...</div>");
			switch (json.reponse)
			{
				case "OK":
				$('#information').html("");
				// $('#datenaissance').prop('readonly', false);

				$("#datenaissance" ).datepicker( "setDate", json.message );
				// $('#btn_soumettre').prop('disabled', false);
				
				soumettre_demande();
				
				break;
				
				case "KO":
				$('#information').html("<div class='alert alert-danger' role='alert'>" + json.message + "</div>");
				break;

				default:
				$('#information').html("<div class='alert alert-danger' role='alert'>Erreur : " + json.message + "</div>");

			}
		},
		beforeSend: function(){
			// debut animation pendant envoi
			$('#information').html("<div class='alert alert-secondary' role='alert'><i class='fa fa-cog fa-spin' aria-hidden='true'></i> Vérification de votre date de naissance en cours...</div>");
				},
		complete: function(){
			// fin animation pendant envoi
			
		},
		error: function(){
			// fin animation pendant envoi
			$('#information').html("<div class='alert alert-danger' role='alert'><i class='fa fa-times' aria-hidden='true'></i> Erreur inconnue...</div>");
		}
		
	});

	return false; // j'empêche le navigateur de soumettre lui-même le formulaire
	
}

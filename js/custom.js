jQuery(document).ready(function(jQuery)
{


	prb_frontend = new PRBFrontEnd();



});

function PRBFrontEnd() {
  this.init();
};

PRBFrontEnd.prototype = {
	/* Alles wichtige was am Anfang gebraucht wird */
	init:function () {
		/* Aufrufen von wichtigen Funktionen */
		this.formSubmit();
		this.goalProgress();
	},
	validateParsleyForm: function() {

		jQuery('.prb_form').parsley();
	},
	goalProgress: function() {

		// Bundesländer anzeigen
		jQuery('.showBundeslandGoals').click(function(evt) {
			evt.preventDefault();
			jQuery('.bundesland_goals').fadeIn(1000, function() {
					jQuery('.hideBundeslandGoals').show();
			});
			jQuery(this).hide();

		})

		// Bundesländer verbergen
		jQuery('.hideBundeslandGoals').click(function(evt) {
			evt.preventDefault();
			jQuery('.bundesland_goals').fadeOut(700, function() {
					jQuery('.showBundeslandGoals').show();
			});
			jQuery(this).hide();

		})

		var main_goal_value = jQuery('#main_goal').attr('data-value');
		var goalAmount = parseInt(main_goal_value) *100 / 80;

		jQuery('#main_goal').goalProgress({
        goalAmount: goalAmount,
        currentAmount: main_goal_value,
        textBefore: '€',
        textAfter: ' gesammelt.'
    });

		jQuery('.goals').each(function() {

			var goal_value = parseInt(jQuery(this).attr('data-value'));

			if(goal_value == 0) {
				goalAmount = 10000;
			} else if (goal_value < 3) {
				goalAmount = 10000;
			} else {
				goalAmount = parseInt(goal_value) *100 / 80;
			}



			jQuery(this).goalProgress({
	        goalAmount: goalAmount,
	        currentAmount: goal_value,
	        textBefore: '€',
	        textAfter: ' gesammelt.'
	    });
		})



	},
	formSubmit: function() {
		jQuery('.prb_form').submit(function(evt) {

			// Validierung hier
			if(jQuery('.prb_form').parsley().validate()) {
				var vorname = jQuery('.prb_form #vorname').val();
				var nachname = jQuery('.prb_form #nachname').val();
				var tel = jQuery('.prb_form #tel').val();
				var mail = jQuery('.prb_form #mail').val();
				var bundesland = jQuery('.prb_form #bundesland').val();
				var nonce = jQuery('.prb_form #mail_nonce').val();

				var sendMails = jQuery.ajax({
						url : Custom.ajaxurl,
						type : 'post',
						data : {
							 action: 'send_prb_mails',
							 vorname: vorname,
							 nachname: nachname,
							 bundesland: bundesland,
							 tel: tel,
							 mail: mail,
							 nonce : nonce
						},
						beforeSend: function( xhr ) {
							jQuery('.prb_form_container').append('<div class="xhrWait"><center><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><br /><br />Deine Anfrage wird bearbeitet. Bitte habe ein wenig Geduld.</center></div>');
							jQuery('.prb_form').hide();
							//jQuery(that).parent().append('<center><i style="font-size: 14px;color:#EA4E92;" class="fa fa-spinner fa-pulse fa-3x fa-fw"></i> wird bearbeitet</center>');
							//jQuery('.edit, .close, .delete').remove();
						},
						error : function(error) {
							console.log(error)
							alert('Keine Berechtigung')
						}
				})

				sendMails.done(function(response) {
					jQuery('.prb_form').hide();
					jQuery('.xhrWait').remove();
					jQuery('.prb_form_abschlusscreen').show();
				})
			}

			return false;
		})
	},
}

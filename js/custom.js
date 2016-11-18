jQuery(document).ready(function(jQuery)
{


	var prb_frontend = new PRBFrontEnd();



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
	goalProgress: function() {


		jQuery('#sample_goal').goalProgress({
        goalAmount: 150,
        currentAmount: 100,
        //textBefore: '$',
        textAfter: ' €'
    });


	},
	formSubmit: function() {
		jQuery('.prb_form').submit(function(evt) {

			// Validierung hier

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
						//jQuery(that).parent().append('<center><i style="font-size: 14px;color:#EA4E92;" class="fa fa-spinner fa-pulse fa-3x fa-fw"></i> wird bearbeitet</center>');
						//jQuery('.edit, .close, .delete').remove();
					},
					error : function(error) {
						console.log(error)
						alert('Keine Berechtigung')
					}
			})

			sendMails.done(function(response) {
				console.log(response);
			})


			return false;
		})
	},
}

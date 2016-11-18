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
        textAfter: ' € von 1000€ gesammelt'
    });


	},
	formSubmit: function() {
		jQuery('.prb_form').submit(function(evt) {

			// Validierung hier




			return false;
		})
	},
}

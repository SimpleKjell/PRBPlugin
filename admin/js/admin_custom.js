jQuery(document).ready(function(jQuery)
{


	var prb_admin = new PRBAdmin();



});

function PRBAdmin() {
  this.init();
};

PRBAdmin.prototype = {
	/* Alles wichtige was am Anfang gebraucht wird */
	init:function () {
		/* Aufrufen von wichtigen Funktionen */
		this.spendenCircle();
	},
	spendenCircle: function() {


		jQuery('.pr-breakfast-admin-contain .circle').circleProgress({
		    value: 0.6,
				fill: { color: '#EA4E92'}
		}).on('circle-animation-progress', function(event, progress) {
		    jQuery(this).find('strong').html(parseInt(160 * progress) + '<i>€</i>');
		});

	},
}

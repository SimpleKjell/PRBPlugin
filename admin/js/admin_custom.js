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
		this.tableSort();
		this.editDonationValues();
	},
	editDonationValues: function() {
		jQuery('#donationTableMain .edit').click(function(evt) {
			evt.preventDefault();
			var parentTr = jQuery(this).parent().parent();

			// Text ausblenden
			parentTr.find('.text').hide();
			// Delete Button ausblenden
			parentTr.find('.delete').hide();
			// InputFeld einblenden
			parentTr.find('.editInput').show();
			// bearbeiten Button ausblenden
			jQuery(this).hide();
			// abbrechen Button einlbenden
			jQuery(this).parent().find('.close').show();
			// Save Button einblenden
			jQuery('.editDonationSaveButton').show();
		})

		jQuery('#donationTableMain .close').click(function(evt) {
			evt.preventDefault();
			var parentTr = jQuery(this).parent().parent();

			// Text wieder anzeigen
			parentTr.find('.text').show();
			// Delete Button einblenden
			parentTr.find('.delete').show();

			parentTr.find('.text').each(function() {
				jQuery(this).parent().find('.editInput input').val(jQuery(this).text());
			});
			// InputFeld ausblenden
			parentTr.find('.editInput').hide();

			jQuery(this).hide();
			jQuery(this).parent().find('.edit').show();

			// Wenn es kein zu bearbeitendes Element mehr gibt, blende auch den Speichern Button aus
			if(!jQuery('#donationTableMain .editInput').is(':visible')) {
				jQuery('.editDonationSaveButton').hide();
			}
		})


		jQuery('#donationTableMain .delete').click(function(evt) {
			evt.preventDefault();


			var id = jQuery(this).attr('data-rowId');
			var nonce = jQuery(this).attr('data-nonce');


			that = this;
			var deleteDonation = jQuery.ajax({
					url : Custom.ajaxurl,
					type : 'post',
					data : {
						 action: 'delete_donation',
						 id : id,
						 nonce : nonce
					},
					beforeSend: function( xhr ) {
						jQuery(that).parent().append('<center><i style="font-size: 14px;color:#EA4E92;" class="fa fa-spinner fa-pulse fa-3x fa-fw"></i> wird bearbeitet</center>');
						jQuery('.edit, .close, .delete').remove();


					},
					error : function(error) {
						console.log(error)
						alert('Keine Berechtigung')
					}
			})

			deleteDonation.done(function(response) {
				location.reload();
			})

		})


	},
	tableSort: function() {

		jQuery('#donationTableMain').tablesorter();

	},
	spendenCircle: function() {


		jQuery('.pr-breakfast-admin-contain .circle').circleProgress({
		}).on('circle-animation-progress', function(event, progress) {
			var value = jQuery(this).attr('data-amount');
		  jQuery(this).find('strong').html(parseInt(value * progress) + '<i>€</i>');
		});

	},
}

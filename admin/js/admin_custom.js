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
	getTwoDataGraph: function(year, lastYear) {

		var dataValuesCurrentYear = jQuery('#graphData'+year).attr('data-graphValues');
		var dataValuesLastYear = jQuery('#graphData'+lastYear).attr('data-graphValues');

		var myData = {
			labels : ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct", "Nov", "Dec"],
			datasets : [
				{
					fillColor : "rgba(220,220,220,.5)",
					strokeColor : "rgba(220,220,220,1)",
					pointColor : "rgba(220,220,220,1)",
					pointStrokeColor : "#fff",
					data : dataValuesLastYear.split(',')
				},
				{
					fillColor : "rgba(234, 78, 146, 0.5)",
					strokeColor : "rgba(234, 78, 146, 1)",
					pointColor : "rgba(234, 78, 146, 1)",
					pointStrokeColor : "#fff",
					data : dataValuesCurrentYear.split(',')
				}
			]
		}

		return myData;
	},
	getSingleDataGraph: function(year) {

		// Bein Initialisieren wird hier einfach current abgefragt
		var dataValuesCurrentYear = jQuery('#graphData'+year).attr('data-graphValues');


		var myData = {
			labels : ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct", "Nov", "Dec"],
			datasets : [
				{
					fillColor : "rgba(234, 78, 146, 0.5)",
					strokeColor : "rgba(234, 78, 146, 1)",
					pointColor : "rgba(234, 78, 146, 1)",
					pointStrokeColor : "#fff",
					data : dataValuesCurrentYear.split(',')
				}
			]
		}

		return myData;
	},
	createBarChart: function(availableYears, currentYear, lastYear) {

		var nextYear = currentYear+1;
		if(availableYears.indexOf(nextYear) != -1)
		{
			// Wenn es ein nächstes Jahr gibt, zeige auch den Pfeil an
			jQuery('.barGraphHeading .nextYear').show();
		} else {			
			// Wenn es kein nächstes Jahr gibt, zeige auch den Pfeil nicht an
			jQuery('.barGraphHeading .nextYear').hide();
		}

		// Wenn es ein Vorjahr gibt, zeige es als zweite Grafik an.
		if(availableYears.indexOf(lastYear) != -1)
		{
			// Wenn es ein Vorjahr gibt, zeige auch den Pfeil an
			jQuery('.barGraphHeading .prevYear').show();
			var myData = this.getTwoDataGraph(currentYear, lastYear);
	 	} else {
			// Da es kein Vorjahr gibt, muss der Pfeil auch ausgeblendet werden
			jQuery('.barGraphHeading .prevYear').hide();
			var myData = this.getSingleDataGraph(currentYear);
		}

		// Vorerst löschen, dann neu setzen, da es Probleme gab.
		jQuery('.canvas_container').empty().append('<canvas id="canvas" width="900" height="300"></canvas>');


		new Chart(document.getElementById("canvas").getContext("2d")).Line(myData)

	},
	barGraph: function() {

		var currentYear = new Date().getFullYear()
		var lastYear = currentYear-1;

		jQuery('.barGraphHeading .year').text(currentYear);


		var availableYears = '';
		var statisticIds = jQuery('.barGraphStatistics div').each(function() {
			var string = jQuery(this).attr('id');
			var year = string.replace( /^\D+/g, '');

			availableYears += year + ',';
		})
		var length = availableYears.length;
		availableYears = availableYears.substring(0, length -1);


		this.createBarChart(availableYears, currentYear, lastYear);

		that = this;
		// Handle prev next Year statistics
		jQuery('.barGraphHeading .prevYear').click(function() {
			currentYear = currentYear-1;
			lastYear = currentYear-1;

			jQuery('.barGraphHeading .year').text(currentYear);

			that.createBarChart(availableYears, currentYear, lastYear);
		})

		// Handle next Year statistics
		jQuery('.barGraphHeading .nextYear').click(function() {
			currentYear = currentYear+1;
			lastYear = currentYear-1;

			jQuery('.barGraphHeading .year').text(currentYear);

			that.createBarChart(availableYears, currentYear, lastYear);
		})


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

		jQuery('#donationTableMain').tablesorter({dateFormat: 'M Y'});

	},
	spendenCircle: function() {


		jQuery('.pr-breakfast-admin-contain .circle').circleProgress({
		}).on('circle-animation-progress', function(event, progress) {
			var value = jQuery(this).attr('data-amount');
		  jQuery(this).find('strong').html(parseInt(value * progress) + '<i>€</i>');
		});

	},
}

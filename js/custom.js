/*
* Test für den Designer
*/
/*jQuery(document).ready(function() {


	var views = jQuery('.entry-content li').attr('data-views');
	//console.log(views);
	//console.log(views['title']);


})*/


jQuery(document).ready(function(jQuery)
{

	// Wenn es sich um die mobile Version handelt, füge die Klasse hinzu
	var isVisible = jQuery('.edit_mobile_element').is(':visible');
	if(isVisible) {
		jQuery('#renderSVG').addClass('sf_mobile');
	}

	function fillImageFullContent()
	{


		// Vorab Scaling auf 1 setzen
		// Scale das Image auf die richtige Größe
		var transformRequestObj=mySVG.createSVGTransform();
		var animTransformList=DragTarget.transform
		var transformList=animTransformList.baseVal

		transformRequestObj.setScale(1,1)

		transformList.appendItem(transformRequestObj)
		transformList.consolidate();



		// Vorab darf kein neues Bild hochgeladen werden
		jQuery('.upload_image_svg').parent().hide();
		// Das Bild muss nun den ganzen Container füllen
		var ww = jQuery('#'+selectedElementId).attr('width')
		var hh = jQuery('#'+selectedElementId).attr('height')
		//console.log(hh)
		//console.log(ww);
		// Herausfinden, ob die Höhe oder die Breite näher am "fill" ist
		var restWW = 0;
		var restHH = 0;
		if(ww < 1200) {
			restWW = 1200 - ww;
		}
		if(hh < 1711) {
			restHH = 1711 - hh;
		}

		var scaleImage = 1;
		var scaleDown = false;
		// benutz für das Skaling den Wert, der am weitesten weg ist
		if(restHH > restWW) {
			scaleImage = 1711 / hh;


		} else if(restHH < restWW) {
			scaleImage = 1200 / ww;
		} else if(restHH == restWW && restWW != 0) {
			// Da beide gleich sind, nimm ein von beiden
			scaleImage = 1200 / ww;

		} else if(restHH == 0) {
			scaleDown = true;
		}


		// Wenn das Bild zu groß ist, bleibt der Wert 1 im scaling
		if(scaleDown) {

			// Das Bild muss nun runterskaliert werden
			var overHH = hh - 1711;
			var overWW = ww - 1200;

			// Welcher ist weniger Weit vom originalMaß entfernt
			if(overHH > overWW) {
				scaleImage = 1200 / ww;
			} else if(overHH < overWW) {
				scaleImage = 1711 / hh;
			} else if(overHH == overWW) {
				scaleImage = 1711 / hh;
			}


		}






		// Scale das Image auf die richtige Größe
		/*var transformRequestObj=mySVG.createSVGTransform();
		var animTransformList=DragTarget.transform
		var transformList=animTransformList.baseVal

		transformRequestObj.setScale(scaleImage,scaleImage)

		transformList.appendItem(transformRequestObj)
		transformList.consolidate();*/

		jQuery('#'+selectedElementId).attr('width', ww* scaleImage)
		jQuery('#'+selectedElementId).attr('height', hh* scaleImage)
	}

	jQuery('.upload_image_svg').click(function(evt) {
		evt.preventDefault();
		jQuery('.new_text_container').hide();
		jQuery('.new_bg_container').hide();

		jQuery('#trigger_image_upload').click();
	})

	jQuery('#trigger_image_upload').on('change', function() {

		//console.log(jQuery('#trigger_image_upload').parsley().validate());

		if(jQuery('#trigger_image_upload').parsley().validate() === true) {
			var fd = new FormData();
			var file = jQuery('#trigger_image_upload');
			var individual_file = file[0].files[0];
			var nonce = jQuery('#nonce_upload').val();
			fd.append("file", individual_file);
			fd.append('action', 'sub_upload');
			fd.append('nonce', nonce);

			them = that;
			jQuery.ajax({
					type: 'POST',
					url: Custom.ajaxurl,
					data: fd,
					contentType: false,
					processData: false,
					beforeSend: function () {
						jQuery('#pbar').show();
						jQuery('.sf_form_upload_btn').hide();
						jQuery('.edit_svg_container_inner').show();
						jQuery('.edit_image_container').show();

					},
					xhr: function () {
							var xhr = jQuery.ajaxSettings.xhr();
							xhr.upload.onprogress = function (e) {
									if (e.lengthComputable) {
										var percantage = Math.round(e.loaded / e.total * 100);
										//if(percantage <)
										//console.log(percantage);
										progressbar.attr('max', percantage);
										if (typeof(animateUploadProgress) != "undefined") {
												clearInterval(animateUploadProgress);
										}
										animateUploadProgress = setInterval(function() {
										them.setNewProgressLoading();
										}, progressTime);
									}
							};
							return xhr;
					},
					error: function(xhr) {
						console.log(xhr);
					},
					success: function(response){
						//console.log(response)
						var res = response.split(';;');

						jQuery('#pbar').hide();
						progressbar.attr('max', 0);
						progressbar.attr('value', 0);


						var anzahlImages = jQuery('.storageImageSVG img').length;

						// Einmal als Original File im Container sichern
						jQuery('.storageImageSVG').append('<div id="imageStorage'+anzahlImages+'">'+res[2]+'</div>');

						// Jetzt noch ein neues SVG Element erstellen
						// Wichtige Bild Daten
						var src = jQuery('#imageStorage'+anzahlImages+' img').attr('src');
						var width= jQuery('#imageStorage'+anzahlImages+' img').attr('width');
						var height= jQuery('#imageStorage'+anzahlImages+' img').attr('height');

						var img = document.createElementNS('http://www.w3.org/2000/svg','image');
						img.setAttributeNS(null,'height',height);
						img.setAttributeNS(null,'width',width);
						img.setAttributeNS('http://www.w3.org/1999/xlink','href',src);
						img.setAttributeNS(null,'x','0');
						img.setAttributeNS(null,'y','0');
						img.setAttributeNS(null, 'visibility', 'visible');
						img.setAttributeNS(null,"id","imageSvgInner"+anzahlImages);

						jQuery('.image-layer').append(img);


						selectedElementId = "imageSvgInner"+anzahlImages;

						DragTarget = document.getElementById(selectedElementId);

						selectedElementIdContainer = "imageStorage"+anzahlImages;
						// Zeige den Bild bearbeitungscontainer
						jQuery('.edit_image_container .image_container').empty();
						jQuery('.edit_image_container .image_container').append(res[3]);
						/* IE nimmt kein .html*/
						//jQuery('.edit_image_container .image_container').html(res[3]);

						jQuery('.storageImageThumb').append('<div class="'+selectedElementId+'">'+res[3]+'</div>');



						// Wenn es sich um die mobile Version handelt, gibt es weniger Funktionen

						if(jQuery('#renderSVG').hasClass('sf_mobile')) {

							fillImageFullContent();

						}
					}
			});
		} else {
			alert('Das Bild überschreitet das Upload- Limit. Bitte lade ein kleineres hoch.')
		}


	})

	jQuery('#add_new_design').parsley();


	jQuery('#add_new_design').submit(function(e) {
		e.preventDefault();
		if(jQuery('#add_new_design').parsley().validate()) {



			// Erstelle zuerst das canvas
			create_svg();

			// Jetzt einen user erstellen.

			var nonce = jQuery('#add_new_design .nonce').attr("data-nonce");
			var subForm = jQuery( '#add_new_design' ).serializeArray();

			var addTeilnehmer = jQuery.ajax({
					url : Custom.ajaxurl,
					type : 'post',
					data : {
						 action: 'add_new_sub',
						 nonce: nonce,
						 subForm: subForm
					},
					beforeSend: function( xhr ) {
						jQuery('#add_new_design').remove();
						jQuery('.loading_process_sub_teilnahme').fadeIn(1000);
					},
					error : function(error) {
						console.log(error)
						alert('Keine Berechtigung')
					}
			})

			addTeilnehmer.done(function(res) {
				//console.log(res);
					// Jetzt das jpg speichern und dem User zuordnen
					var c=document.getElementById("svg-canvas");
					var img_src =  c.toDataURL('image/png')
					jQuery('#svg-img').attr('src', img_src);

					var teilnehmer_id = res;
					jQuery.ajax({
							url : Custom.ajaxurl,
							type : 'post',
							data : {
								 action: 'add_design_to_sub',
								 teilnehmer_id: teilnehmer_id,
								 img_src: img_src
							},
							beforeSend: function( xhr ) {
							},
							error : function(error) {
								console.log(error)
								alert('Keine Berechtigung')
							},
							success: function(res) {
								console.log(res);
								jQuery('.loading_process_sub_teilnahme').hide();
								jQuery('.finish_process_sub_teilnahme').fadeIn(1000);
							}
					})

			})

			return false;
		} else {
				return false;
		}

	})


	function create_svg()
	{
		// Canvas Element
		var c=document.getElementById("svg-canvas");
		var ctx=c.getContext("2d");
		ctx.clearRect(0, 0, c.width, c.height);
		ctx.save();
		// Zuerst die Hintergrundfarbe schreiben
		ctx.beginPath();
		var bgColor= jQuery('.bg-layer rect').attr('fill');
		ctx.rect(0, 0, 1200, 1711);
		ctx.fillStyle = bgColor;
		ctx.fill();

		// Jetzt alle Bilder aus dem "Storage"
		/*jQuery('.image-layer image').each(function(index, inhalt) {
			//var imageID = jQuery(this).attr('id');

			matrix = jQuery(this).attr('transform');
			matrix_array = matrix.split(' ');


			if(typeof matrix_array[5] !== "undefined") {
				var laenge = matrix_array[5].length
				myX = matrix_array[4];
				myY = matrix_array[5].substr(0,laenge-1);


				var string = myX;
				substring = ",";
				if(string.indexOf(substring) !== -1) {
						myX = myX.replace(",", "");
				}

				var string = myY;
				substring = ",";
				if(string.indexOf(substring) !== -1) {
						myY = myY.replace(",", "");
				}


				// Höhe und Breite Scaling herausfinden
				var scaleFactor = matrix_array[0].substring(7);
				var startWidth = jQuery(this).attr('width')
				var startHeight = jQuery(this).attr('height')

				var string = scaleFactor;
				substring = ",";
				if(string.indexOf(substring) !== -1) {
						scaleFactor = scaleFactor.replace(",", "");
				}

				var width = startWidth*scaleFactor;
				var height = startHeight*scaleFactor;


				var imageIdString = jQuery(this).attr('id');

				var imageId = imageIdString.match(/\d+/g);

				//var img = jQuery('#imageStorage'+imageId);
				var imgContainer = document.getElementById('imageStorage'+imageId);
				var img = imgContainer.getElementsByTagName('img')[0]
				//var img=document.getElementById("");
				//var img2 = document.getElementById("test_image2");



				var myImage = new Image();
				myImage.src = jQuery(img).attr('src')
				myImage.onload = function(){
					 	ctx.drawImage(myImage,myX,myY,width,height);
				}



				//ctx.drawImage(img,myX,myY,width,height);
				//ctx.drawImage(img2,100,100, 50, 50);


			}
		})*/

		// canvg hat ein Problem beim rendern von Bildern im Firefox, also werden sie oben so eingefügt mit drawImage. Jetzt werden die Bilder entfernt und den Rest rendert canvg
		//if(jQuery.browser.mozilla) {
		//		jQuery('.image-layer').remove();
		//}


		var html = jQuery('#mySVG').html();
		html = html.replace(/>\s+/g, ">").replace(/\s+</g, "<");

		//console.log(jQuery('.image-layer').html());

		canvg(c, '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="1200" height="1711">'+html+'</svg>', { ignoreMouse: true, ignoreAnimation: true });

		return true;
	}








	var TransformRequestObj
	var TransList
	var DragTarget=null;
	var Dragging = false;
	var OffsetX = 0;
	var OffsetY = 0;


	jQuery('.designer_svg').bind('mousedown', function(evt){
		//dragging = true;

		evt.preventDefault();
		// Wenn ein Target anvisiert wird, zeige auch die editier Möglichkeiten - gilt hier nur für keine tspan elemente
		editableTarget = evt.target;
		selectedElementId = editableTarget.id;
		editableTargetID = editableTarget.id;

		if(editableTargetID != 'mySVG') {
			// Zeige den Text editor
			var string = editableTargetID,
			substring = "text";
			if (string.indexOf(substring) !== -1) {

				// Es handelt sich um ein Text Element, also zeige das Text editier feld
				jQuery('.new_text_container').fadeIn(500);
				jQuery('.edit_image_container').hide();
				jQuery('.new_bg_container').hide();
				// Fülle den Text Container
				//console.log(document.getElementById(editableTargetID).innerHTML)
				var textInhalt = jQuery('#'+editableTargetID).html();

				jQuery('#new_text').val(textInhalt);
			}

			// Zeige den Bild Editor
			var string = editableTargetID,
			substring = "image";
			if (string.indexOf(substring) !== -1) {

				// Es handelt sich um ein Text Element, also zeige das Text editier feld
				jQuery('.edit_image_container').fadeIn(500);
				jQuery('.new_text_container').hide();
				jQuery('.new_bg_container').hide();

				// Fülle den Bild Container
				var imageThumb = jQuery('.storageImageThumb .'+selectedElementId).html();
				jQuery('.edit_image_container .image_container').html(imageThumb);

			}
		} else {
			jQuery('.new_text_container').hide();
			jQuery('.edit_image_container').hide();
			jQuery('.new_bg_container').hide();
			jQuery('.new_bg_container').hide();
		}



		DragTarget = evt.target;

		//DragTarget = evt.target;
		//DragTarget = jQuery(evt).parent().target;

		if(jQuery(DragTarget).hasClass('tspan')) {
				var parent = jQuery(DragTarget).parent();
				parentId = parent.attr('id');
				DragTarget = document.getElementById(parentId);






				// Hier handelt es sich um ein tSpan Element, die Text editierMöglichkeit muss hier anders erzeugt werden
				editableTarget = DragTarget;
				editableTargetID = editableTarget.id;
				selectedElementId = editableTarget.id;
				var string = editableTargetID,
				substring = "text";
				if (string.indexOf(substring) !== -1) {

					// Es handelt sich um ein Text Element, also zeige das Text editier feld
					jQuery('.new_text_container').show();
					// Fülle den Text Container
					var textInhalt = '';
					jQuery('#'+editableTargetID+' .tspan').each(function(index, value) {
							textInhalt = textInhalt + jQuery(this).html() + '\n';
					})
					// letzten Linebreak ausnehmen
					var zeichenLaenge = textInhalt.length;
					textInhalt = textInhalt.substring(0,zeichenLaenge-1);
					jQuery('#new_text').val(textInhalt);
				} else {
					jQuery('.new_text_container').hide();
				}











		}

		//console.log(DragTarget.ownerSVGElement);
		var pnt = DragTarget.ownerSVGElement.createSVGPoint();
		pnt.x = evt.clientX;
		pnt.y = evt.clientY;


		//---elements transformed and/or in different(svg) viewports---
		var sCTM = DragTarget.getScreenCTM();
		var Pnt = pnt.matrixTransform(sCTM.inverse());

		TransformRequestObj = DragTarget.ownerSVGElement.createSVGTransform()
		//---attach new or existing transform to element, init its transform list---
		var myTransListAnim=DragTarget.transform
		TransList=myTransListAnim.baseVal

		OffsetX = Pnt.x
		OffsetY = Pnt.y

		Dragging=true;

	})
	jQuery(document).bind('mouseup', function() {

				Dragging = false;


	})
	jQuery(document).bind('mousemove', function(evt) {


		if(Dragging)
		{

			evt.preventDefault();

			var pnt = DragTarget.ownerSVGElement.createSVGPoint();
			pnt.x = evt.clientX;
			pnt.y = evt.clientY;
			//---elements in different(svg) viewports, and/or transformed ---
			var sCTM = DragTarget.getScreenCTM();
			var Pnt = pnt.matrixTransform(sCTM.inverse());
			Pnt.x -= OffsetX;
			Pnt.y -= OffsetY;

			//console.log(OffsetY);

			TransformRequestObj.setTranslate(Pnt.x,Pnt.y)
			//console.log(TransformRequestObj.matrix);
			TransList.appendItem(TransformRequestObj)
			TransList.consolidate()
		}
	})



	// Zeige den Background Container
	jQuery('.change_background_container').click(function() {
		jQuery('.edit_svg_container_inner').show();
		jQuery('.new_text_container').hide();
		jQuery('.new_bg_container').fadeIn(1000);
		jQuery('.edit_image_container').hide();
	})
	// Zeige den Text Container
	jQuery('.add_new_text_container').click(function() {
		jQuery('.edit_svg_container_inner').show();
		jQuery('.new_bg_container').hide();
		jQuery('.new_text_container').fadeIn(1000);
		jQuery('.edit_image_container').hide();


		jQuery('.new_text_container .input textarea').css('font-family', 'PlayfairDisplay-Regular');
		jQuery('.new_text_container .input textarea').css('color', '#111111');
		// Es wird gleich ein Text erstellt, dieser wird dann bei keydown aktuallisiert
		var valText = 'Dein Text';


		//var colorText = jQuery('#text_color').val();
		//var fontFamily = jQuery('#font_family').val();

		var anzahlTexte = jQuery('.storageTextSVG div').length;

		var newText = document.createElementNS('http://www.w3.org/2000/svg',"text");

		if(jQuery('#renderSVG').hasClass('sf_mobile')) {
			newText.setAttributeNS(null,"x",600);
			newText.setAttributeNS(null,"y",855.5);
			newText.setAttributeNS(null, "text-anchor", "middle");
		} else {
			newText.setAttributeNS(null,"x",150);
			newText.setAttributeNS(null,"y",150);
		}



		newText.setAttributeNS(null,"font-size","100");
		newText.setAttributeNS(null,"fill","#111111");
		newText.setAttributeNS(null,"font-family","PlayfairDisplay-Regular");
		newText.setAttributeNS(null,"id","textSvgInner"+anzahlTexte);


		var textNode = document.createTextNode(valText);
		//newTspan.appendChild(textNodeTspan);
		newText.appendChild(textNode);




		//newText.setAttributeNS(null,"font-family",fontFamily);

		//newText.style.fill = colorText;


		/*valTextBreaks = valText.replace(/(?:\r\n|\r|\n)/g, '<br />');
		valTextBreaks = valTextBreaks.split('<br />');

		for($i = 0; $i < valTextBreaks.length; $i++) {

			var newTspan = document.createElementNS('http://www.w3.org/2000/svg',"tspan");

			newTspan.setAttributeNS(null,"x",10);
			newTspan.setAttributeNS(null,"dy",150);
			newTspan.setAttributeNS(null,"class",'tspan');

			var textNodeTspan = document.createTextNode(valTextBreaks[$i]);
			newTspan.appendChild(textNodeTspan);
			newText.appendChild(newTspan);

		}*/



		selectedElementIdContainer = "textSvg"+anzahlTexte;
		selectedElementId = "textSvgInner"+anzahlTexte;


		jQuery('.storageTextSVG').append('<div id="textSvg'+anzahlTexte+'">'+valText+'</div>');
		jQuery('.text-layer').append(newText);
		jQuery('#new_text').val('');

		DragTarget = document.getElementById(selectedElementId);
		//console.log(DragTarget);


		// Mobil darf es nur einen Text geben
		if(jQuery('#renderSVG').hasClass('sf_mobile')) {
			jQuery('.add_new_text_container').hide();
		}


	})


	// Hintergrund ändern
	jQuery('.new_bg_container .bgColorPick').click(function() {
		var bg_color = jQuery(this).attr('bgcolor');
		jQuery('.bg-layer rect').attr('fill', bg_color);
	})


	istSpanElement = false;
	ismultiTSpanElement = false;

	// Text hinzufügen
	jQuery('#new_text').keyup(function(e) {


		if(selectedElementId) {

			var changeText = jQuery(this).val()

			var code = e.which;

			if(code==13) { // Wenn es sich um "Enter handelt, erstelle tspans"

				if(!ismultiTSpanElement) {

					//jQuery('#'+selectedElementId).html('');
					/* IE nimmt kein html*/
					jQuery('#'+selectedElementId).empty();


					//console.log(changeText);
					// Beim ersten tspan nimm alle vorherigen Zeichen und speicher sie im tspan
					var newTspan = document.createElementNS('http://www.w3.org/2000/svg',"tspan");

					if(jQuery('#renderSVG').hasClass('sf_mobile')) {
						newTspan.setAttributeNS(null,"x",600);
						newTspan.setAttributeNS(null,"dy",0);
						newTspan.setAttributeNS(null, "text-anchor", "middle");






					} else {
						newTspan.setAttributeNS(null,"x",150);
						newTspan.setAttributeNS(null,"dy",0);
					}



					newTspan.setAttributeNS(null,"class",'tspan');

					var textNodeTspan = document.createTextNode(changeText);
					newTspan.appendChild(textNodeTspan);

					jQuery('#'+selectedElementId).append(newTspan);



					if(jQuery('#renderSVG').hasClass('sf_mobile')) {
						//console.log('teste2');
						// Mobil soll es immer in der mitte sein, also berechne die neue höhe
						var textElementId = document.getElementById(selectedElementId);
						var bbox = textElementId.getBBox();
						var bboxHeight = bbox.height;
						var halfbboxHeight = bboxHeight / 2;
						var newTextBoxHeight = 855.5 - halfbboxHeight

						jQuery('#'+selectedElementId).attr('y', newTextBoxHeight);
					}



					// Da es sich hier nun um ein tspan Element handelt, muss auch anders vorgegangen werden.
					istSpanElement = true;
				}
			} else {

				if(istSpanElement) {

					// Es gibt bereits Text im Element und es handelt sich hier um ein tspan element, also muss der folgende Text auch ein tSpan sein
					/* IE nimmt kein .html*/
					//jQuery('#'+selectedElementId).html('');
					jQuery('#'+selectedElementId).empty();
					var splitted = changeText.split("\n");
					//console.log(splitted);

					var y = 0;
					for($i = 0; $i < splitted.length; $i++) {



						var newTspan = document.createElementNS('http://www.w3.org/2000/svg',"tspan");

						if(jQuery('#renderSVG').hasClass('sf_mobile')) {

							newTspan.setAttributeNS(null,"x",600);
							newTspan.setAttributeNS(null,"dy",y);
							newTspan.setAttributeNS(null, "text-anchor", "middle");
						} else {
							newTspan.setAttributeNS(null,"x",150);
							newTspan.setAttributeNS(null,"dy",y);
						}


						newTspan.setAttributeNS(null,"class",'tspan');

						var textNodeTspan = document.createTextNode(splitted[$i]);
						newTspan.appendChild(textNodeTspan);

						jQuery('#'+selectedElementId).append(newTspan);



						y = 150;
					}
					if(jQuery('#renderSVG').hasClass('sf_mobile')) {

						// Mobil soll es immer in der mitte sein, also berechne die neue höhe
						var textElementId = document.getElementById(selectedElementId);
						var bbox = textElementId.getBBox();
						var bboxHeight = bbox.height;
						var halfbboxHeight = bboxHeight / 2;
						var newTextBoxHeight = 855.5 - halfbboxHeight

						jQuery('#'+selectedElementId).attr('y', newTextBoxHeight);
					}

					ismultiTSpanElement = true;

				} else {

					/*
					* IE untersützt .html() nicht
					*/

					jQuery('#'+selectedElementId).empty();
					jQuery('#'+selectedElementId).append(changeText);
					jQuery('#'+selectedElementIdContainer).empty();
					jQuery('#'+selectedElementIdContainer).append(changeText);
				}
			}
				//console.log(changeText);

				//console.log(selectedElementId);


		}
		// Hinzufügen und Container ausblenden
		//jQuery('.new_text_container').hide();
		// Gleich Editierbarer Container anzeigen
		//jQuery('.edit_text_container').fadeIn(1000);







	})


	// Textbearbeiten rotieren
	jQuery('.rotateText').click(function() {

		var editableElement = jQuery('#'+selectedElementId);



		var width = editableElement.height() / 2;

		if(width == '0') {

			width = editableElement.attr('height') / 2;

		}

		var attr = jQuery(editableElement).attr('transform')
		var rotate = 90;

		// text wurde gerade erst erstellt, und hat noch kein transform attr
		if (typeof attr !== typeof undefined && attr !== false) {
			var transformArray = attr.split(' ');

			if(transformArray.length > 1) {

				var x = transformArray[4];
				var yLaenge = transformArray[5].length;
				var y = transformArray[5].substring(0,yLaenge-1);


				if (typeof transformArray[6] !== typeof undefined && transformArray[6] !== false) {
					var rotateAttr = transformArray[6];
					var rotateAttrLength = transformArray[6].length;
					var angle = rotateAttr.substring(rotateAttrLength-2);


					//console.log(transformArray[6]);

					if(angle == '90') {
						rotate = 180;
					} else if(angle == '80') {
						rotate = 270;
					} else if(angle == '70') {
						rotate = 0;
					}
				}



				//jQuery(editableElement).attr('transform', attr+' rotate(90,'+x+','+y+')')
				jQuery(editableElement).attr('transform', transformArray[0]+' '+transformArray[1]+' '+transformArray[2]+' '+transformArray[3]+' '+transformArray[4]+' '+transformArray[5]+' rotate('+rotate+','+width+','+width+')')
			} else {

					var angle = transformArray[0].substring(7,9);

					if(angle == '90') {
						rotate = 180;
					} else if(angle == '18') {
						rotate = 270;
					} else if(angle == '27') {
						rotate = 0;
					}

					if(jQuery('#renderSVG').hasClass('sf_mobile')) {
						var string = selectedElementId;
						substring = "text";
						if (string.indexOf(substring) !== -1) {
							width = jQuery(editableElement).attr('x');
							height = jQuery(editableElement).attr('y');

							//console.log(height)
							//console.log(width)
						} else {
								height = width;
						}
					} else {
						height = width;
					}

					jQuery(editableElement).attr('transform', 'rotate('+rotate+','+width+','+height+')')
			}

		} else {

			if(jQuery('#renderSVG').hasClass('sf_mobile')) {
				var string = selectedElementId;
				substring = "text";
				if (string.indexOf(substring) !== -1) {
					width = jQuery(editableElement).attr('x');
					height = jQuery(editableElement).attr('y');
				} else {
						height = width;
				}
			} else {
				height = width;
			}
			jQuery(editableElement).attr('transform', 'rotate('+rotate+','+width+','+height+')')
		}


		/*jQuery('#'+selectedElementId).attr('width', ww*scaleImage)
		jQuery('#'+selectedElementId).attr('height', hh*scaleImage)
		console.log(ww)
		console.log(hh)
		console.log(scaleImage);*/
		if(jQuery('#renderSVG').hasClass('sf_mobile')) {

			// Nur Für Bilder
			var string = selectedElementId,
			substring = "text";
			if (string.indexOf(substring) === -1) {
				fillImageFullContent();
			}



		}


	})


	// Text bearbeite, schriftart
	jQuery('.font_choose div').click(function() {

		var font = jQuery(this).attr('font');

		var editableElement = jQuery('#'+selectedElementId);
		jQuery(editableElement).attr('font-family', font)

		jQuery('.new_text_container .input textarea').css('font-family', font);

	})

	// Text bearbeite, schriftart
	jQuery('.new_text_container .color_choose div').click(function() {
		//console.log('test');
		var color = jQuery(this).attr('bgcolor');

		var editableElement = jQuery('#'+selectedElementId);
		jQuery(editableElement).attr('fill', color)

		//jQuery('.new_text_container .input textarea').css('color', color);
	})


	// Text bearbeiten bold
	jQuery('.new_text_container .text_edit .bold').click(function() {
		//console.log(selectedElementId);

		var editableElement = jQuery('#'+selectedElementId);
		var attr = editableElement.attr('font-weight');

		if (typeof attr !== typeof undefined && attr !== false) {
			if(attr == 'bold') {
				jQuery(editableElement).attr('font-weight', 'normal');
			} else {
				jQuery(editableElement).attr('font-weight', 'bold');
			}
		} else {
			jQuery(editableElement).attr('font-weight', 'bold');
		}
	})


	// Text bearbeiten
	jQuery('.new_text_container .text_edit .bold').click(function() {
		//console.log(selectedElementId);

		var editableElement = jQuery('#'+selectedElementId);
		var attr = editableElement.attr('font-weight');

		if (typeof attr !== typeof undefined && attr !== false) {
			if(attr == 'bold') {
				jQuery(editableElement).attr('font-weight', 'normal');
			} else {
				jQuery(editableElement).attr('font-weight', 'bold');
			}
		} else {
			jQuery(editableElement).attr('font-weight', 'bold');
		}
	})





	// Wenn der Text gespeichert wird, setze die Variablen zurück
	jQuery('.add_new_text').click(function() {
		istSpanElement = false;
		ismultiTSpanElement = false;

		// Blende das Textfeld aus und setze das Feld leer
		jQuery('#new_text').val('');
		jQuery('.new_text_container').hide();
	})

	// Löschen eines Textes
	jQuery('.new_text_container .deleteLayer').click(function() {
		if(DragTarget)
		{
			// Bild aus dem SVG löschen

			// remove() funktionier im IE nicht
			//DragTarget.remove();
			jQuery('#'+selectedElementId).remove();

			// Bild aus den Storages löschen
			jQuery('.storageTextSVG #'+selectedElementIdContainer).remove();
			jQuery('.new_text_container .input textarea').val('');
			jQuery('.new_text_container').hide();


			// Mobil muss dann wieder der Upload button eingefügt werden
			if(jQuery('#renderSVG').hasClass('sf_mobile')) {
				jQuery('.add_new_text_container').parent('.edit_mobile_element').show();
			}

		}
	})

	// Löschen eins Bildes
	jQuery('.edit_image_container .deleteLayer').click(function() {
		if(DragTarget)
		{
			// Bild aus dem SVG löschen

			// remove() funktionier im IE nicht
			//DragTarget.remove();
			jQuery('#'+selectedElementId).remove();

			// Bild aus den Storages löschen
			jQuery('.storageImageSVG #'+selectedElementIdContainer).remove();
			jQuery('.storageImageThumb .'+selectedElementId).remove();
			/* IE nimmt kein .html*/
			jQuery('.edit_image_container .image_container').empty();
			//jQuery('.edit_image_container .image_container').html('');
			jQuery('.edit_image_container').hide();


			// Mobil muss dann wieder der Upload button eingefügt werden
			if(jQuery('#renderSVG').hasClass('sf_mobile')) {

				jQuery('.upload_image_svg').parent('.edit_mobile_element').show();
			}


		}
	})

	// Bild "speichern"
	jQuery('.edit_image_container .save_picture').click(function() {
		jQuery('.edit_image_container').hide();
	})

	// Bild größer maken
	jQuery('.bigger').bind('mousedown', function() {

		if(DragTarget)
		{

			if(jQuery('#renderSVG').hasClass('sf_mobile')) {

				var size = jQuery('#'+selectedElementId).attr('font-size');
				size = parseInt(size);
				size += 10;
				jQuery('#'+selectedElementId).attr('font-size', size)

			} else {
				// Transformations
				var transformRequestObj=mySVG.createSVGTransform();
				var animTransformList=DragTarget.transform
				var transformList=animTransformList.baseVal

				transformRequestObj.setScale(1.03,1.03)

				transformList.appendItem(transformRequestObj)
				transformList.consolidate()
			}






		}
	})

	// Bild größer maken
	jQuery('.smaller').bind('mousedown', function() {

		if(DragTarget)
		{

			if(jQuery('#renderSVG').hasClass('sf_mobile')) {

				var size = jQuery('#'+selectedElementId).attr('font-size');
				size = parseInt(size);
				size -= 10;
				jQuery('#'+selectedElementId).attr('font-size', size)

			} else {
				// Transformations
				var transformRequestObj=mySVG.createSVGTransform();
				var animTransformList=DragTarget.transform
				var transformList=animTransformList.baseVal

				transformRequestObj.setScale(0.97,0.97)

				transformList.appendItem(transformRequestObj)
				transformList.consolidate()
			}


		}
	})


});









jQuery(document).ready(function(jQuery)
{

	var sfg_gewinnspiel = new SFGewinnspiel();

});

function SFGewinnspiel() {
  this.init();
};

SFGewinnspiel.prototype = {

	/* Alles wichtige was am Anfang gebraucht wird */
	init:function () {


		/* Aufrufen von wichtigen Funktionen */
		this.form();
		this.formvalidierung();
		this.parsleyCustomValidator();
		this.uploadIamge();
		this.formProgress();
		//this.setNewProgressLoading();
		this.navigateStepbyStep();
		this.formValiTranslate();
		this.subGrid();
		this.paginateSubscriber();
	},
	rotateSubImage:function () {


		jQuery('.roate_sub_single_pic .rotate').click(function() {

			var id = jQuery(this).attr('data-id');
			var rotateSubImage = jQuery.ajax({
					url : Custom.ajaxurl,
					type : 'post',
					data : {
						 action: 'roate_sub_image',
						 id : id
					},
					beforeSend: function( xhr ) {
						jQuery('.roate_sub_single_pic .fa-refresh').css('display', 'inline-block');
						jQuery('.roate_sub_single_pic .fa-repeat').hide();
					},
					error : function(error) {
						console.log(error)
						alert('Keine Berechtigung')
					}
			})

			rotateSubImage.done(function(response) {

				var container = jQuery('.image-preview .sub_single_pic .image_container');
				// Im Hintergrund werden die neuen Thumbs generiert, für die Vorschau muss js reichen
				if (container.hasClass('rotate90')) {
					container.removeClass('rotate90');
					container.addClass('rotate180');
				} else if(container.hasClass('rotate180')) {
					container.removeClass('rotate180');
					container.addClass('rotate270');
				} else if(container.hasClass('rotate270')) {
					container.removeClass('rotate270');
				} else if (!container.hasClass('rotate90') && !container.hasClass('rotate180') && !container.hasClass('rotate270')) {
					container.addClass('rotate90');
				}
				jQuery('.roate_sub_single_pic .fa-refresh').hide();
				jQuery('.roate_sub_single_pic .fa-repeat').show();

			})


		})
	},
	paginateSubscriber:function () {






		jQuery('.sf_load_more_container').on('click', function() {




			var sub_page = jQuery('.sf_sub_grid').attr('sub-page');
			if(typeof sub_page == typeof undefined || sub_page === false) {
				sub_page = 1;
			}

			that = this;
			var getMoreSubs = jQuery.ajax({
					url : Custom.ajaxurl,
					type : 'post',
					dataType: 'json',
					data : {
						 action: 'load_more_subs',
						 sub_page: sub_page
					},
					beforeSend: function( xhr ) {
							jQuery('.sf_load_more').hide();
							jQuery('.sf_load_more.loading').show();
					},
					error : function(error) {
						console.log(error)
						alert('Keine Berechtigung')
					}
			})
			getMoreSubs.done(function(response) {


					jQuery('.sf_sub_grid').append(response['html']);
					jQuery('.sf_sub_grid').attr('sub-page', parseInt(sub_page)+1);

					if(response['amount'] == '8') {
							jQuery('.sf_load_more').show();
					}
					jQuery('.sf_load_more.loading').hide();


			})
		})





	},
	subGrid:function () {
		/*var tape = jQuery('.sf_sub_grid .tape img').attr('src');
		var tape_blue = tape.replace('tape', 'tape_blue');

		jQuery('.sf_sub_grid .sub_single_pic').hover(function() {
			jQuery(this).find('.tape img').attr('src', tape_blue);
		}, function() {
			jQuery(this).find('.tape img').attr('src', tape);
		});

		tape.replace('tape','tape_blue');*/
	},
	formValiTranslate:function () {
		window.Parsley.addMessages('de', {
		  defaultMessage: "Die Eingabe scheint nicht korrekt zu sein.",
		  type: {
		    email:        "Die Eingabe muss eine gültige E-Mail-Adresse sein.",
		    url:          "Die Eingabe muss eine gültige URL sein.",
		    number:       "Die Eingabe muss eine Zahl sein.",
		    integer:      "Die Eingabe muss eine Zahl sein.",
		    digits:       "Die Eingabe darf nur Ziffern enthalten.",
		    alphanum:     "Die Eingabe muss alphanumerisch sein."
		  },
		  notblank:       "Die Eingabe darf nicht leer sein.",
		  required:       "Dies ist ein Pflichtfeld.",
		  pattern:        "Die Eingabe scheint ungültig zu sein.",
		  min:            "Die Eingabe muss größer oder gleich %s sein.",
		  max:            "Die Eingabe muss kleiner oder gleich %s sein.",
		  range:          "Die Eingabe muss zwischen %s und %s liegen.",
		  minlength:      "Die Eingabe ist zu kurz. Es müssen mindestens %s Zeichen eingegeben werden.",
		  maxlength:      "Die Eingabe ist zu lang. Es dürfen höchstens %s Zeichen eingegeben werden.",
		  length:         "Die Länge der Eingabe ist ungültig. Es müssen zwischen %s und %s Zeichen eingegeben werden.",
		  mincheck:       "Wählen Sie mindestens %s Angaben aus.",
		  maxcheck:       "Wählen Sie maximal %s Angaben aus.",
		  check:          "Wählen Sie zwischen %s und %s Angaben.",
		  equalto:        "Dieses Feld muss dem anderen entsprechen."
		});

		window.Parsley.setLocale('de');
	},
	validateSection:function (aktivesElement) {
		if (jQuery('#addNewSubForm').parsley().validate({group: 'block-' + aktivesElement})) {
			return true;
		} else {
			return false;
		}
	},
	navigateStepbyStep:function () {

		that = this;
		// Shortcode stepby step navi
		jQuery('.sf_form_navi_element').click(function(e) {
			e.preventDefault();
			var aktivesElement = parseInt(jQuery('.sf_form_navi_element.current').attr('data-nav'));
			var neuesElement = parseInt(jQuery(this).attr('data-nav'));


			// Es braucht keine Validierung, wenn der User zurück will.
			if(aktivesElement > neuesElement) {
				that.navigateTo(neuesElement);
			} else {
				// ist das neue Element, der nächste Slide, dann nimm die normale Funktion wie unten
				if(neuesElement == parseInt(aktivesElement)+1) {
					if(!jQuery('#addNewSubForm').hasClass('dontValidate')){
						if(that.validateSection(aktivesElement))
							that.navigateTo(neuesElement);
					} else {
						// Validierung wurde schon inline übernommen, zum Beispiel beim Bildupload, gibt es ein Success- Handler. Der soll nicht 2x aufgerufen werden.
						that.navigateTo(neuesElement);
						jQuery('#addNewSubForm').removeClass('dontValidate');
					}
				} else {
					/*
					* neuesElement ist nicht der nächste Slide => validierung fällt anders aus
					*/
					if(!jQuery('#addNewSubForm').hasClass('dontValidate')){

						// gehe jedes Element durch
						for (i = aktivesElement; i < neuesElement; i++) {

							// Wenn die Validierung fehlschlägt
							if(!that.validateSection(i)) {

								break;

							}
						}
						if(i != aktivesElement) {
							that.navigateTo(i);
						}


					} else {
						// Validierung wurde schon inline übernommen, zum Beispiel beim Bildupload, gibt es ein Success- Handler. Der soll nicht 2x aufgerufen werden.
						that.navigateTo(neuesElement);
						jQuery('#addNewSubForm').removeClass('dontValidate');
					}
				}
			}
		})
	},
	formProgress:function () {
		progressbar = jQuery('#progress_bar');
		max = progressbar.attr('max');
		progressTime = (1000 / max) * 1;
		progressValue = progressbar.val();

		// War nur zum anstoßen ohne Uplaod
		//that = this;
		//animateUploadProgress = setInterval(function() {
		//that.setNewProgressLoading();
		//}, progressTime);
	},
	setNewProgressLoading:function () {


		max = progressbar.attr('max');
		if(progressValue > max) {
			progressValue = max -1;
		}


		progressValue += 1;
		addValue = progressbar.val(progressValue);

		jQuery('.progress-value').empty();
		jQuery('.progress-value').append(progressValue + '%');
		/* IE nimmt kein .html*/
		//jQuery('.progress-value').html(progressValue + '%');
		var $ppc = jQuery('.progress-pie-chart'),
		deg = 360 * progressValue / 100;
		if (progressValue > 50) {
		$ppc.addClass('gt-50');
		}

		jQuery('.ppc-progress-fill').css('transform', 'rotate(' + deg + 'deg)');

		jQuery('.ppc-percents span').empty();
		jQuery('.ppc-percents span').append(progressValue + '%');
		/* IE nimmt kein .html*/
		//jQuery('.ppc-percents span').html(progressValue + '%');

		if (progressValue == max) {
		clearInterval(animateUploadProgress);
		}
	},
	uploadIamge:function () {
		that = this;
		if(typeof jQuery('#upload_image').parsley() !== "undefined") {
			jQuery('#upload_image').parsley().on('field:success', function() {

				// This callback will be called for the upload field that success validation.

				// Beim Klick auf den "Weiter"- Button soll keine erneute Validierung passieren.
				jQuery('#addNewSubForm').addClass('dontValidate');

				/*
				* Da es sich um ein Upload von nicht eingeloggten usern handelt, kann hier nicht wps attachment async-upload genutzt werden.
				*/
				/*
				* formData
				*/
				var fd = new FormData();
				var form = jQuery('#addNewSubForm');
				var file = jQuery(form).find('input[type="file"]');
				var individual_file = file[0].files[0];
				var nonce = jQuery(form).find('#nonce_upload').val();
				fd.append("file", individual_file);
				fd.append('action', 'sub_upload');
				fd.append('nonce', nonce);

				them = that;
				jQuery.ajax({
						type: 'POST',
						url: Custom.ajaxurl,
						data: fd,
						contentType: false,
						processData: false,
						beforeSend: function () {
							jQuery('#pbar').show();
							jQuery('.sf_form_upload_btn').hide();

						},
						xhr: function () {
								var xhr = jQuery.ajaxSettings.xhr();
								xhr.upload.onprogress = function (e) {
										if (e.lengthComputable) {
											var percantage = Math.round(e.loaded / e.total * 100);

											progressbar.attr('max', percantage);
											if (typeof(animateUploadProgress) != "undefined") {
													clearInterval(animateUploadProgress);
											}
											animateUploadProgress = setInterval(function() {
											them.setNewProgressLoading();
											}, progressTime);
										}
								};
								return xhr;
						},
						success: function(response){
							var res = response.split(';;');
							jQuery('#put_image_id').val(res[0]);

							jQuery('.image-preview').empty();
							jQuery('.image-preview').append(res[1]);
							/* IE nimmt kein .html*/
							//jQuery('.image-preview').html(res[1]);


							jQuery('#pbar').hide();
							jQuery('.sf_form_upload_btn').show(500);
							progressbar.attr('max', 0);
							progressbar.attr('value', 0);
							them.formProgress();
							them.rotateSubImage();
						}
				});

			});

		}
	},
	parsleyCustomValidator:function () {

		window.Parsley.addValidator('maxFileSize', {
		  validateString: function(_value, maxSize, parsleyInstance) {
		    if (!window.FormData) {
		      alert('You are making all developpers in the world cringe. Upgrade your browser!');
		      return true;
		    }
		    var files = parsleyInstance.$element[0].files;
		    return files.length != 1  || files[0].size <= maxSize * 1024;
		  },
		  requirementType: 'integer',
		  messages: {
				de: 'Bitte lade ein Bild mit maximal 3 MB hoch',
		    en: 'This file should not be larger than %s Kb',
		    fr: "Ce fichier est plus grand que %s Kb."
		  }
		});
	},
	navigateTo:function (index) {

		$navSections
			.removeClass('current')
			.eq(index)
				.addClass('current');
		// Mark the current section with the class 'current'
		$sections
			.removeClass('current')
			.eq(index)
				.addClass('current');
		// Show only the navigation buttons that make sense for the current section:
		jQuery('.form-navigation .previous').toggle(index > 0);
		var atTheEnd = index >= $sections.length - 1;
		jQuery('.form-navigation .next').toggle(!atTheEnd);
		jQuery('.form-navigation [type=submit]').toggle(atTheEnd);
	},
	formvalidierung:function () {

		/*
		* Select eines bildes
		*/
		jQuery('.imageSelect label').click(function() {
			jQuery('.imageSelect label').removeClass('active');
			jQuery(this).addClass('active');
			jQuery('.imageSelect label .image_overlay').stop().fadeTo('slow', 0);
			jQuery(this).find('.image_overlay').stop().fadeTo('slow', 1);
		});
		jQuery('.imageSelect label').hover(function() {
			jQuery(this).find('.image_overlay').stop().fadeTo('slow', 1);

		}, function() {
			if(!jQuery(this).hasClass('active')) {
				jQuery(this).find('.image_overlay').stop().fadeTo('slow', 0);
			}
		})




		$sections = jQuery('.form-section');
		$navSections = jQuery('.sf_form_navi_element');

	  function curIndex() {
	    // Return the current index by looking at which section has the class 'current'
	    return $sections.index($sections.filter('.current'));
	  }

		that = this;
	  // Previous button is easy, just go back
	  jQuery('.form-navigation .previous').click(function() {
	    that.navigateTo(curIndex() - 1);
	  });


	  // Next button goes forward iff current block validates
	  jQuery('.form-navigation .next').click(function() {
			if(!jQuery('#addNewSubForm').hasClass('dontValidate')){
				if (jQuery('#addNewSubForm').parsley().validate({group: 'block-' + curIndex()}))
					that.navigateTo(curIndex() + 1);
			} else {
				// Validierung wurde schon inline übernommen, zum Beispiel beim Bildupload, gibt es ein Success- Handler. Der soll nicht 2x aufgerufen werden.
				that.navigateTo(curIndex() + 1);
				jQuery('#addNewSubForm').removeClass('dontValidate');
			}
	  });

	  // Prepare sections by setting the `data-parsley-group` attribute to 'block-0', 'block-1', etc.
	  $sections.each(function(index, section) {
	    jQuery(section).find(':input').attr('data-parsley-group', 'block-' + index);
	  });
	  this.navigateTo(0); // Start at the beginning



	},
	form:function () {

		/*
		* Layout "STANDARD" versenden der Informationen
		*/
		jQuery('.sfgewinnspiel_main_container .formInputs form').submit(function(e) {
			e.preventDefault();

			/*
			* Formvalidierung
			* TODO: Muss noch gemacht werden
			*/


			/*
			* Form absenden - neuen Eintrag schreiben
			*/
			var nonce = jQuery('.sfgewinnspiel_main_container .formInputs form .nonce').attr("data-nonce");
			var subForm = jQuery( '.sfgewinnspiel_main_container .formInputs form' ).serializeArray();

			var lastElement = jQuery('li.sf_form_navi_element:last-of-type').attr('data-nav');
			if (jQuery('#addNewSubForm').parsley().validate({group: 'block-' + lastElement})) {
				var addTeilnehmer = jQuery.ajax({
						url : Custom.ajaxurl,
						type : 'post',
						data : {
							 action: 'add_new_sub',
							 nonce: nonce,
							 subForm: subForm
						},
						beforeSend: function( xhr ) {
							jQuery('#addNewSubForm').hide();
						},
						error : function(error) {
							console.log(error)
							alert('Keine Berechtigung')
						}
				})
			} else {
				return false;
			}


			addTeilnehmer.done(function(response) {
				//console.log(response);
				// Form ausblenden
				// Eventuell Cookie setzen?
				//jQuery('#addNewSubForm').hide(500);
				//var hoehe = jQuery('.sf_form_nav_step').height();
				//jQuery('.sf_form_nav_step').after().html('<div style="height:'+hoehe+'px;"></div>');




				var getDankScreen = jQuery.ajax({
						url : Custom.ajaxurl,
						type : 'post',
						data : {
							 action: 'show_dank_screen',
						},
						beforeSend: function( xhr ) {
						},
						error : function(error) {
							console.log(error)
							alert('Keine Berechtigung')
						}
				})
				getDankScreen.done(function(dank) {

						jQuery('.formInputs ').empty();
						jQuery('.formInputs ').append(dank);
						/* IE nimmt kein .html*/
						//jQuery('.formInputs ').html(dank);
						jQuery('html, body').animate({
				        scrollTop: jQuery(".sfgewinnspiel_main_container").offset().top
				    }, 2000);
				})

			})


		})
	},
}

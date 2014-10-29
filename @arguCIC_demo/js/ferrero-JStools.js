jQuery(document).ready(function($) {

	////////////////////////////////////
	// GESTION DES ANIMATIONS
	////////////////////////////////////

	// variables animations
	// phase initiale de contrôle
	var phase = 0;
	var maxPhase = 0;
	var nbPhases = 0;
	var pauseStatut = false;
	var phasesData = Array();
	var $model = false;
	var firstLaunch = true;
	var animateMode = "no";
	var memoPhases = Array();
	// touche de contrôle (espace = 32)
	var pauseKey = 32;
	var homeKey = 27;
	var rewKey = 37; // retour page précédente (a)
	var nexKey = 39; // retour page précédente (z)
	// url page suivante
	var nextUrl = $("a#fleche-droite").attr("href");
	// url page suivante
	var prevUrl = $("a#fleche-gauche").attr("href");
	// url page home
	var homeUrl = $("a#retourButton").attr("href");

	// console.log("nextUrl : ", nextUrl);
	// console.log("prevUrl : ", prevUrl);
	// console.log("homeUrl : ", homeUrl);

	var visible = 'off';
	// PHASE D'INITIALISATION
	var dataInit = function(elem) {
		visible = 'off';
		if($model == false) $model = $(elem);
		datt = Array();
		var phaseCourt = false;
		params = $(elem).attr("data-effect").split('|');

		for (var i = 0; i < params.length; i++) {
			if (params[i].substr(0,1) == "#") {
				// nouvelle phase
				phaseCourt = 'phase-'+params[i].substring(1);
				datt[phaseCourt] = Array();
			} else if (phaseCourt != false) {
				test = params[i].split(":");
				// données simples
				switch(test[0]) {
					case "i":
						datt[phaseCourt]['speed'] = parseInt(test[1]);
						break;
					case "d":
						datt[phaseCourt]['delay'] = parseInt(test[1]);
						break;
					case "s":
						datt[phaseCourt]['effect'] = test[1];
						if(test[1] == "fadeIn") visible = "on";
						if(test[1] == "fadeOut") visible = "off";
						break;
				}
				datt[phaseCourt]['visible'] = visible;
			}
		}
		$(elem).removeAttr("data-effect");
		$(elem).data("data-effect", datt);
		maxPhase = parseInt(phaseCourt.substring(6));
	}

	// init sprites
	if($("#sprite-phases-description").length > 0) {
		var dph = $("#sprite-phases-description").attr('data-phases').split('|');
		for (var i = 0 ; i < dph.length ; i++) {
			dcp = dph[i].split(':');
			phasesData[dcp[0]] = dcp[1];
		}
		$("#sprite-phases-description").removeAttr("data-phases");
		nbPhases = dph.length;
		// Run automatique
		if($("#sprite-phases-description").attr('data-run') == 1) pauseStatut = true;
			else pauseStatut = false;
		$("#sprite-phases-description").removeAttr("data-run");
		// Mode "sequ" ou "pause"
		animateMode = $("#sprite-phases-description").attr('data-mode');
		$("#sprite-phases-description").removeAttr("data-mode");
		// INIT Gestion des sprites/images
		var tx = "";
		$(".sprite").each(function(index) {
			dataInit(this);
			// console.log("data :", $(this).data("data-effect"));
			tx = tx+"index "+index+" : "+(maxPhase+1)+" phases\r\n";
		});
		// if($(".sprite").length > 0) {
		// 	console.log("Mode anim : ", animateMode);
		// 	console.log("Max phase : ", maxPhase);
		// 	console.log("Nb phases : ", nbPhases);
		// 	console.log("En cours  : ", phase);
		// 	console.log("phases :\r\n", tx);
		// }
	}

	var senss;
	// animation par séquence
	var animateSEQU = function(sens) {
		if(sens == "prev") senss = "prev";
			else senss = "next";
		// incrémentation/décrémentation
		if(senss == "prev") {
			phase--;
			if(phase < 0) return "change";
			var debut = phase+1;
			for (var i = debut; i <= maxPhase; i++) {
				$(".sprite").each(function() {
					datt = $(this).data("data-effect");
					if(datt['phase-'+phase]["visible"] == "off") {
						$(this).fadeOut(parseInt(datt['phase-'+phase]["speed"]));
						// console.log("Phase ", i+" off");
					} else {
						$(this).fadeIn(parseInt(datt['phase-'+phase]["speed"]));
						// console.log("Phase ", i+" on");
					}
				});
			}
		}
		if(senss == "next") {
			phase++;
			if(phase > maxPhase) return "change";
		}
		// console.log("Nouvelle phase", 'phase-'+phase);
		$(".sprite").each(function() {
			datt = $(this).data("data-effect");
			switch(datt['phase-'+phase]["effect"]) {
				case "fadeIn":
					$(this).fadeIn(parseInt(datt['phase-'+phase]["speed"]));
					// if(senss == "next") {$(this).fadeIn(parseInt(datt['phase-'+phase]["speed"]));}
					// if(senss == "prev") {$(this).fadeOut(parseInt(datt['phase-'+phase]["speed"]));}
					break;
				case "fadeOut":
					$(this).fadeOut(parseInt(datt['phase-'+phase]["speed"]));
					// if(senss == "next") {$(this).fadeOut(parseInt(datt['phase-'+phase]["speed"]));}
					// if(senss == "prev") {$(this).fadeIn(parseInt(datt['phase-'+phase]["speed"]));}
					break;
			}
		});
		return "no";
	}

	var res = "no";
	// évènement contrôle
	// $('body').prepend("<p id='cpt' style='position:absolute;top:0px;left:50px;'>Report</p>");
	$(document).keydown(function(event) {
		// $("#cpt").text(event.which + " = " + phase);
		switch(event.which) {
			case nexKey:
				event.preventDefault();
				switch(animateMode) {
					case "sequ":
						res = animateSEQU("next");
						if(res == "change") {
							// dernière animation passée -> go page suivante
							if(nextUrl != undefined) window.location.replace(nextUrl);
						}
						break;
					default:
						// sans animation
						if(nextUrl != undefined) window.location.replace(nextUrl);
						break;
				}
				break;
			case rewKey:
				event.preventDefault();
				// console.log("window.location.replace", prevUrl);
				switch(animateMode) {
					case "sequ":
						res = animateSEQU("prev");
						if(res == "change") {
							// dernière animation passée -> go page suivante
							if(prevUrl != undefined) window.location.replace(prevUrl);
						}
						break;
					default:
						// sans animation
						if(prevUrl != undefined) window.location.replace(prevUrl);
						break;
				}
				break;
			case homeKey:
				event.preventDefault();
				// console.log("window.location.replace", homeUrl);
				if(homeUrl != undefined) window.location.replace(homeUrl);
				break;
		}
	});

	$('.fadein').each(function() {
		$(this).css("display", "none");
		attr = $(this).attr("data-effect").split("|");
		$(this).delay(parseInt(attr[0])).fadeIn(parseInt(attr[1]));
	});

});


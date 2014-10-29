/* **************************************************** */
/* Récupération des donnes GET
/* **************************************************** */

	function extractUrlParams() {
		var t = location.search.substring(1).split('&');
		var f = [];
		for (var i=0; i<t.length; i++) {
			var x = t[ i ].split('=');
			f[x[0]]=x[1];
		}
		return f;
	}
	val = extractUrlParams();
	if(val["rupture"] == "ok") alert("Article en rupture !");

jQuery(document).ready(function($) {

	// Menu haut
	$("#menuHaut >li.reactif").on("mouseenter", function() {
		$(this).switchClass("off", "on", 200);
	});
	$("#menuHaut >li.reactif").on("mouseleave", function() {
		$(this).switchClass("on", "of", 200);
	});

	// Menu droite
	$("#menuDroite >a").on("mouseenter", function() {
		if(!$(this).hasClass("popactif")) $(this).css("background-position", "0px -85px");
	});
	$("#menuDroite >a").on("mouseleave", function() {
		if(!$(this).hasClass("popactif")) $(this).css("background-position", "0px 0px");
	});
	$(".popactif").css("background-position", "0px -85px");

	// Menu bas
	$("#menuBas >ul >li, #menuBas2 >ul >li").on("mouseenter", function() {
		if(!$(this).hasClass("MBon")) $(this).css("background-position", "0px -58px");
	});
	$("#menuBas >ul >li, #menuBas2 >ul >li").on("mouseleave", function() {
		if(!$(this).hasClass("MBon")) $(this).css("background-position", "0px -1px");
	});
	$(".MBon").css("background-position", "0px -58px");

	// // Menu vidéo
	// $(".optivid, .optivid2").on("mouseenter", function() {
	// 	$(this).css("background-position", "0px -33px");
	// });
	// $(".optivid, .optivid2").on("mouseleave", function() {
	// 	$(this).css("background-position", "0px 0px");
	// });
	// $(".optivid, .optivid2").hide();

	$(".optivid, .optivid2").on("click", function() {
		window.location($(this).attr("href"));
		return false;
	});



	/* **************************************************** */
	/* DIAPORAMAS ©2013 E.Dujardin Aequation-Webdesign v2.00
	/* **************************************************** */

	// if($("#videobloc").length) {
	// 	var tempo = parseInt($("input#tempo", this).attr("value"));
	// 	setTimeout(function() {
	// 		$(".optivid").fadeIn(1000);
	// 	}, tempo);
	// }

	if($("#popin02").length) {
		SUPERDIA();
		$("#popin02").dialog({
			modal: true,
			width: 831,
			height: 570,
			draggable: false,
			resizable: false,
			dialogClass: "noTitleStuff",
			position: ["center", 70]
		});
	}
	$("#popin01").on("clickoutside", function() {
		$(this).dialog("close");
	});
	$("#popin02").on("clickoutside", function() {
		$(this).dialog("close");
	});
});

function SUPERDIA() {
	if($(".AEdiaporama").length) {
		$.prototype.AEdiapo = function (param) {
			// param = objet contenant les paramètres :
			// timeL		: temps entre chaque diapo, en milliseconde
			// reaction		: commande de menu ("click", "mouseenter", etc.)
			// stopZone		: désactive (true) ou non (false) le diaporama si la souris est dessus (dans la div#nomdiapo)
			// end			: s'arrête à la fin du diaporama (true) ou tourne en boucle (false)
			// modei		: mode initial => true pour un diaporama qui tourne automatiquement / false pour non

			var objparent = this;
			this.nom = this.selector.substring(1); //Récupère le nom de l'id sans "#"
			// paramètres par défaut si non renseignés
			if(!param.timeL) param.timeL = "3000";
			if(!param.reaction) param.reaction = "mouseenter";
			if(!param.fadeSpeed) param.fadeSpeed = "400";
			if(param.stopZone == "true") param.stopZone = true; else param.stopZone = false;
			if(param.end == "true") param.end = true; else param.end = false;
			if(param.modei == "true") param.modei = true; else param.modei = false;
			// variables = paramètres en entrée
			this.TimeLaps = parseInt(param.timeL);
			this.clic = param.reaction;
			this.fadeSpeed = parseInt(param.fadeSpeed);
			this.desactive = param.stopZone;
			this.stoplink = param.stoplink;
			// autre variables
			this.mode = true;
			if(!param.modei) this.mode = false;
			this.compteur = 0;
			this.elem = new Array();
			this.test = false;			// ----> Mettre à true pour avoir le numéro de diapositive en haut à gauche (pour tests)
			this.menu = false;
			this.actif = "actif";		// --> style css pour élément de menu actif
			if(this.test) {
				$("<p id='"+this.nom+"test' style='position:absolute;top:4px;left:6px;height:14px;width:14px;background-color:#ddd;border:1px solid #666;text-align:center;line-height:14px;font-size:10px;overflow:hidden;color:#333;z-index:100000;'>?</p>").appendTo(this.selector);
			}
			if($(this.selector+" >ul")) this.menu = true;

			// nomme (id) les 2 containers : diaporama (div) et menu (ul)
			$(this.selector+" >div").first().attr("id", this.nom+"listpd");
			if(this.menu) $(this.selector+" >ul").first().attr("id", this.nom+"listpm");

			this.$listpd = $(this.selector+" div#"+this.nom+"listpd >div");
			if(this.menu) this.$listpm = $(this.selector+" ul#"+this.nom+"listpm >li");

			this.$listpd.each( function(a) {
				$(this).attr("id", objparent.nom+"pd"+a);
				$(this).css("position", "absolute");
				$(this).css("display", "none");
				// $(this).hide();
				if(a == objparent.compteur) $(this).show(1);
			});
			this.$listpm.each( function(a) {
				$(this).removeClass(objparent.actif);
				if(a == objparent.compteur) $(this).addClass(objparent.actif, 1);
			});
			
			if(this.menu) {
				if(this.menu) this.$listpm.each( function(a) {
					$(this).attr("id", objparent.nom+"pm"+a);
					objparent.elem[objparent.nom+"pm"+a] = a;
				});

				this.Spd = $(this.$listpd).length;
				this.Spm = $(this.$listpm).length;
				if((this.Spd > this.Spm) && (this.Spm > 0)) this.taille = this.Spm;
					else this.taille = this.Spd;

				$("ul#"+this.nom+"listpm >li").on(this.clic, function() {
					// objparent.mode = false;
					objparent.afficheElem($(this).attr("id").substring(objparent.nom.length + 2));
				});
			} else this.taille = $(this.$listpd).length;

			$(".recule").on('click', function() {
				objparent.reculeElem();
			});
			$(".avance").on('click', function() {
				objparent.avanceElem();
			});

			if(!this.desactive && param.modei) {
				$(this.selector).on("mouseenter", function() {objparent.mode = false;});
				$(this.selector).on("mouseleave", function() {objparent.mode = true;});
			}

			// fonctions
			// gestion du compteur
			this.CTRLcompteur = function() {
				$(".recule").show()
				$(".avance").show();
				if(this.compteur == this.taille-1) {
					if(objparent.stoplink) {
						$("<a href='"+objparent.stoplink+"'></a>").appendTo($(".avance").removeClass("avance").addClass("stoplink"));
					} else {
						$(".avance").hide();
					}
				} else {
					$(".stoplink").removeClass("stoplink").addClass("avance").empty();
				}

				if(this.compteur == 0) {
					$(".recule").hide();
				}

				if(this.compteur == this.taille) {
					if(!param.end) this.compteur = 0;
					else {
						objparent.mode = false;
						this.compteur  = this.taille - 1;
					}
				}
				if(this.compteur < 0) {
					if(!param.end) this.compteur = this.taille-1;
					else {
						objparent.mode = false;
						this.compteur  = 0;
					}
				}
			}
			this.avanceCompteur = function() {
				this.compteur++;
				this.CTRLcompteur();
			}
			this.reculeCompteur = function() {
				this.compteur--;
				this.CTRLcompteur();
			}
			this.indexCompteur = function(idx) {
				this.compteur = idx;
				this.CTRLcompteur();
			}
			// affichage de la diapositive niv
			this.afficheElem = function(niv) {
				this.indexCompteur(niv);
				$(this.selector+"pd"+this.compteur).fadeIn(this.fadeSpeed);
				for(this.i=0; this.i<this.taille; this.i++) {
					if(this.i != this.compteur) {
						$(this.selector+"pd"+this.i).fadeOut(this.fadeSpeed);
						if(this.menu) $(this.selector+"pm"+this.i).removeClass(this.actif, this.fadeSpeed/4, "easeInOutQuad");
					}
					if(this.menu) $(this.selector+"pm"+this.compteur).addClass(this.actif, this.fadeSpeed/4, "easeInOutQuad");
				}
				if(this.test) $("#"+this.nom+"test").html(this.compteur);
			}
			// gestion diaporama
			this.avanceElem = function() {
				objparent.avanceCompteur();
				objparent.afficheElem(objparent.compteur);
			}
			this.reculeElem = function() {
				objparent.reculeCompteur();
				objparent.afficheElem(objparent.compteur);
			}
			this.Diapo = function() {
				if((objparent.mode == true) && (param.modei)) objparent.avanceElem();
				// this.timeoutID = window.setTimeout(function(){this.Diapo();}, 3000);
				// window.setTimeout("Diapo()", objparent.TimeLaps);
			}

			// affiche le bloc actif
			this.afficheElem(this.compteur);
			// lance le diaporama...
			// Diapo();
			this.timeoutID = window.setInterval(function(){objparent.Diapo();}, this.TimeLaps);

		}

		// Création des différents diaporamas
		AEdia = new Array();
		$(".AEdiaporama").each( function(e) {
			this.parametres = new Array();
			var objparent = this;
			// attribution du nom (id) s'il n'en existe pas
			if(!$(this).attr("id")) {
				do testid = facticeText("Aequation"); while(!$("#"+testid));
				$(this).attr("id", testid);
			}
			this.where = "#"+$(this).attr("id");
			// récupération des paramètres (placés dans span.parametres >input)
			if($(this.where+" >span.parametres").html()) {
				$(this.where+" >span.parametres").css("display", "none");
				$(this.where+" >span.parametres >input").each( function() {
					objparent.parametres[$(this).attr("name")] = $(this).attr("value");
				});
			}
			// Création des diaporamas
			AEdia[e] = new $(this.where).AEdiapo(this.parametres);
		});
		// alert("Nombre de diaporamas = "+AEdia.length);
	}
}

// Retourne une phrase équivalente à tx en changeant tous les caractères de manière aléatoire
function facticeText(tx) {
	this.t = "";
	this.J = 0;
	this.l = tx.length;
	for(this.i=0; i<l; i++) {
		n1 = Math.round(Math.random() * 100);
		if(n1 < 33) J = 65 + Math.floor((Math.random() * 26));
		else if(n1 > 66) J = 48 + Math.floor((Math.random() * 10));
		else J = 97 + Math.floor((Math.random() * 10));
		t = t + String.fromCharCode(J);
	}
	return t;
}

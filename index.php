<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<title>ADV Constructor</title>
	<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" />
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" href="css/styles.css" type="text/css" />
</head>
<body>
	<h1><a href="index.php">ADV Constructor <span>v1.00</span></a> - <a href="?test=test"><span>test</span></a></h1>
<?php
// **********************************************************
// PROGRAMME
// **********************************************************
define("GENERATEFOLDER", "ADVgenerated");

echo("<div class='fenppale'>");
affMenu(ListFiles(null, "#@.+#"));
echo("</div>");

echo("<div class='fenppale'>");
$menu = ListFiles(null, "#@.+#");
$activ = ListFiles(GENERATEFOLDER, "#[^@].+#");
// print_r($activ);
$list = array();
foreach($activ as $nom) {
	if(in_array("@".$nom, $menu)) $list[] = $nom;
}
affActiv($list);
echo("</div>");

if($_GET['test']=='test') {
	$test = true;
} else {
	$test = false;
}
$dossPpal = null;

creationADV();

// **********************************************************
// FONCTIONNALITES
// **********************************************************

function ListFiles($path = null, $motif = "#.+#") {
	$arrayResult = array();
	$dir = __DIR__."/".$path;
	$dossiers = array_diff(scandir($dir), array('..', '.'));
	foreach ($dossiers as $nom) {
		if(preg_match($motif, $nom)) $arrayResult[] = $nom;
	}
	return $arrayResult;
}

function affMenu($dirs) {
	echo("<h2>Fichiers sources présents</h2>");
	echo("<form name='menu' method='post'>");
	echo("<ul class='listsources'>");
	foreach($dirs as $nom) {
		echo("<li><input type='submit' name='ADV' value='".$nom."' class='btn btn-xs btn-primary col-xs-2' /></li>");
	}
	echo("</ul>");
	echo("</form>");
}

function affActiv($dirs) {
	echo("<h2>Animations générées</h2>");
	echo("<ul class='listsources'>");
	foreach($dirs as $nom) {
		echo("<li><a href='".GENERATEFOLDER.'/'.$nom."/index.html' class='btn btn-xs btn-default col-xs-2 URLext' />".$nom."</a></li>");
	}
	echo("</ul>");
}

function creationADV() {
	global $dossPpal;
	if($_POST) {
		$OK = true;
		$dossPpal = $_POST["ADV"];
		echo("<h1>Génération de l'ADV</h1>");
		echo("<div class='fenppale'>");
		echo("<h2>Vérification des éléments</h2>");
		// Récupération du fichier de description XML
		$filesXML = ListFiles("/".$dossPpal."/architecture/", "#^[a-zA-Z0-9-_.]{1,}(\.)(xml)$#i");
		// affDump($filesXML);
		echo('<p>--&gt; Récupération du fichier de description</p>');
		if(file_exists($dossPpal."/architecture/".$filesXML[0])) {
			$description = simplexml_load_file($dossPpal."/architecture/".$filesXML[0]);
			// affDump($description);
			echo('<p class="ok">Fichier de description trouvé : '.$filesXML[0].'</p>');
		} else {
			echo('<p class="error">Le fichier nom du fichier de description doit être constité de lettres et/ou de chiffres et au format XML</p>');
			$OK = false;
		}

		if($OK) {
			// création du dossier ADVgenerated si non existant
			if(!file_exists(GENERATEFOLDER)) {
				if(@mkdir(GENERATEFOLDER, 0777)) echo('<p class="ok">Création du dossier /'.GENERATEFOLDER.'</p>');
					else echo('<p class="error">Erreur à la création du dossier /'.GENERATEFOLDER.'</p>');
			}
			// Préparation des dossiers
			$suivi = "";
			$dirs = array();
			$dd = $dirs[] = GENERATEFOLDER."/".str_replace("@", "", $dossPpal);
			$dirs[] = $dd."/html";
			$dirs[] = $dd."/images";
			$dirs[] = $dd."/video";
			$dirs[] = $dd."/css";
			// $dirs[] = $dd."/css/images";
			$dirs[] = $dd."/js";
			// Efface le dossier principal de destination s'il existe déjà
			if(file_exists($dd)) {
				echo('<p class="notice">Le dossier /'.$dd.' existe déjà...</p>');
				if(emptyDir($dd)) echo('<p class="notice">Suppression du dossier principal /'.$dd.'</p>');
					else die('<p class="error">Suppression du dossier /'.$dd.' impossible !</p>');
			}
			// Création des dossiers
			foreach($dirs as $nomdir)
				if(mkdir($nomdir, 0777)) echo('<p class="ok">Création du dossier /'.$nomdir.'</p>');
					else echo('<p class="error">Erreur à la création du dossier /'.$nomdir.'</p>');

			// Copie des fichiers CSS et JS
			echo('<p>--&gt; Duplication des dossier/fichiers externes (css, js...)</p>');
			$typfile = array("css", "js");
			foreach($typfile as $tf) {
				$list = ListFiles("/".$dossPpal."/".$tf."/", "#^[a-zA-Z0-9-_.]{1,}(\.)(".$tf."){1}$#i");
				foreach($list as $nomf) {
					if(copy($dossPpal."/".$tf."/".$nomf, $dd."/".$tf."/".$nomf)) {
						chmod($dd."/".$tf."/".$nomf, 0777);
						echo('<p class="ok">Copie du fichier '.$tf.'/'.$nomf.'</p>');
					} else echo('<p class="error">Erreur à la copie du fichier /'.$nomf.'</p>');
				}
			}
			// copie des images css
			if(file_exists($dossPpal."/css/images")) {
				if(mkdir($dd."/css/images", 0777)) {
					echo('<p class="ok">Création du dossier /css/images</p>');
					$listimg = ListFiles("/".$dossPpal."/css/images", "#^[a-zA-Z0-9-_.]{1,}(\.)(png|jpg|jpeg|gif)$#i");
					foreach($listimg as $nom) {
						// $nomD = str_replace(" ", "", $nom);
						if(copy($dossPpal."/css/images/".$nom, $dd."/css/images/".$nom)) {
							chmod($dd."/css/images/".$nom, 0777);
							echo('<p class="ok">Copie du fichier css/images/'.$nom.'('.$nom.')</p>');
						} else echo('<p class="error">Erreur à la copie du fichier css/images/'.$nom.'</p>');
					}
				} else echo('<p class="error">Erreur à la création du dossier /css/images</p>');
			}
			// Copie du dossier images
			echo('<p class="ok">Copie des images du dossier /images</p>');
			$listimg = ListFiles("/".$dossPpal."/images", "#^[a-zA-Z0-9-_. ]{1,}(\.)(png|jpg|jpeg|gif)$#i");
			foreach($listimg as $nom) {
				// $nomD = str_replace(" ", "", $nom);
				if(copy($dossPpal."/images/".$nom, $dd."/images/".$nom)) {
					chmod($dd."/images/".$nom, 0777);
					echo('<p class="ok">Copie du fichier images/'.$nom.'</p>');
				} else echo('<p class="error">Erreur à la copie du fichier images/'.$nom.'</p>');
			}
			// Copie du dossier videos
			echo('<p class="ok">Copie des images du dossier /video</p>');
			$listimg = ListFiles("/".$dossPpal."/video", "#^[a-zA-Z0-9-_. ]{1,}(\.)(mov|swf|flv|wmv|mp4|webm|ogv)$#i");
			foreach($listimg as $nom) {
				if(copy($dossPpal."/video/".$nom, $dd."/video/".$nom)) {
					chmod($dd."/video/".$nom, 0777);
					echo('<p class="ok">Copie du fichier video/'.$nom.'</p>');
				} else echo('<p class="error">Erreur à la copie du fichier video/'.$nom.'</p>');
			}
			// Copie du fichier favicon.ico et autres…
			$fichiers = array("favicon.ico");
			foreach($fichiers as $fichier) {
				echo('<p class="ok">Copie de fichiers</p>');
				if(copy($dossPpal."/".$fichier, $dd."/".$fichier)) {
					echo('<p class="ok">Copie du fichier '.$fichier.'</p>');
				} else echo('<p class="error">Erreur à la copie du fichier '.$fichier.'</p>');
			}

			require_once("aequickxml.class.php");
			// $fichier = "structure.xml";
			$cpt = 10000;
			$description = AeQuickXML::parseX(@simplexml_load_file($dossPpal."/architecture/".$filesXML[0]));
			// affDump("Contenu du fichier : ", $description);
			// Création basique de PAGES ORIGINALES (pas des copies)
			foreach($description['pages'][0]['child']['page'] as $item) if(!isset($item['attr']['copie'])) {
				$data = basicpage($item, $data);
				$data = calculepage($item, $data);
			}
			// Création basique de PAGES en COPIE
			foreach($description['pages'][0]['child']['page'] as $item) if(isset($item['attr']['copie'])) {
				$data = basicpage($item, $data);
				$data = calculepage($item, $data);
			}
			// Récupération des données de MENUS
			foreach($description['menus'][0]['child']['menu'] as $item) {
				$nomPAGE = $item['attr']['nom'];
				$data['menus'][$nomPAGE]['modele'] = $item['attr']['modele'];
				foreach($item['child']['item'] as $num => $val) {
					foreach($val["attr"] as $n2 => $v3) $data['menus'][$nomPAGE]['items'][$num][$n2] = $v3;
				} 
			}
			affDump("Données envoyées Twig : ", $data);

			// Génération des pages d'après modèles TWIG
			echo("<h2>Génération de l'ADV <span>".$dossPpal."</span> : commencement...</h2>");
			echo("<p>Templates : ".$dossPpal.'/templates</p>');
			echo("<p>Structure : ".$dossPpal."/architecture/".$filesXML[0]);
			require_once('Twig-1.12.2/lib/Twig/Autoloader.php');
			Twig_Autoloader::register();
			$loader = new Twig_Loader_Filesystem($dossPpal.'/templates');
			$twig = new Twig_Environment($loader, array('charset' => 'utf-8','cache' => false));

			foreach($data['pages'] as $nom => $details) {
				$rendu = $twig->render($details['modele'], array("page" => $details, "pages" => $data['pages'], "menus" => $data['menus']));
				$suivi = $suivi."<div class='fenppale close'><h3>".$nom." ( ".$details['url']." )</h3><p>".nl2br(htmlspecialchars($rendu))."</p></div>";
				if(false != file_put_contents($dd."/".$details['url'], $rendu)) {
					chmod($dd."/".$details['url'], 0777);
					echo('<p class="ok">Création réussie pour le fichier '.$details['url'].'</p>');
				} else echo("<h2 class='error'>Echec lors de la création du fichier ".$details['url']."</h2>");
			}
		} else {
			echo("<h2 class='error'>Eléments manquants : impossible de créer l'ADV<br />Consultez les erreurs ci-dessus.</h2>");
		}
		echo("</div>");
		// Affichage du contenu des fichiers générés
		echo("<h1>Fichiers générés : contenus</h1>");
		echo $suivi; // Affichage des templates créés
	}
}

function affDump($nom, $data) {
	global $test;
	if($test) {
		echo("<div class='fenppale'><h3>".$nom."</h3><p class='close'>");
		echo("<pre style='width:100%;'>");
		print_r($data);
		echo("</pre>");
		echo("</p></div>");
	}
}

function affAutre($nom, $data) {
	echo("<div class='fenppale'><h3>".$nom."</h3><p class='close'>".nl2br(htmlspecialchars($data))."</p></div>");
}

function emptyDir($dir) {
	$r = true;
	if(file_exists($dir)) {
		$lf = ListFiles("/".$dir);
		foreach ($lf as $file) {
			if(is_file($dir.'/'.$file)) if(!unlink($dir.'/'.$file)) $r = false;
			if(is_dir($dir.'/'.$file)) if(!emptyDir($dir.'/'.$file)) $r = false;
		}
		if(!rmdir($dir)) $r = false;
	} else $r = false;
	return $r;
}

function basicpage($item, $data) {
	global $cpt;
	$nomPAGE = $item['attr']['nom'];
	$data['pages'][$nomPAGE]['modele'] = $item['attr']['modele'];
	$data['pages'][$nomPAGE]['nom'] = $nomPAGE;
	$data['pages'][$nomPAGE]['locate'] = "html/";
	$data['pages'][$nomPAGE]['go'] = "../";
	$data['pages'][$nomPAGE]['html'] = $nomPAGE."_".$cpt++.".html";
	$data['pages'][$nomPAGE]['url'] = "html/".$data['pages'][$nomPAGE]['html'];
	if($item['attr']['default'] == "default") {
		$data['defaultpage'] = $nomPAGE;
		$data['pages'][$nomPAGE]['html'] = $item['attr']['modele'];
		$data['pages'][$nomPAGE]['locate'] = "";
		$data['pages'][$nomPAGE]['default'] = "default";
		$data['pages'][$nomPAGE]['go'] = "";
		$data['pages'][$nomPAGE]['url'] = $item['attr']['modele'];
	}
	return $data;
}

function calculepage($item, $data) {
	global $dossPpal;
	$nomPAGE = $item['attr']['nom'];
	// data
	foreach($item['child']['data'] as $it2) {
		switch($it2['attr']['nom']) {
			case "refresh" :
				$cut = explode("|", $it2['attr']["val"]);
				$data['pages'][$nomPAGE]['refresh']['sec'] = $cut[0];
				$data['pages'][$nomPAGE]['refresh']['redir'] = $cut[1];
			break;
			case "diaporama" :
				foreach($it2['attr'] as $nom => $at)
					$data['pages'][$nomPAGE]['diaporama'][$nom] = $at;
				foreach($it2['child']['diapo'] as $key => $at3) {
					foreach($at3['attr'] as $n5 => $at4) {
						$data['pages'][$nomPAGE]['diaporama']['diapos'][$key][$n5] = $at4;
					}
				}
			break;
			default :
				$data['pages'][$nomPAGE]['data'][$it2['attr']["nom"]] = $it2['attr']["val"];
			break;
		}
	}
	// menus
	$i = 0;
	if(isset($item['child']['menu'])) foreach($item['child']['menu'] as $n => $it2) {
		$nom2 = $it2['attr']['nom'];
		if($nom2 === null) $nom2 = $n.$i++;
		$data['pages'][$nomPAGE]['menu'][$nom2]["modele"] = $it2["attr"]["modele"];
		foreach($it2['child']['item'] as $nom3 => $it3) {
			$nom44 = $it3['attr']['nom'];
			// if($nom44 === null) $nom44 = $nom4;
			foreach($it3["attr"] as $nom4 => $val4) {
				$data['pages'][$nomPAGE]['menu'][$nom2]['item'][$nom44][$nom4] = $val4;
			}
		}
	}
	// templates
	$i = 0;
	if(isset($item['child']['templates'])) foreach($item['child']['templates'] as $n => $it2) {
		// $html = $it2['attr']['nom'];
		// if($nom2 === null) $nom2 = $n.$i++;
		// $data['pages'][$nomPAGE]['templates'][$nom2]["fichier"] = $it2["attr"]["fichier"];
		foreach($it2['child']['html'] as $nom3 => $it3) {
			// $nom44 = $it3['attr']['nom'];
			foreach($it3["attr"] as $nom4 => $val4) {
				$data['pages'][$nomPAGE]['templates'][$i++][$nom4] = $val4;
			}
		}
	}
	// animations
	if(isset($item['child']['animation'])) {
		$Zindex = 100;
		$ZItab = array();
		// récupération de phases
		if(isset($item['child']['animation'][0]['attr']['phases'])) {
			$data['pages'][$nomPAGE]['animation']['phases']['string'] = $item['child']['animation'][0]['attr']['phases'];
			$phases = explode("|", $item['child']['animation'][0]['attr']['phases']);
			foreach($phases as $phaz) {
				$ph = explode(":", $phaz);
				// $data['pages'][$nomPAGE]['animation']['phases'][substr($ph[0], 1)] = $ph[1];
				$data_phases[substr($ph[0], 1)] = $ph[1];
			}
			$data['pages'][$nomPAGE]['animation']['phases']['data'] = $data_phases;
		}
		// RUN
		if(isset($item['child']['animation'][0]['attr']['run'])) {
			if(strtolower($item['child']['animation'][0]['attr']['run']) === "true")
				$data['pages'][$nomPAGE]['animation']['run'] = true;
				else $data['pages'][$nomPAGE]['animation']['run'] = false;
		} else $data['pages'][$nomPAGE]['animation']['run'] = false;
		// MODE
		if(isset($item['child']['animation'][0]['attr']['mode'])) {
			$data['pages'][$nomPAGE]['animation']['mode'] = strtolower($item['child']['animation'][0]['attr']['mode']);
		} else $data['pages'][$nomPAGE]['animation']['mode'] = "pause";
		// sprites
		foreach($item['child']['animation'][0]['child'] as $nsprite => $theSprite) {
			foreach($theSprite as $n => $it2) {
				// nom du sprite
				$nom2 = null;
				if(isset($it2['attr']['nom'])) $nom2 = $it2['attr']['nom'];
				if($nom2 === null) $nom2 = $nsprite."-".$n;
				$data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]['nom'] = $nom2;
				// attributs du sprite
				foreach($it2['attr'] as $nom3 => $attr3) {
					switch ($nom3) {
						case 'image':
							$image = $attr3;
							$data['pages'][$nomPAGE]['animation'][$nsprite][$nom2][$nom3] = $data['pages'][$nomPAGE]['go'].$attr3;
							break;
						case 'animate':
							$data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]['animate_origin'] = $attr3;
							$anim = explode("|", $attr3);
							$data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["phases"] = array();
							foreach($anim as $val) {
								if(substr($val, 0, 1) !== "#") {
									// descriptif
									$sep = explode(":", $val);
									if($sep[0] === "i") {
										$tv = "speed";
										$vv = intval($sep[1]);
									} else {
										$tv = "effect";
										$vv = $sep[1];
									}
									$data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["phases"][$nn][$tv] = $vv;
								} else {
									// num de phase
									$nn = substr($val, 1);
									// $data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["phases"][$nn]["delay"] = $delay;
								}
							}
							foreach($data_phases as $nphaz => $phaz) {
								if(!isset($data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["phases"][$nphaz])) {
									$data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["phases"][$nphaz]["effect"] = "delay";
									$data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["phases"][$nphaz]["speed"] = 0;
									$data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["phases"][$nphaz]["delay"] = $phaz;
								} else {
									$dl = $phaz - $data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["phases"][$nphaz]["speed"];
									if($dl < 0) {
										$data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["phases"][$nphaz]["speed"] = $phaz;
										$data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["phases"][$nphaz]["delay"] = 0;
									} else {
										$data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["phases"][$nphaz]["delay"] = $dl;
									}
								}
							}
 							break;
						case 'zindex':
							$ZI = intval($attr3);
							while(in_array($ZI, $ZItab)) {$ZI++;}
							$ZItab[] = $data['pages'][$nomPAGE]['animation'][$nsprite][$nom2][$nom3] = $ZI;
							break;
						default:
							$data['pages'][$nomPAGE]['animation'][$nsprite][$nom2][$nom3] = $attr3;
							break;
					}
				}
				// tailles automatiques si non précisées
				if((!isset($data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["tailleX"])) && (!isset($data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["tailleY"]))) {
					echo("<p style='color:orange;'>Calcul auto d'image : ".$dossPpal."/".$image."</p>");
					$imageSize = getimagesize($dossPpal."/".$image);
					$data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["tailleX"] = $imageSize[0];
					$data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["tailleY"] = $imageSize[1];
				}
				// z-index auto si non précisé
				if(!isset($data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["zindex"])) {
					while(in_array($Zindex, $ZItab)) {$Zindex++;}
					$ZItab[] = $data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["zindex"] = $Zindex++;
				}
				// reconstruction de la description sous forme de texte
				$str = "";
				ksort($data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["phases"]);
				foreach($data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]["phases"] as $nphaz => $phaz) {
					$str[$nphaz] = "#".$nphaz."|s:".$phaz["effect"]."|i:".$phaz['speed']."|d:".$phaz['delay'];
				}
				ksort($str);
				$data['pages'][$nomPAGE]['animation'][$nsprite][$nom2]['animate'] = implode("|", $str);
			}
		}
	}
	return $data;
}

?>
	<script src="js/jquery-1.9.1.js"></script>
	<script src="js/jquery-ui.js"></script>
	<script src="js/jquery-ui.js"></script>
	<script>
		jQuery(document).ready(function($) {
			$(".close >h3").on("click", function() {
				$(">p:first", $(this).parent()).slideDown();
			});
			$(".close >p").on("click", function() {
				$(this).slideUp();
				return false;
			});
			$(".URLext").on("click", function(event) {
				URL = $(this).attr("href");
				if(URL == undefined) URL = $(">a", this).first().attr("href");
				// alert(URL);
				window.open(URL);
				event.preventDefault();
				return false;
			});
		});
	</script>
</body>
</html>


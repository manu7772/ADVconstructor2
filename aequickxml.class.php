<?php
###########################################
### VERSION	1.00						###
### NOM		AeQuickXML					###
### DATE	07/04/2013					###
### AUTEUR	Emmanuel Dujardin			###
###########################################

/* ************************************* */
/* AeQuickXML      				         */
/* ************************************* */
CLASS AeQuickXML {

	public static $_fXML; //nom du document XML
	public static $_XMLdata;
	public static $_XMLrech;

	public static $_ModePars = false;	// Mode de renvoie : objet (false) ou array (true)

	### Construction de classe
	### $fXML = fichier xml
	### $recherche = chaîne de recherche des données XML
	function __construct($fXML=null, $recherche=null) {
		if($fXML && file_exists($fXML)) {
			self::$_fXML = $fXML;
			self::loadSXD(self::$_fXML, $recherche);
		}
	}

	public static function parsMode($mode) {
		if($mode == false || $mode == true) self::$_ModePars = $mode;
	}
	
	### Charge le fichier $fXML
	### le filtre $recherche permet de ne renvoyer que les résultats voulus
	public static function loadSXD($fXML, $recherche=null) {
		self::$_XMLdata = @simplexml_load_file($fXML);
		if(is_object(self::$_XMLdata)) {
			if(self::$_ModePars) {
				if(!is_null($recherche)) return self::parseX(self::$_XMLdata->xpath($recherche));
					else return self::parseX(self::$_XMLdata);
			} else {
				if(!is_null($recherche)) return self::$_XMLdata->xpath($recherche);
					else return self::$_XMLdata;
			}
		}
	}

	### Renvoie directement la valeur (string) du PREMIER élément trouvé dans $recherche
	public static function ValFist($fXML, $recherche) {
		self::$_XMLdata = @simplexml_load_file($fXML);
		if(is_object(self::$_XMLdata)) {
			$r = self::$_XMLdata->xpath($recherche);
			$r = trim((string) $r[0]);
		} else return false;
		return $r;
	}

	### Renvoie le tableau (array) des éléments trouvés dans $recherche
	### sans tenir compte des éléments enfants
	public static function ValAllFirst($fXML, $recherche) {
		self::$_XMLdata = @simplexml_load_file($fXML);
		if(is_object(self::$_XMLdata)) {
			$r = array();
			$a = self::$_XMLdata->xpath($recherche);
			foreach($a as $vr) {
				$r[] = trim((string) $vr[0]);
			}
		} else return false;
		return $r;
	}

	### Parse des données XML (total)
	public static function parseX($a) {
		if($a != null) {
			$tb = array();
			foreach($a as $num => $ojbc) $tb[$ojbc->getName()][] = self::parss2($ojbc);
			return $tb;
		} else return false;
	}
	private static function parss2($a) {
		$tb = array();
		$att = $a->attributes();if(sizeof($att) > 0) foreach($att as $natt => $vatt) $tb["attr"][$natt] = trim((string) $vatt);
		$tb["txt"] = trim((string) $a);
		if($a->count() > 0) foreach($a->children() as $nom1 => $val1) $tb["child"][$nom1][] = self::parss2($val1);
		return $tb;
	}


}

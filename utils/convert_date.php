<?php

function convert_date($timestamp,$type) {
	
	if($type=='ilya') {
		$timestamp_act = time();
		$diff = $timestamp_act-$timestamp;
		if($diff<3600) {
			$return_date = 'il y a '.floor($diff/60).' min';
		}
		if($diff >= 3600 && $diff < 3600*24) {
			$return_date = 'il y a '.floor($diff/3600).'h';
		}
		if($diff >= (3600*24) && $diff < (3600*24*7)) {
			$return_date = 'il y a '.floor($diff/(3600*24)).'j';
		}
		if($diff >= (3600*24*7) && $diff < (3600*24*7*4)) {
			$return_date = 'il y a '.floor($diff/(3600*24*7)).'sem';
		}
		if($diff >= (3600*24*7*4)) {
			$return_date = 'il y a '.floor($diff/(3600*24*7*4)).'mois';
		}
		
	} else if($type=='rss') {
		$jour_n = date('d',$timestamp);
		$jour_n = str_replace('0','',$jour_n);
		$jour_m = date('l',$timestamp);
		$jour_m = $jour_m[0].$jour_m[1].$jour_m[2];
		$mois = date('F',$timestamp);
		$mois = $mois[0].$mois[1].$mois[2];
		$annee = date('Y',$timestamp);
		$heure = date('H',$timestamp);
		$minute = date('i',$timestamp);
		$seconde = date('s',$timestamp);
		
		$return_date = $jour_m.', '.$jour_n.' '.$mois.' '.$annee.' '.$heure.':'.$minute.':'.$seconde.' GMT';
		
	} else {		
		
		$jour_brut = date('d',$timestamp);
		$tab_r = array('01','02','03','04','05','06','07','08','09');
		$tab_r2 = array('1','2','3','4','5','6','7','8','9');
		$jour = str_replace($tab_r,$tab_r2,$jour_brut);
		$mois = date('m',$timestamp);
			if($mois=='01') $mois = 'janvier';
			if($mois=='02') $mois = 'fevrier';
			if($mois=='03') $mois = 'mars';
			if($mois=='04') $mois = 'avril';
			if($mois=='05') $mois = 'mai';
			if($mois=='06') $mois = 'juin';
			if($mois=='07') $mois = 'juillet';
			if($mois=='08') $mois = 'août';
			if($mois=='09') $mois = 'septembre';
			if($mois=='10') $mois = 'octobre';
			if($mois=='11') $mois = 'novembre';
			if($mois=='12') $mois = 'décembre';
		$annee = date('Y',$timestamp);
		$heure = date('H',$timestamp);
		$minute = date('i',$timestamp);
		
		$tab_petit_mois = array(
			'janvier' => '1',
			'fevrier' => '2',
			'mars' => '3',
			'avril' => '4',
			'mai' => '5',
			'juin' => '6',
			'juillet' => '7',
			'août' => '8',
			'septembre' => '9',
			'octobre' => '10',
			'novembre' => '11',
			'décembre' => '12'
		);
		
		if($type=='grand')	$return_date = $jour.' '.$mois.' '.$annee.' à '.$heure.'h'.$minute;
		if($type=='petit')	$return_date = $jour.' '.$mois.' '.$annee;
		if($type=='w3c') $return_date = $annee.'-'.date('m',$timestamp).'-'.$jour_brut;
		if($type=='petit_mois') $return_date = $jour.'/'.$tab_petit_mois[$mois].'/'.$annee.' ('.$heure.'h'.$minute.')';
	}
	
	return $return_date;
}

?>
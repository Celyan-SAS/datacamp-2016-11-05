<?php
$file = $argv[1];
$outfile = $file . '-out2.csv';
$bureau = 0;

if( !file_exists( $file ) ) {
	echo "Pas trouvé fichier $file\n";
	exit();
}

if( !$handle = fopen( $file, 'r' ) ) {
	echo "Pas pu ouvrir fichier $file en entrée\n";
	exit();
}

if( !$outhandle = fopen( $outfile, 'w' ) ) {
	echo "Pas pu ouvrir fichier $outfile en sortie\n";
	exit();
}

echo "Traitement du fichier $file - sortie dans $outfile\n";

while( ( $line = fgets( $handle, 4096 ) ) !== false ) {
	
	if( preg_match( '/^\s*$/', $line ) )
		continue;
	
	if( preg_match( '/^\s*Bureau\s+n(.+?)(\d+)\s+/i', $line, $matches ) ) {
		$bureau = $matches[2];
		echo "Trouvé bureau $bureau\n";
		continue;
	}
	
	$split_str = '-—';
	$s='—';
	//echo unpack('V', iconv('UTF-8', 'UCS-4LE', $s))[1];
	$split = preg_split( '/\s*[\-—_]\s+/u', $line );
	
	$rue = $split[0];
	$c_debut = 0;
	$c_fin = 0;
	$debut_pair_rep = '';
	$fin_pair_rep = '';
	$debut_impair_rep = '';
	$fin_impair_rep = '';
	if( 2 == count( $split ) && preg_match( '/total/i', $split[1] ) ) {
		
		$debut_pair = 0;
		$fin_pair = 9998;
		$debut_impair = 1;
		$fin_impair = 9999;
		
		/*
		$q = $debut_pair . ' ' .
				$rue . ' ' .
				'Nogent-sur-Marne';
		$req = 'http://api-adresse.data.gouv.fr/search/?q=' . urlencode( $q );
		$r = file_get_contents( $req );
		$o = json_decode( $r );
		if( $o->features[0]->properties->city == 'Nogent-sur-Marne' )
			$c_debut = implode( ';', $o->features[0]->geometry->coordinates );
		//var_dump( $o ); exit;
			
		$q = $fin_impair . ' ' .
				$rue . ' ' .
				'Nogent-sur-Marne';
		$req = 'http://api-adresse.data.gouv.fr/search/?q=' . urlencode( $q );
		$r = file_get_contents( $req );
		$o = json_decode( $r );
		if( $o->features[0]->properties->city == 'Nogent-sur-Marne' )
			$c_fin = implode( ';', $o->features[0]->geometry->coordinates );
		*/
		
	} elseif( preg_match( '/[il]mpair/i', $split[2] ) ) {
		
		$split[1] = preg_replace( '/début/ui', 1, $split[1] );
		$split[1] = preg_replace( '/[àa]\s*la\s*[ft]in/ui', 'au 9999', $split[1] );
		$split[1] = preg_replace( '/[àa]\s*la\s+ﬁn/ui', 'au 9999', $split[1] );
		if( preg_match( '/du\s*(\d+(?:\sB)?)\s*au\s*(\d+(?:\sB)?)/i', $split[1], $matches ) ) {
			
			$debut_impair = $matches[1];
			$fin_impair = $matches[2];
			
			if( preg_match( '/(\D+)/', $debut_impair, $rematches ) ) {
				$debut_impair_rep = $rematches[1];
			}
			/*
			$q = $debut_impair . ' ' .
				$rue . ' ' .
				'Nogent-sur-Marne';
			$req = 'http://api-adresse.data.gouv.fr/search/?q=' . urlencode( $q );
			$r = file_get_contents( $req );
			$o = json_decode( $r );
			if( $o->features[0]->properties->city == 'Nogent-sur-Marne' )
				$c_debut = implode( ';', $o->features[0]->geometry->coordinates );
			
			$q = $fin_impair . ' ' .
					$rue . ' ' .
					'Nogent-sur-Marne';
			$req = 'http://api-adresse.data.gouv.fr/search/?q=' . urlencode( $q );
			$r = file_get_contents( $req );
			$o = json_decode( $r );
			if( $o->features[0]->properties->city == 'Nogent-sur-Marne' )
				$c_fin = implode( ';', $o->features[0]->geometry->coordinates );
			*/
			
		} else {
			$debut_impair = '*err*';
			$fin_impair = '*err*';
		}		
		$debut_pair = 0;
		$fin_pair = 0;

	} else {
		
		$split[1] = preg_replace( '/début/ui', 0, $split[1] );
		$split[1] = preg_replace( '/[àa]\s*la\s*[ft]in/ui', 'au 9998', $split[1] );
		$split[1] = preg_replace( '/[àa]\s*la\s+ﬁn/ui', 'au 9998', $split[1] );
		if( preg_match( '/du\s*(\d+(?:\sB)?)\s*au\s*(\d+(?:\sB)?)/i', $split[1], $matches ) ) {
			
			$debut_pair = $matches[1];
			$fin_pair = $matches[2];
			
			/*
			$q = $debut_pair . ' ' .
					$rue . ' ' .
					'Nogent-sur-Marne';
			$req = 'http://api-adresse.data.gouv.fr/search/?q=' . urlencode( $q );
			$r = file_get_contents( $req );
			$o = json_decode( $r );
			if( $o->features[0]->properties->city == 'Nogent-sur-Marne' )
				$c_debut = implode( ';', $o->features[0]->geometry->coordinates );
				
			$q = $fin_pair . ' ' .
					$rue . ' ' .
					'Nogent-sur-Marne';
			$req = 'http://api-adresse.data.gouv.fr/search/?q=' . urlencode( $q );
			$r = file_get_contents( $req );
			$o = json_decode( $r );
			if( $o->features[0]->properties->city == 'Nogent-sur-Marne' )
				$c_fin = implode( ';', $o->features[0]->geometry->coordinates );
			*/
			
		} else {
			$debut_pair = '*err*';
			$fin_pair = '*err*';
		}		
		$debut_impair = 0;
		$fin_impair = 0;		
	}
	
	$outdata = array(
		'',
		94052,
		$bureau,
		$rue,
		$debut_pair,
		$fin_pair,
		$debut_impair,
		$fin_impair,
		//$c_debut,
		//$c_fin
	);
	
	$outline = trim( implode( ',', $outdata ) ) . "\n";
	
	fputs( $outhandle, $outline );
}

fclose( $handle );
fclose( $outhandle );

echo "--fin--\n";
?>
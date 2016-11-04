<?php
$file = $argv[1];
$geofile = $file . '.geojson';
$bureau = 0;
$first = true;

if( !file_exists( $file ) ) {
	echo "Pas trouvé fichier $file\n";
	exit();
}

if( !$handle = fopen( $file, 'r' ) ) {
	echo "Pas pu ouvrir fichier $file en entrée\n";
	exit();
}

if( !$geohandle = fopen( $geofile, 'w' ) ) {
	echo "Pas pu ouvrir fichier $geofile en sortie\n";
	exit();
}

echo "Traitement du fichier $file - sortie dans $geofile\n";

$geo_header = <<<EOT
{
  "type": "FeatureCollection",
  "generator": "overpass-turbo",
  "copyright": "The data included in this document is from www.openstreetmap.org. The data is made available under ODbL.",
  "timestamp": "2016-11-02T18:03:02Z",
  "features": [
EOT;
fputs( $geohandle, $geo_header );

while( ($data = fgetcsv( $handle, 1000, ',' ) ) !== false ) {
	
	if( $data[0] > 1 ) continue;
	
	if( $data[0] != $bureau ) {
		if( !$first ) {
			//feature end
			fputs( $geohandle, "]]}},\n" );
		}
		$first = false;
		$bureau = $data[0];
		$feature_header = <<<EOT
		    {
		      "type": "Feature",

		      "properties": {

		        "boundary": "polling_station",
		        "ref": "$bureau",
		        "type": "boundary"
		      },
		      "geometry": {
		        "type": "Polygon",
		        "coordinates": [
		          [
EOT;
		fputs( $geohandle, $feature_header );
	}
	if( $data[6] && $data[7] ) {
		list( $long, $lat ) = preg_split( '/;/', $data[6] );
		fputs( $geohandle, '[' . $long . ',' . $lat . ']' );
		list( $long, $lat ) = preg_split( '/;/', $data[7] );
		fputs( $geohandle, '[' . $long . ',' . $lat . ']' );
	}
}

$geo_footer = <<<EOT
					]
				]
			}
		}
	]
}
EOT;
fputs( $geohandle, $geo_footer );
fclose( $geohandle );
?>
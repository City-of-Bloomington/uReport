<?php

$username = "root";
$password = "1234";
$hostname = "localhost"; 

//connection to the database
$dbhandle = mysql_connect($hostname, $username, $password) 
  or die(mysql_error());
echo "Connected to MySQL\n";

//select a database to work with
$selected = mysql_select_db("crm",$dbhandle) 
  or die(mysql_error());

//execute the SQL query
for($level = 0; $level <= 6; $level ++) {
	$dist = 0.01 * pow(2, $level * 2);
	$tickets = mysql_query("
	SELECT id, latitude, longitude 
	FROM tickets
	WHERE 
		latitude IS NOT NULL AND 
		longitude IS NOT NULL AND 
		cluster_id_lv$level IS NULL
	");

	$max = mysql_query("SELECT MAX(cluster_id_lv$level) AS max FROM tickets");
	$max_cluster_id = mysql_fetch_array($max);
	$cluster_id = $max_cluster_id{'max'} === NULL ? 1 : $max_cluster_id{'max'} + 1;

	$count = 1;
	while($tic = mysql_fetch_array($tickets)) {
		$id = $tic{'id'};
		echo "Cluster Level: $level, Count: $count, Ticket ID: $id\n";
		$lat = $tic{'latitude'};
		$lng = $tic{'longitude'};
		$result = mysql_query("
		SELECT 
			t.cluster_id_lv$level, 
			(SELECT AVG(latitude) FROM tickets i where i.cluster_id_lv$level = t.cluster_id_lv$level) as mean_lat,
			(SELECT AVG(longitude) FROM tickets i where i.cluster_id_lv$level = t.cluster_id_lv$level) as mean_lng,
			(
				SELECT
				(ACOS(SIN(RADIANS(mean_lat)) * SIN(RADIANS($lat))
				 + COS(RADIANS(mean_lat)) * COS(RADIANS($lat))
				 * COS(RADIANS(mean_lng - $lng))) * 6371.0)
			) as distance
		FROM tickets t
		WHERE 
			t.cluster_id_lv$level IS NOT NULL AND 
			(ACOS(SIN(RADIANS(latitude)) * SIN(RADIANS($lat))
			 + COS(RADIANS(latitude)) * COS(RADIANS($lat))
			 * COS(RADIANS(longitude - $lng))) * 6371.0) < $dist 
		GROUP BY t.cluster_id_lv$level
		HAVING distance < $dist
		LIMIT 1;
		");
	
		if (is_bool($result)) {
			echo "Query Error!\n";
		}
		else {
			if(mysql_num_rows($result) === 0) {
				mysql_query("
				UPDATE tickets
				SET cluster_id_lv$level = $cluster_id
				WHERE id = $id
				");
				$cluster_id ++;
			}
			else {
				$topRow = mysql_fetch_array($result);
				$closestClusterId = $topRow{'cluster_id_lv'.$level};
				mysql_query("
				UPDATE tickets
				SET cluster_id_lv$level = $closestClusterId
				WHERE id = $id
				");
			}
		}
		$count ++;
	}
}

//close the connection
mysql_close($dbhandle);


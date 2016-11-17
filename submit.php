<pre>

<?php

//	debug
//foreach ($_POST as $key => $value)
//	echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";

//	debug
//print_r($_POST);
//echo "<hr>";

//$count = 0;
//foreach ($_POST[referrer] as $key => $value) {
//	if ($count > 0) {
//		$referrer_data .= ", ";
//	}
//
//	$referrer_data .= $value;
//	$count ++;
//}


$data_array = array (
	"timestamp" => date("c"),
	"name" => htmlspecialchars( $_POST["name"] ),
	"email" => htmlspecialchars ( $_POST["email"] ),
	"phone" => htmlspecialchars ( $_POST["phone"] ),
	"street" => htmlspecialchars ( $_POST["street"] ),
	"city" => htmlspecialchars ( $_POST["city"] ),
	"state" => htmlspecialchars ( $_POST["state"] ),
	"zip" => htmlspecialchars ( $_POST["zip"] ),
	"marian_newsletter" => htmlspecialchars ( $_POST["marian_newsletter"] ),
	"referrer_0" => htmlspecialchars( $_POST[referrer][0] ),
	"referrer_1" => htmlspecialchars( $_POST[referrer][1] ),
	"referrer_2" => htmlspecialchars( $_POST[referrer][2] ),
	"referrer_3" => htmlspecialchars( $_POST[referrer][3] ),
	"referrer_4" => htmlspecialchars( $_POST[referrer][4] ),
	"referrer_5" => htmlspecialchars( $_POST[referrer][5] ),
	"referrer_6" => htmlspecialchars( $_POST[referrer][6] ),
	"comments" => htmlspecialchars( $_POST["comments"] )
);

//	debug
//print_r($data_array);
//echo "<hr>";
//echo "data array <br />";
//echo json_encode($data_array);
//echo "data array \n";
//print_r($data_array);


$fp = fopen('/var/www/guestbook.csv', 'a');

fputcsv($fp, $data_array);

fclose($fp);


//WRITE DATA TO DATABASE HERE

//


exit(header("Location:thanks.php"));

?>

</pre>

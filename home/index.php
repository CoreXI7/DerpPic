<?php

$directory = getcwd();

$directories = array();
getListOfDirectories( $directories, $directory );

// Max width of image thumbnail
$max_width = 150;
// Max height of image thumbnail
$max_height = 150;

$directories = array_flip($directories);
foreach( $directories as $directory => $foo){
	$directories[ $directory ] = array();
	$directory_reader = dir($directory);
	// Get a list of images
	while (false !== ($filename = $directory_reader->read())) {
		if( preg_match( "~.*\.(gif|jpg|jpeg|png|JPG|PNG|JPEG|GIF)~Ui", $filename, $matches ) ){
			$directories[ $directory ][] = $filename;
		}
	}
	$directory_reader->close();
}

function getListOfDirectories( &$directories, $current_directory ){
	$directories[] = $current_directory;
	$directory_reader = dir($current_directory);
	while (false !== ($filename = $directory_reader->read())) {
		if( is_dir( $current_directory . '/' . $filename ) && $filename[0] != '.'){
		}
	}
}
?> 

<html>
	<head>
		<style>
@font-face {
    font-family: Celestia;
    src: url(http://derpcock.com/Celestia.ttf);
}
h1 {
    font-family: Celestia;
    font-size:40px;
}
p {
    font-family: Celestia;
    font-size:30px;
}

.thumb_container {
	width: <?= $max_width ?>px;
	height: <?= $max_height + 38 ?>px;
	float: left;
	margin: 2px;
}

.thumb_container .imgCt {
	width: <?= $max_width ?>px;
	height: <?= $max_height ?>px;
}

.thumb_container p {
	font-family: sans-serif;
	font-size: 16px;
}
		</style>
	</head>
	<body style="background-color:#9FE7FF;">
		<div><img src="http://derpcock.com/logo.png"></div>
		<p>
			<a href="../">Back</a> 
			<a href="http://derpcock.com/home/0Pony">Horror</a> 
			<a href="http://derpcock.com/home/0Flash">SWF Videos</a> 
			<a href="http://derpcock.com/home/0Programs">Programs</a>
		</p>
<?php

// Render the images
foreach($directories as $directory => $images)
{
	foreach($images as $image)
	{
		?>
		<div class="thumb_container">
			<a href="<?= $image ?>">
				<div class="imgCt">
					<img
						border="0"
						src="get_image.php?image_name=<?= $image ?>&width=<?= $max_width ?>&height=<?= $max_height ?>" />
				</div>
				<p><?= $image ?></p>
			</a>
		</div>
		<?php
	}
}
?>
	</body>
</html>
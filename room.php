<?php
// Cross contaminating PHP and HTML is disgusting but we'll do it anyway.
require_once('config.php');

// Confirm protocol
if(!isset($_SERVER['HTTPS'])) {
	die('You wouldn\'t dare not use a condom, so why not use HTTPS?');
}

if (empty($_GET['slug'])) {
	// No slug? Redirect them to error page
	header('Location: ' . 'https://' . HOST . '/error.html');
	exit;

} else {
	$query = "SELECT room_id FROM rooms WHERE expire > NOW() AND slug = ?";
	$result = $mysqli->prepare($query);
	$result->bind_param('s', $_GET['slug']);

	if (!$result->execute()) {
		die('Error executing SQL query.');
	} else {
		$result->store_result();
	}

	if ($result->num_rows == 0) {
		// Slug not found? Redirect them to error page
		header('Location: ' . 'https://' . HOST . '/error.html');
		exit;
	}
}

// Get room name as a safe variable
$room_name = htmlentities($_GET['slug']);
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Conference - Voice Links</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="//<?php echo HOST; ?>/assets/css/main.css">
		<link rel="icon" href="//<?php echo HOST; ?>/images/connection-color.png">

		<script src="//<?php echo HOST; ?>:8080/socket.io/socket.io.js"></script>
		<script src="//<?php echo HOST; ?>:8080/easyrtc/easyrtc.js"></script>
		<!-- We add a version to this request to stop the browser caching an old version when we update it -->
		<script src="//<?php echo HOST; ?>/js/webrtc.js?v=test-<?php echo rand(1000,100000); ?>"></script>
	</head>

	<body onload="my_init('<?php echo $room_name; ?>')">
		<!-- Wrapper -->
		<div id="wrapper">
			<table>
				<tr>
					<td><img src="//<?php echo HOST; ?>/images/connection-color.png" style="height: 150px;"></td>
					<td><img src="//<?php echo HOST; ?>/images/logo.png" style="height: 150px;"></td>
				</tr>
			</table>

			<!-- Main -->
			<p>Send this link to others to allow them to join the conference:</p>
			<form>
				<input type="text" onclick="this.select()" value="https://<?php echo HOST . '/room/' . $room_name; ?>" style="width: 500px">
			</form>
			
			<section id="main" style="width:80%">
				<header>
					<h1>Users Currently in this conference:</h1>
						<div id="other-clients"> </div><br>
						<!-- Our box -->
						<video id="self" width="1" height="1" style="display:none"></video>
						<div id="client-box">
							<!-- New clients get a box in here -->
						</div>
				</header>
			</section>

			<!-- Footer -->
			<footer id="footer">
				<ul class="copyright">
					<li>&copy; Voice Links</li>
					<li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
				</ul>
				<span>Icons made by <a href="http://www.flaticon.com/authors/rami-mcmin" title="Rami McMin">Rami McMin</a> from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></span>
			</footer>
		</div> <!-- Close wrapper -->
	</body>
</html>
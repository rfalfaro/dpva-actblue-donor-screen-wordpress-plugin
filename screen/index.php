﻿﻿<html>
<header>
	<title>BCG Donation Screen</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="style.css"/>
</header>
<body>

<div align="center" style="max-width:1920px;">
	<div class="container-fluid">
	<div class="row">
		<div class="col-md-1">
			
		</div>
		<div class="col-md-3" align="center">
			<img src="logo.png" style="width:auto; max-height:250px;">
		</div>
		<div class="col-md-7" align="center">
			<h1 id="displayTitle"></h1>
			<h1 id="displayTotal"></h1>
			<div class="goal-text-display">of <span id="displayGoal"></span> goal</div>
		</div>
		<div class="col-md-1">
			
		</div>
	</div>
	<div style="width: 100%; height: 30px;"></div>
		<div class="progress">
			<div class="progress-bar" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
		</div>
		<div class="row">
			<div class="col-md-9" style="padding-top:10px; padding-left:100px; padding-right:100px; padding-bottom:10px;">	
				<h1 id="#displayThankYou">Thank you for donating and helping<br/>make 2019 our year!</h1>
			</div>
			<div class="col-md-3 getting-text-display" align="right">				
				<img src="arrow.png" align="right"><br/>Getting here helps us flip the General Assembly in November!
			</div>
		</div>
		<div style="width: 100%; height: 30px;"></div>
		<div id="displayLatestDonors" class="row">
		</div>
		<div class="disclaimer-text-display" align="center">
		Txt HELP for help, STOP to end.  Msg & Data rates may apply.  Privacy Policy: https://vademocrats.org/dpva-privacy-policy/
		</div>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="../js/dpva-bcg-actblue.js"></script>
</body>
</html>
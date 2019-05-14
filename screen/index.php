<html>
<header>
	<title>BCG Donation Screen</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<style>
	body{
		width: 100%;
		height: 100%;
		display: flex;
		justify-content: center;
		flex-direction: column;
		text-align: center;
	}
	.progress {
		height: 3rem !important;
	}
	</style>
</header>
<body>

<div align="center">
	<div class="container-fluid">
		<h1 id="displayTitle"></h1>
		<h1 id="displayTotal"></h1>
		<div class="progress">
			<div class="progress-bar" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
		</div>
		<h3 id="displayGoal"></h3>
		<p>Goal</p>
		<p>Thank you for donating!</p>
		<div id="displayLatestDonors" class="row">
		</div>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="../js/dpva-bcg-actblue.js"></script>
</body>
</html>
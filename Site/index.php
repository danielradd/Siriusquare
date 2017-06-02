<?php 
/*
* Daniel Radd
* 
*/
	include "api.php";
	$redirect_uri = "http://siriusquare.esy.es/index.php";
	
	// Load the Foursquare API library
	$foursquare = new FoursquareAPI($client_key,$client_secret);
	// If the link has been clicked, and we have a supplied code, use it to request a token
	//if(array_key_exists("code",$_GET)){
	//	$token = $foursquare->GetToken($_GET['code'],$redirect_uri);
	//}
	
?>
<html>
	<head>
		<title>Sirius - Recomendador para o Foursquare</title>
		
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta charset="utf-8" />
	
		<link rel="stylesheet" href="jquery.mobile-1.0rc2.min.css" />
		<script type="text/javascript" src="js/jquery-1.6.4.min.js"></script>
		<script type="text/javascript" src="js/jquery.mobile-1.0rc2.min.js"></script>
	</head>

	<body>
		<article data-role="page">
			<header data-role="header" data-position="fixed">
				<h1>Sirius</h1>
			</header>
			<img alt="SiriusquareLogo" longdesc="SiriusquareLogo" src="images/EstrelaSirius.png" width="91" height="95" /><p>
			<section data-role="content">
				<h2>Sistema de Recomendação para o Foursquare</h2>
				<p><i>Versão BETA (Estável para Cidades Brasileiras)</i></p>
				<p>
					<p>
						<form action="valida.php" method="POST">
							<div data-role="fieldcontain">
								<label for="search">Cidade para visitar: </label>
								<input type="search" name="location" id="location" value=""  /> Exemplo: Rio de Janeiro
							</div>
							<div data-role="fieldcontain">
								<label for="select-choice-a" class="select">Número de Dias: </label>
								<br>
								<select id="days" name="days" data-native-menu="false">
									<option value="1">1 dia</option>
									<option value="2">2 dias</option>
									<option value="3">3 dias</option>
									<option value="4">4 dias</option>
									<option value="5">5 dias</option>
									<option value="6">6 dias</option>
									<option value="7">7 dias</option>
								</select>
								
							</div>
							<div class="ui-block-b">
								<input type="submit" value="Prosseguir" data-theme="a" />
							</div>
							
						</form>
					</p>

				</p>
				<br><br><br><br>
				<p align="center">
				<img src="images/poweredByFoursquare_gray.png" /><br>
				</p>

			</section>
			<footer data-role="footer" data-position="fixed" class="ui-bar">
				<p align="center">Sirius - Criado por: <a href="http://twitter.com/danielradd" target="_blank">Daniel Radd</a></p>
			</footer>
		</article>
	</body>
</html>
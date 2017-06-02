<?php
/*
* Daniel Radd
* danielraddps@gmail.com
*/
	include "api.php";
	include "kmeans.php";
	$redirect_uri = "http://siriusquare.esy.es/index.php";
	$foursquare = new FoursquareAPI($client_key,$client_secret);
	// If the link has been clicked, and we have a supplied code, use it to request a token
	if(array_key_exists("code",$_GET)){
		$token = $foursquare->GetToken($_GET['code'],$redirect_uri);
	}
	
	$location = array_key_exists("location",$_POST) ? $_POST['location'] : "Belo Horizonte, MG";
	$days = array_key_exists("days",$_POST) ? $_POST['days'] : "1";


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
				<a href="index.php" data-icon="back">Voltar</a> 
				<h1>Sirius</h1>
				<a href="index.php" data-icon="delete">Logoff</a> 
			</header>
			<img alt="SiriusquareLogo" longdesc="SiriusquareLogo" src="images/EstrelaSirius.png" width="91" height="95" /><p>
			<section data-role="content">
				<p>
					<?php
					
						//echo "Your auth token: $token";
						$auth_token = $token;
						// Load the Foursquare API library
						$foursquare = new FoursquareAPI($client_key,$client_secret);
						$foursquare->SetAccessToken($auth_token);
						
						
						// Gerar nome da cidade e seu respectivo estado usando Google Maps API
						list($cidade,$estado,$pais) = $foursquare->GeoLocate_names($location);
							
						if (empty($cidade) || empty($estado) || empty($days) || $days > 8)
						{
							echo "Campo(s) em Branco ou a busca não foi considerada uma Cidade<br>";
							echo "Cidade: ".$cidade."<br>Estado: ".$estado."<br>Dia(s): ".$days."";
						}
						else{

							// Categorias e suas 8 IDs
							$arteeLazer="4d4b7104d754a06370d81259"; //id 1
							$alimentacao="4d4b7105d754a06374d81259"; //id 2
							$vidaNoturna="4d4b7105d754a06376d81259"; //id 3
							$arlivre="4d4b7105d754a06377d81259"; //id 4
							//$cafedaManha="4bf58dd8d48988d143941735"; //id * **
							//$shoppingCenter="4bf58dd8d48988d1fd941735"; //id * **
							//$estadio="4bf58dd8d48988d184941735"; //id * **
							//$hotel="4bf58dd8d48988d1fa931735"; //id * **
							//$viagemTransp="4d4b7105d754a06379d81259"; //id * **
							
							// 1º parametro Arte e Lazer
							// Prepare parameters
							$params = array("near"=>"$cidade,$estado","limit"=>"15", "categoryId"=>$arteeLazer);
							// Perform a request to a public resource
							$response = $foursquare->GetPublic("venues/search",$params);
							$venues = json_decode($response);
							foreach($venues->response->venues as $venue):
								if($venue->location->city == $cidade)
									$vetorV[] =  new Venue($venue->id, ($venue->location->lat), ($venue->location->lng), $venue->stats->checkinsCount, $venue->categories['0']->id);
							endforeach;
							//-----------------------
							
							// 2º parametro Alimentação
							// Prepare parameters
							$params = array("near"=>"$cidade,$estado","limit"=>"15", "categoryId"=>$alimentacao);
							// Perform a request to a public resource
							$response = $foursquare->GetPublic("venues/search",$params);
							$venues = json_decode($response);
							foreach($venues->response->venues as $venue):
								if($venue->location->city == $cidade)
									$vetorV[] =  new Venue($venue->id, ($venue->location->lat), ($venue->location->lng), $venue->stats->checkinsCount, $venue->categories['0']->id);
							endforeach;
							//-----------------------
							
							// 3º parametro vidaNoturna
							// Prepare parameters
							$params = array("near"=>"$cidade,$estado","limit"=>"15", "categoryId"=>$vidaNoturna);
							// Perform a request to a public resource
							$response = $foursquare->GetPublic("venues/search",$params);
							$venues = json_decode($response);
							foreach($venues->response->venues as $venue):
								if($venue->location->city == $cidade)
									$vetorV[] =  new Venue($venue->id, ($venue->location->lat), ($venue->location->lng), $venue->stats->checkinsCount, $venue->categories['0']->id);
							endforeach;
							
							//-----------------------
							// 4º parametro arlivre
							// Prepare parameters
							$params = array("near"=>"$cidade,$estado","limit"=>"15", "categoryId"=>$arlivre);
							// Perform a request to a public resource
							$response = $foursquare->GetPublic("venues/search",$params);
							$venues = json_decode($response);
							foreach($venues->response->venues as $venue):
								if($venue->location->city == $cidade)
									$vetorV[] =  new Venue($venue->id, ($venue->location->lat), ($venue->location->lng), $venue->stats->checkinsCount, $venue->categories['0']->id);
							endforeach;
							//-----------------------

							//---------KMEANS-----------------------------------------------
							
							// kmeans com o vetor de todas as Venues
							$kvalues = kmeans($vetorV, $days);
							//print_r($kvalues);
							echo "Cidade: ".$cidade." | Estado: ".$estado." | País: ".$pais."<br>Nº dias escolhido: ".$days."<br>";
							if(count($kvalues) != $days)
								echo "<b>Foi possível selecionar ".count($kvalues)." dias</b><br>";
							echo "<br><br>";
							$contador = 0;
							foreach ($kvalues as $v1) {
								echo "<hr><center><h1>Dia: ".++$contador."</h1></center><hr>";
									foreach ($v1 as $v2) {										
										// o restante é guardado para ordenar o maior
										//echo "else <br>";
										$maior[] = $v2->cont;// recolhe quant publico
										$idtemp[] = $v2->id; // capturar id dos grandes
										//echo "<br><br>";
										
									}
									// remoção de duplicatas
									$idtemp=array_unique($idtemp);
									$maior=array_unique($maior);
									// fim remoção de duplatas
									// ordena os maiores
									for ($i=0; $i<count($maior); $i++)
									{
										for ($j=$i+1; $j<count($maior); $j++)
										{
											if ($maior[$i] < $maior[$j])
											{
												$aux = $maior[$i];
												$maior[$i] = $maior[$j];
												$maior[$j] = $aux;
												// ajusta ids tbm.
												$aux = $idtemp[$i];
												$idtemp[$i] = $idtemp[$j];
												$idtemp[$j] = $aux;
											}
										}
									}
									// mostra resultados ----------------------------------------------

									if (count($idtemp)<5)
										$tamanhoAmostra=count($idtemp);
									else
										$tamanhoAmostra=5;
										
									if (count($idtemp)!=0)
										echo "<p><b>Você poderá se interessar em visitar esse(s) Lugar(es)</b></p><br>";
										
									for($v4=0;$v4<$tamanhoAmostra;$v4++)
									{
										//echo "Local: $v4 <br>";
										$response = $foursquare->GetPublic("venues/$idtemp[$v4]");
										$clusters = json_decode($response);
										if (is_object($clusters->response))
										{	echo '<a href="https://foursquare.com/v/'.$clusters->response->venue->id.'" target="_blank"/><b>';
											echo $clusters->response->venue->name;
											echo "</b></a><br/>";
											if(isset($clusters->response->venue->categories['0']))
											{
												if(property_exists($clusters->response->venue->categories['0'],"name"))
												{
													echo ' <i> Categoria: '.$clusters->response->venue->categories['0']->name.'</i><br/>';
												}
											}
												if(property_exists($clusters->response->venue->hereNow,"count"))
												{
													echo 'Tem '.$clusters->response->venue->hereNow->count ." pessoa(s) atualmente aqui.<br/> ";
												}
												echo '<b><i>Histórico</i></b>: '.$clusters->response->venue->stats->usersCount." Visitante(s) , ".$clusters->response->venue->stats->checkinsCount." visitas ";
												echo "<br><br>";
										}
									}
									echo'<br>';
									
									// fim mostra resultados --------------------------------------------
									// destroi vetores para comecar de novo
									
									unset($idtemp);
									unset($maior);
								echo "<br>";
							}
							
						}// fim else $cidade $estado com erro
					?>
				</p>
			
			</section>
			<footer data-role="footer" data-position="fixed" class="ui-bar">
				<p align="center">Sirius - Criado por: <a href="http://twitter.com/danielradd" target="_blank">Daniel Radd</a></p>
			</footer>
		</article>
	</body>
</html>
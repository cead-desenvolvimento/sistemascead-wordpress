<?php
	
	/* 
	Template Name: Portfolio
	*/

	require_once('dao.php');
	
	// Display Header
	get_header();
	
	// Get Theme Options
	global $data;
	
	// Get Post ID
	global $wp_query;$post_id = $wp_query->post->ID;
	
	// Get Header Image
	$header_image = page_header(get_post_meta($post_id, 'qns_page_header_image_url', true));
	
	// Get Content ID/Class
	$content_id_class = content_id_class(get_post_meta($post_id, 'qns_page_sidebar', true));
	
	// Reset Query
	wp_reset_query();

?>

<div id="portfolio-img" class="header-image-container parallax-container img-banner" style="background-image: url('https://www.cead.ufjf.br/wp-content/uploads/2021/11/polos-3.png');"></div>

<div class="portfolio container">
<div class="section">
	
	<!-- BEGIN .content-wrapper -->
	<div class="content-wrapper page-content-wrapper clearfix">

		<!-- BEGIN .main-content -->
		<div class="main-content-full page-content">
		
			<!-- BEGIN .inner-content-wrapper -->
			<div class="inner-content-wrapper">
				<span id="conteudo" class="anchor"></span>
				<div id="polo-ref" class="row pad-style">
					<ul class="list-polo">
						<li>
							<a href="#Conheça nossos polos">
								Conheça nossos polos
							</a>
						</li>
						<li>
							<a href="#Localize os polos">
								Localize os polos
							</a>
						</li>
						<li>
							<a href="#Polo UAB: saiba como ser um parceiro da UFJF">
								Polo UAB - Seja um parceiro da UFJF
							</a>
						</li>
					</ul>
				</div>				
				<hr class="gray-separator">
				<?php 
					$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;		
					$args = array(
						'post_type'      => 'portfolio',						
						'posts_per_page' => -1,						
						'orderby'        => 'title',
						'order'          => 'ASC',
					);

					$wp_query = new WP_Query($args);

					if($wp_query->have_posts()):
						$titulos = [];
						$postsLink = [];
						
						while ($wp_query->have_posts()) :
							$wp_query->the_post();
						
							$titulo = get_the_title();
							if ($titulo) {
								$titulos[] = $titulo;
							}

							$postsLink[get_post_field('post_name', get_the_ID())] = get_permalink();
						endwhile;

						$dao = new dao();
						$dadosPolos = $dao->getPolosInfos($titulos);
						
						// Início do HTML
						$listaLinkPoloTopo = '<div class="row"><div class="strip-multicolor"><div class="stripped margin-top-default-portifolio"><a id="Conheça nossos polos" class="anchor"></a><p class="bold">Conheça nossos polos:</p><br><ul class="two-list nostyle">';
						$listaEstadoCidades = '';
						
						if (!empty($dadosPolos)) {
							$ufAnterior = '';						
						
							foreach ($dadosPolos as $polo) {
								$uf = explode(',', $polo['municipio_uf'])[1] ?? ''; // "Juiz de Fora, Minas Gerais"
						
								if ($uf !== $ufAnterior) {
									if ($ufAnterior !== '') {
										$listaEstadoCidades .= "</ul></div></div></div></div>";	
									}
						
									$ufAnchor = str_replace(" ", "", $uf);
						
									$listaLinkPoloTopo .= '<li><a class="color-link" href="#'.$ufAnchor.'">'.$uf.'</a></li>';										
									$listaEstadoCidades .= '<div class="row no-margin"><div class="strip-multicolor"><div class="stripped polos polo-color"><a id="'.$ufAnchor.'" class="anchor"></a><span></span><h5 class="padding-list">'.$uf.'</h5><div><ul class="two-list">';
								}
						
								// Criar o slug a partir do nome_breve como antes
								$slug = implode("-", explode(" ", strtolower(remove_accents($polo['nome']))));
						
								$listaEstadoCidades .= '<li class="list-f18"><a class="color-purple" href="'.$postsLink[$slug].'">'.formataNomePolo($polo['nome']).'</a></li>';
						
								$ufAnterior = $uf;
							}
						
							$listaEstadoCidades .= "</ul></div></div></div></div>";										
							$listaLinkPoloTopo .= '</ul></div></div></div>';
						} else {
							echo "Nenhum resultado foi encontrado nesta busca!";
						}
						
						echo $listaLinkPoloTopo;
						echo $listaEstadoCidades;
					endif;

					function changeString($inputString){
						$inputString = strtolower_utf8($inputString);						
						$composeWord = explode(" ", $inputString);
						$resultWord = '';

						foreach($composeWord as $word){
							if(strlen($word) > 2 && $word != "" && $word != "&nbsp;" && !(strlen($word) == 3 && substr($word,2,1) == 's')){
								$word = ucfirst($word);
							}							
							$resultWord .= " " . $word;
						}						
						return $resultWord;
					}

					function strtolower_utf8($inputString) {
						$outputString = utf8_decode($inputString);
						$outputString = strtolower($outputString);
						$outputString = utf8_encode($outputString);
						return $outputString;
					}	
				?>

				<hr class="gray-separator">

				<hr id="Localize os polos" class="anchor">

				<div class="row">										
					<div class="strip-multicolor">
						<div class="stripped margin-top-default-portifolio">										
							<p class="bold">Localize os polos</p>			
						</div>
					</div>
				</div>
				<div class="row">					
					<div id="map-divide" class="col s12 m6 l6"> 	
						<div class="strip-multicolor">
							<div class="stripped polos polo-color">
								<span></span>
								<div class="no-margin">
									<div class="widget content-block"> <br>
										<span id="map-title" class="color-purple">Digite um local para localizar os polos</span>										
										<input
										  id="endereco"
										  type="text"
										  placeholder="Digite um endereço..."
										  onfocus="geolocate()"
										  style="width: 95%; max-width: 600px; height: 37px; border: 1px solid #A3A3A3; border-radius: 18.5px;
										         font-family: Roboto, sans-serif; font-size: 16px; text-indent: 15px; padding: 0 10px;
										         display: block; margin: 20px auto;"
										/>
										<input style="width: 100%; display: none;" type="submit" id="buscaPolos" value="Buscar Polos Próximos">										
										<div style="overflow: auto; height: 430px; margin-top: 10px">												
											<ul id="polo">													
											</ul>
										</div>
									</div>																	
								</div>		
							</div>		
						</div>		
					</div>		
					<div id="map-divide" class="col s12 m6 l6"> 						
						<div id="map-container" style="width: 100%; height: 400px;"></div>
					</div>		
				</div>

				<script src="<?php echo get_bloginfo('template_directory'); ?>/js/jquery-2.1.1.min.js"></script>
				<script src="<?php echo get_bloginfo('template_directory'); ?>/page-templates/markerclusterer.js"></script>

				<script>
					// Ensure global scope for carregaMapa
					window.carregaMapa = function() {
						const mapDiv = document.getElementById("map-container");
						if (!mapDiv) {
							console.error("Elemento 'map-container' não encontrado.");
							return;
						}

						window.map = new google.maps.Map(mapDiv, {
							center: { lat: -21.762, lng: -43.349 },
							zoom: 5,
							mapTypeId: "roadmap",
						});

						window.directionsService = new google.maps.DirectionsService();

						// Initialize PlaceAutocompleteElement
						const autocompleteElement = document.getElementById("endereco");
						if (!autocompleteElement) {
							console.error("Elemento 'endereco' não encontrado.");
							return;
						}

						window.autocomplete = autocompleteElement;

						// Handle place change event
						autocomplete.addEventListener("gmp-place-changed", () => {
							const place = autocomplete.place;
							if (!place || !place.geometry) {
								window.alert("Local não encontrado");
								return;
							}

							if (place.geometry.viewport) {
								map.fitBounds(place.geometry.viewport);
							} else {
								map.setCenter(place.geometry.location);
								map.setZoom(17);
							}

							// Create or update home marker
							if (window.marcadorCasa) {
								window.marcadorCasa.position = place.geometry.location;
							} else {
								window.marcadorCasa = new google.maps.marker.AdvancedMarkerElement({
									map: map,
									position: place.geometry.location,
									title: "Casa",
									content: createMarkerContent(window.imagemHome),
								});
							}

							// Store place globally
							window.place = place;

							// Trigger search for nearby locations
							buscaLocaisProximos(place.geometry.location);
						});

						// Call geolocate to bias map to user's location
						geolocate();
					};

					// Global variables
					window.map = null;
					window.directionsDisplay = null;
					window.directionsService = null;
					window.autocomplete = null;
					window.initLocate = null;
					window.marcadorCasa = null;
					window.polos_ordenados = [];
					window.imagem = "<?php echo get_bloginfo('template_directory'); ?>/images/map-marker-logo.png";
					window.imagemHome = "<?php echo get_bloginfo('template_directory'); ?>/images/map-home-marker-logo.png";

					function geolocate() {
						if (navigator.geolocation) {
							navigator.geolocation.getCurrentPosition(
								(position) => {
									const geolocation = new google.maps.LatLng(
										position.coords.latitude,
										position.coords.longitude
									);
									window.initLocate = geolocation;
									if (window.map) {
										window.map.setCenter(geolocation);
									}
								},
								(error) => {
									console.error("Geolocation error:", error);
								}
							);
						}
					}

					function createMarkerContent(imageUrl) {
						const img = document.createElement("img");
						img.src = imageUrl;
						img.style.width = "32px"; // Adjust size as needed
						img.style.height = "32px";
						return img;
					}

					function buscaLocaisProximos(pontoLatLng) {
						const destinos = [];

						// Calculate approximate distances
						for (let i = 0; i < window.polos.length; i++) {
							window.polos[i].distanciareal = Math.sqrt(
								Math.pow(pontoLatLng.lat() - window.polos[i].latitude, 2) +
								Math.pow(pontoLatLng.lng() - window.polos[i].longitude, 2)
							);
						}

						// Copy and sort polos
						window.polos_ordenados = [...window.polos];
						bubbleSort();

						// Limit to 25 destinations
						for (let i = 0; i < window.polos_ordenados.length && i < 25; i++) {
							destinos.push(
								new google.maps.LatLng(
									window.polos_ordenados[i].latitude,
									window.polos_ordenados[i].longitude
								)
							);
						}

						const service = new google.maps.DistanceMatrixService();
						service.getDistanceMatrix(
							{
								origins: [pontoLatLng],
								destinations: destinos,
								travelMode: google.maps.TravelMode.DRIVING,
								unitSystem: google.maps.UnitSystem.METRIC,
								avoidHighways: false,
								avoidTolls: false,
							},
							callback
						);
					}

					function callback(response, status) {
						if (status !== google.maps.DistanceMatrixStatus.OK) {
							alert("O erro foi: " + status);
							return;
						}

						const results = response.rows[0].elements;
						jQuery("#polo").html("");

						for (let j = 0; j < results.length && j < 25; j++) {
							window.polos_ordenados[j].tempo = results[j].duration.text;
							window.polos_ordenados[j].distancia = results[j].distance.text;
							window.polos_ordenados[j].distanciareal = results[j].distance.value;
						}

						for (let i = 25; i < window.polos.length; i++) {
							window.polos_ordenados[i].tempo = null;
							window.polos_ordenados[i].distancia = null;
							window.polos_ordenados[i].distanciareal = null;
						}

						bubbleSort();

						for (let i = 0; i < results.length; i++) {
							jQuery("#polo").append(
								`<li><input type='hidden' value='${i}'>` +
								`<i class="tiny material-icons">chevron_right</i>` +
								`<span class='nome-polo'>${formataTextoLista(window.polos_ordenados[i].nome)}</span><br/>` +
								`<span class='distancia-polo'><b>${window.polos_ordenados[i].distancia}</b> até o polo</span>` +
								`<span class='tempo-polo'><b>${window.polos_ordenados[i].tempo}</b> de carro</span></li>`
							);
						}

						aplicaEstilo();

						jQuery("#polo li").click(function () {
							const indice = jQuery(this).children("input").val();
							caminho(
								window.place.geometry.location,
								new google.maps.LatLng(
									window.polos_ordenados[indice].latitude,
									window.polos_ordenados[indice].longitude
								)
							);
						});
					}

					function caminho(origem, destino) {
						if (!window.directionsDisplay) {
							window.directionsDisplay = new google.maps.DirectionsRenderer({ suppressMarkers: true });
						}
						window.directionsDisplay.setMap(window.map);

						const request = {
							origin: origem,
							destination: destino,
							travelMode: google.maps.TravelMode.DRIVING,
						};

						window.directionsService.route(request, (response, status) => {
							if (status === google.maps.DirectionsStatus.OK) {
								window.directionsDisplay.setDirections(response);
							} else {
								alert("Infelizmente não foi reconhecido um caminho de carro até este polo.");
							}
						});
					}

					function bubbleSort() {
						let swapped;
						do {
							swapped = false;
							for (let i = 0; i < window.polos_ordenados.length - 1; i++) {
								if (
									window.polos_ordenados[i].distanciareal > window.polos_ordenados[i + 1].distanciareal &&
									window.polos_ordenados[i + 1].distanciareal != null
								) {
									const temp = window.polos_ordenados[i];
									window.polos_ordenados[i] = window.polos_ordenados[i + 1];
									window.polos_ordenados[i + 1] = temp;
									swapped = true;
								}
							}
						} while (swapped);
					}

					function formataTextoLista(nome) {
						const texto = nome.split(" ");
						let resultado = "";
						for (let i = 0; i < texto.length; i++) {
							texto[i] = texto[i].toLowerCase();
							if (texto[i].length > 2 && !(texto[i].length === 3 && texto[i].charAt(2) === "s")) {
								resultado += " " + texto[i][0].toUpperCase() + texto[i].slice(1);
							} else {
								resultado += " " + texto[i];
							}
						}
						return resultado.trim();
					}

					function aplicaEstilo() {
						jQuery(".nome-polo").css({ "font-size": "140%" });
						jQuery("#polo li").css({
							"padding": "5px 5px 20px 5px",
							"cursor": "pointer",
							"width": "100%",
							"box-shadow": "5px 1px #888888",
						});
						jQuery(".tempo-polo").css({ "float": "right" });
					}

					function centralizaEMarcaCasa(pontoLatLng) {
						window.map.setZoom(14);
						window.map.panTo(pontoLatLng);
						if (window.marcadorCasa == null) {
							window.marcadorCasa = new google.maps.marker.AdvancedMarkerElement({
								position: pontoLatLng,
								map: window.map,
								title: "Casa",
								content: createMarkerContent(window.imagemHome),
							});
						} else {
							window.marcadorCasa.setPosition(pontoLatLng);
						}
					}

					jQuery(function () {
						aplicaEstilo();

						jQuery("#buscaPolos").click(function () {
							if (!window.place || jQuery("#endereco").val() === "") {
								alert("Por favor, digite um endereço válido.");
							} else {
								centralizaEMarcaCasa(window.place.geometry.location);
								buscaLocaisProximos(window.place.geometry.location);
							}
						});
					});

					<?php
						$dao = new dao();
						$polos = $dao->getPolos();

						$polos_json = [];
						$i = 0;

						foreach ($polos as $polo) {
							if (!empty($polo['latitude']) && !empty($polo['longitude'])) {
								$polos_json[$i] = [
									"nome" => $polo['nome'],
									"nome_breve" => $polo['nome'],
									"id_polo" => $polo['id'],
									"end_polo" => $polo['logradouro'],
									"num_polo" => $polo['numero'],
									"bairro_polo" => $polo['bairro'],
									"municipio_uf" => $polo['municipio_uf'],
									"tel_polo" => $polo['telefone_formatado'],
									"cep_polo" => $polo['cep_formatado'],
									"email_polo" => $polo['email'],
									"latitude" => $polo['latitude'],
									"longitude" => $polo['longitude'],
									"tempo" => '',
									"distancia" => '',
									"distanciareal" => '',
								];
								$i++;
							}
						}

						echo "window.polos = " . json_encode($polos_json) . ";";
						echo "window.polos_ordenados = new Array();";
					?>

					<?php
						$dao = new dao();
						$polos = $dao->getPolosComOfertaAtiva();

						mb_internal_encoding("UTF-8");
						mb_http_output("iso-8859-1");

						$cursos_json = [];
						foreach ($polos as $polo) {
							$cursos_json[$polo['id']] = ['lista' => " ✓ " . mb_strtolower($polo['nome']) . "<br>"];
						}

						echo "window.cursos = " . json_encode($cursos_json) . ";";
					?>
				</script>

				<!-- Load Google Maps API after carregaMapa is defined -->
				<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCMHA4q5G3-OFO1stMxiXOvJQoK4yrrVx8&libraries=places,marker&callback=carregaMapa"></script>

				<hr class="gray-separator">
				<hr id="Polo UAB: saiba como ser um parceiro da UFJF" class="anchor">
				<div class="row">										
					<div class="strip-multicolor">
						<div class="stripped margin-top-default-portifolio">										
							<?php 
								$page = get_page_by_path( 'polo-uab-saiba-como-ser-um-parceiro-da-ufjf-2', '', 'post' );														
								$content = apply_filters('the_content', $page->post_content);					
								echo '<p class="bold">'. $page->post_title . '</p>';
							?>							
						</div>
					</div>
				</div>
				<div class="row polo-content">										
					<div class="strip-multicolor">
						<div class="stripped polos polo-color">
							<span></span>	
							<div class="no-margin">
								<?php
									echo $content; 					
								?>
							</div>												
						</div>
					</div>					
				</div>

			<!-- END .inner-content-wrapper -->
			</div>
		<!-- END .main-content -->
		</div>		
	<!-- END .content-wrapper -->
	</div>
</div>
</div>

<?php get_footer(); ?>
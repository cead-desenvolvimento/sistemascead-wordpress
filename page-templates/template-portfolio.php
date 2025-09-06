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

<!-- <div class="header-image-container parallax-container"> -->
	<!-- <img class="responsive-img" src="<?php //echo get_bloginfo('template_directory'); ?>/img/institucional-banner.png" /> -->
	<!-- <img class="responsive-img" src="<?php //echo get_bloginfo('template_directory'); ?>/img/polo.png" /> -->
<!-- </div> -->
<div id="portfolio-img" class="header-image-container parallax-container img-banner" style="background-image: url('http://www.cead.ufjf.br/wp-content/uploads/2021/11/polos-3.png');"></div>

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
				<!-- <?php //the_content(); ?> -->
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
										<input style="width: 95%; margin-bottom: 5px;margin-top: 40px; height: 37px;border: 1px solid #A3A3A3;border-radius: 18.5px;font-family: Roboto;font-size: 18px;
										text-indent: 20px;" type="text" onFocus="geolocate()" name="endereco" id="endereco" placeholder="Insira seu endereço...">
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

						<?php //while ( have_posts() ) : the_post(); ?>	
						<?php //the_content(); ?>
							<div id="map-container">
								<div id="map_canvas" style="width:100%; height:580px"></div>
								<div style="clear: both"></div>
							</div>
						<?php //wp_link_pages(array('before' => '<p><strong>'.__('Pages:', 'qns').'</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>		
						<?php //if ( comments_open() ) : comments_template(); endif; ?>
						<?php //endwhile; ?>



					</div>		
				</div>

				<!-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDh4kLGCeRgUxYj4NPp4BLxZHDTl20I5gQ&sensor=true&libraries=places"></script>				 -->
				<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCMHA4q5G3-OFO1stMxiXOvJQoK4yrrVx8&sensor=true&libraries=places"></script>				
				<script src="<?php echo get_bloginfo('template_directory'); ?>/page-templates/data.json"></script>
				<script src="<?php echo get_bloginfo('template_directory'); ?>/page-templates/markerclusterer.js"></script>
				<script src="<?php echo get_bloginfo('template_directory'); ?>/js/jquery-2.1.1.min.js"></script>
				
				<script type="text/javascript">
                    function data() {					
						var d = new Date();
						var mes = d.getMonth();
						var resultado;
						
						if (mes <= 6) {
							resultado = d.getFullYear() + '-1'; 
						}
						else {
							resultado = d.getFullYear() + '-3';
						}
						
						return resultado;
					}
 
					var directionsDisplay;
                    var directionsService = new google.maps.DirectionsService();
                    var map;
                    var autocomplete;
                    var initLocate
                    var imagem = "<?php echo get_bloginfo('template_directory'); ?>/images/map-marker-logo.png";
                    var imagemHome = "<?php echo get_bloginfo('template_directory'); ?>/images/map-home-marker-logo.png";
                    var mapOptions;
                    var place; //endereco da pessoa
                    var marcadorCasa;

                    function carregaMapa() {                        
                        geolocate();

                        //verifica se não conseguiu pegar LatLng do navegador
                        if(initLocate == null){
                            //centraliza em Patos de Minas por mostrar melhor a localização total dos polos
                            initLocate = new google.maps.LatLng(-18.588312, -46.513668);
                        }

                        mapOptions = {
							'center': initLocate,
							'zoom': 5,
							'minZoom': 2,
							'mapTypeId': google.maps.MapTypeId.ROADMAP,
							'zoomControl': true,
							'zoomControlOptions': {
								style: google.maps.ZoomControlStyle.SMALL
							},
							'disableDefaultUI': false
                        };
                        map = new google.maps.Map(document.getElementById("map_canvas"),mapOptions);

						var infowindow = new google.maps.InfoWindow({});
                        /**
                         * Percorre os polos plotando no mapa
                         */
						var markers = [];
                        var polo;



						for(i = 0; i < polos.length; i++){
                            polo = polos[i];

                            //alert(polos[0].nome_breve);
							
							var marker = new google.maps.Marker({
                                position: new google.maps.LatLng(polo.latitude,polo.longitude),
                                map: map,
                                title: polo.nome + "\nClique para mais informações",
                                icon: imagem
                            });



				
							/*** INFOWINDOW ***/
							google.maps.event.addListener(marker, 'click', (function(marker, i) {
								return function() {
                                    //alert(polos[0].nome_breve);
									
									//content = '<div class="infowindow"><h5>' + polos[i].nome_breve + '</h5><h6>Cursos oferecidos em ' + data() + 
										//':</h6> <p>' + cursos[ polos[i].id_polo ].lista + '</p> <p>Endereço:<br>'+ polos[i].end_polo +', '+ polos[i].num_polo +
										//'<br>Contato: ('+ polos[i].ddd_polo +')'+polos[i].tel_polo+'</p> </div>';
									
									content = '<div class="infowindow"><h5>' + polos[i].nome_breve + '</h5><p>Endereço:<br>'+ polos[i].end_polo +
									', '+ polos[i].num_polo +'<br>Contato: ('+ polos[i].ddd_polo +')'+polos[i].tel_polo+'</p> </div>';
									
									infowindow.close();
									infowindow.setContent(content);
									infowindow.open(map, marker);
								}
							})(marker, i));
							/***/
							
							markers.push(marker);	
						}

                        // Clusterização
						var markerCluster = new MarkerClusterer(map, markers);		
						
						//google.maps.event.addListener( map, 'click', function() { 
						  //infowindow.open( null, null ); 
                        //} );
											
                        
                    }google.maps.event.addDomListener(window, 'load', carregaMapa);
					
                    
                    function iniciarAutoComplete(){						
                        // Auto complete que obriga seleção de objetos do google maps
                        autocomplete = new google.maps.places.Autocomplete(
                            /* Input que o endereço será digitado */(document.getElementById('endereco')),
                            { types: ['geocode'] });
                        // Quando o usuário seleciona um endereço popula os endereços
                        google.maps.event.addListener(autocomplete, 'place_changed', function() {
                            //pega o endereco da pessoa
                            place = autocomplete.getPlace().geometry.location;
                            if(directionsDisplay == null){
                                directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers : true});
                            }
                            directionsDisplay.setMap(null);
                            centralizaEMarcaCasa(place);
                            
                            buscaLocaisProximos(place);
                            
                        });
                    }
					

					function checkBounds() {    						
						if(! allowedBounds.contains(map.getCenter())) {
							var C = map.getCenter();
							var X = C.lng();
							var Y = C.lat();

							var AmaxX = allowedBounds.getNorthEast().lng();
							var AmaxY = allowedBounds.getNorthEast().lat();
							var AminX = allowedBounds.getSouthWest().lng();
							var AminY = allowedBounds.getSouthWest().lat();

							if (X < AminX) {X = AminX;}
							if (X > AmaxX) {X = AmaxX;}
							if (Y < AminY) {Y = AminY;}
							if (Y > AmaxY) {Y = AmaxY;}

							map.setCenter(new google.maps.LatLng(Y,X));
						}
					}
                    
                    function buscaLocaisProximos(pontoLatLng){						
                        
                        var destinos = new Array();
                        
                        for(i = 0; i < polos.length; i++){
                            
                            polos[i].distanciareal = Math.sqrt(
                                    Math.pow((pontoLatLng.lat() - polos[i].latitude), 2)+
                                    Math.pow((pontoLatLng.lng() - polos[i].longitude), 2)
                            );
                        }

                        for(var i=0; i<polos.length; i++)
                        {
                            polos_ordenados[i] = polos[i];
                            
                        }
                        //alert(polos[0].nome_breve);
                        //alert(polos_ordenados[0].nome_breve);
                        
                        bubbleSort();

                        //alert(polos[0].nome_breve);
                        //alert(polos_ordenados[0].nome_breve);
                        
                        for(i = 0; i < polos_ordenados.length; i++){
                            //destino tem q ter 25 pontos
                            if(i >= 25) break;
                              
                            destinos.push(new google.maps.LatLng(polos_ordenados[i].latitude, polos_ordenados[i].longitude));
                        }                      
                        
                        
                        var service = new google.maps.DistanceMatrixService();
                        service.getDistanceMatrix(
                            {
                              origins: [pontoLatLng],
                              destinations: destinos,
                              travelMode: google.maps.TravelMode.DRIVING,
                              unitSystem: google.maps.UnitSystem.METRIC,
                              avoidHighways: false,
                              avoidTolls: false
                            }, callback);
                    }
                    
                    function callback(response, status) {						

                        if (status != google.maps.DistanceMatrixStatus.OK) {
                            alert('O erro foi: ' + status);
                        } else {
							//alert('ta entrando aqui');     // REMOVER
                            var results = response.rows[0].elements;
                            
                            jQuery("#polo").html("");
                            
                            for (var j = 0; (j < results.length && j < 25); j++) {
                                polos_ordenados[j].tempo = results[j].duration.text;
                                polos_ordenados[j].distancia = results[j].distance.text;
                                polos_ordenados[j].distanciareal = results[j].distance.value;
                            }
                            
                            for (var i = 25; i < polos.length; i++) {
                                polos_ordenados[i].tempo = null;
                                polos_ordenados[i].distancia = null;
                                polos_ordenados[i].distanciareal = null;
                            }
                            
                            bubbleSort();
                            
                            for (var i = 0; i < results.length; i++) {                                
                                jQuery("#polo").append("<li><input type='hidden' value='"+i+"'>"
										+"<i class=\"tiny material-icons\">chevron_right</i>"
										+"<span class='nome-polo'>"+formataTextoLista(polos_ordenados[i].nome)+"</span><br/>"
                                        +"<span class='distancia-polo'><b>"+polos_ordenados[i].distancia+"</b> até o polo</span>"
                                        +"<span class='tempo-polo'><b>"+polos_ordenados[i].tempo+"</b> de carro</span></li>");
                            }
                            
                            aplicaEstilo();
                            
                            jQuery("#polo li").click(function() {
                                var indice = jQuery(this).children("input").val();
                                
                                caminho(place,new google.maps.LatLng(polos_ordenados[indice].latitude, polos_ordenados[indice].longitude));
                                
                            });
                            
                        }
                    }

					function formataTextoLista(nome){
						var texto = nome.split(" ");
						var resultado = "";						
						for(i = 0; i < texto.length; i++){							
							texto[i] = texto[i].toLowerCase();							
							if((texto[i].length > 2) && !((texto[i].length == 3) && (texto[i].charAt(2) == "s"))){
								resultado += " " + texto[i][0].toUpperCase() + texto[i].slice(1);
							}else{
								resultado += " " + texto[i];
							}
						}
						return resultado;
					}
                    
                    function bubbleSort(){						

                        var swapped;
                        //polos_ordenados = polos;						
                        
                        do {
                            swapped = false;
                            for (i=0; i < polos.length-1; i++) { // verificar
                                if (polos_ordenados[i].distanciareal > polos_ordenados[i+1].distanciareal && polos_ordenados[i+1].distanciareal != null){
                                    var temp = polos_ordenados[i];
                                    polos_ordenados[i] = polos_ordenados[i+1];
                                    polos_ordenados[i+1] = temp;
                                    swapped = true;
                                }
                            }
                        } while(swapped);
                    }
                    
                    
                    function centralizaEMarcaCasa(pontoLatLng){						

                        map.setZoom(14);
                        map.panTo(pontoLatLng);
                        if(marcadorCasa == null){
                            marcadorCasa = new google.maps.Marker({
                                position: pontoLatLng,
                                map: map,
                                title: "Casa",
                                icon: imagemHome
                            });
                        } else {
                            marcadorCasa.setPosition(pontoLatLng);
                        }
                        
                    }
                    
                    function caminho(origem, destino){						
						
                        if(directionsDisplay == null){
                            directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers : true});
                        }
                        directionsDisplay.setMap(map);
                        var request = {
                            origin: origem,
                            destination: destino,
                            travelMode: google.maps.TravelMode.DRIVING
                        };
                        
                        directionsService.route(request, function(response, status) {
                            if (status == google.maps.DirectionsStatus.OK) {
                                directionsDisplay.setDirections(response);
                            } else{
                                alert("Infelizmente não foi reconhecido um caminho de carro até este polo.");
                            }
                        });
                    } 
                      
                    // [START region_geolocation]
                    // Bias the autocomplete object to the user's geographical location,
                    // as supplied by the browser's 'navigator.geolocation' object.
                    function geolocate() {						

                      if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                          var geolocation = new google.maps.LatLng(
                                position.coords.latitude, position.coords.longitude);
                                if(autocomplete != null){
                                    autocomplete.setBounds(new google.maps.LatLngBounds(geolocation,geolocation));
                                }
                                initLocate = geolocation;
                        });
                      }
                    }
                    // [END region_geolocation]  
                    
                    <?php
						$dao = new dao();
						$polos = $dao->getPolos();

						$polos_json = array();
						$i = 0;

						foreach ($polos as $polo) {
							if (!empty($polo['latitude']) && !empty($polo['longitude'])) {
								$polos_json[$i] = array(
									"nome"         => $polo['nome'],
									"nome_breve"   => $polo['nome'],
									"id_polo"      => $polo['id'],
									"end_polo"     => $polo['logradouro'],
									"num_polo"     => $polo['numero'],
									"bairro_polo"  => $polo['bairro'],
									"tel_polo"     => $polo['telefone'],
									"ddd_polo"     => $polo['ddd'],
									"cep_polo"     => $polo['cep'],
									"email_polo"   => $polo['email'],
									"latitude"     => $polo['latitude'],
									"longitude"    => $polo['longitude'],
									"tempo"        => '',
									"distancia"    => '',
									"distanciareal"=> ''
								);
								$i++;
							}
						}

						echo "var polos = " . json_encode($polos_json) . ";";
						echo "var polos_ordenados = new Array();";
						?>
                    
                    function aplicaEstilo(){						

                        /* Estilo */
                        jQuery(".nome-polo").css({
                            "font-size": "140%"
                        });
                        jQuery("#polo li").css({
                            //"border-top": "1px solid #990000",
                            //"border-left": "1px solid #990000",
                            "padding" : "5px 5px 20px 5px",
                            "cursor": "pointer",
                            // "width" : "344px",
                            "width" : "100%",
                            "box-shadow": "5px 1px #888888"
                        });
                        jQuery(".tempo-polo").css({
                            "float": "right"
                        });
						// jQuery("#map-container").css({
						// 	"-webkit-box-shadow": "rgba(64, 64, 64, 0.5) 0 2px 5px",
						// 	"-moz-box-shadow": "rgba(64, 64, 64, 0.5) 0 2px 5px",
						// 	"box-shadow": "rgba(64, 64, 64, 0.1) 0 2px 5px"
						// });
						/*
						jQuery(".infowindow h5").css({
							"color": "#ae1919",
							"font-style": "italic"
						});                                     INLINE linha 169. Estilo não aplica via javascript. verificar.*/
						
					}
                    
                    jQuery(function() {
                        
                        carregaMapa();
                        
                        iniciarAutoComplete();
                        
                        aplicaEstilo();
                        
                        jQuery("#buscaPolos").click(function(){
                            if(place == null || jQuery("#endereco").val() == "" ){
                                alert("Por favor, digite um endereço válido.");
                                
                            }else{
                                centralizaEMarcaCasa(place);
                                buscaLocaisProximos(place);
                            }
                        });
                        
                    });

					<?php
						$dao = new dao();
						$num_polos = $dao->getMaxIdPolo();

						$cursos_json = array();
						for ($i = 1; $i <= $num_polos; $i++) {
							$nomePoloRows = $dao->getNomePolo($i);

							mb_internal_encoding("UTF-8");
							mb_http_output("iso-8859-1");

							if (!is_array($nomePoloRows) || count($nomePoloRows) == 0) {
								continue;
							}
							
							$temp = '';
							foreach ($nomePoloRows as $row) {
								$temp .= " ✓ " . mb_strtolower($row['nome']) . "<br>";
							}
							$cursos_json[$i] = array('lista' => $temp);
						}

						echo "<script language='javascript' type='text/javascript'>var cursos = " . json_encode($cursos_json) . ";</script>";
					?>

				</script>
				<hr class="gray-separator">
				<hr id="Polo UAB: saiba como ser um parceiro da UFJF" class="anchor">
				<div class="row">										
					<div class="strip-multicolor">
						<div class="stripped margin-top-default-portifolio">										
							<?php 
								$page = get_page_by_path( 'polo-uab-saiba-como-ser-um-parceiro-da-ufjf-2', '', 'post' );														
								$content = apply_filters('the_content', $page->post_content);					
								// echo '<h5 id="'.$page->post_title.'">'. $page->post_title . '</h5>';
								// echo '<h5>'. $page->post_title . '</h5>';
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


<!-- https://www.w3schools.com/howto/tryit.asp?filename=tryhow_js_autocomplete -->
<!-- http://www.devwilliam.com.br/php/autocomplete-com-jquery-ui-php-mysql -->
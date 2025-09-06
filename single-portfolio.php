<?php

	require_once('page-templates/dao.php');
	
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
</script>

<?php
	function formata_data_inicio($data) {
		if (date('i', strtotime($data)) == '00') return date('G', strtotime($data));
		else return date('G\hi', strtotime($data));
	}
	
	function formata_data_fim($data) {
		if (date('i', strtotime($data)) == '00') return date('G\h', strtotime($data));
		else return date('G\hi', strtotime($data));
	}
	
/**
		BUSCA CURSOS ATIVOS DO POLO NO BANCO DE DADOS DO SISTEMA INTERNO
*/

	/*
	&#8211; Código para hífen do word (aquele mesmo que é maior) ( – )
	*/

	$nome_polo = get_the_title();
	$nome_polo = str_replace("&#8211;", '-', $nome_polo);
	//$nome_polo = str_replace("&#8211;", '%', $nome_polo);
	//var_dump($nome_polo);

	$dao = new dao();
	$cursos = $dao->getCursosDoPolo($nome_polo);

	$sem_cursos = empty($cursos);
	$cursos_json = '<ul class="courses-list">';

	if (!empty($cursos) && is_array($cursos)) {
		foreach ($cursos as $row) {
			$nome_curso = $row['nome'];
			$pagina = get_page_by_title($nome_curso, 'OBJECT', 'course');
	
			if ($pagina) {
				$cursos_json .= "<li><a href='" . get_site_url() . "/curso/" . $pagina->post_name . "'>" . $nome_curso . "</a></li>";
			}
		}
	}

	$cursos_json .= '</ul>';
	echo "<script language='javascript' type='text/javascript'>var cursos = " . json_encode($cursos_json) . "</script>";

/**
		BUSCA APRESENTAÇÃO DO POLO NO BANCO DE DADOS DO SISTEMA INTERNO
*/

	/*
	&#8211; Código para hífen do word (aquele mesmo que é maior) ( – )
	*/

	$nome_polo = get_the_title();
	$nome_polo = str_replace("&#8211;", '-', $nome_polo);
	//$nome_polo = str_replace("&#8211;", '%', $nome_polo);
	//var_dump($nome_polo);

	$dao = new dao();
	$apres = $dao->getDescricaoPolo($nome_polo);
	$sem_apres = false;
	$apres_json = '<p>';
	
	if (!is_array($apres) || empty($apres)) {
		$sem_apres = true;
	} else {
		if (isset($apres['apresentacao'])) {
			$apres_json .= $apres['apresentacao'];
		}
	}
	
	$apres_json .= '</p>';
	echo "<script language='javascript' type='text/javascript'>var apres_polo = " . json_encode($apres_json) . ";</script>";

/**
		BUSCA INFORMAÇÕES DO POLO NO BANCO DE DADOS DO SISTEMA INTERNO
*/

	/*
	&#8211; Código para hífen do word (aquele mesmo que é maior) ( – )
	*/

	$info_polo = get_the_title();
	$nome_polo = str_replace("&#8211;", '-', $nome_polo);
	//$nome_polo = str_replace("&#8211;", '%', $nome_polo);
	//var_dump($nome_polo);
	
	/*
	$stmt_polo = $mysqli_info->query(
		"SELECT
			id_polo
		FROM
			cm_polo
		WHERE 
			nome_breve = '$nome_polo'"
		);
	$polo_info = $stmt_polo->fetch_array();
	$id_polo = $polo_info['id_polo'];
	$stmt_polo->close();
	*/
	
	$info = $dao->getInfoPolo($nome_polo);
	$horario = $dao->getHorarioFuncPolo($nome_polo);
	
	$sem_info = false;
	if (!is_array($info) || empty($info)) {
		$sem_info = true;
	}
	
	$info_json = '<p>';
	if (is_array($info) && !empty($info)) {
		$info_coordenador = isset($info["cm_pessoa_coordenador"]) ? $info["cm_pessoa_coordenador"] : '';
		$info_email = isset($info['email'])
			? "E-mail: <a href='mailto:" . $info['email'] . "'>" . $info['email'] . "</a>"
			: '';

		$info_endereco = (isset($info['logradouro'], $info['numero']) && !empty($info['logradouro']) && !empty($info['numero']))
			? $info['logradouro'] . ", " . $info['numero']
			: '';
		if (isset($info['complemento']) && !empty($info['complemento'])) {
			$info_endereco .= " - " . $info['complemento'];
		}

		$info_bairroCep = '';
		if (isset($info['bairro']) && !empty($info['bairro'])) {
			$info_bairroCep = $info['bairro'];
			if (isset($info['cep_formatado']) && !empty($info['cep_formatado'])) {
				$info_bairroCep .= " - CEP: " . $info['cep_formatado'];
			}
		}

		$info_cidadeUF = isset($info['municipio_uf']) ? $info['municipio_uf'] : '';

		$info_telefone = isset($info['telefone_formatado'])
			? "<a href='tel:" . $info['telefone_formatado'] . "'>" . $info['telefone_formatado'] . "</a>"
			: '';
	
		// Processa os horários de funcionamento
		$info_horario = "";
		$ultimo_dia = null;
		if (is_array($horario) && !empty($horario)) {
			foreach ($horario as $row_horario) {
				// Converte o valor de dia_semana para inteiro
				$dia = intval($row_horario['dia_semana']);
				// Funções para formatação de horário (já existentes)
				$hora_inicio = formata_data_inicio($row_horario['hora_inicio']);
				$hora_fim = formata_data_fim($row_horario['hora_fim']);
				
				// Mapeamento dos dias de acordo com isodow:
				// 1: Segundas-feiras, 2: Terças-feiras, 3: Quartas-feiras, 4: Quintas-feiras, 5: Sextas-feiras, 6: Sábados, 7: Domingos
				switch ($dia) {
					case 1:
						$dia_semana = "Segundas-feiras";
						break;
					case 2:
						$dia_semana = "Terças-feiras";
						break;
					case 3:
						$dia_semana = "Quartas-feiras";
						break;
					case 4:
						$dia_semana = "Quintas-feiras";
						break;
					case 5:
						$dia_semana = "Sextas-feiras";
						break;
					case 6:
						$dia_semana = "Sábados";
						break;
					case 7:
						$dia_semana = "Domingos";
						break;
					default:
						$dia_semana = "Dia " . $dia;
				}
				
				// Se for um novo dia, inicia uma nova linha; caso contrário, acrescenta os horários separados por vírgula
				if ($ultimo_dia !== $dia) {
					if ($info_horario != "") {
						$info_horario .= "<br />";
					}
					$info_horario .= $dia_semana . ": " . $hora_inicio . " às " . $hora_fim;
					$ultimo_dia = $dia;
				} else {
					$info_horario .= ", " . $hora_inicio . " às " . $hora_fim;
				}
			}
		}
		
		$info_json .= "Coordenador: " . $info_coordenador . "<br />" .
					  $info_email . "<br />Telefone: " . $info_telefone .
					  "<br />Endereço: " . $info_endereco . "<br />Bairro: " . $info_bairroCep . "<br /> Município: " .
					  $info_cidadeUF . "<br /><br />Horário de funcionamento:<br />" . $info_horario;
	}
	$info_json .= '</p>';
	
	// Envia o HTML montado para uma variável JavaScript
	echo "<script language='javascript' type='text/javascript'>var info_polo = " . json_encode($info_json) . ";</script>";
?>

<!-- BEGIN .page-header -->
<!-- <div class="page-header clearfix" <?php //echo $header_image;	?>> -->
	
	<!-- <div class="page-header-inner clearfix">	
		<?php //dimox_breadcrumbs(); ?>
		<div class="page-title">	
			<h2><?php //the_title(); ?></h2>
			<div class="page-title-block"></div>
		</div>
	</div>
	 -->
<!-- END .page-header -->
<!-- </div> -->

<div id="portfolio-img" class="header-image-container parallax-container img-banner" style="background-image: url(<?php echo get_bloginfo('template_directory')."/img/polos-banner.jpg"?>);"></div>

<div class="portfolio container">
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
							<a href="<?php echo get_permalink(get_page_by_path('/polos/')).'#Conheça nossos polos';?>">
								Conheça nossos polos
							</a>
						</li>
						<li>
							<a href="<?php echo get_permalink(get_page_by_path('/polos/')).'#Localize os polos';?>">
								Localize os polos
							</a>
						</li>
						<li>
							<a href="<?php echo get_permalink(get_page_by_path('/polos/')).'#Polo UAB: saiba como ser um parceiro da UFJF';?>">
								Polo UAB - Seja um parceiro da UFJF
							</a>
						</li>
					</ul>
				</div>

				<hr>
				
			<!-- <div class="page-title">	
				<h5>Polo UAB - <?php //the_title(); ?></h5>				
			</div> -->
			
			<?php if ( post_password_required() ) {
				echo qns_password_form();
			} else { ?>
			
			<?php if( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<?php // Get Portfolio Main Title
				/*$portfolio_main_title = get_post_meta($post->ID, $prefix.'portfolio_main_title', true);
				if ( $portfolio_main_title == '' ) { $portfolio_main_title = 'N/A'; }
					
				// Get Portfolio Main Content
				$portfolio_main_content = get_post_meta($post->ID, $prefix.'portfolio_main_content', true);
				if ( $portfolio_main_content == '' ) { $portfolio_main_content = 'N/A'; }*/
				


				//$polo_apresentacao = get_post_meta($post->ID, $prefix.'polo_apresentacao', true);
				


				// Get Portfolio Secondary Title
				/*$portfolio_secondary_title = get_post_meta($post->ID, $prefix.'portfolio_secondary_title', true);
				if ( $portfolio_secondary_title == '' ) { $portfolio_secondary_title = 'N/A'; }
					
				// Get Portfolio Secondary Content
				$portfolio_secondary_content = get_post_meta($post->ID, $prefix.'portfolio_secondary_content', true);
				if ( $portfolio_secondary_content == '' ) { $portfolio_secondary_content = 'N/A'; }*/
				$polo_informacoes = get_post_meta($post->ID, $prefix.'polo_informacoes', true); ?>

				<?php					
				 if (get_post_meta($post->ID, '_slideshow_images', true) != '') { 
					 $attachments = get_post_meta($post->ID, '_slideshow_images', true); 					 
					?>

					<!-- BEGIN .page-slider -->
					<div class="page-slider portfolio-slider clearfix">
						<ul class="slides slide-page-loader">

						<?php
						 $attachments_array = array_filter( explode( ',', $attachments ) );

							// Display Attachments
							if ( $attachments_array ) {

								foreach ( $attachments_array as $attachment_id ) {	
									
									$link = wp_get_attachment_link($attachment_id, 'image-style7', false); 
									?>
									<li>
										<?php echo $link; ?>
										<?php 
										if ( get_post_field('post_excerpt', $attachment_id) != '' ) {
											echo '<div class="flex-caption">' . get_post_field('post_excerpt', $attachment_id) . '</div>';
										} 
										?>
									</li>
								<?php }
								 
							 } ?>

							</ul>
						<!-- END .page-slider -->
						</div>

					<?php } /*elseif ( has_post_thumbnail() ) { ?>		
						 
							<?php $src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'image-style7' ); ?>
							<?php echo '<img src="' . $src[0] . '" alt="" class="portfolio-image-single" />'; ?>
							
					<?php }*/ ?>

				  <div class="portfolio-info">
					<ul>

						<li class="col-1">
							<div class="title1 clearfix">
								<h4 class="title-portifolio font-default size-default-1 new-bold"><?php echo 'Polo UAB - '.get_the_title(); //echo 'Apresenta&ccedil;&atilde;o' ?></h4>
								<div class="title-block size-default-2 font-default"></div>
							</div>
							<div class="font-default size-default">
								<script language='javascript' type='text/javascript'>
									document.write(apres_polo);
								</script>
							</div>

							<?php if($sem_apres){echo "Este polo não possui apresentação.<br><br>";} ?>

							<!-- Imagem de apresentação -->
							<div class="row portfolio-image">
								<?php
									$img = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'image-style7' );
									if($img == null || $img == false){
										$src = get_template_directory_uri().'/img/polos_photo_default.jpg';
									} else {
										$src = $img[0];
									}
									// if(getimagesize($src)[1] < 400) {
									if($src != null && $src != false && $src[1] < 400) {
								?>
								<div class="col s12 m12 l12 back-polo center valign-wrapper portfolio-image-single">
								<?php
									} else {
								?>
								<div class="col s12 m12 l12 center valign-wrapper portfolio-image-single">
									<?php
									}				
										//$id = get_page_by_title( get_the_title());
										// print_r($id);
										 																			
										echo '';
									?>
									<img src="<?php echo $src; ?>" alt="" class="center" />
								</div>
							</div>

							<!-- Fim Imagem de apresentação -->
							
							<div class="strip-multicolor">
								<div class="stripped polo-color">
									<span></span>
										<div>
											<div class="title1 clearfix">
												<h4 class="font-default size-default-1 new-bold">Cursos vigentes em
													<script language='javascript' type='text/javascript'>
														document.write(data().concat(":"));
													</script>
												</h4>
												<div class="title-block"></div>
											</div>
											<script language='javascript' type='text/javascript'>
												document.write(cursos)
											</script>
											<div>
												<p class="font-default size-default">
													<?php 
														if($sem_cursos){echo "Não há cursos vigentes no período atual para este polo.";} 
													?>
												</p>
											</div>
										</div>
									</div>
								</div>
						</li>						

						<li class="col-2">
							
						<div class="strip-multicolor">
									<div class="stripped polo-color">
										<span></span>
											<div>
												<div class="title1 clearfix">
													<h4 class="font-default size-default-1 new-bold"><?php echo 'Informa&ccedil;&otilde;es' ?></h4>
													<div class="title-block font-default"></div>
												</div>
												<div class="font-default size-default">												
													<script language='javascript' type='text/javascript'>
														document.write(info_polo);
													</script>
												</div>
												<?php if($sem_info){echo "Não há informações para este polo.";} ?>
											</div>
									</div>
								</div>
						</li>
					</ul>

				    <?php endwhile; endif; ?>
			      </div>
				<?php } ?>
				
		<!-- END .inner-content-wrapper -->
		</div>
		
	<!-- END .main-content -->
	</div>

<!-- END .content-wrapper -->
</div>
</div>

<?php get_footer(); ?>

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


<div id="curso-img" class="header-image-container parallax-container img-banner" style="background-image: url(<?php echo get_bloginfo('template_directory')."/img/posts_cover.jpg"?>);"></div>

<?php
ini_set("display_errors", 1);
/*
LISTA OS POLOS ONDE O CURSO ESTÁ ATIVO
*/

    $nome_curso = get_the_title();
    $dao = new dao();
    $polos = $dao->getPolosDoCurso($nome_curso);

    $polos_json = '';
    if (is_array($polos) && count($polos) > 0) {
        $polos_json = '<p>';
        foreach ($polos as $row_polos) {
            $nome_polo = $row_polos['nome'];
            $pagina = get_page_by_title($nome_polo, 'OBJECT', 'portfolio');
            $nome_polo = formataNomePolo($row_polos['nome']);
            $polos_json .= "<a href='".get_site_url()."/polo/".$pagina->post_name."'>".$nome_polo."</a><br>";
        }
        $polos_json .= '</p>';
    }
    echo "<script language='javascript' type='text/javascript'>var polos = ".json_encode($polos_json)."</script>";
    

    function changeString($inputString){
        $inputString = strtolower_utf8($inputString);						
        $composeWord = explode(" ",$inputString);
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
        $outputString    = utf8_decode($inputString);
        $outputString    = strtolower($outputString);
        $outputString    = utf8_encode($outputString);
        return $outputString;
    }	

/**
BUSCA APRESENTAÇÃO E ÁREA DE ATUAÇÃO (PERFIL DO EGRESSO) DO CURSO
*/

$apres_curso = get_the_title();

$dao = new dao();
$descricaoCurso = $dao->getDescricaoCurso($nome_curso);

$apres_json = '';
$perfil_json = '';

if (is_array($descricaoCurso) && !empty($descricaoCurso)) {
        $apres_json = "<p>" . $descricaoCurso['descricao'] . "</p>";
    $perfil_json = "<p>" . $descricaoCurso['perfil_egresso'] . "</p>";
}

echo "<script language='javascript' type='text/javascript'>var apresentacao = " . json_encode($apres_json) . ";</script>";
echo "<script language='javascript' type='text/javascript'>var perfil_egresso = " . json_encode($perfil_json) . ";</script>";


/**
BUSCA INFORMAÇÕES DO CURSO
*/

    $nome_curso = get_the_title();
    $dao = new dao();
    $info = $dao->getInfoCurso($nome_curso);

    $info_json = '<p>';
    if (is_array($info) && !empty($info)) {
        $coordenador = isset($info['cm_pessoa_coordenador']['nome']) ? $info['cm_pessoa_coordenador']['nome'] : '';
        $email = isset($info['email']) ? $info['email'] : '';
        $telefone = isset($info['telefone_formatado']) ? $info['telefone_formatado'] : '';
        
        $info_json .= "Coordenador: " . $coordenador . "<br>" .
                    "E-mail: <a href='mailto:" . $email . "'>" . $email . "</a><br>" .
                    "Telefone: <a href='tel:" . $telefone . "'>" . $telefone . "</a>";
    }
    $info_json .= '</p>';

    echo "<script language='javascript' type='text/javascript'>var informacoes = " . json_encode($info_json) . ";</script>";
?>

?>

        <!-- BEGIN .page-header -->
        <div class="page-header clearfix" <?php echo $header_image; ?>>

            <div class="page-header-inner clearfix">

                <?php // Get course name
			$course_name = get_post_meta($post->ID, $prefix.'course_name', true);
			if ( $course_name == '' ) { $course_name = __('N/A','qns'); }

			//$course_id = get_post_meta($post->ID, $prefix.'course_id', true);
			$course_program = get_post_meta($post->ID, $prefix.'program_type', true);
			$course_length = get_post_meta($post->ID, $prefix.'course_length', true);
			//$course_description = get_post_meta($post->ID, $prefix.'course_description', true);

			//$course_areaatuacao = get_post_meta($post->ID, $prefix.'course_areaatuacao', true);
			$course_grade = get_post_meta($post->ID, $prefix.'course_grade', true);
			//$course_polos = get_post_meta($post->ID, $prefix.'course_polos', true);
			//$course_informacoes = get_post_meta($post->ID, $prefix.'course_informacoes', true);
		 ?>

                <?php //dimox_breadcrumbs(); ?>
                <!-- <div class="page-title">
			<h2><?php //echo $course_name; ?></h2>
			<div class="page-title-block"></div>
		</div> -->

            </div>

            <!-- END .page-header -->
        </div>

        <div class="courses container">
					<div class="section">

            <!-- BEGIN .content-wrapper -->
            <div class="content-wrapper page-content-wrapper clearfix">

                <span id="conteudo" class="anchor"></span>

                <div class="">
                    <p class="title-default course-name">
                        <?php echo $course_name; ?>
                    </p>
                    <div class="page-title-block"></div>
                </div>

                <!-- BEGIN .main-content -->
                <div <?php echo //$content_id_class;
                    'main-content-full page-content' ?>>

                    <!-- BEGIN .inner-content-wrapper -->
                    <div class="inner-content-wrapper font-default size-default">

                        <?php if (get_post_meta($post->ID, '_slideshow_images', true) != '') { ?>
                        <?php $attachments = get_post_meta($post->ID, '_slideshow_images', true); ?>

                        <!-- BEGIN .page-slider -->
                        <div class="page-slider portfolio-slider clearfix">
                            <ul class="slides slide-page-loader">

                                <?php $attachments_array = array_filter( explode( ',', $attachments ) );

							// Display Attachments
							if ( $attachments_array ) {

								foreach ( $attachments_array as $attachment_id ) {

									$link = wp_get_attachment_link($attachment_id, 'image-style7', false); ?>
                                <li>
                                    <?php echo $link; ?>
                                    <?php if ( get_post_field('post_excerpt', $attachment_id) != '' ) {
											echo '<div class="flex-caption">' . get_post_field('post_excerpt', $attachment_id) . '</div>';
										} ?>
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
                        <div class="responsive-center-image-posts">
                            <div class="stripped"><span></span>
                                <div>
                                    <ul class="list1">
                                        <li><strong>Nível:</strong>
                                            <?php echo $course_program ?>
                                        </li>
                                        <li><strong>Duração:</strong>
                                            <?php echo $course_length ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="stripped"><span></span>
                                <div>
                                    <!--<p><?php //echo $course_description; ?></p>-->
                                    <script language='javascript' type='text/javascript'>
                                        document.write(apresentacao);
                                    </script>
                                </div>
                            </div>




                            <?php if ( post_password_required() ) {
				                    echo qns_password_form();
                                } else { ?>

                            <?php if( have_posts() ) : while ( have_posts() ) : the_post(); ?>

                            <?php /*if ( $course_id != '' ) { ?>
                            <div class="title1 clearfix">
                                <h4>
                                    <?php echo __('Course Code','qns') . ': ' . $course_id; ?>
                                </h4>
                                <div class="title-block"></div>
                            </div>
                            <?php } ?>

                            <?php if( has_post_thumbnail() ) { ?>
                            <div class="course-desc-wrapper clearfix">
                                <?php $src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'image-style11' ); ?>
                                <?php echo '<img src="' . $src[0] . '" alt="" class="course-image" />'; ?>
                                <p class="course-desc">
                                    <?php echo $course_description; ?>
                                </p>
                            </div>
                            <?php } else { ?>
                            <ul class="list1">
                                <li><strong>Nível:</strong>
                                    <?php echo $course_program ?>
                                </li>
                                <li><strong>Duração:</strong>
                                    <?php echo $course_length ?>
                                </li>
                            </ul>
                            <p>
                                <?php echo $course_description; ?>
                            </p>
                            <?php }*/ ?>
                            <!-- BEGIN #tabs -->
                            <div id="">
                                <div class="stripped"><span></span>
                                    <div>
                                        <ul class="nav clearfix">
                                            <li>
                                                <a href="#tabs-tab-title-1">
                                                    <?php echo 'Área de atuação'; ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#tabs-tab-title-2">
                                                    <?php echo 'Grade do curso'; ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#tabs-tab-title-3">
                                                    <?php echo 'Polos ativos'; ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#tabs-tab-title-4">
                                                    <?php echo 'Outras informações' ?>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="stripped"><span></span>
                                    <div>
                                        <a id="tabs-tab-title-1" class="anchor"></a>
                                        <p class="title-default">Área de atuação</p>
                                        <div>
                                            <script language='javascript' type='text/javascript'>
                                                document.write(perfil_egresso);
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <a id="tabs-tab-title-2" class="anchor"></a>
                            <div class="stripped">
                                <div>
                                    <p class="title-default">Grade do curso</p>
                                    <?php echo do_shortcode($course_grade); ?>
                                </div>
                            </div>

                            <div class="stripped">
                                <script language='javascript' type='text/javascript'>                                            
                                    // if(polos != ''){
                                        document.write("<span></span>");
                                    // }
                                </script>                                
                                <div>
                                    <a id="tabs-tab-title-3" class="anchor"></a>
                                    <p class="title-default">Polos ativos</p>
                                    <div>
                                        <script language='javascript' type='text/javascript'>                                                                                            
                                            if(polos != ''){
                                                document.write(polos);                                            
                                            }else{
                                                document.write("Não foram encontrados polos ativos para este curso.");
                                            }
                                        </script>
                                    </div>
                                </div>
                            </div>

                            <div class="stripped"><span></span>
                                <div>
                                    <a id="tabs-tab-title-4" class="anchor"></a>
                                    <p class="title-default">Outras informações</p>
                                    <div>
                                        <script language='javascript' type='text/javascript'>
                                            document.write(informacoes);
                                        </script>
                                    </div>
                                </div>
                            </div>
                            <!-- END #tabs -->

                        </div>

                        <?php endwhile;endif; ?>

                        <?php } ?>
                    </div>

                    <!-- END .inner-content-wrapper -->
                </div>

                <!-- END .main-content -->
            </div>
            
            <!-- END .content-wrapper -->
        </div>
		</div>

        <?php get_footer(); ?>

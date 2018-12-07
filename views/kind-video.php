<?php
/*
 * Video Template
 *
 */

$videos      = $mf2_post->get_videos();
if ( is_array( $videos ) ) {
	foreach( $videos as $video ) {
		$video_attachment = new MF2_Post( $video );
		$cite = $video_attachment->get();
	}
}
$photos      = $mf2_post->get_images();
$first_photo = null;
if ( is_array( $photos ) ) {
	$first_photo = array_pop( array_reverse( $photos ) );
}	
$embed       = null;
if ( is_array( $cite ) && ! $videos ) {
	$url   = ifset( $cite['url'] );
	$embed = self::get_embed( $url );
	if ( ! $embed ) {
		$embed = kind_video_gallery( $url );
	}
}


?>
<section class="response">
<header>
<?php
echo Kind_Taxonomy::get_before_kind( 'video' );
if ( isset( $cite['name'] ) ) {
	echo sprintf( '<span class="p-name">%1s</a>', $cite['name'] );
}

?>
</header>
</section>
<?php
if ( $embed ) {
	printf( '<blockquote class="e-summary">%1s</blockquote>', $embed );
} elseif ( $videos ) {

	$poster = wp_get_attachment_image_url( $first_photo, 'full' );
	echo kind_video_gallery( $videos, array( 'poster' => $poster ) );
}
?>
<?php

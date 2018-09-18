<?php
/*
  Watch Template
 *
 */

if ( ! $cite ) {
	return;
}
$author    = Kind_View::get_hcard( ifset( $cite['author'] ) );
$url       = ifset( $cite['url'] );
$site_name = Kind_View::get_site_name( $cite, $url );
$title     = Kind_View::get_cite_title( $cite, $url );
$embed     = self::get_embed( $url );
$duration  = $mf2_post->get( 'duration', true );
if ( ! $duration ) {
		$duration = calculate_duration( $mf2_post->get( 'dt-start' ), $mf2_post->get( 'dt-end' ) );
}


?>

<section class="response u-watch-of h-cite">
<header>
<?php
$category = get_the_category();
switch ($category) {
		case in_category('movies'):
								echo '<strong><i class="fa fa-film fa-2x"></i></strong>&nbsp;';
								break;
						case in_category('television'):
								echo '<strong><i class="fa fa-tv fa-2x"></i></strong>&nbsp;';
								break;
						case in_category('sports'):
								echo '<strong><i class="fa fa-trophy fa-2x"></i></strong>&nbsp;';
								break;
						default:
								echo Kind_Taxonomy::get_before_kind( 'watch' );
				}
if ( ! $embed ) {
	if ( $title ) {
		echo $title;
	}
	if ( $author ) {
		echo ' ' . __( 'by', 'indieweb-post-kinds' ) . ' ' . $author;
	}
	if ( $site_name ) {
		echo __( ' from ', 'indieweb-post-kinds' ) . '<em>' . $site_name . '</em>';
	}
	if ( $duration ) {
		echo '(<data class="p-duration" value="' . $duration . '">' . Kind_View::display_duration( $duration ) . '</data>)';
	}
}
?>
</header>
<?php
if ( $cite ) {
	if ( $embed ) {
		echo sprintf( '<blockquote class="e-summary">%1s</blockquote>', $embed );
	} elseif ( array_key_exists( 'summary', $cite ) ) {
		echo sprintf( '<blockquote class="e-summary">%1s</blockquote>', $cite['summary'] );
	}
}

// Close Response
?>
</section>

<?php

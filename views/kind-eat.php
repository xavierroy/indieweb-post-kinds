<?php
/*
 * Eat Template
 *
 */

if ( ! $cite ) {
	return;
}
$url    = ifset( $cite['url'] );
?>

<section class="response h-food p-ate">
<header>
<?php
echo Kind_Taxonomy::get_before_kind( 'eat' );
if ( ! $embed ) {
	if ( ! array_key_exists( 'name', $cite ) ) {
		$cite['name'] = self::get_post_type_string( $url );
	}
	if ( ! empty( $url ) ) {
		echo sprintf( '<a href="%1s" class="p-name u-url">%2s</a>', $url, $cite['name'] );
	} else {
		echo sprintf( '<span class="p-name">%1s</span>', $cite['name'] );
	}
	if ( array_key_exists( 'publication', $cite ) ) {
		echo sprintf( ' <em>(<span class="p-publication">%1s</span>)</em>', $cite['publication'] );
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
if ( isset( $_POST['cite_tags'] ) ) {
$cite['category'] = array_filter( explode( ';', $_POST['cite_tags'] ) );
}
echo '<footer><p>Machine tags for this post: <br />';
echo sprintf ( '<small><span class="machinetags">%1s</span></small></p></footer>', implode (", ",$cite['category']) );
// Close Response
?>
</section>

<?php

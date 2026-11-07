<?php
/**
 * Lista wpisów (archiwum aktualności, kategorie, tagi).
 *
 * @package Bizlife
 *
 * @var array $args title, lead.
 */

defined( 'ABSPATH' ) || exit;

$bzl_blog_id = (int) get_option( 'page_for_posts' );
$bzl_title   = $args['title'] ?? '';
$bzl_lead    = $args['lead'] ?? '';

get_template_part(
	'template-parts/components/page-hero',
	null,
	array(
		'eyebrow' => is_home() ? bzl_mod( 'news_eyebrow' ) : __( 'Aktualności', 'bizlife' ),
		'title'   => $bzl_title,
		'lead'    => $bzl_lead,
		'ghost'   => __( 'Wiedza', 'bizlife' ),
	)
);

$bzl_cats = get_categories(
	array(
		'hide_empty' => true,
		'number'     => 12,
	)
);
?>
<section class="section archive tone-dark" aria-label="<?php esc_attr_e( 'Lista artykułów', 'bizlife' ); ?>">
	<div class="container">
		<?php if ( count( $bzl_cats ) > 1 ) : ?>
			<nav class="filter-nav" aria-label="<?php esc_attr_e( 'Kategorie', 'bizlife' ); ?>">
				<ul>
					<li><a class="pill<?php echo is_home() ? ' is-active' : ''; ?>" href="<?php echo esc_url( $bzl_blog_id ? get_permalink( $bzl_blog_id ) : home_url( '/' ) ); ?>"<?php echo is_home() ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Wszystkie', 'bizlife' ); ?></a></li>
					<?php foreach ( $bzl_cats as $bzl_cat ) : ?>
						<?php $bzl_active = is_category( $bzl_cat->term_id ); ?>
						<li><a class="pill<?php echo $bzl_active ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $bzl_cat ) ); ?>"<?php echo $bzl_active ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $bzl_cat->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<ul class="post-grid post-grid--archive">
				<?php
				$bzl_n = 0;
				while ( have_posts() ) {
					the_post();
					$bzl_featured = ( 0 === $bzl_n && ! is_paged() );
					get_template_part(
						'template-parts/cards/post',
						null,
						array(
							'index'    => $bzl_n,
							'featured' => $bzl_featured,
							'heading'  => 2,
						)
					);
					++$bzl_n;
				}
				?>
			</ul>
			<?php
			the_posts_pagination(
				array(
					'mid_size'           => 1,
					'prev_text'          => bzl_icon( 'arrow-left' ) . '<span class="screen-reader-text">' . esc_html__( 'Poprzednia strona', 'bizlife' ) . '</span>',
					'next_text'          => '<span class="screen-reader-text">' . esc_html__( 'Następna strona', 'bizlife' ) . '</span>' . bzl_icon( 'arrow-right' ),
					'before_page_number' => '<span class="screen-reader-text">' . esc_html__( 'Strona', 'bizlife' ) . ' </span>',
					'aria_label'         => __( 'Strony artykułów', 'bizlife' ),
					'class'              => 'pagination',
				)
			);
			?>
		<?php else : ?>
			<div class="empty-state">
				<p><?php esc_html_e( 'Nie ma jeszcze artykułów w tej kategorii.', 'bizlife' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
get_template_part( 'template-parts/sections/cta', null, array( 'tone' => 'tone-darker' ) );

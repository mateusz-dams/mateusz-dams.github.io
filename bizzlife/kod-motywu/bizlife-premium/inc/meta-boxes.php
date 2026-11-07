<?php
/**
 * Pola dodatkowe usług i FAQ (natywne meta boxy – bez ACF).
 *
 * @package Bizlife
 */

defined( 'ABSPATH' ) || exit;

/**
 * Rejestracja meta boxów.
 */
function bzl_add_meta_boxes() {
	add_meta_box( 'bzl_service_details', __( 'Szczegóły usługi', 'bizlife' ), 'bzl_service_meta_box', 'bzl_service', 'normal', 'high' );
	add_meta_box( 'bzl_faq_details', __( 'Ustawienia pytania', 'bizlife' ), 'bzl_faq_meta_box', 'bzl_faq', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'bzl_add_meta_boxes' );

/**
 * Meta box usługi.
 *
 * @param WP_Post $post Wpis.
 */
function bzl_service_meta_box( $post ) {
	wp_nonce_field( 'bzl_service_meta', 'bzl_service_meta_nonce' );
	$icon     = (string) get_post_meta( $post->ID, 'bzl_icon', true );
	$scope    = (string) get_post_meta( $post->ID, 'bzl_scope', true );
	$audience = (string) get_post_meta( $post->ID, 'bzl_audience', true );
	$topic    = (int) get_post_meta( $post->ID, 'bzl_faq_topic', true );
	?>
	<p class="description"><?php esc_html_e( 'Krótki opis na karcie usługi pobierany jest z pola „Zajawka” (panel boczny edytora). Zdjęcie wyróżniające wyświetla się w nagłówku podstrony usługi. Kolejność kart ustawisz w polu „Kolejność” (Atrybuty).', 'bizlife' ); ?></p>
	<p>
		<label for="bzl_icon"><strong><?php esc_html_e( 'Ikona', 'bizlife' ); ?></strong></label><br>
		<select name="bzl_icon" id="bzl_icon">
			<?php foreach ( bzl_icon_choices() as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $icon, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="bzl_scope"><strong><?php esc_html_e( 'Zakres usługi', 'bizlife' ); ?></strong></label><br>
		<textarea name="bzl_scope" id="bzl_scope" rows="6" class="widefat" placeholder="<?php esc_attr_e( 'Jedna pozycja w linii', 'bizlife' ); ?>"><?php echo esc_textarea( $scope ); ?></textarea>
		<span class="description"><?php esc_html_e( 'Wyświetlane jako lista „Co obejmuje usługa” w panelu bocznym podstrony.', 'bizlife' ); ?></span>
	</p>
	<p>
		<label for="bzl_audience"><strong><?php esc_html_e( 'Dla kogo', 'bizlife' ); ?></strong></label><br>
		<textarea name="bzl_audience" id="bzl_audience" rows="4" class="widefat" placeholder="<?php esc_attr_e( 'Jedna pozycja w linii', 'bizlife' ); ?>"><?php echo esc_textarea( $audience ); ?></textarea>
	</p>
	<p>
		<label for="bzl_faq_topic"><strong><?php esc_html_e( 'Pytania FAQ na podstronie', 'bizlife' ); ?></strong></label><br>
		<?php
		wp_dropdown_categories(
			array(
				'taxonomy'          => 'bzl_faq_topic',
				'name'              => 'bzl_faq_topic',
				'id'                => 'bzl_faq_topic',
				'selected'          => $topic,
				'show_option_none'  => __( '— nie pokazuj —', 'bizlife' ),
				'option_none_value' => 0,
				'hide_empty'        => false,
				'hierarchical'      => true,
			)
		);
		?>
		<br><span class="description"><?php esc_html_e( 'Wybierz temat FAQ, którego pytania pojawią się pod treścią usługi.', 'bizlife' ); ?></span>
	</p>
	<?php
}

/**
 * Zapis pól usługi.
 *
 * @param int $post_id ID.
 */
function bzl_save_service_meta( $post_id ) {
	if ( ! isset( $_POST['bzl_service_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bzl_service_meta_nonce'] ) ), 'bzl_service_meta' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$icon = isset( $_POST['bzl_icon'] ) ? sanitize_key( wp_unslash( $_POST['bzl_icon'] ) ) : '';
	if ( ! array_key_exists( $icon, bzl_icon_choices() ) ) {
		$icon = '';
	}
	update_post_meta( $post_id, 'bzl_icon', $icon );
	update_post_meta( $post_id, 'bzl_scope', isset( $_POST['bzl_scope'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bzl_scope'] ) ) : '' );
	update_post_meta( $post_id, 'bzl_audience', isset( $_POST['bzl_audience'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bzl_audience'] ) ) : '' );
	update_post_meta( $post_id, 'bzl_faq_topic', isset( $_POST['bzl_faq_topic'] ) ? absint( $_POST['bzl_faq_topic'] ) : 0 );
}
add_action( 'save_post_bzl_service', 'bzl_save_service_meta' );

/**
 * Meta box FAQ.
 *
 * @param WP_Post $post Wpis.
 */
function bzl_faq_meta_box( $post ) {
	wp_nonce_field( 'bzl_faq_meta', 'bzl_faq_meta_nonce' );
	$on_home = (bool) get_post_meta( $post->ID, 'bzl_on_home', true );
	?>
	<p>
		<label>
			<input type="checkbox" name="bzl_on_home" value="1" <?php checked( $on_home ); ?>>
			<?php esc_html_e( 'Pokaż na stronie głównej', 'bizlife' ); ?>
		</label>
	</p>
	<p class="description"><?php esc_html_e( 'Jeśli żadne pytanie nie jest zaznaczone, strona główna pokaże pierwsze pytania według kolejności.', 'bizlife' ); ?></p>
	<?php
}

/**
 * Zapis FAQ.
 *
 * @param int $post_id ID.
 */
function bzl_save_faq_meta( $post_id ) {
	if ( ! isset( $_POST['bzl_faq_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bzl_faq_meta_nonce'] ) ), 'bzl_faq_meta' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	update_post_meta( $post_id, 'bzl_on_home', ! empty( $_POST['bzl_on_home'] ) );
}
add_action( 'save_post_bzl_faq', 'bzl_save_faq_meta' );

/**
 * Kolumna „Kolejność” w listach usług i FAQ.
 *
 * @param array $cols Kolumny.
 * @return array
 */
function bzl_order_column( $cols ) {
	$cols['menu_order'] = __( 'Kolejność', 'bizlife' );
	return $cols;
}
add_filter( 'manage_bzl_service_posts_columns', 'bzl_order_column' );
add_filter( 'manage_bzl_faq_posts_columns', 'bzl_order_column' );

/**
 * Wartość kolumny.
 *
 * @param string $col Kolumna.
 * @param int    $id  ID.
 */
function bzl_order_column_value( $col, $id ) {
	if ( 'menu_order' === $col ) {
		echo (int) get_post_field( 'menu_order', $id );
	}
}
add_action( 'manage_bzl_service_posts_custom_column', 'bzl_order_column_value', 10, 2 );
add_action( 'manage_bzl_faq_posts_custom_column', 'bzl_order_column_value', 10, 2 );

/**
 * Domyślne sortowanie list w panelu wg kolejności.
 *
 * @param WP_Query $q Zapytanie.
 */
function bzl_admin_order( $q ) {
	if ( ! is_admin() || ! $q->is_main_query() ) {
		return;
	}
	$pt = $q->get( 'post_type' );
	if ( in_array( $pt, array( 'bzl_service', 'bzl_faq' ), true ) && ! $q->get( 'orderby' ) ) {
		$q->set( 'orderby', 'menu_order' );
		$q->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'bzl_admin_order' );

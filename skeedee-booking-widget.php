<?php
/*
Plugin Name: Skeedee Booking Widget
Description: Plugin will connect widget from <a href="https://skeedee.com/">Skeedee.com</a> to your WordPress website without code editing.
Version: 1.0
Author: Skeedee.com
Author URI: https://skeedee.com/
*/

add_action('admin_menu', 'skde_setting_page');

function skde_setting_page() {
    add_options_page('Skeedee Booking Widget', 'Skeedee Booking Widget', 'manage_options', basename(__FILE__), 'skde_admin_form');
}

function skde_admin_form() {

    $wIdValue = get_option('widgetId');

    if ( isset($_POST['submit']) )
    {
       if ( function_exists('current_user_can') &&
            !current_user_can('manage_options') )
                die ( _e('Hacker?', 'skeedee') );

        if (function_exists ('check_admin_referer') )
        {
            check_admin_referer('skeedee_form');
        }

        if ( isset( $_POST['wIdValue'] ) ) {
          $wIdValue = sanitize_text_field( $_POST['wIdValue'] );
		    }

        update_option('widgetId', $wIdValue);
    }
    ?>
    <div class='wrap'>
        <h2><?php _e('Skeedee Widget Settings', 'skeedee'); ?></h2>
        <form name="skeedee" method="post"
            action="<?php echo esc_html($_SERVER['PHP_SELF']); ?>?page=skeedee-booking-widget.php&amp;updated=true">

            <?php
                if (function_exists ('wp_nonce_field') )
                {
                    wp_nonce_field('skeedee_form');
                }
            ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Widget ID:', 'skeedee'); ?></th>
                    <td>
                        <input type="text" name="wIdValue"
                            size="80" value="<?php echo esc_html($wIdValue); ?>" />
                    </td>
                </tr>
            </table>
            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="widgetId" />

            <p class="submit">
            <input type="submit" name="submit" value="<?php _e('Save Changes') ?>" />
            </p>
        </form>
    </div>
    <?
}

function skde_add_plugin_page_settings_link( $links ) {
  	$links[] = '<a href="' .
  		admin_url( 'admin.php?page=skeedee-booking-widget.php' ) .
  		'">' . __('Settings') . '</a>';
  	return $links;
}

add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'skde_add_plugin_page_settings_link');

function skde_prefix_footer_code() {
    if ( get_option('widgetId') !== null ) {
      $widgetId = get_option('widgetId');
    }
    wp_enqueue_script( 'skeedee', 'https://widget.skeedee.com/components/widget.js', array(), null, true );
    wp_add_inline_script( 'skeedee', 'new window.Widget("'.$widgetId.'");' );
}

add_action('wp_footer', 'skde_prefix_footer_code' );

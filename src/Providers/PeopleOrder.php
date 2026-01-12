<?php

namespace People\Providers;
use WP_Query;

class PeopleOrder implements Provider
{
    public function __construct()
    {
        add_action('admin_menu', function() {
            add_submenu_page(
                'edit.php?post_type=people',
                'Order by Group',
                'Order by Group',
                'manage_options',
                'order-by-group',
                [$this, 'people_order_page']
            );
        });

        add_action('admin_init', function(){
            if (
                isset($_POST['people_order_data'], $_POST['cat_id']) &&
                check_admin_referer('save_people_order')
            ) {
                $cat_id = $_POST['cat_id'];
                $ids = array_filter(array_map('intval', explode(',', $_POST['people_order_data'])));

                if ($cat_id === 'all') {
                    update_option('people_post_order_all', $ids);
                } else {
                    $cat_id = intval($cat_id);
                    update_term_meta($cat_id, 'people_post_order', $ids);
                }

                add_action('admin_notices', function(){
                    echo '<div class="updated"><p>Order saved successfully!</p></div>';
                });
            }
        });

        add_action('admin_enqueue_scripts', function($hook) {
            if (isset($_GET['page']) && $_GET['page'] === 'order-by-group') {
                wp_enqueue_script('jquery-ui-sortable');
            }
        });
    }

    public function register()
    {
        //
    }

    public function people_order_page() {
        $selected_cat = isset($_GET['cat_id']) ? $_GET['cat_id'] : '';
        $categories = get_terms([
            'taxonomy' => 'people_group',
            'hide_empty' => false,
        ]);
        ?>
        <div class="wrap">
            <h1>Order Peoples by Group</h1>
            <form method="get">
                <input type="hidden" name="post_type" value="people">
                <input type="hidden" name="page" value="order-by-group">
                <label for="cat_id">Select Group:</label>
                <select name="cat_id" id="cat_id" onchange="this.form.submit()">
                    <option value="">-- Select --</option>
                    <option value="all" <?php selected($selected_cat, 'all'); ?>>All Groups</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo esc_attr($cat->term_id); ?>" <?php selected($selected_cat, $cat->term_id); ?>>
                            <?php echo esc_html($cat->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <?php if ($selected_cat): ?>
                <?php
                if ($selected_cat === 'all') {
                    $saved_order = get_option('people_post_order_all', []);
                    if (!is_array($saved_order)) $saved_order = [];

                    $posts = get_posts([
                        'post_type' => 'people',
                        'posts_per_page' => -1,
                    ]);
                } else {
                    $selected_cat = intval($selected_cat);
                    $saved_order = get_term_meta($selected_cat, 'people_post_order', true);
                    if (!is_array($saved_order)) $saved_order = [];

                    $posts = get_posts([
                        'post_type' => 'people',
                        'posts_per_page' => -1,
                        'tax_query' => [[
                            'taxonomy' => 'people_group',
                            'field'    => 'term_id',
                            'terms'    => $selected_cat,
                        ]]
                    ]);
                }

                usort($posts, function($a, $b) use ($saved_order) {
                    $pos_a = array_search($a->ID, $saved_order);
                    $pos_b = array_search($b->ID, $saved_order);
                    if ($pos_a === false) $pos_a = PHP_INT_MAX;
                    if ($pos_b === false) $pos_b = PHP_INT_MAX;
                    return $pos_a - $pos_b;
                });
                ?>
                <form method="post">
                    <?php wp_nonce_field('save_people_order'); ?>
                    <input type="hidden" name="cat_id" value="<?php echo esc_attr($selected_cat); ?>">
                    <ul id="people-order-list" style="list-style:none;max-width:600px;padding:0;">
                        <?php foreach ($posts as $post): ?>
                            <li data-id="<?php echo esc_attr($post->ID); ?>" style="padding:5px;border:1px solid #ccc;margin-bottom:3px;background:#fff;cursor:move;">
                                <?php echo esc_html($post->post_title); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <input type="hidden" name="people_order_data" id="people_order_data" value="">
                    <p><button type="submit" class="button button-primary">Save Order</button></p>
                </form>
                <script>
                    jQuery(document).ready(function($){
                        $('#people-order-list').sortable({
                            update: function(){
                                var order = [];
                                $('#people-order-list li').each(function(){
                                    order.push($(this).data('id'));
                                });
                                $('#people_order_data').val(order.join(','));
                            }
                        });

                        var initialOrder = [];
                        $('#people-order-list li').each(function(){
                            initialOrder.push($(this).data('id'));
                        });
                        $('#people_order_data').val(initialOrder.join(','));
                    });
                </script>
            <?php endif; ?>
        </div>
        <?php
    }
}

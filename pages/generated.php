<?php
$api_key = $_ENV['GIPHY_API_KEY'];

$response = wp_remote_get("https://api.giphy.com/v1/gifs/random?api_key=$api_key&tag=office+celebrations&rating=g");
if (is_wp_error($response)) {
    // Handle error
} else {
    $data = json_decode(wp_remote_retrieve_body($response));
    $random_gif_url = esc_html($data->data->embed_url);
}
?>
<div class="container">
    <div class="screen">
        <div class="screen__content">
            <div class="login">
                <div class="login__field">
                    <h1 class="login__title">Your post has been generated!</h1>
                </div>
                <div class="login__field">
                    <div style="width:100%">
                        <div style="height:0;padding-bottom:56.25%;position:relative;width:100%"><iframe allowfullscreen="" frameBorder="0" height="100%" src="<?php echo $random_gif_url ?>" style="left:0;position:absolute;top:0" width="100%"></iframe></div>
                    </div>
                </div>
                <a href="<?php echo get_edit_post_link($post_id) ?>" class="login__submit">
                    <span class="button__text">Go to your generated post!</span>
                </a>
            </div>
        </div>
        <div class="screen__background">
            <span class="screen__background__shape screen__background__shape4"></span>
            <span class="screen__background__shape screen__background__shape3"></span>
            <span class="screen__background__shape screen__background__shape2"></span>
            <span class="screen__background__shape screen__background__shape1">
                <img class="copywriter" src="<?php echo plugin_dir_url(dirname(__FILE__)) . 'images/copywriter-new.png'; ?>">
            </span>
        </div>
    </div>
</div>
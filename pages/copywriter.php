<?php
// wp_enqueue_script('tenor', 'https://tenor.com/embed.js', [], null);
$current_user = wp_get_current_user();
$username = $current_user->user_login;
?>

<div class="container">
    <div class="screen">
        <div class="screen__content">
            <form name="generate" class="login" action="" method="POST">
                <div id="not-loading" style="display: block">
                    <h3 class="welcome">Welcome <?php echo esc_html(ucfirst($username)) ?>, </h3>
                    <div class="login__field">
                        <label>Fill in the subject of the new post in this field.</label>
                        <input type="text" class="login__input" placeholder="Subject" name="subject" required>
                    </div>
                    <div class="login__field">
                        <label>Fill in the goal you want to achieve by publishing this new post here. (be specific)</label>
                        <input type="text" class="login__input" placeholder="The goal you want to reach" name="goal">
                    </div>
                    <div class="login__field">
                        <label>You can also specify which headings will be used in the post by separating them with commas.</label>
                        <input type="text" class="login__input" placeholder="Subheadings-1, subheading-2, subheading-3" name="subheadings">
                    </div>
                    <button type="submit" id="generate_content" class="login__submit" name="generate">
                        <span class="button__text">Generate content</span>
                        <!-- <i class="button__icon fas fa-chevron-right"></i> -->
                    </button>
                </div>
                <div id="loading-gif" style="display: none;">
                    <div class="login__field">
                        <h1 class="login__title">Just a moment.....</h1>
                    </div>
                    <img class="loading-icon" src="<?php echo plugin_dir_url(dirname(__FILE__)) . 'images/loading.gif'; ?>">
                    <script type="text/javascript" async src=""></script>
                </div>
            </form>
        </div>
        <div class="screen__background">
            <span class="screen__background__shape screen__background__shape4"></span>
            <span class="screen__background__shape screen__background__shape3"></span>
            <span class="screen__background__shape screen__background__shape2"></span>
            <span class="screen__background__shape screen__background__shape1"><img class="copywriter" src="<?php echo plugin_dir_url(dirname(__FILE__)) . 'images/copywriter-new.png'; ?>"></span>
        </div>
    </div>
</div>

<script>
    document.getElementById('generate_content').addEventListener('click', function() {
        document.getElementById('loading-gif').style.display = 'block';
        document.getElementById('not-loading').style.display = 'none';
    });
</script>
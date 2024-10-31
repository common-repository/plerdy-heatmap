<div class="wrap">
    <h1><img src="<?php echo plugins_url('/../images/icon.png', __FILE__);?>">Plerdy for WordPress</h1>
    <p>Create an account or log in to Plerdy <a href="https://a.plerdy.com/auth/login" target="_blank">https://a.plerdy.com/auth/login</a></p>

    <?php
    settings_errors();
    ?>

    <form action="options.php" method="post">
        <?php
        settings_fields( 'plerdy-options' );
        do_settings_sections( 'plerdy' );
        ?>

        <table class="form-table form-plerdy">
            <tr valign="top">
                <td>
                    <strong>Add a tracking code</strong>
                    <br>
                    <textarea name="plerdy_tracking_script" rows="8" cols="80"><?php echo esc_attr( get_option('plerdy_tracking_script') ); ?></textarea>
                </td>
            </tr>

            <tr valign="top">
                <td>
                    <strong>Add A/B testing tracking code</strong>
                    <br>
                    <textarea name="plerdy_abtracking_script" rows="8" cols="80"><?php echo esc_attr( get_option('plerdy_abtracking_script') ); ?></textarea>
                </td>
            </tr>
            <tr>
                <td style="display: flex;">
                    <p style="display: flex; align-items: center;">
                        <input type="checkbox" value="checked"  <?php echo esc_attr( get_option('checkbox') ); ?> id="checkbox" name="checkbox">Collect e-commerce data "Sales performance"</p>
                    <span class="waper">&#x3f; <img class="imgplerdynone" style="width: 629px; margin-left: 22px;" src="<?php echo plugins_url('/../images/plerdy.png', __FILE__);?>"></span>
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>

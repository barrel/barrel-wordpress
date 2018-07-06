<?php
/**
 * Plugin Options page
 */
?>
<div class="wrap">
  <h2><?php _e( 'Tag Manager - Options', 'barrel-tag-manager'); ?></h2>

  <hr />
  <div id="poststuff">
  <div id="post-body" class="metabox-holder columns-2">
    <div id="post-body-content">
      <div class="postbox">
        <div class="inside">
          <form name="dofollow" action="options.php" method="post">

            <?php settings_fields( 'barrel-tag-manager' ); ?>

            <h3 class="brrl-labels" for="<?= B_Tag_Manager::INSERT_HEADER; ?>"><?php _e( 'Scripts in header:', 'barrel-tag-manager'); ?></h3>
            <textarea style="width:98%;" rows="10" cols="57" id="<?= B_Tag_Manager::INSERT_HEADER; ?>" name="<?= B_Tag_Manager::INSERT_HEADER; ?>"><?php echo esc_html( get_option( B_Tag_Manager::INSERT_HEADER ) ); ?></textarea>
            <p><?php _e( 'Above script(s) will be inserted into the <code>&lt;head&gt;</code> section using <code>wp_head</code> hook.', 'barrel-tag-manager'); ?></p>
            <p><?php _e( 'Exclude any <code>&lt;img/&gt;</code>, <code>&lt;noscript/&gt;</code> or <code>&lt;iframe/&gt;</code> tags as this would be considered invalid HTML.', 'barrel-tag-manager'); ?></p><hr />

            <h3 class="brrl-labels afterbodylabel" for="<?= B_Tag_Manager::INSERT_AFTERBODY; ?>"><?php _e( 'Scripts in afterbody:', 'barrel-tag-manager'); ?></h3>
            <textarea style="width:98%;" rows="10" cols="57" id="<?= B_Tag_Manager::INSERT_AFTERBODY; ?>" name="<?= B_Tag_Manager::INSERT_AFTERBODY; ?>"><?php echo esc_html( get_option( B_Tag_Manager::INSERT_AFTERBODY ) ); ?></textarea>
            <p><?php _e( 'Above script(s) will be inserted just after <code>&lt;body&gt;</code> open tag using <code>after_body_open</code> hook.', 'barrel-tag-manager'); ?></p>
            <p><?php _e( 'The theme must include a <code>&lt;?php do_action( "after_body_open" ); ?&gt;</code> just after the <code>&lt;body&gt;</code> open tag.', 'barrel-tag-manager'); ?></p><hr/>

            <h3 class="brrl-labels footerlabel" for="<?= B_Tag_Manager::INSERT_FOOTER; ?>"><?php _e( 'Scripts in footer:', 'barrel-tag-manager'); ?></h3>
            <textarea style="width:98%;" rows="10" cols="57" id="<?= B_Tag_Manager::INSERT_FOOTER; ?>" name="<?= B_Tag_Manager::INSERT_FOOTER; ?>"><?php echo esc_html( get_option( B_Tag_Manager::INSERT_FOOTER ) ); ?></textarea>
            <p><?php _e( 'Above script(s) will be inserted just before <code>&lt;/body&gt;</code> tag using <code>wp_footer</code> hook.', 'barrel-tag-manager'); ?></p>

          <p class="submit">
            <input class="button button-primary" type="submit" name="Submit" value="<?php _e( 'Save settings', 'barrel-tag-manager'); ?>" />
          </p>

          </form>
        </div>
    </div>
    </div>

    </div>
  </div>
</div>

<?php
/**
 * Plugin meta for single post or page type.
 */
?>
<div class="brrl_meta_control">

	<p>
		<textarea name="_post_head_script[head_script_code]" rows="5" style="width:98%;"><?php if(!empty($meta['head_script_code'])) echo $meta['head_script_code']; ?></textarea>
	</p>

	<p><?php _e('Add some code to <code>&lt;head&gt;</code>', 'barrel-tag-manager'); ?>.</p>
</div>

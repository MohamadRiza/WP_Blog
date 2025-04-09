<?php

use SmashBalloon\Reviews\Common\Admin\SBR_Support_Tool;

if (!defined('ABSPATH')) {
	return;
}
$role_id = SBR_Support_Tool::$plugin . SBR_Support_Tool::$role;
$cap = $role_id;
if (!current_user_can($cap)) {
	return;
}


$all_sources = \SmashBalloon\Reviews\Common\Customizer\Db::get_facebook_sources();
if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'sbr-api-check')) {
	$results = $this->validate_and_sanitize_support_settings($_POST);
}



?>
<div class="sbr_support_tools_wrap">
	<form method="post" action="">
		<?php wp_nonce_field('sbr-api-check'); ?>

		<div class="sbr_support_tools_field_group">
			<label for="sb_reviews_support_source">
				<?php esc_html_e('Connected Sources', 'custom-reviews-feed'); ?>
			</label>
			<select id="sb_reviews_support_source" name="sb_reviews_support_source">
				<option value="">Please Select</option>
				<?php foreach ($all_sources as $source): ?>
					<option value="<?php echo esc_attr($source['account_id']); ?>">
						<?php echo esc_html($source['name']); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="sbr_support_tools_field_group">
			<label for="sb_reviews_support_endpoint">
				<?php esc_html_e('Endpoint', 'custom-reviews-feed'); ?>
			</label>
			<select id="sb_reviews_support_endpoint" name="sb_reviews_support_endpoint">
				<?php foreach ($this->available_endpoints() as $key => $endpoint): ?>
					<option value="<?php echo esc_attr($key); ?>">
						<?php echo esc_html($endpoint); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<button class="button button-primary" type="submit">Submit</button>

	</form>
</div>


<style>
	.sbr_support_tools_wrap {
		padding: 20px;
		padding-top: 50px;
	}

	.sbr_support_tools_field_group {
		margin-bottom: 20px;
		width: 50%;
	}

	.sbr_support_tools_field_group label {
		display: block;
		font-weight: bold;
	}

	.sbr_support_tools_field_group>* {
		width: 100%;
	}

	.sbr_support_tools_field_group_hide_show {
		display: block;
	}
</style>

<script>
	jQuery(function ($) {
		$('#sb_reviews_support_endpoint').on('change', function () {
			let endpoint = $(this).val();
			$('.sbr_support_tools_field_group_hide_show').hide();
			$('.sbr_support_tools_field_group_hide_show[data-show*="' + endpoint + '"]').show();
		})

	});

</script>
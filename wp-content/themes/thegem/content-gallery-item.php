<?php
	$thegem_item = get_post($attachment_id);

	if (!$thegem_item) {
		return;
	}

	$thegem_highlight = (bool) get_post_meta($thegem_item->ID, 'highlight', true);
	$thegem_highligh_type = get_post_meta($thegem_item->ID, 'highligh_type', true);
	if (!$thegem_highligh_type) {
		$thegem_highligh_type = 'squared';
	}
	$thegem_attachment_link = get_post_meta($thegem_item->ID, 'attachment_link', true);
	$thegem_single_icon = true;

	if (!empty($thegem_attachment_link)) {
		$thegem_single_icon = false;
	}

	if ($params['type'] == 'grid') {
		$thegem_size = 'thegem-gallery-justified';
		if ($thegem_highlight) {
			$thegem_size = 'thegem-gallery-justified-double';
			if ($params['layout'] == '4x')
				$thegem_size = 'thegem-gallery-justified-double-4x';
		}
		if ($params['layout'] == '2x') {
			$thegem_size = 'thegem-gallery-justified-double';
		}
		if ($params['style'] == 'masonry')
			if ($thegem_highlight)
				$thegem_size = 'thegem-gallery-masonry-double';
			else
				$thegem_size = 'thegem-gallery-masonry';

		if ($params['layout'] == '100%')
			$thegem_size .= '-100';

		if ($params['style'] == 'metro')
			$thegem_size = 'thegem-gallery-metro';

		if ($thegem_highlight && $params['style'] != 'metro' && $thegem_highligh_type != 'squared') {
			$thegem_size .= '-' . $thegem_highligh_type;
		}
	} else {
		$thegem_size = 'thegem-container';
		$thegem_thumb_image_url = wp_get_attachment_image_src($thegem_item->ID, 'thegem-post-thumb');
	}

	$thegem_small_image_url = thegem_generate_thumbnail_src($thegem_item->ID, $thegem_size);
	$thegem_full_image_url = wp_get_attachment_image_src($thegem_item->ID, 'full');

	$thegem_classes = array('gallery-item');

	if ($params['type'] == 'grid' && $params['style'] != 'metro' && $params['layout'] == '2x') {
	  $thegem_classes = array_merge($thegem_classes, array('col-lg-6', 'col-md-6', 'col-sm-6', 'col-xs-12'));
	}

	if ($params['type'] == 'grid' && $params['style'] != 'metro' && $params['layout'] == '3x') {
		if ($thegem_highlight && $thegem_highligh_type != 'vertical')
			$thegem_classes = array_merge($thegem_classes, array('col-lg-8', 'col-md-8', 'col-sm-12', 'col-xs-12'));
		else
			$thegem_classes = array_merge($thegem_classes, array('col-lg-4', 'col-md-4', 'col-sm-6', 'col-xs-6'));
	}

	if ($params['type'] == 'grid' && $params['style'] != 'metro' && $params['layout'] == '4x') {
		if ($thegem_highlight && $thegem_highligh_type != 'vertical')
			$thegem_classes = array_merge($thegem_classes, array('col-lg-6', 'col-md-6', 'col-sm-8', 'col-xs-12'));
		else
			$thegem_classes = array_merge($thegem_classes, array('col-lg-3', 'col-md-3', 'col-sm-4', 'col-xs-6'));
	}

	if ($params['type'] == 'grid' && $params['style'] != 'metro' && $thegem_highlight)
		$thegem_classes[] = 'double-item';

	if ($params['type'] == 'grid' && $params['style'] != 'metro' && $thegem_highlight && $thegem_highligh_type != 'squared') {
		$thegem_classes[] = 'double-item-' . $thegem_highligh_type;
	}

	$thegem_wrap_classes = $params['item_style'];

	if ($params['type'] == 'grid')
		$thegem_classes[] = 'item-animations-not-inited';


?>





<li
	<?php if ($params['gaps_size']): ?>style="padding: <?php echo($params['gaps_size'] / 2);?>px"<?php endif;?>

	<?php post_class($thegem_classes); ?>>
	<div class="wrap <?php if($params['type'] == 'grid' && $params['item_style'] != ''): ?> gem-wrapbox-style-<?php echo esc_attr($thegem_wrap_classes); ?><?php endif; ?>">
		<?php if($params['type'] == 'grid' && $params['item_style'] == '11'): ?>
			<div class="gem-wrapbox-inner"><div class="shadow-wrap">
		<?php endif; ?>
		<div class="overlay-wrap">
			<div class="image-wrap <?php if($params['type'] == 'grid' && $params['item_style'] == '11'): ?>img-circle<?php endif; ?>">
				<img src="<?php echo esc_url($thegem_small_image_url[0]); ?>" <?php if($params['type'] == 'slider'): ?>data-thumb-url="<?php echo esc_url($thegem_thumb_image_url[0]); ?>"<?php endif; ?> <?php if($params['type'] == 'grid'): ?>width="<?php echo esc_attr($thegem_small_image_url[1]); ?>" height="<?php echo esc_attr($thegem_small_image_url[2]); ?>"<?php endif; ?> alt="">
			</div>
			<div class="overlay <?php if($params['type'] == 'grid' && $params['item_style'] == '11'): ?>img-circle<?php endif; ?>">
				<div class="overlay-circle"></div>
				<?php if($thegem_single_icon): ?>
					<a href="<?php echo esc_url($thegem_full_image_url[0]); ?>" class="gallery-item-link fancy-gallery" rel="gallery-<?php echo esc_attr($gallery_uid); ?>">
						<span class="slide-info">
							<?php if(!empty($thegem_item->post_excerpt)) : ?>
								<span class="slide-info-title">
									<?php echo $thegem_item->post_excerpt; ?>
								</span>
								<?php if(!empty($thegem_item->post_content)) : ?>
									<span class="slide-info-summary">
										<?php echo $thegem_item->post_content; ?>
									</span>
								<?php endif; ?>
							<?php endif; ?>
						</span>
					</a>
				<?php endif; ?>
				<div class="overlay-content">
					<div class="overlay-content-center">
						<div class="overlay-content-inner">
							<a href="<?php echo esc_url($thegem_full_image_url[0]); ?>" class="icon photo <?php if(!$thegem_single_icon): ?>fancy-gallery<?php endif; ?>" <?php if(!$thegem_single_icon): ?>rel="gallery-<?php echo esc_attr($gallery_uid); ?>"<?php endif; ?> >

								<?php if(!$thegem_single_icon): ?>
									<span class="slide-info">
										<?php if(!empty($thegem_item->post_excerpt)) : ?>
											<span class="slide-info-title ">
												<?php echo $thegem_item->post_excerpt; ?>
											</span>
											<?php if(!empty($thegem_item->post_content)) : ?>
												<span class="slide-info-summary">
													<?php echo $thegem_item->post_content; ?>
												</span>
											<?php endif; ?>
										<?php endif; ?>
									</span>
								<?php endif; ?>
							</a>

							<?php if (!empty($thegem_attachment_link)): ?>
								<a href="<?php echo esc_url($thegem_attachment_link); ?>" target="_blank" class="icon link"></a>
							<?php endif; ?>
							<div class="overlay-line"></div>
							<?php if(!empty($thegem_item->post_excerpt)) : ?>
								<div class="title">
									<?php echo $thegem_item->post_excerpt; ?>
								</div>
							<?php endif; ?>
							<?php if(!empty($thegem_item->post_content)) : ?>
								<div class="subtitle">
									<?php echo $thegem_item->post_content; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php if($params['type'] == 'grid' && $params['item_style'] == '11'): ?>
			</div></div>
		<?php endif; ?>
	</div>
	<?php if ($params['style']  == 'metro' &&  $params['item_style']):?><?php endif;?>
</li>

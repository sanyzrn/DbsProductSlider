<?php
if (!defined('ABSPATH')) {
    exit;
}
$preset = $this->sanitize_choice($settings['card_preset'] ?? 'default', ['default', 'minimal', 'catalog'], 'default');
$wrapper_class = 'pce-v5-wrapper pce-preset-' . $preset;
if ($slider_options['respectReducedMotion']) {
    $wrapper_class .= ' pce-respect-motion';
}
if (($settings['equal_height'] ?? 'yes') === 'yes') {
    $wrapper_class .= ' pce-equal-height';
}
if (($settings['image_ratio'] ?? '') === 'auto') {
    $wrapper_class .= ' pce-image-auto';
}
$direction = $slider_options['flowDirection'] === 'auto' ? (is_rtl() ? 'rtl' : 'ltr') : $slider_options['flowDirection'];
$label = PCE_Settings::text($settings['carousel_label'] ?? '') ?: __('Product carousel', 'advanced-carousel-pro');
$slider_id = $widget_id . '-slider';
$show = static function ($part) use ($settings) { return ($settings['show_' . $part] ?? 'yes') === 'yes'; };
?>
<div id="<?php echo esc_attr($widget_id); ?>" class="<?php echo esc_attr($wrapper_class); ?>" dir="<?php echo esc_attr($direction); ?>"
    role="region" aria-roledescription="<?php echo esc_attr__('carousel', 'advanced-carousel-pro'); ?>" aria-label="<?php echo esc_attr($label); ?>"
    data-widget-id="<?php echo esc_attr($widget_id); ?>" data-settings="<?php echo esc_attr(wp_json_encode($slider_options)); ?>">
    <div id="<?php echo esc_attr($slider_id); ?>" class="swiper pce-v5-slider" tabindex="0">
        <div class="swiper-wrapper">
            <?php foreach ($items as $index => $item) :
                $title = trim(PCE_Settings::text($item['title'] ?? ''));
                $badge = trim(PCE_Settings::text($item['category'] ?? ''));
                $price = trim(PCE_Settings::text($item['price'] ?? ''));
                $desc = trim(PCE_Settings::text($item['desc'] ?? ''));
                $btn_text = PCE_Settings::text($item['btn_text'] ?? '') ?: __('View Product', 'advanced-carousel-pro');
                $has_btn_icon = !empty($item['btn_icon']['value']);
                $position = ($item['btn_icon_position'] ?? '') === 'after' ? 'after' : 'before';
                $button_classes = 'pce-v5-btn' . ($has_btn_icon ? ' has-icon icon-' . $position : '');
                $link_key = 'item_link_' . $index;
                $this->remove_render_attribute($link_key);
                $url = esc_url_raw(PCE_Settings::text($item['link']['url'] ?? ''));
                if ($url) {
                    // Only explicit link options are accepted; imported event attributes are ignored.
                    $this->add_render_attribute($link_key, 'href', $url, true);
                    $this->add_render_attribute($link_key, 'class', $button_classes, true);
                    $this->add_render_attribute($link_key, 'aria-label', $title ? $btn_text . ': ' . $title : $btn_text, true);
                    $rels = [];
                    if (!empty($item['link']['is_external'])) {
                        $this->add_render_attribute($link_key, 'target', '_blank', true);
                        $rels = ['noopener', 'noreferrer'];
                    }
                    if (!empty($item['link']['nofollow'])) {
                        $rels[] = 'nofollow';
                    }
                    if ($rels) {
                        $this->add_render_attribute($link_key, 'rel', implode(' ', $rels), true);
                    }
                }
                ?>
                <article class="swiper-slide" data-pce-card="<?php echo esc_attr(PCE_Settings::text($item['_id'] ?? $index)); ?>">
                    <div class="pce-v5-card">
                        <?php if ($show('badge') && $badge !== '') : ?>
                            <span class="pce-v5-badge">
                                <?php if (!empty($item['badge_icon']['value'])) : ?>
                                    <span class="pce-v5-badge-icon" aria-hidden="true"><?php \Elementor\Icons_Manager::render_icon($item['badge_icon'], ['aria-hidden' => 'true']); ?></span>
                                <?php endif; ?>
                                <span class="pce-v5-badge-text"><?php echo esc_html($badge); ?></span>
                            </span>
                        <?php endif; ?>
                        <?php if ($show('image')) : ?>
                            <div class="pce-v5-media"><?php echo $this->get_image_html($item, $settings, $title); // WordPress-generated image or escaped fallback. ?></div>
                        <?php endif; ?>
                        <div class="pce-v5-body">
                            <?php if ($show('title') && $title !== '') : ?>
                                <<?php echo esc_attr($title_tag); ?> class="pce-v5-title"><?php echo esc_html($title); ?></<?php echo esc_attr($title_tag); ?>>
                            <?php endif; ?>
                            <?php if ($show('price') && (!empty($item['price_html']) || $price !== '')) : ?>
                                <div class="pce-v5-price"><?php echo $source === 'woocommerce' ? wp_kses_post($item['price_html'] ?? '') : esc_html($price); ?></div>
                            <?php endif; ?>
                            <?php if ($show('description') && $desc !== '') : ?>
                                <p class="pce-v5-desc"><?php echo esc_html($desc); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php if ($show('button')) : ?>
                            <div class="pce-v5-btn-wrapper">
                                <?php if ($url) : ?>
                                    <a <?php echo $this->get_render_attribute_string($link_key); ?>>
                                <?php else : ?>
                                    <span class="<?php echo esc_attr($button_classes); ?> is-disabled" aria-disabled="true">
                                <?php endif; ?>
                                <?php if ($has_btn_icon) : ?>
                                    <span class="pce-v5-btn-icon" aria-hidden="true"><?php \Elementor\Icons_Manager::render_icon($item['btn_icon'], ['aria-hidden' => 'true']); ?></span>
                                <?php endif; ?>
                                <span class="pce-v5-btn-label"><?php echo esc_html($btn_text); ?></span>
                                <?php echo $url ? '</a>' : '</span>'; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="pce-v5-navigation is-hidden">
        <button type="button" class="pce-v5-nav pce-v5-prev" aria-controls="<?php echo esc_attr($slider_id); ?>" aria-label="<?php echo esc_attr($slider_options['messages']['prev']); ?>"><span aria-hidden="true">&larr;</span></button>
        <button type="button" class="pce-v5-nav pce-v5-next" aria-controls="<?php echo esc_attr($slider_id); ?>" aria-label="<?php echo esc_attr($slider_options['messages']['next']); ?>"><span aria-hidden="true">&rarr;</span></button>
    </div>
    <div class="swiper-pagination pce-v5-pagination pce-v5-pagination-<?php echo esc_attr($slider_options['paginationType']); ?> is-hidden"></div>
    <?php if ($slider_options['autoplay'] && $slider_options['showAutoplayButton']) : ?>
        <button type="button" class="pce-v5-autoplay" aria-controls="<?php echo esc_attr($slider_id); ?>" aria-label="<?php echo esc_attr($slider_options['messages']['pause']); ?>" title="<?php echo esc_attr($slider_options['messages']['pause']); ?>" hidden>
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path class="pce-autoplay-pause" d="M7 5h4v14H7zM13 5h4v14h-4z"/><path class="pce-autoplay-play" d="m8 5 11 7-11 7z"/></svg>
        </button>
    <?php endif; ?>
</div>

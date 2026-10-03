<?php
if (!defined('ABSPATH')) {
    exit;
}

/** Resolve Elementor's max-width device controls into explicit runtime profiles. */
final class PCE_Settings {
    public static function text($value) {
        return is_scalar($value) ? (string) $value : '';
    }

    public static function number($value, $fallback, $min, $max) {
        return is_numeric($value) && is_finite((float) $value) && $value >= $min && $value <= $max ? (float) $value : $fallback;
    }

    public static function profiles(array $settings, ?array $raw_settings = null) {
        $explicit_settings = $raw_settings ?? $settings;
        $breakpoints = [
            'mobile' => ['value' => 767, 'direction' => 'max'],
            'tablet' => ['value' => 1024, 'direction' => 'max'],
        ];
        if (class_exists('Elementor\\Plugin')) {
            $plugin = \Elementor\Plugin::$instance;
            if (isset($plugin->breakpoints)) {
                $breakpoints = [];
                foreach ($plugin->breakpoints->get_active_breakpoints() as $name => $breakpoint) {
                    $breakpoints[$name] = ['value' => $breakpoint->get_value(), 'direction' => $breakpoint->get_direction()];
                }
            }
        }
        $base = [
            'minWidth' => 0,
            'slides' => self::number($settings['slides_per_view'] ?? '', self::number($settings['slides_desktop'] ?? '', 3, 1, 6), 1, 6),
            'gap' => self::number($settings['space_between']['size'] ?? '', 20, 0, 60),
            'group' => (int) self::number($settings['slides_per_group'] ?? '', 1, 1, 6),
            'arrows' => ($settings['show_arrows'] ?? 'yes') === 'yes',
            'dots' => ($settings['show_dots'] ?? 'yes') === 'yes',
        ];
        $max_devices = array_filter($breakpoints, static function ($point) { return $point['direction'] === 'max'; });
        uasort($max_devices, static function ($a, $b) { return $b['value'] <=> $a['value']; });
        $profiles = [];
        $current = $base;
        $has_new_slides = false;
        foreach ($settings as $key => $value) {
            if (strpos($key, 'slides_per_view') === 0 && is_numeric($value)) {
                $has_new_slides = true;
            }
        }
        foreach ($max_devices as $name => $point) {
            $current['minWidth'] = (int) $point['value'] + 1;
            $profiles[] = $current;
            $suffix = '_' . $name;
            $legacy = $name === 'mobile' ? 1.15 : ($name === 'tablet' ? 2 : $current['slides']);
            $fallback = is_numeric($settings['slides_' . $name] ?? '') || !$has_new_slides
                ? self::number($settings['slides_' . $name] ?? '', $legacy, 1, 6) : $current['slides'];
            $current['slides'] = self::number($settings['slides_per_view' . $suffix] ?? '', $fallback, 1, 6);
            $current['gap'] = self::number($settings['space_between' . $suffix]['size'] ?? '', $current['gap'], 0, 60);
            $current['group'] = (int) self::number($settings['slides_per_group' . $suffix] ?? '', $current['group'], 1, 6);
            foreach (['arrows' => 'show_arrows', 'dots' => 'show_dots'] as $output => $control) {
                if (array_key_exists($control . $suffix, $explicit_settings)) {
                    $current[$output] = $settings[$control . $suffix] === 'yes';
                }
            }
        }
        $current['minWidth'] = 0;
        $profiles[] = $current;
        foreach ($breakpoints as $name => $point) {
            if ($point['direction'] !== 'min') {
                continue;
            }
            $wide = $base;
            $wide['minWidth'] = (int) $point['value'];
            $wide['slides'] = self::number($settings['slides_per_view_' . $name] ?? '', $base['slides'], 1, 6);
            $wide['gap'] = self::number($settings['space_between_' . $name]['size'] ?? '', $base['gap'], 0, 60);
            $wide['group'] = (int) self::number($settings['slides_per_group_' . $name] ?? '', $base['group'], 1, 6);
            foreach (['arrows' => 'show_arrows', 'dots' => 'show_dots'] as $output => $control) {
                $wide[$output] = array_key_exists($control . '_' . $name, $explicit_settings) ? $settings[$control . '_' . $name] === 'yes' : $base[$output];
            }
            $profiles[] = $wide;
        }
        usort($profiles, static function ($a, $b) { return $a['minWidth'] <=> $b['minWidth']; });
        return $profiles;
    }
}

<style>
    .custom-btn {
        display: inline-flex;
        text-align: center;
        align-items: center;
        justify-content: center;
        font-family: system-ui, -apple-system, sans-serif;
        font-weight: 600;
        text-decoration: none !important;
        box-sizing: border-box;
        transition: all 0.2s ease;
        white-space: nowrap;

        &.is-clickable {
            cursor: pointer;
    
            &:hover {
                filter: brightness(1.05);
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
            }
    
            &:active {
                transform: scale(0.97);
            }
        }

        &.is-disabled {
            cursor: not-allowed;
            user-select: none;
        }

        .btn-inner {
            display: inline-flex;
            align-items: center;
            transform: translateY(-1px);
            img {
                display: inline-block;
                flex-shrink: 0;
            }
        }
    }

</style>

<?php

/**
 * Renders a customizable action button or disabled button track.
 * @param string $text The text content inside the button
 * @param string $font_size Custom CSS typography font size (e.g. '1rem', '16px')
 * @param string $link The target destination URL (ignored if $is_clickable is false)
 * @param bool $is_clickable Toggle to enable/disable interactive click properties and link rendering
 * @param string $bg_color Custom CSS background color
 * @param string $text_color Custom CSS typography text color
 * @param string $border_color Custom CSS border color (or 'none')
 * @param string $width Custom CSS layout width (e.g. 'auto', '100%')
 * @param string $height Custom CSS layout height (e.g. 'auto', '45px')
 * @param string $padding Custom CSS inner spacing metrics (e.g. '12px 28px')
 * @param string $border_radius Custom CSS border radius (e.g. '1rem', '50%')
 */
function render_button(
    $text = 'Button',
    $font_size = '1rem',
    $link = '#',
    $is_clickable = true,
    $text_color = '#ffffff',
    $bg_color = '#558B55',
    $border_color = 'none',
    $width = 'auto',
    $height = 'auto',
    $padding = '12px 28px',
    $border_radius = '1rem'
) {
    $border_style = ($border_color === 'none') ? 'border: none;' : "border: 1px solid {$border_color};";

    $button_styles = "
        background-color: {$bg_color}; 
        color: {$text_color} !important; 
        {$border_style}
        width: {$width}; 
        height: {$height}; 
        padding: {$padding};
        border-radius: {$border_radius};
        font-size: {$font_size};
    ";

    $tag = $is_clickable ? 'a' : 'div';
    $clickable_class = $is_clickable ? 'is-clickable' : 'is-disabled';
?>

    <<?php echo $tag; ?>
        class="custom-btn <?php echo $clickable_class; ?>"
        href="<?php echo $is_clickable ? htmlspecialchars($link) : '#'; ?>"
        style="<?php echo $button_styles; ?>" ?>
        <span class="btn-inner">
            <?php echo $text; ?>
        </span>
    </<?php echo $tag; ?>>
<?php
}
?>
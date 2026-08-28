<?php

function render_color_mask($color, $image_path, $width = '100%', $height = '100%') 
{
?>
    <div
        style="
            width: <?php echo $width; ?>;
            height: <?php echo $height; ?>;
            background-color: <?php echo $color; ?>;
            mask-image: url('<?php echo $image_path; ?>');
            mask-size: contain;
            mask-repeat: no-repeat;
            mask-position: center;
        "></div>
<?php
}
?>
<?php

function render_mobile_layout($layout_body, $user_info, $layout_left, $layout_right)
{
?>
    <style>
        .mobile-layout-container {
            width: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            padding-bottom: 80px;
            .layout_body-mobile {
                flex: 1;
                padding-bottom: 1rem;
                padding-left: 3rem;
                padding-right: 3rem;
                box-sizing: border-box;
            }

            .layout_left-mobile,
            .layout_right-mobile {
                padding: 1rem;
                box-sizing: border-box;
                background: #ffffff;
            }
        }
    </style>

    <div class="mobile-layout-container">
        <header>
            <?php render_mobile_navbar($user_info); ?>
        </header>

        <?php if (is_callable($layout_left)): ?>
            <aside class="layout_left-mobile"><?php $layout_left($user_info); ?></aside>
        <?php endif; ?>

        <main class="layout_body-mobile">
            <?php if (is_callable($layout_body)) {
                $layout_body($user_info);
            } ?>
        </main>

        <?php if (is_callable($layout_right)): ?>
            <aside class="layout_right-mobile"><?php $layout_right($user_info); ?></aside>
        <?php endif; ?>

        <footer class="layout-footer-mobile">
            <?php render_footer(); ?>
        </footer>
    </div>
<?php
}
?>
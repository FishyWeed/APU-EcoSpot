<?php

function render_desktop_layout($layout_body, $user_info, $layout_left, $layout_right)
{
?>
    <style>
        .desktop-layout-grid {
            width: 100%;
            min-height: 100vh;
            box-sizing: border-box;
            display: grid;
            gap: 8px;
            grid:
                "header header header" auto 
                "leftSide body rightSide" 1fr 
                "footer footer footer" auto 
                / 15% 1fr 15%;
            .layout-header {
                grid-area: header;
                padding-top: 4rem;
            }

            .layout_left {
                grid-area: leftSide;
                background: #fafafa;
                min-width: 200px;
                padding: 1rem;
            }

            .layout_body {
                grid-area: body;
                padding: 1rem;
            }

            .layout_right {
                grid-area: rightSide;
                background: #fafafa;
                min-width: 200px;
                padding: 1rem;
            }

            .layout-footer {
                grid-area: footer;
            }
        }
    </style>

    <div class="desktop-layout-grid">
        <header class="layout-header">
            <?php render_desktop_navbar($user_info); ?>
        </header>

        <?php if (is_callable($layout_left)): ?>
            <aside class="layout_left"><?php $layout_left($user_info); ?></aside>
        <?php else: ?>
            <div style="grid-area: leftSide;"></div>
        <?php endif; ?>

        <main class="layout_body">
            <?php if (is_callable($layout_body)) {
                $layout_body($user_info);
            } ?>
        </main>

        <?php if (is_callable($layout_right)): ?>
            <aside class="layout_right"><?php $layout_right($user_info); ?></aside>
        <?php else: ?>
            <div style="grid-area: rightSide;"></div>
        <?php endif; ?>

        <footer class="layout-footer">
            <?php render_footer(); ?>
        </footer>
    </div>
<?php
}
?>
<?php

function render_desktop_layout($layout_body, $user_info, $layout_left, $layout_right)
{
    $has_left  = is_callable($layout_left);
    $has_right = is_callable($layout_right);
?>
    <style>
        .desktop-layout-grid {
            width: 100%;
            min-height: 100vh;
            box-sizing: border-box;
            background-color: #f5f4ef;
            display: grid;
            gap: 16px;

            grid-template-areas:
                "header header header"
                "leftSide body rightSide"
                "footer footer footer";
            grid-template-rows: auto 1fr auto;
            grid-template-columns: <?php echo $has_left ? 'minmax(200px, 15%)' : '0px'; ?> 1fr <?php echo $has_right ? 'minmax(200px, 15%)' : '0px'; ?>;

            .layout-header {
                grid-area: header;
                width: 100%;
            }

            .layout_left {
                grid-area: leftSide;
                background-color: transparent;
                padding: 1.5rem 1rem;
                box-sizing: border-box;
                min-width: 200px;

                @media (max-width: 1100px) {
                    display: none !important;
                }
            }

            .layout_body {
                grid-area: body;
                padding: 1.5rem 1rem;
                box-sizing: border-box;
                width: 100%;
                min-width: 0;
            }

            .layout_right {
                grid-area: rightSide;
                background-color: transparent;
                padding: 1.5rem 1rem;
                box-sizing: border-box;
                min-width: 200px;

                @media (max-width: 1200px) {
                    display: none !important;
                }
            }

            .layout-footer {
                grid-area: footer;
                width: 100%;
            }
        }
    </style>

    <div class="desktop-layout-grid">
        <header class="layout-header">
            <?php render_desktop_navbar($user_info); ?>
        </header>

        <?php if ($has_left): ?>
            <aside class="layout_left">
                <?php $layout_left($user_info); ?>
            </aside>
        <?php else: ?>
            <div style="grid-area: leftSide; display: none;"></div>
        <?php endif; ?>

        <main class="layout_body">
            <?php if (is_callable($layout_body)) {
                $layout_body($user_info);
            } ?>
        </main>

        <?php if ($has_right): ?>
            <aside class="layout_right">
                <?php $layout_right($user_info); ?>
            </aside>
        <?php else: ?>
            <div style="grid-area: rightSide; display: none;"></div>
        <?php endif; ?>

        <footer class="layout-footer">
            <?php render_footer(); ?>
        </footer>
    </div>
<?php
}
?>
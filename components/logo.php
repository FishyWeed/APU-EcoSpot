<?php
function render_leaf_icon(string $class = 'eco-leaf-icon'): string
{
    $svg_content = file_get_contents(dirname(__DIR__) . '/assets/leaf.svg');

    return str_replace('<svg ', '<svg class="' . htmlspecialchars($class, ENT_QUOTES) . '" ', $svg_content);
}

function render_logo()
{
?>

    <style>
        .eco-brand-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            font-family: system-ui, -apple-system, sans-serif;
            white-space: nowrap;

            &:hover {
                filter: brightness(1.125);
            }

            .eco-logo-circle {
                width: 2rem;
                height: 2rem;
                background-color: #5b8a58;
                border-radius: 0.75rem;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #ffffff;
                flex-shrink: 0;
            }

            .eco-logo-circle .eco-leaf-icon {
                width: 1.25rem;
                height: 1.25rem;
                display: block;
            }

            .eco-logo-text {
                font-size: 1.1rem;
                font-weight: 700;
                color: #1a1a1a;
                letter-spacing: -0.3px;
                transform: translateY(-1px);

                span {
                    color: #5b8a58;
                }
            }
        }
    </style>

    <a href="/Assignment/src/pages/index.php" class="eco-brand-wrapper">
        <div class="eco-logo-circle">
            <?php echo render_leaf_icon(); ?>
        </div>
        <div class="eco-logo-text">APU <span>EcoSpot</span></div>
    </a>
<?php
}

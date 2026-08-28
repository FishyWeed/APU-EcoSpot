<?php

require_once __DIR__ . '/eco_card.php';

/**
 * Loops and renders an array of report items inside an adaptive nested layout harness.
 * Automatically catches empty data and prints a custom empty-state fallback card.
 * 
 * @param array $items Array of structural item arrays
 * @param string $layout Layout mode choice: 'grid' or 'list'
 */
function render_eco_card_container($items = [], $layout = 'grid')
{
?>
    <style>
        .eco-cards-container {
            width: 100%;
            box-sizing: border-box;
            display: grid;
            font-family: system-ui, -apple-system, sans-serif;

            .empty-state-card {
                width: 100%;
                background-color: #ffffff;
                border: 1px solid #eaeaea;
                border-radius: 20px;
                padding: 3rem 2rem;
                box-sizing: border-box;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                gap: 1.5rem;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.015);
                grid-column: 1 / -1;

                .empty-icon {
                    font-size: 2.25rem;
                    line-height: 1;
                    margin-bottom: 4px;
                }

                .empty-title {
                    font-size: 1.2rem;
                    font-weight: 700;
                    color: #1a1a1a;
                    margin: 0;
                    letter-spacing: -0.3px;
                }

                .empty-desc {
                    font-size: 0.95rem;
                    color: #8c8c8c;
                    margin: 0;
                    max-width: 320px;
                    line-height: 1.5;
                }
            }

            &.mode-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;

                @media (min-width: 768px) {
                    grid-template-columns: repeat(2, 1fr);
                }

                @media (min-width: 1024px) {
                    grid-template-columns: repeat(3, 1fr);
                }
            }

            &.mode-list {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }
    </style>

    <div class="eco-cards-container mode-<?php echo $layout; ?>">
        <?php if (empty($items)): ?>
            <div class="empty-state-card">
                <div class="empty-icon">🍃</div>
                <h4 class="empty-title">No Reports Available</h4>
                <p class="empty-desc">Everything looks clean! There are currently no active energy waste issues reported across the campus tracks.</p>
            </div>

        <?php else: ?>
            <?php foreach ($items as $item) {
                render_eco_card($item, $layout);
            } ?>
        <?php endif; ?>
    </div>
<?php
}
?>
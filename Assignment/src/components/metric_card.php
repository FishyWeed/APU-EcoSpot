<?php
/**
 * Renders a simple analytics dashboard metric card with explicit dimension and badge color overrides.
 * 
 * @param string $label The descriptive layout label underneath (e.g. 'Reports Filed')
 * @param string $text The prominent display header data (e.g. '18')
 * @param string $logo Content inside the top badge icon container (e.g. '📷')
 * @param string $width Custom CSS box layout width restriction bounds (e.g., '100%', '240px')
 * @param string $height Custom CSS box layout height restriction bounds (e.g., 'auto', '200px')
 * @param string $logo_bg_color Custom CSS background color for the icon container (e.g., '#f2f7f2', '#eef2ff')
 */
function render_metric_card(
    $label = 'Label', 
    $text = '0', 
    $logo = '📊', 
    $width = '100%', 
    $height = 'auto',
    $logo_bg_color = '#f2f7f2'
) {
    ?>
    <style>
        .metric-card-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background-color: #ffffff;
            border: 1px solid #eaeaea;
            border-radius: 20px;
            box-sizing: border-box;
            padding: 1.5rem;
            font-family: system-ui, -apple-system, sans-serif;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);

            .metric-logo {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 12px;
                font-size: 1.15rem;
                margin-bottom: 1.25rem;
                box-sizing: border-box;
                flex-shrink: 0;

                img {
                    width: 1.25rem;
                    height: 1.25rem;
                    object-fit: contain;
                }
            }

            .metric-label {
                font-size: 1.75rem;
                font-weight: 700;
                color: #1a1a1a;
                margin-bottom: 1rem 0;
                line-height: 1.1;
                letter-spacing: -0.5px;
            }

            .metric-value {
                text-align: left;
                font-size: 1rem;
                font-weight: 400;
                color: #8c8c8c;
                margin: 0;
            }

            &:hover {
                filter: brightness(1.02);
                transform: translateY(-4px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                transition: all 0.2s ease;
            }
        }
    </style>

    <div class="metric-card-container" style="width: <?php echo $width; ?>; height: <?php echo $height; ?>;">
        <div class="metric-logo" style="background-color: <?php echo $logo_bg_color; ?>;">
            <span style="transform: translateY(-1px);"><?php echo $logo; ?></span>
        </div>
        <h2 class="metric-label"><?php echo htmlspecialchars($label); ?></h2>
        <p class="metric-value"><?php echo htmlspecialchars($text); ?></p>
    </div>
    <?php
}
?>

<?php
/**
 * Renders a responsive bar graph component displaying kWh savings by location.
 * Highlights the top 3 highest bars with richer green color.
 * 
 * @param array $data Array of items. Accepts:
 *                    - [['location' => 'Block A', 'kwh' => 42], ...]
 *                    - [['Location' => 'Block A', 'kWh' => 42], ...]
 *                    - [['Block A', 42], ...]
 */
function render_bar_graph($data = [])
{
    $normalized_data = [];
    $raw_values = [];

    foreach ($data as $item) {
        $location = '';
        $kwh = 0;

        if (is_array($item)) {
            if (isset($item['location'])) {
                $location = (string)$item['location'];
                $kwh = (float)($item['kwh'] ?? 0);
            } elseif (isset($item['Location'])) {
                $location = (string)$item['Location'];
                $kwh = (float)($item['kWh'] ?? $item['kwh'] ?? 0);
            } elseif (isset($item[0]) && isset($item[1])) {
                $location = (string)$item[0];
                $kwh = (float)$item[1];
            }
        }

        $normalized_data[] = [
            'location' => $location,
            'kwh'      => $kwh,
        ];
        $raw_values[] = $kwh;
    }

    if (empty($normalized_data)) {
        echo '<div style="padding: 1.5rem; text-align: center; color: #7a9175; font-size: 0.85rem;">No graph data available</div>';
        return;
    }

    $max_val = max(10, max($raw_values));
    $ceil_val = ceil($max_val / 25) * 25;
    if ($ceil_val < 50) {
        $ceil_val = 50;
    }

    $sorted_vals = $raw_values;
    rsort($sorted_vals, SORT_NUMERIC);
    $top_3_threshold = $sorted_vals[min(2, count($sorted_vals) - 1)] ?? 0;

    $y_steps = [
        $ceil_val,
        (int)($ceil_val * 0.75),
        (int)($ceil_val * 0.5),
        (int)($ceil_val * 0.25),
        0
    ];
?>
    <style>
        .bar-graph-wrapper {
            width: 100%;
            margin-top: 1rem;
            font-family: system-ui, -apple-system, sans-serif;
            box-sizing: border-box;
        }

        .bar-graph-canvas {
            display: flex;
            height: 170px;
            position: relative;
            align-items: flex-end;
            padding-left: 2rem;
            padding-bottom: 1.75rem;
            box-sizing: border-box;
        }

        .bar-graph-y-axis {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 1.75rem;
            width: 1.75rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-end;
            padding-right: 0.35rem;
            box-sizing: border-box;
        }

        .bar-graph-y-label {
            font-size: 0.68rem;
            color: #8c9b8c;
            line-height: 1;
            font-weight: 500;
        }

        .bar-graph-grid-lines {
            position: absolute;
            left: 2rem;
            right: 0;
            top: 0;
            bottom: 1.75rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            pointer-events: none;
        }

        .bar-graph-grid-line {
            width: 100%;
            height: 1px;
            border-bottom: 1px dashed rgba(91, 138, 88, 0.15);
        }

        .bar-graph-bars-container {
            display: flex;
            width: 100%;
            height: 100%;
            align-items: flex-end;
            justify-content: space-around;
            gap: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .bar-graph-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            justify-content: flex-end;
            min-width: 0;
            position: relative;
        }

        .bar-graph-pillar-track {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        .bar-graph-pillar {
            width: clamp(14px, 2.5vw, 24px);
            border-radius: 4px 4px 0 0;
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
        }

        .bar-graph-pillar:hover {
            transform: scaleY(1.03);
            filter: brightness(1.08);
        }

        .bar-graph-pillar.is-top3 {
            background-color: #558b55;
            box-shadow: 0 2px 6px rgba(85, 139, 85, 0.25);
        }

        .bar-graph-pillar.is-standard {
            background-color: #a4cda0;
        }

        .bar-graph-x-label {
            position: absolute;
            bottom: -1.6rem;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.65rem;
            color: #7a9175;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 48px;
            text-align: center;
        }

        .bar-graph-pillar::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 105%;
            left: 50%;
            transform: translateX(-50%) translateY(-4px);
            background-color: #1c2b1a;
            color: #ffffff;
            font-size: 0.65rem;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease;
            z-index: 10;
        }

        .bar-graph-pillar:hover::after {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }
    </style>

    <div class="bar-graph-wrapper">
        <div class="bar-graph-canvas">
            <div class="bar-graph-y-axis">
                <?php foreach ($y_steps as $step): ?>
                    <span class="bar-graph-y-label"><?php echo $step; ?></span>
                <?php endforeach; ?>
            </div>

            <div class="bar-graph-grid-lines">
                <?php foreach ($y_steps as $step): ?>
                    <div class="bar-graph-grid-line"></div>
                <?php endforeach; ?>
            </div>

            <div class="bar-graph-bars-container">
                <?php
                foreach ($normalized_data as $bar):
                    $kwh = $bar['kwh'];
                    $location = $bar['location'];
                    $height_pct = ($ceil_val > 0) ? min(100, max(4, round(($kwh / $ceil_val) * 100))) : 4;
                    $is_top3 = ($kwh >= $top_3_threshold && $kwh > 0);
                    $bar_class = $is_top3 ? 'is-top3' : 'is-standard';
                ?>
                    <div class="bar-graph-col">
                        <div class="bar-graph-pillar-track">
                            <div class="bar-graph-pillar <?php echo $bar_class; ?>"
                                style="height: <?php echo $height_pct; ?>%;"
                                data-tooltip="<?php echo htmlspecialchars($location . ': ' . $kwh . ' kWh'); ?>">
                            </div>
                        </div>
                        <span class="bar-graph-x-label" title="<?php echo htmlspecialchars($location); ?>">
                            <?php echo htmlspecialchars($location); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php
}
?>

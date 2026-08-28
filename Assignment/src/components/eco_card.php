<?php
function render_eco_card($item, $layout = 'grid')
{
    $status_config = [
        'pending'   => ['text' => '• Pending',   'text_color' => '#fa5c5c', 'bg' => '#ffebeb', 'border' => '#fcd3d3'],
        'validated' => ['text' => '• Validated', 'text_color' => '#558B55', 'bg' => '#f2f7f2', 'border' => '#e1ebe1'],
        'resolved'  => ['text' => '• Resolved',  'text_color' => '#4f46e5', 'bg' => '#eef2ff', 'border' => '#e0e7ff']
    ];

    $cfg = $status_config[$item['status']] ?? $status_config['pending'];
    $icon = $item['icon'] ?? '💡';
    $bg_color = $item['bg_color'] ?? '#dbeafe';
    $report_id = $item['id'] ?? 'ECO-0000';
?>
    <style>
        .eco-card-box-universal {
            background-color: #ffffff;
            border-radius: 20px;
            border: 1px solid #eaeaea;
            box-sizing: border-box;
            width: 100%;
            font-family: system-ui, -apple-system, sans-serif;
            transition: transform 0.2s ease, box-shadow 0.2s ease;

            &:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
            }

            p,
            h4 {
                margin: 0;
            }
        }

        .mode-grid .eco-card-box-universal {
            display: flex;
            flex-direction: column;
            padding: 16px;
            gap: 12px;
            min-height: 250px;

            .grid-media-header {
                width: 100%;
                height: 120px;
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2rem;
                flex-shrink: 0;
            }

            .grid-title-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                gap: 12px;
                margin-top: 4px;

                .grid-title-text {
                    font-size: 1.1rem;
                    font-weight: 700;
                    color: #1a1a1a;
                    flex: 1;
                    text-align: left;
                    min-width: 0;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }

                .grid-badge-box {
                    flex-shrink: 0;
                }
            }

            .grid-location-text {
                font-size: 0.85rem;
                color: #8c8c8c;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .grid-footer-divider {
                width: 100%;
                height: 1px;
                background-color: #f5f4ef;
                margin-top: auto;
            }

            .grid-footer-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 0.8rem;
                color: #8c8c8c;
                font-weight: 500;

                .grid-reporter {
                    font-weight: 600;
                    color: #737373;
                }
            }
        }

        .mode-list .eco-card-box-universal {
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 14px 20px;
            gap: 16px;
            min-height: auto;

            .list-media-badge {
                width: 44px;
                height: 44px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
                flex-shrink: 0;
            }

            .list-content-splitter {
                display: flex;
                flex: 1;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                min-width: 0;
            }

            .list-left-details {
                display: flex;
                flex-direction: column;
                gap: 4px;
                min-width: 0;
                flex: 1;

                .list-meta-headline {
                    display: flex;
                    align-items: center;
                    gap: 8px;

                    .list-id-string {
                        font-weight: 600;
                        color: #b5b5b5;
                        font-size: 0.85rem;
                        letter-spacing: 0.2px;
                    }

                    .list-title-text {
                        font-size: 1.05rem;
                        font-weight: 700;
                        color: #1a1a1a;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                }

                .list-location-text {
                    font-size: 0.85rem;
                    color: #8c8c8c;
                    font-weight: 500;
                }
            }

            .list-right-actions {
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                gap: 6px;
                flex-shrink: 0;

                .list-time-string {
                    font-size: 0.8rem;
                    color: #8c8c8c;
                }
            }
        }
    </style>


    <div class="eco-card-box-universal">
        <?php if ($layout === 'grid'): ?>
            <div class="grid-media-header" style="background-color: <?php echo $bg_color; ?>;">
                <?php echo $icon; ?>
            </div>

            <div class="grid-title-row">
                <h4 class="grid-title-text" title="<?php echo htmlspecialchars($item['title']); ?>">
                    <?php echo htmlspecialchars($item['title']); ?>
                </h4>
                <div class="grid-badge-box">
                    <?php
                    render_button(
                        $cfg['text'],
                        '0.75rem',
                        '#',
                        false,
                        $cfg['text_color'],
                        $cfg['bg'],
                        $cfg['border'],
                        'auto',
                        'auto',
                        '4px 10px',
                        '20px'
                    );
                    ?>
                </div>
            </div>

            <p class="grid-location-text">📍 <?php echo htmlspecialchars($item['location']); ?></p>

            <div class="grid-footer-divider"></div>

            <div class="grid-footer-row">
                <span class="grid-reporter"><?php echo htmlspecialchars($item['reporter'] ?? 'Anonymous'); ?></span>
                <span><?php echo htmlspecialchars($item['time'] ?? 'Just now'); ?></span>
            </div>

        <?php else: ?>
            <div class="list-media-badge" style="background-color: <?php echo $bg_color; ?>;">
                <?php echo $icon; ?>
            </div>

            <div class="list-content-splitter">
                <div class="list-left-details">
                    <div class="list-meta-headline">
                        <span class="list-id-string"><?php echo htmlspecialchars($report_id); ?></span>
                        <h4 class="list-title-text" title="<?php echo htmlspecialchars($item['title']); ?>">
                            <?php echo htmlspecialchars($item['title']); ?>
                        </h4>
                    </div>
                    <p class="list-location-text">📍 <?php echo htmlspecialchars($item['location']); ?></p>
                </div>

                <div class="list-right-actions">
                    <?php
                    render_button(
                        $cfg['text'],
                        '0.8rem',
                        '#',
                        false,
                        $cfg['text_color'],
                        $cfg['bg'],
                        $cfg['border'],
                        'auto',
                        'auto',
                        '4px 12px',
                        '20px'
                    );
                    ?>
                    <span class="list-time-string"><?php echo htmlspecialchars($item['time'] ?? 'Just now'); ?></span>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php
}
?>
<?php
// src/pages/student/student.php

require_once __DIR__ . '/../../components/components.php';

$layout_body = function ($user_info) {
    global $db_connection;

    $all_reports     = $user_info['my_reports'] ?? [];
    $summary_reports = array_slice($all_reports, 0, 3);
    $current_user_id = $user_info['user_id'] ?? 0;

    $username = $user_info['user_name'] ?? 'User';
    $parts = explode(' ', $username);
    $initials = $parts[0][0] . $parts[1][0];

    date_default_timezone_set('Asia/Kuala_Lumpur');
    $hour = (int)date('H');
    $greeting = ($hour >= 5 && $hour < 12) ? "Good morning ☀️" : (($hour >= 12 && $hour < 17) ? "Good afternoon 🌤️" : "Good evening 🌙");

    $live_stats = fetch_student_dashboard_stats($db_connection, $current_user_id);
?>

    <style>
        .student-dashboard {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            margin-top: 2rem;
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            flex-direction: column;
            gap: 24px;
            box-sizing: border-box;

            .welcome-greeting-text {
                .greeting-hint {
                    font-size: 0.95rem;
                    color: #8c8c8c;
                    margin: 0 0 4px 0;
                    font-weight: 500;
                }
            }

            .profile-banner {
                width: 100%;
                background-color: #ffffff;
                border-radius: 24px;
                padding: 1.5rem;
                box-shadow: 0 10px 25px -5px rgba(91, 138, 88, 0.2);
                box-sizing: border-box;

                .profile-banner-content {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;

                    .profile-banner-left {
                        display: flex;
                        align-items: center;
                        gap: 16px;

                        .profile-banner-avatar {
                            display: flex;
                            align-items: center;
                            gap: 12px;

                            .profile-banner-text-group {
                                display: flex;
                                flex-direction: column;

                                .profile-banner-faculty {
                                    font-size: 1.1rem;
                                    font-weight: 700;
                                    color: #1a1a1a;
                                    margin: 0;
                                }

                                .profile-banner-rank {
                                    font-size: 0.85rem;
                                    color: #8c8c8c;
                                    margin: 0;
                                }
                            }
                        }
                    }

                    .profile-banner-right {
                        display: flex;
                        flex-direction: column;
                        align-items: flex-end;

                        .profile-banner-points {
                            font-size: 1rem;
                            font-weight: 600;
                            color: #417f42;

                            span {
                                font-size: 0.85rem;
                                color: #8c8c8c;
                            }
                        }

                        .profile-banner-faculty-rank {
                            font-size: 0.85rem;
                            color: #8c8c8c;
                            margin-top: 4px;
                        }
                    }
                }
            }

            .energy-hero-banner {
                background-color: #5b8a58;
                border-radius: 24px;
                padding: 1.5rem;
                color: #ffffff;
                box-shadow: 0 10px 25px -5px rgba(91, 138, 88, 0.2);
                box-sizing: border-box;

                .banner-label {
                    font-size: 0.9rem;
                    font-weight: 600;
                    opacity: 0.9;
                    margin: 0 0 8px 0;
                }

                .banner-metric-row {
                    display: flex;
                    align-items: baseline;
                    gap: 8px;
                    margin-bottom: 1rem;

                    h2 {
                        font-size: 1.25rem;
                        font-weight: 800;
                        margin: 0;
                        line-height: 1;
                    }

                    span {
                        font-size: 1.5rem;
                        font-weight: 600;
                        opacity: 0.9;
                    }
                }

                .progress-track-bar {
                    width: 100%;
                    height: 6px;
                    background-color: rgba(255, 255, 255, 0.2);
                    border-radius: 9999px;
                    margin-bottom: 0.75rem;
                    overflow: hidden;

                    .progress-fill {
                        height: 100%;
                        background-color: #ffffff;
                        border-radius: 9999px;
                        transition: width 0.6s ease;
                    }
                }

                .banner-footer-meta {
                    display: flex;
                    justify-content: space-between;
                    font-size: 0.85rem;
                    font-weight: 500;
                    opacity: 0.85;
                }
            }

            .metrics-summary-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 16px;
                width: 100%;

                @media (min-width: 576px) {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            .dashboard-section-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 1rem;
                margin-bottom: 4px;

                h3 {
                    font-size: 1.35rem;
                    font-weight: 800;
                    color: #1a1a1a;
                    margin: 0;
                    letter-spacing: -0.3px;
                }

                .toggle-history-btn {
                    font-size: 0.9rem;
                    color: #5b8a58;
                    font-weight: 600;
                    background: none;
                    border: none;
                    padding: 0;
                    cursor: pointer;

                    &:hover {
                        color: #417f42;
                        text-decoration: underline;
                    }
                }
            }

            .reports-panel-wrapper {
                width: 100%;
                box-sizing: border-box;
                margin-bottom: 5rem;

                &.panel-hidden {
                    display: none !important;
                }
            }
        }
    </style>

    <div class="student-dashboard">
        <p class="welcome-greeting-text"><?php echo $greeting; ?></p>

        <div class="profile-banner">
            <div class="profile-banner-content">
                <div class="profile-banner-left">
                    <div class="profile-banner-avatar">
                        <?php
                        render_button(
                            '
                        <span>' . htmlspecialchars($initials) . '</span>',
                            '0.9rem',
                            '#',
                            true,
                            '#ffffff',
                            '#5b8a58',
                            'none',
                            '2.75rem',
                            '2.75rem',
                            '0'
                        );
                        ?>
                        <div class="profile-banner-text-group">
                            <h4 class="profile-banner-faculty"><?php echo htmlspecialchars($user_info['faculty']); ?></h4>
                            <p class="profile-banner-rank"><?php echo htmlspecialchars($user_info['rank']); ?></p>
                        </div>
                    </div>

                    <div class="profile-banner-right">
                        <div class="profile-banner-points">
                            ⭐ <?php echo htmlspecialchars($user_info['eco_points']); ?> <span>pts</span>
                        </div>
                        <p class="profile-banner-faculty-rank"><?php echo htmlspecialchars($user_info['live_campus_rank']); ?></p>
                    </div>
                </div>
            </div>



            <div class="energy-hero-banner">
                <p class="banner-label">Campus saved today</p>
                <div class="banner-metric-row">
                    <h2><?php echo $live_stats['kwh_saved']; ?></h2>
                    <span>kWh</span>
                </div>
                <div class="progress-track-bar">
                    <div class="progress-fill" style="width: <?php echo $live_stats['progress_percentage']; ?>%;"></div>
                </div>
                <div class="banner-footer-meta">
                    <span><?php echo $live_stats['progress_percentage']; ?>% of daily goal</span>
                    <span>Based on verified spots</span>
                </div>
            </div>

            <div class="metrics-summary-grid">
                <?php render_metric_card('Impact pts', number_format($user_info['eco_points']), '⭐', '100%', 'auto', '#f2f7f2'); ?>
                <?php render_metric_card('Campus rank', $live_stats['campus_rank'], '🏆', '100%', 'auto', '#f2f7f2'); ?>
            </div>

            <div class="dashboard-section-header">
                <h3>My Reports</h3>
                <button type="button" id="history-toggle-trigger" class="toggle-history-btn" onclick="toggleReportHistory()">
                    Show Report History &rsaquo;
                </button>
            </div>

            <div id="reports-summary-panel" class="reports-panel-wrapper">
                <?php
                render_eco_card_container($summary_reports, 'grid');
                ?>
            </div>

            <div id="reports-history-panel" class="reports-panel-wrapper panel-hidden">
                <?php render_eco_card_container($all_reports, 'list'); ?>
            </div>

        </div>

        <script>
            function toggleReportHistory() {
                const summaryPanels = document.querySelectorAll('[id="reports-summary-panel"]');
                const historyPanels = document.querySelectorAll('[id="reports-history-panel"]');
                const toggleButtons = document.querySelectorAll('[id="history-toggle-trigger"]');

                if (historyPanels.length > 0) {
                    const isHistoryHidden = historyPanels[0].classList.contains('panel-hidden');

                    if (isHistoryHidden) {
                        summaryPanels.forEach(panel => panel.classList.add('panel-hidden'));
                        historyPanels.forEach(panel => panel.classList.remove('panel-hidden'));
                        toggleButtons.forEach(btn => btn.innerHTML = 'Show Summary View &lsaquo;');
                    } else {
                        summaryPanels.forEach(panel => panel.classList.remove('panel-hidden'));
                        historyPanels.forEach(panel => panel.classList.add('panel-hidden'));
                        toggleButtons.forEach(btn => btn.innerHTML = 'Show Report History &rsaquo;');
                    }
                }
            }
        </script>
    <?php
};

render_layout("Student Console", $layout_body);
    ?>
<?php

require_once __DIR__ . '/../../components/components.php';

function get_user_faculty_by_id($db_connection, $user_id)
{
    $faculty = "APU Campus";

    if (!$db_connection instanceof mysqli || empty($user_id)) {
        return $faculty;
    }

    $sql = "SELECT school_department FROM user WHERE user_id = ? LIMIT 1";

    if ($stmt = mysqli_prepare($db_connection, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['school_department'])) {
                $faculty = $row['school_department'];
            }
        }
        mysqli_stmt_close($stmt);
    }
    return $faculty;
}

$layout_body = function ($user_info) {
    global $db_connection;

    $raw_reports     = $user_info['my_reports'] ?? [];
    $summary_reports = [];
    $all_reports     = [];

    foreach ($raw_reports as $report) {
        if (empty($report['reporter']) || $report['reporter'] === 'Anonymous') {
            $report['reporter'] = $user_info['user_name'] ?? $_SESSION['fullname'] ?? 'Anonymous';
        }
        $all_reports[] = $report;
    }
    $summary_reports = array_slice($all_reports, 0, 3);
    
    $username = $user_info['user_name'] ?? 'User';
    $parts = explode(' ', $username);
    $initials = $parts[0][0] . $parts[1][0];
    
    $current_user_id = $user_info['user_id'] ?? 0;
    $live_stats = fetch_student_dashboard_stats($db_connection, $current_user_id);
?>

    <style>
        .student-dashboard {
            width: 100%;
            max-width: 80rem;
            margin: 0 auto;
            margin-top: 2rem;
            font-family: system-ui, -apple-system, sans-serif;
            padding: 0 1rem;
            box-sizing: border-box;

            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 24px;

            .profile-banner {
                display: flex;
                justify-content: space-between;
                align-items: center;

                width: 100%;
                color: #417f42 !important;
                background-color: #eef6ea;
                padding: 1.5rem;

                box-sizing: border-box;
                border-radius: 24px;
                border: 1px solid rgba(91, 138, 88, 0.2);
                box-shadow: 0 10px 25px -5px rgba(91, 138, 88, 0.2);

                .profile-banner-left {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    width: 100%;

                    .profile-banner-avatar {
                        display: flex;
                        align-items: center;
                        gap: 12px;

                    }

                    .profile-banner-text-group {
                        display: flex;
                        justify-content: center;
                        align-items: left;
                        flex-direction: column;
                        padding: 0;

                        .profile-banner-text-name {
                            text-align: left;
                            font-size: 1.5rem;
                            font-weight: 800;
                            margin: 0;
                            text-overflow: ellipsis;
                            white-space: nowrap;
                            overflow: hidden;
                        }

                        .profile-banner-text-faculty {
                            text-align: left;
                            font-size: 0.9rem;
                            font-weight: 600;
                            margin: 0;
                        }
                    }
                }

                .profile-banner-right {
                    display: flex;
                    flex-direction: column;
                    align-items: flex-end;
                    width: 100%;
                    gap: 0;

                    .profile-banner-points {
                        width: fit-content;
                        height: auto;
                        font-size: 1rem;
                        font-weight: 600;
                        color: #ffffff;
                        background-color: #5b8a58;
                        padding: 0.25rem 0.65rem;
                        border-radius: 24px;

                        .profile-eco-points-icon {
                            font-size: 1rem;
                            margin-right: 0.3rem;
                        }
                    }

                    .profile-banner-faculty-rank {
                        font-size: 0.85rem;
                        color: #8c8c8c;
                        margin-top: 4px;
                    }
                }

                @media (max-width: 576px) {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 16px;

                    .profile-banner-right {
                        align-items: flex-start;
                        width: 100%;
                    }
                }
            }

            .energy-hero-banner {
                width: 100%;
                display: flex;
                flex-direction: column;
                background-color: #5b8a58;
                border-radius: 24px;
                padding: 1.5rem;
                color: #ffffff;
                box-shadow: 0 10px 25px -5px rgba(91, 138, 88, 0.2);
                box-sizing: border-box;

                .banner-label {
                    font-size: 1rem;
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

                .metrics-summary-card {
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    justify-content: center;
                    gap: 16px;
                    padding: 1.5rem;
                    color: #417f42 !important;
                    background-color: #ffffff;
                    border: 1px solid rgba(91, 138, 88, 0.2);
                    border-radius: 24px;
                    box-shadow: 0 10px 25px -5px rgba(91, 138, 88, 0.2);
                    box-sizing: border-box;
                    transition: all 0.2s ease;

                    &:hover {
                        box-shadow: 0 10px 25px -5px rgba(91, 138, 88, 0.4);
                        transform: translateY(-2px) scale(1.02);
                    }

                    h1 {
                        font-size: 1.25rem;
                        font-weight: 800;
                        margin: 0;
                        color: #1a1a1a;
                    }

                    p {
                        font-size: 1rem;
                        font-weight: 600;
                        margin: 0;
                        color: #1a1a1a91;
                    }
                }

                @media (min-width: 576px) {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            .my-reports-section {
                width: 100%;
                box-sizing: border-box;
                display: flex;
                flex-direction: column;

                .my-reports-section-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin: 1rem 0;

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

                .my-reports-cards {
                    width: 100%;
                    overflow: wrap;

                    .reports-panel-wrapper {
                        width: 100%;
                        box-sizing: border-box;
                        margin-bottom: 5rem;

                        &.panel-hidden {
                            display: none !important;
                        }
                    }
                }
            }
        }
    </style>

    <div class="student-dashboard">

        <section class="profile-banner">

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
                        '3rem',
                        '3rem',
                        '0'
                    );
                    ?>
                </div>

                <div class="profile-banner-text-group">
                    <h1 class="profile-banner-text-name"><?php echo htmlspecialchars($user_info['user_name']) ?></h1>
                    <h6 class="profile-banner-text-faculty"><?php echo get_user_faculty_by_id($db_connection, $user_info['user_id']); ?></h6>
                </div>
            </div>

            <div class="profile-banner-right">
                <?php render_button(
                    '<div class="profile-banner-points"><span class="profile-eco-points-icon">&#9733;</span>' . htmlspecialchars($user_info['eco_points']) . ' pts</div>',
                    '0.9rem',
                    '#',
                    true,
                    '#ffffff',
                    '#5b8a58',
                    'none',
                    'auto',
                    'auto',
                    '0'
                ); ?>
                <p style="margin: 3px 0 0 0; font-size: 0.9rem; font-weight: 500;">Faculty <?php echo htmlspecialchars($live_stats['campus_rank']); ?></p>
            </div>
        </section>



        <section class="energy-hero-banner">
            <p class="banner-label">Campus saved today</p>
            <div class="banner-metric-row">
                <h1 style="margin: 0; font-size: 1.5rem;"><?php echo $live_stats['kwh_saved']; ?></h1>
                <span>kWh</span>
            </div>
            <div class="progress-track-bar">
                <div class="progress-fill" style="width: <?php echo $live_stats['progress_percentage']; ?>%;"></div>
            </div>
            <div class="banner-footer-meta">
                <span><?php echo $live_stats['progress_percentage']; ?>% of daily goal</span>
                <span>Based on verified spots</span>
            </div>
        </section>

        <section class="metrics-summary-grid">
            <div class="metrics-summary-card">
                <?php render_button(
                    '⭐',
                    '1rem',
                    '#',
                    true,
                    '#1a1a1a',
                    '#95cf784b',
                    '1px solid rgb(17, 255, 0)',
                    '3.5rem',
                    '3.5rem',
                    '0.5rem',
                    '1.15rem'
                ); ?>
                <div style="gap: 0;">
                    <h1><?php echo htmlspecialchars($user_info['eco_points']); ?></h1>
                    <p>Impact pts</p>
                </div>
            </div>
            <div class="metrics-summary-card">
                <?php render_button(
                    '🏆',
                    '1rem',
                    '#',
                    true,
                    '#1a1a1a',
                    '#95cf784b',
                    '1px solid rgb(17, 255, 0)',
                    '3.5rem',
                    '3.5rem',
                    '0.5rem',
                    '1.15rem'
                ); ?>
                <div style="gap: 0;">
                    <h1><?php echo htmlspecialchars($live_stats['campus_rank']); ?></h1>
                    <p>Faculty Rank</p>
                </div>
            </div>
        </section>



        <section class="my-reports-section">
            <div class="my-reports-section-header">
                <h3>My Reports</h3>
                <button type="button" id="history-toggle-trigger" class="toggle-history-btn" onclick="toggleReportHistory()">
                    Show Report History &rsaquo;
                </button>
            </div>
            <div class="my-reports-cards">
                <div id="reports-summary-panel" class="reports-panel-wrapper">
                    <?php
                    render_eco_card_container($summary_reports, 'grid');
                    ?>
                </div>

                <div id="reports-history-panel" class="reports-panel-wrapper panel-hidden">
                    <?php render_eco_card_container($all_reports, 'list'); ?>
                </div>
            </div>
        </section>
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
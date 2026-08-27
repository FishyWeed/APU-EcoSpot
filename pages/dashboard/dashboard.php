<?php
require_once __DIR__ . '/../../components/components.php';
require_once __DIR__ . '/../../util/dbcon.php';

if (!function_exists('dashboardTimeAgo')) {
    function dashboardTimeAgo($datetime)
    {
        $diff = time() - strtotime($datetime);
        if ($diff < 60) return $diff . 's ago';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';
        return date('d M', strtotime($datetime));
    }
}

if (!function_exists('getDashboardInitials')) {
    function getDashboardInitials($name)
    {
        $parts = preg_split('/\s+/', trim($name));
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }
}

if (!function_exists('getDashboardIssueEmoji')) {
    function getDashboardIssueEmoji($type)
    {
        switch (strtolower(trim($type))) {
            case 'lighting':
                return '💡';
            case 'aircon':
                return '🌡️';
            case 'projector':
            case 'equipment':
                return '🖥️';
            default:
                return '💡';
        }
    }
}

if (!function_exists('getDashboardIssueColorClass')) {
    function getDashboardIssueColorClass($type)
    {
        switch (strtolower(trim($type))) {
            case 'lighting':
                return 'lighting';
            case 'aircon':
                return 'aircon';
            case 'projector':
            case 'equipment':
                return 'projector';
            default:
                return 'other';
        }
    }
}

$layout_body = function ($user_info) {
    global $db_connection;
    $conn = $db_connection;

    $current_user_id = (int)($user_info['user_id'] ?? $_SESSION['user_id'] ?? $_SESSION['db_user_id'] ?? 0);
    $user_role = $user_info['user_role'] ?? 'student';

    $user_name = $user_info['user_name'] ?? 'User';
    $school_dept = 'Campus Community';
    $user_type_label = ucfirst($user_role);
    $impact_pts = (int)($user_info['eco_points'] ?? 0);
    $member_since = 'Jan 2026';

    if ($current_user_id > 0 && $conn instanceof mysqli) {
        $stmt = mysqli_prepare($conn, "SELECT full_name, school_department, user_type, impact_pts, created_at FROM user WHERE user_id = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $current_user_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($res)) {
                $user_name = $row['full_name'];
                $school_dept = !empty($row['school_department']) ? $row['school_department'] : $school_dept;
                $user_type_label = !empty($row['user_type']) ? $row['user_type'] : $user_type_label;
                $impact_pts = (int)$row['impact_pts'];
                if (!empty($row['created_at'])) {
                    $member_since = date('M Y', strtotime($row['created_at']));
                }
            }
            mysqli_stmt_close($stmt);
        }
    }

    $my_tickets = [];
    if ($current_user_id > 0 && $conn instanceof mysqli) {
        $stmt_t = mysqli_prepare($conn, "SELECT ticket_id, block_name, floor_number, room_number, issue_type, description, status, created_at FROM ticket WHERE user_id = ? ORDER BY created_at DESC LIMIT 8");
        if ($stmt_t) {
            mysqli_stmt_bind_param($stmt_t, "i", $current_user_id);
            mysqli_stmt_execute($stmt_t);
            $res_t = mysqli_stmt_get_result($stmt_t);
            while ($t_row = mysqli_fetch_assoc($res_t)) {
                $my_tickets[] = $t_row;
            }
            mysqli_stmt_close($stmt_t);
        }
    }

    if (empty($my_tickets) && $conn instanceof mysqli) {
        $sample_res = mysqli_query($conn, "SELECT ticket_id, block_name, floor_number, room_number, issue_type, description, status, created_at FROM ticket ORDER BY created_at DESC LIMIT 4");
        if ($sample_res) {
            while ($s_row = mysqli_fetch_assoc($sample_res)) {
                $my_tickets[] = $s_row;
            }
        }
    }

    $first_name = explode(' ', trim($user_name))[0];
    $time_hour = (int)date('H');
    $greeting = ($time_hour < 12) ? 'Good morning' : (($time_hour < 18) ? 'Good afternoon' : 'Good evening');
?>
    <link rel="stylesheet" href="dashboard_styles.css">

    <div class="dashboard-container">
        <div class="dashboard-user-header">
            <div class="dashboard-user-avatar">
                <?php echo htmlspecialchars(getDashboardInitials($user_name)); ?>
            </div>
            <div class="dashboard-user-meta">
                <h1 class="dashboard-user-greeting">
                    <?php echo $greeting; ?>, <?php echo htmlspecialchars($first_name); ?> 👋
                </h1>
                <p class="dashboard-user-subtitle">
                    <?php echo htmlspecialchars($school_dept); ?> · <?php echo htmlspecialchars($user_type_label); ?> · Member since <?php echo htmlspecialchars($member_since); ?>
                </p>
            </div>
        </div>
        <div class="dashboard-quick-access">
            <div class="dashboard-section-label">QUICK ACCESS</div>
            <div class="dashboard-quick-grid">
                <a href="/Assignment/src/pages/report/report.php" class="dashboard-quick-card report">
                    <span class="dashboard-quick-icon">📷</span>
                    <span>Report Waste</span>
                </a>
                <a href="/Assignment/src/pages/leaderboard/leaderboard.php" class="dashboard-quick-card leaderboard">
                    <span class="dashboard-quick-icon">🏆</span>
                    <span>Leaderboard</span>
                </a>
                <?php if ($user_role === 'facilities'): ?>
                    <a href="/Assignment/src/pages/facilities/facilities.php" class="dashboard-quick-card console">
                        <span class="dashboard-quick-icon">🏢</span>
                        <span>Facilities Console</span>
                    </a>
                <?php elseif ($user_role === 'ambassador'): ?>
                    <a href="/Assignment/src/pages/ambassador/ambassador.php" class="dashboard-quick-card console" style="background-color: #eef6ea; border-color: #c8e6c9; color: #417f42 !important;">
                        <span class="dashboard-quick-icon">🛡️</span>
                        <span>Eco-Ambassador</span>
                    </a>
                <?php else: ?>
                    <a href="/Assignment/src/pages/profile/profile.php" class="dashboard-quick-card console" style="background-color: #f3f4f6; border-color: #e5e7eb; color: #4b5563 !important;">
                        <span class="dashboard-quick-icon">👤</span>
                        <span>My Profile</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="dashboard-content-grid">
            <div class="dashboard-reports-col">
                <div class="dashboard-section-header">
                    <h2 class="dashboard-section-title">My Reports</h2>
                    <?php render_button("＋ New Report", "0.8rem", "/Assignment/src/pages/report/report.php", true, "#ffffff", "#558B55", "none", "auto", "auto", "0.5rem 0.75rem", "1.25rem"); ?>
                </div>

                <div class="dashboard-reports-list">
                    <?php if (empty($my_tickets)): ?>
                        <div class="dashboard-empty-reports">
                            <span style="font-size: 2.25rem; display: block; margin-bottom: 0.5rem;">🍃</span>
                            <h3 style="font-size: 1.15rem; font-weight: 700; color: #1c2b1a; margin: 0 0 0.25rem 0;">No reports filed yet</h3>
                            <p style="color: #7a9175; font-size: 0.85rem; margin: 0 0 1rem 0;">Spot energy waste on campus and earn impact points!</p>
                            <?php render_button("File a Report", "0.8rem", "/Assignment/src/pages/report/report.php", true, "#ffffff", "#558B55", "none", "auto", "auto", "0.5rem 0.75rem", "1.25rem"); ?>
                        </div>
                    <?php else: ?>
                        <?php foreach ($my_tickets as $ticket):
                            $status = $ticket['status'];
                            $status_class = 'pending';
                            if ($status === 'Verified' || $status === 'Validated') {
                                $status_class = 'validated';
                                $status = 'Validated';
                            } elseif ($status === 'Resolved') {
                                $status_class = 'resolved';
                            } elseif ($status === 'In Progress') {
                                $status_class = 'validated';
                            }
                        ?>
                            <div class="dashboard-report-card">
                                <div class="dashboard-report-icon <?php echo getDashboardIssueColorClass($ticket['issue_type']); ?>">
                                    <?php echo getDashboardIssueEmoji($ticket['issue_type']); ?>
                                </div>
                                <div class="dashboard-report-info">
                                    <div class="dashboard-report-title-row">
                                        <span class="dashboard-report-id">ECO-<?php echo str_pad($ticket['ticket_id'], 4, '0', STR_PAD_LEFT); ?></span>
                                        <span class="dashboard-report-title"><?php echo htmlspecialchars($ticket['issue_type']); ?> Waste</span>
                                    </div>
                                    <div class="dashboard-report-location">
                                        📍 <?php echo htmlspecialchars($ticket['room_number'] . ' (' . $ticket['block_name'] . ')'); ?>
                                    </div>
                                </div>
                                <div class="dashboard-report-meta">
                                    <span class="dashboard-status-badge <?php echo $status_class; ?>">
                                        • <?php echo htmlspecialchars($status); ?>
                                    </span>
                                    <span class="dashboard-report-time">
                                        <?php echo dashboardTimeAgo($ticket['created_at']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="dashboard-activity-col">
                <h2 class="dashboard-section-title">Points Activity</h2>
                <div class="dashboard-activity-card">
                    <div class="dashboard-activity-list">
                        <?php if (empty($my_tickets)): ?>
                            <div style="padding: 2rem 1.25rem; text-align: center; color: #7a9175; font-size: 0.88rem;">
                                No recent activity.
                            </div>
                        <?php else: ?>
                            <?php foreach ($my_tickets as $i => $ticket):
                                $action_title = 'Report Submitted';
                                $pts_earned = '+75';
                                if ($ticket['status'] === 'Resolved') {
                                    $action_title = 'Report Resolved';
                                    $pts_earned = '+150';
                                } elseif ($ticket['status'] === 'Verified' || $ticket['status'] === 'Validated') {
                                    $action_title = 'Report Validated';
                                    $pts_earned = '+100';
                                }
                            ?>
                                <div class="dashboard-activity-item">
                                    <div class="dashboard-activity-info">
                                        <span class="dashboard-activity-title"><?php echo $action_title; ?></span>
                                        <span class="dashboard-activity-sub">
                                            ECO-<?php echo str_pad($ticket['ticket_id'], 4, '0', STR_PAD_LEFT); ?> · <?php echo dashboardTimeAgo($ticket['created_at']); ?>
                                        </span>
                                    </div>
                                    <span class="dashboard-activity-pts">
                                        <?php echo $pts_earned; ?> <span style="font-size: 0.75rem;">⭐</span>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="dashboard-activity-footer">
                        <span class="dashboard-activity-footer-label">Total</span>
                        <div class="dashboard-activity-total-badge">
                            <span>⭐</span> <?php echo number_format($impact_pts); ?> pts
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
};

render_layout("Dashboard", $layout_body);
?>
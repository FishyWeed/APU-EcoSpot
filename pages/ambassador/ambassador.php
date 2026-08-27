<?php
require_once __DIR__ . '/../../components/components.php';
require_once __DIR__ . '/../../util/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['ticket_id'])) {
    global $db_connection;

    $ticket_id = (int)$_POST['ticket_id'];
    $action = $_POST['action'];

    if ($action === 'validate') {
        $status = 'Verified';
        $stmt = mysqli_prepare($db_connection, "UPDATE ticket SET status = ? WHERE ticket_id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $status, $ticket_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    } elseif ($action === 'reject') {
        $status = 'Dismissed';
        $q = mysqli_query($db_connection, "SELECT user_id FROM ticket WHERE ticket_id = $ticket_id");
        if ($q && ($row = mysqli_fetch_assoc($q))) {
            $uid = (int)$row['user_id'];
            mysqli_query($db_connection, "UPDATE user SET impact_pts = GREATEST(0, impact_pts - 75) WHERE user_id = $uid");
        }

        $stmt = mysqli_prepare($db_connection, "UPDATE ticket SET status = ? WHERE ticket_id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $status, $ticket_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    header("Location: ambassador.php");
    exit();
}

if (!function_exists('ambassadorTimeAgo')) {
    function ambassadorTimeAgo($datetime)
    {
        $diff = time() - strtotime($datetime);
        if ($diff < 60) return $diff . 's ago';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        return date('d M', strtotime($datetime));
    }
}

if (!function_exists('getAmbassadorIssueEmoji')) {
    function getAmbassadorIssueEmoji($type)
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
                return '⚡';
        }
    }
}

if (!function_exists('getAmbassadorIssueColorClass')) {
    function getAmbassadorIssueColorClass($type)
    {
        switch (strtolower(trim($type))) {
            case 'lighting':
                return 'lighting';
            case 'aircon':
                return 'aircon';
            default:
                return 'other';
        }
    }
}

if (!function_exists('getAmbassadorInitials')) {
    function getAmbassadorInitials($name)
    {
        $parts = preg_split('/\s+/', trim($name));
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }
}

$layout_body = function ($user_info) {
    global $db_connection;
    $conn = $db_connection;

    $sql = "SELECT t.*, u.full_name 
            FROM ticket t 
            JOIN user u ON t.user_id = u.user_id 
            WHERE t.status IN ('Pending', 'Verified') 
            ORDER BY t.created_at DESC";
    $result = mysqli_query($conn, $sql);
    $tickets = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $tickets[] = $row;
        }
    }

    $pending_count = count(array_filter($tickets, function ($t) {
        return $t['status'] === 'Pending';
    }));
?>
    <link rel="stylesheet" href="ambassador_styles.css">

    <div class="ambassador-container">
        <div class="ambassador-header">
            <div class="ambassador-header-left">
                <div class="ambassador-header-icon-box">
                    🛡️
                </div>
                <div>
                    <h1 class="ambassador-title">Eco-Ambassador Console</h1>
                    <p class="ambassador-subtitle">Review and validate incoming energy waste reports</p>
                </div>
            </div>
            <div class="ambassador-stats-wrapper">
                <span class="ambassador-pending-badge">
                    <?php echo $pending_count; ?> pending
                </span>
            </div>
        </div>

        <div class="ambassador-filters" id="ambassador-block-filters">
            <button type="button" class="ambassador-filter-chip active" onclick="filterReports('all', this)">All</button>
            <button type="button" class="ambassador-filter-chip" onclick="filterReports('Block A', this)">Block A</button>
            <button type="button" class="ambassador-filter-chip" onclick="filterReports('Block B', this)">Block B</button>
            <button type="button" class="ambassador-filter-chip" onclick="filterReports('Block C', this)">Block C</button>
            <button type="button" class="ambassador-filter-chip" onclick="filterReports('Block D', this)">Block D</button>
            <button type="button" class="ambassador-filter-chip" onclick="filterReports('Block E', this)">Block E</button>
            <button type="button" class="ambassador-filter-chip" onclick="filterReports('Library', this)">Library Block</button>
            <button type="button" class="ambassador-filter-chip" onclick="filterReports('Tech Hub', this)">Tech Hub</button>
            <button type="button" class="ambassador-filter-chip" onclick="filterReports('Admin', this)">Admin Block</button>
        </div>

        <div class="ambassador-grid">
            <div class="ambassador-reports-col" id="ambassador-reports-list">
                <?php if (empty($tickets)): ?>
                    <div class="ambassador-card" style="padding: 3rem 1.5rem; text-align: center;">
                        <span style="font-size: 2.5rem; display: block; margin-bottom: 0.75rem;">🎉</span>
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: #1c2b1a; margin: 0 0 0.35rem 0;">All caught up!</h3>
                        <p style="color: #7a9175; font-size: 0.88rem; margin: 0;">No pending reports to validate right now.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($tickets as $t):
                        $block_attr = htmlspecialchars($t['block_name']);
                        $is_pending = ($t['status'] === 'Pending');
                        $priority = in_array(strtolower($t['issue_type']), ['aircon', 'lighting']) ? 'High' : 'Medium';
                        $priority_class = strtolower($priority);
                    ?>
                        <div class="ambassador-card report-item" data-block="<?php echo $block_attr; ?>">
                            <div class="ambassador-card-body">
                                <div class="ambassador-card-icon <?php echo getAmbassadorIssueColorClass($t['issue_type']); ?>">
                                    <?php echo getAmbassadorIssueEmoji($t['issue_type']); ?>
                                </div>
                                <div class="ambassador-card-details">
                                    <div class="ambassador-card-header">
                                        <div class="ambassador-card-title-row">
                                            <span class="ambassador-card-id">ECO-<?php echo str_pad($t['ticket_id'], 4, '0', STR_PAD_LEFT); ?></span>
                                            <span class="ambassador-card-title"><?php echo htmlspecialchars($t['issue_type']); ?> Waste</span>
                                        </div>
                                        <div class="ambassador-badges-row">
                                            <span class="ambassador-badge-priority <?php echo $priority_class; ?>"><?php echo $priority; ?></span>
                                            <?php if ($is_pending): ?>
                                                <span class="ambassador-badge-status pending">• Pending</span>
                                            <?php else: ?>
                                                <span class="ambassador-badge-status validated">• Validated</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="ambassador-card-location">
                                        📍 <?php echo htmlspecialchars($t['room_number'] . ' (' . $t['block_name'] . ')'); ?>
                                    </div>

                                    <p class="ambassador-card-desc"><?php echo htmlspecialchars($t['description'] ?? 'Energy waste spotted at campus area.'); ?></p>

                                    <div class="ambassador-card-meta">
                                        <span class="ambassador-reporter">
                                            <span class="ambassador-reporter-avatar"><?php echo getAmbassadorInitials($t['full_name']); ?></span>
                                            <?php echo htmlspecialchars($t['full_name']); ?>
                                        </span>
                                        <span>🕐 <?php echo ambassadorTimeAgo($t['created_at']); ?></span>
                                    </div>
                                </div>
                            </div>

                            <?php if ($is_pending): ?>
                                <div class="ambassador-card-footer">
                                    <form action="ambassador.php" method="POST" style="flex: 2; display: flex; margin: 0;">
                                        <input type="hidden" name="ticket_id" value="<?php echo $t['ticket_id']; ?>">
                                        <input type="hidden" name="action" value="validate">
                                        <button type="submit" class="ambassador-btn-validate" style="width: 100%;">
                                            ✓ Validate Report
                                        </button>
                                    </form>
                                    <form action="ambassador.php" method="POST" style="flex: 1; display: flex; margin: 0;">
                                        <input type="hidden" name="ticket_id" value="<?php echo $t['ticket_id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="ambassador-btn-reject" style="width: 100%;">
                                            ✕ Reject
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="ambassador-card-footer">
                                    <div class="ambassador-validated-footer">
                                        <span>✓</span> Validated — forwarded to Facilities Management
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="ambassador-sidebar">
                <div class="ambassador-sidebar-header">
                    <h2 class="ambassador-sidebar-title">Switch-Off Challenges</h2>
                    <button type="button" class="ambassador-btn-new-challenge" onclick="alert('Challenge creation modal coming soon!');">
                        ＋ New
                    </button>
                </div>

                <div class="ambassador-challenge-card">
                    <div class="ambassador-challenge-top">
                        <span class="ambassador-challenge-badge">• Active</span>
                        <span class="ambassador-challenge-id">CH-014</span>
                    </div>
                    <h3 class="ambassador-challenge-title">Block B Switch-Off Monday</h3>
                    <p class="ambassador-challenge-desc">Turn off all lights and ACs during lunch break (12:00–14:00) in Block B.</p>
                    <div class="ambassador-progress-section">
                        <div class="ambassador-progress-header">
                            <span>Participants</span>
                            <span>47 / 100</span>
                        </div>
                        <div class="ambassador-progress-track">
                            <div class="ambassador-progress-fill" style="width: 47%;"></div>
                        </div>
                    </div>
                    <div class="ambassador-challenge-footer">
                        <span>🕐 3 days left</span>
                        <span>Target: 120 kWh</span>
                    </div>
                </div>

                <div class="ambassador-challenge-card">
                    <div class="ambassador-challenge-top">
                        <span class="ambassador-challenge-badge">• Active</span>
                        <span class="ambassador-challenge-id">CH-013</span>
                    </div>
                    <h3 class="ambassador-challenge-title">Library After-Hours Lights Off</h3>
                    <p class="ambassador-challenge-desc">Ensure study zones are powered down by 23:30 for the entire week.</p>
                    <div class="ambassador-progress-section">
                        <div class="ambassador-progress-header">
                            <span>Participants</span>
                            <span>23 / 50</span>
                        </div>
                        <div class="ambassador-progress-track">
                            <div class="ambassador-progress-fill" style="width: 46%;"></div>
                        </div>
                    </div>
                    <div class="ambassador-challenge-footer">
                        <span>🕐 6 days left</span>
                        <span>Target: 80 kWh</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterReports(block, btn) {
            let buttons = document.querySelectorAll('#ambassador-block-filters .ambassador-filter-chip');
            buttons.forEach(function(b) {
                b.classList.remove('active');
            });
            btn.classList.add('active');

            let items = document.querySelectorAll('#ambassador-reports-list .report-item');
            items.forEach(function(item) {
                if (block === 'all') {
                    item.classList.remove('ambassador-hidden');
                } else {
                    let itemBlock = item.getAttribute('data-block') || '';
                    if (itemBlock.toLowerCase().indexOf(block.toLowerCase()) !== -1) {
                        item.classList.remove('ambassador-hidden');
                    } else {
                        item.classList.add('ambassador-hidden');
                    }
                }
            });
        }
    </script>
<?php
};

render_layout("Eco-Ambassador Console", $layout_body);
?>
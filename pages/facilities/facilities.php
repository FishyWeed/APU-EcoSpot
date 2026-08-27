<?php
require_once __DIR__ . '/../../components/components.php';
require_once __DIR__ . '/../../util/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['ticket_id'])) {
    global $db_connection;
    $conn = $db_connection;

    $ticket_id = (int)$_POST['ticket_id'];
    $action = $_POST['action'];

    if ($action === 'assign') {
        $status = 'In Progress';
    } elseif ($action === 'resolve') {
        $status = 'Resolved';
    } else {
        $status = null;
    }

    if ($status !== null) {
        $stmt = mysqli_prepare($conn, "UPDATE ticket SET status = ? WHERE ticket_id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $status, $ticket_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    header("Location: facilities.php");
    exit();
}

if (!function_exists('facilitiesTimeAgo')) {
    function facilitiesTimeAgo($datetime)
    {
        $diff = time() - strtotime($datetime);
        if ($diff < 60) return $diff . 's ago';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        return date('d M', strtotime($datetime));
    }
}

$layout_body = function ($user_info) {
    global $db_connection;
    $conn = $db_connection;

    $open_count = 0;
    $open_query = mysqli_query($conn, "SELECT COUNT(*) as c FROM ticket WHERE status = 'Verified'");
    if ($open_query && ($row = mysqli_fetch_assoc($open_query))) {
        $open_count = (int)$row['c'];
    }

    $assigned_count = 0;
    $assigned_query = mysqli_query($conn, "SELECT COUNT(*) as c FROM ticket WHERE status = 'In Progress'");
    if ($assigned_query && ($row = mysqli_fetch_assoc($assigned_query))) {
        $assigned_count = (int)$row['c'];
    }

    $resolved_count = 0;
    $resolved_query = mysqli_query($conn, "SELECT COUNT(*) as c FROM ticket WHERE status = 'Resolved' AND MONTH(created_at) = MONTH(CURRENT_DATE())");
    if ($resolved_query && ($row = mysqli_fetch_assoc($resolved_query))) {
        $resolved_count = (int)$row['c'];
    }

    $sql = "SELECT t.*, u.full_name 
            FROM ticket t 
            JOIN user u ON t.user_id = u.user_id 
            WHERE t.status IN ('Verified', 'In Progress', 'Resolved') 
            ORDER BY FIELD(t.status, 'Verified', 'In Progress', 'Resolved'), t.created_at DESC 
            LIMIT 15";
    $result = mysqli_query($conn, $sql);
    $tickets = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $tickets[] = $row;
        }
    }

    $campus_kwh_data = [
        ['Location' => 'Block A',   'kWh' => 42],
        ['Location' => 'Block B',   'kWh' => 68],
        ['Location' => 'Block C',   'kWh' => 32],
        ['Location' => 'Library',   'kWh' => 55],
        ['Location' => 'Tech Hub',  'kWh' => 90],
        ['Location' => 'Cafeteria', 'kWh' => 24],
        ['Location' => 'Admin',     'kWh' => 15],
    ];

    $total_kwh = array_sum(array_column($campus_kwh_data, 'kWh'));
?>
    <link rel="stylesheet" href="facilities_styles.css">

    <div class="facilities-container">
        <div class="facilities-header">
            <div class="facilities-header-icon-box">
                🏢
            </div>
            <div>
                <h1 class="facilities-title">Facilities Management Console</h1>
                <p class="facilities-subtitle">Manage verified energy waste tickets and dispatch technicians</p>
            </div>
        </div>

        <div class="facilities-filters" id="facilities-block-filters">
            <button type="button" class="facilities-filter-chip active" onclick="filterTickets('all', this)">All</button>
            <button type="button" class="facilities-filter-chip" onclick="filterTickets('Block A', this)">Block A</button>
            <button type="button" class="facilities-filter-chip" onclick="filterTickets('Block B', this)">Block B</button>
            <button type="button" class="facilities-filter-chip" onclick="filterTickets('Tech Hub', this)">Tech Hub</button>
            <button type="button" class="facilities-filter-chip" onclick="filterTickets('Library', this)">Library Block</button>
        </div>

        <div class="facilities-grid">
            <div class="facilities-table-card">
                <div class="facilities-table-header">
                    <span>Ticket</span>
                    <span>Priority</span>
                    <span>Status</span>
                    <span>Actions</span>
                </div>
                <div class="facilities-table-body" id="facilities-tickets-list">
                    <?php if (empty($tickets)): ?>
                        <div style="padding: 2.5rem 1.5rem; text-align: center; color: #7a9175; font-weight: 500;">
                            No active tickets in queue. Great job!
                        </div>
                    <?php else: ?>
                        <?php foreach ($tickets as $t):
                            $block_attr = htmlspecialchars($t['block_name']);
                            $priority = in_array(strtolower($t['issue_type']), ['aircon', 'lighting']) ? 'High' : (strtolower($t['issue_type']) === 'other' ? 'Low' : 'Medium');
                            $priority_class = strtolower($priority);

                            $status_label = 'Open';
                            $status_class = 'open';
                            if ($t['status'] === 'In Progress') {
                                $status_label = 'Assigned';
                                $status_class = 'assigned';
                            } elseif ($t['status'] === 'Resolved') {
                                $status_label = 'Resolved';
                                $status_class = 'resolved';
                            }
                        ?>
                            <div class="facilities-ticket-row ticket-item" data-block="<?php echo $block_attr; ?>">
                                <div class="facilities-ticket-info">
                                    <div class="facilities-ticket-title-row">
                                        <span class="facilities-ticket-id">ECO-<?php echo str_pad($t['ticket_id'], 4, '0', STR_PAD_LEFT); ?></span>
                                        <span class="facilities-ticket-title"><?php echo htmlspecialchars($t['issue_type']); ?> Waste</span>
                                    </div>
                                    <div class="facilities-ticket-location">
                                        📍 <?php echo htmlspecialchars($t['room_number'] . ' (' . $t['block_name'] . ')'); ?>
                                    </div>
                                    <?php if ($t['status'] === 'In Progress'): ?>
                                        <div class="facilities-ticket-assignee">Technician Azmi</div>
                                    <?php elseif ($t['status'] === 'Resolved'): ?>
                                        <div class="facilities-ticket-assignee">Technician Farid</div>
                                    <?php endif; ?>
                                    <div class="facilities-ticket-time"><?php echo facilitiesTimeAgo($t['created_at']); ?></div>
                                </div>

                                <div>
                                    <span class="facilities-priority-badge <?php echo $priority_class; ?>"><?php echo $priority; ?></span>
                                </div>

                                <div>
                                    <span class="facilities-status-badge <?php echo $status_class; ?>">
                                        • <?php echo $status_label; ?>
                                    </span>
                                </div>

                                <div>
                                    <?php if ($t['status'] === 'Verified'): ?>
                                        <form action="facilities.php" method="POST" style="margin: 0;">
                                            <input type="hidden" name="ticket_id" value="<?php echo $t['ticket_id']; ?>">
                                            <input type="hidden" name="action" value="assign">
                                            <button type="submit" class="facilities-btn-assign">Assign</button>
                                        </form>
                                    <?php elseif ($t['status'] === 'In Progress'): ?>
                                        <form action="facilities.php" method="POST" style="margin: 0;">
                                            <input type="hidden" name="ticket_id" value="<?php echo $t['ticket_id']; ?>">
                                            <input type="hidden" name="action" value="resolve">
                                            <button type="submit" class="facilities-btn-resolve">Resolve</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="facilities-done-text">✓ Done</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="facilities-sidebar">
                <div class="facilities-sidebar-section">
                    <h2 class="facilities-sidebar-title">Queue Summary</h2>
                    <div class="facilities-queue-list">
                        <div class="facilities-queue-item">
                            <span class="facilities-queue-label">Open tickets</span>
                            <span class="facilities-queue-val open"><?php echo $open_count; ?></span>
                        </div>
                        <div class="facilities-queue-item">
                            <span class="facilities-queue-label">Assigned</span>
                            <span class="facilities-queue-val assigned"><?php echo $assigned_count; ?></span>
                        </div>
                        <div class="facilities-queue-item">
                            <span class="facilities-queue-label">Resolved this month</span>
                            <span class="facilities-queue-val resolved"><?php echo $resolved_count; ?></span>
                        </div>
                    </div>
                </div>

                <div class="facilities-sidebar-section">
                    <h2 class="facilities-sidebar-title">kWh Saved — <?php echo date('F Y'); ?></h2>
                    <div class="facilities-chart-card">
                        <div class="facilities-chart-header">
                            <span class="facilities-chart-value"><?php echo $total_kwh; ?></span>
                            <span class="facilities-chart-unit">kWh total</span>
                        </div>
                        <p class="facilities-chart-subtitle">Across all campus buildings</p>

                        <?php render_bar_graph($campus_kwh_data); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterTickets(block, btn) {
            var buttons = document.querySelectorAll('#facilities-block-filters .facilities-filter-chip');
            buttons.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');

            var items = document.querySelectorAll('#facilities-tickets-list .ticket-item');
            items.forEach(function (item) {
                if (block === 'all') {
                    item.classList.remove('facilities-hidden');
                } else {
                    var itemBlock = item.getAttribute('data-block') || '';
                    if (itemBlock.toLowerCase().indexOf(block.toLowerCase()) !== -1) {
                        item.classList.remove('facilities-hidden');
                    } else {
                        item.classList.add('facilities-hidden');
                    }
                }
            });
        }
    </script>
<?php
};

render_layout("Facilities Management Console", $layout_body);
?>
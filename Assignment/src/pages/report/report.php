<?php
require_once __DIR__ . '/../../components/components.php';
require_once __DIR__ . '/../../util/dbcon.php';

$success = "";
$error   = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    global $db_connection;
    $conn = $db_connection;

    $real_uid      = (int)($_SESSION['user_id'] ?? $_SESSION['db_user_id'] ?? 0);
    $location_type = $_POST['location_type'] ?? 'Other';
    $block_name    = trim($_POST['block_name'] ?? '');
    $floor_number  = trim($_POST['floor_number'] ?? '');
    $room_number   = trim($_POST['room_number'] ?? '');
    $issue_type    = trim($_POST['waste_type'] ?? 'Lighting');
    $description   = trim($_POST['description'] ?? '');

    if ($real_uid > 0 && !empty($block_name) && !empty($room_number)) {
        try {
            mysqli_begin_transaction($conn);

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO ticket (user_id, location_type, block_name, floor_number, room_number, issue_type, description, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')"
            );
            mysqli_stmt_bind_param($stmt, "issssss", $real_uid, $location_type, $block_name, $floor_number, $room_number, $issue_type, $description);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            mysqli_query($conn, "UPDATE user SET impact_pts = impact_pts + 75 WHERE user_id = $real_uid");

            $_SESSION['eco_points'] = ($_SESSION['eco_points'] ?? 0) + 75;
            $_SESSION['impact_pts'] = ($_SESSION['impact_pts'] ?? 0) + 75;

            mysqli_commit($conn);
            $success = "Report submitted successfully! You earned +75 impact points.";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Failed to submit report: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields (Building / Block and Room / Area).";
    }
}

$layout_body = function ($user_info) use ($success, $error) {
?>
    <link rel="stylesheet" href="report_styles.css">

    <div class="report-container">
        <div class="report-header">
            <h1 class="report-title">Report Energy Waste</h1>
            <p class="report-subtitle">Fill in the details below — it takes less than a minute.</p>
        </div>

        <?php if (!empty($success)): ?>
            <div class="report-success-alert">
                <span>✓</span>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="report-error-alert">
                <span>✕</span>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form action="report.php" method="POST">
            <div class="report-card">
                <div class="report-form-group">
                    <label class="report-label">Building / Block</label>
                    <select name="block_name" required class="report-select">
                        <option value="" disabled selected>Select building...</option>
                        <option value="Block A">Block A</option>
                        <option value="Block B">Block B</option>
                        <option value="Block C">Block C</option>
                        <option value="Block D">Block D</option>
                        <option value="Block E">Block E</option>
                        <option value="Atrium">Atrium</option>
                        <option value="Library">Library</option>
                        <option value="Studios">Studios</option>
                        <option value="Tech Hub">Tech Lab</option>
                        <option value="Admin">Admin Office</option>
                        <option value="Cafeteria">Cafeteria</option>
                    </select>
                </div>

                <div class="report-grid-2">
                    <div class="report-form-group">
                        <label class="report-label">Location Type</label>
                        <select name="location_type" required class="report-select">
                            <option value="Classroom">Classroom</option>
                            <option value="Lab">Lab</option>
                            <option value="Common Area">Common Area</option>
                            <option value="Office">Office</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="report-form-group">
                        <label class="report-label">Floor</label>
                        <input type="text" name="floor_number" placeholder="e.g. 02" maxlength="2" class="report-input" />
                    </div>
                </div>

                <div class="report-form-group">
                    <label class="report-label">Room / Area</label>
                    <input type="text" name="room_number" placeholder="e.g. A-02-04 or Library Level 1" required class="report-input" />
                </div>

                <div class="report-form-group">
                    <label class="report-label">Waste Type</label>
                    <div class="report-waste-grid" id="waste-type-group">
                        <button type="button" data-waste-type="Lighting" class="report-waste-btn active" onclick="selectWasteType('Lighting', this)">
                            <span class="report-waste-icon">💡</span>
                            <span class="report-waste-text">Lighting</span>
                        </button>
                        <button type="button" data-waste-type="Aircon" class="report-waste-btn" onclick="selectWasteType('Aircon', this)">
                            <span class="report-waste-icon">🌡️</span>
                            <span class="report-waste-text">Aircon</span>
                        </button>
                        <button type="button" data-waste-type="Projector" class="report-waste-btn" onclick="selectWasteType('Projector', this)">
                            <span class="report-waste-icon">🖥️</span>
                            <span class="report-waste-text">Projector</span>
                        </button>
                        <button type="button" data-waste-type="Other" class="report-waste-btn" onclick="selectWasteType('Other', this)">
                            <span class="report-waste-icon">⚡</span>
                            <span class="report-waste-text">Other</span>
                        </button>
                    </div>
                    <input type="hidden" name="waste_type" id="selected-waste-type" value="Lighting" />
                </div>

                <div class="report-form-group">
                    <label class="report-label">
                        Description <span class="report-label-optional">(optional)</span>
                    </label>
                    <textarea name="description" placeholder="e.g. Lights have been on since 9am with no one in the room..." rows="3" class="report-textarea"></textarea>
                </div>

                <div class="report-form-group">
                    <label class="report-label">Urgency Level</label>
                    <div class="report-urgency-grid" id="urgency-group">
                        <button type="button" data-urgency="low" class="report-urgency-btn active-low" onclick="selectUrgency('low', this)">
                            <span class="report-urgency-title">Low</span>
                            <span class="report-urgency-subtitle">Can wait</span>
                        </button>
                        <button type="button" data-urgency="medium" class="report-urgency-btn" onclick="selectUrgency('medium', this)">
                            <span class="report-urgency-title">Medium</span>
                            <span class="report-urgency-subtitle">Today</span>
                        </button>
                        <button type="button" data-urgency="high" class="report-urgency-btn" onclick="selectUrgency('high', this)">
                            <span class="report-urgency-title">High</span>
                            <span class="report-urgency-subtitle">Urgent now</span>
                        </button>
                    </div>
                    <input type="hidden" name="urgency" id="selected-urgency" value="low" />
                </div>

                <div class="report-submit-group">
                    <button type="submit" class="report-submit-btn">
                        Submit Report →
                    </button>
                    <p class="report-footer-text">
                        You will earn <strong class="report-footer-strong">+75 impact points</strong> for submitting this report
                    </p>
                </div>
            </div>
        </form>
    </div>

    <script>
        function selectWasteType(val, btn) {
            var buttons = document.querySelectorAll('#waste-type-group .report-waste-btn');
            buttons.forEach(function(b) {
                b.classList.remove('active');
            });
            btn.classList.add('active');
            var input = document.getElementById('selected-waste-type');
            if (input) input.value = val;
        }

        function selectUrgency(val, btn) {
            var buttons = document.querySelectorAll('#urgency-group .report-urgency-btn');
            buttons.forEach(function(b) {
                b.classList.remove('active-low', 'active-medium', 'active-high');
            });
            btn.classList.add('active-' + val);
            var input = document.getElementById('selected-urgency');
            if (input) input.value = val;
        }
    </script>
<?php
};

render_layout("Report Energy Waste", $layout_body);
?>
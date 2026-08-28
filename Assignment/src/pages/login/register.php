<?php
require_once __DIR__ . '/../../components/components.php';
require_once __DIR__ . '/../../util/dbcon.php';

error_reporting(E_ALL);

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    global $db_connection;

    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $userid = trim($_POST['userid'] ?? '');
    $school_department = trim($_POST['school_department'] ?? '');
    $role = $_POST['role'] ?? 'student';
    $raw_password = $_POST['password'] ?? '';
    $raw_confirm_password = $_POST['confirm_password'] ?? '';

    if ($raw_password !== $raw_confirm_password) {
        $error = "Error: Passwords do not match.";
    } elseif (strlen($raw_password) < 8) {
        $error = "Error: Password must be at least 8 characters.";
    } else {
        $duplicate_stmt = mysqli_prepare($db_connection, "SELECT user_id FROM user WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($duplicate_stmt, "s", $email);
        mysqli_stmt_execute($duplicate_stmt);
        $duplicate_result = mysqli_stmt_get_result($duplicate_stmt);

        if ($duplicate_result && mysqli_num_rows($duplicate_result) > 0) {
            mysqli_stmt_close($duplicate_stmt);
            $error = "Error: An account with this email already exists.";
        } else {
            mysqli_stmt_close($duplicate_stmt);
            $password_hash = password_hash($raw_password, PASSWORD_DEFAULT);

            mysqli_begin_transaction($db_connection);
            try {
            $user_type = ($role === 'eco-ambassador') ? 'Eco-Ambassador' : 'Student';

            $stmt = mysqli_prepare(
                $db_connection,
                "INSERT INTO user (user_type, full_name, email, student_id, password, school_department, impact_pts) VALUES (?, ?, ?, ?, ?, ?, 0)"
            );
            mysqli_stmt_bind_param($stmt, "ssssss", $user_type, $fullname, $email, $userid, $password_hash, $school_department);

            if (mysqli_stmt_execute($stmt)) {
                $new_user_id = mysqli_insert_id($db_connection);
                mysqli_stmt_close($stmt);

                $role_name = ($role === 'eco-ambassador') ? 'Eco-Ambassador' : 'Student';
                $role_query = "SELECT role_id FROM role WHERE name = ?";
                $role_stmt = mysqli_prepare($db_connection, $role_query);
                mysqli_stmt_bind_param($role_stmt, "s", $role_name);
                mysqli_stmt_execute($role_stmt);
                $role_result = mysqli_stmt_get_result($role_stmt);

                if ($role_result && mysqli_num_rows($role_result) > 0) {
                    $role_row = mysqli_fetch_assoc($role_result);
                    $role_id = $role_row['role_id'];
                } else {
                    mysqli_stmt_close($role_stmt);
                    $insert_role_stmt = mysqli_prepare($db_connection, "INSERT INTO role (name) VALUES (?)");
                    mysqli_stmt_bind_param($insert_role_stmt, "s", $role_name);
                    mysqli_stmt_execute($insert_role_stmt);
                    $role_id = mysqli_insert_id($db_connection);
                    mysqli_stmt_close($insert_role_stmt);
                }

                if (isset($role_stmt) && $role_stmt instanceof mysqli_stmt) {
                    mysqli_stmt_close($role_stmt);
                }

                $assign_stmt = mysqli_prepare($db_connection, "INSERT INTO user_role (user_id, role_id) VALUES (?, ?)");
                mysqli_stmt_bind_param($assign_stmt, "ii", $new_user_id, $role_id);
                mysqli_stmt_execute($assign_stmt);
                mysqli_stmt_close($assign_stmt);

                mysqli_commit($db_connection);
                header("Location: login.php");
                exit();
            }

            mysqli_rollback($db_connection);
            $error = "Error: " . mysqli_error($db_connection);
            } catch (Exception $e) {
                mysqli_rollback($db_connection);
                $error = "Exception Error: " . $e->getMessage();
            }
        }
    }
}

$layout_body = function ($user_info) use ($error) {
?>
    <link rel="stylesheet" href="/Assignment/src/pages/login/style.css">
    <link rel="stylesheet" href="/Assignment/src/pages/login/register_styles.css">

    <div class="register-wrapper">
        <div class="register-container">
            <div class="register-header">
                <div class="register-logo-box">
                    <?php echo render_leaf_icon('register-logo-icon'); ?>
                </div>
                <h1 class="register-title">Create your account</h1>
                <p class="register-subtitle">Join the campus sustainability movement</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="register-error-box">
                    <span class="register-error-icon-box">
                        <i data-lucide="x" class="register-error-icon"></i>
                    </span>
                    <span class="register-error-text"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <div class="register-tabs">
                <a href="login.php" class="register-tab-link">Sign In</a>
                <button class="register-tab-active">Register</button>
            </div>

            <div class="register-card">
                <form action="register.php" method="POST" class="register-form">
                    <div>
                        <label class="register-field-label">Full Name</label>
                        <input type="text" name="fullname" placeholder="e.g. Ahmad Faris Hakim" value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" required class="register-input" />
                    </div>

                    <div>
                        <label class="register-field-label">APU Email</label>
                        <input type="email" name="email" placeholder="yourname@mail.apu.edu.my" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required class="register-input" />
                        <p class="register-help-text">Students: @mail.apu.edu.my · Staff: @apu.edu.my</p>
                    </div>

                    <div>
                        <label class="register-field-label">Student / Staff ID</label>
                        <input type="text" name="userid" placeholder="e.g. TP123456 or APU-STF-0091" value="<?php echo isset($_POST['userid']) ? htmlspecialchars($_POST['userid']) : ''; ?>" required class="register-input" />
                    </div>

                    <div>
                        <label class="register-field-label">School / Department</label>
                        <input type="text" name="school_department" placeholder="e.g. School of Computing" value="<?php echo isset($_POST['school_department']) ? htmlspecialchars($_POST['school_department']) : ''; ?>" required class="register-input" />
                    </div>

                    <div>
                        <label class="register-field-label">I am registering as</label>
                        <div class="register-role-grid">
                            <input type="radio" id="role-student" name="role" value="student" class="register-role-input" <?php echo (isset($_POST['role']) && $_POST['role'] === 'student') ? 'checked' : (!isset($_POST['role']) ? 'checked' : ''); ?>>
                            <label for="role-student" class="register-role-label">
                                <div class="register-role-title">Student / Faculty</div>
                                <div class="register-role-desc">Report energy waste</div>
                            </label>

                            <input type="radio" id="role-ambassador" name="role" value="eco-ambassador" class="register-role-input" <?php echo (isset($_POST['role']) && $_POST['role'] === 'eco-ambassador') ? 'checked' : ''; ?>>
                            <label for="role-ambassador" class="register-role-label">
                                <div class="register-role-title">Eco-Ambassador</div>
                                <div class="register-role-desc">Validate & run challenges</div>
                            </label>
                        </div>
                        <p class="register-help-text" style="margin-top: 0.5rem;">Facilities Management accounts are provisioned by the IT department.</p>
                    </div>

                    <div>
                        <label class="register-field-label">Password</label>
                        <input type="password" name="password" placeholder="At least 8 characters" required class="register-input" />
                    </div>

                    <div>
                        <label class="register-field-label">Confirm Password</label>
                        <input type="password" name="confirm_password" placeholder="••••••••" required class="register-input" />
                    </div>

                    <button type="submit" class="register-submit-btn">Create Account</button>
                </form>

                <p class="register-login-prompt">
                    Already have an account?
                    <a href="login.php" class="register-login-link">Sign in</a>
                </p>
            </div>

            <p class="register-footer">APU EcoSpot · Asia Pacific University · Sustainability Initiative 2026</p>
        </div>
    </div>
<?php
};

render_layout("Register Page", $layout_body);
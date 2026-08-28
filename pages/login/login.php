<?php
require_once __DIR__ . '/../../components/components.php';
require_once __DIR__ . '/../../util/dbcon.php';

error_reporting(E_ALL);

$error_message = "";

function map_role_for_layout($role_name, $user_type = '')
{
    $raw = !empty($role_name) ? $role_name : $user_type;
    $role = strtolower(str_replace([' ', '-', '_'], '', $raw ?? ''));

    if (strpos($role, 'ecoambassador') !== false || strpos($role, 'ambassador') !== false) {
        return 'ambassador';
    }
    if (strpos($role, 'facilities') !== false || strpos($role, 'admin') !== false) {
        return 'facilities';
    }

    return 'student';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    global $db_connection;

    $email = trim($_POST['email'] ?? '');
    $password_input = $_POST['password'] ?? '';

    try {
        $stmt = mysqli_prepare($db_connection, "SELECT user.*, role.name AS role_name FROM user 
            LEFT JOIN user_role ON user.user_id = user_role.user_id 
            LEFT JOIN role ON user_role.role_id = role.role_id 
            WHERE email = ?
        ");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $stored_password = (string)($row['password'] ?? '');

            if (password_verify($password_input, $stored_password) || hash_equals($stored_password, $password_input)) {
                $raw_role = !empty($row['role_name']) ? $row['role_name'] : ($row['user_type'] ?? '');
                $role_slug = strtolower(str_replace([' ', '-', '_'], '', $raw_role));

                // Session keys used by /src layout and navbar
                $_SESSION['user_id']    = $row['user_id'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_name']  = $row['full_name'];
                $_SESSION['user_role']  = map_role_for_layout($row['role_name'] ?? '', $row['user_type'] ?? '');
                $_SESSION['eco_points'] = $row['impact_pts'];

                $_SESSION['email']      = $row['email'];
                $_SESSION['fullname']   = $row['full_name'];
                $_SESSION['userid']     = $row['student_id'];
                $_SESSION['role']       = !empty($row['role_name']) ? $row['role_name'] : $row['user_type'];
                $_SESSION['user_type']  = $row['user_type'];
                $_SESSION['impact_pts'] = $row['impact_pts'];
                $_SESSION['db_user_id'] = $row['user_id'];

                if (strpos($role_slug, 'ecoambassador') !== false || strpos($role_slug, 'ambassador') !== false) {
                    header("Location: /Assignment/src/pages/ambassador/ambassador.php");
                } elseif (strpos($role_slug, 'facilities') !== false || strpos($role_slug, 'admin') !== false) {
                    header("Location: /Assignment/src/pages/facilities/facilities.php");
                } else {
                    header("Location: /Assignment/src/pages/dashboard/dashboard.php");
                }
                exit();
            }

            $error_message = "Invalid APU Email or Password.";
        } else {
            $error_message = "Invalid APU Email or Password.";
        }
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}



$layout_body = function ($user_info) use ($error_message) {
?>
    <link rel="stylesheet" href="/Assignment/src/pages/login/style.css">
    <link rel="stylesheet" href="/Assignment/src/pages/login/login_styles.css">

    <div class="login-container">
        <div class="login-wrapper">
            <div class="login-header">
                <div class="login-logo-container">
                    <?php echo render_leaf_icon('login-logo-icon'); ?>
                </div>
                <h1 class="login-title">
                    Sign in to APU EcoSpot
                </h1>
                <p class="login-subtitle">
                    Use your APU institutional email to continue
                </p>
            </div>


            <?php if (!empty($error_message)): ?>
                <div class="login-error-container">
                    <span class="login-error-icon-bg">
                        <i data-lucide="x" class="login-error-icon"></i>
                    </span>
                    <span class="login-error-text"><?php echo $error_message; ?></span>
                </div>
            <?php endif; ?>


            <div class="login-tab-container">
                <button class="login-tab-active">
                    Sign In
                </button>
                <a href="register.php" class="login-tab-inactive">
                    Register
                </a>
            </div>

            <div class="login-form-container">
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="login-form">
                    <div>
                        <label class="login-label">APU Email</label>
                        <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required
                            placeholder="yourname@mail.apu.edu.my" class="login-input" />
                    </div>
                    <div>
                        <div class="login-label-group">
                            <label class="login-label">Password</label>
                            <button type="button" class="login-forgot-password">
                                Forgot password?
                            </button>
                        </div>
                        <input type="password" name="password" required
                            placeholder="••••••••" class="login-input" />
                    </div>

                    <button type="submit" class="login-btn">
                        Sign In with APU Email
                    </button>
                </form>

                <p class="login-register-prompt">
                    No account yet?
                    <a href="register.php" class="login-register-link">
                        Register here
                    </a>
                </p>

                <div class="login-demo-container">
                    <p class="login-demo-title">Demo — Sign in as (For Testing Purposes)</p>
                    <div class="login-demo-grid">
                        <button type="button" class="login-demo-btn login-demo-student" onclick="fillDemo('ahmad@mail.apu.edu.my', 'password123')">Student</button>
                        <button type="button" class="login-demo-btn login-demo-eco" onclick="fillDemo('ecoambassador@apu.edu.my', 'abc12345')">Eco-Amb.</button>
                        <button type="button" class="login-demo-btn login-demo-facilities" onclick="fillDemo('admin@apu.edu.my', 'admin123')">Facilities</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillDemo(email, password) {
        let emailInputs = document.querySelectorAll('input[name="email"]');
        let passwordInputs = document.querySelectorAll('input[name="password"]');

        emailInputs.forEach(input => input.value = email);
        passwordInputs.forEach(input => input.value = password);
        let forms = document.querySelectorAll('.login-form');
        forms.forEach(form => {
            if (form.offsetParent !== null) {
                form.submit();
            }
        });
    }
    </script>
<?php
};

render_layout("Login Page", $layout_body);
?>
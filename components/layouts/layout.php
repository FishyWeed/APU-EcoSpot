<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/util/dbcon.php';
require_once dirname(__DIR__, 2) . '/util/dashboard_fetch.php';

require_once 'desktop_layout.php';
require_once 'mobile_layout.php';

function render_layout($page_title, $layout_body, $layout_left = null, $layout_right = null)
{
    global $db_connection;

    $current_filename = basename($_SERVER['PHP_SELF']);
    $is_logged_in = !empty($_SESSION['user_email']) || !empty($_SESSION['email']);

    $user_info = [
        'is_logged_in' => $is_logged_in,
        'user_id'      => $_SESSION['user_id'] ?? $_SESSION['db_user_id'] ?? null,
        'user_email'   => $_SESSION['user_email'] ?? $_SESSION['email'] ?? null,
        'user_name'    => $_SESSION['user_name'] ?? $_SESSION['fullname'] ?? 'Guest User',
        'user_role'    => $_SESSION['user_role'] ?? 'student',
        'eco_points'   => (int)($_SESSION['eco_points'] ?? $_SESSION['impact_pts'] ?? 0),
        'my_reports'   => []
    ];

    if ($is_logged_in && isset($db_connection) && $db_connection instanceof mysqli) {
        $session_user_id = (int)($user_info['user_id'] ?? 0);

        if ($session_user_id > 0) {
            $stmt = mysqli_prepare(
                $db_connection,
                "SELECT user.full_name, user.impact_pts, user.user_type, role.name AS role_name
                FROM user
                LEFT JOIN user_role ON user.user_id = user_role.user_id
                LEFT JOIN role ON user_role.role_id = role.role_id
                WHERE user.user_id = ?
                LIMIT 1"
            );
            mysqli_stmt_bind_param($stmt, "i", $session_user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && ($row = mysqli_fetch_assoc($result))) {
                $user_info['user_name']  = $row['full_name'];
                $user_info['eco_points'] = (int)$row['impact_pts'];

                $raw_role = !empty($row['role_name']) ? $row['role_name'] : ($row['user_type'] ?? '');
                $role_slug = strtolower(str_replace([' ', '-', '_'], '', $raw_role));

                if (strpos($role_slug, 'ecoambassador') !== false || strpos($role_slug, 'ambassador') !== false) {
                    $user_info['user_role'] = 'ambassador';
                } elseif (strpos($role_slug, 'facilities') !== false || strpos($role_slug, 'admin') !== false) {
                    $user_info['user_role'] = 'facilities';
                } else {
                    $user_info['user_role'] = 'student';
                }

                $_SESSION['user_name']  = $user_info['user_name'];
                $_SESSION['eco_points'] = $user_info['eco_points'];
                $_SESSION['user_role']  = $user_info['user_role'];
                $_SESSION['impact_pts'] = $user_info['eco_points'];
            }

            mysqli_stmt_close($stmt);

            $stmt_t = mysqli_prepare(
                $db_connection,
                "SELECT ticket_id AS id, description AS title, 'APU Campus' AS location, status 
                FROM ticket 
                WHERE user_id = ? 
                ORDER BY ticket_id DESC"
            );

            if ($stmt_t) {
                mysqli_stmt_bind_param($stmt_t, "i", $session_user_id);
                mysqli_stmt_execute($stmt_t);
                $res_t = mysqli_stmt_get_result($stmt_t);

                while ($row_t = mysqli_fetch_assoc($res_t)) {
                    if (strlen($row_t['title']) > 24) {
                        $row_t['title'] = substr($row_t['title'], 0, 22) . '...';
                    }

                    $row_t['time']     = 'Just now';
                    $row_t['icon']     = '💡';
                    $row_t['bg_color'] = '#ffebeb';

                    if ($row_t['status'] === 'validated') {
                        $row_t['icon'] = '🌡️';
                        $row_t['bg_color'] = '#f2f7f2';
                    } elseif ($row_t['status'] === 'resolved') {
                        $row_t['icon'] = '🖥️';
                        $row_t['bg_color'] = '#eef2ff';
                    }

                    $user_info['my_reports'][] = $row_t;
                }
                mysqli_stmt_close($stmt_t);
            }
        }
    }

    $public_pages = ['index.php', 'login.php', 'register.php'];

    if (!$is_logged_in && !in_array($current_filename, $public_pages, true)) {
        header('Location: /Assignment/src/pages/login/login.php');
        exit();
    }
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($page_title); ?></title>
        <style>
            html,
            body {
                margin: 0;
                padding: 0;
                min-height: 100vh;
                font-family: system-ui, -apple-system, sans-serif;
                background-color: #f5f4ef;
                overflow-x: hidden;
            }

            @media (max-width: 925px) {

                html,
                body {
                    font-size: 12px;
                }

                .desktop-layout-shell {
                    display: none !important;
                }

                .mobile-layout-shell {
                    display: flex;
                    flex-direction: column;
                    min-height: 100vh;
                    box-sizing: border-box;
                }
            }

            @media (min-width: 926px) {

                html,
                body {
                    font-size: 16px;
                }

                .mobile-layout-shell {
                    display: none !important;
                }

                .desktop-layout-shell {
                    display: flex;
                    flex-direction: column;
                    min-height: 100vh;
                    box-sizing: border-box;
                }
            }
        </style>
    </head>

    <body>

        <div class="desktop-layout-shell">
            <?php render_desktop_layout($layout_body, $user_info, $layout_left, $layout_right); ?>
        </div>

        <div class="mobile-layout-shell">
            <?php render_mobile_layout($layout_body, $user_info, $layout_left, $layout_right); ?>
        </div>

    </body>

    </html>
<?php
}
?>
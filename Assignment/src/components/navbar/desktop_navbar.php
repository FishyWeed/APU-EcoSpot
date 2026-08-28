<?php
require_once dirname(__DIR__) . '/logo.php';

function render_desktop_navbar($user_info)
{
    $current_page = basename($_SERVER['PHP_SELF']);
    $user_role = $user_info['user_role'] ?? 'user';
?>
    <style>
        .site-navbar-wrapper {
            width: 100%;
            background-color: #ffffff;
            border-bottom: 1px solid #eaeaea;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;

            z-index: 1000;

            .site-navbar-grid {
                display: grid;
                grid-template-columns: 15% 1fr 15%;
                align-items: center;
                max-width: 100%;
                margin: 0 auto;
                box-sizing: border-box;
                height: 4rem;

                .nav-left-zone {
                    grid-column: 1;
                    padding-left: 1rem;
                }

                .nav-center-menu {
                    grid-column: 2;
                    display: flex;
                    justify-content: center;
                    gap: 12px;
                }

                .nav-right-zone {
                    grid-column: 3;
                    display: flex;
                    justify-content: flex-end;
                    align-items: center;
                    padding-right: 1rem;
                    gap: 12px;
                }

                .nav-link-item {
                    color: #707070;
                    text-decoration: none;
                    font-weight: 500;
                    font-size: 0.95rem;
                    padding: 8px 16px;
                    border-radius: 20px;
                    transition: all 0.2s ease;

                    &:hover {
                        filter: brightness(1.05);
                        transform: translateY(-1px);
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                        background-color: #1a1a1a1a;
                        color: #1a1a1a;
                    }

                    &.active {
                        background-color: #f5f5f5;
                        color: #1a1a1a;
                        border: 1px solid #1a1a1a1a;
                        font-weight: 600;
                    }
                }

                .nav-user-avatar {
                    width: 2.5rem;
                    height: 2.25rem;
                    background-color: #417f42;
                    color: #ffffff;
                    text-decoration: none;
                    border-radius: 10px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                    font-weight: 600;
                    font-size: 0.85rem;
                    cursor: pointer;
                }
            }
        }
    </style>

    <div class="site-navbar-wrapper">
        <nav class="site-navbar-grid">
            <div class="nav-left-zone">
                <?php render_logo(); ?>
            </div>

            <div class="nav-center-menu">
                <?php if ($user_info['is_logged_in']): ?>
                    <a href="/Assignment/src/pages/dashboard/dashboard.php" class="nav-link-item <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
                    <a href="/Assignment/src/pages/leaderboard/leaderboard.php" class="nav-link-item <?php echo ($current_page === 'leaderboard.php') ? 'active' : ''; ?>">Leaderboard</a>

                    <?php if ($user_info['user_role'] === 'student'): ?>
                        <a href="/Assignment/src/pages/student/student.php" class="nav-link-item <?php echo ($current_page === 'student.php') ? 'active' : ''; ?>">Student</a>
                    <?php elseif ($user_info['user_role'] === 'ambassador'): ?>
                        <a href="/Assignment/src/pages/ambassador/ambassador.php" class="nav-link-item <?php echo ($current_page === 'ambassador.php') ? 'active' : ''; ?>">Ambassador</a>
                    <?php elseif ($user_info['user_role'] === 'facilities'): ?>
                        <a href="/Assignment/src/pages/facilities/facilities.php" class="nav-link-item <?php echo ($current_page === 'facilities.php') ? 'active' : ''; ?>">Facilities</a>
                    <?php endif; ?>
                <?php endif ?>
            </div>

            <div class="nav-right-zone">
                <?php if ($user_info['is_logged_in']): ?>
                    <?php render_button('＋ Report', '0.95rem', '/Assignment/src/pages/report/report.php', true, '#ffffff', '#417f42', 'none', 'auto', 'auto', '8px 18px', '20px'); ?>
                    <?php
                    $name = $user_info['user_name'] ?? 'User';
                    $parts = explode(' ', $name);
                    $initials = $parts[0][0] . $parts[1][0];
                    render_button(
                        '
                        <span class="nav-user-avatar">
                            ' . $initials . '
                        </span>',
                        '0.9rem',
                        '/Assignment/src/pages/profile/profile.php',
                        true,
                        '#ffffff',
                        'transparent',
                        'none',
                        'auto',
                        'auto',
                        '0'
                    );
                    ?>

                    <?php render_button('[→ Sign out', '0.9rem', '/Assignment/src/util/logout.php', true, '#707070', 'transparent', 'none', 'auto', 'auto', '6px 12px'); ?>
                <?php else: ?>
                    <?php render_button('＋ Report', '0.95rem', '/Assignment/src/pages/login/login.php', true, '#ffffff', '#417f42', 'none', 'auto', 'auto', '8px 18px', '20px'); ?>

                    <?php render_button('→] Sign In', '0.95rem', '/Assignment/src/pages/login/login.php', true, '#1a1a1a', '#ffffff', '#eaeaea', 'auto', 'auto', '8px 20px', '20px'); ?>
                <?php endif; ?>
            </div>
        </nav>
    </div>
<?php
}
?>
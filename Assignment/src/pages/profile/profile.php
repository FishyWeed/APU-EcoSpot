<?php
require_once __DIR__ . '/../../components/components.php';

$layout_body = function ($user_info) {
    $name = $user_info['user_name'] ?? 'User';
    $parts = explode(' ', $name);
    $initials = $parts[0][0] . $parts[1][0];

    $username = $user_info['user_name'] ?? 'User';
    $email = $user_info['user_email'] ?? 'user@mail.apu.edu.my';
    $role_name = $user_info['user_role'] ?? 'Student';

    $display_role = ucwords(str_replace(['-', '_'], ' ', $role_name));
?>
    <style>
        .profile-container {
            width: 100%;
            max-width: 30rem;
            margin: 2rem auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
            font-family: system-ui, -apple-system, sans-serif;
            box-sizing: border-box;

            .profile-header-card {
                background-color: #ffffff;
                border: 1px solid #eaeaea;
                border-radius: 16px;
                padding: 2.5rem 1.5rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.015);
                box-sizing: border-box;

                .profile-display-name {
                    font-size: 1.35rem;
                    font-weight: 800;
                    color: #1a1a1a;
                    margin: 0 0 6px 0;
                    letter-spacing: -0.3px;
                }

                .profile-display-email {
                    font-size: 0.9rem;
                    color: #b5b5b5;
                    margin: 0 0 1.25rem 0;
                    font-weight: 500;
                }

                .role-badge-wrapper {
                    display: inline-block;
                }
            }

            .profile-navigation-card {
                background-color: #ffffff;
                border: 1px solid #eaeaea;
                border-radius: 16px;
                padding: 1rem 0.5rem;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.015);
                box-sizing: border-box;

                .menu-section-title {
                    font-size: 0.75rem;
                    font-weight: 700;
                    color: #b5b5b5;
                    text-transform: uppercase;
                    letter-spacing: 0.07em;
                    margin: 0 0 1.25rem 6px;
                }

                .menu-links-list {
                    display: flex;
                    flex-direction: column;
                    width: 100%;
                }

                .menu-link-row {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 0.9rem 0.75rem;
                    text-decoration: none !important;
                    border-radius: 10px;
                    color: #1a1a1a;
                    font-weight: 600;
                    font-size: 1.05rem;
                    transition: background-color 0.2s ease, transform 0.1s ease;
                    box-sizing: border-box;

                    &:hover {
                        background-color: #f5f4ef;

                        .arrow-chevron {
                            transform: translateX(2px);
                            color: #1a1a1a;
                        }
                    }

                    &:active {
                        transform: scale(0.99);
                    }

                    &:not(:last-child) {
                        border-bottom: 1px solid #fafafa;
                    }

                    .link-left-content {
                        display: flex;
                        align-items: center;
                        gap: 14px;
                    }

                    .link-icon {
                        font-size: 1.25rem;
                        line-height: 1;
                        color: #707070;
                    }

                    .link-right-content {
                        display: flex;
                        align-items: center;
                        gap: 10px;
                    }

                    .link-counter-metric {
                        font-size: 0.85rem;
                        color: #8c8c8c;
                        font-weight: 500;
                    }

                    .arrow-chevron {
                        font-size: 1rem;
                        color: #c5c5c5;
                        font-weight: 400;
                        transition: transform 0.2s ease, color 0.2s ease;
                    }
                }
            }
        }
    </style>

    <div class="profile-container">
        <div class="profile-header-card">
            <?php
            render_button(
                $initials,
                '1.25rem',
                '#',
                true,
                '#ffffff',
                '#417f42',
                '#e0e0e0',
                '2.75rem',
                '2.75rem',
                '0px 0px',
                '10px'
            );
            ?>


            <h2 class="profile-display-name"><?php echo htmlspecialchars($username); ?></h2>
            <p class="profile-display-email"><?php echo htmlspecialchars($email); ?></p>

            <div class="role-badge-wrapper">
                <?php
                render_button(
                    $display_role,
                    '0.8rem',
                    '#',
                    true,
                    '#3a61d0',
                    '#eef4ff',
                    '#dbe8fe',
                    'auto',
                    'auto',
                    '6px 14px',
                    '20px'
                );
                ?>
            </div>
        </div>

        <div class="profile-navigation-card">
            <p class="menu-section-title">Navigation</p>

            <div class="menu-links-list">
                <a href="/Assignment/src/pages/index.php" class="menu-link-row">
                    <div class="link-left-content">
                        <span class="link-icon">🏠</span>
                        <span>Home Page</span>
                    </div>
                    <span class="arrow-chevron">&rsaquo;</span>
                </a>

                <a href="/Assignment/src/pages/dashboard/dashboard.php" class="menu-link-row">
                    <div class="link-left-content">
                        <span class="link-icon">📊</span>
                        <span>Dashboard</span>
                    </div>
                    <span class="arrow-chevron">&rsaquo;</span>
                </a>

                <a href="/Assignment/src/pages/leaderboard/leaderboard.php" class="menu-link-row">
                    <div class="link-left-content">
                        <span class="link-icon">🏆</span>
                        <span>Campus Leaderboard</span>
                    </div>
                    <span class="arrow-chevron">&rsaquo;</span>
                </a>

                <?php if (strtolower($role_name) === 'student'): ?>
                    <a href="/Assignment/src/pages/student/student.php" class="menu-link-row">
                        <div class="link-left-content">
                            <span class="link-icon">🎓</span>
                            <span>Student Console</span>
                        </div>
                        <span class="arrow-chevron">&rsaquo;</span>
                    </a>
                <?php elseif (strtolower($role_name) === 'facilities'): ?>
                    <a href="/Assignment/src/pages/facilities/facilities.php" class="menu-link-row">
                        <div class="link-left-content">
                            <span class="link-icon">🏢</span>
                            <span>Facilities Console</span>
                        </div>
                    </a>
                <?php elseif (strtolower($role_name) === 'ambassador'): ?>
                    <a href="/Assignment/src/pages/ambassador/ambassador.php" class="menu-link-row">
                        <div class="link-left-content">
                            <span class="link-icon">🛡️</span>
                            <span>Ambassador Console</span>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="profile-navigation-card">
            <div class="menu-links-list">
                <a href="/Assignment/src/util/logout.php" class="menu-link-row">
                    <div class="link-left-content"">
                        <span class=" link-icon">🚪</span>
                        <span>[→ Sign Out</span>
                    </div>
                    <span class="arrow-chevron">&rsaquo;</span>
                </a>
            </div>
        </div>
    <?php
};
render_layout("User Profile Dashboard", $layout_body); ?>
?>
<?php

function render_mobile_navbar($user_info)
{
    $current_page = basename($_SERVER['PHP_SELF']);
    $user_role = $user_info['user_role'] ?? 'user';
?>
    <style>
        .mobile-dock-wrapper {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 5.5rem;
            background-color: #ffffff;
            border-top: 1px solid #eaeaea;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.04);
            z-index: 9999;
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 0 1rem;
            box-sizing: border-box;

            .dock-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-decoration: none !important;
                color: #707070;
                font-size: 0.75rem;
                font-weight: 500;
                gap: 0.35rem;
                width: 5rem;
                height: 100%;
                position: relative;
                transition: color 0.2s ease;

                &.active {
                    color: #417f42;
                    font-weight: 600;

                    &::after {
                        content: '';
                        position: absolute;
                        bottom: 8px;
                        width: 8px;
                        height: 4px;
                        background-color: #417f42;
                        border-radius: 3px;
                    }
                }

                .dock-icon {
                    font-size: 1.35rem;
                    line-height: 1;
                }

            }

            .dock-report-button-wrapper {
                position: relative;
                width: 4.75rem;
                height: 4.75rem;
                display: flex;
                align-items: center;
                justify-content: center;

                .dock-report-button {
                    position: absolute;
                    top: -24px;
                    width: 4.45rem;
                    height: 4.45rem;
                    background-color: #558B55;
                    color: #ffffff !important;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.8rem;
                    font-weight: 400;
                    text-decoration: none !important;
                    box-shadow: 0 4px 12px rgba(85, 139, 85, 0.45);
                    border: 1px solid #417f42;
                    transition: transform 0.2s ease, background-color 0.2s ease;

                    & > span {
                        transform: translateX(0.5px);
                    }

                    &:hover {
                        background-color: #417f42;
                        transform: translateY(-2px);
                    }

                    &:active {
                        transform: translateY(0) scale(0.95);
                        & > span {
                            transform: translateX(0.5px);
                        }
                    }
                }
            }
        }
    </style>

    <div class="mobile-dock-wrapper">
        <a href="/Assignment/src/pages/dashboard/dashboard.php" class="dock-item <?php echo ($current_page === 'dashboard.php' || $current_page === 'dashboard.php') ? 'active' : ''; ?>">
            <span class="dock-icon"><?php render_color_mask('#5b8a58', '/Assignment/src/assets/dashboard.png', '1.35rem', '1.35rem'); ?></span>
            <span>Dashboard</span>
        </a>

        <a href="/Assignment/src/pages/leaderboard/leaderboard.php" class="dock-item <?php echo ($current_page === 'leaderboard.php') ? 'active' : ''; ?>">
            <span class="dock-icon"><?php render_color_mask('#5b8a58', '/Assignment/src/assets/podium.png', '1.35rem', '1.35rem'); ?></span>
            <span>Leaderboard</span>
        </a>


        <div class="dock-report-button-wrapper">
            <a href="/Assignment/src/pages/report/report.php" class="dock-report-button"><span><?php render_color_mask('#ffffff', '/Assignment/src/assets/plus.png', '2rem', '2rem'); ?></span></a>
        </div>


        <a href="<?php
                    if ($user_role === 'student') {
                        echo '/Assignment/src/pages/student/student.php';
                    } else if ($user_role === 'ambassador') {
                        echo '/Assignment/src/pages/ambassador/ambassador.php';
                    } else if ($user_role === 'facilities') {
                        echo '/Assignment/src/pages/facilities/facilities.php';
                    }
                    ?>" class="dock-item <?php echo ($current_page === 'student.php' || $current_page === 'ambassador.php' || $current_page === 'facilities.php') ? 'active' : ''; ?>">
            <span class="dock-icon"><?php render_color_mask('#5b8a58', '/Assignment/src/assets/console.png', '1.35rem', '1.35rem'); ?></span>
            <span>Console</span>
        </a>

        <a href="/Assignment/src/pages/profile/profile.php" class="dock-item <?php echo ($current_page === 'profile.php') ? 'active' : ''; ?>">
            <span class="dock-icon"><?php render_color_mask('#5b8a58', '/Assignment/src/assets/users-round.png', '1.35rem', '1.35rem'); ?></span>
            <span>Profile</span>
        </a>
    </div>
<?php
}

?>
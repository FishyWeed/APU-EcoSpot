<?php
require_once __DIR__ . '/../../components/components.php';
require_once __DIR__ . '/../../util/dbcon.php';

if (!function_exists('schoolAbbr')) {
    function schoolAbbr($name)
    {
        $skip = ['of', 'and', '&', 'the'];
        $words = explode(' ', $name);
        $abbr = '';
        foreach ($words as $word) {
            $clean = trim(preg_replace('/[^a-zA-Z]/', '', $word));
            if (!in_array(strtolower($clean), $skip, true) && strlen($clean) > 0) {
                $abbr .= strtoupper($clean[0]);
            }
        }
        return !empty($abbr) ? $abbr : strtoupper(substr($name, 0, 3));
    }
}

if (!function_exists('schoolAvatarCode')) {
    function schoolAvatarCode($name)
    {
        $abbr = schoolAbbr($name);
        if (strlen($abbr) >= 2) {
            return substr($abbr, 0, 2);
        }
        return strtoupper(substr($name, 0, 2));
    }
}

if (!function_exists('userInitials')) {
    function userInitials($name)
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

    $current_user_id = $user_info['user_id'] ?? $_SESSION['user_id'] ?? $_SESSION['db_user_id'] ?? null;
    $user_faculty = '';

    if ($current_user_id && $db_connection instanceof mysqli) {
        $stmt_uf = mysqli_prepare($db_connection, "SELECT school_department FROM user WHERE user_id = ? LIMIT 1");
        if ($stmt_uf) {
            mysqli_stmt_bind_param($stmt_uf, "i", $current_user_id);
            mysqli_stmt_execute($stmt_uf);
            $res_uf = mysqli_stmt_get_result($stmt_uf);
            if ($row_uf = mysqli_fetch_assoc($res_uf)) {
                $user_faculty = $row_uf['school_department'] ?? '';
            }
            mysqli_stmt_close($stmt_uf);
        }
    }

    $sql_faculty = "
        SELECT 
            school_department,
            SUM(impact_pts) AS impact_points,
            (SELECT COUNT(*) FROM ticket t JOIN user u2 ON t.user_id = u2.user_id WHERE u2.school_department = u.school_department) AS total_reports
        FROM user u
        WHERE user_type != 'Facilities' AND school_department IS NOT NULL AND TRIM(school_department) != ''
        GROUP BY school_department
        ORDER BY impact_points DESC
        LIMIT 10
    ";
    $result_faculty = mysqli_query($db_connection, $sql_faculty);
    $faculty_rows   = [];
    if ($result_faculty) {
        while ($row = mysqli_fetch_assoc($result_faculty)) {
            $faculty_rows[] = $row;
        }
    }

    $sql_individual = "
        SELECT 
            u.user_id,
            u.full_name,
            u.school_department,
            u.impact_pts,
            COUNT(t.ticket_id) AS total_reports
        FROM user u
        LEFT JOIN ticket t ON u.user_id = t.user_id
        WHERE u.user_type != 'Facilities'
        GROUP BY u.user_id
        ORDER BY u.impact_pts DESC
        LIMIT 10
    ";
    $result_individual = mysqli_query($db_connection, $sql_individual);
    $individual_rows   = [];
    $user_rank         = 0;
    $user_points       = 0;
    $rank_counter      = 1;
    if ($result_individual) {
        while ($row = mysqli_fetch_assoc($result_individual)) {
            $row['rank']          = $rank_counter++;
            $row['impact_points'] = (int)$row['impact_pts'];
            if ($current_user_id && $row['user_id'] == $current_user_id) {
                $user_rank   = $row['rank'];
                $user_points = $row['impact_points'];
            }
            $individual_rows[] = $row;
        }
    }

    $rank_colours = [
        1 => ['bg' => '#FFF3CD', 'text' => '#D97706'],
        2 => ['bg' => '#F2F2F2', 'text' => '#6B7280'],
        3 => ['bg' => '#FEE9D8', 'text' => '#C2410C'],
    ];
?>
    <link rel="stylesheet" href="leaderboard_styles.css">

    <div class="leaderboard-container">
        <div class="leaderboard-header-row">
            <div>
                <h1 class="leaderboard-title">Campus Leaderboard</h1>
                <p class="leaderboard-subtitle"><?php echo date('F Y'); ?> · Updated hourly</p>
            </div>
            <div class="leaderboard-tabs" id="lb-tabs">
                <button type="button" id="tab-faculty" onclick="showTab('faculty')" class="leaderboard-tab active">Faculty</button>
                <button type="button" id="tab-individual" onclick="showTab('individual')" class="leaderboard-tab">Individual</button>
            </div>
        </div>

        <div id="tab-faculty-content">
            <?php if (count($faculty_rows) >= 3): ?>
                <div class="leaderboard-podium">
                    <?php
                    $medals = ['🏆', '🥈', '🥉'];
                    $podium_styles = [
                        'leaderboard-podium-card first',
                        'leaderboard-podium-card',
                        'leaderboard-podium-card',
                    ];
                    for ($i = 0; $i < 3; $i++):
                        $r = $faculty_rows[$i];
                    ?>
                        <div class="<?php echo $podium_styles[$i]; ?>">
                            <div class="leaderboard-podium-medal"><?php echo $medals[$i]; ?></div>
                            <div class="leaderboard-podium-name"><?php echo htmlspecialchars(schoolAbbr($r['school_department'])); ?></div>
                            <div class="leaderboard-podium-score"><?php echo number_format($r['impact_points']); ?></div>
                            <div class="leaderboard-podium-unit">pts</div>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

            <div class="leaderboard-list-container">
                <div class="leaderboard-list">
                    <?php if (empty($faculty_rows)): ?>
                        <div class="leaderboard-empty">No reports submitted yet.</div>
                    <?php else: ?>
                        <?php foreach ($faculty_rows as $i => $r):
                            $rank = $i + 1;
                            $rc = $rank_colours[$rank] ?? ['bg' => '#F5F5F5', 'text' => '#7A9175'];
                            $is_my_faculty = (!empty($user_faculty) && strcasecmp(trim($r['school_department']), trim($user_faculty)) === 0);
                        ?>
                            <div class="leaderboard-list-item <?php echo $is_my_faculty ? 'is-faculty' : ''; ?>">
                                <div class="leaderboard-rank" style="background-color: <?php echo $rc['bg']; ?>; color: <?php echo $rc['text']; ?>;">
                                    <?php echo $rank; ?>
                                </div>
                                <div class="leaderboard-list-avatar faculty <?php echo $is_my_faculty ? 'highlight' : ''; ?>">
                                    <?php echo htmlspecialchars(schoolAvatarCode($r['school_department'])); ?>
                                </div>
                                <div class="leaderboard-list-info">
                                    <div class="leaderboard-title-line">
                                        <span class="leaderboard-list-title"><?php echo htmlspecialchars($r['school_department']); ?></span>
                                        <?php if ($is_my_faculty): ?>
                                            <span class="leaderboard-you-badge">Your Faculty</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="leaderboard-list-subtitle">
                                        <?php echo $r['total_reports']; ?> report<?php echo $r['total_reports'] != 1 ? 's' : ''; ?> submitted
                                    </div>
                                </div>
                                <div class="leaderboard-list-pts-badge">
                                    <span class="leaderboard-list-pts-icon">&#9733;</span> <?php echo number_format($r['impact_points']); ?> pts
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div id="tab-individual-content" class="leaderboard-hidden">
            <?php if (count($individual_rows) >= 3): ?>
                <div class="leaderboard-podium">
                    <?php
                    $medals = ['🏆', '🥈', '🥉'];
                    $podium_styles = [
                        'leaderboard-podium-card first',
                        'leaderboard-podium-card',
                        'leaderboard-podium-card',
                    ];
                    for ($i = 0; $i < 3; $i++):
                        $r = $individual_rows[$i];
                    ?>
                        <div class="<?php echo $podium_styles[$i]; ?>">
                            <div class="leaderboard-podium-medal"><?php echo $medals[$i]; ?></div>
                            <div class="leaderboard-podium-name"><?php echo htmlspecialchars($r['full_name']); ?></div>
                            <div class="leaderboard-podium-score"><?php echo number_format($r['impact_points']); ?></div>
                            <div class="leaderboard-podium-unit">pts</div>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

            <div class="leaderboard-list-container">
                <div class="leaderboard-list">
                    <?php if (empty($individual_rows)): ?>
                        <div class="leaderboard-empty">No individual reports submitted yet.</div>
                    <?php else: ?>
                        <?php foreach ($individual_rows as $r):
                            $rank = $r['rank'];
                            $rc = $rank_colours[$rank] ?? ['bg' => '#F5F5F5', 'text' => '#7A9175'];
                            $is_me = ($current_user_id && $r['user_id'] == $current_user_id);
                        ?>
                            <div class="leaderboard-list-item <?php echo $is_me ? 'is-me' : ''; ?>">
                                <div class="leaderboard-rank" style="background-color: <?php echo $rc['bg']; ?>; color: <?php echo $rc['text']; ?>;">
                                    <?php echo $rank; ?>
                                </div>
                                <div class="leaderboard-list-avatar individual <?php echo $is_me ? 'highlight' : ''; ?>">
                                    <?php echo htmlspecialchars(userInitials($r['full_name'])); ?>
                                </div>
                                <div class="leaderboard-list-info">
                                    <div class="leaderboard-title-line">
                                        <span class="leaderboard-list-title"><?php echo htmlspecialchars($r['full_name']); ?></span>
                                        <?php if ($is_me): ?>
                                            <span class="leaderboard-you-badge">You</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="leaderboard-list-subtitle">
                                        <?php echo htmlspecialchars($r['school_department']); ?> · <?php echo $r['total_reports']; ?> report<?php echo $r['total_reports'] != 1 ? 's' : ''; ?>
                                    </div>
                                </div>
                                <div class="leaderboard-list-pts-badge">
                                    <span class="leaderboard-list-pts-icon">&#9733;</span> <?php echo number_format($r['impact_points']); ?> pts
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tab) {
            let  facultyTabs = document.querySelectorAll('.leaderboard-tabs #tab-faculty');
            let  individualTabs = document.querySelectorAll('.leaderboard-tabs #tab-individual');
            let  facultyContents = document.querySelectorAll('[id="tab-faculty-content"]');
            let  individualContents = document.querySelectorAll('[id="tab-individual-content"]');

            if (tab === 'faculty') {
                facultyTabs.forEach(el => el.classList.add('active'));
                individualTabs.forEach(el => el.classList.remove('active'));

                facultyContents.forEach(el => el.classList.remove('leaderboard-hidden'));
                individualContents.forEach(el => el.classList.add('leaderboard-hidden'));
            } else {
                individualTabs.forEach(el => el.classList.add('active'));
                facultyTabs.forEach(el => el.classList.remove('active'));

                individualContents.forEach(el => el.classList.remove('leaderboard-hidden'));
                facultyContents.forEach(el => el.classList.add('leaderboard-hidden'));
            }
        }
    </script>
<?php
};

render_layout("Leaderboard", $layout_body);
?>
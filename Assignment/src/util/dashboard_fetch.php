<?php
/**
 * Fetches dynamic real-time dashboard analytics counters from the database.
 * 
 * @param mysqli $db_connection The active database connection reference
 * @param int $user_id The logged-in user's database ID
 * @return array Processed tracking stats
 */
function fetch_student_dashboard_stats($db_connection, $user_id)
{
    $stats = [
        'campus_rank' => '—',
        'kwh_saved' => '0.0',
        'progress_percentage' => 5
    ];

    if (!$db_connection instanceof mysqli || !$user_id) {
        return $stats;
    }

    $sql_rank = "SELECT rank_pos 
        FROM (
            SELECT user_id, 
            DENSE_RANK() OVER (ORDER BY impact_pts DESC) AS rank_pos 
            FROM user
        ) rank_table 
        WHERE user_id = ? LIMIT 1
    ";
    
    if ($stmt_r = mysqli_prepare($db_connection, $sql_rank)) {
        mysqli_stmt_bind_param($stmt_r, "i", $user_id);
        mysqli_stmt_execute($stmt_r);
        $res_r = mysqli_stmt_get_result($stmt_r);
        if ($row_r = mysqli_fetch_assoc($res_r)) {
            $stats['campus_rank'] = "#" . ($row_r['rank_pos'] ?? '—');
        }
        mysqli_stmt_close($stmt_r);
    }

    $base_kwh_per_report = 1.25;
    $daily_target_kwh = 50.0;
    $total_resolved_tickets = 0;

    $sql_kwh = "SELECT COUNT(*) AS resolved_total FROM ticket WHERE status IN ('validated', 'resolved')";
    $res_kwh = mysqli_query($db_connection, $sql_kwh);
    if ($res_kwh && $row_kwh = mysqli_fetch_assoc($res_kwh)) {
        $total_resolved_tickets = (int)$row_kwh['resolved_total'];
    }

    $calculated_kwh = $total_resolved_tickets * $base_kwh_per_report;
    $stats['kwh_saved'] = number_format($calculated_kwh, 1);
    $stats['progress_percentage'] = min(100, max(5, round(($calculated_kwh / $daily_target_kwh) * 100)));

    return $stats;
}

/**
 * Aggregates active ticket metrics into kWh data arrays for the bar graph component.
 * 
 * @param mysqli $db_connection Active database connection reference
 * @return array Properly formatted dataset array matching your graph parameters
 */
function fetch_ambassador_chart_data($db_connection) {
    $graph_dataset = [];

    if (!$db_connection instanceof mysqli) {
        return $graph_dataset;
    }

    $base_kwh_per_report = 1.25;

    $sql = "SELECT block_name, COUNT(*) AS ticket_count 
            FROM ticket 
            WHERE status != 'resolved' 
            GROUP BY block_name 
            ORDER BY ticket_count DESC";

    if ($stmt = mysqli_prepare($db_connection, $sql)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $block_label = !empty($row['block_name']) ? $row['block_name'] : 'Block ?';
            $calculated_kwh = (int)$row['ticket_count'] * $base_kwh_per_report;

            $graph_dataset[] = [
                'location' => $block_label,
                'kwh'      => $calculated_kwh
            ];
        }
        mysqli_stmt_close($stmt);
    }

    return $graph_dataset;
}



<?php
require_once __DIR__ . '/../components/components.php';

$layout_body = function ($user_info) {
?>
    <style>
        .span-across-layout-horizontally {
            position: relative;
            max-width: 100vw;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
        }

        .hero-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            gap: 3rem;
            width: 100%;
            padding: 2rem 0;
            box-sizing: border-box;

            .hero-text-block {
                flex: 1;
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: flex-start;

                .hero-caption-note {
                    font-size: 0.9rem;
                    color: #8c8c8c;
                    margin-top: 1rem;
                    font-weight: 500;
                }
            }

            .hero-main-img {
                width: 100%;
                height: auto;
                border-radius: 1.25rem;
                object-fit: cover;
            }
        }

        @media (min-width: 1500px) {
            .hero-container {
                flex-direction: row;
                gap: 4rem;
                padding: 2rem 0;

                .hero-text-block {
                    max-width: 50%;
                }

                .hero-main-img {
                    min-width: 20rem;
                    max-width: 40rem;
                }
            }
        }

        @media (max-width: 1499px) {
            .hero-container {
                .hero-main-img {
                    min-width: 15rem;
                    max-width: 35rem;
                }
            }
        }

        .second-container {
            display: flex;
            flex-direction: row;
            text-align: center;
            align-items: center;
            justify-content: space-around;
            background-color: #5b8a58;
            margin-top: 2.5rem;
            margin-bottom: 2.5rem;

            div {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                flex: 1;
                padding: 0 clamp(3rem, 7rem, 9rem);
                position: relative;

                img {
                    width: 1.5rem;
                    height: 1.5rem;
                    filter: brightness(0) invert(1);
                }

                h2 {
                    font-size: clamp(0.75rem, 2rem, 2.25rem);
                    font-weight: 800;
                    color: #ffffff;
                    margin: 0;
                }

                p {
                    font-size: clamp(0.875rem, 1.25rem, 1.5rem);
                    color: #ffffff;
                    margin: 0;
                }

                &:not(:last-child)::after {
                    content: '';
                    position: absolute;
                    right: 0;
                    top: 15%;
                    height: 70%;
                    width: 1px;
                    background-color: rgba(255, 255, 255, 0.3);
                }

                @media (min-width: 1351px) {
                    padding: 4rem 0;
                }

                @media (max-width: 1350px) and (min-width: 891px) {
                    padding: 3.5rem 0;

                    div {
                        padding: 0 clamp(0rem, 3rem, 7rem);
                    }

                    h2 {
                        font-size: 1.5rem;
                    }

                    p {
                        font-size: 1rem;
                    }
                }

                @media (max-width: 890px) and (min-width: 481px) {
                    padding: 2.75rem 0;

                    div {
                        padding: 0 clamp(0.5rem, 1rem, 1rem);
                    }

                    h2 {
                        font-size: 1.5rem;
                    }

                    p {
                        font-size: 1rem;
                    }
                }

                @media (max-width: 480px) {
                    padding: 2rem 0;

                    div {
                        padding: 0 clamp(0.25rem, 0.75rem, 1.5rem);
                    }

                    h2 {
                        font-size: 1.25rem;
                    }

                    p {
                        font-size: 0.75rem;
                    }
                }
            }
        }

        .third-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            padding: 2rem 0 3rem 0;

            div {
                text-align: center;

                h2 {
                    font-size: clamp(1.5rem, 2.25rem, 2.5rem);
                    font-weight: 800;
                    color: #1a1a1a;
                    margin: 0 0 1.75rem 0;
                }

                p {
                    font-size: clamp(1rem, 1.25rem, 1.5rem);
                    color: #737373;
                    margin: 0;
                }
            }
        }

        .fourth-container {
            position: relative;
            width: 100%;
            padding: 3rem 0;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;

            &::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                transform: translateX(-50%);
                width: 200vw;
                height: 1px;
                background-color: #00000028;
            }
        }
    </style>

    <section class="hero-container">
        <div class="hero-text-block">
            <?php render_button('🍃 APU Sustainability Initiative 2026', '0.85rem', '#', true, '#5b8a58', '#e8f5e9', '#c8e6c9', 'auto', 'auto', '6px 14px', '20px'); ?>

            <h1 style="font-size: clamp(2.5rem, 3rem, 3.5rem); font-weight: 800; color: #1a1a1a; margin: 1.5rem 0 1rem 0; line-height: 1.2; letter-spacing: -0.5px;">
                Spot the waste. <span style="color: #5b8a58;">Fix it</span> in seconds.
            </h1>

            <p style="font-size: 1rem; line-height: 1.6; color: #737373; margin: 0 0 2rem 0; max-width: 100%;">
                APU EcoSpot lets students and faculty report energy waste across campus in under a minute. Together we save kWh — and earn points doing it.
            </p>
            <?php if ($user_info['is_logged_in']): ?>
                <?php render_button('Get Started →', '1.05rem', './dashboard/dashboard.php', true, '#ffffff', '#5b8a58', 'none', 'auto', 'auto', '14px 32px', '14px'); ?>
            <?php else: ?>
                <?php render_button('Get Started →', '1.05rem', './login/login.php', true, '#ffffff', '#5b8a58', 'none', 'auto', 'auto', '14px 32px', '14px'); ?>
            <?php endif; ?>

            <span class="hero-caption-note">Sign in with your APU email to report and earn points</span>
        </div>

        <?php render_button('<img src="../assets/apu.png" alt="APU Campus Landscape Overview" class="hero-main-img">', '0.85rem', '#', true, '#5b8a58', '#e8f5e9', '#c8e6c9', 'auto', 'auto', '0 0', '20px'); ?>
    </section>

    <section class="second-container span-across-layout-horizontally">
        <div>
            <img src="../assets/circle-check-big.png" alt="circle check icon">
            <h2>2,847</h2>
            <p>Reports Resolved</p>
        </div>
        <div>
            <img src="../assets/clock.png" alt="clock icon">
            <h2>4.2hrs</h2>
            <p>Average Response Time</p>
        </div>
        <div>
            <img src="../assets/users-round.png" alt="users icon">
            <h2>67</h2>
            <p>Active Eco-Ambassadors</p>
        </div>
    </section>


    <section class="third-container">
        <div>
            <h2>How it works</h2>
            <p>Three steps from spotted waste to campus resolution</p>
        </div>
        
        <div style="display: flex; flex-direction: row; gap: 2rem; flex-wrap: wrap; justify-content: center; width: 100%;">
            <?php render_metric_card('Report', 'Spot energy waste anywhere on campus. Snap a photo, pick the location, select waste type — done in under a minute.', '<span style="font-size: 1.125rem; font-weight: 500; color: #6db868;">1</span>', '25rem', 'auto', '#6dec8954'); ?>

            <?php render_metric_card('Validate', 'An Eco-Ambassador reviews your report, confirms it on-site, and escalates to Facilities Management.', '<span style="font-size: 1.125rem; font-weight: 500; color: #3ca9b8;">2</span>', '25rem', 'auto', '#8ef0f0b6'); ?>

            <?php render_metric_card('Resolve', 'A technician is dispatched, fixes the waste source, and marks the ticket resolved. You earn impact points!', '<span style="font-size: 1.125rem; font-weight: 500; color: #af7aec;">3</span>', '25rem', 'auto', '#af7aec54'); ?>
        </div>
    </section>

    <section class="fourth-container">
        <?php render_eco_card_container([
            [
                'id'       => 'ECO-2847',
                'title'    => 'Lighting Waste',
                'location' => 'B-02-04',
                'reporter' => 'Ahmad Faris',
                'time'     => '14 min ago',
                'status'   => 'pending',
                'icon'     => '💡',
                'bg_color' => '#ffebeb' 
            ],
            [
                'id'       => 'ECO-2846',
                'title'    => 'Aircon Waste',
                'location' => 'A-03-02',
                'reporter' => 'Priya Menon',
                'time'     => '1h ago',
                'status'   => 'validated',
                'icon'     => '🌡️',
                'bg_color' => '#f2f7f2'
            ],
            [
                'id'       => 'ECO-2845',
                'title'    => 'Projector Waste',
                'location' => 'CS Lab 3',
                'reporter' => 'Lim Wei',
                'time'     => '3h ago',
                'status'   => 'resolved',
                'icon'     => '🖥️',
                'bg_color' => '#eef2ff'
            ]
        ], 'grid'); ?>
    </section>

<?php
};

render_layout("Home Page", $layout_body, function() {}, function() {});
?>
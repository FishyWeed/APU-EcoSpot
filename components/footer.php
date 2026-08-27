<?php
require_once __DIR__ . '/logo.php';

function render_footer()
{
?>
    <style>
        .site-footer {
            width: 100%;
            background-color: #ffffff;
            border-top: 1px solid #eaeaea;
            padding: 1rem 1.25rem;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, sans-serif;

            display: flex;
            justify-content: space-between;
            align-items: center;
            .footer-left {
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .footer-brand {
                font-size: 0.85rem;
                font-weight: 700;
                color: #1a1a1a;
                white-space: nowrap;
            }

            .footer-tagline {
                font-size: 0.85rem;
                color: #8c8c8c;
                display: none;
            }

            .footer-copyright {
                font-size: 0.825rem;
                color: #8c8c8c;
                margin: 0;
                white-space: nowrap;
            }

            @media (min-width: 768px) {
                padding: 20px 24px;

                .footer-brand {
                    font-size: 0.9rem;
                }

                .footer-tagline {
                    display: inline-block;
                    font-size: 0.9rem;
                }

                .footer-copyright {
                    font-size: 0.9rem;
                }
            }
        }
    </style>

    <footer class="site-footer">
        <div class="footer-left">
            <?php render_logo(); ?>
            <span class="footer-tagline">&middot; Asia Pacific University Sustainability Initiative</span>
        </div>

        <p class="footer-copyright">
            &copy; <?php echo date("Y"); ?> All rights reserved.
        </p>
    </footer>
<?php
}
?>
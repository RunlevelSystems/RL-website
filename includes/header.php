<?php
// Site Header - PHP version with dynamic path detection
// This replaces header.html for better subdirectory support

// Path detection (same as navigation.php)
$current_url = $_SERVER['REQUEST_URI'];
$is_in_wiki = (strpos($current_url, '/staff/wiki/') !== false);
$is_in_staff = (strpos($current_url, '/staff/') !== false);
$is_in_projects = (strpos($current_url, '/projects/') !== false);

if ($is_in_wiki) {
    $header_base_path = '../../';
} elseif ($is_in_staff) {
    $header_base_path = '../';
} elseif ($is_in_projects) {
    $header_base_path = '../';
} else {
    $header_base_path = '';
}
?>
<!-- Site Header -->
<div class="site-header-bg" style="background-color: #0a0a0f; position: relative; z-index: 5; border-bottom: 1px solid rgba(0,212,255,0.1);">
    <div class="container-fluid">
        <div class="row" style="display: flex; align-items: center; padding: 8px 15px;">
            <div class="col-sm-5">
                <a href="<?php echo $header_base_path; ?>index.php" class="lx-logo" style="text-decoration: none;">
                    <div class="lx-logo-mark">LX</div>
                    <div class="lx-logo-text">
                        <span class="lx-logo-name">LEVEL <span>X</span></span>
                        <span class="lx-logo-sub">Development</span>
                    </div>
                </a>
            </div>
            <div class="col-sm-4 col-sm-offset-3 text-right">
                <form method="GET" action="<?php echo $header_base_path; ?>search.php">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Search Level X...">
                        <span class="input-group-btn">
                            <button class="btn btn-default btn-wds" type="submit">
                                <i class="ion-search" aria-hidden="true"></i> Search
                            </button>
                        </span>
                    </div><!-- /input-group -->
                </form>
            </div>
        </div>
    </div>
</div>

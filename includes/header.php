<?php
$current_url = $_SERVER['REQUEST_URI'];
$is_in_wiki = (strpos($current_url, '/staff/wiki/') !== false);
$is_in_staff = (strpos($current_url, '/staff/') !== false);
$is_in_projects = (strpos($current_url, '/projects/') !== false);

if ($is_in_wiki) {
    $header_base_path = '../../';
} elseif ($is_in_staff || $is_in_projects) {
    $header_base_path = '../';
} else {
    $header_base_path = '';
}
?>
<div class="site-header-bg">
    <div class="container-fluid">
        <div class="row wds-topbar-row">
            <div class="col-sm-6 col-xs-12">
                <a href="<?php echo $header_base_path; ?>index.php" class="lx-logo" aria-label="Core Loop Development home">
                    <img src="<?php echo $header_base_path; ?>assets/images/icon.png" alt="Core Loop icon" class="lx-logo-icon">
                    <span class="lx-logo-text">
                        <span class="lx-logo-name">Core Loop</span>
                        <span class="lx-logo-sub">Development</span>
                    </span>
                </a>
            </div>
            <div class="col-sm-6 col-xs-12 wds-header-search">
                <form method="GET" action="<?php echo $header_base_path; ?>search.php" role="search">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Search Core Loop..." aria-label="Search site">
                        <span class="input-group-btn">
                            <button class="btn btn-wds" type="submit">
                                <i class="ion-search" aria-hidden="true"></i> Search
                            </button>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

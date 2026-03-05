// Developed by World Domination Software LLC
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
<div class="site-header-bg" style="background-color: #000000; position: relative; z-index: 5;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
             <!--    <a href="<?php echo $header_base_path; ?>index.php"><img src="<?php echo $header_base_path; ?>assets/images/wds-hero.png" alt="logo"></a> -->
            </div>
            <div class="col-sm-3 col-sm-offset-3 text-right">
                <!-- <span class="ion-android-cart"></span> 0 products -->
                <form method="GET" action="<?php echo $header_base_path; ?>search.php">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Search WDS...">
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

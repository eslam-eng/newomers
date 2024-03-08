<?php
cleanConfigData();
$page  = 'dashboard';
$wo['all_pages'] = scandir('admin-panel/pages');
unset($wo['all_pages'] [0]);
unset($wo['all_pages'] [1]);
unset($wo['all_pages'] [2]);
$pages = array(
    'general-settings',
    'dashboard',
    'site-settings',
    'dashboard',
    'site-features',
    'amazon-settings',
    'email-settings',
    'social-login',
    'video-settings',
    'manage-languages',
    'add-language',
    'edit-lang',
    'manage-users',
    'manage-stories',
    'manage-profile-fields',
    'add-new-profile-field',
    'edit-profile-field',
    'manage-verification-reqeusts',
    'payment-reqeuests',
    'affiliates-settings',
    'referrals-list',
    'pro-memebers',
    'pro-settings',
    'pro-payments',
    'payment-settings',
    'manage-pages',
    'manage-groups',
    'manage-posts',
    'manage-articles',
    'manage-events',
    'manage-forum-sections',
    'manage-forum-forums',
    'manage-forum-threads',
    'manage-forum-messages',
    'create-new-section',
    'create-new-forum',
    'manage-movies',
    'add-new-movies',
    'manage-games',
    'add-new-game',
    'ads-settings',
    'manage-site-ads',
    'manage-user-ads',
    'manage-themes',
    'manage-site-design',
    'manage-announcements',
    'mass-notifications',
    'ban-users',
    'generate-sitemap',
    'manage-invitation-keys',
    'backups',
    'manage-custom-pages',
    'add-new-custom-page',
    'edit-custom-page',
    'edit-terms-pages',
    'manage_terms_pages',
    'manage-reports',
    'push-notifications-system',
    'manage-api-access-keys',
    'verfiy-applications',
    'manage-updates',
    'changelog',
    'online-users',
    'custom-code',
    'manage-third-psites',
    'edit-movie',
    'auto-delete',
    'manage-gifts',
    'add-new-gift',
    'post-settings',
    'manage-stickers',
    'add-new-sticker',
    'manage-apps',
    'auto-friend',
    'fake-users',
    'manage-genders',
    'pages-categories',
    'groups-categories',
    'blogs-categories',
    'products-categories',
    'bank-receipts',
    'manage-currencies',
    'manage-colored-posts',
    'job-categories',
    'manage-fund',
    'manage-jobs',
    'auto-like',
    'auto-join',
    'manage-reactions',
    'pages-sub-categories',
    'groups-sub-categories',
    'products-sub-categories',
    'pages-fields',
    'groups-fields',
    'products-fields',
    'pro-features',
    'pro-refund',
    'manage-offers',
    'manage-invitation',
    'send_email',
    'live',
    'node',
    'manage_emails',
    'ffmpeg',
    'manage-permissions',
    'store-settings',
    'manage-products',
    'manage-orders',
    'manage-reviews',
    'website_mode',
    'user_reports',
    'edit-forum',
    'edit-section',
    'cronjob_settings',
    'system_status',
    "upload-to-storage",
    "ai-settings",
    "manage-content-monetization",
);
$wo['mod_pages'] = array('dashboard', 'post-settings', 'manage-stickers', 'manage-gifts', 'manage-users', 'online-users', 'manage-stories', 'manage-pages', 'manage-groups', 'manage-posts', 'manage-articles', 'manage-events', 'manage-forum-threads', 'manage-forum-messages', 'manage-movies', 'manage-games', 'add-new-game', 'manage-user-ads', 'manage-reports', 'manage-third-psites', 'edit-movie','bank-receipts','job-categories','manage-jobs');


if (!empty($_GET['page'])) {
    $page = Wo_Secure($_GET['page'], 0);
}
$wo['decode_android_v']  = $wo['config']['footer_background'];
$wo['decode_android_value']  = base64_decode('I2FhYQ==');

$wo['decode_android_n_v']  = $wo['config']['footer_background_n'];
$wo['decode_android_n_value']  = base64_decode('I2FhYQ==');

$wo['decode_ios_v']  = $wo['config']['footer_background_2'];
$wo['decode_ios_value']  = base64_decode('I2FhYQ==');

$wo['decode_windwos_v']  = $wo['config']['footer_text_color'];
$wo['decode_windwos_value']  = base64_decode('I2RkZA==');
if ($is_moderoter && !empty($wo['user']['permission'])) {
    $wo['user']['permission'] = json_decode($wo['user']['permission'],true);

    if (!in_array($page, array_keys($wo['user']['permission']))) {
        $wo['user']['permission'][$page] = 0;
        $permission = json_encode($wo['user']['permission']);
        $db->where('user_id',$wo['user']['user_id'])->update(T_USERS,array('permission' => $permission));
        
            cache($wo['user']['id'], 'users', 'delete');
        header("Location: " . Wo_LoadAdminLinkSettings($page));
        exit();
    }
    else{
        if ($wo['user']['permission'][$page] == 0) {
            foreach ($wo['user']['permission'] as $key => $value) {
                if ($value == 1) {
                    header("Location: " . Wo_LoadAdminLinkSettings($key));
                    exit();
                }
            }
        }
    }
}
elseif ($is_moderoter && empty($wo['user']['permission'])) {
    $permission = array();
    if (!empty($wo['all_pages'])) {
        foreach ($wo['all_pages']  as $key => $value) {
            if (in_array($value,$wo['mod_pages'])) {
                $permission[$value] = 1;
            }
            else{
                $permission[$value] = 0;
            }
        }
    }
    $permission = json_encode($permission);
    $db->where('user_id',$wo['user']['user_id'])->update(T_USERS,array('permission' => $permission));
    cache($wo['user']['id'], 'users', 'delete');
    $wo['user'] = Wo_UserData($wo['user']['user_id']);
}

// if ($is_moderoter == true && $is_admin == false) {
//     if (!in_array($page, $wo['mod_pages'])) {
//         header("Location: " . Wo_SeoLink('index.php?link1=admin-cp'));
//         exit();
//     }
// }
if (in_array($page, $pages)) {
   $page_loaded = Wo_LoadAdminPage("$page/content");
}
if (empty($page_loaded)) {
    header("Location: " . Wo_SeoLink('index.php?link1=admin-cp'));
    exit();
}

$notify_count = $db->where('recipient_id',0)->where('admin',1)->where('seen',0)->getValue(T_NOTIFICATION,'COUNT(*)');
$notifications = $db->where('recipient_id',0)->where('admin',1)->where('seen',0)->orderBy('id','DESC')->get(T_NOTIFICATION);
$old_notifications = $db->where('recipient_id',0)->where('admin',1)->where('seen',0,'!=')->orderBy('id','DESC')->get(T_NOTIFICATION,5);
$mode = 'day';
if (!empty($_COOKIE['mode']) && $_COOKIE['mode'] == 'night') {
    $mode = 'night';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Panel | <?php echo $wo['config']['siteTitle']; ?></title>
    <link rel="icon" href="<?php echo $wo['config']['theme_url']; ?>/img/icon.png" type="image/png">

    <link rel="stylesheet" href="<?php echo $wo['config']['theme_url'];?>/stylesheet/bootsrap-rtl.min.css<?php echo $wo['update_cache']; ?>?version=<?php echo $wo['config']['version']; ?>">


    <!-- Main css -->
    <link rel="stylesheet" href="<?php echo(Wo_LoadAdminLink('vendors/bundle.css')) ?>" type="text/css">

    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Daterangepicker -->
    <link rel="stylesheet" href="<?php echo(Wo_LoadAdminLink('vendors/datepicker/daterangepicker.css')) ?>" type="text/css">

    <!-- DataTable -->
    <link rel="stylesheet" href="<?php echo(Wo_LoadAdminLink('vendors/dataTable/datatables.min.css')) ?>" type="text/css">

<!-- App css -->
    <link rel="stylesheet" href="<?php echo(Wo_LoadAdminLink('assets/css/app.css')) ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo(Wo_LoadAdminLink('assets/css/rtl.css')) ?>" type="text/css">
    <!-- Main scripts -->
<script src="<?php echo(Wo_LoadAdminLink('vendors/bundle.js')) ?>"></script>

    <!-- Apex chart -->
    <script src="<?php echo(Wo_LoadAdminLink('vendors/charts/apex/apexcharts.min.js')) ?>"></script>

    <!-- Daterangepicker -->
    <script src="<?php echo(Wo_LoadAdminLink('vendors/datepicker/daterangepicker.js')) ?>"></script>

    <!-- DataTable -->
    <script src="<?php echo(Wo_LoadAdminLink('vendors/dataTable/datatables.min.js')) ?>"></script>

    <!-- Dashboard scripts -->
    <script src="<?php echo(Wo_LoadAdminLink('assets/js/examples/pages/dashboard.js')) ?>"></script>
    <script src="<?php echo Wo_LoadAdminLink('vendors/charts/chartjs/chart.min.js'); ?>"></script>

<!-- App scripts -->

<link href="<?php echo Wo_LoadAdminLink('vendors/sweetalert/sweetalert.css'); ?>" rel="stylesheet" />
<script src="<?php echo Wo_LoadAdminLink('assets/js/admin.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo(Wo_LoadAdminLink('vendors/select2/css/select2.min.css')) ?>" type="text/css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
<?php //if ($page == 'create-article' || $page == 'edit-article' || $page == 'manage-announcements' || $page == 'newsletters') { ?>
<script src="<?php echo Wo_LoadAdminLink('vendors/tinymce/js/tinymce/tinymce.min.js'); ?>"></script>
<script src="<?php echo Wo_LoadAdminLink('vendors/bootstrap-tagsinput/src/bootstrap-tagsinput.js'); ?>"></script>
<link href="<?php echo Wo_LoadAdminLink('vendors/bootstrap-tagsinput/src/bootstrap-tagsinput.css'); ?>" rel="stylesheet" />
<?php //} ?>
<?php //if ($page == 'custom-code') { ?>
<script src="<?php echo Wo_LoadAdminLink('vendors/codemirror-5.30.0/lib/codemirror.js'); ?>"></script>
<script src="<?php echo Wo_LoadAdminLink('vendors/codemirror-5.30.0/mode/css/css.js'); ?>"></script>
<script src="<?php echo Wo_LoadAdminLink('vendors/codemirror-5.30.0/mode/javascript/javascript.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo Wo_LoadAdminLink('vendors/codemirror-5.30.0/lib/codemirror.css'); ?>">
<?php //} ?>


    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php if ($page == 'bank-receipts' || $page == 'manage-verification-reqeusts' || $page == 'monetization-requests' || $page == 'manage-user-ads') { ?>
        <!-- Css -->
        <link rel="stylesheet" href="<?php echo(Wo_LoadAdminLink('vendors/lightbox/magnific-popup.css')) ?>" type="text/css">

        <!-- Javascript -->
        <script src="<?php echo(Wo_LoadAdminLink('vendors/lightbox/jquery.magnific-popup.min.js')) ?>"></script>
        <script src="<?php echo(Wo_LoadAdminLink('vendors/charts/justgage/raphael-2.1.4.min.js')) ?>"></script>
        <script src="<?php echo(Wo_LoadAdminLink('vendors/charts/justgage/justgage.js')) ?>"></script>
    <?php } ?>
    <script src="<?php echo Wo_LoadAdminLink('assets/js/jquery.form.min.js'); ?>"></script>
    <script>
        function Wo_Ajax_Requests_File(){
            return "<?php echo $wo['config']['site_url'].'/requests.php';?>"
        }
        function Wo_Ajax_Requests_File_load(){
            return "<?php echo $wo['config']['site_url'].'/admin_load.php';?>"
        }
    </script>
    <style>
        body {background-color: #222;}
        .btn.btn-primary, a.btn[href="#next"], a.btn[href="#previous"] {color: #fff !important;background: #C32E3A;border-color: #C32E3A;}
        .btn.btn-primary:not(:disabled):not(.disabled):hover, a.btn[href="#next"]:not(:disabled):not(.disabled):hover, a.btn[href="#previous"]:not(:disabled):not(.disabled):hover, .btn.btn-primary:not(:disabled):not(.disabled):focus, a.btn[href="#next"]:not(:disabled):not(.disabled):focus, a.btn[href="#previous"]:not(:disabled):not(.disabled):focus, .btn.btn-primary:not(:disabled):not(.disabled):active, a.btn[href="#next"]:not(:disabled):not(.disabled):active, a.btn[href="#previous"]:not(:disabled):not(.disabled):active, .btn.btn-primary:not(:disabled):not(.disabled).active, a.btn[href="#next"]:not(:disabled):not(.disabled).active, a.btn[href="#previous"]:not(:disabled):not(.disabled).active {background: #CE3643;border-color: #CE3643;}
        body.dark .navigation .navigation-menu-body ul li a.active, .breadcrumb .breadcrumb-item.active, body.dark .breadcrumb li.breadcrumb-item.active, body.dark .navigation .navigation-menu-body ul li a.active .nav-link-icon {color: #C32E3A !important;}
        .card form .form-check-inline input:checked {background-color: #C32E3A;}
        .card form .form-check-inline input:checked + label::before, .card form .form-check-inline input:active + label::before {border-color: #C32E3A;}
        .card form .form-check-inline label::after {background-color: #C32E3A;}
        .select2-container--default.select2-container--focus .select2-selection--multiple {border: 2px solid #C32E3A !important;}
    </style>
</head>
<script type="text/javascript">

    $(function() {

        $(document).on('click', 'a[data-ajax]', function(e) {
            $(document).off('click', '.ranges ul li');
            $(document).off('click', '.applyBtn');
            e.preventDefault();
            if (($(this)[0].hasAttribute("data-sent") && $(this).attr('data-sent') == '0') || !$(this)[0].hasAttribute("data-sent")) {
                if (!$(this)[0].hasAttribute("data-sent") && !$(this).hasClass('waves-effect')) {
                    $('.navigation-menu-body').find('a').removeClass('active');
                    $(this).addClass('active');
                }
                window.history.pushState({state:'new'},'', $(this).attr('href'));
                $(".barloading").css("display","block");
                if ($(this)[0].hasAttribute("data-sent")) {
                    $(this).attr('data-sent', "1");
                }
                var url = $(this).attr('data-ajax');
                $.post(Wo_Ajax_Requests_File_load() + url, {url:url}, function (data) {
                    $(".barloading").css("display","none");
                    if ($('#redirect_link')[0].hasAttribute("data-sent")) {
                        $('#redirect_link').attr('data-sent', "0");
                    }
                    json_data = JSON.parse($(data).filter('#json-data').val());
                    $('.content').html(data);
                    setTimeout(function () {
                      $(".content").getNiceScroll().resize()
                    }, 500);
                    $(".content").animate({ scrollTop: 0 }, "slow");
                    showEncryptedAlert();
                });
            }
        });
        $(window).on("popstate", function (e) {
            location.reload();
        });
    });
</script>
<body <?php echo ($mode == 'night' ? 'class="dark"' : ''); ?> class="rtl">
    <div class="barloading"></div>
    <a id="redirect_link" href="" data-ajax="" data-sent="0"></a>
    <input type="hidden" class="main_session" value="<?php echo Wo_CreateMainSession();?>">
    <div class="colors"> <!-- To use theme colors with Javascript -->
        <div class="bg-primary"></div>
        <div class="bg-primary-bright"></div>
        <div class="bg-secondary"></div>
        <div class="bg-secondary-bright"></div>
        <div class="bg-info"></div>
        <div class="bg-info-bright"></div>
        <div class="bg-success"></div>
        <div class="bg-success-bright"></div>
        <div class="bg-danger"></div>
        <div class="bg-danger-bright"></div>
        <div class="bg-warning"></div>
        <div class="bg-warning-bright"></div>
    </div>
<!-- Preloader -->
<div class="preloader">
    <div class="preloader-icon"></div>
    <span>Loading...</span>
</div>
<!-- ./ Preloader -->

<!-- Sidebar group -->
<div class="sidebar-group">

</div>
<!-- ./ Sidebar group -->

<!-- Layout wrapper -->
<div class="layout-wrapper">

    <!-- Header -->
    <div class="header d-print-none">
        <div class="header-container">
            <div class="header-left">
                <div class="navigation-toggler">
                    <a href="#" data-action="navigation-toggler">
                        <i data-feather="menu"></i>
                    </a>
                </div>

                <div class="header-logo">
                    <a href="<?php echo $wo['config']['site_url'] ?>">
                        <img class="logo" src="<?php echo $wo['config']['theme_url'];?>/img/<?php echo($wo['config']['theme'] == 'sunshine' ? 'night-' : '') ?>logo.<?php echo $wo['config']['logo_extension'];?>" alt="logo">
                    </a>
                </div>
            </div>

            <div class="header-body">
                <div class="header-body-left">
                    <ul class="navbar-nav">
                        <li class="nav-item mr-3">
                            <div class="header-search-form">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button class="btn">
                                            <i data-feather="search"></i>
                                        </button>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Search"  onkeyup="searchInFiles($(this).val())">
                                    <div class="pt_admin_hdr_srch_reslts" id="search_for_bar"></div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="header-body-right">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link <?php if ($notify_count > 0) { ?> nav-link-notify<?php } ?>" title="Notifications" data-toggle="dropdown">
                                <i data-feather="bell"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-big">
                                <div
                                    class="border-bottom px-4 py-3 text-center d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Notifications</h5>
                                    <?php if ($notify_count > 0) { ?>
                                    <small class="opacity-7"><?php echo $notify_count; ?>   unread notifications</small>
                                    <?php } ?>
                                </div>
                                <div class="dropdown-scroll">
                                    <ul class="list-group list-group-flush">
                                        <?php if ($notify_count > 0) { ?>
                                            <li class="px-4 py-2 text-center small text-muted bg-light">Unread Notifications</li>
                                            <?php if (!empty($notifications)) {
                                                    foreach ($notifications as $key => $notify) {
                                                        $page_ = '';
                                                        $text = '';
                                                        if ($notify->type == 'bank') {
                                                            $page_ = 'bank-receipts';
                                                            $text = 'You have a new bank payment awaiting your approval';
                                                        }
                                                        elseif ($notify->type == 'verify') {
                                                            $page_ = 'manage-verification-reqeusts';
                                                            $text = 'You have a new verification requests awaiting your approval';
                                                        }
                                                        elseif ($notify->type == 'refund') {
                                                            $page_ = 'pro-refund';
                                                            $text = 'You have a new refund requests awaiting your approval';
                                                        }
                                                        elseif ($notify->type == 'with') {
                                                            $page_ = 'payment-reqeuests';
                                                            $text = 'You have a new withdrawal requests awaiting your approval';
                                                        }
                                                        elseif ($notify->type == 'report') {
                                                            $page_ = 'manage-reports';
                                                            $text = 'You have a new reports awaiting your approval';
                                                        }
                                                        elseif ($notify->type == 'user_reports') {
                                                            $page_ = 'user_reports';
                                                            $text = 'You have a new reports awaiting your approval';
                                                        }
                                                ?>
                                            <li class="px-4 py-3 list-group-item">
                                                <a href="<?php echo Wo_LoadAdminLinkSettings($page_); ?>" class="d-flex align-items-center hide-show-toggler">
                                                    <div class="flex-shrink-0">
                                                        <figure class="avatar mr-3">
                                                            <span
                                                                class="avatar-title bg-info-bright text-info rounded-circle">
                                                                <?php if ($notify->type == 'bank') { ?>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-credit-card"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                                                <?php }elseif ($notify->type == 'verify') { ?>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#2196f3" d="M12 2C6.5 2 2 6.5 2 12S6.5 22 12 22 22 17.5 22 12 17.5 2 12 2M10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z"></path></svg>
                                                                <?php }elseif ($notify->type == 'refund') { ?>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                                                                <?php }elseif ($notify->type == 'with') { ?>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                                                <?php }elseif ($notify->type == 'report' || $notify->type == 'user_reports') { ?>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-flag"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                                                                <?php } ?>

                                                            </span>
                                                        </figure>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <p class="mb-0 line-height-20 d-flex justify-content-between">
                                                            <?php echo $text; ?>
                                                        </p>
                                                        <span class="text-muted small"><?php echo Wo_Time_Elapsed_String($notify->time); ?></span>
                                                    </div>
                                                </a>
                                            </li>
                                            <?php } } ?>
                                        <?php } ?>
                                        <?php if ($notify_count == 0 && !empty($old_notifications)) { ?>
                                            <li class="px-4 py-2 text-center small text-muted bg-light">Old Notifications</li>
                                            <?php
                                                    foreach ($old_notifications as $key => $notify) {
                                                        $page_ = '';
                                                        $text = '';
                                                        if ($notify->type == 'bank') {
                                                            $page_ = 'bank-receipts';
                                                            $text = 'You have a new bank payment awaiting your approval';
                                                        }
                                                        elseif ($notify->type == 'verify') {
                                                            $page_ = 'verification-requests';
                                                            $text = 'You have a new verification requests awaiting your approval';
                                                        }
                                                        elseif ($notify->type == 'refund') {
                                                            $page_ = 'pro-refund';
                                                            $text = 'You have a new refund requests awaiting your approval';
                                                        }
                                                        elseif ($notify->type == 'with') {
                                                            $page_ = 'payment-reqeuests';
                                                            $text = 'You have a new withdrawal requests awaiting your approval';
                                                        }
                                                        elseif ($notify->type == 'report') {
                                                            $page_ = 'manage-reports';
                                                            $text = 'You have a new reports awaiting your approval';
                                                        }
                                                        elseif ($notify->type == 'user_reports') {
                                                            $page_ = 'user_reports';
                                                            $text = 'You have a new reports awaiting your approval';
                                                        }
                                                ?>
                                            <li class="px-4 py-3 list-group-item">
                                                <a href="<?php echo Wo_LoadAdminLinkSettings($page_); ?>" class="d-flex align-items-center hide-show-toggler">
                                                    <div class="flex-shrink-0">
                                                        <figure class="avatar mr-3">
                                                            <span class="avatar-title bg-secondary-bright text-secondary rounded-circle">
                                                                <?php if ($notify->type == 'bank') { ?>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-credit-card"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                                                <?php }elseif ($notify->type == 'verify') { ?>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#2196f3" d="M12 2C6.5 2 2 6.5 2 12S6.5 22 12 22 22 17.5 22 12 17.5 2 12 2M10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z"></path></svg>
                                                                <?php }elseif ($notify->type == 'refund') { ?>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                                                                <?php }elseif ($notify->type == 'with') { ?>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                                                <?php }elseif ($notify->type == 'report' || $notify->type == 'user_reports') { ?>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-flag"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                                                                <?php } ?>
                                                            </span>
                                                        </figure>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <p class="mb-0 line-height-20 d-flex justify-content-between">
                                                            <?php echo $text; ?>
                                                        </p>
                                                        <span class="text-muted small"><?php echo Wo_Time_Elapsed_String($notify->time); ?></span>
                                                    </div>
                                                </a>
                                            </li>
                                        <?php } } ?>
                                    </ul>
                                </div>
                                <?php if ($notify_count > 0) { ?>
                                <div class="px-4 py-3 text-right border-top">
                                    <ul class="list-inline small">
                                        <li class="list-inline-item mb-0">
                                            <a href="javascript:void(0)" onclick="ReadNotify()">Mark All Read</a>
                                        </li>
                                    </ul>
                                </div>
                                <?php } ?>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" title="User menu" data-toggle="dropdown">
                                <figure class="avatar avatar-sm">
                                    <img src="<?php echo $wo['user']['avatar']; ?>"
                                         class="rounded-circle"
                                         alt="avatar">
                                </figure>
                                <span class="ml-2 d-sm-inline d-none"><?php echo $wo['user']['name']; ?></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-big">
                                <div class="text-center py-4">
                                    <figure class="avatar avatar-lg mb-3 border-0">
                                        <img src="<?php echo $wo['user']['avatar']; ?>"
                                             class="rounded-circle" alt="image">
                                    </figure>
                                    <h5 class="text-center"><?php echo $wo['user']['name']; ?></h5>
                                    <div class="mb-3 small text-center text-muted"><?php echo $wo['user']['email']; ?></div>
                                    <a href="<?php echo $wo['user']['url']; ?>" class="btn btn-outline-light btn-rounded">View Profile</a>
                                </div>
                                


                                
                                <div class="list-group">
                                <a href="" class="list-group-item ">arabic</a>
                                
         
                                    <a href="<?php echo(Wo_Link('logout')) ?>" class="list-group-item text-danger">Sign Out!</a>
                                    <?php if ($mode == 'night') { ?>
                                        <a href="javascript:void(0)" class="list-group-item admin_mode" onclick="ChangeMode('day')">
                                            <span id="night-mode-text">Day mode</span>
                                            <svg class="feather feather-moon float-right" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                                        </a>
                                    <?php }else{ ?>
                                        <!-- <a href="javascript:void(0)" class="list-group-item admin_mode" onclick="ChangeMode('night')">
                                            <span id="night-mode-text">Night mode</span>
                                            <svg class="feather feather-moon float-right" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                                        </a> -->
                                    <?php } ?>

                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item header-toggler">
                    <a href="#" class="nav-link">
                        <i data-feather="arrow-down"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <!-- ./ Header -->

    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- begin::navigation -->
        <div class="navigation">
            <div class="navigation-header">
                <span>Navigation</span>
                <a href="#">
                    <i class="ti-close"></i>
                </a>
            </div>
            <div class="navigation-menu-body">
                <ul>
                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['dashboard'] == 1)) { ?>
                    <li>
                        <a <?php echo ($page == 'dashboard') ? 'class="active"' : ''; ?>  href="<?php echo Wo_LoadAdminLinkSettings(''); ?>" data-ajax="?path=dashboard">
                            <span class="nav-link-icon">
                                <i class="material-icons">dashboard</i>
                            </span>
                            <span>لوحه التحكم</span>
                        </a>
                    </li>
                    <?php } ?>

                    <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['post-settings'] == 1 || $wo['user']['permission']['manage-colored-posts'] == 1 || $wo['user']['permission']['manage-reactions'] == 1 || $wo['user']['permission']['live'] == 1 || $wo['user']['permission']['general-settings'] == 1 || $wo['user']['permission']['site-settings'] == 1 || $wo['user']['permission']['amazon-settings'] == 1 || $wo['user']['permission']['email-settings'] == 1 || $wo['user']['permission']['video-settings'] == 1 || $wo['user']['permission']['social-login'] == 1 || $wo['user']['permission']['node'] == 1 || $wo['user']['permission']['cronjob_settings'] == 1))) { ?>
                    <li <?php echo ($page == 'general-settings' || $page == 'post-settings' || $page == 'site-settings' || $page == 'email-settings' || $page == 'social-login' || $page == 'site-features' || $page == 'amazon-settings' ||  $page == 'video-settings' || $page == 'manage-currencies' || $page == 'manage-colored-posts' || $page == 'live' || $page == 'node' || $page == 'manage-reactions' || $page == 'ffmpeg' || $page == 'cronjob_settings') ? 'class="open"' : ''; ?>>
                        <a href="#">
                            <span class="nav-link-icon">
                                <i class="material-icons">settings</i>
                            </span>
                            <span>الاعدادات</span>
                        </a>
                        <ul class="ml-menu">
    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['website_mode'] == 1)) { ?>
    <li>
        <a <?php echo ($page == 'website_mode') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('website_mode'); ?>" data-ajax="?path=website_mode">وضع الموقع</a>
    </li>
    <?php } ?>
    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['general-settings'] == 1)) { ?>
    <li>
        <a <?php echo ($page == 'general-settings') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('general-settings'); ?>" data-ajax="?path=general-settings">التكوين العام</a>
    </li>
    <?php } ?>
    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['site-settings'] == 1)) { ?>
    <li>
        <a <?php echo ($page == 'site-settings') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('site-settings'); ?>" data-ajax="?path=site-settings">معلومات الموقع</a>
    </li>
    <?php } ?>
    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['amazon-settings'] == 1)) { ?>
    <li>
        <a <?php echo ($page == 'amazon-settings') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('amazon-settings'); ?>" data-ajax="?path=amazon-settings">تكوين تحميل الملفات</a>
    </li>
    <?php } ?>
    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['email-settings'] == 1)) { ?>
    <li>
        <a <?php echo ($page == 'email-settings') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('email-settings'); ?>" data-ajax="?path=email-settings">إعداد البريد الإلكتروني والرسائل القصيرة</a>
    </li>
    <?php } ?>
    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['video-settings'] == 1)) { ?>
    <li>
        <a <?php echo ($page == 'video-settings') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('video-settings'); ?>" data-ajax="?path=video-settings">الدردشة والفيديو/الصوت</a>
    </li>
    <?php } ?>
    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['social-login'] == 1)) { ?>
    <li>
        <a <?php echo ($page == 'social-login') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('social-login'); ?>" data-ajax="?path=social-login">إعدادات تسجيل الدخول الاجتماعي</a>
    </li>
    <?php } ?>
    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['node'] == 1)) { ?>
    <li>
        <a <?php echo ($page == 'node') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('node'); ?>" data-ajax="?path=node">إعدادات NodeJS</a>
    </li>
    <?php } ?>
    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['cronjob_settings'] == 1)) { ?>
    <li>
        <a <?php echo ($page == 'cronjob_settings') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('cronjob_settings'); ?>" data-ajax="?path=cronjob_settings">إعدادات CronJob</a>
    </li>
    <?php } ?>
    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['ai-settings'] == 1)) { ?>
    <li>
        <a <?php echo ($page == 'ai-settings') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('ai-settings'); ?>" data-ajax="?path=ai-settings">إعدادات الذكاء الاصطناعي</a>
    </li>
    <?php } ?>

    <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['post-settings'] == 1 || $wo['user']['permission']['manage-colored-posts'] == 1 || $wo['user']['permission']['manage-reactions'] == 1 || $wo['user']['permission']['live'] == 1))) { ?>
    <li>
        <a <?php echo ($page == 'post-settings' || $page == 'manage-colored-posts' || $page == 'manage-reactions') ? 'class="open"' : ''; ?> href="javascript:void(0);">إعدادات المنشورات</a>
        <ul class="ml-menu">
            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['post-settings'] == 1)) { ?>
            <li>
                <a <?php echo ($page == 'post-settings') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('post-settings'); ?>" data-ajax="?path=post-settings">
                    <span>إعدادات المنشورات</span>
                </a>
            </li>
            <?php } ?>
            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-colored-posts'] == 1)) { ?>
            <li>
                <a <?php echo ($page == 'manage-colored-posts') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-colored-posts'); ?>" data-ajax="?path=manage-colored-posts">
                    <span>إدارة المنشورات الملونة</span>
                </a>
            </li>
            <?php } ?>
            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-reactions'] == 1)) { ?>
            <li>
                <a <?php echo ($page == 'manage-reactions') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-reactions'); ?>" data-ajax="?path=manage-reactions">
                    <span>تفاعلات المنشور</span>
                </a>
            </li>
            <?php } ?>
            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['live'] == 1)) { ?>
            <li>
                <a <?php echo ($page == 'live') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('live'); ?>" data-ajax="?path=live">إعداد بث مباشر</a>
            </li>
            <?php } ?>
        </ul>
    </li>
    <?php } ?>
</ul>

                    </li>
                    <?php } ?>

                    <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['manage-apps'] == 1 || $wo['user']['permission']['manage-pages'] == 1 || $wo['user']['permission']['manage-stickers'] == 1 || $wo['user']['permission']['add-new-sticker'] == 1 || $wo['user']['permission']['manage-gifts'] == 1 || $wo['user']['permission']['add-new-gift'] == 1 || $wo['user']['permission']['manage-groups'] == 1 || $wo['user']['permission']['manage-posts'] == 1 || $wo['user']['permission']['manage-articles'] == 1 || $wo['user']['permission']['manage-events'] == 1 || $wo['user']['permission']['manage-forum-sections'] == 1 || $wo['user']['permission']['manage-forum-forums'] == 1 || $wo['user']['permission']['manage-forum-threads'] == 1 || $wo['user']['permission']['manage-forum-messages'] == 1 || $wo['user']['permission']['create-new-forum'] == 1 || $wo['user']['permission']['create-new-section'] == 1 || $wo['user']['permission']['manage-movies'] == 1 || $wo['user']['permission']['add-new-movies'] == 1 || $wo['user']['permission']['manage-games'] == 1 || $wo['user']['permission']['add-new-game'] == 1 || $wo['user']['permission']['edit-movie'] == 1 || $wo['user']['permission']['pages-categories'] == 1 || $wo['user']['permission']['pages-sub-categories'] == 1 || $wo['user']['permission']['groups-sub-categories'] == 1 || $wo['user']['permission']['products-sub-categories'] == 1 || $wo['user']['permission']['groups-categories'] == 1 || $wo['user']['permission']['blogs-categories'] == 1 || $wo['user']['permission']['products-categories'] == 1 || $wo['user']['permission']['manage-fund'] == 1 || $wo['user']['permission']['manage-jobs'] == 1 || $wo['user']['permission']['manage-offers'] == 1 || $wo['user']['permission']['pages-fields'] == 1 || $wo['user']['permission']['groups-fields'] == 1 || $wo['user']['permission']['products-fields'] == 1 ))) { ?>

                     <li <?php echo ($page == 'manage-apps' || $page == 'manage-pages' || $page == 'manage-stickers' || $page == 'add-new-sticker' || $page == 'manage-gifts' || $page == 'add-new-gift' || $page == 'manage-groups' || $page == 'manage-posts' || $page == 'manage-articles' || $page == 'manage-events'||  $page == 'manage-forum-sections' || $page == 'manage-forum-forums' || $page == 'manage-forum-threads' || $page == 'manage-forum-messages' || $page == 'create-new-forum' || $page == 'create-new-section' || $page == 'manage-movies' || $page == 'add-new-movies' || $page == 'manage-games' || $page == 'add-new-game' || $page == 'edit-movie' || $page == 'pages-categories' || $page == 'pages-sub-categories' || $page == 'groups-sub-categories' || $page == 'products-sub-categories' || $page == 'groups-categories' || $page == 'blogs-categories' || $page == 'products-categories' || $page == 'manage-fund' || $page == 'manage-jobs' || $page == 'manage-offers' || $page == 'pages-fields' || $page == 'groups-fields' || $page == 'products-fields') ? 'class="open"' : ''; ?>>
                        <a href="#">
                            <span class="nav-link-icon">
                                <i class="material-icons">view_agenda</i>
                            </span>
                            <span>إدارة المميزات</span>
                        </a>

                        <ul class="ml-menu">
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['site-features'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'site-features') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('site-features'); ?>" data-ajax="?path=site-features">شغل خاصية التوقف</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-apps'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-apps') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-apps'); ?>" data-ajax="?path=manage-apps">التطبيقات</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-pages'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-pages') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-pages'); ?>" data-ajax="?path=manage-pages">الصفحات</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-groups'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-groups') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-groups'); ?>" data-ajax="?path=manage-groups">مجموعات</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-posts'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-posts') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-posts'); ?>" data-ajax="?path=manage-posts">دعامات</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-fund'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-fund') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-fund'); ?>" data-ajax="?path=manage-fund">تمويل</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-jobs'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-jobs') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-jobs'); ?>" data-ajax="?path=manage-jobs">وظائف</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-offers'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-offers') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-offers'); ?>" data-ajax="?path=manage-offers">عروض</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-articles'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-articles') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-articles'); ?>" data-ajax="?path=manage-articles">مقالات (مدونة)</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-events'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-events') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-events'); ?>" data-ajax="?path=manage-events">الأحداث</a>
                            </li>
                            <?php } ?>
                                <li>
                                    <a <?php echo ($page == 'manage-events') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-content-monetization'); ?>" data-ajax="?path=manage-content-monetization">تسييل المحتوى</a>
                                </li>


                            <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['store-settings'] == 1 || $wo['user']['permission']['manage-products'] == 1 || $wo['user']['permission']['manage-orders'] == 1 || $wo['user']['permission']['manage-reviews'] == 1))) { ?>
                            <li <?php echo ($page == 'store-settings' || $page == 'manage-products' || $page == 'manage-orders' || $page == 'manage-reviews') ? 'class="open"' : ''; ?>>
                                <a href="javascript:void(0);">المخزن</a>
                                <ul class="ml-menu">
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['store-settings'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'store-settings') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('store-settings'); ?>" data-ajax="?path=store-settings">
                                            <span>إعدادات المتجر</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-products'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'manage-products') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-products'); ?>" data-ajax="?path=manage-products">
                                            <span>إدارة المنتجات</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-orders'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'manage-orders') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-orders'); ?>" data-ajax="?path=manage-orders">
                                            <span>إدارة الطلبات</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-reviews'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'manage-reviews') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-reviews'); ?>" data-ajax="?path=manage-reviews">
                                            <span>إدارة المراجعات</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php } ?>









                            <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['manage-forum-sections'] == 1 || $wo['user']['permission']['manage-forum-forums'] == 1 || $wo['user']['permission']['manage-forum-threads'] == 1 || $wo['user']['permission']['manage-forum-messages'] == 1 || $wo['user']['permission']['create-new-forum'] == 1 || $wo['user']['permission']['create-new-section'] == 1 ))) { ?>
                            <li <?php echo ($page == 'manage-forum-sections' || $page == 'manage-forum-forums' || $page == 'manage-forum-threads' || $page == 'manage-forum-messages' || $page == 'create-new-forum' || $page == 'create-new-section') ? 'class="open"' : ''; ?>>
                                <a href="javascript:void(0);">المنتديات</a>
                                <ul class="ml-menu">
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-forum-sections'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'manage-forum-sections') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-forum-sections'); ?>" data-ajax="?path=manage-forum-sections">
                                            <span>إدارة أقسام المنتديات</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-forum-forums'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'manage-forum-forums') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-forum-forums'); ?>" data-ajax="?path=manage-forum-forums">
                                            <span>إدارة المنتديات</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-forum-threads'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'manage-forum-threads') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-forum-threads'); ?>" data-ajax="?path=manage-forum-threads">
                                            <span>إدارة المواضيع</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-forum-messages'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'manage-forum-messages') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-forum-messages'); ?>" data-ajax="?path=manage-forum-messages">
                                            <span>إدارة الردود</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['create-new-section'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'create-new-section') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('create-new-section'); ?>" data-ajax="?path=create-new-section">
                                            <span>إنشاء قسم جديد</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['create-new-forum'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'create-new-forum') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('create-new-forum'); ?>" data-ajax="?path=create-new-forum">
                                            <span>إنشاء منتدى جديد</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['manage-movies'] == 1 || $wo['user']['permission']['add-new-movies'] == 1 ))) { ?>
                            <li <?php echo ($page == 'manage-movies' || $page == 'add-new-movies') ? 'class="open"' : ''; ?>>
                                <a href="javascript:void(0);">أفلام</a>
                                <ul class="ml-menu">
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-movies'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'manage-movies') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-movies'); ?>" data-ajax="?path=manage-movies">
                                            <span>إدارة الأفلام</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['add-new-movies'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'add-new-movies') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('add-new-movies'); ?>" data-ajax="?path=add-new-movies">
                                            <span>أضف فيلمًا جديدًا</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['manage-games'] == 1 || $wo['user']['permission']['add-new-game'] == 1 ))) { ?>

                            <li <?php echo ($page == 'manage-games' || $page == 'add-new-game') ? 'class="open"' : ''; ?>>
                                <a href="javascript:void(0);">Games</a>
                                <ul class="ml-menu">
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-games'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'manage-games') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-games'); ?>" data-ajax="?path=manage-games">
                                            <span>إدارة الألعاب</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['add-new-game'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'add-new-game') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('add-new-game'); ?>" data-ajax="?path=add-new-game">
                                            <span>أضف لعبة جديدة</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['pages-categories'] == 1 || $wo['user']['permission']['pages-sub-categories'] == 1 || $wo['user']['permission']['groups-sub-categories'] == 1 || $wo['user']['permission']['products-sub-categories'] == 1 || $wo['user']['permission']['groups-categories'] == 1 || $wo['user']['permission']['blogs-categories'] == 1 || $wo['user']['permission']['products-categories'] == 1 ))) { ?>
                            <li <?php echo ($page == 'pages-categories' || $page == 'pages-sub-categories' || $page == 'groups-sub-categories' || $page == 'products-sub-categories' || $page == 'groups-categories' || $page == 'blogs-categories' || $page == 'products-categories') ? 'class="open"' : ''; ?>>
                                <a href="javascript:void(0);">فئات</a>
                                <ul class="ml-menu">
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['pages-categories'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'pages-categories') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('pages-categories'); ?>" data-ajax="?path=pages-categories">
                                            <span>فئات الصفحات</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['pages-sub-categories'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'pages-sub-categories') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('pages-sub-categories'); ?>" data-ajax="?path=pages-sub-categories">
                                            <span>صفحات الفئات الفرعية</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['groups-categories'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'groups-categories') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('groups-categories'); ?>" data-ajax="?path=groups-categories">
                                            <span>مجموعات فئات</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['groups-sub-categories'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'groups-sub-categories') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('groups-sub-categories'); ?>" data-ajax="?path=groups-sub-categories">
                                            <span>مجموعات الفئات الفرعية</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['blogs-categories'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'blogs-categories') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('blogs-categories'); ?>" data-ajax="?path=blogs-categories">
                                            <span>فئات المدونات</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['products-categories'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'products-categories') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('products-categories'); ?>" data-ajax="?path=products-categories">
                                            <span>فئات المنتجات</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['products-sub-categories'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'products-sub-categories') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('products-sub-categories'); ?>" data-ajax="?path=products-sub-categories">
                                            <span>المنتجات الفئات الفرعية</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['job-categories'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'job-categories') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('job-categories'); ?>" data-ajax="?path=job-categories">
                                            <span>فئات الوظائف</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['add-new-gift'] == 1 || $wo['user']['permission']['manage-gifts'] == 1 ))) { ?>
                            <?php if ($wo['config']['gift_system'] == 1){?>
                            <li <?php echo ($page == 'manage-gifts' || $page == 'add-new-gift') ? 'class="open"' : ''; ?>>
                                <a href="javascript:void(0);">الهدايا</a>
                                <ul class="ml-menu">
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-gifts'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'manage-gifts') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-gifts'); ?>" data-ajax="?path=manage-gifts">
                                            <span>إدارة الهدايا</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['add-new-gift'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'add-new-gift') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('add-new-gift'); ?>" data-ajax="?path=add-new-gift">
                                            <span>أضف هدية جديدة</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php } ?>
                            <?php } ?>

                            <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['manage-stickers'] == 1 || $wo['user']['permission']['add-new-sticker'] == 1 ))) { ?>
                            <?php if ($wo['config']['stickers_system'] == 1){?>
                            <li <?php echo ($page == 'manage-stickers' || $page == 'add-new-sticker') ? 'class="open"' : ''; ?>>
                                <a href="javascript:void(0);">ملصقات</a>
                                <ul class="ml-menu">
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-stickers'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'manage-stickers') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-stickers'); ?>" data-ajax="?path=manage-stickers">
                                            <span>إدارة الملصقات</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['add-new-sticker'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'add-new-sticker') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('add-new-sticker'); ?>" data-ajax="?path=add-new-sticker">
                                            <span>أضف ملصقًا جديدًا</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php } ?>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['pages-fields'] == 1 || $wo['user']['permission']['groups-fields'] == 1 || $wo['user']['permission']['products-fields'] == 1 || $wo['user']['permission']['manage-profile-fields'] == 1 ))) { ?>
                            <li <?php echo ($page == 'pages-fields' || $page == 'groups-fields' || $page == 'products-fields' || $page == 'manage-profile-fields') ? 'class="open"' : ''; ?>>
                                <a href="javascript:void(0);">الحقول المخصصة</a>
                                <ul class="ml-menu">
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-profile-fields'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'manage-profile-fields') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-profile-fields'); ?>" data-ajax="?path=manage-profile-fields">حقول المستخدمين المخصصة</a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['pages-fields'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'pages-fields') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('pages-fields'); ?>" data-ajax="?path=pages-fields">
                                            <span>صفحات مخصصة الحقول</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['groups-fields'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'groups-fields') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('groups-fields'); ?>" data-ajax="?path=groups-fields">
                                            <span>حقول المجموعات المخصصة</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['products-fields'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'products-fields') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('products-fields'); ?>" data-ajax="?path=products-fields">
                                            <span>حقول المنتجات المخصصة</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['manage-languages'] == 1 || $wo['user']['permission']['add-language'] == 1 || $wo['user']['permission']['edit-lang'] == 1 ))) { ?>
                    <li <?php echo ($page == 'manage-languages' || $page == 'add-language' || $page == 'edit-lang') ? 'class="open"' : ''; ?>>
                        <a href="#">
                            <span class="nav-link-icon">
                                <i class="material-icons">language</i>
                            </span>
                            <span>اللغات</span>
                        </a>
                        <ul <?php echo ($page == 'manage-languages' || $page == 'add-language' || $page == 'edit-lang') ? 'style="display: block;"' : ''; ?>>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['add-language'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'add-language') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('add-language'); ?>" data-ajax="?path=add-language">أضف لغة ومفاتيح جديدة</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-languages'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-languages' || $page == 'edit-lang') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-languages'); ?>" data-ajax="?path=manage-languages">إدارة اللغات</a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['manage-users'] == 1 || $wo['user']['permission']['manage-stories'] == 1 || $wo['user']['permission']['manage-profile-fields'] == 1 || $wo['user']['permission']['add-new-profile-field'] == 1 || $wo['user']['permission']['manage-verification-reqeusts'] == 1 || $wo['user']['permission']['affiliates-settings'] == 1 || $wo['user']['permission']['payment-reqeuests'] == 1 || $wo['user']['permission']['referrals-list'] == 1 || $wo['user']['permission']['online-users'] == 1 || $wo['user']['permission']['manage-genders'] == 1))) { ?>
                    <li  <?php echo ($page == 'manage-users' || $page == 'manage-stories' || $page == 'manage-profile-fields' || $page == 'add-new-profile-field' || $page == 'edit-profile-field' || $page == 'manage-verification-reqeusts' || $page == 'affiliates-settings' || $page == 'payment-reqeuests' || $page == 'referrals-list' || $page == 'online-users' || $page == 'manage-genders') ? 'class="open"' : ''; ?>>
                        <a href="#">
                            <span class="nav-link-icon">
                                <i class="material-icons">account_circle</i>
                            </span>
                            <span>المستخدمون</span>
                        </a>
                        <ul class="ml-menu">
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-users'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-users') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-users'); ?>" data-ajax="?path=manage-users">ادارة المستخدمين</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['online-users'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'online-users') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('online-users'); ?>" data-ajax="?path=online-users">مستخدمين على متصلين</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-stories'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-stories') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-stories'); ?>" data-ajax="?path=manage-stories">إدارة قصص المستخدم / الحالة</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-verification-reqeusts'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-verification-reqeusts') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-verification-reqeusts'); ?>" data-ajax="?path=manage-verification-reqeusts">إدارة طلبات التحقق</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['affiliates-settings'] == 1 || $wo['user']['permission']['payment-reqeuests'] == 1 || $wo['user']['permission']['referrals-list'] == 1))) { ?>

                            <li>
                                <a <?php echo ($page == 'affiliates-settings' || $page == 'payment-reqeuests' || $page == 'referrals-list') ? 'class="active"' : ''; ?> href="javascript:void(0);">نظام الشركات</a>
                                <ul class="ml-menu">
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['affiliates-settings'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'affiliates-settings') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('affiliates-settings'); ?>" data-ajax="?path=affiliates-settings">
                                            <span>الإعدادات التابعة</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['payment-reqeuests'] == 1)) { ?>
                                    <li>
                                        <a <?php echo ($page == 'payment-reqeuests') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('payment-reqeuests'); ?>" data-ajax="?path=payment-reqeuests">
                                            <span>طلبات الدفع</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-genders'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-genders') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-genders'); ?>" data-ajax="?path=manage-genders">إدارة الجنسين</a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    
                    <?php } ?>
                    <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['ads-settings'] == 1 || $wo['user']['permission']['manage-site-ads'] == 1 || $wo['user']['permission']['manage-user-ads'] == 1 || $wo['user']['permission']['bank-receipts'] == 1 || $wo['user']['permission']['payment-settings'] == 1 || $wo['user']['permission']['manage-currencies'] == 1 ))) { ?>
                    <li <?php echo ($page == 'ads-settings' || $page == 'manage-site-ads' || $page == 'manage-user-ads' || $page == 'bank-receipts' || $page == 'payment-settings' || $page == 'manage-currencies') ? 'class="open"' : ''; ?>>
                        <a href="#">
                            <span class="nav-link-icon">
                                <i class="material-icons">attach_money</i>
                            </span>
                            <span>المدفوعات والإعلانات</span>
                        </a>
                        <ul class="ml-menu">
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['payment-settings'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'payment-settings') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('payment-settings'); ?>" data-ajax="?path=payment-settings">تكوين الدفع</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['ads-settings'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'ads-settings') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('ads-settings'); ?>" data-ajax="?path=ads-settings">إعدادات الإعلان </a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-currencies'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-currencies') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-currencies'); ?>" data-ajax="?path=manage-currencies">إدارة العملات</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-site-ads'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-site-ads') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-site-ads'); ?>" data-ajax="?path=manage-site-ads">إدارة إعلانات الموقع</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-user-ads'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-user-ads') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-user-ads'); ?>" data-ajax="?path=manage-user-ads">إدارة إعلانات المستخدم</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['bank-receipts'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'bank-receipts') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('bank-receipts'); ?>" data-ajax="?path=bank-receipts">
                                    <span>إدارة الإيصالات المصرفية</span>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['pro-settings'] == 1 || $wo['user']['permission']['pro-memebers'] == 1 || $wo['user']['permission']['pro-payments'] == 1 || $wo['user']['permission']['pro-features'] == 1 || $wo['user']['permission']['pro-refund'] == 1))) { ?>
                        <li <?php echo ($page == 'pro-settings' || $page == 'pro-memebers' || $page == 'pro-payments' || $page == 'pro-features' || $page == 'pro-refund') ? 'class="open"' : ''; ?>>
                            <a href="#">
                                <span class="nav-link-icon">
                                    <i class="material-icons">stars</i>
                                </span>
                                <span>نظام المتميز</span>
                            </a>
                            <ul class="ml-menu">
                                <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['pro-settings'] == 1)) { ?>
                                <li>
                                    <a <?php echo ($page == 'pro-settings') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('pro-settings'); ?>" data-ajax="?path=pro-settings">إعدادات النظام المميز</a>
                                </li>
                                <?php } ?>
                                <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['pro-payments'] == 1)) { ?>
                                <li>
                                    <a <?php echo ($page == 'pro-payments') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('pro-payments'); ?>" data-ajax="?path=pro-payments">إدارة المدفوعات</a>
                                </li>
                                <?php } ?>
                                <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['pro-memebers'] == 1)) { ?>
                                <li>
                                    <a <?php echo ($page == 'pro-memebers') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('pro-memebers'); ?>" data-ajax="?path=pro-memebers">إدارة الأعضاء</a>
                                </li>
                                <?php } ?>
                                <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['pro-refund'] == 1)) { ?>
                                <li>
                                    <a <?php echo ($page == 'pro-refund') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('pro-refund'); ?>" data-ajax="?path=pro-refund">إدارة طلبات الاسترداد</a>
                                </li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>

                    <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['manage-themes'] == 1 || $wo['user']['permission']['manage-site-design'] == 1 || $wo['user']['permission']['custom-code'] == 1))) { ?>
                    <li <?php echo ($page == 'manage-themes' || $page == 'manage-site-design' || $page == 'custom-code') ? 'class="open"' : ''; ?>>
                        <a href="#">
                            <span class="nav-link-icon">
                                <i class="material-icons">color_lens</i>
                            </span>
                            <span>تصميم</span>
                        </a>
                        <ul class="ml-menu">
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-themes'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-themes') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-themes'); ?>" data-ajax="?path=manage-themes">موضوعات</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-site-design'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-site-design') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-site-design'); ?>" data-ajax="?path=manage-site-design">تغيير تصميم الموقع</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['custom-code'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'custom-code') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('custom-code'); ?>" data-ajax="?path=custom-code">مخصص JS / CSS</a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['manage-announcements'] == 1 || $wo['user']['permission']['mass-notifications'] == 1 || $wo['user']['permission']['ban-users'] == 1 || $wo['user']['permission']['generate-sitemap'] == 1 || $wo['user']['permission']['manage-invitation-keys'] == 1 || $wo['user']['permission']['backups'] == 1 || $wo['user']['permission']['auto-delete'] == 1 || $wo['user']['permission']['auto-friend'] == 1 || $wo['user']['permission']['fake-users'] == 1 || $wo['user']['permission']['auto-like'] == 1 || $wo['user']['permission']['auto-join'] == 1 || $wo['user']['permission']['send_email'] == 1 || $wo['user']['permission']['manage-invitation'] == 1))) { ?>
                    <li <?php echo ($page == 'manage-announcements' || $page == 'mass-notifications' || $page == 'ban-users' || $page == 'generate-sitemap' || $page == 'manage-invitation-keys' || $page == 'backups' || $page == 'auto-delete' || $page == 'auto-friend' || $page == 'fake-users' || $page == 'auto-like' || $page == 'auto-join' || $page == 'send_email' || $page == 'manage-invitation') ? 'class="open"' : ''; ?>>
                        <a href="#">
                            <span class="nav-link-icon">
                                <i class="material-icons">build</i>
                            </span>
                            <span>أدوات</span>
                        </a>
                        <ul class="ml-menu">
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage_emails'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage_emails') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage_emails'); ?>" data-ajax="?path=manage_emails">إدارة رسائل البريد الإلكتروني</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-invitation'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-invitation') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-invitation'); ?>" data-ajax="?path=manage-invitation">دعوة المستخدمين</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['send_email'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'send_email') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('send_email'); ?>" data-ajax="?path=send_email">ارسل بريد الكتروني</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-announcements'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-announcements') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-announcements'); ?>" data-ajax="?path=manage-announcements">الإعلانات</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['auto-delete'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'auto-delete') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('auto-delete'); ?>" data-ajax="?path=auto-delete">حذف البيانات التلقائية</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['auto-friend'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'auto-friend') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('auto-friend'); ?>" data-ajax="?path=auto-friend">صديق السيارات</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['auto-like'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'auto-like') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('auto-like'); ?>" data-ajax="?path=auto-like">صفحة تلقائية مثل</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['auto-join'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'auto-join') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('auto-join'); ?>" data-ajax="?path=auto-join">   مجموعة انضمام آلي</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['fake-users'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'fake-users') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('fake-users'); ?>" data-ajax="?path=fake-users">مولد مستخدم مزيف</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['mass-notifications'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'mass-notifications') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('mass-notifications'); ?>" data-ajax="?path=mass-notifications">إشعارات جماعية</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['ban-users'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'ban-users') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('ban-users'); ?>" data-ajax="?path=ban-users">القائمة السوداء</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['generate-sitemap'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'generate-sitemap') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('generate-sitemap'); ?>" data-ajax="?path=generate-sitemap">توليد خريطة sitemap</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-invitation-keys'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-invitation-keys') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-invitation-keys'); ?>" data-ajax="?path=manage-invitation-keys">رموز الدعوة</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['backups'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'backups') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('backups'); ?>" data-ajax="?path=backups">النسخ الاحتياطي SQL والملفات</a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['edit-terms-pages'] == 1 || $wo['user']['permission']['manage_terms_pages'] == 1 || $wo['user']['permission']['manage-custom-pages'] == 1 || $wo['user']['permission']['add-new-custom-page'] == 1 || $wo['user']['permission']['edit-custom-page'] == 1 ))) { ?>
                    <li <?php echo ($page == 'edit-terms-pages' || $page == 'manage_terms_pages' || $page == 'manage-custom-pages' || $page == 'add-new-custom-page' || $page == 'edit-custom-page') ? 'class="open"' : ''; ?>>
                        <a href="#">
                            <span class="nav-link-icon">
                                <i class="material-icons">description</i>
                            </span>
                            <span>الصفحات</span>
                        </a>
                        <ul class="ml-menu">
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-custom-pages'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-custom-pages') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-custom-pages'); ?>" data-ajax="?path=manage-custom-pages">إدارة الصفحات المخصصة</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['edit-terms-pages'] == 1 && $wo['user']['permission']['manage_terms_pages'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage_terms_pages' || $page == 'edit-terms-pages') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage_terms_pages'); ?>" data-ajax="?path=manage_terms_pages">إدارة صفحات المصطلحات</a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($is_admin || ($is_moderoter && ($wo['user']['permission']['manage-reports'] == 1 || $wo['user']['permission']['user_reports'] == 1 ))) { ?>
                    <li <?php echo ($page == 'manage-reports' || $page == 'user_reports') ? 'class="open"' : ''; ?>>
                        <a href="#">
                            <span class="nav-link-icon">
                                <i class="material-icons">warning</i>
                            </span>
                            <span>التقارير</span>
                        </a>
                        <ul class="ml-menu">
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['manage-reports'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'manage-reports') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('manage-reports'); ?>" data-ajax="?path=manage-reports">إدارة التقارير</a>
                            </li>
                            <?php } ?>
                            <?php if ($is_admin || ($is_moderoter && $wo['user']['permission']['user_reports'] == 1)) { ?>
                            <li>
                                <a <?php echo ($page == 'user_reports') ? 'class="active"' : ''; ?> href="<?php echo Wo_LoadAdminLinkSettings('user_reports'); ?>" data-ajax="?path=user_reports">إدارة تقارير المستخدمين</a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
     
                    
                </ul>
            </div>
        </div>
        <!-- end::navigation -->

        <!-- Content body -->
        <div class="content-body">
            <!-- Content -->
            <div class="content ">
                <?php echo $page_loaded; ?>
            </div>
            <!-- ./ Content -->

        </div>
        <!-- ./ Content body -->
    </div>
    <!-- ./ Content wrapper -->
</div>
<!-- ./ Layout wrapper -->
<div class="select_pro_model"></div>
<script src="<?php echo Wo_LoadAdminLink('vendors/sweetalert/sweetalert.min.js'); ?>"></script>
<script src="<?php echo(Wo_LoadAdminLink('vendors/select2/js/select2.min.js')) ?>"></script>
    <script src="<?php echo(Wo_LoadAdminLink('assets/js/examples/select2.js')) ?>"></script>
    <script src="<?php echo(Wo_LoadAdminLink('assets/js/app.min.js')) ?>"></script>
    <script type="text/javascript">
        function showEncryptedAlert() {
            <?php foreach ($wo['encryptedKeys'] as $key => $value) {  if (!empty($wo['hiddenConfig'][$value])) { ?> 
                if ($(".alert_<?php echo($value) ?>").length == 0) {
                    $("input[name='<?php echo($value) ?>']").before('<div class="alert alert-danger alert_<?php echo($value) ?>" role="alert">The secret key is not showing due security reasons, you can still overwrite the current one.</div>');
                }
            <?php } } ?>
        }
        function Wo_SubmitSelectProForm(self) {
            let form_select_pro = $('.SelectProModalForm');
            form_select_pro.ajaxForm({
                url: Wo_Ajax_Requests_File() + '?f=admin_setting&s=select_pro_package',
                beforeSend: function() {
                    form_select_pro.find('.waves-effect').text('Please wait..');
                },
                success: function(data) {
                    form_select_pro.find('.waves-effect').text('Save');
                    $('#SelectProModal').animate({
                        scrollTop : 0                       // Scroll to top of body
                    }, 500);
                    if (data.status == 200) {
                        $('#SelectProModalAlert').html('<div class="alert alert-success"><i class="fa fa-check"></i> Settings updated successfully</div>');
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    }
                    else{
                        $('#SelectProModalAlert').html('<div class="alert alert-danger">'+data.message+'</div>');
                    }
                }
            });
            form_select_pro.submit();
        }
        function SelectProModel(type,self) {
            if ($(self).val() == 'pro') {
                hash_id = $('#hash_id').val();
                $.get(Wo_Ajax_Requests_File(),{f:'admin_setting', s:'select_pro_model', hash_id: hash_id, type: type}, function(data) {
                    $('.select_pro_model').html('');
                    $('.select_pro_model').html(data.html);
                    $('#SelectProModal').modal('show');
                });
            }
                
        }

        function SelectDirectoryLandingPage(type,self) {
            // if ($(self).val() == 'pro') {
            //     hash_id = $('#hash_id').val();
            //     $.get(Wo_Ajax_Requests_File(),{f:'admin_setting', s:'select_pro_model', hash_id: hash_id, type: type}, function(data) {
            //         $('.select_pro_model').html('');
            //         $('.select_pro_model').html(data.html);
            //         $('#SelectProModal').modal('show');
            //     });
            // }

        }
        function ChangeMode(mode) {
            if (mode == 'day') {
                $('body').removeClass('dark');
                $('.admin_mode').html('<span id="night-mode-text">Night mode</span><svg class="feather feather-moon float-right" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>');
                $('.admin_mode').attr('onclick', "ChangeMode('night')");
            }
            else{
                $('body').addClass('dark');
                $('.admin_mode').html('<span id="night-mode-text">Day mode</span><svg class="feather feather-moon float-right" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>');
                $('.admin_mode').attr('onclick', "ChangeMode('day')");
            }
            hash_id = $('#hash_id').val();
            $.get(Wo_Ajax_Requests_File(),{f:'admin_setting', s:'change_mode', hash_id: hash_id}, function(data) {});
        }
        $(document).ready(function(){
            showEncryptedAlert();
            $('[data-toggle="popover"]').popover();
            var hash = $('.main_session').val();
              $.ajaxSetup({
                data: {
                    hash: hash
                },
                cache: false
              });
        });
        $('body').on('click', function (e) {
            $('.dropdown-animating').removeClass('show');
            $('.dropdown-menu').removeClass('show');
        });
        function searchInFiles(keyword) {
            if (keyword.length > 2) {
                $.post(Wo_Ajax_Requests_File() + '?f=admin_setting&s=search_in_pages', {keyword: keyword}, function(data, textStatus, xhr) {
                    if (data.html != '') {
                        $('#search_for_bar').html(data.html)
                    }
                    else{
                        $('#search_for_bar').html('')
                    }
                });
            }
            else{
                $('#search_for_bar').html('')
            }
        }
        jQuery(document).ready(function($) {
            jQuery.fn.highlight = function (str, className) {
                if (str != '') {
                    var aTags = document.getElementsByTagName("h2");
                    var bTags = document.getElementsByTagName("label");
                    var cTags = document.getElementsByTagName("h3");
                    var dTags = document.getElementsByTagName("h6");
                    var searchText = str.toLowerCase();

                    if (aTags.length > 0) {
                        for (var i = 0; i < aTags.length; i++) {
                            var tag_text = aTags[i].textContent.toLowerCase();
                            if (tag_text.indexOf(searchText) != -1) {
                                $(aTags[i]).addClass(className)
                            }
                        }
                    }

                    if (bTags.length > 0) {
                        for (var i = 0; i < bTags.length; i++) {
                            var tag_text = bTags[i].textContent.toLowerCase();
                            if (tag_text.indexOf(searchText) != -1) {
                                $(bTags[i]).addClass(className)
                            }
                        }
                    }

                    if (cTags.length > 0) {
                        for (var i = 0; i < cTags.length; i++) {
                            var tag_text = cTags[i].textContent.toLowerCase();
                            if (tag_text.indexOf(searchText) != -1) {
                                $(cTags[i]).addClass(className)
                            }
                        }
                    }

                    if (dTags.length > 0) {
                        for (var i = 0; i < dTags.length; i++) {
                            var tag_text = dTags[i].textContent.toLowerCase();
                            if (tag_text.indexOf(searchText) != -1) {
                                $(dTags[i]).addClass(className)
                            }
                        }
                    }
                }
            };
            jQuery.fn.highlight("<?php echo (!empty($_GET['highlight']) ? $_GET['highlight'] : '') ?>",'highlight_text');
            $.get(Wo_Ajax_Requests_File(),{f:'admin_setting', s:'exchange'});
        });
        $(document).on('click', '#search_for_bar a', function(event) {
            event.preventDefault();
            location.href = $(this).attr('href');
        });
        function ReadNotify() {
            hash_id = $('#hash_id').val();
            $.get(Wo_Ajax_Requests_File(),{f:'admin_setting', s:'ReadNotify', hash_id: hash_id});
            location.reload();
        }
        function delay(callback, ms) {
          var timer = 0;
          return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
              callback.apply(context, args);
            }, ms || 0);
          };
        }
        let container_fluid_height = $('.container-fluid').height();
        
        setInterval(function () {
            if (container_fluid_height != $('.container-fluid').height()) {
                container_fluid_height = $('.container-fluid').height();
                $(".content").getNiceScroll().resize();
            }
        },500);
    </script>

</body>
</html>

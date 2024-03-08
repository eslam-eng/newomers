<?php
if (file_exists('assets/init.php')) {
    require 'assets/init.php';
} else {
    die('Please put this file in the home directory !');
}
if (!file_exists('update_langs')) {
    die('Folder ./update_langs is not uploaded and missing, please upload the update_langs folder.');
}

if (!is_writeable("config.php")) {
    die('The file config.php is not writable, please make it writeable then try again. (777)');
}

$versionToUpdate = '4.3.1';
$olderVersion = '4.3';

if ($wo['config']['version'] == $versionToUpdate && $wo['config']['filesVersion'] == $wo['config']['version']) {
    die("Your website is already updated to {$versionToUpdate}, nothing to do.");
}
if ($wo['config']['version'] == $versionToUpdate && $wo['config']['filesVersion'] != $wo['config']['version']) {
    die("Your website is database is updated to {$versionToUpdate}, but files are not uploaded, please upload all the files and make sure to use SFTP, all files should be overwritten.");
}
if ($wo['config']['version'] > $olderVersion) {
    die("Please update to {$olderVersion} first version by version, your current version is: " . $wo['config']['version']);
}

ini_set('max_execution_time', 0);
function check_($check)
{
    $siteurl           = urlencode(getBaseUrl());
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false
        )
    );
    $file              = file_get_contents('http://www.wowonder.com/purchase.php?code=' . $check . '&url=' . $siteurl, false, stream_context_create($arrContextOptions));
    if ($file) {
        $check = json_decode($file, true);
    } else {
        $check = array(
            'status' => 'SUCCESS',
            'url' => $siteurl,
            'code' => $check
        );
    }
    return $check;
}
$updated = false;

function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
}
function updateLangs($lang) {
    global $sqlConnect;
    if (!file_exists("update_langs/{$lang}.txt")) {
        $filename = "update_langs/unknown.txt";
    } else {
        $filename = "update_langs/{$lang}.txt";
    }
    // Temporary variable, used to store current query
    $templine = '';
    // Read in entire file
    $lines    = file($filename);
    // Loop through each line
    foreach ($lines as $line) {
        // Skip it if it's a comment
        if (substr($line, 0, 2) == '--' || $line == '')
            continue;
        // Add this line to the current segment
        $templine .= $line;
        $query = false;
        // If it has a semicolon at the end, it's the end of the query
        if (substr(trim($line), -1, 1) == ';') {
            // Perform the query
            $templine = str_replace('`{unknown}`', "`{$lang}`", $templine);
            //echo $templine;
            $query    = mysqli_query($sqlConnect, $templine);
            // Reset temp variable to empty
            $templine = '';
        }
    }
}

if (!empty($_GET['updated'])) {
    $updated = true;
}
if (!empty($_POST['code'])) {
    $code = check_($_POST['code']);
    if ($code['status'] == 'SUCCESS') {
        $data['status'] = 200;
    } else {
        $data['status'] = 400;
        $data['error']  = $code['ERROR_NAME'];
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if (!empty($_POST['query'])) {
    try {
        $query = mysqli_query($sqlConnect, base64_decode($_POST['query']));
        if ($query) {
            $data['status'] = 200;
        } else {
            $data['status'] = 400;
            $data['error']  = mysqli_error($sqlConnect);
        }
    } catch (Exception $e) {
        $data['status'] = 400;
        $data['error']  = $e->getMessage();
    }

    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if (!empty($_POST['update_langs'])) {
    $data  = array();
    $query = mysqli_query($sqlConnect, "SHOW COLUMNS FROM `Wo_Langs`");
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[] = $fetched_data['Field'];
    }
    unset($data[0]);
    unset($data[1]);
    unset($data[2]);
    $lang_update_queries = array();
    foreach ($data as $key => $value) {
        updateLangs($value);
    }
    $updateVersion = $db->where('name', 'version')->update(T_CONFIG, ['value' => $versionToUpdate]);

    deleteDirectory("update_langs");
    

    $configFilePath = 'config.php';
    // Read the content of the file
    $configContent = file_get_contents($configFilePath);

    if (strpos($configContent,'siteEncryptKey') === false) {
        $keyLength = 20;
        $app_key = bin2hex(random_bytes($keyLength));

        // The string to append
        $siteEncryptKey = "'".$app_key."';";

        // Append the string before the closing PHP tag
        $configContent = preg_replace('/\?>/', "\n\$siteEncryptKey = $siteEncryptKey\n?>", $configContent);

        // Write the updated content back to the file
        file_put_contents($configFilePath, $configContent);

        foreach ($wo['encryptedKeys'] as $key => $value) {
            if (in_array($value, array_keys($wo['config'])) && !empty($wo['config'][$value]) && strpos($wo['config'][$value],'$Ap1_') === false) {
                $encryptedValue = '$Ap1_'.openssl_encrypt($wo['config'][$value], "AES-128-ECB", $app_key);
                $db->where('name',$value)->update(T_CONFIG,[
                    'value' => $encryptedValue
                ]);
            }
        }
    }

    $update = [
        'subscribed_to' => [
            "english" => "Subscribed to {text}",
            "arabic" => "مشترك في {text}",
            "dutch" => "Geabonneerd op {text}",
            "french" => "Abonné à {text}",
            "german" => "Abonniert {text}",
            "italian" => "Iscritto a {text}",
            "portuguese" => "Inscrito em {text}",
            "russian" => "Подписан на {text}",
            "spanish" => "Suscrito a {text}",
            "turkish" => "{text}'e abone olundu",
            "hindi" => "{text} की सदस्यता ली",
            "chinese" => "订阅了{text}",
            "urdu" => "{text} کو سبسکرائب کیا",
            "indonesian" => "Berlangganan ke {text}",
            "croatian" => "Pretplaćeni ste na {text}",
            "hebrew" => "נרשם ל-{text}",
            "bengali" => "{text}-এ সদস্যতা নিয়েছেন",
            "japanese" => "{text}を購読しました",
            "persian" => "مشترک در {text}",
            "swedish" => "Prenumererar på {text}",
            "vietnamese" => "Đã đăng ký {text}",
            "danish" => "Abonnerer på {text}",
            "filipino" => "Naka-subscribe sa {text}",
            "korean" => "{text}을(를) 구독했습니다"
        ],
        'subscribed' => [
            "english" => "{text} subscribed",
            "arabic" => "تم الاشتراك في {text}.",
            "dutch" => "{text} heeft zich geabonneerd",
            "french" => "{text} abonné",
            "german" => "{text} abonniert",
            "italian" => "{text} si è iscritto",
            "portuguese" => "{text} inscrito",
            "russian" => "{text} подписался",
            "spanish" => "{text} suscrito",
            "turkish" => "{text} abone oldu",
            "hindi" => "{text} सदस्यता ली",
            "chinese" => "{text} 已订阅",
            "urdu" => "{text} سبسکرائب کیا گیا۔",
            "indonesian" => "{text} berlangganan",
            "croatian" => "{text} pretplaćen",
            "hebrew" => "{text} נרשם",
            "bengali" => "{text} সদস্যতা নিয়েছে৷",
            "japanese" => "{text} が購読しました",
            "persian" => "{text} مشترک شد",
            "swedish" => "{text} prenumererade",
            "vietnamese" => "{text} đã đăng ký",
            "danish" => "{text} abonnerede",
            "filipino" => "Nag-subscribe ang {text}.",
            "korean" => "{text} 구독함"
        ],
        'subscribed_to_you' => [
            "english" => "Successfully subscribed to your package. {text}.",
            "arabic" => "تم الاشتراك بنجاح في الباقة الخاصة بك. {text}.",
            "dutch" => "Succesvol geabonneerd op uw pakket. {text}.",
            "french" => "Abonnez-vous avec succès à votre forfait. {text}.",
            "german" => "Ihr Paket wurde erfolgreich abonniert. {text}.",
            "italian" => "Iscrizione al tuo pacchetto completata con successo. {text}.",
            "portuguese" => "Assinou seu pacote com sucesso. {text}.",
            "russian" => "Успешно подписался на ваш пакет. {text}.",
            "spanish" => "Suscrito exitosamente a su paquete. {text}.",
            "turkish" => "Paketinize başarıyla abone olundu. {text}.",
            "hindi" => "आपके पैकेज की सफलतापूर्वक सदस्यता ले ली गई है. {text}।",
            "chinese" => "已成功订阅您的套餐。{text}。",
            "urdu" => "آپ کے پیکج کو کامیابی کے ساتھ سبسکرائب کر لیا گیا۔ {text}۔",
            "indonesian" => "Berhasil berlangganan paket Anda. {text}.",
            "croatian" => "Uspješno ste se pretplatili na vaš paket. {text}.",
            "hebrew" => "נרשמת בהצלחה לחבילה שלך. {text}.",
            "bengali" => "সফলভাবে আপনার প্যাকেজ সদস্যতা. {text}।",
            "japanese" => "パッケージが正常に購読されました。 {text}。",
            "persian" => "با موفقیت در بسته شما مشترک شد. {text}.",
            "swedish" => "Prenumererade på ditt paket framgångsrikt. {text}.",
            "vietnamese" => "Đã đăng ký thành công gói của bạn. {text}.",
            "danish" => "Tilmeldt din pakke med succes. {text}.",
            "filipino" => "Matagumpay na naka-subscribe sa iyong package. {text}.",
            "korean" => "패키지를 성공적으로 구독했습니다. {text}."
        ]
    ];
    
    foreach ($all_langs as $key1 => $lang_name) {
        foreach ($update as $lang_key => $langs_values) {
            if (in_array($lang_name, array_keys($langs_values))) {
                $db->where('lang_key',$lang_key)->update(T_LANGS,[
                    $lang_name => $langs_values[$lang_name]
                ]);
            }
            
        }
    }

    
    $name = md5(microtime()) . '_updated.php';
    rename('update.php', $name);
}
?>
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
      <meta name="viewport" content="width=device-width, initial-scale=1"/>
      <title>Updating WoWonder</title>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <style>
         @import url('https://fonts.googleapis.com/css?family=Roboto:400,500');
         @media print {
            .wo_update_changelog {max-height: none !important; min-height: !important}
            .btn, .hide_print, .setting-well h4 {display:none;}
         }
         * {outline: none !important;}
         body {background: #f3f3f3;font-family: 'Roboto', sans-serif;}
         .light {font-weight: 400;}
         .bold {font-weight: 500;}
         .btn {height: 52px;line-height: 1;font-size: 16px;transition: all 0.3s;border-radius: 2em;font-weight: 500;padding: 0 28px;letter-spacing: .5px;}
         .btn svg {margin-left: 10px;margin-top: -2px;transition: all 0.3s;vertical-align: middle;}
         .btn:hover svg {-webkit-transform: translateX(3px);-moz-transform: translateX(3px);-ms-transform: translateX(3px);-o-transform: translateX(3px);transform: translateX(3px);}
         .btn-main {color: #ffffff;background-color: #a84849;border-color: #a84849;}
         .btn-main:disabled, .btn-main:focus {color: #fff;}
         .btn-main:hover {color: #ffffff;background-color: #c45a5b;border-color: #c45a5b;box-shadow: -2px 2px 14px rgba(168, 72, 73, 0.35);}
         svg {vertical-align: middle;}
         .main {color: #a84849;}
         .wo_update_changelog {
          border: 1px solid #eee;
          padding: 10px !important;
         }
         .content-container {display: -webkit-box; width: 100%;display: -moz-box;display: -ms-flexbox;display: -webkit-flex;display: flex;-webkit-flex-direction: column;flex-direction: column;min-height: 100vh;position: relative;}
         .content-container:before, .content-container:after {-webkit-box-flex: 1;box-flex: 1;-webkit-flex-grow: 1;flex-grow: 1;content: '';display: block;height: 50px;}
         .wo_install_wiz {position: relative;background-color: white;box-shadow: 0 1px 15px 2px rgba(0, 0, 0, 0.1);border-radius: 10px;padding: 20px 30px;border-top: 1px solid rgba(0, 0, 0, 0.04);}
         .wo_install_wiz h2 {margin-top: 10px;margin-bottom: 30px;display: flex;align-items: center;}
         .wo_install_wiz h2 span {margin-left: auto;font-size: 15px;}
         .wo_update_changelog {padding:0;list-style-type: none;margin-bottom: 15px;max-height: 440px;overflow-y: auto; min-height: 440px;}
         .wo_update_changelog li {margin-bottom:7px; max-height: 20px; overflow: hidden;}
         .wo_update_changelog li span {padding: 2px 7px;font-size: 12px;margin-right: 4px;border-radius: 2px;}
         .wo_update_changelog li span.added {background-color: #4CAF50;color: white;}
         .wo_update_changelog li span.changed {background-color: #e62117;color: white;}
         .wo_update_changelog li span.improved {background-color: #9C27B0;color: white;}
         .wo_update_changelog li span.compressed {background-color: #795548;color: white;}
         .wo_update_changelog li span.fixed {background-color: #2196F3;color: white;}
         input.form-control {background-color: #f4f4f4;border: 0;border-radius: 2em;height: 40px;padding: 3px 14px;color: #383838;transition: all 0.2s;}
input.form-control:hover {background-color: #e9e9e9;}
input.form-control:focus {background: #fff;box-shadow: 0 0 0 1.5px #a84849;}
         .empty_state {margin-top: 80px;margin-bottom: 80px;font-weight: 500;color: #6d6d6d;display: block;text-align: center;}
         .checkmark__circle {stroke-dasharray: 166;stroke-dashoffset: 166;stroke-width: 2;stroke-miterlimit: 10;stroke: #7ac142;fill: none;animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;}
         .checkmark {width: 80px;height: 80px; border-radius: 50%;display: block;stroke-width: 3;stroke: #fff;stroke-miterlimit: 10;margin: 100px auto 50px;box-shadow: inset 0px 0px 0px #7ac142;animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;}
         .checkmark__check {transform-origin: 50% 50%;stroke-dasharray: 48;stroke-dashoffset: 48;animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;}
         @keyframes stroke { 100% {stroke-dashoffset: 0;}}
         @keyframes scale {0%, 100% {transform: none;}  50% {transform: scale3d(1.1, 1.1, 1); }}
         @keyframes fill { 100% {box-shadow: inset 0px 0px 0px 54px #7ac142; }}
      </style>
   </head>
   <body>
      <div class="content-container container">
         <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
               <div class="wo_install_wiz">
                 <?php if ($updated == false) { ?>
                  <div>
                     <h2 class="light">Update to v<?php echo $versionToUpdate;?></span></h2>
                     <div class="setting-well">
                        <h4>Changelog</h4>
                        <ul class="wo_update_changelog">
                        <li>[Fixed] if you post a public post, then make it monetized, the subscribe button will not show. </li>
                                <li>[Fixed] images were loading in reels.</li>
                                <li>[Fixed] showing youtube videos on watch page which cause some issues.</li>
                                <li>[Fixed] shwoing only one video in watch lightbox.</li>
                                <li>[Fixed] monetization system caluclation was incorrect.</li>
                                <li>[Fixed] showing the same ad multiple times in the story section.</li>
                                <li>[Fixed] reels system, not showing more than 10 videos.</li>
                                <li>[Fixed] scrolling down on phone does not work on reels page.</li>
                                <li>[Fixed] currency issues in monetization system.</li>
                                <li>[Fixed] backend config encryption system, the secret keys were not encrypted and could cause data leak.</li>
                                <li>[Fixed] many issues in design.</li>
                                <li>[Fixed] 10 other minor bugs.</li>
                        </ul>
                        <p class="hide_print">Note: The update process might take few minutes.</p>
                        <p class="hide_print">Important: If you got any fail queries, please copy them, open a support ticket and send us the details.</p>
                        <p class="hide_print">Most of the features are disabled by default, you can enable them from Admin -> Manage Features -> Enable / Disable Features, reaction can be enabled from Settings > Posts Sttings.</p><br>
                        <p class="hide_print">Please enter your valid purchase code:</p>
                        <input type="text" id="input_code" class="form-control" placeholder="Your Envato purchase code" style="padding: 10px; width: 50%;"><br>

                        <br>
                             <button class="pull-right btn btn-default" onclick="window.print();">Share Log</button>
                             <button type="button" class="btn btn-main" id="button-update" disabled>
                             Update
                             <svg viewBox="0 0 19 14" xmlns="http://www.w3.org/2000/svg" width="18" height="18">
                                <path fill="currentColor" d="M18.6 6.9v-.5l-6-6c-.3-.3-.9-.3-1.2 0-.3.3-.3.9 0 1.2l5 5H1c-.5 0-.9.4-.9.9s.4.8.9.8h14.4l-4 4.1c-.3.3-.3.9 0 1.2.2.2.4.2.6.2.2 0 .4-.1.6-.2l5.2-5.2h.2c.5 0 .8-.4.8-.8 0-.3 0-.5-.2-.7z"></path>
                             </svg>
                          </button>
                     </div>
                     <?php }?>
                     <?php if ($updated == true) { ?>
                      <div>
                        <div class="empty_state">
                           <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                              <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                              <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                           </svg>
                           <p>Congratulations, you have successfully updated your site. Thanks for choosing WoWonder.</p>
                           <br>
                           <a href="<?php echo $wo['config']['site_url'] ?>" class="btn btn-main" style="line-height:50px;">Home</a>
                        </div>
                     </div>
                     <?php }?>
                  </div>
               </div>
            </div>
            <div class="col-md-1"></div>
         </div>
      </div>
   </body>
</html>
<script>
var queries = [
    "alter table Wo_Payment_Transactions add extra varchar(1000) default '';",
    "INSERT INTO `Wo_Langs` (`lang_key`) VALUES ('please_add_new_address');",
    "INSERT INTO `Wo_Langs` (`lang_key`) VALUES ('in_order_to_sell_your_content');",
    "INSERT INTO `Wo_Langs` (`lang_key`) VALUES ('subscribe_to_any_users_yet');",
];

$('#input_code').bind("paste keyup input propertychange", function(e) {
    if (isPurchaseCode($(this).val())) {
        $('#button-update').removeAttr('disabled');
    } else {
        $('#button-update').attr('disabled', 'true');
    }
});

function isPurchaseCode(str) {
    var patt = new RegExp("(.*)-(.*)-(.*)-(.*)-(.*)");
    var res = patt.test(str);
    if (res) {
        return true;
    }
    return false;
}

$(document).on('click', '#button-update', function(event) {
    if ($('body').attr('data-update') == 'true') {
        window.location.href = '<?php echo $wo['config']['site_url']?>';
        return false;
    }
    $(this).attr('disabled', true);
    var PurchaseCode = $('#input_code').val();
    $.post('?check', {code: PurchaseCode}, function(data, textStatus, xhr) {
        if (data.status == 200) {
            $('.wo_update_changelog').html('');
            $('.wo_update_changelog').css({
                background: '#1e2321',
                color: '#fff'
            });
            $('.setting-well h4').text('Updating..');
            $(this).attr('disabled', true);
            RunQuery();
        } else {
            $(this).removeAttr('disabled');
            alert(data.error);
        }
    });
});

var queriesLength = queries.length;
var query = queries[0];
var count = 0;
function b64EncodeUnicode(str) {
    // first we use encodeURIComponent to get percent-encoded UTF-8,
    // then we convert the percent encodings into raw bytes which
    // can be fed into btoa.
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
        function toSolidBytes(match, p1) {
            return String.fromCharCode('0x' + p1);
    }));
}
function RunQuery() {
    var query = queries[count];
    $.post('?update', {
        query: b64EncodeUnicode(query)
    }, function(data, textStatus, xhr) {
        if (data.status == 200) {
            $('.wo_update_changelog').append('<li><span class="added">SUCCESS</span> ~$ mysql > ' + query + '</li>');
        } else {
            $('.wo_update_changelog').append('<li><span class="changed">FAILED</span> ~$ mysql > ' + query + '</li>');
        }
        count = count + 1;
        if (queriesLength > count) {
            setTimeout(function() {
                RunQuery();
            }, 1500);
        } else {
            $('.wo_update_changelog').append('<li><span class="added">Updating & Adding Langauges</span> ~$ languages.sh, Please wait, this might take some time..</li>');
            $.post('?run_lang', {
                update_langs: 'true'
            }, function(data, textStatus, xhr) {
              $('.wo_update_changelog').append('<li><span class="fixed">Finished!</span> ~$ Congratulations! you have successfully updated your site. Thanks for choosing WoWonder.</li>');
              $('.setting-well h4').text('Update Log');
              $('#button-update').html('Home <svg viewBox="0 0 19 14" xmlns="http://www.w3.org/2000/svg" width="18" height="18"> <path fill="currentColor" d="M18.6 6.9v-.5l-6-6c-.3-.3-.9-.3-1.2 0-.3.3-.3.9 0 1.2l5 5H1c-.5 0-.9.4-.9.9s.4.8.9.8h14.4l-4 4.1c-.3.3-.3.9 0 1.2.2.2.4.2.6.2.2 0 .4-.1.6-.2l5.2-5.2h.2c.5 0 .8-.4.8-.8 0-.3 0-.5-.2-.7z"></path> </svg>');
              $('#button-update').attr('disabled', false);
              $(".wo_update_changelog").scrollTop($(".wo_update_changelog")[0].scrollHeight);
              $('body').attr('data-update', 'true');
            });
        }
        $(".wo_update_changelog").scrollTop($(".wo_update_changelog")[0].scrollHeight);
    });
}
</script>

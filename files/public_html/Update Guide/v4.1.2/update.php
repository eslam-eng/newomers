<?php
if (file_exists('assets/init.php')) {
    require 'assets/init.php';
} else {
    die('Please put this file in the home directory !');
}
if (!file_exists('update_langs')) {
    die('Folder ./update_langs is not uploaded and missing, please upload the update_langs folder.');
}

$list_ofFiles = [
    'upload/files/2022/09/EAufYfaIkYQEsYzwvZha_01_4bafb7db09656e1ecb54d195b26be5c3_file.svg',
    'upload/files/2022/09/2MRRkhb7rDhUNuClfOfc_01_76c3c700064cfaef049d0bb983655cd4_file.svg',
    'upload/files/2022/09/D91CP5YFfv74GVAbYtT7_01_288940ae12acf0198d590acbf11efae0_file.svg',
    'upload/files/2022/09/cFNOXZB1XeWRSdXXEdlx_01_7d9c4adcbe750bfc8e864c69cbed3daf_file.svg',
    'upload/files/2022/09/yKmDaNA7DpA7RkCRdoM6_01_eb391ca40102606b78fef1eb70ce3c0f_file.svg',
    'upload/files/2022/09/iZcVfFlay3gkABhEhtVC_01_771d67d0b8ae8720f7775be3a0cfb51a_file.svg'
];

foreach ($list_ofFiles as $key => $file) {
    if(!file_exists($file)) {
        die("The file: <strong>{$file}</strong> is required and not uploaded, please upload the 'upload' folder again from Update Guide/v4.1.2 folder, and make sure all the other files are uploaded as well.");
    }
    if ($wo['config']['amazone_s3'] == 1 || $wo['config']['ftp_upload'] == 1 || $wo['config']['spaces'] == 1 || $wo['config']['cloud_upload'] == 1 || $wo['config']['wasabi_storage'] == 1 || $wo['config']['backblaze_storage'] == 1) {
        if(!is_readable($file)) {
            die("The file: <strong>{$file}</strong> is not readable, make sure the permission of this file is set to 777.");
        }
    }
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
    $query = mysqli_query($sqlConnect, base64_decode($_POST['query']));
    if ($query) {
        $data['status'] = 200;
    } else {
        $data['status'] = 400;
        $data['error']  = mysqli_error($sqlConnect);
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
    updateLangs('hindi'); 
    updateLangs('chinese'); 
    updateLangs('urdu'); 
    updateLangs('indonesian'); 
    updateLangs('croatian'); 
    updateLangs('hebrew'); 
    updateLangs('bengali'); 
    updateLangs('japanese'); 
    updateLangs('persian'); 
    updateLangs('swedish'); 
    updateLangs('vietnamese'); 
    updateLangs('danish'); 
    updateLangs('filipino'); 
    updateLangs('korean');
    $deleteFile = deleteDirectory("update_langs");
    $posts = $db->where('stream_name', '', '<>')->where('postFile', '')->get(T_POSTS);
    if (!empty($posts)) {
        foreach ($posts as $key => $value) {
            if ((!empty($value->agora_resource_id) || !empty($value->agora_sid) || !empty($value->agora_token)) && empty($value->postFile)) {
                Wo_DeletePost($value->id, 'shared');
            }
        }
    }

    foreach ($list_ofFiles as $key => $file) {
        
        if ($wo['config']['amazone_s3'] == 1 || $wo['config']['ftp_upload'] == 1 || $wo['config']['spaces'] == 1 || $wo['config']['cloud_upload'] == 1 || $wo['config']['wasabi_storage'] == 1 || $wo['config']['backblaze_storage'] == 1) {
            try {
                $upload = Wo_UploadToS3($file, array(
                    'delete' => 'no'
                ));
            } catch (Exception $e) {
                
            }
        }
    }
    
    foreach ($wo["pro_packages"] as $key => $value) {
        if (!empty($value) && !empty($value['features'])) {
            $r                      = json_decode($value["features"], true);
            $r['can_use_affiliate'] = 1;
            $db->where('id', $value["id"])->update(T_MANAGE_PRO, array(
                'features' => json_encode($r)
            ));
        }
    }
    
    $files = array(
        'themes/wowonder/layout/ads/includes/style.phtml',
        'themes/wowonder/layout/blog/blog-h-list.phtml',
        'themes/wowonder/layout/blog/blog-list.phtml',
        'themes/wowonder/layout/chat/chat-user-list.phtml',
        'themes/wowonder/layout/chat/user-part-list.phtml',
        'themes/wowonder/layout/events/includes/sidbar-events-list.phtml',
        'themes/wowonder/layout/group/members-list.phtml',
        'themes/wowonder/layout/header/og-meta-3.phtml',
        'themes/wowonder/layout/header/share_post.phtml',
        'themes/wowonder/layout/lightbox/share_post_container.phtml',
        'themes/wowonder/layout/messages/players/audio.phtml',
        'themes/wowonder/layout/messages/players/video.phtml',
        'themes/wowonder/layout/modals/language.phtml',
        'themes/wowonder/layout/modals/message-image.phtml',
        'themes/wowonder/layout/products/review_page.phtml',
        'themes/wowonder/layout/setting/includes/adsspentdaily.phtml',
        'themes/wowonder/layout/setting/includes/prosystemtransactions.phtml',
        'themes/wowonder/layout/setting/includes/wallettopup.phtml',
        'themes/wowonder/layout/welcome/pro_register.phtml',
        'themes/wowonder/layout/modals/donate_payment_methods.phtml',
        'themes/sunshine/layout/ads/includes/style.phtml',
        'themes/sunshine/layout/blog/blog-h-list.phtml',
        'themes/sunshine/layout/blog/blog-list.phtml',
        'themes/sunshine/layout/chat/chat-user-list.phtml',
        'themes/sunshine/layout/chat/user-part-list.phtml',
        'themes/sunshine/layout/events/includes/sidbar-events-list.phtml',
        'themes/sunshine/layout/group/members-list.phtml',
        'themes/sunshine/layout/header/og-meta-3.phtml',
        'themes/sunshine/layout/header/share_post.phtml',
        'themes/sunshine/layout/lightbox/share_post_container.phtml',
        'themes/sunshine/layout/messages/players/audio.phtml',
        'themes/sunshine/layout/messages/players/video.phtml',
        'themes/sunshine/layout/modals/language.phtml',
        'themes/sunshine/layout/modals/message-image.phtml',
        'themes/sunshine/layout/products/review_page.phtml',
        'themes/sunshine/layout/setting/includes/adsspentdaily.phtml',
        'themes/sunshine/layout/setting/includes/prosystemtransactions.phtml',
        'themes/sunshine/layout/setting/includes/wallettopup.phtml',
        'themes/sunshine/layout/welcome/pro_register.phtml',
        'themes/wowonder/layout/modals/donate_payment_methods.phtml',
        'admin-panel/pages/mailing-list',
        'assets/libraries/onesignal',
        'assets/libraries/spaces',
        'assets/libraries/s3',
        'assets/libraries/stripe-php-3.20.0',
        'assets/libraries/twilio-php-master',
        'assets/libraries/welcome',
        'assets/libraries/PayPal',
        'assets/libraries/infobip',
        'assets/libraries/google',
        'xhr/pro_register.php',
        'xhr/payu.php',
        'xhr/admincp.php',
        'xhr/upgrade.php',
        'xhr/payment.php',
        'xhr/get_paypal_url.php'
    );
    
    foreach ($files as $key => $value) {
        if (file_exists($value)) {
            if (is_dir($value)) {
                deleteDirectory($value);
            } else {
                @unlink($value);
            }
        }
    }
    
    $lg = array(
        'money_sent_to',
        'sent_you',
        'successfully_received_from'
    );
    foreach ($lg as $key3 => $value3) {
        $lang = $db->where('lang_key', $value3)->getOne(T_LANGS);
        foreach ($lang as $key => $value) {
            if (!empty($value)) {
                $trans = $db->where('notes', '%' . $value . '%', 'LIKE')->get(T_PAYMENT_TRANSACTIONS);
                if (!empty($trans)) {
                    foreach ($trans as $key2 => $value2) {
                        $db->where('id', $value2->id)->update(T_PAYMENT_TRANSACTIONS, array(
                            'notes' => str_replace($value, '', $value2->notes)
                        ));
                    }
                }
            }
        }
    }
    
    
    
    $trans = $db->where('notes', '%Doanted to%', 'LIKE')->get(T_PAYMENT_TRANSACTIONS);
    if (!empty($trans)) {
        foreach ($trans as $key2 => $value2) {
            $db->where('id', $value2->id)->update(T_PAYMENT_TRANSACTIONS, array(
                'notes' => str_replace('Doanted to', '', $value2->notes)
            ));
        }
    }
    
    
    
    $languages_list = array(
        array(
            "name" => "Afrikaans",
            "code" => "af"
        ),
        array(
            "name" => "Albanian - shqip",
            "code" => "sq"
        ),
        array(
            "name" => "Amharic - አማርኛ",
            "code" => "am"
        ),
        array(
            "name" => "Arabic - العربية",
            "code" => "ar"
        ),
        array(
            "name" => "Aragonese - aragonés",
            "code" => "an"
        ),
        array(
            "name" => "Armenian - հայերեն",
            "code" => "hy"
        ),
        array(
            "name" => "Asturian - asturianu",
            "code" => "ast"
        ),
        array(
            "name" => "Azerbaijani - azərbaycan dili",
            "code" => "az"
        ),
        array(
            "name" => "Basque - euskara",
            "code" => "eu"
        ),
        array(
            "name" => "Belarusian - беларуская",
            "code" => "be"
        ),
        array(
            "name" => "Bengali - বাংলা",
            "code" => "bn"
        ),
        array(
            "name" => "Bosnian - bosanski",
            "code" => "bs"
        ),
        array(
            "name" => "Breton - brezhoneg",
            "code" => "br"
        ),
        array(
            "name" => "Bulgarian - български",
            "code" => "bg"
        ),
        array(
            "name" => "Catalan - català",
            "code" => "ca"
        ),
        array(
            "name" => "Central Kurdish - کوردی (دەستنوسی عەرەبی)",
            "code" => "ckb"
        ),
        array(
            "name" => "Chinese - 中文",
            "code" => "zh"
        ),
        array(
            "name" => "Chinese (Hong Kong) - 中文（香港）",
            "code" => "zh-HK"
        ),
        array(
            "name" => "Chinese (Simplified) - 中文（简体）",
            "code" => "zh-CN"
        ),
        array(
            "name" => "Chinese (Traditional) - 中文（繁體）",
            "code" => "zh-TW"
        ),
        array(
            "name" => "Corsican",
            "code" => "co"
        ),
        array(
            "name" => "Croatian - hrvatski",
            "code" => "hr"
        ),
        array(
            "name" => "Czech - čeština",
            "code" => "cs"
        ),
        array(
            "name" => "Danish - dansk",
            "code" => "da"
        ),
        array(
            "name" => "Dutch - Nederlands",
            "code" => "nl"
        ),
        array(
            "name" => "English",
            "code" => "en"
        ),
        array(
            "name" => "English (Australia)",
            "code" => "en-AU"
        ),
        array(
            "name" => "English (Canada)",
            "code" => "en-CA"
        ),
        array(
            "name" => "English (India)",
            "code" => "en-IN"
        ),
        array(
            "name" => "English (New Zealand)",
            "code" => "en-NZ"
        ),
        array(
            "name" => "English (South Africa)",
            "code" => "en-ZA"
        ),
        array(
            "name" => "English (United Kingdom)",
            "code" => "en-GB"
        ),
        array(
            "name" => "English (United States)",
            "code" => "en-US"
        ),
        array(
            "name" => "Esperanto - esperanto",
            "code" => "eo"
        ),
        array(
            "name" => "Estonian - eesti",
            "code" => "et"
        ),
        array(
            "name" => "Faroese - føroyskt",
            "code" => "fo"
        ),
        array(
            "name" => "Filipino",
            "code" => "fil"
        ),
        array(
            "name" => "Finnish - suomi",
            "code" => "fi"
        ),
        array(
            "name" => "French - français",
            "code" => "fr"
        ),
        array(
            "name" => "French (Canada) - français (Canada)",
            "code" => "fr-CA"
        ),
        array(
            "name" => "French (France) - français (France)",
            "code" => "fr-FR"
        ),
        array(
            "name" => "French (Switzerland) - français (Suisse)",
            "code" => "fr-CH"
        ),
        array(
            "name" => "Galician - galego",
            "code" => "gl"
        ),
        array(
            "name" => "Georgian - ქართული",
            "code" => "ka"
        ),
        array(
            "name" => "German - Deutsch",
            "code" => "de"
        ),
        array(
            "name" => "German (Austria) - Deutsch (Österreich)",
            "code" => "de-AT"
        ),
        array(
            "name" => "German (Germany) - Deutsch (Deutschland)",
            "code" => "de-DE"
        ),
        array(
            "name" => "German (Liechtenstein) - Deutsch (Liechtenstein)",
            "code" => "de-LI"
        ),
        array(
            "name" => "German (Switzerland) - Deutsch (Schweiz)",
            "code" => "de-CH"
        ),
        array(
            "name" => "Greek - Ελληνικά",
            "code" => "el"
        ),
        array(
            "name" => "Guarani",
            "code" => "gn"
        ),
        array(
            "name" => "Gujarati - ગુજરાતી",
            "code" => "gu"
        ),
        array(
            "name" => "Hausa",
            "code" => "ha"
        ),
        array(
            "name" => "Hawaiian - ʻŌlelo Hawaiʻi",
            "code" => "haw"
        ),
        array(
            "name" => "Hebrew - עברית",
            "code" => "he"
        ),
        array(
            "name" => "Hindi - हिन्दी",
            "code" => "hi"
        ),
        array(
            "name" => "Hungarian - magyar",
            "code" => "hu"
        ),
        array(
            "name" => "Icelandic - íslenska",
            "code" => "is"
        ),
        array(
            "name" => "Indonesian - Indonesia",
            "code" => "id"
        ),
        array(
            "name" => "Interlingua",
            "code" => "ia"
        ),
        array(
            "name" => "Irish - Gaeilge",
            "code" => "ga"
        ),
        array(
            "name" => "Italian - italiano",
            "code" => "it"
        ),
        array(
            "name" => "Italian (Italy) - italiano (Italia)",
            "code" => "it-IT"
        ),
        array(
            "name" => "Italian (Switzerland) - italiano (Svizzera)",
            "code" => "it-CH"
        ),
        array(
            "name" => "Japanese - 日本語",
            "code" => "ja"
        ),
        array(
            "name" => "Kannada - ಕನ್ನಡ",
            "code" => "kn"
        ),
        array(
            "name" => "Kazakh - қазақ тілі",
            "code" => "kk"
        ),
        array(
            "name" => "Khmer - ខ្មែរ",
            "code" => "km"
        ),
        array(
            "name" => "Korean - 한국어",
            "code" => "ko"
        ),
        array(
            "name" => "Kurdish - Kurdî",
            "code" => "ku"
        ),
        array(
            "name" => "Kyrgyz - кыргызча",
            "code" => "ky"
        ),
        array(
            "name" => "Lao - ລາວ",
            "code" => "lo"
        ),
        array(
            "name" => "Latin",
            "code" => "la"
        ),
        array(
            "name" => "Latvian - latviešu",
            "code" => "lv"
        ),
        array(
            "name" => "Lingala - lingála",
            "code" => "ln"
        ),
        array(
            "name" => "Lithuanian - lietuvių",
            "code" => "lt"
        ),
        array(
            "name" => "Macedonian - македонски",
            "code" => "mk"
        ),
        array(
            "name" => "Malay - Bahasa Melayu",
            "code" => "ms"
        ),
        array(
            "name" => "Malayalam - മലയാളം",
            "code" => "ml"
        ),
        array(
            "name" => "Maltese - Malti",
            "code" => "mt"
        ),
        array(
            "name" => "Marathi - मराठी",
            "code" => "mr"
        ),
        array(
            "name" => "Mongolian - монгол",
            "code" => "mn"
        ),
        array(
            "name" => "Nepali - नेपाली",
            "code" => "ne"
        ),
        array(
            "name" => "Norwegian - norsk",
            "code" => "no"
        ),
        array(
            "name" => "Norwegian Bokmål - norsk bokmål",
            "code" => "nb"
        ),
        array(
            "name" => "Norwegian Nynorsk - nynorsk",
            "code" => "nn"
        ),
        array(
            "name" => "Occitan",
            "code" => "oc"
        ),
        array(
            "name" => "Oriya - ଓଡ଼ିଆ",
            "code" => "or"
        ),
        array(
            "name" => "Oromo - Oromoo",
            "code" => "om"
        ),
        array(
            "name" => "Pashto - پښتو",
            "code" => "ps"
        ),
        array(
            "name" => "Persian - فارسی",
            "code" => "fa"
        ),
        array(
            "name" => "Polish - polski",
            "code" => "pl"
        ),
        array(
            "name" => "Portuguese - português",
            "code" => "pt"
        ),
        array(
            "name" => "Portuguese (Brazil) - português (Brasil)",
            "code" => "pt-BR"
        ),
        array(
            "name" => "Portuguese (Portugal) - português (Portugal)",
            "code" => "pt-PT"
        ),
        array(
            "name" => "Punjabi - ਪੰਜਾਬੀ",
            "code" => "pa"
        ),
        array(
            "name" => "Quechua",
            "code" => "qu"
        ),
        array(
            "name" => "Romanian - română",
            "code" => "ro"
        ),
        array(
            "name" => "Romanian (Moldova) - română (Moldova)",
            "code" => "mo"
        ),
        array(
            "name" => "Romansh - rumantsch",
            "code" => "rm"
        ),
        array(
            "name" => "Russian - русский",
            "code" => "ru"
        ),
        array(
            "name" => "Scottish Gaelic",
            "code" => "gd"
        ),
        array(
            "name" => "Serbian - српски",
            "code" => "sr"
        ),
        array(
            "name" => "Serbo - Croatian",
            "code" => "sh"
        ),
        array(
            "name" => "Shona - chiShona",
            "code" => "sn"
        ),
        array(
            "name" => "Sindhi",
            "code" => "sd"
        ),
        array(
            "name" => "Sinhala - සිංහල",
            "code" => "si"
        ),
        array(
            "name" => "Slovak - slovenčina",
            "code" => "sk"
        ),
        array(
            "name" => "Slovenian - slovenščina",
            "code" => "sl"
        ),
        array(
            "name" => "Somali - Soomaali",
            "code" => "so"
        ),
        array(
            "name" => "Southern Sotho",
            "code" => "st"
        ),
        array(
            "name" => "Spanish - español",
            "code" => "es"
        ),
        array(
            "name" => "Spanish (Argentina) - español (Argentina)",
            "code" => "es-AR"
        ),
        array(
            "name" => "Spanish (Latin America) - español (Latinoamérica)",
            "code" => "es-419"
        ),
        array(
            "name" => "Spanish (Mexico) - español (México)",
            "code" => "es-MX"
        ),
        array(
            "name" => "Spanish (Spain) - español (España)",
            "code" => "es-ES"
        ),
        array(
            "name" => "Spanish (United States) - español (Estados Unidos)",
            "code" => "es-US"
        ),
        array(
            "name" => "Sundanese",
            "code" => "su"
        ),
        array(
            "name" => "Swahili - Kiswahili",
            "code" => "sw"
        ),
        array(
            "name" => "Swedish - svenska",
            "code" => "sv"
        ),
        array(
            "name" => "Tajik - тоҷикӣ",
            "code" => "tg"
        ),
        array(
            "name" => "Tamil - தமிழ்",
            "code" => "ta"
        ),
        array(
            "name" => "Tatar",
            "code" => "tt"
        ),
        array(
            "name" => "Telugu - తెలుగు",
            "code" => "te"
        ),
        array(
            "name" => "Thai - ไทย",
            "code" => "th"
        ),
        array(
            "name" => "Tigrinya - ትግርኛ",
            "code" => "ti"
        ),
        array(
            "name" => "Tongan - lea fakatonga",
            "code" => "to"
        ),
        array(
            "name" => "Turkish - Türkçe",
            "code" => "tr"
        ),
        array(
            "name" => "Turkmen",
            "code" => "tk"
        ),
        array(
            "name" => "Twi",
            "code" => "tw"
        ),
        array(
            "name" => "Ukrainian - українська",
            "code" => "uk"
        ),
        array(
            "name" => "Urdu - اردو",
            "code" => "ur"
        ),
        array(
            "name" => "Uyghur",
            "code" => "ug"
        ),
        array(
            "name" => "Uzbek - o‘zbek",
            "code" => "uz"
        ),
        array(
            "name" => "Vietnamese - Tiếng Việt",
            "code" => "vi"
        ),
        array(
            "name" => "Walloon - wa",
            "code" => "wa"
        ),
        array(
            "name" => "Welsh - Cymraeg",
            "code" => "cy"
        ),
        array(
            "name" => "Western Frisian",
            "code" => "fy"
        ),
        array(
            "name" => "Xhosa",
            "code" => "xh"
        ),
        array(
            "name" => "Yiddish",
            "code" => "yi"
        ),
        array(
            "name" => "Yoruba - Èdè Yorùbá",
            "code" => "yo"
        ),
        array(
            "name" => "Zulu - isiZulu",
            "code" => "zu"
        )
    );
    $a              = array();
    foreach ($languages_list as $key => $value) {
        $pieces = explode("-", $value['name']);
        if (!empty($pieces)) {
            foreach ($pieces as $key2 => $value2) {
                $a[trim(strtolower($value2))] = trim(strtolower($value['code']));
            }
        }
    }
    foreach ($all_langs as $key => $value) {
        if (in_array($value, array_keys($a))) {
            $count = $db->where('lang_name', $value)->getValue(T_LANG_ISO, 'COUNT(*)');
            if ($count > 0) {
                $db->where('lang_name', $value)->update(T_LANG_ISO, array(
                    'iso' => $a[$value]
                ));
            } else {
                $db->insert(T_LANG_ISO, array(
                    'lang_name' => $value,
                    'iso' => $a[$value]
                ));
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
                     <h2 class="light">Update to v4.1.2</span></h2>
                     <div class="setting-well">
                        <h4>Changelog</h4>
                        <ul class="wo_update_changelog">
                          <li> [Added] new emojis to messages and posts.</li>
                              <li> [Added] drag and drop to upload stories page.</li>
                              <li> [Added] unseen effect to stories.</li>
                              <li> [Added] new video player.</li>
                              <li> [Added] video player for movies.</li>
                              <li> [Added] drag and drop to create an album page.</li>
                              <li> [Added] audio player on chat when sending a recording file.</li>
                              <li> [Added] new dropdown menu on header, post and profile.</li>
                              <li> [Added] new upgrade to pro system. Now users can upgrade to another pro package using the same go pro page instead of using settings page.</li>
                              <li> [Added] new cron-job.php file, (should be added).</li>
                              <li> [Added] ISO system for langs.</li>
                              <li> [Added] Href SEO meta tags.</li>
                              <li> [Added] BACKBLAZE stroage.</li>
                              <li> [Added] the ability to upload files from server directly to third party stroages like S3.</li>
                              <li> [Added] custom endpoint for each storage (CDN support).</li>
                              <li> [Added] new reaction icons.</li>
                              <li> [Added] stories unseen border colored animation (profile page).</li>
                              <li> [Added] 10+ more regions to Wasabi, Ocean and Amazon.</li>
                              <li> [Added] the ability to choose who can use the affiliate system.</li>
                              <li> [Added] the ability to edit forum sections and forum names.</li>
                              <li> [Added] the ability to set custom minimum withdrawal amount.</li>
                              <li> [Added] "Add Photo" button to post publisher box after choosing an image.</li>
                              <li> [Added] Google Translate API for posts.</li>
                              <li> [Added] "Email Deliverability" system to debug and check the log of SMTP settings. </li>
                              <li> [Added] Hindi, Urdu, Chine, Indonesian, Croatian, Hebrew, Bengali, Japanese, Portuguese, Italian, Persian, Swedish, Vietnamese, Danish, and Filipino languages.</li>
                              <li> [Improved] design / colors in default theme for few sections.</li>
                              <li> [Improved] code security.</li>
                              <li> [Removed] 10+ unused files.</li>
                              <li> [Updated] loading icon for notification dropdown.</li>
                              <li> [Updated] design of user list in right sidebar in user profile page .</li>
                              <li> [Updated] movies watch page design.</li>
                              <li> [Updated] funding page design.</li>
                              <li> [Updated] date selector for events, profile and other sections.</li>
                              <li> [Updated] user settings page.</li>
                              <li> [Updated] keyboard shortcuts model design.</li>
                              <li> [Updated] header icons.</li>
                              <li> [Updated] "Profile Completion" design.</li>
                              <li> [Updated] page design.</li>
                              <li> [Updated] start-up page. </li>
                              <li> [Updated] chat tab design.</li>
                              <li> [Updated] wallet page design.</li>
                              <li> [Updated] design in few section in sunshine theme.</li>
                              <li> [Fixed] reply to messages on sunshine theme (nodejs).</li>
                              <li> [Fixed] video / audio call dialog not closing if there are multiple tabs.</li>
                              <li> [Fixed] "mark all messages" as read button was hanging.</li>
                              <li> [Fixed] stickers not working on nodejs.</li>
                              <li> [Fixed] FTP storage was not working on nodejs.</li>
                              <li> [Fixed] SEO links on movies, added movie title to movie link instead of using only /ID</li>
                              <li> [Fixed] showing dollar sign on points page even if you have a different currency.</li>
                              <li> [Fixed] clicking on "Advanced Search" was redirecting to 404 page.</li>
                              <li> [Fixed] create blog link could be accessed even if the permission is not allowed.</li>
                              <li> [Fixed] points were not added if the blog approval system was enabled.</li>
                              <li> [Fixed] blog is not deleted if you delete the blog post.</li>
                              <li> [Fixed] Agora live streaming.</li>
                              <li> [Fixed] Wallet + Balance were showing on "My Earnings" page.</li>
                              <li> [Fixed] group chat notifications (nodejs).</li>
                              <li> [Fixed] br tags showing on post title.</li>
                              <li> [Fixed] video files are not deleting from the server (FFmpeg)</li>
                              <li> [Fixed] promoted pages/posts were not reset after the pro membership is ended.</li>
                              <li> [Fixed] 3 nodejs warning messages.</li>
                              <li> [Fixed] comment is not working when using the french language.</li>
                              <li> [Fixed] the ability to upload multiple images on the product.</li>
                              <li> [Fixed] wasabi not working on default east region.</li>
                              <li> [Fixed] UTF-8 characters not working on games.</li>
                              <li> [Fixed] "Max Upload Size" not updating from admin panel.</li>
                              <li> [Fixed] user can upload audio files even if the audio upload is not allowed for the user group.</li>
                              <li> [Fixed] weather not showing on the sunshine theme.</li>
                              <li> [Fixed] audio recording upload using nodejs from chat and messages page.</li>
                              <li> [Fixed] can't post if post approval system enabled from admin panel. </li>
                              <li> [Fixed] 50+ design and frontend minor bugs.</li>
                              <li> [Fixed] 10+ more minor bugs.</li>
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
    "UPDATE `Wo_Config` SET `value` = '4.1.2' WHERE `name` = 'version';",
    "CREATE TABLE `Wo_LangIso` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `lang_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `iso` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `image` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , PRIMARY KEY (`id`), INDEX (`lang_name`), INDEX (`iso`), INDEX (`image`)) ENGINE = InnoDB;",
    "CREATE TABLE `Wo_PendingPayments` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL DEFAULT '0' , `payment_data` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `method_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`user_id`), INDEX (`payment_data`), INDEX (`method_name`), INDEX (`time`)) ENGINE = InnoDB;",
    "ALTER TABLE `Wo_Users`  DROP `StripeSessionId`,  DROP `coinbase_hash`,  DROP `coinbase_code`,  DROP `yoomoney_hash`,  DROP `ConversationId`,  DROP `securionpay_key`,  DROP `fortumo_hash`,  DROP `aamarpay_tran_id`,  DROP `ngenius_ref`,  DROP `coinpayments_txn_id`;",
    "  INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'affiliate_request', 'all');",
    "ALTER TABLE `Wo_Games` CHANGE `game_name` `game_name` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;",
    "ALTER TABLE `Wo_UserAds` CHANGE `end` `end` VARCHAR(50) NOT NULL DEFAULT '';",
    "ALTER TABLE `Wo_UserAds` CHANGE `start` `start` VARCHAR(50) NOT NULL DEFAULT '';",
    "CREATE TABLE `Wo_UploadedMedia` ( `id` INT NOT NULL AUTO_INCREMENT , `filename` VARCHAR(200) NOT NULL DEFAULT '' , `time` INT NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`filename`), INDEX (`time`)) ENGINE = InnoDB;",
    "ALTER TABLE `Wo_UploadedMedia` ADD `storage` VARCHAR(34) NOT NULL AFTER `filename`;",
    "ALTER TABLE `Wo_UploadedMedia` ADD INDEX( `filename`, `storage`);",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'yandex_translate', '0');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'google_translate', '0');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'google_translation_api', '');",
    "ALTER TABLE `Wo_Users` ADD `pro_remainder` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `pro_type`, ADD INDEX (`pro_remainder`);",
    "ALTER TABLE `Wo_Funding` CHANGE `description` `description` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;",
    "ALTER TABLE `Wo_Users` CHANGE `ip_address` `ip_address` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '';",
    "ALTER TABLE `Wo_Banned_Ip` CHANGE `ip_address` `ip_address` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';",
    "UPDATE `Wo_Reactions_Types` SET `wowonder_icon` = 'upload/files/2022/09/EAufYfaIkYQEsYzwvZha_01_4bafb7db09656e1ecb54d195b26be5c3_file.svg' WHERE `name` = 'like';",
    "UPDATE `Wo_Reactions_Types` SET `wowonder_icon` = 'upload/files/2022/09/2MRRkhb7rDhUNuClfOfc_01_76c3c700064cfaef049d0bb983655cd4_file.svg' WHERE `name` = 'love';",
    "UPDATE `Wo_Reactions_Types` SET `wowonder_icon` = 'upload/files/2022/09/D91CP5YFfv74GVAbYtT7_01_288940ae12acf0198d590acbf11efae0_file.svg' WHERE `name` = 'haha';",
    "UPDATE `Wo_Reactions_Types` SET `wowonder_icon` = 'upload/files/2022/09/cFNOXZB1XeWRSdXXEdlx_01_7d9c4adcbe750bfc8e864c69cbed3daf_file.svg' WHERE `name` = 'wow';",
    "UPDATE `Wo_Reactions_Types` SET `wowonder_icon` = 'upload/files/2022/09/yKmDaNA7DpA7RkCRdoM6_01_eb391ca40102606b78fef1eb70ce3c0f_file.svg' WHERE `name` = 'sad';",
    "UPDATE `Wo_Reactions_Types` SET `wowonder_icon` = 'upload/files/2022/09/iZcVfFlay3gkABhEhtVC_01_771d67d0b8ae8720f7775be3a0cfb51a_file.svg' WHERE `name` = 'angry';",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'backblaze_storage', '0');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'backblaze_bucket_id', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'backblaze_bucket_endpoint', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'backblaze_access_key_id', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'backblaze_access_key', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'backblaze_bucket_name', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'amazon_endpoint', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'spaces_endpoint', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'wasabi_endpoint', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'backblaze_endpoint', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'cloud_endpoint', '');",
    "ALTER TABLE `Wo_Users` ADD `converted_points` FLOAT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `daily_points`, ADD INDEX (`converted_points`);",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'cronjob_last_run', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'backblaze_bucket_region', '');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'weeks');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'ago');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'drop_img_videos_here');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'max_number_status_6');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'please_select_a_media_file');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'sales_trans');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'pro_trans');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'sent_trans');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'wallet_trans');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'received_trans');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'sale_trans');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'purchase_trans');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'coinpayments');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'stripe');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'trans_doanted_to');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'trans_money_sent_to');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'trans_successfully_received_from');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'trans_upgrade_to_pro');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'manage_subscription');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'current');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'edit_post_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'delete_post_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'comments_status_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'open_post_in_new_tab_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'report_post_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'hide_post_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'only_me_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'everyone_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'my_friends_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'people_i_follow_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'people_follow_me_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'liked_my_page_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'edit_offer_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'mark_as_sold_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'edit_product_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'pin_post_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'add_photos_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'boost_post_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'unboost_post_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'save_post_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'hiring_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'anonymous_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'all_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'block_user_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'report_user_text_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'unreport_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'poke_user_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'add_to_family_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'edit_tx');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'loading');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'show_original');",

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

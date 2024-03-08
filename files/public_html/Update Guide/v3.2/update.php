<?php
if (file_exists('assets/init.php')) {
    require 'assets/init.php';
} else {
    die('Please put this file in the home directory !');
}
if (!file_exists("SQL_3-2.sql")) {
    die('Please upload the file SQL_3-2.sql located in ./Update Guide/v3.2/Script');
    
}
ini_set('max_execution_time', 0);
function check_($check) {
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
    function Wo_UpdateLangs($lang, $key, $value) {
        global $sqlConnect;
        $update_query         = "UPDATE Wo_Langs SET `{lang}` = '{lang_text}' WHERE `lang_key` = '{lang_key}'";
        $update_replace_array = array(
            "{lang}",
            "{lang_text}",
            "{lang_key}"
        );
        return str_replace($update_replace_array, array(
            $lang,
            Wo_Secure($value),
            $key
        ), $update_query);
    }
    $lang_update_queries = array();
    foreach ($data as $key => $value) {
        $value = ($value);
        if ($value == 'arabic') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'terms_contact', 'أتفق مع {الخصوصية} لتلقي الاتصالات.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'first_name_last_name_empty', 'اسمك الأول واسم الأخير لا يمكن أن يكون فارغا.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_message', 'الرد على الرسالة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_story', 'كان رد فعل قصتك.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_story', 'الرد على القصة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_message', 'كان رد فعل على رسالتك.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_story', 'الرد على القصة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ffmpeg_file_text', 'يتم معالجة الفيديو الخاص بك، وسوف نعلمك عندما تكون جاهزة للعرض.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'video_ready_to_view', 'الفيديو الخاص بك جاهز للعرض. ');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'processing_video', 'لا يزال يتم تحويل الفيديو إلى قرارات أخرى، يرجى الانتظار أكثر قليلا لمحتوى HD.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_story', 'عرض القصة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_to', 'الرد على');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'see_trending_people', 'نرى ما يتحدث الناس عنه.');
        } else if ($value == 'dutch') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'terms_contact', 'Mee eens met {privacy} om communicatie te ontvangen.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'first_name_last_name_empty', 'Uw voornaam en achternaam kunnen niet leeg zijn.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_message', 'Antwoord op bericht');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_story', 'reageerde op je verhaal.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_story', 'Antwoord op verhaal');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_message', 'reageerde op uw bericht.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_story', 'Antwoorden op verhaal');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ffmpeg_file_text', 'Je video wordt verwerkt, we laten je weten wanneer het klaar is om te bekijken.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'video_ready_to_view', 'Je video is klaar om te bekijken. ');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'processing_video', 'De video wordt nog steeds geconverteerd naar andere resoluties, wacht een beetje meer voor HD-inhoud.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_story', 'Beeldverhaal');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_to', 'Antwoorden op');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'see_trending_people', 'Kijk waar mensen het over hebben.');
        } else if ($value == 'french') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'terms_contact', 'd\'accord avec {Confidentialité} pour recevoir des communications.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'first_name_last_name_empty', 'Votre prénom et votre nom de famille ne peuvent pas être vides.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_message', 'Répondre au message');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_story', 'réagi à votre histoire.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_story', 'Répondre à l\'histoire');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_message', 'réagi à votre message.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_story', 'Répondre à l\'histoire');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ffmpeg_file_text', 'Votre vidéo est en cours de traitement, nous vous ferons savoir quand il est prêt à voir.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'video_ready_to_view', 'Votre vidéo est prête à voir. ');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'processing_video', 'La vidéo est toujours en cours de convertie en d\'autres résolutions, veuillez patienter un peu plus pour le contenu HD.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_story', 'Histoire');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_to', 'Répondre à');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'see_trending_people', 'Voir ce que les gens parlent de.');
        } else if ($value == 'german') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'terms_contact', 'stimmen Sie mit {Privacy} zu, um die Kommunikation zu erhalten.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'first_name_last_name_empty', 'Ihr Vorname und der Nachname können nicht leer sein.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_message', 'Antwort auf Nachricht.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_story', 'reagierte auf deine Geschichte.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_story', 'Antwort auf die Geschichte');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_message', 'reagierte auf Ihre Nachricht.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_story', 'Antwort auf die Geschichte');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ffmpeg_file_text', 'Ihr Video wird verarbeitet, wir informieren Sie, wann es zum Anzeigen bereit ist.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'video_ready_to_view', 'Ihr Video ist bereit zu sehen. ');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'processing_video', 'Das Video wird immer noch in andere Auflösungen umgewandelt, warten Sie bitte ein bisschen mehr für HD-Inhalte.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_story', 'Geschichte anzeigen.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_to', 'Antwort auf');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'see_trending_people', 'Sehen Sie, worüber Menschen sprechen.');
        } else if ($value == 'italian') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'terms_contact', 'concordare con {privacy} per ricevere le comunicazioni.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'first_name_last_name_empty', 'Il tuo nome e cognome non possono essere vuoti.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_message', 'Rispondi al messaggio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_story', 'reagito alla tua storia.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_story', 'Rispondi alla storia');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_message', 'reagito al tuo messaggio.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_story', 'Rispondendo alla storia');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ffmpeg_file_text', 'Il tuo video viene elaborato, ti faremo sapere quando è pronto per la visualizzazione.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'video_ready_to_view', 'Il tuo video è pronto per visualizzare. ');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'processing_video', 'Il video è ancora convertito in altre risoluzioni, attendere un po \'di più per il contenuto HD.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_story', 'Visualizza la storia');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_to', 'Rispondendo a.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'see_trending_people', 'Guarda di cosa parlano le persone.');
        } else if ($value == 'portuguese') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'terms_contact', 'Concordo com {privacy} para receber comunicações.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'first_name_last_name_empty', 'Seu primeiro nome e sobrenome não podem estar vazios.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_message', 'Responder à mensagem.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_story', 'reagiu à sua história.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_story', 'Responder à história');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_message', 'reagiu à sua mensagem.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_story', 'Respondendo à história');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ffmpeg_file_text', 'Seu vídeo está sendo processado, informaremos você quando estiver pronto para ver.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'video_ready_to_view', 'Seu vídeo está pronto para visualizar. ');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'processing_video', 'O vídeo ainda está sendo convertido em outras resoluções, aguarde um pouco mais para conteúdo HD.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_story', 'Ver a história');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_to', 'Respondendo a');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'see_trending_people', 'Veja o que as pessoas estão falando.');
        } else if ($value == 'russian') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'terms_contact', 'согласен с {конфиденциальность} для получения коммуникаций.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'first_name_last_name_empty', 'Ваше имя и фамилия не могут быть пустыми.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_message', 'Ответить на сообщение');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_story', 'отреагировал на вашу историю.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_story', 'Ответить на историю');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_message', 'отреагировал на ваше сообщение.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_story', 'Отвечая на историю');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ffmpeg_file_text', 'Ваше видео обрабатывается, мы сообщим вам, когда он будет готов к просмотру.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'video_ready_to_view', 'Ваше видео готово к просмотру. ');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'processing_video', 'Видео все еще преобразуется в другие резолюции, пожалуйста, подождите немного больше для HD Content.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_story', 'Вид истории');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_to', 'Отвечать');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'see_trending_people', 'Посмотрите, о чем говорят люди.');
        } else if ($value == 'spanish') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'terms_contact', 'De acuerdo con {Privacidad} para recibir comunicaciones.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'first_name_last_name_empty', 'Su nombre y apellido no pueden estar vacíos.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_message', 'Responder al mensaje');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_story', 'Reaccionó a tu historia.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_story', 'Responder a la historia');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_message', 'reaccionó a tu mensaje.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_story', 'Respondiendo a la historia');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ffmpeg_file_text', 'Se está procesando su video, le informaremos cuando esté listo para ver.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'video_ready_to_view', 'Tu video está listo para ver. ');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'processing_video', 'El video aún se está convirtiendo a otras resoluciones, espere un poco más para el contenido de HD.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_story', 'Ver historia');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_to', 'Respondiendo a');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'see_trending_people', 'Mira de qué están hablando la gente.');
        } else if ($value == 'turkish') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'terms_contact', 'iletişim almak için {gizlilik} ile katılıyorum.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'first_name_last_name_empty', 'İlk adınız ve soyadınız boş olamaz.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_message', 'Mesajı Yanıtla');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_story', 'hikayenize tepki gösterdi.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_story', 'Hikaye Cevapla');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_message', 'mesajınıza tepki gösterdi.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_story', 'Hikayeye cevap vermek');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ffmpeg_file_text', 'Videonuz işleniyor, ne zaman görüntülenmeye hazır olduğunda size haber vereceğiz.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'video_ready_to_view', 'Videonuz görüntülenmeye hazır. ');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'processing_video', 'Video hala diğer kararlara dönüştürülüyor, lütfen HD içerik için biraz daha bekleyin.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_story', 'Hikayeyi görüntüle');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_to', 'Cevap vermek');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'see_trending_people', 'İnsanların neden bahsettiğini görün.');
        } else if ($value == 'english') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'terms_contact', 'agree with {privacy} to receive communications.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'first_name_last_name_empty', 'Your First Name and Last Name can not be empty.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_message', 'Reply To Message');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_story', 'reacted to your story.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_story', 'Reply To Story');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_message', 'reacted to your message.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_story', 'Replying to story');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ffmpeg_file_text', 'Your video is being processed, We’ll let you know when it\'s ready to view.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'video_ready_to_view', 'Your video is ready to view. You can now watch it.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'processing_video', 'The video is still being converted to other resolutions, please wait a bit more for HD content.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_story', 'View Story');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_to', 'Replying to');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'see_trending_people', 'See what people are talking about.');
        } else if ($value != 'english') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'terms_contact', 'agree with {privacy} to receive communications.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'first_name_last_name_empty', 'Your First Name and Last Name can not be empty.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_message', 'Reply To Message');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_story', 'reacted to your story.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reply_story', 'Reply To Story');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_to_your_message', 'reacted to your message.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_story', 'Replying to story');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ffmpeg_file_text', 'Your video is being processed, We’ll let you know when it\'s ready to view.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'video_ready_to_view', 'Your video is ready to view. You can now watch it.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'processing_video', 'The video is still being converted to other resolutions, please wait a bit more for HD content.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'view_story', 'View Story');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'replying_to', 'Replying to');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'see_trending_people', 'See what people are talking about.');
        }
    }
    if (!empty($lang_update_queries)) {
        foreach ($lang_update_queries as $key => $query) {
            $sql = mysqli_query($sqlConnect, $query);
        }
        $mysqlHTMLCODE = file_get_contents('SQL_3-2.sql');
        if ($mysqlHTMLCODE) {
            $sql = mysqli_query($sqlConnect, $mysqlHTMLCODE);
            if ($sql) {
                @unlink('SQL_3-2.sql');
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
                     <h2 class="light">Update to v3.2</span></h2>
                     <div class="setting-well">
                        <h4>Changelog</h4>
                        <ul class="wo_update_changelog">
                                <li> [Added] ability to edit and translate emails from admin panel, under Tools > Manage E-mails.</li>
                                <li> [Added] expire time for reset password links, 12 hours.</li>
                                <li> [Added] FFMPEG for video conversation + thumbnail generation + debug ffmpeg feature from admin panel + up to 4K video support, (Enable / Disable).</li>
                                <li> [Added] auto username generator, for new user registration users are not required to write their username. (Enable / Disable)</li>
                                <li> [Added] new APIs, more than 40+.</li>
                                <li> [Added] reactions for messages, (ajax / nodejs). </li>
                                <li> [Added] reply system for messages, (ajax / nodejs). </li>
                                <li> [Added] reactions system for stories. </li>
                                <li> [Added] reply system for stories. </li>
                                <li> [Added] gifs + stickers to comment system. </li>
                                <li> [Added] gifs + stickers to reply system. </li>
                                <li> [Added] video chat by Agora. </li>
                                <li> [Added] terms of use checkbox to contact us page. </li>
                                <li> [Added] the ability to delete poll answers while creating one.</li>
                                <li> [Added] login with Mailru, Discord, WeChat and QQ.</li>
                                <li> [RE-ADDED] script version in admin panel sidebar.</li>
                                <li> [Updated] twilio video chat system. </li>
                                <li> [Updated] message seen systemn (NodeJS). </li>
                                <li> [Fixed] 80+ reported bugs and improvments.</li>
                                <li> [Fixed] bugs in API.</li>
                        </ul>
                        <p class="hide_print">Note: The update process might take few minutes.</p>
                        <p class="hide_print">Important: If you got any fail queries, please copy them, open a support ticket and send us the details.</p>
                        <p class="hide_print">Most of the features are disabled by default, you can enable them from Admin > Site Settings > Manage Site Features, reaction can be enabled from Settings > Site Sttings.</p><br>
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
    "UPDATE `Wo_Config` SET `value` = '3.2' WHERE `name` = 'version';",
    "ALTER TABLE `Wo_Messages` ADD `reply_id` INT(11) NOT NULL DEFAULT '0' AFTER `lng`;",
    "ALTER TABLE `Wo_Messages` ADD `story_id` INT(11) NOT NULL DEFAULT '0' AFTER `reply_id`;",
    "CREATE TABLE `Wo_Mute` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `chat_id` INT(11) NOT NULL DEFAULT '0' , `user_id` INT(11) NOT NULL DEFAULT '0' , `notify` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'yes' , `call_chat` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'yes' , `archive` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'yes' , `pin` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'yes' , `type` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`)) ENGINE = InnoDB;",
    "CREATE TABLE `Wo_Mute_Story` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL DEFAULT '0' , `story_user_id` INT(11) NOT NULL DEFAULT '0' , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`)) ENGINE = InnoDB;",
    "ALTER TABLE `Wo_Reactions` ADD `message_id` INT(11) NOT NULL DEFAULT '0' AFTER `replay_id`, ADD `story_id` INT(11) NOT NULL DEFAULT '0' AFTER `message_id`;",
    "ALTER TABLE `Wo_Mute` ADD `message_id` INT(11) NOT NULL DEFAULT '0' AFTER `chat_id`;",
    "CREATE TABLE `broadcast` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL DEFAULT '0' , `name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `image` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`)) ENGINE = InnoDB;",
    "CREATE TABLE `broadcast_users` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL DEFAULT '0' , `broadcast_id` INT(11) NOT NULL DEFAULT '0' , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`)) ENGINE = InnoDB;",
    "ALTER TABLE `broadcast` CHANGE `image` `image` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'upload/photos/d-group.jpg';",
    "ALTER TABLE `broadcast` ADD INDEX(`user_id`);",
    "ALTER TABLE `broadcast` ADD INDEX(`time`);",
    "ALTER TABLE `Wo_Mute` ADD INDEX(`chat_id`);",
    "ALTER TABLE `Wo_Mute` ADD INDEX(`message_id`);",
    "ALTER TABLE `Wo_Mute` ADD INDEX(`user_id`);",
    "ALTER TABLE `Wo_Mute` ADD INDEX(`time`);",
    "ALTER TABLE `Wo_Mute_Story` ADD INDEX(`story_user_id`);",
    "ALTER TABLE `Wo_Mute_Story` ADD INDEX(`user_id`);",
    "ALTER TABLE `Wo_Mute_Story` ADD INDEX(`time`);",
    "ALTER TABLE `Wo_Messages` ADD `broadcast_id` INT(11) NOT NULL DEFAULT '0' AFTER `story_id`;",
    "ALTER TABLE `Wo_UserStory` CHANGE `title` `title` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '';",
    "ALTER TABLE `Wo_UserStory` CHANGE `description` `description` VARCHAR(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '';",
    "ALTER TABLE `Wo_Polls` CHANGE `text` `text` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '';",
    "ALTER TABLE `Wo_Users` ADD `time_code_sent` INT(11) NOT NULL DEFAULT '0' AFTER `StripeSessionId`;",
    "CREATE TABLE `Wo_HTML_Emails` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `value` TEXT NULL DEFAULT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('auto_username', '0');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('agora_app_certificate', '');",
    "ALTER TABLE `Wo_Posts` ADD `agora_token` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `stream_name`;",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('agora_chat_video', '0');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('agora_chat_app_id', '');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('agora_chat_app_certificate', '');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('agora_chat_customer_id', '');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('agora_chat_customer_certificate', '');",
    "ALTER TABLE `Wo_AgoraVideoCall` ADD `active` INT(11) NOT NULL DEFAULT '0' AFTER `status`, ADD `called` INT(11) NOT NULL DEFAULT '0' AFTER `active`, ADD `declined` INT(11) NOT NULL DEFAULT '0' AFTER `called`;",
    "ALTER TABLE `Wo_AgoraVideoCall` ADD `access_token` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `declined`, ADD `access_token_2` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `access_token`;",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('qqLogin', '0');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('qqAppId', '');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('qqAppkey', '');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('WeChatLogin', '0');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('WeChatAppId', '');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('WeChatAppkey', '');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('DiscordLogin', '0');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('DiscordAppId', '');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('DiscordAppkey', '');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('MailruLogin', '0');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('MailruAppId', '');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('MailruAppkey', '');",
    "ALTER TABLE `Wo_Users` ADD `qq` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `instagram`, ADD `wechat` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `qq`, ADD `discord` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `wechat`, ADD `mailru` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `discord`;",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('twilio_video_chat', '0');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('ffmpeg_binary_file', './ffmpeg/ffmpeg');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('ffmpeg_system', 'off');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('convert_speed', 'fast');",
    "ALTER TABLE `Wo_Posts` ADD `240p` INT(2) NOT NULL DEFAULT '0' AFTER `send_notify`, ADD `360p` INT(2) NOT NULL DEFAULT '0' AFTER `240p`, ADD `480p` INT(2) NOT NULL DEFAULT '0' AFTER `360p`, ADD `720p` INT(2) NOT NULL DEFAULT '0' AFTER `480p`, ADD `1080p` INT(2) NOT NULL DEFAULT '0' AFTER `720p`, ADD `2048p` INT(2) NOT NULL DEFAULT '0' AFTER `1080p`, ADD `4096p` INT(2) NOT NULL DEFAULT '0' AFTER `2048p`;",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('allowedffmpegExtenstion', 'mov,mp4,m4a,3gp,3g2,mj2,asf,avi,flv,webm,m4v,mpeg,mpeg,mpeg,ogv,mkv,webm,mov');",
    "INSERT INTO `Wo_Config` (`name`, `value`) VALUES ('ffmpeg_mime_types', 'application/vnd.ms-asf,video/x-msvideo,video/x-flv,video/webm,video/x-m4v,video/mp4,video/mpeg,video/ogg,video/x-matroska,video/quicktime,video/x-ms-wmv,video/x-msvideo');",
    "ALTER TABLE `Wo_Posts` ADD `processing` INT(2) NOT NULL DEFAULT '0' AFTER `4096p`;",
    "INSERT INTO `wondertage_settings` (`id`, `name`, `value`) VALUES (33, 'tag_posts_feed', '0'), (34, 'tag_artplayer', '0'), (35, 'tag_show_comments', '0'), (36, 'tag_hide_menu', '0');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'terms_contact');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'first_name_last_name_empty');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'reply_message');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'reacted_to_your_story');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'reply_story');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'reacted_to_your_message');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'replying_story');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'ffmpeg_file_text');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'video_ready_to_view');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'processing_video');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'view_story');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'replying_to');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'see_trending_people');",

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
            $('.wo_update_changelog').append('<li><span class="added">Updating Langauges</span> ~$ languages.sh, Please wait, this might take some time..</li>');
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
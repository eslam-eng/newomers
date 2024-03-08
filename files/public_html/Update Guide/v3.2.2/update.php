<?php
if (file_exists('assets/init.php')) {
    require 'assets/init.php';
} else {
    die('Please put this file in the home directory !');
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
            $lang_update_queries[] = Wo_UpdateLangs($value, 'feels_like_temp', 'أحس كأنني');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pressure_temp', 'ضغط');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'uvi_temp', 'الأشعة فوق البنفسجية');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'qr_dash', 'qr.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'hourly_forecast', 'توقعات ساعة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'daily_forecast', 'توقعات يومية');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created', 'إجمالي المشاركات التي تم إنشاؤها');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created_month', 'إجمالي المشاركات التي تم إنشاؤها هذا الشهر');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reaction_dash', 'تفاعل');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_dash', 'كان رد فعل');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_by', 'ردود الفعل من قبل');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_reacted', 'هذا يدل على عدد المرات التي تتفاعل بها مشاركات المستخدمين الآخرين');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_reacted', 'هذا يدل على عدد مرات رد فعل المستخدمين على مشاركاتك');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_liked', 'هذا يدل على عدد المرات التي أعجبتها المشاركات المستخدمين الآخرين');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_liked', 'هذا يوضح كم مرة يحب المستخدمون مشاركاتك');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_commented', 'كم مرة علقت على مشاركات المستخدمين الآخرين');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_commented', 'كم مرة علق المستخدمين على مشاركاتك');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_shared', 'إجمالي عدد المشاركات التي شاركتها');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_shared', 'إجمالي عدد المستخدمين الذين شاركون مشاركاتك');
        } else if ($value == 'dutch') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'feels_like_temp', 'Voelt als');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pressure_temp', 'Druk');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'uvi_temp', 'Uv');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'qr_dash', 'Qr');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'hourly_forecast', 'Uurlijkse voorspeling');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'daily_forecast', 'Dagelijkse voorspelling');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created', 'Totale berichten gemaakt');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created_month', 'Totale berichten gemaakt deze maand');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reaction_dash', 'Reactie');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_dash', 'Reageerde');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_by', 'Reacties door');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_reacted', 'Dit laat zien hoe vaak u reageerde op andere gebruikers');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_reacted', 'Dit laat zien hoe vaak gebruikers reageerden op uw berichten');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_liked', 'Dit laat zien hoe vaak je andere gebruikersposten leuk vond');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_liked', 'Dit laat zien hoe vaak gebruikers van je berichten leuk vonden');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_commented', 'Hoe vaak heb je gereageerd op andere gebruikersposten');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_commented', 'Hoe vaak hebben gebruikers gereageerd op uw berichten');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_shared', 'Totaal aantal berichten dat u hebt gedeeld');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_shared', 'Totaal aantal gebruikers dat uw berichten deelden');
        } else if ($value == 'french') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'feels_like_temp', 'Se sent comme');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pressure_temp', 'Pression');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'uvi_temp', 'Uv');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'qr_dash', 'Qr');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'hourly_forecast', 'Prévision horaire');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'daily_forecast', 'Prévision quotidienne');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created', 'Total des messages créés');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created_month', 'Total des messages créés ce mois-ci');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reaction_dash', 'Réaction');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_dash', 'Réagi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_by', 'Réactions par');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_reacted', 'Cela montre combien de fois vous avez réagi aux autres postes d\'utilisateurs');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_reacted', 'Cela montre le nombre de fois que les utilisateurs ont réagi à vos messages');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_liked', 'Cela montre combien de fois vous avez aimé les autres postes d\'utilisateurs');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_liked', 'Cela montre le nombre de fois que les utilisateurs ont aimé vos messages');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_commented', 'Combien de fois avez-vous commenté les postes d\'autres utilisateurs?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_commented', 'Combien de fois ont commenté les utilisateurs sur vos messages');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_shared', 'Nombre total de messages que vous avez partagés');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_shared', 'Nombre total d\'utilisateurs ayant partagé vos messages');
        } else if ($value == 'german') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'feels_like_temp', 'Fühlt sich an wie');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pressure_temp', 'Druck');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'uvi_temp', 'Uv.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'qr_dash', 'Qr.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'hourly_forecast', 'Stündliche Vorhersage');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'daily_forecast', 'Tägliche Prognose');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created', 'Gesamte Beiträge erstellt.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created_month', 'Gesamtpflege, die diesen Monat erstellt wurden');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reaction_dash', 'Reaktion');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_dash', 'Reagiert');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_by', 'Reaktionen von');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_reacted', 'Dies zeigt, wie oft Sie auf andere Benutzer-Beiträge reagiert haben');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_reacted', 'Dies zeigt, wie oft Benutzer auf Ihre Beiträge reagiert wurden');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_liked', 'Dies zeigt, wie oft Ihnen andere Benutzer-Beiträge gefallen hat');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_liked', 'Dies zeigt, wie oft Benutzer Ihre Beiträge mochten');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_commented', 'Wie oft haben Sie mit anderen Nutzer Beiträgen kommentiert?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_commented', 'Wie oft haben Benutzer Ihre Beiträge kommentiert?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_shared', 'Gesamtzahl der von Ihnen geteilten Beiträge');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_shared', 'Gesamtzahl der Benutzer, die Ihre Beiträge geteilt haben');
        } else if ($value == 'italian') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'feels_like_temp', 'Si sente come');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pressure_temp', 'Pressione');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'uvi_temp', 'UV.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'qr_dash', 'QR.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'hourly_forecast', 'Previsione oraria');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'daily_forecast', 'Previsioni giornaliere');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created', 'Posti totali creati.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created_month', 'Posti totali creati questo mese');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reaction_dash', 'Reazione');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_dash', 'Reagito');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_by', 'Reazioni di');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_reacted', 'Questo mostra quante volte hai reagito ad altri post degli utenti');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_reacted', 'Questo mostra quante volte gli utenti hanno reagito ai tuoi post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_liked', 'Questo mostra quante volte ti sono piaciuti altri post degli utenti');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_liked', 'Questo mostra quante volte gli utenti sono piaciuti i tuoi post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_commented', 'Quante volte hai commentato i post degli altri utenti');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_commented', 'Quante volte gli utenti hanno commentato i tuoi post');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_shared', 'Numero totale di post che hai condiviso');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_shared', 'Numero totale di utenti che hanno condiviso i tuoi post');
        } else if ($value == 'portuguese') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'feels_like_temp', 'Parece');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pressure_temp', 'Pressão');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'uvi_temp', 'UV.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'qr_dash', 'Qr.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'hourly_forecast', 'Previsão horária');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'daily_forecast', 'Previsão diária');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created', 'Postagens totais criadas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created_month', 'Postagens totais criadas este mês');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reaction_dash', 'Reação');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_dash', 'Reagiu');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_by', 'Reações de.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_reacted', 'Isso mostra quantas vezes você reagiu a outros posts de usuários');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_reacted', 'Isso mostra quantas vezes os usuários reagiram aos seus posts');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_liked', 'Isso mostra quantas vezes você gostou de outros usuários');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_liked', 'Isso mostra quantas vezes os usuários gostaram de suas postagens');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_commented', 'Quantas vezes você já comentou sobre outros posts de usuários?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_commented', 'Quantas vezes os usuários comentaram em suas postagens');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_shared', 'Número total de postagens que você compartilhou');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_shared', 'Número total de usuários que compartilharam suas postagens');
        } else if ($value == 'russian') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'feels_like_temp', 'Как будто');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pressure_temp', 'Давление');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'uvi_temp', 'УФ');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'qr_dash', 'QR.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'hourly_forecast', 'Почасовой прогноз');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'daily_forecast', 'Суточный прогноз');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created', 'Общие посты созданы');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created_month', 'Общие посты созданы в этом месяце');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reaction_dash', 'Реакция');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_dash', 'Отреагировал');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_by', 'Реакции');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_reacted', 'Это показывает, сколько раз вы отреагировали на другие посты пользователей');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_reacted', 'Это показывает, сколько раз пользователи отреагировали на ваши сообщения');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_liked', 'Это показывает, сколько раз вам понравились другие сообщения пользователя');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_liked', 'Это показывает, сколько раз пользователи понравились ваши сообщения');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_commented', 'Сколько раз вы прокомментировали другие сообщения пользователя');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_commented', 'Сколько раз пользователи прокомментировали свои сообщения');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_shared', 'Общее количество постов, которые вы поделились');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_shared', 'Общее количество пользователей, которые поделились вашими сообщениями');
        } else if ($value == 'spanish') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'feels_like_temp', 'Se siente como');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pressure_temp', 'Presión');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'uvi_temp', 'UV');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'qr_dash', 'QR');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'hourly_forecast', 'Pronóstico por hora');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'daily_forecast', 'Pronóstico diario');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created', 'Publicaciones totales creadas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created_month', 'Publicaciones totales creadas este mes.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reaction_dash', 'Reacción');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_dash', 'Reaccionado');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_by', 'Reacciones por');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_reacted', 'Esto muestra cuántas veces reaccionó a otras publicaciones de usuarios');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_reacted', 'Esto muestra cuántas veces los usuarios reaccionaron a sus publicaciones.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_liked', 'Esto muestra cuántas veces te gustó otros usuarios de usuarios');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_liked', 'Esto muestra cuántas veces a los usuarios les gustó tus publicaciones.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_commented', '¿Cuántas veces ha comentado en otras publicaciones de usuarios?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_commented', '¿Cuántas veces ha comentado los usuarios en sus publicaciones?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_shared', 'Número total de publicaciones que compartiste');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_shared', 'Número total de usuarios que compartieron tus publicaciones.');
        } else if ($value == 'turkish') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'feels_like_temp', 'Gibi hissettiriyor');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pressure_temp', 'Basınç');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'uvi_temp', 'Uv');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'qr_dash', 'Qr');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'hourly_forecast', 'Saatlik tahmin');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'daily_forecast', 'Günlük tahmin');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created', 'Oluşturulan toplam mesajlar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created_month', 'Bu ay oluşturulan toplam mesajlar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reaction_dash', 'Reaksiyon');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_dash', 'Reaksiyona girmiş');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_by', 'Tarafından reaksiyonlar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_reacted', 'Bu, diğer kullanıcıların yayınlarına kaç kez reaksiyona girdiğinizi gösterir.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_reacted', 'Bu, kullanıcıların yayınlarınıza kaç kez tepki verdiğini gösterir.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_liked', 'Bu, diğer kullanıcıların gönderilerini kaç kez sevdiğini gösterir.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_liked', 'Bu, kullanıcıların yayınlarınızı kaç kez sevdiğini gösterir.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_commented', 'Kaç kez diğer kullanıcılar mesajlarına yorum yaptınız?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_commented', 'Kullanıcıların yayınlarınızda kaç kez yorum yaptılar?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_shared', 'Paylaştığınız toplam yayın sayısı');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_shared', 'Mesajlarınızı paylaşan toplam kullanıcı sayısı');
        } else if ($value == 'english') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'feels_like_temp', 'Feels like');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pressure_temp', 'Pressure');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'uvi_temp', 'UV');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'qr_dash', 'QR');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'hourly_forecast', 'Hourly forecast');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'daily_forecast', 'Daily forecast');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created', 'Total Posts created');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created_month', 'Total Posts Created this Month');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reaction_dash', 'Reaction');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_dash', 'Reacted');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_by', 'Reactions By');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_reacted', 'This shows how many times you reacted to other users posts');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_reacted', 'This shows how many times users reacted to your posts');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_liked', 'This shows how many times you liked other users posts');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_liked', 'This shows how many times users liked your posts');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_commented', 'How many times have you commented on other users posts');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_commented', 'How many times have users commented on your posts');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_shared', 'Total number of posts that you shared');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_shared', 'Total number of users who shared your posts');
        } else if ($value != 'english') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'feels_like_temp', 'Feels like');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'pressure_temp', 'Pressure');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'uvi_temp', 'UV');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'qr_dash', 'QR');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'hourly_forecast', 'Hourly forecast');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'daily_forecast', 'Daily forecast');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created', 'Total Posts created');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'posts_created_month', 'Total Posts Created this Month');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reaction_dash', 'Reaction');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_dash', 'Reacted');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reacted_by', 'Reactions By');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_reacted', 'This shows how many times you reacted to other users posts');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_reacted', 'This shows how many times users reacted to your posts');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_liked', 'This shows how many times you liked other users posts');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_liked', 'This shows how many times users liked your posts');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_commented', 'How many times have you commented on other users posts');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_commented', 'How many times have users commented on your posts');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'i_shared', 'Total number of posts that you shared');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_shared', 'Total number of users who shared your posts');
        }
    }
    if (!empty($lang_update_queries)) {
        foreach ($lang_update_queries as $key => $query) {
            $sql = mysqli_query($sqlConnect, $query);
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
                     <h2 class="light">Update to v3.2.2</span></h2>
                     <div class="setting-well">
                        <h4>Changelog</h4>
                        <ul class="wo_update_changelog">
                                <li> [Added] Palestine to country list.</li>
                                <li> [Removed] dublicated nginx rules in nginx.conf, update required for nginx users. </li>
                                <li> [Replaced] Yahoo weather with new provider https://openweathermap.org</li>
                                <li> [Fixed] live video not working on firefox.</li>
                                <li> [Fixed] blog point issue, user will get points now after approving.</li>
                                <li> [Fixed] nearby shops counter. </li>
                                <li> [Fixed] line break issue in nodejs chat system.</li>
                                <li> [Fixed] refund page wasn't apearing. </li>
                                <li> [Fixed] add movies page in admin, sometimes it looks broken.</li>
                                <li> [Fixed] bank details wasn't getting updated in admin panel. </li>
                                <li> [Fixed] issues in desgin.</li>
                                <li> [Fixed] video post on MySQL 8 and PHP 8.</li>
                                <li> [Fixed] more minor bugs.</li>
                                <li> [Fixed] bugs in API.</li>
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
    "UPDATE `Wo_Config` SET `value` = '3.2.2' WHERE `name` = 'version';",
    "ALTER TABLE `Wo_Mute` ADD `fav` VARCHAR(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'no' AFTER `pin`, ADD INDEX (`fav`);",
    "ALTER TABLE `Wo_Messages` ADD `forward` INT(2) NOT NULL DEFAULT '0' AFTER `broadcast_id`, ADD INDEX (`forward`);",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'feels_like_temp');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'pressure_temp');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'uvi_temp');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'qr_dash');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'hourly_forecast');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'daily_forecast');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'posts_created');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'posts_created_month');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'reaction_dash');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'reacted_dash');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'reacted_by');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'i_reacted');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'user_reacted');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'i_liked');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'user_liked');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'i_commented');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'user_commented');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'i_shared');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'user_shared');",
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
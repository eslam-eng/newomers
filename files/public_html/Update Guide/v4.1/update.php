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
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report_user_text', 'أبلغ عن مستخدم');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report', 'تقرير');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reason_empty', 'لا يمكن أن يكون السبب فارغًا');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_submitted', 'تم تقديم طلبك.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_removed', 'تمت إزالة طلبك.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_profile_banned', 'تم حظر هذا الحساب بسبب أنشطة مشبوهة.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fluttewave', 'موجة فلوت');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fortumo', 'فورتومو');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'aamarpay', 'أمارباي');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ngenius', 'عبقري');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'empty_amount', 'المبلغ فارغ');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_canceled', 'تم إلغاء دفعك باستخدام CoinPayments');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_approved', 'تمت الموافقة على دفعتك باستخدام CoinPayments');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'id_empty', 'لا يمكن أن يكون المعرف فارغًا');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_product_post', 'حذف المنتج');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_product_post', 'هل أنت متأكد أنك تريد حذف هذا المنتج؟');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_request_pending_app', 'طلب استرداد الأموال في انتظار الموافقة ، وسنتواصل معك قريبًا.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'send_again', 'إعادة إرسال');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_two_expired', 'انتهت صلاحية الرمز ، يرجى تسجيل الدخول مرة أخرى.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_send_now', 'يرجى الانتظار لبضع دقائق قبل طلب رمز جديد.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_successfully_sent', 'تم ارسال الكود!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'skrill', 'سكريل');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'transfer_to', 'حول إلى');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_select_payment_method', 'الرجاء اختيار طريقة الدفع');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'week', 'أسبوع');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_not_use_feature', 'لا يمكنك استخدام هذه الميزة الآن.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_funding', 'التمويل');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_jobs', 'وظائف');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_games', 'ألعاب');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_market', 'سوق');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_events', 'الأحداث');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_forum', 'المنتدى');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_groups', 'مجموعات');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_pages', 'الصفحات');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_call', 'مكالمة صوتية');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_call', 'مكالمة فيديو');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_offer', 'عرض');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_blog', 'مقالات');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_movies', 'أفلام');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_story', 'قصة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_stickers', 'ملصقات');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gif', 'GIF');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gift', 'هدية');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_nearby', 'أصدقاء قريبون');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_upload', 'رفع فيديو');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_upload', 'تحميل الصوت');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_shout_box', 'صندوق الصراخ');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_colored_posts', 'المشاركات الملونة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_poll', 'تصويت');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_live', 'مقاطع فيديو حية');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_background', 'خلفية الملف الشخصي');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_chat', 'دردشة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'after_verified', 'يمكنك استخدام هذه الميزات بمجرد التحقق من حسابك.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity', 'نشاط غير قانوني');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity_regulated', 'السلع أو الخدمات غير القانونية أو الخاضعة للتنظيم');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_supporting_promoting', 'دعم أو الترويج لجماعة كراهية أو إرهابية');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_profiling', 'التنميط العنصري');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_belittles', 'الكلام الذي يقلل من شأن مجموعة من الناس أو يصورهم');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_sexism', 'العنصرية أو التحيز الجنسي أو رهاب المثلية أو أي تمييز آخر');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_promoting', 'دعم أو الترويج لمجموعة تحض على الكراهية');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_vaccines', 'معلومات خاطئة عن اللقاحات');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_election', 'معلومات مضللة عن عملية أو نتيجة انتخابات وطنية');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_commercial', 'تجاري أو ترويج ذاتي');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_politics', 'السياسة القومية أو الدين');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_spam', 'رسائل إلكترونية مزعجة');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_posted_error', 'تم النشر عن طريق الخطأ');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_irrelevant', 'غير ذي صلة أو مزعج');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_against', 'يتعارض مع معتقداتي أو قيمي أو سياستي');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_other', 'آخر');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'max_upload', 'أقصى حجم للتحميل');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'go_pro_more_features', 'كن عضوًا محترفًا وقم بالوصول إلى هذه الميزات');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'explore_latest_media', 'استكشف أحدث الصور ومقاطع الفيديو!');
        } else if ($value == 'dutch') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report_user_text', 'Rapporteer gebruiker');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report', 'Rapport');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reason_empty', 'Reden kan niet leeg zijn');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_submitted', 'Uw aanvraag is ingediend.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_removed', 'Uw verzoek is verwijderd.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_profile_banned', 'Dit account is geblokkeerd vanwege verdachte activiteiten.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fluttewave', 'Flutte Golf');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fortumo', 'Fortumo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'aamarpay', 'Aamarpay');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ngenius', 'Ngenius');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'empty_amount', 'Bedrag is leeg');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_canceled', 'Uw betaling met CoinPayments is geannuleerd');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_approved', 'Uw betaling met CoinPayments is goedgekeurd');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'id_empty', 'ID mag niet leeg zijn');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_product_post', 'Product verwijderen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_product_post', 'Weet je zeker dat je dit product wilt verwijderen?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_request_pending_app', 'Uw teruggaveverzoek wacht op goedkeuring. We nemen zo snel mogelijk contact met u op.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'send_again', 'Opnieuw verzenden');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_two_expired', 'Code verlopen, gelieve opnieuw in te loggen.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_send_now', 'Wacht enkele minuten voordat u een nieuwe code aanvraagt.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_successfully_sent', 'Code verzonden!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'skrill', 'Skrill');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'transfer_to', 'Overzetten naar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_select_payment_method', 'Selecteer een betaalmethode, alstublieft');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'week', 'Week');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_not_use_feature', 'U kunt deze functie nu niet gebruiken.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_funding', 'Financiering');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_jobs', 'Banen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_games', 'Spellen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_market', 'Markt');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_events', 'Evenementen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_forum', 'Forum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_groups', 'Groepen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_pages', 'Pagina&#39;s');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_call', 'Audiogesprek');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_call', 'Video-oproep');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_offer', 'Bieden');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_blog', 'Blog');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_movies', 'Films');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_story', 'Verhaal');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_stickers', 'stickers');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gif', 'Gif');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gift', 'Geschenk');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_nearby', 'Vrienden in de buurt');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_upload', 'Upload video');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_upload', 'Audio uploaden');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_shout_box', 'Shoutbox');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_colored_posts', 'Gekleurde berichten');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_poll', 'opiniepeiling');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_live', 'Live video&#39;s');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_background', 'Profielachtergrond');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_chat', 'Chatten');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'after_verified', 'U kunt deze functies gebruiken zodra uw account is geverifieerd.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity', 'Illegale activiteit');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity_regulated', 'Illegale of gereguleerde goederen of diensten');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_supporting_promoting', 'Een haat- of terreurgroep steunen of promoten');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_profiling', 'Raciale profilering');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_belittles', 'Spraak die een groep mensen kleineert of stereotypeert');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_sexism', 'Racisme, seksisme, homofobie of andere discriminatie');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_promoting', 'Een haatgroep ondersteunen of promoten');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_vaccines', 'Verkeerde informatie over vaccins');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_election', 'Verkeerde informatie over een nationaal verkiezingsproces of uitslag');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_commercial', 'Commercieel of zelfpromotie');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_politics', 'Nationale politiek of religie');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_spam', 'Spam');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_posted_error', 'Fout geplaatst');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_irrelevant', 'Irrelevant of vervelend');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_against', 'Gaat in tegen mijn overtuigingen, waarden of politiek');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_other', 'Ander');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'max_upload', 'Maximale uploadgrootte');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'go_pro_more_features', 'Word Pro-lid en krijg toegang tot deze functies');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'explore_latest_media', 'Ontdek de nieuwste afbeeldingen en video&#39;s!');
        } else if ($value == 'french') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report_user_text', 'Dénoncer un utilisateur');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report', 'Signaler');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reason_empty', 'La raison ne peut pas être vide');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_submitted', 'Votre requête à bien été envoyée.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_removed', 'Votre demande a été supprimée.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_profile_banned', 'Ce compte a été bloqué en raison d&#39;activités suspectes.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fluttewave', 'Flûte Vague');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fortumo', 'Fortumo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'aamarpay', 'Aamarpay');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ngenius', 'Ngénie');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'empty_amount', 'Le montant est vide');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_canceled', 'Votre paiement avec CoinPayments a été annulé');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_approved', 'Votre paiement avec CoinPayments a été approuvé');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'id_empty', 'L&#39;ID ne peut pas être vide');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_product_post', 'Supprimer le produit');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_product_post', 'Êtes-vous sûr de vouloir supprimer ce produit ?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_request_pending_app', 'Votre demande de remboursement est en attente d&#39;approbation, nous vous répondrons bientôt.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'send_again', 'Renvoyer');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_two_expired', 'Code expiré, veuillez vous reconnecter.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_send_now', 'Veuillez patienter quelques minutes avant de demander un nouveau code.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_successfully_sent', 'Code envoyé!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'skrill', 'Skrill');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'transfer_to', 'Transférer à');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_select_payment_method', 'Veuillez choisir un moyen de paiement');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'week', 'La semaine');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_not_use_feature', 'Vous ne pouvez pas utiliser cette fonctionnalité maintenant.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_funding', 'Financement');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_jobs', 'Travaux');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_games', 'Jeux');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_market', 'Marché');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_events', 'Événements');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_forum', 'Forum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_groups', 'Groupes');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_pages', 'pages');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_call', 'Appel audio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_call', 'Appel vidéo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_offer', 'Offrir');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_blog', 'Blog');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_movies', 'Films');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_story', 'Histoire');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_stickers', 'Autocollants');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gif', 'GIF');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gift', 'Cadeau');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_nearby', 'Amis à proximité');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_upload', 'Télécharger une video');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_upload', 'Télécharger l&#39;audio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_shout_box', 'Boîte à cris');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_colored_posts', 'Poteaux colorés');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_poll', 'Sondage');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_live', 'Vidéos en direct');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_background', 'Arrière-plan du profil');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_chat', 'Discuter');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'after_verified', 'Vous pouvez utiliser ces fonctionnalités une fois votre compte vérifié.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity', 'Activité illégale');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity_regulated', 'Biens ou services illégaux ou réglementés');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_supporting_promoting', 'Soutenir ou promouvoir un groupe haineux ou terroriste');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_profiling', 'Le profilage racial');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_belittles', 'Discours qui rabaisse ou stéréotype un groupe de personnes');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_sexism', 'Racisme, sexisme, homophobie ou autre discrimination');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_promoting', 'Soutenir ou promouvoir un groupe haineux');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_vaccines', 'Désinformation sur les vaccins');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_election', 'Informations erronées sur le processus ou le résultat d&#39;une élection nationale');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_commercial', 'Commercial ou autopromotion');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_politics', 'Politique nationale ou religion');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_spam', 'Spam');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_posted_error', 'Posté par erreur');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_irrelevant', 'Indifférent ou ennuyeux');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_against', 'Va à l&#39;encontre de mes croyances, de mes valeurs ou de la politique');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_other', 'Autre');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'max_upload', 'Taille maximale de téléchargement');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'go_pro_more_features', 'Devenez membre Pro et accédez à ces fonctionnalités');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'explore_latest_media', 'Explorez les dernières images et vidéos !');
        } else if ($value == 'german') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report_user_text', 'Benutzer melden');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report', 'Bericht');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reason_empty', 'Der Grund darf nicht leer sein');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_submitted', 'Ihre Anfrage wurde übermittelt.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_removed', 'Ihre Anfrage wurde entfernt.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_profile_banned', 'Dieses Konto wurde aufgrund verdächtiger Aktivitäten gesperrt.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fluttewave', 'Flötenwelle');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fortumo', 'Fortumo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'aamarpay', 'Aamarpay');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ngenius', 'Genius');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'empty_amount', 'Betrag ist leer');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_canceled', 'Ihre Zahlung mit CoinPayments wurde storniert');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_approved', 'Ihre Zahlung mit CoinPayments wurde genehmigt');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'id_empty', 'ID darf nicht leer sein');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_product_post', 'Produkt löschen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_product_post', 'Möchten Sie dieses Produkt wirklich löschen?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_request_pending_app', 'Ihr Erstattungsantrag steht noch aus. Wir werden uns bald mit Ihnen in Verbindung setzen.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'send_again', 'Erneut senden');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_two_expired', 'Code abgelaufen, bitte erneut einloggen.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_send_now', 'Bitte warten Sie einige Minuten, bevor Sie einen neuen Code anfordern.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_successfully_sent', 'Code gesendet!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'skrill', 'Skrill');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'transfer_to', 'Übertragen an');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_select_payment_method', 'Bitte Zahlungsart wählen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'week', 'Woche');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_not_use_feature', 'Sie können diese Funktion jetzt nicht verwenden.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_funding', 'Finanzierung');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_jobs', 'Arbeitsplätze');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_games', 'Spiele');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_market', 'Markt');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_events', 'Veranstaltungen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_forum', 'Forum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_groups', 'Gruppen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_pages', 'Seiten');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_call', 'Audioanruf');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_call', 'Videoanruf');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_offer', 'Angebot');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_blog', 'Bloggen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_movies', 'Filme');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_story', 'Geschichte');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_stickers', 'Aufkleber');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gif', 'Gif');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gift', 'Geschenk');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_nearby', 'Freunde, in der Nähe');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_upload', 'Video hochladen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_upload', 'Audio hochladen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_shout_box', 'Shout-Box');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_colored_posts', 'Farbige Beiträge');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_poll', 'Umfrage');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_live', 'Live-Videos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_background', 'Profilhintergrund');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_chat', 'Plaudern');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'after_verified', 'Sie können diese Funktionen verwenden, sobald Ihr Konto verifiziert ist.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity', 'Illegale Aktivität');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity_regulated', 'Illegale oder regulierte Waren oder Dienstleistungen');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_supporting_promoting', 'Unterstützung oder Förderung einer Hass- oder Terrorgruppe');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_profiling', 'Rassenprofilierung');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_belittles', 'Äußerungen, die eine Gruppe von Menschen herabsetzen oder stereotypisieren');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_sexism', 'Rassismus, Sexismus, Homophobie oder andere Diskriminierung');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_promoting', 'Unterstützung oder Förderung einer Hassgruppe');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_vaccines', 'Fehlinformationen über Impfstoffe');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_election', 'Fehlinformationen über einen nationalen Wahlprozess oder -ausgang');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_commercial', 'Kommerziell oder Eigenwerbung');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_politics', 'Nationale Politik oder Religion');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_spam', 'Spam');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_posted_error', 'Fehlerhaft gepostet');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_irrelevant', 'Irrelevant oder nervig');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_against', 'Widerspricht meinen Überzeugungen, Werten oder meiner Politik');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_other', 'Sonstiges');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'max_upload', 'Maximale Uploadgröße');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'go_pro_more_features', 'Werden Sie Pro-Mitglied und greifen Sie auf diese Funktionen zu');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'explore_latest_media', 'Entdecken Sie die neuesten Bilder und Videos!');
        } else if ($value == 'russian') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report_user_text', 'Пожаловаться на пользователя');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report', 'Отчет');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reason_empty', 'Причина не может быть пустой');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_submitted', 'Ваш запрос был отправлен.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_removed', 'Ваш запрос был удален.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_profile_banned', 'Этот аккаунт был заблокирован из-за подозрительных действий.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fluttewave', 'Флейта Волна');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fortumo', 'Фортумо');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'aamarpay', 'Аамарпай');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ngenius', 'Нгениус');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'empty_amount', 'Сумма пуста');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_canceled', 'Ваш платеж с использованием CoinPayments был отменен');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_approved', 'Ваш платеж с помощью CoinPayments одобрен');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'id_empty', 'Укажите идентификатор.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_product_post', 'Удалить продукт');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_product_post', 'Вы уверены, что хотите удалить этот продукт?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_request_pending_app', 'Ваш запрос на возврат средств находится на рассмотрении. Мы свяжемся с вами в ближайшее время.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'send_again', 'Отправить');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_two_expired', 'Срок действия кода истек, пожалуйста, войдите снова.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_send_now', 'Пожалуйста, подождите несколько минут, прежде чем запрашивать новый код.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_successfully_sent', 'Код отправлен!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'skrill', 'Скрилл');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'transfer_to', 'Перевести в');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_select_payment_method', 'Пожалуйста, выберите способ оплаты');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'week', 'Неделя');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_not_use_feature', 'Вы не можете использовать эту функцию сейчас.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_funding', 'Финансирование');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_jobs', 'Работа');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_games', 'Игры');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_market', 'Рынок');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_events', 'События');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_forum', 'Форум');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_groups', 'Группы');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_pages', 'Страницы');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_call', 'Аудиовызов');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_call', 'Видеозвонок');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_offer', 'Предложение');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_blog', 'Блог');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_movies', 'Фильмы');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_story', 'История');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_stickers', 'Наклейки');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gif', 'гифка');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gift', 'Подарок');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_nearby', 'Друзья поблизости');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_upload', 'Загрузить видео');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_upload', 'Загрузить аудио');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_shout_box', 'Коробка для крика');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_colored_posts', 'Цветные посты');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_poll', 'Опрос');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_live', 'Живые видео');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_background', 'Фон профиля');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_chat', 'Чат');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'after_verified', 'Вы можете использовать эти функции после подтверждения вашей учетной записи.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity', 'Незаконная деятельность');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity_regulated', 'Незаконные или регулируемые товары или услуги');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_supporting_promoting', 'Поддержка или пропаганда ненависти или террористической группы');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_profiling', 'Расовое профилирование');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_belittles', 'Речь, принижающая или стереотипизирующая группу людей');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_sexism', 'Расизм, сексизм, гомофобия или другая дискриминация');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_promoting', 'Поддержка или продвижение группы ненависти');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_vaccines', 'Дезинформация о вакцинах');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_election', 'Дезинформация о процессе или результатах общенациональных выборов');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_commercial', 'Коммерческая или самореклама');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_politics', 'Национальная политика или религия');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_spam', 'Спам');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_posted_error', 'Опубликовано с ошибкой');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_irrelevant', 'Неактуально или раздражает');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_against', 'Идет вразрез с моими убеждениями, ценностями или политикой');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_other', 'Другой');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'max_upload', 'Максимальный размер загрузки');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'go_pro_more_features', 'Станьте участником Pro и получите доступ к этим функциям');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'explore_latest_media', 'Исследуйте последние изображения и видео!');
        } else if ($value == 'spanish') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report_user_text', 'Reportar usuario');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report', 'Reporte');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reason_empty', 'La razón no puede estar vacía');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_submitted', 'Su solicitud ha sido enviada.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_removed', 'Su solicitud ha sido eliminada.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_profile_banned', 'Esta cuenta ha sido bloqueada debido a actividades sospechosas.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fluttewave', 'Onda de flauta');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fortumo', 'Fortumo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'aamarpay', 'aamarpay');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ngenius', 'Genio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'empty_amount', 'La cantidad está vacía');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_canceled', 'Su pago con CoinPayments ha sido cancelado');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_approved', 'Su pago con CoinPayments ha sido aprobado');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'id_empty', 'ID no puede estar vacío');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_product_post', 'Eliminar producto');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_product_post', '¿Está seguro de que desea eliminar este producto?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_request_pending_app', 'Su solicitud de reembolso está pendiente de aprobación, nos pondremos en contacto pronto.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'send_again', 'reenviar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_two_expired', 'Código caducado, vuelva a iniciar sesión.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_send_now', 'Espere unos minutos antes de solicitar un nuevo código.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_successfully_sent', '¡Código enviado!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'skrill', 'Skrill');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'transfer_to', 'Transferir a');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_select_payment_method', 'Por favor seleccione un método de pago');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'week', 'Semana');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_not_use_feature', 'No puede usar esta característica ahora.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_funding', 'Fondos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_jobs', 'Trabajos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_games', 'Juegos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_market', 'Mercado');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_events', 'Eventos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_forum', 'Foro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_groups', 'Grupos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_pages', 'Paginas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_call', 'Llamada de audio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_call', 'Videollamada');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_offer', 'Oferta');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_blog', 'Blog');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_movies', 'Películas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_story', 'Historia');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_stickers', 'Pegatinas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gif', 'Gif');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gift', 'Regalo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_nearby', 'Amigos cercanos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_upload', 'Subir vídeo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_upload', 'Subir audio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_shout_box', 'caja de gritos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_colored_posts', 'Publicaciones de colores');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_poll', 'Encuesta');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_live', 'Vídeos en vivo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_background', 'Fondo de perfil');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_chat', 'Charlar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'after_verified', 'Puede usar esas funciones una vez que se verifique su cuenta.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity', 'Actividad ilegal');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity_regulated', 'Bienes o servicios ilegales o regulados');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_supporting_promoting', 'Apoyar o promover un grupo de odio o terror.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_profiling', 'perfiles raciales');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_belittles', 'Discurso que menosprecia o estereotipa a un grupo de personas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_sexism', 'Racismo, sexismo, homofobia u otra discriminación');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_promoting', 'Apoyar o promover un grupo de odio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_vaccines', 'Desinformación sobre las vacunas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_election', 'Información errónea sobre un proceso o resultado de una elección nacional');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_commercial', 'Comercial o autopromoción');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_politics', 'Política nacional o religión');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_spam', 'Correo no deseado');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_posted_error', 'Publicado por error');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_irrelevant', 'Irrelevante o molesto');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_against', 'Va en contra de mis creencias, valores o política');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_other', 'Otro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'max_upload', 'Tamaño máximo de carga');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'go_pro_more_features', 'Conviértase en miembro Pro y acceda a estas funciones');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'explore_latest_media', '¡Explora las últimas imágenes y videos!');
        } else if ($value == 'turkish') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report_user_text', 'Kullanıcıyı bildir');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report', 'Bildiri');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reason_empty', 'Neden boş olamaz');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_submitted', 'İsteğiniz sunulmuştur.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_removed', 'İsteğiniz kaldırıldı.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_profile_banned', 'Bu hesap şüpheli etkinlikler nedeniyle engellendi.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fluttewave', 'flüt dalgası');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fortumo', 'Fortumo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'aamarpay', 'Aamarpay');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ngenius', 'Ngenius');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'empty_amount', 'Tutar boş');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_canceled', 'CoinPayments kullanarak yaptığınız ödeme iptal edildi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_approved', 'CoinPayments kullanarak yaptığınız ödeme onaylandı');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'id_empty', 'Kimlik boş olamaz');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_product_post', 'Ürünü Sil');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_product_post', 'Bu ürünü silmek istediğinizden emin misiniz?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_request_pending_app', 'Geri ödeme isteğiniz onay bekliyor, kısa süre içinde size geri döneceğiz.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'send_again', 'Yeniden gönder');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_two_expired', 'Kodun süresi doldu, lütfen tekrar giriş yapın.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_send_now', 'Yeni bir kod istemeden önce lütfen birkaç dakika bekleyin.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_successfully_sent', 'Kod gönderildi!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'skrill', 'Skrill');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'transfer_to', 'Transfer');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_select_payment_method', 'Lütfen bir ödeme yöntemi seçin');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'week', 'Hafta');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_not_use_feature', 'Bu özelliği şimdi kullanamazsınız.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_funding', 'Finansman');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_jobs', 'Meslekler');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_games', 'Oyunlar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_market', 'Pazar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_events', 'Olaylar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_forum', 'Forum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_groups', 'Gruplar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_pages', 'Sayfalar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_call', 'Sesli Arama');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_call', 'Görüntülü arama');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_offer', 'Teklif');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_blog', 'Blog');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_movies', 'Filmler');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_story', 'Hikaye');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_stickers', 'çıkartmalar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gif', 'gif');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gift', 'Hediye');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_nearby', 'Yakındaki arkadaşlar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_upload', 'Video yükle');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_upload', 'Ses Yükle');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_shout_box', 'bağırma kutusu');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_colored_posts', 'renkli yazılar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_poll', 'Anket');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_live', 'Canlı Videolar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_background', 'Profil Arka Planı');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_chat', 'Sohbet');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'after_verified', 'Hesabınız doğrulandıktan sonra bu özellikleri kullanabilirsiniz.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity', 'yasa dışı faaliyet');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity_regulated', 'Yasa dışı veya düzenlemeye tabi mal veya hizmetler');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_supporting_promoting', 'Bir nefret veya terör grubunu desteklemek veya teşvik etmek');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_profiling', 'Irksal profilleme');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_belittles', 'Bir grup insanı küçümseyen veya klişeleştiren konuşma');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_sexism', 'Irkçılık, cinsiyetçilik, homofobi veya diğer ayrımcılık');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_promoting', 'Bir nefret grubunu desteklemek veya teşvik etmek');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_vaccines', 'aşılar hakkında yanlış bilgi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_election', 'Ulusal seçim süreci veya sonucu hakkında yanlış bilgi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_commercial', 'Ticari veya kendi kendini tanıtma');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_politics', 'Ulusal siyaset veya din');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_spam', 'İstenmeyen e-posta');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_posted_error', 'yanlışlıkla gönderildi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_irrelevant', 'alakasız veya rahatsız edici');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_against', 'İnançlarıma, değerlerime veya siyasetime aykırı');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_other', 'Başka');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'max_upload', 'Maksimum yükleme boyutu');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'go_pro_more_features', 'Pro üye olun ve bu özelliklere erişin');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'explore_latest_media', 'En son görüntüleri ve videoları keşfedin!');
        } else if ($value == 'portuguese') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report_user_text', 'Reportar usuário');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report', 'Relatório');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reason_empty', 'A razão não pode estar vazia');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_submitted', 'O seu pedido foi enviado.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_removed', 'Sua solicitação foi removida.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_profile_banned', 'Esta conta foi bloqueada devido a atividades suspeitas.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fluttewave', 'Flauta Onda');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fortumo', 'Fortumo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'aamarpay', 'Aamarpay');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ngenius', 'Ngenius');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'empty_amount', 'O valor está vazio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_canceled', 'Seu pagamento usando CoinPayments foi cancelado');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_approved', 'Seu pagamento usando CoinPayments foi aprovado');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'id_empty', 'ID não pode estar vazio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_product_post', 'Excluir produto');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_product_post', 'Tem certeza de que deseja excluir este produto?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_request_pending_app', 'Sua solicitação de reembolso está pendente de aprovação. Entraremos em contato em breve.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'send_again', 'Reenviar');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_two_expired', 'O código expirou, faça login novamente.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_send_now', 'Aguarde alguns minutos antes de solicitar um novo código.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_successfully_sent', 'Código enviado!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'skrill', 'Skrill');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'transfer_to', 'Transferir para');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_select_payment_method', 'Selecione uma forma de pagamento');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'week', 'Semana');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_not_use_feature', 'Você não pode usar esse recurso agora.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_funding', 'Financiamento');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_jobs', 'Empregos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_games', 'Jogos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_market', 'Mercado');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_events', 'Eventos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_forum', 'Fórum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_groups', 'Grupos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_pages', 'Páginas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_call', 'Chamada de áudio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_call', 'Video chamada');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_offer', 'Oferta');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_blog', 'Blogue');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_movies', 'Filmes');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_story', 'História');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_stickers', 'Adesivos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gif', 'Gif');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gift', 'Presente');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_nearby', 'Amigos nas proximidades');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_upload', 'Envio vídeo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_upload', 'Carregar áudio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_shout_box', 'Caixa de gritos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_colored_posts', 'Postagens coloridas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_poll', 'Votação');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_live', 'Vídeos ao vivo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_background', 'Plano de fundo do perfil');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_chat', 'Bater papo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'after_verified', 'Você pode usar esses recursos assim que sua conta for verificada.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity', 'Atividade ilegal');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity_regulated', 'Bens ou serviços ilegais ou regulamentados');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_supporting_promoting', 'Apoiar ou promover um grupo de ódio ou terror');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_profiling', 'Perfil racial');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_belittles', 'Discurso que menospreza ou estereotipa um grupo de pessoas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_sexism', 'Racismo, sexismo, homofobia ou outra discriminação');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_promoting', 'Apoiar ou promover um grupo de ódio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_vaccines', 'Desinformação sobre vacinas');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_election', 'Desinformação sobre um processo ou resultado eleitoral nacional');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_commercial', 'Comercial ou autopromoção');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_politics', 'Política nacional ou religião');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_spam', 'Spam');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_posted_error', 'Postado com erro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_irrelevant', 'Irrelevante ou irritante');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_against', 'Vai contra minhas crenças, valores ou política');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_other', 'Outro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'max_upload', 'Tamanho máximo de upload');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'go_pro_more_features', 'Torne-se um membro Pro e acesse esses recursos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'explore_latest_media', 'Explore as últimas imagens e vídeos!');
        } else if ($value == 'italian') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report_user_text', 'Segnala utente');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report', 'Rapporto');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reason_empty', 'La ragione non può essere vuota');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_submitted', 'La tua richiesta è stata inviata.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_removed', 'La tua richiesta è stata rimossa.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_profile_banned', 'Questo account è stato bloccato a causa di attività sospette.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fluttewave', 'Onda svolazzante');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fortumo', 'Fortuna');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'aamarpay', 'Amarpay');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ngenius', 'Ngenius');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'empty_amount', 'L&#39;importo è vuoto');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_canceled', 'Il tuo pagamento tramite CoinPayments è stato annullato');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_approved', 'Il tuo pagamento tramite CoinPayments è stato approvato');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'id_empty', 'L&#39;ID non può essere vuoto');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_product_post', 'Elimina prodotto');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_product_post', 'Sei sicuro di voler eliminare questo prodotto?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_request_pending_app', 'La tua richiesta di rimborso è in attesa di approvazione, ti contatteremo presto.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'send_again', 'Rinviare');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_two_expired', 'Codice scaduto, effettuare nuovamente il login.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_send_now', 'Si prega di attendere qualche minuto prima di richiedere un nuovo codice.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_successfully_sent', 'Codice inviato!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'skrill', 'Skrill');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'transfer_to', 'Trasferire a');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_select_payment_method', 'Seleziona un metodo di pagamento');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'week', 'Settimana');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_not_use_feature', 'Non è possibile utilizzare questa funzione ora.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_funding', 'Finanziamento');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_jobs', 'Lavori');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_games', 'Giochi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_market', 'Mercato');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_events', 'Eventi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_forum', 'Forum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_groups', 'Gruppi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_pages', 'Pagine');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_call', 'Chiamata audio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_call', 'Video chiamata');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_offer', 'Offerta');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_blog', 'Blog');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_movies', 'Film');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_story', 'Storia');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_stickers', 'Adesivi');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gif', 'Gif');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gift', 'Regalo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_nearby', 'Amici vicini');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_upload', 'Carica video');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_upload', 'Carica audio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_shout_box', 'Casella di urlo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_colored_posts', 'Messaggi colorati');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_poll', 'Sondaggio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_live', 'Video dal vivo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_background', 'Sfondo del profilo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_chat', 'Chiacchierata');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'after_verified', 'Puoi utilizzare queste funzionalità una volta verificato il tuo account.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity', 'Attività illegale');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity_regulated', 'Beni o servizi illegali o regolamentati');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_supporting_promoting', 'Sostenere o promuovere un gruppo di odio o terrore');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_profiling', 'Profilazione razziale');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_belittles', 'Discorso che sminuisce o stereotipa un gruppo di persone');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_sexism', 'Razzismo, sessismo, omofobia o altre discriminazioni');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_promoting', 'Sostenere o promuovere un gruppo di odio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_vaccines', 'Disinformazione sui vaccini');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_election', 'Disinformazione su un processo o esito elettorale nazionale');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_commercial', 'Commerciale o autopromozione');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_politics', 'Politica nazionale o religione');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_spam', 'Spam');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_posted_error', 'Inserito per errore');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_irrelevant', 'Irrilevante o fastidioso');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_against', 'Va contro le mie convinzioni, i miei valori o la mia politica');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_other', 'Altro');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'max_upload', 'Dimensione massima di caricamento');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'go_pro_more_features', 'Diventa un membro Pro e accedi a queste funzionalità');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'explore_latest_media', 'Esplora le ultime immagini e video!');
        } else if ($value == 'english') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report_user_text', 'Report User');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report', 'Report');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reason_empty', 'Reason can not be empty');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_submitted', 'Your request has been submitted.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_removed', 'Your request has been removed.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_profile_banned', 'This account has been blocked due to suspicious activities.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fluttewave', 'Flutte Wave');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fortumo', 'Fortumo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'aamarpay', 'Aamarpay');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ngenius', 'Ngenius');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'empty_amount', 'Amount is empty');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_canceled', 'Your payment using CoinPayments has been canceled');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_approved', 'Your payment using CoinPayments has been approved');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'id_empty', 'ID can not be empty');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_product_post', 'Delete Product');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_product_post', 'Are you sure that you want to delete this product?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_request_pending_app', 'Your refund request is pending approval, we will reach back soon.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'send_again', 'Resend');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_two_expired', 'Code expired, please Login again.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_send_now', 'Please wait for few minutes before requesting a new code.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_successfully_sent', 'Code sent!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'skrill', 'Skrill');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'transfer_to', 'Transfer To');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_select_payment_method', 'Please select a payment method');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'week', 'Week');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_not_use_feature', 'You can not use this feature now.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_funding', 'Funding');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_jobs', 'Jobs');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_games', 'Games');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_market', 'Market');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_events', 'Events');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_forum', 'Forum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_groups', 'Groups');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_pages', 'Pages');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_call', 'Audio Call');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_call', 'Video Call');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_offer', 'Offer');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_blog', 'Blog');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_movies', 'Movies');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_story', 'Story');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_stickers', 'Stickers');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gif', 'Gif');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gift', 'Gift');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_nearby', 'Nearby Friends');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_upload', 'Upload Video');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_upload', 'Upload Audio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_shout_box', 'Shout Box');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_colored_posts', 'Colored Posts');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_poll', 'Poll');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_live', 'Live Videos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_background', 'Profile Background');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_chat', 'Chat');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'after_verified', 'You can use those features once your account is verified.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity', 'Illegal activity');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity_regulated', 'Illegal or regulated goods or services');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_supporting_promoting', 'Supporting or promoting a hate or terror group');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_profiling', 'Racial profiling');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_belittles', 'Speech that belittles or stereotypes a group of people');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_sexism', 'Racism, sexism, homophobia or other discrimination');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_promoting', 'Supporting or promoting a hate group');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_vaccines', 'Misinformation about vaccines');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_election', 'Misinformation about a national election process or outcome');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_commercial', 'Commercial or self-promotion');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_politics', 'National politics or religion');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_spam', 'Spam');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_posted_error', 'Posted in error');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_irrelevant', 'Irrelevant or annoying');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_against', 'Goes against my beliefs, values or politics');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_other', 'Other');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'max_upload', 'Max upload size');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'go_pro_more_features', 'Become a Pro member and access these features');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'explore_latest_media', 'Explore latest images and videos!');
        } else if ($value != 'english') {
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report_user_text', 'Report User');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'report', 'Report');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'reason_empty', 'Reason can not be empty');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_submitted', 'Your request has been submitted.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'request_removed', 'Your request has been removed.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'user_profile_banned', 'This account has been blocked due to suspicious activities.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fluttewave', 'Flutte Wave');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'fortumo', 'Fortumo');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'aamarpay', 'Aamarpay');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'ngenius', 'Ngenius');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'empty_amount', 'Amount is empty');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_canceled', 'Your payment using CoinPayments has been canceled');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'coinpayments_approved', 'Your payment using CoinPayments has been approved');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'id_empty', 'ID can not be empty');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'delete_product_post', 'Delete Product');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'confirm_delete_product_post', 'Are you sure that you want to delete this product?');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'your_request_pending_app', 'Your refund request is pending approval, we will reach back soon.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'send_again', 'Resend');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_two_expired', 'Code expired, please Login again.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'you_cant_send_now', 'Please wait for few minutes before requesting a new code.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'code_successfully_sent', 'Code sent!');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'skrill', 'Skrill');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'transfer_to', 'Transfer To');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'please_select_payment_method', 'Please select a payment method');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'week', 'Week');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_not_use_feature', 'You can not use this feature now.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_funding', 'Funding');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_jobs', 'Jobs');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_games', 'Games');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_market', 'Market');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_events', 'Events');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_forum', 'Forum');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_groups', 'Groups');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_pages', 'Pages');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_call', 'Audio Call');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_call', 'Video Call');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_offer', 'Offer');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_blog', 'Blog');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_movies', 'Movies');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_story', 'Story');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_stickers', 'Stickers');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gif', 'Gif');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_gift', 'Gift');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_nearby', 'Nearby Friends');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_video_upload', 'Upload Video');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_audio_upload', 'Upload Audio');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_shout_box', 'Shout Box');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_colored_posts', 'Colored Posts');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_poll', 'Poll');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_live', 'Live Videos');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_background', 'Profile Background');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'can_use_chat', 'Chat');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'after_verified', 'You can use those features once your account is verified.');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity', 'Illegal activity');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_illegal_activity_regulated', 'Illegal or regulated goods or services');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_supporting_promoting', 'Supporting or promoting a hate or terror group');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_profiling', 'Racial profiling');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_belittles', 'Speech that belittles or stereotypes a group of people');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_sexism', 'Racism, sexism, homophobia or other discrimination');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_promoting', 'Supporting or promoting a hate group');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_vaccines', 'Misinformation about vaccines');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_election', 'Misinformation about a national election process or outcome');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_commercial', 'Commercial or self-promotion');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_politics', 'National politics or religion');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_spam', 'Spam');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_posted_error', 'Posted in error');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_irrelevant', 'Irrelevant or annoying');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_against', 'Goes against my beliefs, values or politics');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'r_other', 'Other');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'max_upload', 'Max upload size');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'go_pro_more_features', 'Become a Pro member and access these features');
            $lang_update_queries[] = Wo_UpdateLangs($value, 'explore_latest_media', 'Explore latest images and videos!');
        }
    }
    if (!empty($lang_update_queries)) {
        foreach ($lang_update_queries as $key => $query) {
            $sql = mysqli_query($sqlConnect, $query);
        }
    }
    mysqli_query($sqlConnect, "INSERT INTO `Wo_Manage_Pro` (`id`, `type`, `price`, `featured_member`, `profile_visitors`, `last_seen`, `verified_badge`, `posts_promotion`, `pages_promotion`, `discount`, `image`, `night_image`, `color`, `description`, `status`, `time`, `time_count`, `max_upload`, `features`) VALUES
(1, 'Star', '4', 1, 1, 1, 1, 0, 2, '0', '', '', '#79c149', 'Get started!', 1, 'month', 1, '24000000', '{\"can_use_funding\":1,\"can_use_jobs\":1,\"can_use_games\":1,\"can_use_market\":1,\"can_use_events\":1,\"can_use_forum\":1,\"can_use_groups\":1,\"can_use_pages\":1,\"can_use_audio_call\":1,\"can_use_video_call\":1,\"can_use_offer\":1,\"can_use_blog\":1,\"can_use_movies\":1,\"can_use_story\":1,\"can_use_stickers\":1,\"can_use_gif\":1,\"can_use_gift\":1,\"can_use_nearby\":1,\"can_use_video_upload\":1,\"can_use_audio_upload\":1,\"can_use_shout_box\":1,\"can_use_colored_posts\":1,\"can_use_poll\":1,\"can_use_live\":1,\"can_use_background\":1,\"can_use_chat\":1}'),
(2, 'Hot', '8', 1, 1, 1, 1, 5, 5, '10', '', '', '#dd3c3c', 'Get Hot! More features.', 1, 'month', 1, '96000000', '{\"can_use_funding\":1,\"can_use_jobs\":1,\"can_use_games\":1,\"can_use_market\":1,\"can_use_events\":1,\"can_use_forum\":1,\"can_use_groups\":1,\"can_use_pages\":1,\"can_use_audio_call\":1,\"can_use_video_call\":1,\"can_use_offer\":1,\"can_use_blog\":1,\"can_use_movies\":1,\"can_use_story\":1,\"can_use_stickers\":1,\"can_use_gif\":1,\"can_use_gift\":1,\"can_use_nearby\":1,\"can_use_video_upload\":1,\"can_use_audio_upload\":1,\"can_use_shout_box\":1,\"can_use_colored_posts\":1,\"can_use_poll\":1,\"can_use_live\":1,\"can_use_background\":1,\"can_use_chat\":1}'),
(3, 'Ultima', '89', 1, 1, 1, 1, 20, 20, '20', '', '', '#fb924b', 'Oh yeah, join the ultima!', 1, 'month', 1, '256000000', '{\"can_use_funding\":1,\"can_use_jobs\":1,\"can_use_games\":1,\"can_use_market\":1,\"can_use_events\":1,\"can_use_forum\":1,\"can_use_groups\":1,\"can_use_pages\":1,\"can_use_audio_call\":1,\"can_use_video_call\":1,\"can_use_offer\":1,\"can_use_blog\":1,\"can_use_movies\":1,\"can_use_story\":1,\"can_use_stickers\":1,\"can_use_gif\":1,\"can_use_gift\":1,\"can_use_nearby\":1,\"can_use_video_upload\":1,\"can_use_audio_upload\":1,\"can_use_shout_box\":1,\"can_use_colored_posts\":1,\"can_use_poll\":1,\"can_use_live\":1,\"can_use_background\":1,\"can_use_chat\":1}'),
(4, 'VIP', '259', 1, 1, 1, 1, 40, 40, '60', '', '', '#5bbaf5', 'GO Limitless!', 1, 'unlimited', 1, '96000000', '{\"can_use_funding\":1,\"can_use_jobs\":1,\"can_use_games\":1,\"can_use_market\":1,\"can_use_events\":1,\"can_use_forum\":1,\"can_use_groups\":1,\"can_use_pages\":1,\"can_use_audio_call\":1,\"can_use_video_call\":1,\"can_use_offer\":1,\"can_use_blog\":1,\"can_use_movies\":1,\"can_use_story\":1,\"can_use_stickers\":1,\"can_use_gif\":1,\"can_use_gift\":1,\"can_use_nearby\":1,\"can_use_video_upload\":1,\"can_use_audio_upload\":1,\"can_use_shout_box\":1,\"can_use_colored_posts\":1,\"can_use_poll\":1,\"can_use_live\":1,\"can_use_background\":1,\"can_use_chat\":1}');");
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
                     <h2 class="light">Update to v4.1</span></h2>
                     <div class="setting-well">
                        <h4>Changelog</h4>
                        <ul class="wo_update_changelog">
                          <li> [Added] report user system + reason.</li>
                          <li> [Added] ban message on user profile if user is banned. </li>
                          <li> [Added] Instagram Mode.</li>
                          <li> [Added] flutterwave payment method.</li>
                          <li> [Added] Ngenius payment method.</li>
                          <li> [Added] Aamarpay payment method.</li>
                          <li> [Added] the ability to add custom pro packages.</li>
                          <li> [Added] the ability to add custom withdrawal method. </li>
                          <li> [Added] the abiltiy to choose whom can use a feature, Admin, All Users, Only Pro.</li>
                          <li> [Added] the ability to resend two auth verfication code.</li>
                          <li> [Added] new design for Instegram mode.</li>
                          <li> [Added] developer mode to admin panel.</li>
                          <li> [Updated] documentation & FAQs: <a href="https://docs.wowonder.com/" target="_blank">https://docs.wowonder.com/</a> .</li>
                          <li> [Updated] email verfication page design. </li>
                          <li> [Updated] chat design.</li>
                          <li> [Updated] all website shadow elements (css).</li>
                          <li> [Updated] header notifications, messages dropdown size.</li>
                          <li> [Updated] post comments design.</li>
                          <li> [Improved] design in default theme for few sections.</li>
                          <li> [Fixed] qrcode.js was missing in sunshine.</li>
                          <li> [Fixed] search in admin panel was not working well.</li>
                          <li> [Fixed] login using twitter.</li>
                          <li> [Fixed] admin commission was not appearing with product purchase.</li>
                          <li> [Fixed] OG meta tags for albums.</li>
                          <li> [Fixed] XSS vulnerability.</li>
                          <li> [Fixed] fixed product showing in stock while there is no stock left.</li>
                          <li> [Fixed] announcements wern't working.</li>
                          <li> [Fixed] agora live video / calls.</li>
                          <li> [Fixed] google login API.</li>
                          <li> [Fixed] twilio video/audio calls.</li>
                          <li> [Fixed] if post was boosted on a page it doesn't appear in booted posts section.</li>
                          <li> [Fixed] the ability to send gif & stickers on nodejs. </li>
                          <li> [Fixed] 40+ more minor bugs.</li>
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
    "UPDATE `Wo_Config` SET `value` = '4.1' WHERE `name` = 'version';",
    "UPDATE `Wo_Config` SET `value` = '#f0f2f5' WHERE `name` = 'body_background';",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'fluttewave_payment', '0');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'fluttewave_secret_key', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'fortumo_payment', '0');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'fortumo_service_id', '');",
    "ALTER TABLE `Wo_Users` ADD `fortumo_hash` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `securionpay_key`, ADD INDEX (`fortumo_hash`);",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'aamarpay_payment', '0');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'aamarpay_mode', 'sandbox');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'aamarpay_store_id', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'aamarpay_signature_key', '');",
    "ALTER TABLE `Wo_Users` ADD `aamarpay_tran_id` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `fortumo_hash`, ADD INDEX (`aamarpay_tran_id`);",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'ngenius_payment', '0');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'ngenius_mode', 'sandbox');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'ngenius_api_key', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'ngenius_outlet_id', '');",
    "ALTER TABLE `Wo_Users` ADD `ngenius_ref` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `aamarpay_tran_id`, ADD INDEX (`ngenius_ref`);",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'coinpayments_public_key', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'coinpayments_coins', '');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'coinpayments_coin', '');",
    "ALTER TABLE `Wo_Users` ADD `coinpayments_txn_id` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `ngenius_ref`, ADD INDEX (`coinpayments_txn_id`);",
    "ALTER TABLE `Wo_Users` ADD `two_factor_hash` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `two_factor`, ADD INDEX (`two_factor_hash`);",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'withdrawal_payment_method', '{\"paypal\":1,\"bank\":0,\"skrill\":0,\"custom\":0}');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'custom_name', '');",
    "ALTER TABLE `Wo_Affiliates_Requests` ADD `type` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `address`, ADD `transfer_info` VARCHAR(600) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `type`, ADD INDEX (`type`);",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'job_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'game_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'market_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'event_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'forum_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'groups_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'pages_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'video_call_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'audio_call_request', 'all');",
    "ALTER TABLE `Wo_Manage_Pro` ADD `color` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '#fafafa' AFTER `night_image`, ADD `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `color`;",
    "ALTER TABLE `Wo_Manage_Pro` CHANGE `type` `type` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';",
    "ALTER TABLE `Wo_Manage_Pro` ADD `time_count` INT(11) NOT NULL DEFAULT '0' AFTER `time`;",
    "ALTER TABLE `Wo_Users` CHANGE `pro_type` `pro_type` INT(11) NOT NULL DEFAULT '0';",
    "ALTER TABLE `Wo_Manage_Pro` ADD `max_upload` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '96000000' AFTER `time_count`;",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'offer_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'blog_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'movies_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'story_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'stickers_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'gif_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'gift_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'nearby_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'video_upload_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'audio_upload_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'shout_box_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'colored_posts_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'poll_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'live_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'profile_background_request', 'all');",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'chat_request', 'all');",
    "ALTER TABLE `Wo_Manage_Pro` ADD `features` VARCHAR(800) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '{\"can_use_funding\":1,\"can_use_jobs\":1,\"can_use_games\":1,\"can_use_market\":1,\"can_use_events\":1,\"can_use_forum\":1,\"can_use_groups\":1,\"can_use_pages\":1,\"can_use_audio_call\":1,\"can_use_video_call\":1,\"can_use_offer\":1,\"can_use_blog\":1,\"can_use_movies\":1,\"can_use_story\":1,\"can_use_stickers\":1,\"can_use_gif\":1,\"can_use_gift\":1,\"can_use_nearby\":1,\"can_use_video_upload\":1,\"can_use_audio_upload\":1,\"can_use_shout_box\":1,\"can_use_colored_posts\":1,\"can_use_poll\":1,\"can_use_live\":1,\"can_use_background\":1,\"can_use_chat\":1}' AFTER `max_upload`;",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'report_reasons', '[\"r_illegal_activity\",\"r_illegal_activity_regulated\",\"r_supporting_promoting\",\"r_profiling\",\"r_belittles\",\"r_sexism\",\"r_promoting\",\"r_vaccines\",\"r_election\",\"r_commercial\",\"r_politics\",\"r_spam\",\"r_posted_error\",\"r_irrelevant\",\"r_against\",\"r_other\"]');",
    "ALTER TABLE `Wo_Reports` ADD `reason` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `text`;",
    "INSERT INTO `Wo_Config` (`id`, `name`, `value`) VALUES (NULL, 'developer_mode', '0');",
    "TRUNCATE `Wo_Manage_Pro`;",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'report_user_text');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'report');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'reason_empty');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'request_submitted');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'request_removed');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'user_profile_banned');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'fluttewave');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'fortumo');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'aamarpay');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'ngenius');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'empty_amount');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'coinpayments_canceled');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'coinpayments_approved');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'id_empty');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'delete_product_post');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'confirm_delete_product_post');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'your_request_pending_app');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'send_again');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'code_two_expired');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'you_cant_send_now');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'code_successfully_sent');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'skrill');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'transfer_to');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'please_select_payment_method');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'week');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_not_use_feature');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_funding');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_jobs');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_games');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_market');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_events');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_forum');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_groups');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_pages');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_audio_call');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_video_call');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_offer');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_blog');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_movies');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_story');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_stickers');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_gif');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_gift');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_nearby');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_video_upload');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_audio_upload');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_shout_box');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_colored_posts');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_poll');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_live');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_background');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_chat');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'after_verified');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'r_illegal_activity');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'r_illegal_activity_regulated');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'r_supporting_promoting');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'r_profiling');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'r_belittles');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'r_sexism');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'r_promoting');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'r_vaccines');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'r_election');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'r_commercial');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'r_politics');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'r_spam');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'r_posted_error');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'r_irrelevant');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'r_against');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'r_other');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'max_upload');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'go_pro_more_features');",
    "INSERT INTO `Wo_Langs` (`id`, `lang_key`) VALUES (NULL, 'explore_latest_media');",

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

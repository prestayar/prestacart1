/*
 * PrestaCart
 * @version  1.0
 *
 * @author Hashem Afkhami @ prestayar.com <hashem_afkhami@yahoo.com>
 * @copyright  2014 prestayar.com

 *
 */
function cityList(mySubject) {
    var Indx = mySubject;
    with(document.getElementById('id_city')) {
        options.length = 0;
        if (Indx == 0) {
            options[0] = new Option("لطفا استان خود را انتخاب کنيد", "");
        }
        if (Indx == 26) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("آسارا", "5");
            options[2] = new Option("اشتهارد", "7");
            options[3] = new Option("چهار باغ", "9");
            options[4] = new Option("طالقان", "3");
            options[5] = new Option("کرج", "1");
            options[6] = new Option("کمالشهر", "15");
            options[7] = new Option("کوهسار", "6");
            options[8] = new Option("گرمدره", "12");
            options[9] = new Option("ماهدشت", "14");
            options[10] = new Option("محمد شهر", "13");
            options[11] = new Option("نظر آباد", "8");
            options[12] = new Option("هشتگرد", "4");
        }
        if (Indx == 41) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("آذر شهر", "12");
            options[2] = new Option("اسكو", "14");
            options[3] = new Option("اهر", "13");
            options[4] = new Option("بستان آباد", "17");
            options[5] = new Option("بناب", "15");
            options[6] = new Option("بندر شرفخانه", "16");
            options[7] = new Option("تبريز", "18");
            options[8] = new Option("تسوج", "19");
            options[9] = new Option("جلفا", "20");
            options[10] = new Option("سراب", "21");
            options[11] = new Option("شبستر", "22");
            options[12] = new Option("صوفیان", "24");
            options[13] = new Option("عجبشير", "23");
            options[14] = new Option("قره آغاج", "1");
            options[15] = new Option("كليبر", "2");
            options[16] = new Option("كندوان", "3");
            options[17] = new Option("مراغه", "7");
            options[18] = new Option("مرند", "6");
            options[19] = new Option("ملكان", "4");
            options[20] = new Option("ميانه", "5");
            options[21] = new Option("ورزقان", "11");
            options[22] = new Option("هاديشهر", "8");
            options[23] = new Option("هريس", "9");
            options[24] = new Option("هشترود", "10");
            options[25] = new Option("ممقان", "25");
        }
        if (Indx == 44) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("اروميه", "7");
            options[2] = new Option("اشنويه", "8");
            options[3] = new Option("بازرگان", "19");
            options[4] = new Option("بوكان", "9");
            options[5] = new Option("پيرانشهر", "6");
            options[6] = new Option("تكاب", "10");
            options[7] = new Option("خوي", "11");
            options[8] = new Option("سر دشت", "14");
            options[9] = new Option("سلماس", "12");
            options[10] = new Option("سيه چشمه", "13");
            options[11] = new Option("شاهين دژ", "15");
            options[12] = new Option("شوط", "18");
            options[13] = new Option("ماكو", "3");
            options[14] = new Option("مهاباد", "1");
            options[15] = new Option("مياندوآب", "2");
            options[16] = new Option("نقده", "4");
        }
        if (Indx == 45) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("اردبيل", "9");
            options[2] = new Option("بيله سوار", "10");
            options[3] = new Option("پارس آباد", "8");
            options[4] = new Option("خلخال", "11");
            options[5] = new Option("سرعين", "12");
            options[6] = new Option("گیوی(کوثر)", "2");
            options[7] = new Option("گرمي", "7");
            options[8] = new Option("مشگين شهر", "3");
            options[9] = new Option("نمين", "5");
            options[10] = new Option("نير", "6");
        }
        if (Indx == 31) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("آران و بيدگل", "11");
            options[2] = new Option("اردستان", "12");
            options[3] = new Option("اصفهان", "13");
            options[4] = new Option("باغ بهادران", "14");
            options[5] = new Option("تيران", "15");
            options[6] = new Option("چادگان", "29");
            options[7] = new Option("خميني شهر", "16");
            options[8] = new Option("خوانسار", "17");
            options[9] = new Option("دولت آباد", "18");
            options[10] = new Option("دهاقان", "23");
            options[11] = new Option("زرين شهر", "19");
            options[12] = new Option("زیبا شهر", "26");
            options[13] = new Option("سميرم", "20");
            options[14] = new Option("سپاهان شهر", "30");
            options[15] = new Option("شاهين شهر", "22");
            options[16] = new Option("شهرضا", "21");
            options[17] = new Option("فريدن", "3");
            options[18] = new Option("فريدون شهر", "4");
            options[19] = new Option("فلاورجان", "1");
            options[20] = new Option("فولاد شهر", "2");
            options[21] = new Option("قهدریجان", "24");
            options[22] = new Option("كاشان", "5");
            options[23] = new Option("گلدشت", "28");
            options[24] = new Option("گلپايگان", "10");
            options[25] = new Option("مباركه", "6");
            options[26] = new Option("ملک شهر", "25");
            options[27] = new Option("نايين", "7");
            options[28] = new Option("نجف آباد", "8");
            options[29] = new Option("نطنز", "9");
            options[30] = new Option("هرند", "27");
        }
        if (Indx == 84) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("آبدانان", "2");
            options[2] = new Option("ايلام", "3");
            options[3] = new Option("ايوان", "4");
            options[4] = new Option("دره شهر", "6");
            options[5] = new Option("دهلران", "5");
            options[6] = new Option("سرابله", "7");
            options[7] = new Option("مهران", "1");
        }
        if (Indx == 77) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("اهرم", "3");
            options[2] = new Option("برازجان", "5");
            options[3] = new Option("آبپخش", "16");
            options[4] = new Option("بوشهر", "4");
            options[5] = new Option("تنگستان", "6");
            options[6] = new Option("جم", "15");
            options[7] = new Option("خارك", "8");
            options[8] = new Option("خورموج", "7");
            options[9] = new Option("دشتستان", "12");
            options[10] = new Option("دلوار", "17");
            options[11] = new Option("دير", "10");
            options[12] = new Option("ديلم", "9");
            options[13] = new Option("عسلویه", "14");
            options[14] = new Option("کاکی", "11");
            options[15] = new Option("كنگان", "1");
            options[16] = new Option("گناوه", "2");
        }
        if (Indx == 21) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("اسلامشهر", "17");
            options[2] = new Option("اندیشه", "38");
            options[3] = new Option("باغستان", "41");
            options[4] = new Option("بومهن", "19");
            options[5] = new Option("پاكدشت", "15");
            options[6] = new Option("پرند", "42");
            options[7] = new Option("تجريش", "21");
            options[8] = new Option("تهران", "20");
            options[9] = new Option("چهاردانگه", "10");
            options[10] = new Option("خاور شهر", "39");
            options[11] = new Option("دماوند", "22");
            options[12] = new Option("رباط كريم", "25");
            options[13] = new Option("رودهن", "23");
            options[14] = new Option("ري", "24");
            options[15] = new Option("شريف آباد", "27");
            options[16] = new Option("شهريار", "26");
            options[17] = new Option("صبا شهر", "43");
            options[18] = new Option("صفادشت", "40");
            options[19] = new Option("فشم", "2");
            options[20] = new Option("فيروزكوه", "1");
            options[21] = new Option("قدس", "3");
            options[22] = new Option("قرچك", "4");
            options[23] = new Option("كن", "5");
            options[24] = new Option("كهريزك", "6");
            options[25] = new Option("گلستان", "14");
            options[26] = new Option("لواسان", "8");
            options[27] = new Option("ملارد", "9");
            options[28] = new Option("ورامين", "13");
        }
        if (Indx == 38) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("اردل", "4");
            options[2] = new Option("بروجن", "5");
            options[3] = new Option("چلگرد", "3");
            options[4] = new Option("سامان", "6");
            options[5] = new Option("شهركرد", "7");
            options[6] = new Option("فارسان", "1");
            options[7] = new Option("فرخ شهر", "9");
            options[8] = new Option("لردگان", "2");
            options[9] = new Option("هفشجان", "8");
        }
        if (Indx == 56) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("بشرویه", "6");
            options[2] = new Option("بيرجند", "2");
            options[3] = new Option("درمیان", "9");
            options[4] = new Option("خضری", "8");
            options[5] = new Option("سرایان", "5");
            options[6] = new Option("سربيشه", "3");
            options[7] = new Option("طبس", "10");
            options[8] = new Option("فردوس", "7");
            options[9] = new Option("قائن", "4");
            options[10] = new Option("نهبندان", "1");
        }
        if (Indx == 51) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("بردسكن", "12");
            options[2] = new Option("بجستان", "23");
            options[3] = new Option("تايباد", "13");
            options[4] = new Option("تربت جام", "14");
            options[5] = new Option("تربت حيدريه", "15");
            options[6] = new Option("جغتای", "2");
            options[7] = new Option("جوین", "5");
            options[8] = new Option("چناران", "9");
            options[9] = new Option("خواف", "16");
            options[10] = new Option("خلیل آباد", "22");
            options[11] = new Option("درگز", "17");
            options[12] = new Option("رشتخوار", "4");
            options[13] = new Option("سبزوار", "18");
            options[14] = new Option("سرخس", "19");
            options[15] = new Option("طوس", "20");
            options[16] = new Option("طرقبه", "21");
            options[17] = new Option("فريمان", "1");
            options[18] = new Option("قوچان", "3");
            options[19] = new Option("كاشمر", "7");
            options[20] = new Option("كلات", "6");
            options[21] = new Option("گناباد", "11");
            options[22] = new Option("مشهد", "8");
            options[23] = new Option("نيشابور", "10");
        }
        if (Indx == 58) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("آشخانه", "1");
            options[2] = new Option("اسفراين", "2");
            options[3] = new Option("بجنورد", "3");
            options[4] = new Option("جاجرم", "4");
            options[5] = new Option("شيروان", "5");
            options[6] = new Option("فاروج", "6");
        }
        if (Indx == 61) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("آبادان", "5");
            options[2] = new Option("اميديه", "6");
            options[3] = new Option("انديمشك", "7");
            options[4] = new Option("اهواز", "8");
            options[5] = new Option("ايذه", "9");
            options[6] = new Option("گتوند", "10");
            options[7] = new Option("باغ ملك", "14");
            options[8] = new Option("بندرامام خميني", "12");
            options[9] = new Option("بندر ماهشهر", "11");
            options[10] = new Option("بهبهان", "13");
            options[11] = new Option("خرمشهر", "15");
            options[12] = new Option("دزفول", "16");
            options[13] = new Option("رامهرمز", "17");
            options[14] = new Option("رامشیر", "22");
            options[15] = new Option("سوسنگرد", "18");
            options[16] = new Option("شادگان", "21");
            options[17] = new Option("شوشتر", "20");
            options[18] = new Option("شوش", "19");
            options[19] = new Option("لالي", "1");
            options[20] = new Option("مسجد سليمان", "2");
            options[21] = new Option("هنديجان", "3");
            options[22] = new Option("هويزه", "4");
        }
        if (Indx == 24) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("آب بر", "4");
            options[2] = new Option("ابهر", "6");
            options[3] = new Option("ايجرود", "5");
            options[4] = new Option("خرمدره", "8");
            options[5] = new Option("زرين آباد", "10");
            options[6] = new Option("زنجان", "9");
            options[7] = new Option("قيدار", "1");
            options[8] = new Option("ماهنشان", "3");
        }
        if (Indx == 23) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("ايوانكي", "2");
            options[2] = new Option("بسطام", "3");
            options[3] = new Option("دامغان", "4");
            options[4] = new Option("سمنان", "5");
            options[5] = new Option("سرخه", "7");
            options[6] = new Option("شاهرود", "6");
            options[7] = new Option("شهمیرزاد", "9");
            options[8] = new Option("گرمسار", "1");
            options[9] = new Option("مهدیشهر", "8");
        }
        if (Indx == 54) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("ايرانشهر", "4");
            options[2] = new Option("چابهار", "3");
            options[3] = new Option("خاش", "5");
            options[4] = new Option("راسك", "6");
            options[5] = new Option("زابل", "8");
            options[6] = new Option("زاهدان", "7");
            options[7] = new Option("سراوان", "9");
            options[8] = new Option("سرباز", "10");
            options[9] = new Option("فنوج", "12");
            options[10] = new Option("کنارک", "11");
            options[11] = new Option("ميرجاوه", "1");
            options[12] = new Option("نيكشهر", "2");
        }
        if (Indx == 71) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("آباده", "13");
            options[2] = new Option("اردكان", "15");
            options[3] = new Option("ارسنجان", "16");
            options[4] = new Option("استهبان", "17");
            options[5] = new Option("اقليد", "14");
            options[6] = new Option("ایزد خواست", "27");
            options[7] = new Option("بوانات", "35");
            options[8] = new Option("پاسارگاد", "41");
            options[9] = new Option("جهرم", "18");
            options[10] = new Option("حاجي آباد", "19");
            options[11] = new Option("خرم بید", "37");
            options[12] = new Option("خنج", "36");
            options[13] = new Option("خشت", "20");
            options[14] = new Option("داراب", "21");
            options[15] = new Option("زرقان", "43");
            options[16] = new Option("شيراز", "24");
            options[17] = new Option("فراشبند", "2");
            options[18] = new Option("فسا", "3");
            options[19] = new Option("فيروز آباد", "1");
            options[20] = new Option("قایمیه", "38");
            options[21] = new Option("قيرو کارزین", "4");
            options[22] = new Option("كازرون", "5");
            options[23] = new Option("کوار", "44");
            options[24] = new Option("گراش", "40");
            options[25] = new Option("لار", "7");
            options[26] = new Option("لامرد", "6");
            options[27] = new Option("مرودشت", "10");
            options[28] = new Option("مصیری(رستم)", "42");
            options[29] = new Option("مهر", "39");
            options[30] = new Option("نورآباد", "11");
            options[31] = new Option("ني ريز", "12");
        }
        if (Indx == 28) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("آبيك", "2");
            options[2] = new Option("الوند.البرز", "7");
            options[3] = new Option("بوئين زهرا", "3");
            options[4] = new Option("تاكستان", "4");
            options[5] = new Option("قزوين", "1");
            options[6] = new Option("محمدیه", "5");
            options[7] = new Option("محمود آباد نمونه", "6");
        }
        if (Indx == 25) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("قم", "1");
            options[2] = new Option("دستجرد", "4");
            options[3] = new Option("جعفریه", "5");
            options[4] = new Option("سلفچگان", "2");
            options[5] = new Option("قمرود", "3");
        }
        if (Indx == 87) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("بانه", "5");
            options[2] = new Option("بيجار", "4");
            options[3] = new Option("ديواندره", "6");
            options[4] = new Option("دهگلان", "9");
            options[5] = new Option("سقز", "7");
            options[6] = new Option("سنندج", "8");
            options[7] = new Option("قروه", "1");
            options[8] = new Option("كامياران", "2");
            options[9] = new Option("مريوان", "3");
        }
        if (Indx == 34) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("شهر بابك", "5");
            options[2] = new Option("بافت", "4");
            options[3] = new Option("بردسير", "6");
            options[4] = new Option("بم", "3");
            options[5] = new Option("جيرفت", "7");
            options[6] = new Option("سرچشمه", "12");
            options[7] = new Option("راور", "9");
            options[8] = new Option("رفسنجان", "8");
            options[9] = new Option("زرند", "10");
            options[10] = new Option("سيرجان", "11");
            options[11] = new Option("قلعه گنج", "13");
            options[12] = new Option("كرمان", "2");
            options[13] = new Option("كهنوج", "1");
        }
        if (Indx == 83) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("اسلام آباد غرب", "7");
            options[2] = new Option("پاوه", "6");
            options[3] = new Option("ثلاث باباجانی", "12");
            options[4] = new Option("جوانرود", "8");
            options[5] = new Option("خسروی", "13");
            options[6] = new Option("سر پل ذهاب", "10");
            options[7] = new Option("سنقر", "9");
            options[8] = new Option("صحنه", "11");
            options[9] = new Option("قصر شيرين", "1");
            options[10] = new Option("كرمانشاه", "3");
            options[11] = new Option("كنگاور", "2");
            options[12] = new Option("گيلان غرب", "5");
            options[13] = new Option("هرسين", "4");
        }
        if (Indx == 74) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("دنا", "3");
            options[2] = new Option("دوگنبدان", "5");
            options[3] = new Option("دهدشت", "4");
            options[4] = new Option("سي سخت", "6");
            options[5] = new Option("گچساران", "2");
            options[6] = new Option("لیکک", "7");
            options[7] = new Option("ياسوج", "1");
        }
        if (Indx == 17) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("آزاد شهر", "7");
            options[2] = new Option("آق قلا", "6");
            options[3] = new Option("بندر گز", "8");
            options[4] = new Option("تركمن", "9");
            options[5] = new Option("جلین", "12");
            options[6] = new Option("راميان", "10");
            options[7] = new Option("علي آباد كتول", "11");
            options[8] = new Option("كردكوي", "2");
            options[9] = new Option("كلاله", "1");
            options[10] = new Option("گالیکش", "13");
            options[11] = new Option("گرگان", "5");
            options[12] = new Option("گنبد كاووس", "4");
            options[13] = new Option("مراوه تپه", "14");
            options[14] = new Option("مينو دشت", "3");
        }
        if (Indx == 13) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("آستارا", "9");
            options[2] = new Option("آستانه اشرفيه", "8");
            options[3] = new Option("املش", "10");
            options[4] = new Option("بندرانزلي", "11");
            options[5] = new Option("تالش", "12");
            options[6] = new Option("خمام", "22");
            options[7] = new Option("رودبار", "14");
            options[8] = new Option("رود سر", "13");
            options[9] = new Option("رستم آباد", "23");
            options[10] = new Option("رشت", "15");
            options[11] = new Option("رضوان شهر", "16");
            options[12] = new Option("سياهكل", "17");
            options[13] = new Option("شفت", "18");
            options[14] = new Option("صومعه سرا", "19");
            options[15] = new Option("فومن", "1");
            options[16] = new Option("كلاچاي", "2");
            options[17] = new Option("لاهيجان", "20");
            options[18] = new Option("لنگرود", "3");
            options[19] = new Option("لوشان", "21");
            options[20] = new Option("ماسال", "6");
            options[21] = new Option("ماسوله", "5");
            options[22] = new Option("منجيل", "4");
        }
        if (Indx == 66) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("ازنا", "7");
            options[2] = new Option("الشتر", "6");
            options[3] = new Option("اليگودرز", "5");
            options[4] = new Option("بروجرد", "8");
            options[5] = new Option("پلدختر", "4");
            options[6] = new Option("خرم آباد", "9");
            options[7] = new Option("دورود", "10");
            options[8] = new Option("سراب دوره", "11");
            options[9] = new Option("سپید دشت", "2");
            options[10] = new Option("شول آباد", "12");
            options[11] = new Option("كوهدشت", "1");
            options[12] = new Option("نور آباد", "3");
        }
        if (Indx == 15) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("آمل", "9");
            options[2] = new Option("بلده", "10");
            options[3] = new Option("بهشهر", "11");
            options[4] = new Option("بابل", "12");
            options[5] = new Option("بابلسر", "13");
            options[6] = new Option("پل سفيد", "8");
            options[7] = new Option("تنكابن", "14");
            options[8] = new Option("جويبار", "15");
            options[9] = new Option("چالوس", "7");
            options[10] = new Option("رامسر", "16");
            options[11] = new Option("ساري", "18");
            options[12] = new Option("سلمانشهر", "19");
            options[13] = new Option("سواد كوه", "17");
            options[14] = new Option("فريدون كنار", "1");
            options[15] = new Option("کلاردشت", "22");
            options[16] = new Option("قائم شهر", "2");
            options[17] = new Option("گلوگاه", "20");
            options[18] = new Option("محمود آباد", "3");
            options[19] = new Option("مرزن آباد", "21");
            options[20] = new Option("نكا", "4");
            options[21] = new Option("نور", "5");
            options[22] = new Option("نوشهر", "6");
        }
        if (Indx == 86) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("آشتيان", "2");
            options[2] = new Option("اراك", "3");
            options[3] = new Option("تفرش", "4");
            options[4] = new Option("خمين", "5");
            options[5] = new Option("خنداب", "12");
            options[6] = new Option("دليجان", "6");
            options[7] = new Option("زرندیه", "11");
            options[8] = new Option("ساوه", "7");
            options[9] = new Option("شازند", "10");
            options[10] = new Option("کمیجان", "9");
            options[11] = new Option("محلات", "1");
        }
        if (Indx == 76) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("ابوموسي", "5");
            options[2] = new Option("بندر جاسك", "7");
            options[3] = new Option("بندر خمیر", "14");
            options[4] = new Option("بندرعباس", "8");
            options[5] = new Option("بندر لنگه", "6");
            options[6] = new Option("بستك", "9");
            options[7] = new Option("پارسیان", "13");
            options[8] = new Option("تنب بزرگ", "10");
            options[9] = new Option("حاجي آباد", "11");
            options[10] = new Option("درگهان", "4");
            options[11] = new Option("دشتی", "15");
            options[12] = new Option("دهبارز", "12");
            options[13] = new Option("قشم", "1");
            options[13].className = "new_ostan"
            options[14] = new Option("ميناب", "3");
        }
        if (Indx == 81) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("اسدآباد", "5");
            options[2] = new Option("بهار", "6");
            options[3] = new Option("تويسركان", "7");
            options[4] = new Option("رزن", "8");
            options[5] = new Option("كبودر اهنگ", "1");
            options[6] = new Option("ملاير", "2");
            options[7] = new Option("نهاوند", "3");
            options[8] = new Option("همدان", "4");
        }
        if (Indx == 35) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("ابركوه", "5");
            options[2] = new Option("اردكان", "6");
            options[3] = new Option("اشكذر", "7");
            options[4] = new Option("بافق", "8");
            options[5] = new Option("تفت", "9");
            options[6] = new Option("مهريز", "1");
            options[7] = new Option("ميبد", "2");
            options[8] = new Option("هرات", "3");
            options[9] = new Option("يزد", "4");
        }
        /*document.getElementById('shahr').options[0].selected=true;*/
    }
}
$(document).ready(function () {
    var select_box = document.getElementById("id_state");
    if (select_box.value != 0) cityList(select_box.value);
});
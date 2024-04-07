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
        if (Indx == 87) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("سنندج", "878");
            options[2] = new Option("كامياران", "872");
            options[3] = new Option("ديواندره", "876");
            options[4] = new Option("بيجار", "874");
            options[5] = new Option("قروه", "871");
            options[6] = new Option("مريوان", "873");
            options[7] = new Option("سقز", "877");
            options[8] = new Option("بانه", "875");
            options[9] = new Option("دهگلان", "879");
        }
        if (Indx == 86) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("اراك", "863");
            options[2] = new Option("ساوه", "867");
            options[3] = new Option("محلات", "861");
            options[4] = new Option("دليجان", "866");
            options[5] = new Option("شازند", "8610");
            options[6] = new Option("خمين", "865");
            options[7] = new Option("تفرش", "864");
            options[8] = new Option("آشتيان", "862");
            options[9] = new Option("خنداب", "8612");
            options[10] = new Option("زرندیه - مامونیه", "8611");
            options[11] = new Option("کمیجان", "869");
        }
        if (Indx == 84) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("ايلام", "843");
            options[2] = new Option("ايوان", "844");
            options[3] = new Option("دره شهر", "846");
            options[4] = new Option("آبدانان", "842");
            options[5] = new Option("دهلران", "845");
            options[6] = new Option("مهران", "841");
            options[7] = new Option("سرابله ـ شيروان و چرداول", "848");
        }
        if (Indx == 83) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("كرمانشاه", "833");
            options[2] = new Option("هرسين", "834");
            options[3] = new Option("اسلام آباد غرب", "837");
            options[4] = new Option("سرپل ذهاب", "8310");
            options[5] = new Option("قصرشيرين", "831");
            options[6] = new Option("پاوه", "836");
            options[7] = new Option("كنگاور", "832");
            options[8] = new Option("صحنه", "8311");
            options[9] = new Option("گيلانغرب", "835");
            options[10] = new Option("جوانرود", "838");
            options[11] = new Option("ثلاث باباجانی", "8312");
            options[12] = new Option("سنقر", "839");
        }
        if (Indx == 81) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("همدان", "814");
            options[2] = new Option("بهار", "816");
            options[3] = new Option("اسدآباد", "815");
            options[4] = new Option("كبودرآهنگ", "811");
            options[5] = new Option("ملاير", "812");
            options[6] = new Option("تويسركان", "817");
            options[7] = new Option("نهاوند", "813");
            options[8] = new Option("رزن", "818");
        }
        if (Indx == 77) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("بوشهر", "774");
            options[2] = new Option("گناوه", "772");
            options[3] = new Option("خورموج", "777");
            options[4] = new Option("اهرم ـ تنگستان", "773");
            options[5] = new Option("برازجان ـ دشتستان", "775");
            options[6] = new Option("ديلم", "779");
            options[7] = new Option("جزيره خارك", "778");
            options[8] = new Option("دشتي", "7711");
            options[9] = new Option("کنگان", "7741");
            options[10] = new Option("دیر", "776");
            options[11] = new Option("عسلویه", "7714");
            options[12] = new Option("آب پخش", "7712");
            options[13] = new Option("جم", "7713");
        }
        if (Indx == 76) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("بندرعباس", "768");
            options[2] = new Option("كيش", "762");
            options[3] = new Option("قشم", "761");
            options[4] = new Option("بستك", "769");
            options[5] = new Option("بندرلنگه", "766");
            options[6] = new Option("ميناب", "763");
            options[7] = new Option("دهبارز ـ رودان", "7612");
            options[8] = new Option("حاجي آباد", "7611");
            options[9] = new Option("ابوموسي", "765");
            options[10] = new Option("جاسك", "767");
            options[11] = new Option("بندر خمیر", "7614");
            options[12] = new Option("پارسیان", "7613");
        }
        if (Indx == 74) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("دهدشت ـ كهگيلويه", "744");
            options[2] = new Option("دوگنبدان ـ گچساران", "745");
            options[3] = new Option("ياسوج", "741");
            options[4] = new Option("سي سخت ـ دنا", "746");
            options[5] = new Option("بهمئی-لیکک", "747");
        }
        if (Indx == 71) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("شيراز", "7124");
            options[2] = new Option("كازرون", "715");
            options[3] = new Option("جهرم", "7118");
            options[4] = new Option("نورآباد ـ ممسني", "7111");
            options[5] = new Option("مرودشت", "7110");
            options[6] = new Option("اقليد", "7114");
            options[7] = new Option("آباده", "7113");
            options[8] = new Option("لار ـ لارستان", "717");
            options[9] = new Option("استهبان", "7117");
            options[10] = new Option("فسا", "713");
            options[11] = new Option("داراب", "7121");
            options[12] = new Option("ني ريز", "7112");
            options[13] = new Option("اردكان ـ سپيدان", "7115");
            options[14] = new Option("فيروزآباد", "711");
            options[15] = new Option("ارسنجان", "7116");
            options[16] = new Option("صفاشهر ـ خرم بيد", "7125");
            options[17] = new Option("لامرد", "716");
            options[18] = new Option("مهر", "719");
            options[19] = new Option("فراشبند", "712");
            options[20] = new Option("حاجي آباد ـ زرين دشت", "7119");
            options[21] = new Option("قائميه", "71191");
            options[22] = new Option("بوانات", "7135");
            options[23] = new Option("پاسارگاد", "7143");
            options[24] = new Option("خنج", "7136");
            options[25] = new Option("قیر و کارزین", "714");
            options[26] = new Option("گراش", "7140");
            options[27] = new Option("مصيري ـ رستم", "7142");
        }
        if (Indx == 66) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("خرم آباد", "669");
            options[2] = new Option("بروجرد", "668");
            options[3] = new Option("نورآباد ـ دلفان", "663");
            options[4] = new Option("كوهدشت", "661");
            options[5] = new Option("پل دختر", "664");
            options[6] = new Option("اليگودرز", "665");
            options[7] = new Option("ازنا", "667");
            options[8] = new Option("الشتر ـ سلسله", "666");
            options[9] = new Option("درود", "6610");
            options[10] = new Option("سراب دوره", "6611");
        }
        if (Indx == 61) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("اهواز", "618");
            options[2] = new Option("آبادان", "615");
            options[3] = new Option("خرمشهر", "6115");
            options[4] = new Option("ماهشهر", "6111");
            options[5] = new Option("بهبهان", "6113");
            options[6] = new Option("رامهرمز", "6117");
            options[7] = new Option("ايذه", "619");
            options[8] = new Option("شادگان", "6121");
            options[9] = new Option("سوسنگرد ـ دشت آزادگان", "6118");
            options[10] = new Option("شوشتر", "6120");
            options[11] = new Option("دزفول", "6116");
            options[12] = new Option("شوش", "6119");
            options[13] = new Option("انديمشك", "617");
            options[14] = new Option("مسجد سليمان", "612");
            options[15] = new Option("بندرامام خميني", "6112");
            options[16] = new Option("هنديجان", "613");
            options[17] = new Option("اميديه", "616");
            options[18] = new Option("باغ ملك", "6114");
            options[19] = new Option("هويزه", "614");
            options[20] = new Option("لالي", "611");
            options[21] = new Option("گتوند", "6110");
            options[22] = new Option("رامشیر", "6122");
            options[23] = new Option("اندیکا", "6124");
            options[24] = new Option("حمیدیه", "6125");
            options[25] = new Option("کارون", "6126");
            options[26] = new Option("هفتکل", "6127");
            options[27] = new Option("آغاجاری", "6128");
            options[28] = new Option("باوی", "6129");
        }
        if (Indx == 58) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("بجنورد", "583");
            options[2] = new Option("آشخانه ـ مانه و سلمقان", "581");
            options[3] = new Option("شيروان", "585");
            options[4] = new Option("اسفراين", "582");
            options[5] = new Option("فاروج", "586");
            options[6] = new Option("جاجرم", "587");
            options[7] = new Option("گرمه", "589");
        }
        if (Indx == 56) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("بیرجند", "562");
            options[2] = new Option("قائن ـ قائنات", "564");
            options[3] = new Option("فردوس", "567");
            options[4] = new Option("سربيشه", "563");
            options[5] = new Option("خضري", "568");
            options[6] = new Option("بشرویه", "566");
            options[7] = new Option("سرایان", "569");
            options[8] = new Option("نهبندان", "5691");
            options[9] = new Option("درميان", "565");
            options[10] = new Option("زیرکوه", "5610");
            options[11] = new Option("خوسف", "5612");
            options[12] = new Option("طبس", "5611");
        }
        if (Indx == 54) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("زاهدان", "547");
            options[2] = new Option("ايرانشهر", "544");
            options[3] = new Option("ميرجاوه", "541");
            options[4] = new Option("زابل", "548");
            options[5] = new Option("خاش", "545");
            options[6] = new Option("سرباز", "5410");
            options[7] = new Option("سراوان", "549");
            options[8] = new Option("چابهار", "543");
            options[9] = new Option("نيك شهر", "542");
            options[10] = new Option("راسك", "546");
            options[11] = new Option("فنوج", "5411");
            options[12] = new Option("کنارک", "5412");
            options[13] = new Option("زهک", "5413");
            options[14] = new Option("سوران ـ سيب سوران", "5414");
            options[15] = new Option("مهرستان - زابلی", "5415");
            options[16] = new Option("هيرمند ـ دوست محمد", "5416");
            options[17] = new Option("دلگان ـ گلمورتي", "5417");
        }
        if (Indx == 51) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("مشهد", "518");
            options[2] = new Option("نيشابور", "5110");
            options[3] = new Option("تربت حيدريه", "5115");
            options[4] = new Option("سبزوار", "5118");
            options[5] = new Option("طرقبه ـ بينالود", "5121");
            options[6] = new Option("چناران", "519");
            options[7] = new Option("سرخس", "5119");
            options[8] = new Option("فريمان", "511");
            options[9] = new Option("قوچان", "513");
            options[10] = new Option("درگز", "5117");
            options[11] = new Option("خواف", "5116");
            options[12] = new Option("تربت جام", "5114");
            options[13] = new Option("كاشمر", "517");
            options[14] = new Option("بردسكن", "5112");
            options[15] = new Option("گناباد", "5111");
            options[16] = new Option("بجستان", "5123");
            options[17] = new Option("تایباد", "5113");
            options[18] = new Option("جغتای", "512");
            options[19] = new Option("جوین", "515");
            options[20] = new Option("خلیل آباد", "5122");
            options[21] = new Option("رشتخوار", "514");
            options[22] = new Option("کلات", "516");
            options[23] = new Option("مه ولات ـ فيض آباد", "5125");
            options[24] = new Option("باخرز", "5126");
            options[25] = new Option("داورزن", "5127");
            options[26] = new Option("طوس", "5120");
            options[27] = new Option("زاوه ـ دولت آباد", "5129");
            options[28] = new Option("فيروزه ـ تخت جلگه", "5130");
        }
        if (Indx == 45) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("اردبيل", "459");
            options[2] = new Option("نمين", "455");
            options[3] = new Option("گرمي ", "457");
            options[4] = new Option("مشكين شهر", "453");
            options[5] = new Option("بيله سوار", "4510");
            options[6] = new Option("خلخال", "4511");
            options[7] = new Option("پارس آباد", "458");
            options[8] = new Option("اصلاندوز", "4582");
            options[9] = new Option("سرعين", "4512");
            options[10] = new Option("نير", "456");
            options[11] = new Option("گیوی-کوثر", "4514");
            options[12] = new Option("لاهرود", "4532");
        }
        if (Indx == 44) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("اروميه", "447");
            options[2] = new Option("خوي", "4411");
            options[3] = new Option("مهاباد", "441");
            options[4] = new Option("نقده", "444");
            options[5] = new Option("اشنويه", "448");
            options[6] = new Option("پيرانشهر", "446");
            options[7] = new Option("ماكو", "443");
            options[8] = new Option("چالدران", "445");
            options[9] = new Option("سلماس", "4412");
            options[10] = new Option("بوكان", "449");
            options[11] = new Option("سردشت", "4414");
            options[12] = new Option("مياندوآب", "442");
            options[13] = new Option("شاهين دژ", "4415");
            options[14] = new Option("تكاب", "4410");
            options[15] = new Option("شوط", "4416");
            options[16] = new Option("باروق", "4417");
            options[17] = new Option("قره ضياء الدين ـ چايپاره", "4418");
            options[18] = new Option("پلدشت", "4420");
            options[19] = new Option("سيه چشمه", "4413");
            options[20] = new Option("بازرگان", "4419");
        }
        if (Indx == 41) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("تبريز", "4118");
            options[2] = new Option("مرند", "416");
            options[3] = new Option("مراغه", "417");
            options[4] = new Option("اسكو", "4114");
            options[5] = new Option("آذرشهر", "4112");
            options[6] = new Option("شبستر", "4122");
            options[7] = new Option("هريس", "419");
            options[8] = new Option("هاديشهر", "418");
            options[9] = new Option("جلفا", "4120");
            options[10] = new Option("اهر", "4113");
            options[11] = new Option("كليبر", "412");
            options[12] = new Option("سراب", "4121");
            options[13] = new Option("بستان آباد", "4117");
            options[14] = new Option("عجب شير", "4123");
            options[15] = new Option("بناب", "4115");
            options[16] = new Option("ملكان", "414");
            options[17] = new Option("قره اغاج ـ چاراويماق", "411");
            options[18] = new Option("تسوج", "4119");
            options[19] = new Option("شرفخانه", "4116");
            options[20] = new Option("ورزقان", "4111");
            options[21] = new Option("خداآفرين", "413");
            options[22] = new Option("ممقان", "4125");
            options[23] = new Option("هشترود", "4136");
            options[24] = new Option("میانه", "4132");
            options[25] = new Option("صوفيان", "4124");
        }
        if (Indx == 38) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("شهركرد", "387");
            options[2] = new Option("فارسان", "381");
            options[3] = new Option("بروجن", "385");
            options[4] = new Option("اردل", "384");
            options[5] = new Option("لردگان", "382");
            options[6] = new Option("سامان", "386");
            options[7] = new Option("چلگرد ـ كوهرنگ", "383");
            options[8] = new Option("بن", "388");
            options[9] = new Option("شلمزار ـ كيار", "3810");
            options[10] = new Option("فرخ شهر", "3811");
            options[11] = new Option("هفشجان", "3812");
        }
        if (Indx == 35) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("يزد", "354");
            options[2] = new Option("اردكان", "356");
            options[3] = new Option("اشكذر ـ صدوق", "357");
            options[4] = new Option("ميبد", "352");
            options[5] = new Option("بافق", "358");
            options[6] = new Option("مهريز", "351");
            options[7] = new Option("تفت", "359");
            options[8] = new Option("طبس", "3520");
            options[9] = new Option("هرات ـ خاتم", "353");
            options[10] = new Option("ابرکوه", "355");
        }
        if (Indx == 34) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("كرمان", "342");
            options[2] = new Option("رفسنجان", "348");
            options[3] = new Option("سيرجان", "3411");
            options[4] = new Option("راور", "349");
            options[5] = new Option("بم", "343");
            options[6] = new Option("شهربابك", "345");
            options[7] = new Option("زرند", "3410");
            options[8] = new Option("بردسير", "346");
            options[9] = new Option("بافت", "344");
            options[10] = new Option("جيرفت", "347");
            options[11] = new Option("قلعه گنج", "3413");
            options[12] = new Option("کهنوج", "341");
        }
        if (Indx == 31) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("اصفهان", "3113");
            options[2] = new Option("شاهين شهر", "3122");
            options[3] = new Option("خميني شهر", "3116");
            options[4] = new Option("نجف آباد", "318");
            options[5] = new Option("شهرضا", "3121");
            options[6] = new Option("كاشان", "315");
            options[7] = new Option("دولت آباد", "3118");
            options[8] = new Option("اردستان", "3112");
            options[9] = new Option("نائين", "317");
            options[10] = new Option("فلاورجان", "311");
            options[11] = new Option("مباركه", "316");
            options[12] = new Option("فولادشهر", "312");
            options[13] = new Option("تيران و کرون", "3115");
            options[14] = new Option("فريدون شهر", "314");
            options[15] = new Option("سميرم", "3120");
            options[16] = new Option("آران وبيدگل", "3111");
            options[17] = new Option("نطنز", "319");
            options[18] = new Option("گلپايگان", "3110");
            options[19] = new Option("خوانسار", "3117");
            options[20] = new Option("باغ بهارداران", "3114");
            options[21] = new Option("زرين شهر ـ لنجان", "3119");
            options[22] = new Option("خور", "3123");
            options[23] = new Option("سپاهان شهر", "3124");
            options[24] = new Option("فريدن ـ داران", "3125");
            options[25] = new Option("دهاقان", "3126");
            options[26] = new Option("چادگان", "3127");
            options[27] = new Option("قهدریجان", "3128");
            options[28] = new Option("ميمه", "3129");
            options[29] = new Option("برخوار", "3130");
        }
        if (Indx == 28) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("قزوين", "281");
            options[2] = new Option("آبيك", "282");
            options[3] = new Option("تاكستان", "284");
            options[4] = new Option("بویین زهرا", "283");
            options[5] = new Option("البرز", "285");
            options[6] = new Option("محمودآباد نمونه", "286");
        }
        if (Indx == 26) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("كرج", "217");
            options[2] = new Option("نظرآباد", "2120");
            options[3] = new Option("آسارا", "2116");
            options[4] = new Option("اشتهارد", "2118");
            options[5] = new Option("طالقان", "2128");
            options[6] = new Option("هشتگرد-ساوجبلاغ", "2119");
            options[7] = new Option("چهارباغ", "2612");
            options[8] = new Option("محمدشهر", "2613");
            options[9] = new Option("ماهدشت", "2614");
        }
        if (Indx == 25) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("قم", "251");
        }
        if (Indx == 24) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("زنجان", "249");
            options[2] = new Option("زرين آباد ـ ايجرود", "2410");
            options[3] = new Option("ماهنشان", "243");
            options[4] = new Option("ابهر", "246");
            options[5] = new Option("خرمدره", "248");
            options[6] = new Option(" آب بر ـ طارم", "244");
            options[7] = new Option("قیدار", "241");
        }
        if (Indx == 23) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("سمنان", "235");
            options[2] = new Option("شاهرود", "236");
            options[3] = new Option("گرمسار", "231");
            options[4] = new Option("ايوانكي", "232");
            options[5] = new Option("دامغان", "234");
            options[6] = new Option("بسطام", "233");
            options[7] = new Option("سرخه", "237");
            options[8] = new Option("شهمیرزاد", "238");
            options[9] = new Option("مهدی شهر", "239");
            options[10] = new Option("آرادان", "2310");
            options[11] = new Option("میامی", "2311");
        }
        if (Indx == 21) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("تهران", "2121");
            options[2] = new Option("اسلام شهر", "2117");
            options[3] = new Option("لواسان", "218");
            options[4] = new Option("ورامين", "2126");
            options[5] = new Option("پاكدشت", "2115");
            options[6] = new Option("شهر قدس", "213");
            options[7] = new Option("رباط كريم", "2125");
            options[8] = new Option("دماوند", "2122");
            options[9] = new Option("فيروزكوه", "211");
            options[10] = new Option("بومهن", "2131");
            options[11] = new Option("قرچك", "214");
            options[12] = new Option("ملارد", "219");
            options[13] = new Option("چهاردانگه", "2110");
            options[14] = new Option("فشم", "212");
            options[15] = new Option("شهر جعفریه", "2124");
            options[16] = new Option("رودهن", "2123");
            options[17] = new Option("شريف آباد", "2127");
            options[18] = new Option("شهر ری", "21211");
            options[19] = new Option("اندیشه", "2138");
            options[20] = new Option("شهریار", "2113");
            options[21] = new Option("کهریزک", "216");
            options[22] = new Option("گلستان", "2114");
        }
        if (Indx == 17) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("گرگان", "175");
            options[2] = new Option("بندر گز", "178");
            options[3] = new Option("كردكوي", "172");
            options[4] = new Option("بندرتركمن", "179");
            options[5] = new Option("آق قلا", "176");
            options[6] = new Option("راميان", "1710");
            options[7] = new Option("آزادشهر", "177");
            options[8] = new Option("گنبدكاوس", "174");
            options[9] = new Option("مينودشت", "173");
            options[10] = new Option("كلاله", "171");
            options[11] = new Option("علی‌آباد کتول", "1711");
            options[12] = new Option("گالیکش", "1713");
            options[13] = new Option("مراوه تپه", "1714");
        }
        if (Indx == 15) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("آمل", "159");
            options[2] = new Option("بابل", "1512");
            options[3] = new Option("ساري", "1518");
            options[4] = new Option("محمودآباد", "153");
            options[5] = new Option("نور", "155");
            options[6] = new Option("نوشهر", "156");
            options[7] = new Option("چالوس", "157");
            options[8] = new Option("تنكابن", "1514");
            options[9] = new Option("رامسر", "1516");
            options[10] = new Option("بابلسر", "1513");
            options[11] = new Option("فريدون كنار", "151");
            options[12] = new Option("جويبار", "1515");
            options[13] = new Option("پل سفيد ـ سوادكوه", "158");
            options[14] = new Option("نكاء", "154");
            options[15] = new Option("بهشهر", "1511");
            options[16] = new Option("بلده", "1510");
            options[17] = new Option("قائمشهر", "15181");
            options[18] = new Option("سلمان شهر", "1519");
            options[19] = new Option("گلوگاه", "1520");
            options[20] = new Option("مرزن آباد", "1521");
        }
        if (Indx == 13) {
            options[0] = new Option("لطفا شهر خود را انتخاب کنيد", "");
            options[1] = new Option("رشت", "1315");
            options[2] = new Option("بندرانزلي", "1311");
            options[3] = new Option("لاهيجان", "1320");
            options[4] = new Option("فومن", "131");
            options[5] = new Option("صومعه سرا", "1319");
            options[6] = new Option("هشتپر ـ تالش", "137");
            options[7] = new Option("ماسال", "136");
            options[8] = new Option("آستارا", "139");
            options[9] = new Option("آستانه اشرفيه", "138");
            options[10] = new Option("منجيل", "134");
            options[11] = new Option("رودبار", "1314");
            options[12] = new Option("لنگرود", "133");
            options[13] = new Option("رودسر", "1313");
            options[14] = new Option("كلاچاي", "132");
            options[15] = new Option("شفت", "1318");
            options[16] = new Option("ماسوله", "135");
            options[17] = new Option("رضوان شهر", "1316");
            options[18] = new Option("املش", "1310");
            options[19] = new Option("سیاهکل", "1317");
            options[20] = new Option("لوشان", "1321");
        }
    }
}
$(document).ready(function () {
    var select_box = document.getElementById("id_state");
    if (select_box.value != 0) cityList(select_box.value);
});
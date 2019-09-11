<?php
/**
 * 用来查看拣货详情和异常
 * @author Simon 2017/11/22
 */
namespace Test\Controller;

use Order\Model\EbayOrderModel;
use Order\Model\OrderTypeModel;
use Package\Model\PickOrderDetailModel;
use Package\Model\PickOrderModel;
use Think\Controller;
use Think\Page;

class TestPickOrderController extends Controller
{
    /**
     * 检测重复的拣货单
     * @author Simon 2017/11/22
     */
    public function checkDuplicatePickOrders()
    {
        $pickOrderDetailModel = new PickOrderDetailModel();
        $request              = $_REQUEST;
        if ($request['start_time']) {
            $map['order_addtime'] = ['egt', strtotime($request['start_time'])];
        }
        if ($request['end_time']) {
            $map['order_addtime'] = ['elt', strtotime($request['start_time'])];
        }
        if (!empty($map['order_addtime'])) {
            die('必须有时间限制');
            $map['is_delete'] = 0;
            $page             = new Page();
            $items            = $pickOrderDetailModel->where($map)->field('ebay_id,sku')->group('sku,ebay_id')->limit($page->firstRow . ',' . $page->listRows)->select();
        }
        dump($items);
        $this->assign('items', $items);
        $this->display();
    }

    public function getCanPrintOrder()
    {
        $request         = $_REQUEST;
        $orderModel      = new EbayOrderModel();
        $pickOrderModel  = new PickOrderModel();
        $pickDetailModel = new PickOrderDetailModel();
        $orderTypeMode   = new OrderTypeModel();
        $ordersnS        = $pickOrderModel->where(['addtime' => ['egt', strtotime('2017-11-28 00:00:00'), 'isprint' => ['neq', 100]]])->getField('ordersn', true);
        $ebay_ids        = $orderModel->where([
            'ebay_status'  => ['in', [1723]],
            'ebay_addtime' => [
                ['egt', strtotime('2017-11-21 00:00:00')],
                ['elt', strtotime('2017-11-27 00:00:00')],
            ],
        ])->getField('ebay_id', true);
        $ebay_ids = [9697370,9703562,9712100,9712351,9712565,9744055,9744150,9744368,9744468,9744588,9744825,9747997,9749904,9764549,9764705,9775544,9796209,9796330,9796465,9796558,9803466,9809365,9810888,9811082,9818021,9818035,9818052,9818072,9818081,9818103,9819187,9819636,9819692,9838462,9838510,9842018,9851251,9860777,9861637,9865050,9873838,9874820,9874851,9874853,9877466,9877793,9880457,9887813,9889231,9894330,9899173,9899181,9908189,9912826,9912892,9931838,9937783,9939963,9939967,9944308,9944369,9944384,9955558,9958812,9963352,9964362,9964385,9964386,9966880,9970168,9970203,9970359,9973534,9977737,9978427,9984964,9987669,9989120,9989689,9993188,9994312,9995537,9996715,9998405,9998687,10012926,10013223,10013474,10015374,10017768,10019991,10023324,10024845,10024978,10025150,10025971,10029052,10029396,10030858,10030899,10031730,10033321,10033560,10035500,10037798,10038975,10040210,10042176,10047265,10047627,10047654,10047664,10050783,10051227,10052000,10052004,10056156,10056509,10056544,10057872,10060623,10061345,10061972,10066791,10067854,10076873,10082044,10082688,10086349,10087968,10089861,10092032,10092771,10093724,10095734,10096544,10101272,10101789,10102380,10102630,10104318,10105969,10105976,10105981,10106685,10107269,10109390,10109394,10109400,10113236,10116066,10117965,10118691,10120138,10121122,10121524,10124037,10126068,10129065,10130146,10130596,10130733,10131286,10132370,10133448,10133745,10135790,10143373,10144608,10145267,10146700,10147019,10148181,10149243,10149334,10149340,10150290,10150368,10151802,10151861,10152400,10153499,10154443,10154706,10154915,10154954,10155229,10155366,10155368,10155374,10155377,10155738,10156900,10158855,10160362,10160906,10161695,10161696,10161713,10161749,10163289,10163294,10163472,10163481,10163485,10163490,10164669,10165589,10166026,10166027,10166028,10166546,10167603,10167722,10167810,10168547,10168801,10168890,10169519,10171907,10173768,10173770,10174537,10176435,10177680,10179088,10179091,10179106,10179833,10180095,10181073,10182240,10182244,10182257,10185006,10185009,10185367,10185595,10185797,10185801,10187032,10187262,10187497,10187785,10187894,10188754,10188766,10188865,10188913,10189103,10189105,10189106,10189107,10189108,10189110,10189111,10189112,10189448,10189451,10189460,10189568,10189581,10189582,10189585,10189586,10189588,10189589,10189597,10189600,10189602,10189605,10189610,10189612,10189616,10189622,10189637,10189644,10189651,10189652,10189657,10191220,10191319,10191426,10191558,10191560,10191566,10191568,10191570,10191571,10191572,10191573,10191574,10191575,10191576,10191577,10191580,10191581,10191585,10191586,10191587,10191593,10191825,10191826,10191931,10193107,10193154,10193155,10193949,10194225,10195628,10195938,10196349,10196654,10200589,10202918,10202921,10202993,10204649,10205536,10206296,10207372,10207574,10207575,10208590,10209410,10210234,10210486,10210564,10211053,10214305,10214360,10214840,10214883,10215617,10215619,10215643,10215645,10215649,10215688,10215698,10215706,10215711,10217558,10217714,10217962,10218762,10219220,10221646,10221657,10222050,10222052,10222083,10223652,10224555,10225654,10226228,10227372,10227906,10227907,10227911,10227936,10228023,10229501,10229544,10229885,10229923,10230098,10230152,10230735,10232076,10232443,10232475,10232607,10232609,10232881,10232889,10233071,10233109,10233201,10233209,10233933,10234989,10235038,10235039,10235040,10235041,10235044,10235061,10235699,10235950,10236478,10237363,10238011,10238525,10238697,10238699,10238701,10238703,10238707,10238810,10238892,10240304,10240481,10240588,10242648,10243221,10243733,10243750,10244289,10245421,10245928,10246230,10246366,10246779,10247894,10249374,10249666,10250441,10250467,10250680,10250681,10250854,10250995,10251498,10251504,10251561,10251567,10251577,10251618,10251631,10251644,10251651,10251696,10251722,10251726,10251739,10252077,10252694,10252702,10252711,10252713,10252727,10253760,10254319,10255180,10255247,10255619,10256490,10256501,10257483,10258862,10260099,10260785,10261398,10261488,10263191,10263202,10263211,10263615,10265722,10266301,10266406,10267084,10267670,10269841,10270600,10270971,10271345,10271704,10271884,10272155,10272454,10272859,10273416,10273677,10277078,10277793,10277815,10278229,10278778,10278949,10280433,10281216,10281251,10281577,10281643,10281684,10281846,10282233,10282343,10282575,10282639,10283158,10283351,10283957,10284282,10286015,10286556,10286576,10286893,10286947,10287111,10287277,10287491,10287756,10288275,10288306,10288366,10288499,10288647,10288925,10289144,10290126,10290235,10290557,10290837,10290957,10292275,10292329,10292521,10292674,10293359,10293570,10294210,10294277,10294527,10294639,10295745,10295959,10296059,10296071,10296397,10296434,10296522,10296526,10296757,10296778,10297371,10297672,10297827,10298565,10298789,10298844,10298868,10299138,10299526,10299772,10299865,10299885,10300029,10301468,10301470,10301555,10302340,10302965,10303199,10303215,10303514,10303977,10304048,10304110,10304247,10304438,10304474,10304628,10304703,10304973,10305708,10305753,10305873,10306220,10306479,10306527,10306568,10306604,10306609,10306990,10307149,10307210,10307634,10307966,10308074,10308557,10308641,10309235,10309281,10309297,10309448,10309924,10310119,10310123,10310511,10310758,10310856,10311143,10311170,10311206,10311358,10311421,10311870,10311911,10312021,10312094,10313426,10313949,10313977,10314021,10314142,10314425,10314525,10314748,10315021,10315389,10315442,10315917,10316136,10316157,10316467,10317564,10317663,10317726,10318487,10318488,10318673,10318859,10319072,10319193,10319532,10319607,10319687,10320080,10320081,10320316,10320421,10320502,10320723,10321189,10321251,10321533,10322297,10322484,10322800,10322886,10323031,10323915,10324627,10324634,10324686,10324699,10324749,10325069,10325077,10325289,10325301,10325561,10325565,10325936,10326034,10326355,10326357,10326361,10326362,10326498,10326810,10326918,10327076,10327344,10327396,10327410,10327412,10327413,10327426,10327896,10327960,10327961,10327994,10327995,10328294,10328794,10328860,10328861,10328862,10328933,10328938,10329232,10329466,10329849,10330054,10330305,10330330,10330760,10330813,10330814,10331140,10331196,10331200,10331467,10331652,10332128,10332158,10332177,10332233,10332374,10332441,10332664,10332692,10332892,10332958,10332987,10333112,10333520,10333682,10333685,10333736,10333756,10334011,10334343,10334458,10334483,10335223,10335522,10335687,10335701,10335728,10335845,10336211,10336256,10336257,10336265,10336277,10336576,10336733,10336734,10336885,10337025,10337026,10337095,10337473,10337727,10337730,10337795,10337823,10337841,10337907,10337913,10337986,10338068,10338176,10338409,10338558,10339338,10339359,10339365,10339604,10339630,10339727,10339737,10339751,10339814,10339896,10339912,10340080,10340450,10340466,10340558,10340575,10340595,10340960,10341251,10341335,10341407,10341641,10341791,10341856,10342213,10342267,10342312,10342394,10342711,10342800,10342873,10342881,10343069,10343157,10343224,10343268,10343412,10343540,10343602,10343684,10343755,10343905,10344163,10344318,10344745,10345083,10346038,10346685,10346834,10346927,10347145,10347295,10347611,10347661,10347879,10347969,10348013,10348426,10348879,10348986,10349092,10349177,10349759,10349845,10350029,10350056,10350073,10350192,10350295,10350336,10350337,10350343,10350353,10350653,10350654,10350709,10350723,10350743,10350749,10350775,10350784,10351088,10351255,10351363,10351440,10352388,10352438,10352782,10352988,10353012,10353137,10353153,10353155,10353159,10353378,10353401,10353491,10353522,10353761,10353926,10353942,10354178,10354222,10354260,10354315,10354363,10354461,10354513,10354634,10354695,10354833,10354856,10355128,10355396,10355451,10355492,10355524,10355597,10355598,10355664,10355829,10355986,10356359,10356511,10356719,10356893,10356988,10357058,10357147,10357194,10357483,10357552,10357616,10357886,10358256,10358541,10358578,10358784,10358802,10358852,10358863,10359195,10359221,10359224,10359298,10359365,10359366,10359435,10359442,10359452,10360051,10360175,10360192,10360333,10360441,10360521,10361005,10361038,10361302,10361424,10361516,10361523,10361541,10361590,10361635,10361665,10361717,10362163,10362273,10362583,10362674,10362716,10362875,10363558,10363824,10363831,10363898,10363935,10364110,10364142,10364263,10364361,10364363,10364394,10364451,10364453,10364549,10364724,10364741,10364832,10364940,10364945,10365130,10365163,10365315,10365324,10365505,10365569,10365690,10365715,10365750,10365760,10365854,10365995,10366087,10366179,10366328,10366408,10366769,10366840,10366949,10366978,10367004,10367474,10367500,10367521,10367610,10367763,10367802,10367862,10368003,10368090,10368171,10368408,10368409,10368416,10368456,10368492,10368514,10368559,10368635,10368779,10368854,10368902,10369012,10369097,10369144,10369193,10369240,10369241,10369287,10369376,10369567,10369603,10369894,10369901,10369934,10370019,10370045,10370215,10370235,10370241,10370242,10370246,10370258,10370269,10370270,10370271,10370314,10370387,10370407,10370483,10370610,10370689,10370903,10370949,10371113,10371116,10371121,10371277,10371295,10371298,10371450,10371606,10371838,10371842,10371843,10371943,10372016,10372183,10372305,10372541,10372588,10372861,10373057,10373095,10373097,10373129,10373397,10373398,10373445,10373652,10373653,10373757,10373818,10373832,10373866,10374026,10374178,10374312,10374498,10374928,10375005,10375095,10375130,10375480,10375512,10375686,10375688,10375694,10376247,10376382,10376428,10376484,10376608,10376785,10377010,10377021,10377039,10377268,10377272,10377386,10377469,10377530,10377581,10377593,10377662,10377701,10377758,10377814,10377829,10377851,10378052,10378084,10378104,10378130,10378145,10378255,10378261,10378269,10378303,10378368,10378542,10378586,10378605,10378646,10378855,10378856,10378999,10379005,10379150,10379166,10379212,10379218,10379232,10379235,10379237,10379238,10379244,10379248,10379257,10379261,10379266,10379273,10379294,10379300,10379301,10379307,10379311,10379313,10379316,10379319,10379325,10379331,10379335,10379336,10379346,10379348,10379576,10379613,10379743,10379807,10379811,10379813,10379963,10379978,10380107,10380118,10380244,10380331,10380609,10380768,10380780,10380818,10380928,10381164,10381413,10381450,10381684,10381853,10381879,10381880,10381960,10381961,10382030,10382155,10382236,10382378,10382439,10382482,10382519,10382581,10382641,10382835,10382956,10383121,10383447,10383562,10383577,10383714,10383813,10383836,10383837,10383890,10384153,10384283,10384296,10384299,10384325,10384334,10384339,10384346,10384378,10384391,10384422,10384423,10384460,10384644,10384808,10385081,10385252,10385348,10385422,10385672,10386171,10386424,10386440,10386484,10386495,10386681,10386750,10386797,10386866,10386903,10386912,10386922,10387003,10387041,10387239,10387299,10387301,10387566,10387798,10387998,10388062,10388088,10388109,10388116,10388274,10388468,10388537,10388970,10389128,10389160,10389309,10389469,10389508,10389526,10389551,10389567,10389688,10389728,10390094,10390095,10390096,10390098,10390382,10390407,10390456,10390578,10390687,10390705,10390768,10390786,10390788,10390799,10390968,10391119,10391228,10391292,10391338,10391358,10391531,10391533,10391534,10391839,10391943,10391949,10392058,10392060,10392179,10392223,10392266,10392347,10392365,10392452,10392926,10392972,10393029,10393207,10393253,10393287,10393331,10393653,10393768,10394195,10394423,10394433,10394441,10394508,10394734,10394942,10395072,10395085,10395218,10395303,10395331,10395491,10395609,10395668,10395958,10396020,10396348,10396450,10396543,10396610,10396695,10396714,10397134,10397195,10397203,10397404,10397548,10397549,10397674,10397717,10398029,10398069,10398257,10398383,10398609,10398628,10398675,10398705,10398857,10398888,10398901,10398968,10399138,10399147,10399231,10399365,10399389,10399517,10399619,10399634,10399638,10399900,10399901,10399920,10400037,10400064,10400404,10400438,10400706,10400749,10400845,10400970,10401142,10401173,10401221,10401298,10401634,10401711,10401848,10401961,10402142,10402226,10402235,10402331,10402373,10402516,10402659,10402719,10403318,10403701,10403789,10403948,10403985,10403991,10403992,10404143,10404196,10404202,10404346,10404471,10404568,10404720,10404747,10404805,10404808,10404820,10404861,10404961,10405132,10405204,10405245,10405291,10405352,10405361,10405635,10405641,10405666,10405674,10405785,10405855,10405856,10405871,10405943,10406077,10406452,10406580,10406585,10406610,10406642,10406830,10406901,10407102,10407239,10407370,10407390,10407502,10407680,10407768,10407806,10407932,10408533,10408660,10408734,10408754,10408873,10408896,10409052,10409431,10409433,10409443,10409655,10409741,10409789,10409793,10409849,10409990,10410082,10410097,10410148,10410205,10410261,10410391,10410467,10410612,10410614,10410655,10410656,10410678,10410684,10410694,10410707,10410757,10410769,10410771,10410786,10410792,10410856,10410882,10410891,10410983,10411090,10411104,10411117,10411216,10411329,10411590,10411718,10412083,10412154,10412462,10412501,10412618,10412626,10412700,10412824,10413023,10413057,10413109,10413166,10413286,10413324,10413724,10413818,10413990,10414072,10414159,10414160,10414269,10414299,10414391,10414436,10414514,10414800,10414808,10415079,10415144,10415159,10415197,10415385,10415563,10415611,10415629,10415850,10415917,10415964,10416118,10416391,10416439,10416549,10416572,10416708,10416709,10416716,10416826,10416886,10417079,10417179,10417313,10417467,10417676,10418035,10418039,10418061,10418119,10418405,10418490,10418504,10418508,10418509,10418515,10418570,10418573,10418634,10418698,10418766,10418816,10418945,10418959,10419023,10419229,10419286,10419655,10419667,10419699,10419701,10419921,10419934,10420325,10420328,10420376,10420402,10420679,10421070,10421199,10421276,10421489,10422008,10422036,10422266,10422352,10422354,10422438,10422446,10422468,10422610,10422670,10422706,10422718,10422730,10422914,10422915,10422944,10422952,10422965,10422976,10422996,10423038,10423216,10423249,10423294,10423355,10423375,10423741,10423835,10423845,10423980,10424047,10424094,10424437,10424679,10424697,10425343,10425458,10425646,10425664,10425689,10425730,10425759,10425812,10426076,10426078,10426087,10426241,10426263,10426266,10426463,10426592,10426639,10426667,10426755,10426813,10426843,10426844,10426926,10426981,10426988,10427309,10427335,10427417,10427548,10427671,10427682,10427763,10427825,10427922,10427977,10428050,10428086,10428220,10428328,10428429,10428670,10428799,10428901,10428954,10428960,10428962,10428965,10429031,10429122,10429210,10429344,10429637,10429757,10429771,10429922,10429950,10430036,10430222,10430232,10430264,10430404,10430502,10430504,10430615,10430618,10430677,10430725,10430847,10430970,10430974,10430983,10431458,10431549,10431644,10431648,10431655,10431795,10432125,10432236,10432248,10432271,10432430,10432527,10432542,10432647,10432789,10432793,10432794,10432798,10432813,10433131,10433245,10433250,10433252,10433377,10433491,10433520,10433541,10433853,10433954,10433991,10434095,10434145,10434151,10434205,10434272,10434313,10434353,10434502,10434558,10434733,10434842,10434966,10435016,10436231,10436239,10439185,10439250,10439459,10439603,10439791,10441313,10441382,10441419,10441423,10441440,10441449,10441454,10441554,10441556,10441742,10442623,10442788,10442894,10442906,10443004,10444009,10444207,10444256,10444258,10445959,10445961,10445982,10445997,10446024,10446028,10446179,10447957,10447993,10449565,10449711,10449728,10449838,10449846,10449861,10450262,10451093,10451835,10452343,10453285,10453457,10454636,10455016,10455023,10459227
        ];
//        print_r(implode(',',$ebay_ids));die;
        //        dump(count($ordersnS));die;
               // dump(count($ebay_ids));

        foreach ($ebay_ids as $k => $ebay_id) {
            $map['ebay_id'] = ['eq', $ebay_id];
            $map['ordersn'] = ['in', $ordersnS];
            $map['_string'] = 'is_delete = 0 and is_normal=1';
            $ss             = $pickDetailModel->where($map)->find();
            if (!empty($ss)) {
                unset($ebay_ids[$k]);
            }
        }
//        dump(count($ordersnS));
        //        die;
        if ($request['rType'] == 1) {
            $orderTypeMode->where(['ebay_id' => ['in', $ebay_ids]])->setField('pick_status', 1);
        }
//        dump(count($ebay_ids));
        print_r(implode(',', $ebay_ids));
//        dump($ebay_ids);
        //        $inPickOrderEbayIds = $pickDetailModel->where(['ordersn'=>['in',$ordersnS],'ebay_id'=>['in',$ebay_ids],'is_delete'=>['eq',0]])->group('ebay_id')->getField('ebay_id',true);
        //        dump($inPickOrderEbayIds);
        //
        //        $ordersns = $pickDetailModel->where(['ebay_id'=>['in',$ebay_ids]])->getField('ordersn',true);
        //        dump($ordersns);
        //        $pkods = $pickOrderModel->where(['ordersn'=>['in',$ordersns],'addtime'=>['egt',strtotime('2017-11-28 00:00:00')]])->select();
        //        dump($pkods);
        //        print_r(implode(',',$inPickOrderEbayIds));
        //        dump($ebay_ids);
        //        dump($inPickOrderEbayIds);
        //        $notInPickOrderEbayIds = array_diff($ebay_ids,$inPickOrderEbayIds);
        //        dump($notInPickOrderEbayIds);
        //        print_r(implode(',',$notInPickOrderEbayIds));
    }

    public function test()
    {
    }
}

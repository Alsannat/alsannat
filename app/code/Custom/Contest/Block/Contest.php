<?php

namespace Custom\Contest\Block;

/**
 * Contest content block
 */
class Contest extends \Magento\Framework\View\Element\Template
{
    /**
     * Contest collection
     *
     * @var Custom\Contest\Model\ResourceModel\Contest\Collection
     */
    protected $_contestCollection = null;
    
    /**
     * Contest factory
     *
     * @var \Custom\Contest\Model\ContestFactory
     */
    protected $_contestCollectionFactory;
    
    /** @var \Custom\Contest\Helper\Data */
    protected $_dataHelper;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Custom\Contest\Model\ResourceModel\Contest\CollectionFactory $contestCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Custom\Contest\Model\ResourceModel\Contest\CollectionFactory $contestCollectionFactory,
        \Custom\Contest\Helper\Data $dataHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_contestCollectionFactory = $contestCollectionFactory;
        $this->_dataHelper = $dataHelper;
        $this->_storeManager = $storeManager;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve contest collection
     *
     * @return Custom\Contest\Model\ResourceModel\Contest\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_contestCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared contest collection
     *
     * @return Custom\Contest\Model\ResourceModel\Contest\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_contestCollection)) {
            $this->_contestCollection = $this->_getCollection();
            $this->_contestCollection->setCurPage($this->getCurrentPage());
            $this->_contestCollection->setPageSize($this->_dataHelper->getContestPerPage());
            $this->_contestCollection->setOrder('published_at','asc');
        }

        return $this->_contestCollection;
    }
    
    /**
     * Fetch the current page for the contest list
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getData('current_page') ? $this->getData('current_page') : 1;
    }
    
    /**
     * Return URL to item's view page
     *
     * @param Custom\Contest\Model\Contest $contestItem
     * @return string
     */
    public function getItemUrl($contestItem)
    {
        return $this->getUrl('*/*/view', array('id' => $contestItem->getId()));
    }
    
    public function getFormAction(){
        return $this->getUrl('contest/index/submit', ['_secure' => true]);
    }
    /**
     * Return URL for resized Contest Item image
     *
     * @param Custom\Contest\Model\Contest $item
     * @param integer $width
     * @return string|false
     */
    public function getImageUrl($item, $width)
    {
        return $this->_dataHelper->resize($item, $width);
    }
    
    /**
     * Get a pager
     *
     * @return string|null
     */
    public function getPager()
    {
        $pager = $this->getChildBlock('contest_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $contestPerPage = $this->_dataHelper->getContestPerPage();

            $pager->setAvailableLimit([$contestPerPage => $contestPerPage]);
            $pager->setTotalNum($this->getCollection()->getSize());
            $pager->setCollection($this->getCollection());
            $pager->setShowPerPage(TRUE);
            $pager->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            );

            return $pager->toHtml();
        }

        return NULL;
    }

    public function getCountryList(){
        $storeCode = $this->_storeManager->getStore()->getCode();
        $contries = [];
        //sa
        if($storeCode == 'en'){
            $contries = ["Saudi Arabia","Egypt","United Arab Emirates","Sudan","Kuwait","Oman","Bahrain","Yemen","Qatar","Iraq","Algeria","Syria","Somalia","Tunisia","Morocco","Libya","Jordan","Palestine","Lebanon","Mauritania","Djibouti","Comoros","Afghanistan","Albania","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","The Bahamas","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombia","Congo","Republic of the","Congo","Democratic Republic of the","Costa Rica","Cote d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Dominica","Dominican Republic","East Timor (Timor-Leste)","Ecuador","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","The Gambia","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Ireland","Israel","Italy","Jamaica","Japan","Kazakhstan","Kenya","Kiribati","Korea","North","Korea","South","Kosovo","Kyrgyzstan","Laos","Latvia","Lesotho","Liberia","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritius","Mexico","Micronesia","Federated States of","Moldova","Monaco","Mongolia","Montenegro","Mozambique","Myanmar (Burma)","Namibia","Nauru","Nepal","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent and the Grenadines","Samoa","San Marino","Sao Tome and Principe","Senegal","Serbia","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","South Africa","South Sudan","Spain","Sri Lanka","Suriname","Swaziland","Sweden","Switzerland","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Kingdom","United States of America","Uruguay","Uzbekistan","Vanuatu","Vatican City (Holy See)","Venezuela","Vietnam","Zambia","Zimbabwe"];
        }else{
            $contries = ["السعودية","مصر","السودان","الأردن","الأمارات","الكويت","عمان","قطر","البحرين","المغرب","الجزائر","العراق","لبنان","فلسطين","اليمن","سوريا","تونس","الصومال","ليبيا","أريتريا","موريتانيا","جيبوتي","جزر القمر","الصحراء الغربية"," الصين"," الهند"," الولايات المتحدة"," إندونيسيا"," البرازيل"," باكستان"," نيجيريا"," بنغلاديش"," روسيا"," اليابان"," المكسيك"," الفلبين"," فيتنام"," إثيوبيا"," مصر"," ألمانيا"," إيران"," تركيا"," جمهورية الكونغو الديمقراطية"," تايلاند"," فرنسا"," المملكة المتحدة"," إيطاليا","بورما"," جنوب أفريقيا"," كوريا الجنوبية"," كولومبيا"," إسبانيا"," أوكرانيا"," تنزانيا"," كينيا"," الأرجنتين"," بولندا"," السودان"," الجزائر"," العراق"," كندا"," أوغندا"," المغرب"," بيرو"," أوزبكستان"," ماليزيا"," فنزويلا"," نيبال"," أفغانستان"," كوريا الشمالية"," غانا"," اليمن"," موزمبيق"," تايوان"," أستراليا"," ساحل العاج"," سوريا"," مدغشقر"," أنغولا"," الكاميرون"," سريلانكا"," رومانيا"," بوركينا فاسو"," النيجر"," كازاخستان"," هولندا"," تشيلي"," مالاوي"," الإكوادور"," غواتيمالا"," مالي"," السنغال"," زامبيا"," زيمبابوي"," تشاد"," جنوب السودان"," كوبا"," بلجيكا"," غينيا"," اليونان"," تونس"," البرتغال"," رواندا"," التشيك"," الصومال"," هايتي"," بنين"," بوروندي"," بوليفيا"," المجر"," السويد"," بيلاروسيا"," جمهورية الدومينيكان"," أذربيجان"," النمسا"," هندوراس"," الإمارات"," سويسرا"," إسرائيل"," طاجيكستان"," بلغاريا"," صربيا"," هونغ كونغ (الصين)"," بابوا غينيا الجديدة"," باراغواي"," لاوس"," الأردن"," السلفادور"," إريتريا"," ليبيا"," توغو"," سيراليون"," نيكاراجوا"," الدنمارك"," قرغيزستان"," فنلندا"," سلوفاكيا"," سنغافورة"," تركمانستان"," النرويج"," لبنان"," كوستاريكا"," جمهورية أفريقيا الوسطى"," جمهورية أيرلندا"," جورجيا"," نيوزيلندا"," جمهورية الكونغو"," فلسطين Authority"," ليبيريا"," كرواتيا"," البوسنة والهرسك"," سلطنة عمان"," بورتوريكو (الولايات المتحدة)"," الكويت"," مولدافيا"," موريتانيا"," بنما"," أوروغواي"," أرمينيا"," ليتوانيا"," ألبانيا"," منغوليا"," جامايكا"," ناميبيا"," ليسوتو"," سلوفينيا"," جمهورية مقدونيا"," بوتسوانا"," لاتفيا"," قطر"," غامبيا"," غينيا بيساو"," الغابون"," غينيا الاستوائية"," ترينيداد وتوباغو"," إستونيا"," موريشيوس"," سوازيلاند"," البحرين"," تيمور الشرقية"," جيبوتي"," قبرص"," فيجي"," ريونيون (فرنسا)"," غويانا"," بوتان"," جزر القمر"," الجبل الأسود"," ماكاو (الصين)"," الصحراء الغربية"," لوكسمبورغ"," سورينام"," الرأس الأخضر"," مالطا"," جوادلوب (فرنسا)"," مارتينيك (فرنسا)"," بروناي"," البهاما"," آيسلندا"," جزر المالديف"," بليز"," باربادوس"," بولينزيا الفرنسية (فرنسا)"," فانواتو"," كاليدونيا الجديدة (فرنسا)"," غويانا الفرنسية (فرنسا)"," مايوت (فرنسا)"," ساموا"," ساو تومي وبرينسيب"," سانت لوسيا"," غوام (الولايات المتحدة)"," كوراساو (هولندا)"," سانت فنسنت والجرينادين"," كيريباتي"," جزر العذراء الأمريكية (الولايات المتحدة)"," جرينادا"," تونجا"," أروبا (هولندا)"," ولايات ميكرونيسيا المتحدة"," جيرزي (المملكة المتحدة)"," سيشيل"," أنتيغوا وباربودا"," جزيرة مان (المملكة المتحدة)"," أندورا"," دومينيكا"," برمودا (المملكة المتحدة)"," جيرنزي (المملكة المتحدة)"," جرينلاند (الدنمارك)"," جزر مارشال"," ساموا الأمريكية (الولايات المتحدة)"," جزر كايمان (المملكة المتحدة)"," جزر ماريانا الشمالية (الولايات المتحدة)"," جزر فارو (الدنمارك)"," سينت مارتن (هولندا)"," سانت مارتن الفرنسية (فرنسا)"," ليختنشتاين"," موناكو"," سان مارينو","(المملكة المتحدة)"," جبل طارق (منطقة حكم ذاتي) (المملكة المتحدة)"," الجزر العذراء البريطانية (المملكة المتحدة)"," أولند"," الجزر الكاريبية الهولندية (هولندا)"," بالاو"," جزر كوك (نيوزيلندا)"," أنجويلا (المملكة المتحدة)"," والس وفوتونا (فرنسا)"," ناورو"," سان بارتليمي (فرنسا)"," سان بيار وميكلون (فرنسا)"," مونتسرات (المملكة المتحدة)"," سانت هيلين (المملكة المتحدة)"," سفالبارد ويان ماين (النرويج)"," جزر فوكلاند (المملكة المتحدة)"," جزيرة نورفولك (أستراليا)"," جزيرة عيد الميلاد (أستراليا)"," نييوي (نيوزيلندا)"," توكلو (نيوزيلندا)"," الفاتيكان"," جزر كوكس (أستراليا)"," جزر بيتكيرن (المملكة المتحدة)"];
        }
        
        return $contries;
    }

    public function getSACityList(){

        $storeCode = $this->_storeManager->getStore()->getCode();

        if($storeCode == 'en'){
            $cities = ["Buraidah","Abha","Riyadh","Duwadimi","Haqil","Quwei'ieh","Hail","Khobar","Yanbu","Qurayat","Tabuk","Makkah","Ahad Rufaidah","Khafji","Jumum","Zulfi","Qatif","Jeddah","Qunfudah","Khamis Mushait","Safwa","Arar","Jizan","Sharourah","Unayzah","Madinah","Hafer Al Batin","Dhahran","Turaif","Al Baha","Ahad Masarha","Seihat","ONAIZA","MUHAYIL","Shaqra","Hofuf","Dammam","Bisha","Domat Al Jandal","Turba","Muzahmiah","Sakaka","Majarda","Bukeiriah","Qariya Al Olaya","Laith","Dhurma","Midinhab","HAWTAT BANI TAMIM","Ras Tanura","Jubail","Khulais","Manakh","Duba","Taif","Kharj","Alghat","Mohayel Aseer","AlRass","Bader","Riyadh Al Khabra","Najran","Badaya","Mubaraz","Abu Areish","Abqaiq","Khurma","Majma","Sulaiyl","Wadi El Dwaser","Umluj","Tarut","Dere'iyeh","Balasmar","Wajeh (Al Wajh)","Tayma","Rafha","Afif","Tatleeth","Anak","Horaimal","Noweirieh","Dawadmi","Sabya","Rabigh","Qassim","Al Hassa","Samtah","Jouf","Oula","Thumair","Daelim","Thadek","Towal","Khaibar","Hareeq"];
        }else{
            $cities = ["بريدة","أبها","مدينة الرياض","الدوادمي","حقيل","القويعية","وابل","مدينه الخبر","ينبع","القريات","تبوك","مكه","احد رفيدة","الخفجي","جموم","الزلفي","القطيف","جدة","القنفذة","خميس مشيط","صفوى","عرعر","جيزان","شرورة","عنيزة","المدينة المنورة","حفر الباطن","الظهران","طريف","الباحة","احد المسارحه","سيهات","عنيزة","محايل","شقراء","الهفوف","الدمام","بيشة","دومة الجندل","توربا","مزاحمية","سكاكا","ماجاردة","البكيرية","قرية العليا","ليث","ضرما","مدينهاب","حوطة بني تميم","رأس تنورة","الجبيل","خليص","المناخ","ضباء","الطائف","الخرج","الغاط","محايل عسير","الرس","بدر","رياض الخبراء","نجران","بدية","مبرز","ابو عريش","بقيق","خورما","المجمعة","سليل","وادي الدواسر","أملج","تاروت","الدرعية","بلاسمار","الوجه (الوجه)","تيماء","رفحاء","عفيف","تاتليث","عنك","حوريمال","نويرية","الدوادمي","صبيا","رابغ","قاسم","الأحساء","صامتة","الجوف","علا","ثمير","ديليم","ثاديك","توال","خيبر","حريق"];
        }

        return $cities;
    }
}

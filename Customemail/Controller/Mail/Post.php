<?php
namespace NewTask\Customemail\Controller\Mail;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;

class Post extends \Magento\Framework\App\Action\Action
{
    protected $_inlineTranslation;
    protected $_scopeConfig;
    protected $_logLoggerInterface;
    protected $storeManager;
    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $loggerInterface,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        array $data = []
    ) {
        $this->_inlineTranslation = $inlineTranslation;
        $this->_scopeConfig = $scopeConfig;
        $this->_logLoggerInterface = $loggerInterface;
        $this->messageManager = $context->getMessageManager();
        $this->storeManager = $storeManager;

        parent::__construct($context);
        $this->transportBuilder = $transportBuilder;
    }

    public function execute()
    {
        try {
            $post = $this->getRequest()->getPost();
            $receiverInfo = [
            'name' => $post['name'],
            'email' => $post['email']];
            $store = $this->storeManager->getStore();
            $templateParams = ['store' => $store, 'customer_name' => $receiverInfo['name']
        ];

            $transport = $this->transportBuilder->setTemplateIdentifier(
                'customemail_email_template'
            )->setTemplateOptions(
            [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ]
        )->addTo(
            $receiverInfo['email'],
            $receiverInfo['name']
        )->setTemplateVars(
            $templateParams
        )->setFrom(
            'general'
        )->getTransport();

            $transport->sendMessage();
            echo "success";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}

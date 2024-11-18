<?php
namespace Jdom\LazyImg\Plugin;

class BlockPlugin
{
    protected $request;
    protected $scopeConfig;

    /**
     * Constructor for BlockPlugin class.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
    }

    public function afterToHtml(\Magento\Framework\View\Element\AbstractBlock $block, $html)
    {
        $isEnabled = $this->scopeConfig->getValue('jdom_lazyimg/general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (!$isEnabled) {
            return $html;
        }

        $imagen = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNiYAAAAAkAAxkR2eQAAAAASUVORK5CYII=';

        $pixelSrc = 'src="' . $imagen . '"';
        $tmpSrc = 'TMP_SRC';
      
        $html = str_replace($pixelSrc, $tmpSrc, $html);
        $html = preg_replace('#<img\s+([^>]*)(?:src="([^"]*)")([^>]*)\/?>#isU', '<img ' . $pixelSrc . ' data-origen="$2" $1 $3/>', $html);
        $html = str_replace($tmpSrc, $pixelSrc, $html);

        return $html;
    }
}
?>

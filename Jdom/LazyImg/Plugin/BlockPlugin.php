<?php
namespace Jdom\LazyImg\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class BlockPlugin
{
    protected $request;
    protected $scopeConfig;
    protected $logger;
    /**
     * Constructor for BlockPlugin class.
     *
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }
    /**
     * Modifies the HTML of blocks after rendering.
     * @param AbstractBlock $block
     * @param string $html
     * @return string
     */
    public function afterToHtml(AbstractBlock $block, $html)
    {
        $isEnabled = $this->scopeConfig->getValue(
            'jdom_lazyimg/general/enabled',
            ScopeInterface::SCOPE_STORE
        );

        // Return original HTML if the module is not enabled
        if (!$isEnabled) {
            return $html;
        }

        try {
            $placeholderImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNiYAAAAAkAAxkR2eQAAAAASUVORK5CYII=';
            $placeholderSrc = 'src="' . $placeholderImage . '"';
            $tempSrcPlaceholder = 'TMP_SRC';

            // Temporarily replace src to avoid conflicts during processing
            $html = str_replace($placeholderSrc, $tempSrcPlaceholder, $html);

            // Add data-origen attribute and set placeholder image
            $html = preg_replace(
                '#<img\s+([^>]*)(?:src="([^"]*)")([^>]*)\/?>#isU',
                '<img ' . $placeholderSrc . ' data-source="$2" $1 $3/>',
                $html
            );

            // Restore placeholder src
            $html = str_replace($tempSrcPlaceholder, $placeholderSrc, $html);

            return $html;
            } catch (\Exception $e) {
            // Log the exception for debugging purposes
            $this->logger->error('Error in LazyImg BlockPlugin: ' . $e->getMessage(), ['exception' => $e]);

            // Return the original HTML in case of an error
            return $html;
        }
    }
}

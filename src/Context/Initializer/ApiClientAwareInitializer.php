<?php
namespace MtkIp\BehatApiExtension\Context\Initializer;

use Mtkip\BehatApiExtension\Context\ApiClientAwareContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use GuzzleHttp\Client;

/**
 * API client aware initializer
 *
 * Initializer for feature contexts that implements the ApiClientAwareContext interface.
 *
 *  @author Salim Muthalib <salim@connect.auto>
 */
class ApiClientAwareInitializer implements ContextInitializer {
    /**
     * @var string
     */
    private $baseUrl;
    /**
     * Class constructor
     *
     * @param string $baseUri
     */
    public function __construct($baseUrl) {
        $this->baseUrl = $baseUrl;
    }
    
    /**
     * Initialize the context
     *
     * Inject the Guzzle client if the context implements the ApiClientAwareContext interface
     *
     * @param Context $context
     */
    public function initializeContext(Context $context) {
        if ($context instanceof ApiClientAwareContext) {
            $context->setClient(new Client(['base_url' => $this->baseUrl]));
        }
    }
}
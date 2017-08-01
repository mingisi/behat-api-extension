<?php
namespace Mingisi\BehatApiExtension\Context\Initializer;

use Mingisi\BehatApiExtension\Context\ApiClientAwareContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Cookie\CookieJar;


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
    private $baseUri;

    /**
     * @var CookieJarInterface
     */
    private $cookie;

    /**
     * Class constructor
     *
     * @param string $baseUri
     */
    public function __construct($baseUri) {
        $this->baseUri = $baseUri;
        $this->cookie = new CookieJar();
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
            $context->setClient(new Client(['base_uri' => $this->baseUri, 'cookies' => $this->cookie]));
        }
    }
}
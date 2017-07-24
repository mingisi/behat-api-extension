<?php
namespace Imbo\BehatApiExtension\Context;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7;

use Assert\Assertion;
use Assert\AssertionFailedException;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use InvalidArgumentException;
use RuntimeException;
use stdClass;

/**
 * Behat feature context that can be used to simplify testing of JSON-based RESTful HTTP APIs
 *
 *  @author Salim Muthalib <salim@connect.auto>
 */
class ApiContext implements ApiClientAwareContext, SnippetAcceptingContext {
 
    /**
     * {@inheritdoc}
     */
    public function setClient(ClientInterface $client) {
        $this->client = $client;
        $this->request = new Request('GET', $client->getConfig('base_uri'));
        return $this;
    }

}
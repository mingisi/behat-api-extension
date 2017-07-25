<?php
namespace MtkIp\BehatApiExtension\Context;

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
use Exception;
use stdClass;

use Coduo\PHPMatcher\Factory\SimpleFactory;

/**
 * Behat feature context that can be used to simplify testing of JSON-based/XML HTTP APIs
 *
 *  @author Salim Muthalib <salim@connect.auto>
 */
class ApiContext implements ApiClientAwareContext, SnippetAcceptingContext {
 

    private $cookie;

    /**
     * Guzzle client
     *
     * @var Client
     */
    private $client;

    /**
     * Request instance
     *
     * The request instance will be created once the client is ready to send it.
     *
     * @var RequestInterface
     */
    protected $request;

     /**
     * Response instance
     *
     * The response object will be set once the request has been made.
     *
     * @var ResponseInterface
     */
    private $response;

    private $user;

    private $factory;

    private $matcher;

    private $requestId;

    private $requestOptions;

    public function __construct()
    {
        $this->factory = new SimpleFactory();
        $this->matcher = $this->factory->createMatcher();
    }

    /**
     * {@inheritdoc}
     */
    public function setClient(ClientInterface $client) {
        $this->client = $client;
        $this->request = new Request('GET', $client->getConfig('base_uri'));

        return $this;
    }
    
    /**
     * @Given I am authenticating as:
     */
    public function login(TableNode $table)
    {
        $this->user = $table->getRowsHash();

        $this->sendRequest('POST', '/login', [
            'form_params' => [
                'username' => $this->user['username'],
                'password' => $this->user['password']
            ]
        ]);

        $this->setRequestId();
    }

    /**
     * @When I request :url
     * @When I send a request request :url
     */
    public function iRequest($url)
    {
        $this->sendRequest('GET', $url);
    }

    /**
     * @When I send a :method request to :url
     */
    public function sendRequest($method, $url, $requestOptions = [])
    {
        $this->request = $this->request->withMethod($method);
        $this->setRequestPath($url);
        $this->response = $this->client->send(
            $this->request,
            $requestOptions
        );
    }

    /**
     * @When I send a :method request to :url with body:
     */
    public function iSendAPostRequestToWithBody($method, $url, PyStringNode $json)
    {
        $data = json_decode($json, true);

        if (array_key_exists('requestid', $data)) {
            $data['requestid'] = $this->requestId;
        }

        $this->sendRequest($method, $url, ['json' => $data]);
    }


    /**
     * @Then print response
     */
    public function printResponse()
    {
        $body = $this->response->getBody();

        echo sprintf(
             "%s %s => %d:\n%s",
            $this->request->getMethod(),
            $this->request->getUri(),
            $this->response->getStatusCode(),
            $this->response->getBody()
        );
    }


     /**
     * @Then the response code should be :statusCode
     * @Then the response code is :statusCode
     */
    public function isResponseCode($statusCode)
    {
        $actualStatusCode = $this->response->getStatusCode();
        Assertion::eq($statusCode, $actualStatusCode);
    }

    /**
     * @Then the response body contains JSON:
     */
    public function theResponseShouldContainJson(PyStringNode $jsonString)
    {
        
        $etalon = $this->getAssociativeArray($jsonString);
        $data = $this->getAssociativeArray($this->response->getBody());
        $match = $this->matcher->match($data, $etalon);

        try {
            Assertion::true($match);
        } catch (AssertionFailedException $e) {
            throw new Exception($this->matcher->getError());
        }
    }

    /**
     * @Then the response body contains XML:
     */
    public function theResponseBodyContainsXml(PyStringNode $xmlString)
    {
        $body = (string) $this->response->getBody();

        $match = $this->matcher->match($body, (string) $xmlString);

        try {
            Assertion::true($match);
        } catch (AssertionFailedException $e) {
            throw new Exception($this->matcher->getError());
        }
    }


    private function setRequestId()
    {
        $body = $this->response->getBody();
        preg_match("/requestId = '(.*)'/", $body, $match);
        $this->requestId = $match[1];
    }



    private function getAssociativeArray($jsonString)
    {
        
        $assocArr = json_decode((string) $jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('The string does not contain valid JSON data.');
        } elseif (!is_array($assocArr) && !($assocArr instanceof stdClass)) {
            throw new InvalidArgumentException('The string does not contain a valid JSON array / object.');
        }

        return $assocArr;
    }

    private function setRequestPath($path)
    {
        $uri = Psr7\Uri::resolve($this->client->getConfig('base_uri'), Psr7\uri_for($path));
        $this->request = $this->request->withUri($uri);

        return $this;
    }
    



}
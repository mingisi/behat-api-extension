<?php
namespace Mtkip\BehatApiExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use GuzzleHttp\ClientInterface;
use InvalidArgumentException;
/**
 * Behat API extension
 *
 * This extension provides a series of steps that can be used to easily test API's. The ApiContext
 * class also exposes the client, request and response objects so custom steps using the underlying
 * client can be implemented.
 *
 * @author Salim Muthalib <salim@connect.auto>
 */
class BehatApiExtension implements ExtensionInterface {

    /**
     * Service ID for the client initializer
     *
     * @var string
     */
    const CLIENT_ID = 'api_extension.client';

    /**
     * Config key for the extension
     *
     * @var string
     */
    const CONFIG_KEY = 'api_extension';

    /**
     * {@inheritdoc}
     */
    public function getConfigKey() {
        return self::CONFIG_KEY;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function initialize(ExtensionManager $extensionManager) {
        // Not used
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder) {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('base_url')
                    ->defaultValue('http://localhost')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function load(ContainerBuilder $container, array $config) {
        // Client initializer definition
        var_dump($config);
        $clientInitializerDefinition = new Definition(
            'Mtkip\BehatApiExtension\Context\Initializer\ApiClientAwareInitializer',
            [
                $config['base_url']
            ]
        );
        $clientInitializerDefinition->addTag(ContextExtension::INITIALIZER_TAG);
        $container->setDefinition(self::CLIENT_ID, $clientInitializerDefinition);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function process(ContainerBuilder $container) {
    }
}
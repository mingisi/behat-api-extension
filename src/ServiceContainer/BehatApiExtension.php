<?php
namespace Mingisi\BehatApiExtension\ServiceContainer;

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
 * @author Salim Muthalib <salim@connect.auto>
 */
class BehatApiExtension implements ExtensionInterface {

    /**
     * Service ID for the client initializer
     *
     * @var string
     */
    const CLIENT_ID = 'mingisi_api_extension.client';

    /**
     * Config key for the extension
     *
     * @var string
     */
    const CONFIG_KEY = 'mingisi_api_extension';

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
                ->scalarNode('base_uri')
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
        echo "\n\n>>>> im here mingisi \n\n";
        $clientInitializerDefinition = new Definition(
            'Mingisi\BehatApiExtension\Context\Initializer\ApiClientAwareInitializer',
            [
                $config['base_uri']
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
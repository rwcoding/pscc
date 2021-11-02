<?php
namespace Rwcoding\Pscc;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Rwcoding\Pscc\Core\Application;
use Rwcoding\Pscc\Core\Context;
use Rwcoding\Pscc\Core\ContextFactory;
use Rwcoding\Pscc\Core\Config;
use Rwcoding\Pscc\Core\Router;
use Rwcoding\Pscc\Exception\ExceptionHandle;
use Rwcoding\Pscc\Lang\Lang;
use Rwcoding\Pscc\Util\ConsoleUtil;
use Illuminate\Validation\Factory as Validator;
use Illuminate\Translation\Translator;

/**
 * @property Config $config
 * @property ConsoleUtil $console
 * @property Router $router
 * @property Application $app
 * @property ExceptionHandle $exception
 * @property Translator $translator
 * @property Validator $validator
 */
class Di extends Container
{
    private static ?Di $instance = null;

    public static function my(): Di
    {
        if (!self::$instance) {
            new Di();
        }
        return self::$instance;
    }

    /**
     * @return Context
     */
    public function context(): Context
    {
        if (PHP_SAPI === 'cli') {
            return ContextFactory::newConsoleContext();
        }
        return ContextFactory::newWebContext();
    }

    public function __construct()
    {
        self::$instance = $this;
        $this->setMultiple([
            'config'    => "\Rwcoding\Pscc\Core\Config",
            'exception' => "\Rwcoding\Pscc\Exception\ExceptionHandle",
            'router'    => "\Rwcoding\Pscc\Core\Router",
            'app'       => "\Rwcoding\Pscc\Core\Application",
            'console'   => "\Rwcoding\Pscc\Util\ConsoleUtil",
            'event'     => "\Rwcoding\Pscc\Core\Event",
            'validator' => function() {
                return new Validator($this->translator);
            },
        ]);
        $this->get('exception');
        Lang::setLang();
    }

    public function init(array $data = []): void
    {
        if (!empty($data['timeZone'])) {
            date_default_timezone_set($data['timeZone']);
        }

        if (!empty($data['lang'])) {
            Lang::setLang($data['lang']);
        }

        if (!empty($data['locale_path'])) {
            $localePath = $data['locale_path'];
            $locale = $data['locale'] ?? 'en';
            $localeFallback = $data['locale_fallback'] ?? '';
            $this->set('translator', function () use($localePath, $locale, $localeFallback) {
                $translator = new Translator(new FileLoader(new Filesystem(), $localePath), $locale);
                if ($localeFallback) {
                    $translator->setFallback($localeFallback);
                }
                return $translator;
            });
        }

        if (!empty($data['components'])) {
            foreach ($data['components'] as $key=>$value) {
                if (!$this->hasObject($key) &&
                    $this->hasDefinition($key) &&
                    is_array($value) &&
                    empty($value['__class'])) {
                    $value['__class'] = $this->getDefinition($key);
                }
                $this->set($key, $value);
            }
        }
    }
}
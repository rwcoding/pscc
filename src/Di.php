<?php
namespace Rwcoding\Pscc;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Rwcoding\Pscc\Core\Application;
use Rwcoding\Pscc\Core\Context;
use Rwcoding\Pscc\Core\ContextFactory;
use Rwcoding\Pscc\Core\Config;
use Rwcoding\Pscc\Core\Logger;
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
 * @property \Swoole\Server|\Swoole\Http\Server $ss
 * @property Logger $logger
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
        if (self::inCli()) {
            return ContextFactory::consoleContext();
        }
        return ContextFactory::webContext();
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
            'logger'    => "\Rwcoding\Pscc\Core\Logger",
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
            foreach ($data['components'] as $key => $value) {
                if ($value instanceof \Closure) {
                    $this->set($key, $value);
                    continue;
                }
                if ($this->has($key)) {
                    $old = $this->getDefinition($key);
                    if ($old instanceof \Closure) {
                        $this->set($key, $value);
                        continue;
                    }
                    if (is_string($old)) {
                        $old = ['__class'=>$old];
                    }
                    if (is_array($value)) {
                        $value = array_merge($old, $value);
                    }
                    if (is_string($value)) {
                        $value = array_merge($old, ['__class'=>$value]);
                    }
                }
                $this->set($key, $value);
            }
        }
    }

    public static function inWeb(): bool
    {
        return defined("PSCC_IN_HTTP") || PHP_SAPI != "cli";
    }

    public static function inCli(): bool
    {
        return !defined("PSCC_IN_HTTP") || PHP_SAPI == "cli";
    }

    public static function inConsole(): bool
    {
        return PHP_SAPI == "cli";
    }
}
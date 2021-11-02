<?php

namespace Rwcoding\Pscc\Core\Web;

use Rwcoding\Pscc\Lang\Lang;
use Rwcoding\Pscc\Core\Meta;

class View extends Meta
{
    private bool $isRender = false;
    private string $layout = 'layout';

    private ?View $topView = null;

    /**
     * @var callable
     */
    private $findViewFile;

    /**
     * @var callable
     */
    private $findLayoutFile;

    /**
     * @var callable
     */
    private $findBlockFile;

    private array $params = [];
    private array $runtimeParams = [];

    public array $scriptFiles = [];
    public array $styleFiles = [];

    public function __get(string $name)
    {
        $method = 'get' . $name;
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        if (isset($this->params[$name])) {
            return $this->params[$name];
        }

        if (isset($this->runtimeParams[$name])) {
            return $this->runtimeParams[$name];
        }

        if ($this->topView) {
            return $this->topView->$name;
        }

        return null;
    }

    public function __set(string $name, $value)
    {
        $method = 'set' . $name;
        if (method_exists($this, $method)) {
            $this->$method($value);
            return;
        }
        $this->runtimeParams[$name] = $value;
    }

    public function setLayout(string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }

    public function setFindViewFile(callable $cb): self
    {
        $this->findViewFile = $cb;
        return $this;
    }

    public function setFindLayoutFile(callable $cb): self
    {
        $this->findLayoutFile = $cb;
        return $this;
    }

    public function setFindBlockFile($cb): self
    {
        $this->findBlockFile = $cb;
        return $this;
    }

    public function setTopView(View $view): void
    {
        $this->topView = $view;
    }

    public function getTopView(): ?self
    {
        return $this->topView;
    }

    public function assign(array $data): self
    {
        $this->params = array_merge($this->params, $data);
        return $this;
    }

    public function renderScript(string $pos = '')
    {
        if ($pos) {
            foreach ($this->scriptFiles[$pos]??[] as $file) {
                echo '<script src="'.$file.'"></script>';
            }
        } else {
            foreach ($this->scriptFiles as $file) {
                if (is_array($file)) continue;
                echo '<script src="'.$file.'"></script>';
            }
        }
    }

    public function renderStyle(string $pos = '')
    {
        if ($pos) {
            foreach ($this->styleFiles[$pos]??[] as $file) {
                echo '<link rel="stylesheet" href="'.$file.'">';
            }
        } else {
            foreach ($this->styleFiles as $file) {
                if (is_array($file)) continue;
                echo '<link rel="stylesheet" href="'.$file.'">';
            }
        }
    }

    public function loadScriptFile(string $script, string $pos = '')
    {
        if (!$pos) {
            $this->scriptFiles[] = $script;
            return;
        }
        if (!isset($this->scriptFiles[$pos])) {
            $this->scriptFiles[$pos] = [];
        }
        $this->scriptFiles[$pos][] = $script;
    }

    public function loadStyleFile(string $style, string $pos = '')
    {
        if (!$pos) {
            $this->styleFiles[] = $style;
            return;
        }
        if (!isset($this->styleFiles[$pos])) {
            $this->styleFiles[$pos] = [];
        }
        $this->styleFiles[$pos][] = $style;
    }

    public function render(string $view, array $params = []): string
    {
        if ($this->isRender) {
            throw new \Exception(Lang::t("view-render-repeat", $view));
        }
        $this->isRender = true;
        $viewFile = call_user_func($this->findViewFile, $view);
        $content = $this->load($viewFile, $params);
        if ($this->layout) {
            $layoutFile = call_user_func($this->findLayoutFile, $this->layout);
            return $this->load($layoutFile, ['content' => $content]);
        }
        return $content;
    }

    public function load(string $viewFile, array $params = []): string
    {
        if (!file_exists($viewFile)) {
            throw new \Exception(Lang::t("view-not-found",$viewFile));
        }
        ob_start();
        ob_implicit_flush(1);
        if (!empty($params)) {
            extract($params, EXTR_OVERWRITE);
        }
        require $viewFile;
        return ob_get_clean();
    }

    public function block(string $block, array $params = [], bool $isReturn = false): ?string
    {
        $viewFile = call_user_func($this->findBlockFile, $block);
        $content = $this->load($viewFile, $params);
        if ($isReturn) {
            return preg_replace("/\s{3,}/i", "", str_replace(["\r","\n"], "", $content));
        } else {
            echo $content;
        }
        return null;
    }

    public function run(?array $params = null): string
    {
        throw new \Exception(Lang::t("view-widget-implement-run"));
    }

    public function widget(string $class, array $params = [])
    {
        $widget = new $class($this);
        if (!$widget instanceof WidgetInterface) {
            throw new \Exception(Lang::t("view-widget-instance", $class, WidgetInterface::class));
        }
        echo $widget->run($params);
    }
}
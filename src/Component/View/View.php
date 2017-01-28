<?php

namespace Blend\Component\View;

use Blend\Component\Templating\TemplateEngineInterface;
use Blend\Framework\Support\Runtime\RuntimeProviderInterface;
use Blend\Component\Filesystem\PathInformation;
use Blend\Component\Model\ModelInterface;

abstract class View
{
    /**
     * @var TemplateEngineInterface
     */
    protected $renderer;

    /**
     * @var RuntimeProviderInterface
     */
    protected $runtime;

    /**
     * Array folding the view data
     * @var mixed
     */
    protected $viewData;

    /**
     * @var bool
     */
    protected $applyParentParams;

    protected abstract function getViewFile();

    public function __construct(
    TemplateEngineInterface $renderer, RuntimeProviderInterface $runtime)
    {
        $this->renderer = $renderer;
        $this->runtime = $runtime;
        $this->reset();
        $this->applyParentParams = false;
    }

    /**
     * Apply the parent Views parameters to this View
     * @param type $value
     */
    public function applyParentParameters()
    {
        $this->applyParentParams = true;
    }

    /**
     * Check if the parent View parameters should be passed to this View
     * @return type
     */
    public function shouldApplyParentParameters()
    {
        return $this->applyParentParams;
    }

    /**
     * Reset/clear the view data
     */
    public function reset()
    {
        $this->viewData = array();
    }

    /**
     * Sets the view data by an array
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->viewData = array_merge($this->viewData, $data);
    }

    /**
     * Sets the view data by a Model
     * @param ModelInterface $model
     */
    public function setModel(ModelInterface $model)
    {
        $this->viewData = array_merge($this->viewData, $model->getData());
    }

    /**
     * Renders the view optionally taking additional data parameters
     * @param array $data
     * @return string
     */
    public function render(array $data = null)
    {
        $viewFileInfo = new PathInformation($this->getViewFile());
        $this->renderer->setViewPaths(array($viewFileInfo->getDirectoryName()));

        if ($data !== null && is_array($data)) {
            $this->setData($data);
        }
        foreach ($this->viewData as $key => $value) {
            if ($value instanceof View) {
                $this->viewData[$key] = $value->render($value->shouldApplyParentParameters() === true ? $this->viewData : array());
            }
        }
        $r = $this->runtime->getRequest();
        $this->viewData = array_merge(array(
            'request' => $this->runtime->getRequest(),
            'runtime' => $this->runtime,
            'is_authenticated' => !($this->runtime->getCurrentUser()->isGuest() === true),
                ), $this->viewData);
        return $this->renderer->render($viewFileInfo->getBasename(), $this->viewData);
    }

    public function __toString()
    {
        return $this->render();
    }
}

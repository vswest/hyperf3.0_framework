<?php

declare(strict_types=1);

namespace VsWest\Framework\Service;

abstract class AbstractService
{
    /**
     * @var \Hyperf\Database\Model\Model
     */
    protected $model;

    public function __construct()
    {
        $modelClass  = str_replace(['\Service', 'Service'], ['\Model', ''], get_class($this));
        $this->model = make($modelClass);
    }
}

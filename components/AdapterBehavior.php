<?php

namespace asimet\red\components;

use yii\base\Behavior;

abstract class AdapterBehavior extends Behavior {

    abstract protected function push();
    abstract protected function pull($uuid);
    
}

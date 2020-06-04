<?php

declare(strict_types=1);

namespace atk4\data\Reference;

use atk4\data\Exception;
use atk4\data\Model;
use atk4\data\Persistence\ArrayOfStrings;
use atk4\data\Reference;

/**
 * ContainsMany reference.
 */
class ContainsMany extends ContainsOne
{
    /**
     * Returns default persistence. It will be empty at this point.
     *
     * @see ref()
     */
    protected function getDefaultPersistence(Model $model): Persistence
    {
        $m = $this->owner;

        // model should be loaded
        /* Imants: it looks that this is not actually required - disabling
        if (!$m->loaded()) {
            throw (new Exception('Model should be loaded!'))
                ->addMoreInfo('model', get_class($m));
        }
        */

        // set data source of referenced array persistence
        $rows = $m->get($this->our_field) ?: [];
        /*
        foreach ($rows as $id=>$row) {
            $rows[$id] = $this->owner->persistence->typecastLoadRow($m, $row); // we need this typecasting because we set persistence data directly
        }
        */

        $data = [$this->table_alias => $rows ?: []];
        $p = new ArrayOfStrings($data);

        return $p;
    }

    /**
     * Returns referenced model.
     */
    public function ref(array $defaults = []): Model
    {
        // get model
        // will not use ID field (no, sorry, will have to use it)
        $m = $this->getModel(array_merge($defaults, [
            'contained_in_root_model' => $this->owner->contained_in_root_model ?: $this->owner,
            //'id_field'              => false,
            'table' => $this->table_alias,
        ]));

        // set some hooks for ref_model
        foreach ([Model::HOOK_AFTER_SAVE, Model::HOOK_AFTER_DELETE] as $spot) {
            $m->onHook($spot, function ($model) {
                $rows = $model->persistence->getRawDataByTable($this->table_alias);
                $this->owner->save([$this->our_field => $rows ?: null]);
            });
        }

        return $m;
    }
}

<?php

declare(strict_types=1);

namespace atk4\data\Reference;

use atk4\data\Exception;
use atk4\data\Field;
use atk4\data\Model;
use atk4\data\Reference;

/**
 * Reference\HasMany class.
 */
class HasMany extends Reference
{
    /**
     * Returns our field value or id.
     *
     * @return mixed
     */
    protected function getOurValue()
    {
        if ($this->owner->loaded()) {
            return $this->our_field
                ? $this->owner->get($this->our_field)
                : $this->owner->id;
        }

        // create expression based on existing conditions
        return $this->owner->action(
            'field',
            [
                $this->our_field ?: ($this->owner->id_field ?: 'id'),
            ]
        );
    }

    /**
     * Returns our field or id field.
     */
    protected function referenceOurValue(): Field
    {
        $this->owner->persistence_data['use_table_prefixes'] = true;

        return $this->owner->getField($this->our_field ?: ($this->owner->id_field ?: 'id'));
    }

    /**
     * Returns referenced model with condition set.
     *
<<<<<<< develop
     * @param array $defaults Properties
=======
     * @throws Exception
>>>>>>> Move types to code if possible
     */
    public function ref(array $defaults = []): Model
    {
        return $this->getModel($defaults)
            ->addCondition(
                $this->their_field ?: ($this->owner->table . '_' . ($this->owner->id_field ?: 'id')),
                $this->getOurValue()
            );
    }

    /**
     * Creates model that can be used for generating sub-query actions.
     *
<<<<<<< develop
     * @param array $defaults Properties
=======
     * @throws Exception
>>>>>>> Move types to code if possible
     */
    public function refLink(array $defaults = []): Model
    {
        return $this->getModel($defaults)
            ->addCondition(
                $this->their_field ?: ($this->owner->table . '_' . ($this->owner->id_field ?: 'id')),
                $this->referenceOurValue()
            );
    }

    /**
     * Adds field as expression to owner model.
     * Used in aggregate strategy.
     *
<<<<<<< develop
     * @param string $n        Field name
     * @param array  $defaults Properties
=======
     * @param string $n Field name
     *
     * @throws Exception
>>>>>>> Move types to code if possible
     */
    public function addField(string $n, array $defaults = []): Field
    {
        if (!isset($defaults['aggregate']) && !isset($defaults['concat']) && !isset($defaults['expr'])) {
            throw (new Exception('Aggregate field requires "aggregate", "concat" or "expr" specified to hasMany()->addField()'))
                ->addMoreInfo('field', $n)
                ->addMoreInfo('defaults', $defaults);
        }

        $defaults['aggregate_relation'] = $this;

        $field_n = $defaults['field'] ?? $n;
        $field = $defaults['field'] ?? null;

        if (isset($defaults['concat'])) {
            $defaults['aggregate'] = $this->owner->dsql()->groupConcat($field_n, $defaults['concat']);
            $defaults['read_only'] = false;
            $defaults['never_save'] = true;
        }

        if (isset($defaults['expr'])) {
            $cb = function () use ($defaults, $field) {
                $r = $this->refLink();

                return $r->action('field', [$r->expr(
                    $defaults['expr'],
                    $defaults['args'] ?? null
                ), 'alias' => $field]);
            };
            unset($defaults['args']);
        } elseif (is_object($defaults['aggregate'])) {
            $cb = function () use ($defaults, $field) {
                return $this->refLink()->action('field', [$defaults['aggregate'], 'alias' => $field]);
            };
        } elseif ($defaults['aggregate'] === 'count' && !isset($defaults['field'])) {
            $cb = function () use ($defaults, $field) {
                return $this->refLink()->action('count', ['alias' => $field]);
            };
        } elseif (in_array($defaults['aggregate'], ['sum', 'avg', 'min', 'max', 'count'], true)) {
            $cb = function () use ($defaults, $field_n) {
                return $this->refLink()->action('fx0', [$defaults['aggregate'], $field_n]);
            };
        } else {
            $cb = function () use ($defaults, $field_n) {
                return $this->refLink()->action('fx', [$defaults['aggregate'], $field_n]);
            };
        }

        $e = $this->owner->addExpression($n, array_merge([$cb], $defaults));

        return $e;
    }

    /**
     * Adds multiple fields.
     *
     * @see addField()
     *
<<<<<<< develop
     * @param array $fields Array of fields
=======
     * @throws Exception
>>>>>>> Move types to code if possible
     *
     * @return $this
     */
    public function addFields(array $fields = []): self
    {
        foreach ($fields as $field) {
            $name = $field[0];
            unset($field[0]);
            $this->addField($name, $field);
        }

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace atk4\data\UserAction;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\data\UserAction\Generic is deprecated. Use atk4\data\Model\UserAction instead', E_USER_DEPRECATED);
}

/**
 * @deprecated use \atk4\data\Model\UserAction instead - will be removed in dec-2020
 */
class Generic extends \atk4\data\Model\UserAction
{
<<<<<<< develop
=======
    use DIContainerTrait;
    use TrackableTrait;
    use InitializerTrait {
        init as init_;
    }

    /** Defining scope of the action */
    const NO_RECORDS = 'none'; // e.g. add
    const SINGLE_RECORD = 'single'; // e.g. archive
    const MULTIPLE_RECORDS = 'multiple'; // e.g. delete
    const ALL_RECORDS = 'all'; // e.g. truncate

    /** @var string by default - action is for a single-record */
    public $scope = self::SINGLE_RECORD;

    /** Defining action modifier */
    public const MODIFIER_CREATE = 'create'; // create new record(s).
    public const MODIFIER_UPDATE = 'update'; // update existing record(s).
    public const MODIFIER_DELETE = 'delete'; // delete record(s).
    public const MODIFIER_READ = 'read'; // just read, does not modify record(s).

    /** @var string How this action interact with record. default = 'read' */
    public $modifier = self::MODIFIER_READ;

    /** @var callable code to execute. By default will call method with same name */
    public $callback;

    /** @var callable code, identical to callback, but would generate preview of action without permanent effect */
    public $preview;

    /** @var string caption to put on the button */
    public $caption;

    /** @var string a longer description of this action */
    public $description;

    /** @var bool Specifies that the action is dangerous. Should be displayed in red. */
    public $dangerous = false;

    /** @var bool|string|callable Set this to "true", string or return the value from the callback. Will ask user to confirm. */
    public $confirmation = false;

    /** @var array UI properties, e,g. 'icon'=>.. , 'warning', etc. UI implementation can interpret or extend. */
    public $ui = [];

    /** @var bool|callable setting this to false will disable action. Callback will be executed with ($m) and must return bool */
    public $enabled = true;

    /** @var bool system action will be hidden from UI, but can still be explicitly triggered */
    public $system = false;

    /** @var array Argument definition. */
    public $args = [];

    /** @var array|bool Specify which fields may be dirty when invoking action. NO_RECORDS|SINGLE_RECORD scopes for adding/modifying */
    public $fields = [];

    /** @var bool Atomic action will automatically begin transaction before and commit it after completing. */
    public $atomic = true;

    public function init(): void
    {
        $this->init_();
    }

    /**
     * Attempt to execute callback of the action.
     *
<<<<<<< develop
     * @param mixed ...$args
=======
     * @throws Exception
>>>>>>> Move types to code if possible
     *
     * @return mixed
     */
    public function execute(...$args)
    {
        // todo - ACL tests must allow

        try {
            if ($this->enabled === false || (is_callable($this->enabled) && call_user_func($this->enabled) === false)) {
                throw new Exception('This action is disabled');
            }

            // Verify that model fields wouldn't be too dirty
            if (is_array($this->fields)) {
                $too_dirty = array_diff(array_keys($this->owner->dirty), $this->fields);

                if ($too_dirty) {
                    throw (new Exception('Calling action on a Model with dirty fields that are not allowed by this action.'))
                        ->addMoreInfo('too_dirty', $too_dirty)
                        ->addMoreInfo('dirty', array_keys($this->owner->dirty))
                        ->addMoreInfo('permitted', $this->fields);
                }
            } elseif (!is_bool($this->fields)) {
                throw (new Exception('Argument `fields` for the action must be either array or boolean.'))
                    ->addMoreInfo('fields', $this->fields);
            }

            // Verify some scope cases
            switch ($this->scope) {
                case self::NO_RECORDS:
                    if ($this->owner->loaded()) {
                        throw (new Exception('This action scope prevents action from being executed on existing records.'))
                            ->addMoreInfo('id', $this->owner->id);
                    }

                    break;
                case self::SINGLE_RECORD:
                    if (!$this->owner->loaded()) {
                        throw new Exception('This action scope requires you to load existing record first.');
                    }

                    break;
            }

            $run = function () use ($args) {
                if ($this->callback === null) {
                    $cb = [$this->owner, $this->short_name];
                } elseif (is_string($this->callback)) {
                    $cb = [$this->owner, $this->callback];
                } else {
                    array_unshift($args, $this->owner);
                    $cb = $this->callback;
                }

                return call_user_func_array($cb, $args);
            };

            if ($this->atomic) {
                return $this->owner->atomic($run);
            }

            return $run();
        } catch (Exception $e) {
            $e->addMoreInfo('action', $this);

            throw $e;
        }
    }

    /**
     * Identical to Execute but display a preview of what will happen.
     *
<<<<<<< develop
     * @param mixed ...$args
=======
     * @throws Exception
>>>>>>> Move types to code if possible
     *
     * @return mixed
     */
    public function preview(...$args)
    {
        if ($this->preview === null) {
            throw new Exception('You must specify preview callback explicitly');
        } elseif (is_string($this->preview)) {
            $cb = [$this->owner, $this->preview];
        } else {
            array_unshift($args, $this->owner);
            $cb = $this->preview;
        }

        return call_user_func_array($cb, $args);
    }

    /**
     * Get description of this current action in a user-understandable language.
     */
    public function getDescription(): string
    {
        return $this->description ?? 'Will execute ' . $this->caption;
    }

    /**
     * Return confirmation message for action.
     *
     * @return string|false
     */
    public function getConfirmation()
    {
        $confirmation = $this->confirmation;

        if (is_callable($confirmation)) {
            $confirmation = $confirmation($this);
        }

        if ($confirmation === true) {
            $confirmation = 'Are you sure you wish to ' . $this->caption . ' ' . $this->owner->getTitle() . '?';
        }

        return $confirmation;
    }

    /**
     * Return model associated with this action.
     */
    public function getModel(): Model
    {
        return $this->owner;
    }
>>>>>>> Move types to code if possible
}

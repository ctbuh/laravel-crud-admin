<?php

namespace ctbuh\Admin\Fields;

use ctbuh\Admin\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class HasMany
 * https://github.com/z-song/laravel-admin/blob/e0a871f7fed00c3d51219f080d5dba8b9273c0de/src/Form/Field/HasMany.php#L286
 *
 * @package ctbuh\Admin\Fields
 */
class HasMany extends Field
{
    protected $view = 'has_many';

    /**
     * @var HasOneOrMany
     */
    protected $relation; // TODO: let's not do this maybe?

    protected $related_resource;
    protected $related_fields;

    protected $field_builder;
    protected $fields = array();

    public function __construct($name, $label = null, $relation_name = null)
    {
        parent::__construct($name, $label);

        if (empty($relation_name)) {
            $relation_name = guess_relationship($name);
        }

        $this->setRelationship($relation_name);
        $this->init();
    }

    public function onLoad()
    {
        $this->relation = $this->resource->{$this->getRelationshipName()}();
        $this->related_resource = $this->relation->getRelated();
    }

    private function init()
    {
        $relation_name = $this->relation_name;

        $this->formatValueUsing(function ($resource) use ($relation_name) {
            return $resource->{$relation_name};
        });

        $this->formatGridValueUsing(function ($resource) use ($relation_name) {
            $models = $resource->{$relation_name};

            return collect($models)->map(function ($model) {
                return $model->label();
            });

        });
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function withFields($closure)
    {
        $this->field_builder = $closure;
        return $this;
    }

    /**
     * @return Field[]
     */
    public function getRelatedFields()
    {
        if (is_callable($this->field_builder)) {
            $this->fields = call_user_func($this->field_builder);
        }

        return $this->fields;
    }

    /**
     * @return Model
     */
    public function getRelatedResource()
    {
        return $this->related_resource;
    }

    /**
     * new?viaResource=users&viaResourceId=3&viaRelationship=keywords
     * @return string
     * @throws \Exception
     */
    public function getRelatedResourceCreateLink()
    {
        $resource = $this->getRelatedResource();

        if (method_exists($resource, 'getActionUri') == false) {
            throw new \Exception('Related Resource does not implement Crud Resource Trait!');
        }

        $base_uri = $resource->getActionUri('create');

        $fk_name = $this->relation->getForeignKeyName();

        $params = array(
            'resource_type' => class_basename($this->resource),
            $fk_name => data_get($this->resource, 'id')
        );

        if ($this->relation instanceof MorphMany) {
            $params[$this->relation->getMorphType()] = $this->relation->getMorphClass();
        }

        return $base_uri . '?' . http_build_query($params);
    }
}

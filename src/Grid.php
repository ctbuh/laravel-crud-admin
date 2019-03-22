<?php

namespace ctbuh\Admin;

// Grid::registerView('name', $filter_builder, $filter_data);
// implements DataProvider
class Grid extends FormBase
{
    // templates
    // https://api.cakephp.org/3.4/source-class-Cake.View.Helper.FormHelper.html#89

    public $can_delete_from_grid = false;

    protected $templates = array(
        'formStart' => '<form{{attrs}}>'
    );

    public function render_tpl($tpl_name)
    {
        return 'gdf';
    }

    public function columns()
    {
        return $this->fields()->filter(function (Field $field) {
            return $field->shouldShow('index');
        });
    }

    public function rowAttributes()
    {

    }

    public function beforeGridText($text)
    {

    }

    public function afterGridText($text)
    {

    }

    /**
     * Rename to apply and model it after global scopes.
     * @param callable $callback
     * @return Grid
     */
    public function filter($callback)
    {
        if (true || is_callable($callback)) {
            call_user_func($callback, $this->query);
        }

        return $this;
    }

    // TODO: eagerloadin with()->data();
    public function data()
    {
        return $this->query->get();
    }

    // AJAX -> datatables
    public function dataAsJson()
    {
        // TODO
    }

    public function dataAsExcel()
    {
        // TODO
    }

    /**
     * @return boolean addition to sortable: add enablable:
     * If an entity is editable, the list view applies the type: 'toggle' option to all its boolean properties.
     * This data type makes these properties be rendered as "flip switches" that allow to toggle their values very easily:
     */
    public function isSortable()
    {
        return (method_exists($this->newModel(), 'setHighestOrderNumber'));
    }
}

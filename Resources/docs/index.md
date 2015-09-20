## CRUD Components

### Actions

#### IndexAction

* __TableView__
  generates a table from array or paginator; including row action and pagination controls
  when using a paginator, the data can also be sorted (not yet by clickable headers)
* __Filter__
  A form to filter the table
* __Action Bar__
  top bar containing actions; by default a link to "create" will be shown if the create route is available
  
  
Configuration:

```
[
    'template'              => 'CtrlRadBundle:crud:index.html.twig',
    'template_filter_form'  => 'CtrlRadBundle:partial:_form_elements.html.twig',
    'filter_form'           => null,
    'filter_enabled'        => false,
    'table'                 => null,
    'sort'                  => [],
]
```
  
#### EditAction

* __Form__
  rendered with bootstrap template
* __Hooks__
  pre and post persist hooks can be added as a callback in the config
* redirect to index or reload the page after saving

Configuration:

```
[
    'form'                      => null,
    'template'                  => 'CtrlRadBundle:crud:edit.html.twig',
    'template_form_elements'    => 'CtrlRadBundle:partial:_form_elements.html.twig',
    'template_form_buttons'     => 'CtrlRadBundle:partial:_form_buttons.html.twig',
    'save_success_redirect'     => self::SAVE_SUCCESS_REDIRECT,
    'post_persist'              => null,
    'pre_persist'               => null,
    'entity'                    => null,
    'entity_id'                 => null,
]
```

### TableView

```
<?php

use Ctrl\RadBundle\TableView\Table;

$table = new Table();

// set columns "propertyName" => "column header"
$table->setColumns(array(
  'id'        => '#',
  'username'  => 'Username',
  'email'     => 'Email',
  'enabled'   => 'Enabled',
  'locked'    => 'Locked',
));

// add actions for each row
$table->addAction(array(
  'label'         => 'Edit',
  'icon'          => 'edit',
  'class'         => 'primary',
  'route'         => 'user_edit',
  'route_params'  => function ($data) { return ['id' => $data->getId()]; },
));

$table->setData(...); // insert a paginator or array
```

passing this table into twig, there is a function available to render the table:

```
<div class="panel panel-default">
    {{ table(table) }}
</div>
```

will result in the following table:

# | Username  | Email | Enabled | Locked | Actions
--|-----------|-------|---------|--------|--------
1 | admin | admin@site.com | true | false | edit
1 | user | user@site.com | true | false | edit


### Connecting everything in the Controller with the `Ctrl\RadBundle\Crud\Crud` Trait

```
<?php

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ctrl\RadBundle\Crud\Crud;

controller UserController extends Controller
{
    use Crud;

    public function indexAction()
    {
        // create a crud builder for an action
        $builder = $this->getCrudConfigBuilder(IndexAction::class);
        
        // set a form to filter using GET parameters
        $builder->setFilterForm(/* create a form */);
        
        // set a table to display results, filtered with values from the filter form
        $builder->setTable(/* create a table */);
        
        // the builder also provides a helper function to create a new table
        $builder->createTable();
        
        // when you are done configuring, render the page
        return $this->buildCrud($builder)->execute($request);
    }
}
```

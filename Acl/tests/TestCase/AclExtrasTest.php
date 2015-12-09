<?php

namespace Croogo\Acl\Test\TestCase;

use Acl\Lib\AclExtras;
use Cake\Controller\Controller;
use Croogo\TestSuite\CroogoTestCase;

class AclExtrasTest extends CroogoTestCase
{

/**
 * fixtures
 *
 * @var array
 */
    public $fixtures = [
        'plugin.users.aro',
        'plugin.users.aco',
        'plugin.users.aros_aco',
    ];

    protected $_coreControllers = [
    ];

    protected $_extensionsControllers = [
        'ExtensionsLocalesController',
        'ExtensionsPluginsController',
        'ExtensionsThemesController',
    ];

    public function setUp()
    {
        Configure::write('Acl.classname', 'DbAcl');
        Configure::write('Acl.database', 'test');
        $this->AclExtras = new AclExtras();
        $this->AclExtras->startup();
        $this->AclExtras->Aco->deleteAll('1 = 1');
    }

    public function tearDown()
    {
        $this->AclExtras->Aco->deleteAll('1 = 1');
    }

    public function testListControllers()
    {
        $controllers = $this->AclExtras->getControllerList();

        $this->assertFalse(in_array('CakeError', $controllers));

        $result = array_intersect($this->_coreControllers, $controllers);
        $this->assertEquals($this->_coreControllers, $result);

        $controllers = $this->AclExtras->getControllerList('Extensions');
        $result = array_intersect($this->_extensionsControllers, $controllers);
        $this->assertEquals($this->_extensionsControllers, $result);
    }

    public function testListActions()
    {
        $expected = [
            'admin_index', 'admin_movedown', 'admin_moveup',
            'admin_create', 'admin_add', 'admin_edit', 'admin_hierarchy',
            'admin_toggle', 'admin_update_paths', 'admin_delete', 'admin_add_meta',
            'admin_delete_meta', 'admin_process', 'index', 'term', 'promoted',
            'search', 'view',
        ];

        $this->AclExtras->aco_sync(['plugin' => 'Nodes']);

        $node = $this->AclExtras->Aco->node('controllers/Nodes/Nodes');
        $result = $this->AclExtras->Aco->children($node[0]['Aco']['id'], true);
        $result = Hash::extract($result, '{n}.Aco.alias');
        sort($result);
        sort($expected);
        $this->assertEquals($expected, $result);
    }
}

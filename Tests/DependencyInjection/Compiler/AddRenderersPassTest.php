<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddRenderersPass;
use PHPUnit\Framework\TestCase;

class AddRenderersPassTest extends TestCase
{
    public function testProcessWithoutProviderDefinition()
    {
        $containerBuilder = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->getMock();
        $containerBuilder->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(false));
        $containerBuilder->expects($this->never())
            ->method('findTaggedServiceIds');

        $renderersPass = new AddRenderersPass();

        $renderersPass->process($containerBuilder);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testProcessWithEmptyAlias()
    {
        $containerBuilderMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->getMock();
        $containerBuilderMock->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(true));
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.renderer'))
            ->will($this->returnValue(array('id' => array('tag1' => array('alias' => '')))));

        $renderersPass = new AddRenderersPass();
        $renderersPass->process($containerBuilderMock);
    }

    public function testProcessWithAlias()
    {
        $definitionMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();
        $definitionMock->expects($this->once())
            ->method('replaceArgument')
            ->with($this->equalTo(2), $this->equalTo(array('test_alias' => 'id')));

        $containerBuilderMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->getMock();
        $containerBuilderMock->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(true));
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.renderer'))
            ->will($this->returnValue(array('id' => array('tag1' => array('alias' => 'test_alias')))));
        $containerBuilderMock->expects($this->once())
            ->method('getDefinition')
            ->with($this->equalTo('knp_menu.renderer_provider'))
            ->will($this->returnValue($definitionMock));

        $renderersPass = new AddRenderersPass();
        $renderersPass->process($containerBuilderMock);
    }
}

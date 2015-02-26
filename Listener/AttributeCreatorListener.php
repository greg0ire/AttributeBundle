<?php

namespace Padam87\AttributeBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\UnitOfWork;
use Padam87\AttributeBundle\Entity\Attribute;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\DBAL\DBALException;

/**
 * @DI\Service("attribute.attribute_creator")
 * @DI\Tag("doctrine.event_listener", attributes = {"event" = "postLoad"})
 */
class AttributeCreatorListener
{
    private $loader;

    /**
     * @DI\InjectParams({"loader" = @DI\Inject("attribute.attribute_loader")})
     */
    public function __construct($loader)
    {
        $this->loader = $loader;
    }

    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $this->loader->setEntityManager($em);
        $entity = $eventArgs->getEntity();
        $refl = new \ReflectionClass($entity);

        $reader = new AnnotationReader();

        if ($reader->getClassAnnotation($refl, 'Padam87\AttributeBundle\Annotation\Entity') != null) {
            $entity->setAttributeLoader($this->loader);
        }
    }
}

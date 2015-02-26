<?php

namespace Padam87\AttributeBundle\Hydration;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Loads attributes in an entity. Should be called lazily.
 *
 * @DI\Service("attribute.attribute_loader")
 */
class AttributeLoader
{
    /**
     * For some reason, constructor injection gives a circular reference exception.
     * Maybe the entity manager depends on the listeners ?
     */
    public function setEntityManager(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function load($entity)
    {
        try {
            $schema = $em->getRepository('Padam87AttributeBundle:Schema')->findOneBy(array(
                'className' => $refl->getName()
            ));

            if ($schema !== null) {
                foreach ($schema->getDefinitions() as $definition) {
                    $qb = $em->getRepository($refl->getName())->createQueryBuilder('main');

                    $qb->join('main.attributes', 'a', 'WITH', 'a.definition = :definition');
                    $qb->where('main = :main');
                    $qb->setParameter('definition', $definition);
                    $qb->setParameter('main', $entity);

                    $attribute = $qb->getQuery()->getOneOrNullResult();

                    if ($attribute === null) {
                        $attribute = new Attribute();
                        $attribute->setDefinition($definition);

                        $entity->addAttribute($attribute);

                        if ($uow->getEntityState($entity) == UnitOfWork::STATE_MANAGED) {
                            $em->persist($entity);
                            $em->flush($entity);
                        }
                    }
                }
            }
        } catch (DBALException $e) {
            // Discard DBAL exceptions in order for schema:update to work
        }
}
}

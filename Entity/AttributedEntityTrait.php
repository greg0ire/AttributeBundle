<?php

namespace Padam87\AttributeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait AttributedEntityTrait
{
    /**
     * @var \Padam87\AttributeBundle\Entity\Attribute
     *
     * @ORM\ManyToMany(targetEntity="\Padam87\AttributeBundle\Entity\Attribute", fetch="EAGER", cascade={"persist", "remove"})
     */
    private $attributes;

    private $areAttributesLoaded = false;

    /**
     * Add attributes
     *
     * @param \Padam87\AttributeBundle\Entity\Attribute $attributes
     */
    public function addAttribute(\Padam87\AttributeBundle\Entity\Attribute $attributes)
    {
        $this->attributes[] = $attributes;

        return $this;
    }

    /**
     * Remove attributes
     *
     * @param \Padam87\AttributeBundle\Entity\Attribute $attributes
     */
    public function removeAttribute(\Padam87\AttributeBundle\Entity\Attribute $attributes)
    {
        $this->attributes->removeElement($attributes);
    }

    /**
     * Get attributes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributes()
    {
        $this->loadAttributes();
        return $this->attributes;
    }

    public function setAttributeLoader($loader)
    {
        $this->loader = $loader;
    }

    public function loadAttributes()
    {
        if (!$this->areAttributesLoaded) {
            $this->loader->load($this);
        }

        $this->areAttributesLoaded = true;
    }
}

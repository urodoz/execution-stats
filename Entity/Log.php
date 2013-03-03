<?php

namespace Urodoz\TimeExecutionStatsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="texst_log")
 * @ORM\HasLifecycleCallbacks
 *
 * @author Albert Lacarta <urodoz@gmail.com>
 */
class Log
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $executions;

    /**
     * @ORM\Column(type="decimal", scale=4)
     */
    protected $time;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $tag;

    /**
     * @ORM\Column(type="string")
     */
    protected $version;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $creationDateTime;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->creationDateTime = new DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set executions
     *
     * @param  integer $executions
     * @return Log
     */
    public function setExecutions($executions)
    {
        $this->executions = $executions;

        return $this;
    }

    /**
     * Get executions
     *
     * @return integer
     */
    public function getExecutions()
    {
        return $this->executions;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Log
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set tag
     *
     * @param  string $tag
     * @return Log
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set version
     *
     * @param  string $version
     * @return Log
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Get creationDateTime
     *
     * @return \DateTime
     */
    public function getCreationDateTime()
    {
        return $this->creationDateTime;
    }

    /**
     * Set time
     *
     * @param  float $name
     * @return Log
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }
}

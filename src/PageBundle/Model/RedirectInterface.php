<?php
namespace PageBundle\Model;

/**
 * Interface RedirectInterface
 */
interface RedirectInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * Set isActive.
     *
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive);

    /**
     * Get isActive.
     *
     * @return bool $isActive
     */
    public function getIsActive();

    /**
     * Set fromPath.
     *
     * @param string $fromPath
     */
    public function setFromPath($fromPath);

    /**
     * Get fromPath.
     *
     * @return string $link
     */
    public function getFromPath();

    /**
     * Set toPath.
     *
     * @param string $toPath
     */
    public function setToPath($toPath);

    /**
     * Get toPath.
     *
     * @return string $path
     */
    public function getToPath();
}

<?php

namespace nathanwooten\View;

interface ViewInterface
{

  /**
   * @param mixed $id
   * Sets the Id,
   * so the internal id
   * will not be used anymore
   */

  public function setId( $id );

  /**
   * Get the id, either,
   * internal id ( basename( FCQN ),
   * or manually set id
   */

  public function getId();

  /**
   * Checks that an id
   * has been manually set
   */

  public function hasId();

}
<?php

namespace Smashballoon\Customizer\Tabs;

/** @internal */
abstract class Tab
{
    protected $id;
    protected $heading;
    public function get_sections()
    {
        return [];
    }
    public function get_id()
    {
        return $this->id;
    }
    public function get_heading()
    {
        return $this->heading;
    }
}

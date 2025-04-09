<?php

namespace Smashballoon\Customizer\Tabs;

use Smashballoon\Stubs\Traits\Singleton;
/** @internal */
class Manager
{
    use Singleton;
    /**
     * @var Tab[]
     */
    private $tabs = [];
    /**
     * @param Tab $tab
     *
     * @return void
     */
    public function register_tab(\Smashballoon\Customizer\Tabs\Tab $tab)
    {
        $this->tabs[] = $tab;
    }
    /**
     * @return array
     */
    public function get_tabs()
    {
        $tabs = [];
        foreach ($this->tabs as $tab) {
            $section_id = $tab->get_id();
            $tabs[$section_id] = ['id' => $section_id, 'heading' => $tab->get_heading(), 'sections' => $tab->get_sections()];
        }
        return $tabs;
    }
}

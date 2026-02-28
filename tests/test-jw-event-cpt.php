<?php
/**
 * Class JW_Event_CPT_Test
 *
 * @package Jw_Event_Manager
 */

class JW_Event_CPT_Test extends WP_UnitTestCase {

    /**
     * Test if the custom post type 'jw_event' is registered successfully.
     */
    public function test_event_cpt_is_registered() {
        $this->assertTrue( post_type_exists( 'jw_event' ) );
    }

    /**
     * Test if the custom taxonomy 'jw_event_type' is registered successfully.
     */
    public function test_event_taxonomy_is_registered() {
        $this->assertTrue( taxonomy_exists( 'jw_event_type' ) );
    }
}
@core @core_my @core_block @_drag_drop
Feature: Blocks can be placed in the content area
  In order to custom my page
  As a user
  I need to drag blocks into the content area

  @javascript
  Scenario Outline: Drag a block on the /my page into the content area
    Given a theme based in "<basetheme>" is selected
    And I log in as "admin"
    And I follow "My home"
    And I press "Customise this page"
    When I drag "Online users" "block" and I drop it in "<mainregion>" "region"
    Then "Online users" "block" should exist in the "<mainregion>" "region"
    And "Online users" "block" should not exist in the "<postregion>" "region"

    Examples:
      | basetheme     | mainregion           | postregion             |
      | base          | region-main          | region-post            |
      | bootstrapbase | block-region-content | block-region-side-post |

  @javascript
  Scenario Outline: Drag all blocks on the /my page into the content area and drag one block back into the right column
    Given a theme based in "<basetheme>" is selected
    And I log in as "admin"
    And I follow "My home"
    And I press "Customise this page"
    When I drag "Online users" "block" and I drop it in "<mainregion>" "region"
    And I drag "Navigation" "block" and I drop it in "<postregion>" "region"
    And I drag "My private files" "block" and I drop it in "<mainregion>" "region"
    And I drag "Online users" "block" and I drop it in "<postregion>" "region"
    Then "Online users" "block" should exist in the "<postregion>" "region"
    And "Navigation" "block" should exist in the "<postregion>" "region"
    And "My private files" "block" should not exist in the "<postregion>" "region"
    And "My private files" "block" should exist in the "<mainregion>" "region"

    Examples:
      | basetheme     | mainregion           | postregion             |
      | base          | region-main          | region-post            |
      | bootstrapbase | block-region-content | block-region-side-post |

  @javascript
  Scenario Outline: Place a block on the /my page into the content area via settings
    Given a theme based in "<basetheme>" is selected
    And I log in as "admin"
    And I follow "My home"
    And I press "Customise this page"
    When I open the "Online users" blocks action menu
    And I follow "Configure Online users block"
    And I set the field "bui_region" to "content"
    And I press "Save changes"
    Then "Online users" "block" should exist in the "<mainregion>" "region"

    # Removing 'base' theme here as it is not working even with MDL-41551 patch applied.
    Examples:
      | basetheme     | mainregion           |
      | bootstrapbase | block-region-content |

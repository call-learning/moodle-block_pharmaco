@block @block_pharmaco @javascript
Feature: The block pharmaco allows to show relevant courses to the user or prompt for them to
  pass the first test.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | student1 | Student   | 1        | student1@example.com | S1       |
      | student2 | Student   | 2        | student2@example.com | S2       |
    And the following "role assigns" exist:
      | user     | role         | contextlevel | reference |
      | student1 | PHARMACO-EXT | System       |           |
    And the following "tags" exist:
      | name         | isstandard |
      | delivrance   | 1          |
      | prescription | 1          |
      | stock        | 1          |
      | observance   | 1          |
    And the following "courses" exist:
      | fullname                | shortname | tags                                   |
      | Quotient Pharmaceutique | CQ        | external_course,quotient_pharmacetique |
      | Course 2                | C2        | delivrance                             |
      | Course 3                | C3        | observance                             |
      | Course 4                | C4        | stock                                  |
      | Course 5                | C5        | prescription                           |
    And I set the entry course to "CQ"
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | CQ        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name             | template    | questiontext | tags       |
      | Test questions   | multichoice | Multi-choice-001 | one_of_four | Question One | delivrance |
      | Test questions   | multichoice | Multi-choice-002 | one_of_four | Question Two | stock      |
    And the following "activities" exist:
      | activity | name   | intro         | course | idnumber | preferredbehaviour | canredoquestions | completion | completionusegrade |
      | quiz     | Quiz 1 | Quiz Pharmaco | CQ     | quiz1    | immediatefeedback  | 1                | 2          | 1                  |
    And quiz "Quiz 1" contains the following questions:
      | question         | page |
      | Multi-choice-001 | 1    |
      | Multi-choice-002 | 2    |
    # This should enrol the users on the external course
    And I trigger cron

  Scenario: As a user who been set to the "External Role", the block has a message and a button
    Given I log in as "student1"
    Then I should see "The course 'Quotient Pharmaceutique' needs to be completed" in the "Pharmacovigilance" "block"
    Then I should see "Start" in the "Pharmacovigilance" "block"
    Then I log out

  Scenario: As a user who has not been set to the "External Role", the block is empty
    Given I log in as "student2"
    Then "Pharmacovigilance" "block" should not exist
    Then I log out

  @run_only
  Scenario: As a user who been set to the "External Role", I should be able to start the quiz
    # Enable completion in the course first
    Given I log in as "admin"
    And I am on "Quotient Pharmaceutique" course homepage with editing mode on
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    And I log out
    # Then do the quiz
    Given I log in as "student1"
    Then I should see "Start" in the "Pharmacovigilance" "block"
    Then I click on "Start" "link" in the "Pharmacovigilance" "block"
    Then I should see "Quotient Pharmaceutique"
    And I click on "Quiz 1" "link"
    And I click on "Attempt quiz now" "button"
    And I should see "Question One"
    And I click on "One" "radio"
    And I click on "Next page" "button"
    And I click on "Two" "radio"
    And I click on "Finish attempt ..." "button"
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    And I log out
    And I trigger cron
    And I log in as "student1"
    Then I pause scenario execution
    Then I log out
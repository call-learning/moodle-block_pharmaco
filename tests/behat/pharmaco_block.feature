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
      | fullname               | shortname | tags                                   |
      | Quotient Pharmacetique | CQ        | external_course,quotient_pharmacetique |
      | Course 2               | C2        | delivrance                             |
      | Course 3               | C3        | observance                             |
      | Course 4               | C4        | stock                                  |
      | Course 5               | C5        | prescription                           |
    And I set the entry course to "CQ"
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | CQ     | student |
      | student2 | CQ     | student |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | CQ        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name             | template    | questiontext | tags       |
      | Test questions   | multichoice | Multi-choice-001 | one_of_four | Question One | delivrance |
      | Test questions   | multichoice | Multi-choice-002 | one_of_four | Question Two | stock      |
    And the following "activities" exist:
      | activity | name   | intro         | course | idnumber | preferredbehaviour | canredoquestions |
      | quiz     | Quiz 1 | Quiz Pharmaco | CQ     | quiz1    | immediatefeedback  | 1                |
    And quiz "Quiz 1" contains the following questions:
      | question         | page |
      | Multi-choice-001 | 1    |
      | Multi-choice-002 | 1    |

  Scenario: As a user who been set to the "External Role", the block has a message and a button
    Given I log in as "student1"
    Then I should see "The course 'Quotient Pharmacetique' needs to be completed in order to be registered to the full program." in the "Pharmacovigilance" "block"
    Then I should see "Start" in the "Pharmacovigilance" "block"

  Scenario: As a user who has not been set to the "External Role", the block is empty
    Given I log in as "student2"
    Then "Pharmacovigilance" "block" should not exist

  Scenario: As a user who been set to the "External Role", I should be able to start the quiz
    Given I log in as "student1"
    Then I should see "Start" in the "Pharmacovigilance" "block"
    Then I click on "Start" "button" in the "Pharmacovigilance" "block"
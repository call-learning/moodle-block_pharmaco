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
      | name            | isstandard |
      | delivrance      | 1          |
      | prescription    | 1          |
      | stock           | 1          |
      | observance      | 1          |
    And the following "courses" exist:
      | fullname                | shortname | tags                                   | enablecompletion |
      | Quotient Pharmaceutique | CQ        | external_course,quotient_pharmacetique | 1                |
      | Course 2                | C2        | external_course,delivrance             | 1                |
      | Course 3                | C3        | external_course,observance             | 1                |
      | Course 4                | C4        | external_course,stock                  | 1                |
      | Course 5                | C5        | external_course, prescription          | 1                |
    And I set the entry course to "CQ"
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | CQ        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name             | template    | questiontext |
      | Test questions   | multichoice | Multi-choice-001 | one_of_four | Question One |
      | Test questions   | multichoice | Multi-choice-002 | one_of_four | Question Two |
    And the following "core_question > Tags" exist:
      | question         | tag        |
      | Multi-choice-001 | delivrance |
      | Multi-choice-002 | stock      |
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

  Scenario: As a user who been set to the "External Role", I should be able to start the quiz
    # Make sure that the quiz is set to complete the course
    Given I log in as "admin"
    And I am on "Quotient Pharmaceutique" course homepage
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Quiz - Quiz 1 | 1 |
    And I click on "Save changes" "button"
    And I log out
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
    # Running completion task just after clicking sometimes fail, as record
    # should be created before the task runs.
    And I run the scheduled task "core\task\completion_regular_task"
    And I wait "1" seconds
    And I run the scheduled task "core\task\completion_regular_task"
    # This is due to a bug in the completion (completion_regular_task)
    # If the reaggregation has been done in the same cron, then the time is
    # the same and reaggregate == time(), so the course is not marked complete
    Then I log out
    And I trigger cron
    # We need to logout in between
    Given I log in as "student1"
    Then I should see "Pharmacovigilance"
    And I should see "Course 2"
    And I should see "Course 3"
    And I should see "Course 4"
    And I should see "Course 5"
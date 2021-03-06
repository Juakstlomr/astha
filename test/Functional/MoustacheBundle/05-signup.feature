@Authentication
@Signup
Feature: Ability to signup

    Scenario: As a hak3r, I cannot send directly a signup request without filling the form.
        When I send a "POST" request to "/signup/01234567abcdef" with parameters:
            | key                            | value                       |
            | signup_plainPassword_first     | passWORDisLongEn0ugh        |
            | signup_plainPassword_second    | passWORDisLongEn0ugh        |
        Then the response status code should be 302
        When I follow the redirection
        Then I should be on "/signup/form/01234567abcdef"

    Scenario: As a guest, I see the signup form
        Given I am on "/signup/form/01234567abcdef"
        Then the response status code should be 200
        Then I should see a "input#signup_plainPassword_first" element
        And I should see a "input#signup_plainPassword_second" element
        And I should see a "input#username[value=guest]" element
        And I should see "You will need this username to log in"
        And I should see a "input#signup__token" element
        And I should see a "button" element

    Scenario: As a guest, I am warned that I did a typo on the second password
        Given I am on "/signup/form/01234567abcdef"
        When I fill in "signup_plainPassword_first" with "mySuperPASS"
        When I fill in "signup_plainPassword_second" with "ymSuperPASS"
        When I press "Signup"
        Then the response status code should be 200
        And I should see "The password does not match" in the "#content .alert" element
        And I should be on "/signup/form/01234567abcdef"

    Scenario: As a guest, I am warned that my password is too short
        When I fill in "signup_plainPassword_first" with "short"
        When I fill in "signup_plainPassword_second" with "short"
        When I press "Signup"
        Then the response status code should be 200
        And I should see "Please pick a password of at least 10 characters" in the "#content .alert" element
        And I should be on "/signup/form/01234567abcdef"

    @saveCookies
    Scenario: As a guest, I signup
        Given I don't follow redirection
        When I fill in "signup_plainPassword_first" with "passWORDisLongEn0ugh"
        When I fill in "signup_plainPassword_second" with "passWORDisLongEn0ugh"
        When I press "Signup"
        Then the response status code should be 302

    Scenario: As a new Moustachor, I am now logged in to my new account
        And I should be on "/signup/01234567abcdef"
        When I follow the redirection
        And I should be on "/"
        Then the response status code should be 200
        When I reload the page
        And I should be on "/"
        Then the response status code should be 200

    Scenario: As moustachor, I cannot see the signup page again
        When I go to "/signup/form/01234567abcdef"
        Then the response status code should be 403

    Scenario: As moustachor, I can logout and login again
        When I am authenticated as "guest" with password "passWORDisLongEn0ugh"
        Then I should be on "/"

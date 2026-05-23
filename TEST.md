# ArtPortal Test Documentation

## 1. Testing Objective

The objective of testing is to confirm that the ArtPortal web application correctly supports the main user workflows, handles invalid input, protects restricted areas, and provides stable behavior for the core modules: registration, authentication, personal dashboards, artist profiles, painting publication, favorites, purchase requests, and the administrative panel.

Testing is performed at three levels:

- unit testing: verification of individual services, models, helper classes, and isolated business logic;
- integration testing: verification of interactions between controllers, services, routes, sessions, and the database;
- end-to-end testing: verification of user scenarios through the browser interface.

Because the project is an educational web application, the test plan is grouped by functional areas instead of listing every test method separately.

## 2. Test Object

The object under test is the ArtPortal web application, including:

- the public part of the website;
- user registration and authentication;
- the user dashboard;
- the artist dashboard;
- the administrative panel;
- operations with paintings, artists, categories, collections, and exhibitions;
- favorites and purchase requests;
- data validation and error handling.

## 3. Types of Testing

| Testing Type | Purpose | Tool |
|---|---|---|
| Unit testing | Checks individual classes and functions without launching a browser | PHPUnit |
| Integration testing | Checks cooperation between application modules and the database | PHPUnit |
| E2E testing | Checks user scenarios through the web interface | Playwright |

## 4. Unit Testing

| Test | Input Data | Expected Result | Actual Result |
|---|---|---|---|
| User registration | Empty fields, invalid email, weak password, password mismatch, duplicate email or username | Invalid data is rejected, and a valid user is created | Passed |
| User authentication | Invalid email, invalid password, non-existing user, blocked account, active account | Login is allowed only for an active user with valid credentials | Passed |
| User account update | New username or email, duplicate data, current and new password | Account data is updated only after successful validation | Passed |
| Artist profile creation and update | Profile data, repeated profile creation, required fields, image | The profile is saved with valid data, and validation errors are displayed | Passed |
| Painting management | Painting data, image, invalid fields, ID of a painting owned by another artist | Only valid actions by the painting owner are allowed | Passed |
| Favorites management | User ID and painting ID | The relation between the user and the painting is created and removed correctly | Passed |
| Purchase requests | User ID, painting ID, missing or invalid values | A purchase request is created only when the data is valid | Passed |
| Category management | Category name, empty name, duplicate name | CRUD operations are performed with validation | Passed |
| Collection management | Collection name, collection type, empty or duplicate data | Collections are saved only with valid data | Passed |
| Exhibition management | Name, dates, collection ID, invalid dates, duplicate name | Exhibitions are created only with valid collection links and valid dates | Passed |
| Pagination | Missing page number, page number less than 1, page number greater than the total number of pages | The page number and offset are calculated correctly | Passed |
| CSRF protection | Valid, missing, and invalid CSRF token | Requests with an invalid token are rejected | Passed |
| Price calculation | Price, desired income, commission, tax, negative values | Final amounts are calculated correctly | Passed |
| Email service | User or artist email, invalid addresses, email template data | Emails are built correctly, and invalid addresses are rejected | Passed |
| Tags and Vision AI | Recognized labels, web entities, duplicate values | The tag list is generated without duplicates or unnecessary values | Passed |

## 5. Integration Testing

| Test | Input Data | Expected Result | Actual Result |
|---|---|---|---|
| Test database connection | Test database configuration, SQL read and write queries | The application uses the test database, and queries are executed correctly | Passed |
| Registration and login | Registration data, email and password of active or blocked users | The user is saved in the database, and authentication respects the account status | Passed |
| Authentication page rendering | Requests to login, registration, and password recovery pages | Controllers return the required pages | Passed |
| Public painting catalog | Search query and published painting data from an approved artist | The painting is displayed in the catalog and is available to the user | Passed |
| Public artist list | Search query and approved artist data | Only publicly available artist profiles are displayed | Passed |
| Artist profile | New profile data, update data, user with an existing profile | The profile is saved in the database and keeps the correct moderation status | Passed |
| Painting relations | User ID, painting ID, purchase request, favorites | User-painting-request relations are created correctly | Passed |
| Content CRUD | Category, collection, and exhibition data for creation, update, and deletion | Data is changed in the database and remains correctly related | Passed |
| Administrative panel | Authorized administrator session | The panel loads, and dashboard indicators are built from database data | Passed |
| Users in admin panel | Search query and test users | The administrator sees the user list and search results | Passed |
| Artist moderation | Artist profiles with pending status | The administrator sees profiles waiting for moderation | Passed |
| Categories, collections, and exhibitions in admin panel | Test reference records | Administrative lists load with test data | Passed |
| Dashboard statistics | User and artist data: favorites, requests, portfolio | Dashboards receive correct aggregated data | Passed |
| 404 error handling | Request for a non-existing page | A user-friendly error page is displayed | Passed |
| Cost calculation | Price, desired income, commission, and tax parameters | Integration-level calculation returns expected values | Passed |

## 6. E2E Testing

| Test | Input Data | Expected Result | Actual Result |
|---|---|---|---|
| Open the home page as a guest | Navigation to the website home page | The home page loads and displays the site heading | Passed |
| View the painting catalog as a guest | Navigation to the all paintings section | The catalog opens and displays a list of paintings | Passed |
| View the artist list as a guest | Navigation to the artists section | The artist page opens and contains a list of profiles | Passed |
| Open the login page as a guest | Navigation to the authentication page | The login form is displayed | Passed |
| View a painting details page as a guest | Click a painting in the catalog | The painting page opens with information about the artwork and artist | Passed |
| Try to send a purchase request without login | Click the purchase request button without authentication | The system redirects the user to the login page | Passed |
| Register a new user | Completed registration form | A successful registration message is displayed after form submission | Passed |
| User login and logout | Valid email and password, logout action | The user enters the dashboard and can log out of the account | Passed |
| View the user dashboard | Authorized user session | Profile, favorites, and user requests are available | Passed |
| View user favorites | Navigation to `My Favorites` | Saved paintings are displayed | Passed |
| Send a purchase request as a user | Authorized user and selected painting | The request is submitted and appears in `My Requests` | Passed |
| View paintings as an artist | Authorized artist session, navigation to `My Paintings` | The artist portfolio is displayed | Passed |
| View incoming purchase requests as an artist | Authorized artist session, navigation to `Purchase Requests` | Buyer requests are displayed | Passed |
| Administrator login to the admin panel | Valid administrator credentials | The administrator panel opens after login | Passed |
| View users as an administrator | Navigation to `Users` | The user list is displayed | Passed |
| View categories as an administrator | Navigation to `Categories` | The category list is displayed | Passed |
| View artist moderation requests | Navigation to artist moderation section | The administrator sees artist profiles awaiting moderation | Passed |
| Check test environment availability | Navigation to the base address of the test site | The test site is available at the base address | Passed |

## 7. Success Criteria

Testing is considered successful when:

- all unit tests finish without errors;
- all integration tests pass against the test database;
- the main E2E scenarios run in the browser without critical errors;
- user forms correctly process valid and invalid data;
- protected sections are unavailable to users without the required permissions;
- data is correctly stored in the database after create, update, and delete operations.

## 8. Conclusion

The completed testing covers the main portal functions at different levels. Unit tests verify internal business logic, integration tests confirm correct cooperation between modules and the database, and E2E tests demonstrate key user workflows through the interface. This combination helps detect defects both in isolated functions and in complete user processes.

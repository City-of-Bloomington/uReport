-- departments
INSERT INTO departments (id, name, defaultPerson_id) VALUES
(1, 'Information Services', NULL),
(2, 'Test Department', NULL);

-- people
INSERT INTO people (
    id, firstname, middlename, lastname, organization,
    address, city, state, zip, department_id,
    username, password, authenticationMethod, role
) VALUES
(
    1, 'Test', NULL, 'Person', NULL,
    NULL, NULL, NULL, NULL, 1,
    NULL, NULL, NULL, NULL
);

-- categories
INSERT INTO categories (
    id, name, description, department_id, categoryGroup_id,
    displayPermissionLevel, postingPermissionLevel,
    customFields, lastModified, slaDays
) VALUES
(1, 'Test Category', NULL, 1, 1, 'staff', 'staff', NULL, '2013-09-11 03:45:27', 1),
(2, 'Another Category', NULL, 1, 1, 'staff', 'staff', NULL, '2014-07-10 16:43:01', 1),
(3, 'Public Category', NULL, 1, 1, 'anonymous', 'anonymous', NULL, '2014-07-10 16:43:01', 1);

-- department_categories
INSERT INTO department_categories (department_id, category_id) VALUES
(2, 1),
(2, 2);

-- tickets
INSERT INTO tickets (id, category_id, description, status)
VALUES (1, 3, 'Test ticket for Solr indexing', 'open');
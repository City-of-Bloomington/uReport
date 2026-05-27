-- departments
INSERT INTO departments (id, name, defaultPerson_id) VALUES
(1, 'Information Services', NULL),
(2, 'Test Department', NULL);

-- people
INSERT INTO people (
    id, firstname, middlename, lastname, organization,
    address, city, state, zip, department_id,
    username, role
) VALUES
(
    1, 'Test', NULL, 'Person', NULL,
    NULL, NULL, NULL, NULL, 1,
    NULL, NULL
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
VALUES 
(1, 3, 'Test ticket for Solr indexing', 'open'),
(2, 3, 'Test ticket with media attachment', 'open');

-- media. Note that filenames must be 13-digit hexadecimal strings without file extensions.
INSERT INTO media (id, ticket_id, person_id, filename, internalFilename, mime_type, uploaded)
VALUES
(1, 2, 1, 'abc123def4567', 'abc123def4567', 'image/png', '2026-04-15');

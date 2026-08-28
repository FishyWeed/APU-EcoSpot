-- Insert the 3 roles for the application
INSERT INTO role (name) VALUES ('Student'), ('Eco-Ambassador'), ('Facilities');

-- Insert test users (impact_pts starts at 0 — recalculated from tickets at the bottom)
INSERT INTO user (user_type, full_name, email, student_id, password, school_department, impact_pts) VALUES
('Student', 'Ahmad Faris Hakim',       'ahmad@mail.apu.edu.my',   'TP060001', 'password123', 'School of Computing',                    0),
('Student', 'Nur Aisyah Binti Zainal', 'aisyah@mail.apu.edu.my',  'TP060002', 'password123', 'School of Computing',                    0),
('Student', 'Lim Wei Jian',            'weijian@mail.apu.edu.my', 'TP060003', 'password123', 'School of Computing',                    0),
('Student', 'Siti Rahmah Abdullah',    'siti@mail.apu.edu.my',    'TP060004', 'password123', 'School of Engineering & Technology',     0),
('Student', 'Rajan Kumar',             'rajan@mail.apu.edu.my',   'TP060005', 'password123', 'School of Engineering & Technology',     0),
('Student', 'Priya Nair',              'priya@mail.apu.edu.my',   'TP060006', 'password123', 'School of Business',                     0),
('Student', 'Marcus Tan',              'marcus@mail.apu.edu.my',  'TP060007', 'password123', 'School of Business',                     0),
('Student', 'Amirah Sofea',            'amirah@mail.apu.edu.my',  'TP060008', 'password123', 'School of Media & Communication',        0),
('Student', 'David Okonkwo',           'david@mail.apu.edu.my',   'TP060009', 'password123', 'School of Computing',                    0),
('Student', 'Mei Ling Tan',            'meiling@mail.apu.edu.my', 'TP060010', 'password123', 'School of Engineering & Technology',     0),
('Eco-Ambassador', 'Eco Ambassador',      'ecoambassador@apu.edu.my', 'ECO-AMB-01', 'abc12345', 'Eco-Ambassador',     0),
('Facilities','System Administrator',    'admin@apu.edu.my',        'APU-ADM-01','admin123',   'Administration & IT Services',           0);

-- Assign roles via user_role table
INSERT INTO user_role (user_id, role_id)
SELECT u.user_id, r.role_id
FROM user u
JOIN role r ON r.name = 'Student'
WHERE u.user_type = 'Student';

-- Assign Admin role
INSERT INTO user_role (user_id, role_id)
SELECT u.user_id, r.role_id
FROM user u
JOIN role r ON r.name = 'Admin'
WHERE u.user_type = 'Admin';

-- Insert test tickets
INSERT INTO ticket (user_id, location_type, block_name, floor_number, room_number, issue_type, description, status) VALUES
(1, 'Classroom',   'Block A',  '02', 'A-02-04',  'Lighting',   'Lights left on all night',              'Resolved'),
(1, 'Lab',         'Block A',  '03', 'A-03-01',  'Aircon',     'AC running in empty lab',               'Verified'),
(2, 'Classroom',   'Block A',  '03', 'A-03-02',  'Projector',  'Projector left on',                     'Resolved'),
(2, 'Common Area', 'Block A',  '01', 'A-01-00',  'Lighting',   'Corridor lights on in daylight',        'Resolved'),
(3, 'Classroom',   'Block A',  '04', 'A-04-01',  'Aircon',     'AC set to 16 degrees',                  'Resolved'),
(9, 'Lab',         'Tech Hub', '01', 'TH-01-03', 'Lighting',   'Lab lights on over weekend',            'Verified'),
(9, 'Classroom',   'Block A',  '02', 'A-02-06',  'Aircon',     'AC on in empty room',                   'Pending'),
(4, 'Lab',         'Block B',  '01', 'B-01-02',  'Lighting',   'Fluorescent lights on all night',       'Resolved'),
(4, 'Classroom',   'Block B',  '02', 'B-02-04',  'Aircon',     'AC unit not turning off',               'Pending'),
(5, 'Office',      'Admin',    '01', 'ADM-01',   'Lighting',   'Office lights left on over weekend',    'Verified'),
(10,'Lab',         'Block B',  '03', 'B-03-01',  'Projector',  'Projector left on after lecture',       'Resolved'),
(6, 'Classroom',   'Block A',  '01', 'A-01-03',  'Lighting',   'Lights on in empty classroom',          'Resolved'),
(7, 'Common Area', 'Library',  '01', 'LIB-01',   'Aircon',     'AC running with windows open',          'Pending'),
(8, 'Classroom',   'Block B',  '04', 'B-04-02',  'Other',      'Multiple devices left on standby',      'Resolved'),
(9, 'Lab',         'Block A',  '01', 'A-01-02',  'Lighting',   'Lights on during closed hours',         'Resolved');

-- Recalculate impact_pts from actual ticket count so points always match
UPDATE user u SET u.impact_pts = (SELECT COUNT(*) * 75 FROM ticket t WHERE t.user_id = u.user_id);


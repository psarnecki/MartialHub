DROP TABLE IF EXISTS fights CASCADE;
DROP TABLE IF EXISTS events CASCADE;
DROP TABLE IF EXISTS user_details CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS clubs CASCADE;

-- Clubs table (1:N user_details)
CREATE TABLE clubs (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL
);

-- Users table
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user', -- 'user', 'organizer', 'admin'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User Details table (1:1 users)
CREATE TABLE user_details (
    id SERIAL PRIMARY KEY,
    user_id INTEGER UNIQUE NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    nickname VARCHAR(100),
    club_id INTEGER REFERENCES clubs(id) ON DELETE SET NULL,
    bio TEXT,
    image_url VARCHAR(255) DEFAULT 'public/img/default-avatar.png'
);

-- Events table (tournaments, seminars)
CREATE TABLE events (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    discipline VARCHAR(50) DEFAULT 'N/A',
    description TEXT,
    organizer_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    date TIMESTAMP NOT NULL,
    location VARCHAR(255) NOT NULL,
    country VARCHAR(100) NOT NULL,
    registration_fee INTEGER DEFAULT 0,
    registration_deadline TIMESTAMP,
    image_url TEXT,
    capacity INTEGER DEFAULT 100,
    is_featured BOOLEAN DEFAULT FALSE
);

-- Event registrations table (users N:N events)
CREATE TABLE event_registrations (
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    event_id INTEGER REFERENCES events(id) ON DELETE CASCADE,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, event_id)
);

-- Fights table (users N:N events)
CREATE TABLE fights (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    opponent_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    event_id INTEGER NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    result VARCHAR(10) NOT NULL, -- 'WIN', 'LOSS', 'DRAW'
    method VARCHAR(100) NOT NULL, -- 'KO/TKO', 'Submission', 'Unanimous Decision', 'Split Decision', 'Majority Decision', 'Points', 'Doctor Stoppage', 'DQ'
    fight_date DATE NOT NULL
);

-- Integrity constraints
ALTER TABLE fights ADD CONSTRAINT check_different_fighters CHECK (user_id <> opponent_id);
ALTER TABLE fights ADD CONSTRAINT check_result CHECK (result IN ('WIN', 'LOSS', 'DRAW'));
ALTER TABLE fights ADD CONSTRAINT check_method CHECK (method IN 
    (
        'KO/TKO',
        'Submission',
        'Unanimous Decision',
        'Split Decision',
        'Majority Decision',
        'Unanimous Draw',
        'Split Draw',
        'Majority Draw',
        'Points',
        'Doctor Stoppage',
        'DQ'
    )
);

-- Initial clubs data
INSERT INTO clubs (name, city) VALUES
    ('Boom Boxing Studio', 'Cracow'), 
    ('Aligatores', 'Warsaw'),
    ('Grappling Kraków', 'Cracow'),
    ('B-ZONE', 'Kędzierzyn-Koźle'),
    ('Kame House', 'Kędzierzyn-Koźle'),
    ('Progress Gym', 'Wrocław'),
    ('Top Team Częstochowa', 'Częstochowa'),
    ('UKS Olimpijczyk Kędzierzyn-Koźle', 'Kędzierzyn-Koźle');

-- Initial users data
INSERT INTO users (email, password, role) VALUES 
    ('admin@martialhub.com', '$2y$10$m9iYSYk9tvwcGmGiwGLMzeLiVAs4DmnBTK85AttfuxsqW3nQNt6M6', 'admin'),
    ('maciej.kawulski@martialhub.com', '$2y$10$VkD3RTJlEaVVVExuM/Lwl.pz2zsEFJvhlFxyiEZn8A0U3dmJp4tva', 'organizer'),
    ('jan.kowalski@martialhub.com', '$2y$10$jbga9l5QuJHfB.6drfTXJOY8zwvjnK17eYQtig.f5axdnuNGtz2Qq', 'user'),
    ('adam.nowak@martialhub.com', '$2y$10$jbga9l5QuJHfB.6drfTXJOY8zwvjnK17eYQtig.f5axdnuNGtz2Qq', 'user'),
    ('piotr.lewandowski@martialhub.com', '$2y$10$jbga9l5QuJHfB.6drfTXJOY8zwvjnK17eYQtig.f5axdnuNGtz2Qq', 'user'),
    ('mmapolska@martialhub.com', '$2y$10$VkD3RTJlEaVVVExuM/Lwl.pz2zsEFJvhlFxyiEZn8A0U3dmJp4tva', 'organizer'),
    ('andrzej.wisniewski@martialhub.com', '$2y$10$jbga9l5QuJHfB.6drfTXJOY8zwvjnK17eYQtig.f5axdnuNGtz2Qq', 'user');

-- Initial user details
INSERT INTO user_details (user_id, firstname, lastname, phone, bio) VALUES
    (1, 'Admin', 'MartialHub', '+48 000 000 000', 'System administrator.'),
    (2, 'Maciej', 'Kawulski', '+48 500 600 700', 'Responsible for managing events and fights.'),
    (6, 'Martin', 'Lewandowski', '+48 888 777 666', 'MMA enthusiast and KSW, mmapolska owner.');

INSERT INTO user_details (user_id, firstname, lastname, club_id, bio) VALUES 
    (3, 'Jan', 'Kowalski', 4, 'K1 professional fighter.'),
    (4, 'Adam', 'Nowak', 2, 'BJJ Blue Belt'),
    (5, 'Piotr', 'Lewandowski', 1, 'Boxing enthusiast'),
    (7, 'Andrzej', 'Wiśniewski', 5, 'BJJ black belt');

-- Initial events data
INSERT INTO events (title, discipline, description, organizer_id, date, location, country, registration_fee, registration_deadline, image_url, capacity, is_featured) VALUES 
    (
        'Polish MMA Championship 2026',
        'MMA',
        'National-level MMA championship featuring top amateur fighters.',
        6,
        '2026-10-18 10:00:00', -- UPCOMING
        'Warsaw',
        'Poland',
        250,
        '2026-09-30 23:59:59',
        'public/img/mma_championship.jpg',
        500,
        TRUE
    ),
    (
        'Regional Judo Cup',
        'Judo',
        'Regional judo tournament for junior and senior competitors.',
        2,
        '2025-10-15 09:00:00', -- FINISHED
        'Cracow',
        'Poland',
        120,
        '2025-10-01 23:59:59',
        'public/img/judo_cup.jpg',
        300,
        FALSE
    ),
    (
        'Copa Silesia 8',
        'BJJ',
        'Brazilian Jiu-Jitsu open tournament with gi and no-gi divisions.',
        6,
        '2025-11-09 08:30:00', -- FINISHED
        'Warsaw',
        'Poland',
        180,
        '2025-10-25 23:59:59',
        'public/img/bjj_open.jpg',
        400,
        FALSE
    ),
    (
        'High Kick 10',
        'Kickboxing',
        'Professional and amateur kickboxing bouts under K-1 rules.',
        2,
        '2026-03-21 18:00:00', -- UPCOMING
        'Gliwice',
        'Poland',
        200,
        '2026-02-28 23:59:59',
        'public/img/kickboxing.jpg',
        250,
        FALSE
    ),
    (
        'ALMMA 219',
        'MMA',
        'Entry-level MMA tournament designed for debuting fighters.',
        6,
        '2025-08-30 10:00:00', -- FINISHED
        'Obroniki Śląskie',
        'Poland',
        150,
        '2025-08-15 23:59:59',
        'public/img/mma_beginners.jpg',
        200,
        FALSE
    ),
    (
        'Wrestling & Grappling Seminar',
        'Wrestling/Grappling',
        'Technical seminar led by international coaches.',
        2,
        '2026-07-12 11:00:00', -- UPCOMING
        'Cracow',
        'Poland',
        80,
        '2026-06-30 23:59:59',
        'public/img/seminar.jpg',
        80,
        FALSE
    ),
    (
        'ALMMA 254',
        'MMA',
        'Amateur MMA League - tournament for debutants and beginner fighters.',
        6,
        '2026-05-23 15:00:00', -- UPCOMING
        'Sochaczew',
        'Poland',
        160,
        '2026-04-30 23:59:59',
        'public/img/almma_254.jpg',
        300,
        FALSE
    ),
    (
        'European BJJ Championship',
        'BJJ',
        'Pan-European Brazilian Jiu-Jitsu championship.',
        6,
        '2026-11-15 09:00:00',
        'Lisbon',
        'Portugal',
        500,
        '2026-10-15 23:59:59',
        'public/img/european_bjj.jpg',
        800,
        FALSE
    );

-- VIEW 1: Detailed fight history
CREATE VIEW v_user_fights AS
SELECT
    f.user_id,
    f.result,
    f.method,
    f.fight_date,
    e.title AS event_name,
    e.discipline,
    ud.firstname AS opponent_firstname,
    ud.lastname AS opponent_lastname
FROM fights f
JOIN events e ON f.event_id = e.id
JOIN user_details ud ON f.opponent_id = ud.user_id;

-- VIEW 2: Automatic record counting by discipline
CREATE VIEW v_athlete_records AS
SELECT 
    f.user_id,
    e.discipline,
    COUNT(f.id) FILTER (WHERE f.result = 'WIN') AS wins,
    COUNT(f.id) FILTER (WHERE f.result = 'LOSS') AS losses,
    COUNT(f.id) FILTER (WHERE f.result = 'DRAW') AS draws
FROM fights f
JOIN events e ON f.event_id = e.id
GROUP BY f.user_id, e.discipline;

-- VIEW 3: Global athlete ranking with points calculation
CREATE VIEW v_rankings AS
SELECT 
    ud.user_id,
    ud.firstname,
    ud.lastname,
    c.name as club_name,
    ar.discipline,
    ar.wins,
    ar.losses,
    ar.draws,
    (ar.wins * 3 + ar.draws * 1) as points
FROM user_details ud
JOIN v_athlete_records ar ON ud.user_id = ar.user_id
LEFT JOIN clubs c ON ud.club_id = c.id
ORDER BY points DESC;

-- VIEW 4: Club ranking based on athlete points
CREATE OR REPLACE VIEW v_club_rankings AS
SELECT 
    club_name,
    COUNT(DISTINCT user_id) as athlete_count,
    SUM(wins) as total_wins,
    SUM(points) as total_points,
    discipline
FROM v_rankings
WHERE club_name IS NOT NULL
GROUP BY club_name, discipline
ORDER BY total_points DESC;

-- TRIGGER 1: creates mirrored fight records for both fighters
CREATE OR REPLACE FUNCTION add_mirror_fight()
RETURNS TRIGGER AS $$
DECLARE
    mirror_result VARCHAR(10);
BEGIN
    IF NEW.result = 'WIN' THEN mirror_result := 'LOSS';
    ELSIF NEW.result = 'LOSS' THEN mirror_result := 'WIN';
    ELSE mirror_result := 'DRAW';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM fights 
        WHERE user_id = NEW.opponent_id 
          AND opponent_id = NEW.user_id 
          AND event_id = NEW.event_id
          AND result = mirror_result
          AND method = NEW.method
    ) THEN
        INSERT INTO fights (user_id, opponent_id, event_id, result, method, fight_date)
        VALUES (NEW.opponent_id, NEW.user_id, NEW.event_id, mirror_result, NEW.method, NEW.fight_date);
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_after_fight_insert
AFTER INSERT ON fights
FOR EACH ROW EXECUTE FUNCTION add_mirror_fight();

-- Initial fights data
INSERT INTO fights (user_id, opponent_id, event_id, result, method, fight_date) VALUES 
    (3, 4, 5, 'LOSS', 'KO/TKO', '2025-08-30'),
    (3, 5, 5, 'WIN', 'Submission', '2025-08-30'),
    (3, 5, 5, 'DRAW', 'Split Draw', '2025-08-30'),
    (3, 5, 2, 'LOSS', 'Submission', '2025-10-15'),
    (3, 5, 4, 'WIN', 'KO/TKO', '2026-03-21'),
    (3, 4, 3, 'DRAW', 'Majority Draw', '2025-11-09'),
    (3, 4, 7, 'WIN', 'Submission', '2026-05-23'),
    (3, 5, 7, 'WIN', 'Majority Decision', '2026-05-23'),
    (7, 5, 7, 'WIN', 'Unanimous Decision', '2026-05-23');
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
    email VARCHAR(255) UNIQUE NOT NULL,
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

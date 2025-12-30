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
    nickname VARCHAR(100),
    club_id INTEGER REFERENCES clubs(id) ON DELETE SET NULL,
    wins INTEGER DEFAULT 0,
    losses INTEGER DEFAULT 0,
    draws INTEGER DEFAULT 0,
    bio TEXT,
    image_url VARCHAR(255) DEFAULT 'public/img/default-avatar.png'
);

-- Events table (tournaments, seminars)
CREATE TABLE events (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    date TIMESTAMP NOT NULL,
    location VARCHAR(255) NOT NULL,
    image_url TEXT,
    capacity INTEGER DEFAULT 100,
    is_featured BOOLEAN DEFAULT FALSE
);

-- Fights table (users N:N events)
CREATE TABLE fights (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    opponent_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    event_id INTEGER NOT NULL REFERENCES events(id) ON DELETE CASCADE,
    result VARCHAR(10) NOT NULL, -- 'WIN', 'LOSS', 'DRAW'
    method VARCHAR(100) NOT NULL, -- 'KO/TKO', 'Submission', 'Decision'
    fight_date DATE NOT NULL
);

-- Integrity constraints
ALTER TABLE fights ADD CONSTRAINT check_result CHECK (result IN ('WIN', 'LOSS', 'DRAW'));
ALTER TABLE fights ADD CONSTRAINT check_method CHECK (method IN ('KO/TKO', 'Submission', 'Decision'));
ALTER TABLE fights ADD CONSTRAINT check_different_fighters CHECK (user_id <> opponent_id);

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
    ('admin@martialhub.pl', '$2b$10$ZbzQrqD1vDhLJpYe/vzSbeDJHTUnVPCpwlXclkiFa8dO5gOAfg8tq', 'admin'),
    ('maciej.kawulski@mail.com', '$2b$10$ZbzQrqD1vDhLJpYe/vzSbeDJHTUnVPCpwlXclkiFa8dO5gOAfg8tq', 'organizer'),
    ('jan.kowalski@mail.com', '$2b$10$ZbzQrqD1vDhLJpYe/vzSbeDJHTUnVPCpwlXclkiFa8dO5gOAfg8tq', 'user'),
    ('adam.nowak@mail.com', '$2b$10$ZbzQrqD1vDhLJpYe/vzSbeDJHTUnVPCpwlXclkiFa8dO5gOAfg8tq', 'user'),
    ('piotr.lewandowski@mail.com', '$2b$10$ZbzQrqD1vDhLJpYe/vzSbeDJHTUnVPCpwlXclkiFa8dO5gOAfg8tq', 'user');

-- Initial user details
INSERT INTO user_details (user_id, firstname, lastname, bio) VALUES
    (1, 'Admin', 'MartialHub', 'System administrator.'),
    (2, 'Maciej', 'Kawulski', 'Responsible for managing events and fights.');

INSERT INTO user_details (user_id, firstname, lastname, club_id, wins, losses, draws, bio) VALUES 
    (3, 'Jan', 'Kowalski', 4, 12, 4, 1, 'K1 professional fighter.'),
    (4, 'Adam', 'Nowak', 2, 5, 2, 0, 'BJJ Blue Belt'),
    (5, 'Piotr', 'Lewandowski', 1, 8, 3, 2, 'Boxing enthusiast');

-- Initial events data
INSERT INTO events (title, description, date, location, image_url, capacity, is_featured) VALUES 
    (
        'Polish MMA Championship 2026',
        'National-level MMA championship featuring top amateur fighters.',
        '2026-10-18 10:00:00', -- UPCOMING
        'Warsaw',
        'public/img/mma_championship.jpg',
        500,
        TRUE
    ),
    (
        'Regional Judo Cup',
        'Regional judo tournament for junior and senior competitors.',
        '2025-10-15 09:00:00', -- FINISHED
        'Cracow',
        'public/img/judo_cup.jpg',
        300,
        FALSE
    ),
    (
        'Copa Silesia 8',
        'Brazilian Jiu-Jitsu open tournament with gi and no-gi divisions.',
        '2025-11-09 08:30:00', -- FINISHED
        'Warsaw',
        'public/img/bjj_open.jpg',
        400,
        FALSE
    ),
    (
        'High Kick 10',
        'Professional and amateur kickboxing bouts under K-1 rules.',
        '2026-03-21 18:00:00', -- UPCOMING
        'Gliwice',
        'public/img/kickboxing.jpg',
        250,
        FALSE
    ),
    (
        'ALMMA 219',
        'Entry-level MMA tournament designed for debuting fighters.',
        '2025-08-30 10:00:00', -- FINISHED
        'Obroniki Śląskie',
        'public/img/mma_beginners.jpg',
        200,
        FALSE
    ),
    (
        'Wrestling & Grappling Seminar',
        'Technical seminar led by international coaches.',
        '2026-07-12 11:00:00', -- UPCOMING
        'Cracow',
        'public/img/seminar.jpg',
        80,
        FALSE
    );

-- Initial fights data
INSERT INTO fights (user_id, opponent_id, event_id, result, method, fight_date) VALUES 
    (2, 3, 5, 'WIN', 'KO/TKO', '2025-08-15'),
    (2, 4, 2, 'LOSS', 'Submission', '2025-10-15'),
    (2, 3, 3, 'DRAW', 'Decision', '2024-11-09');
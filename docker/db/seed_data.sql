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
    ('jan.kowalski@martialhub.com', '$2y$10$BX7LMHlrH8RrrPE/Qtx9uumP/UHoFmPDeMTItB/zyYh8dCRAY6f4K', 'user'),
    ('adam.nowak@martialhub.com', '$2y$10$BX7LMHlrH8RrrPE/Qtx9uumP/UHoFmPDeMTItB/zyYh8dCRAY6f4K', 'user'),
    ('piotr.lewandowski@martialhub.com', '$2y$10$BX7LMHlrH8RrrPE/Qtx9uumP/UHoFmPDeMTItB/zyYh8dCRAY6f4K', 'user'),
    ('mmapolska@martialhub.com', '$2y$10$VkD3RTJlEaVVVExuM/Lwl.pz2zsEFJvhlFxyiEZn8A0U3dmJp4tva', 'organizer'),
    ('andrzej.wisniewski@martialhub.com', '$2y$10$BX7LMHlrH8RrrPE/Qtx9uumP/UHoFmPDeMTItB/zyYh8dCRAY6f4K', 'user');

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
        'Oborniki Śląskie',
        'Poland',
        150,
        '2025-08-15 23:59:59',
        'public/img/mma_beginners.jpg',
        200,
        FALSE
    ),
    (
        'High Kick 9',
        'Kickboxing',
        'Professional and amateur kickboxing bouts under K-1 rules.',
        2,
        '2025-12-15 18:00:00', -- FINISHED
        'Wrocław',
        'Poland',
        200,
        '2025-11-30 23:59:59',
        'public/img/kickboxing.jpg',
        250,
        FALSE
    ),
    (
        'ALMMA 237',
        'MMA',
        'Amateur MMA League - tournament for debutants and beginner fighters.',
        6,
        '2025-12-20 15:00:00', -- FINISHED
        'Sochaczew',
        'Poland',
        160,
        '2025-12-05 23:59:59',
        'public/img/almma_237.jpg',
        250,
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

-- Initial fights data
INSERT INTO fights (user_id, opponent_id, event_id, result, method, fight_date) VALUES 
    (3, 4, 5, 'LOSS', 'KO/TKO', '2025-08-30'),
    (3, 5, 5, 'WIN', 'Submission', '2025-08-30'),
    (3, 5, 5, 'DRAW', 'Split Draw', '2025-08-30'),
    (3, 5, 2, 'LOSS', 'Submission', '2025-10-15'),
    (3, 5, 6, 'WIN', 'KO/TKO', '2025-12-15'),
    (3, 4, 3, 'DRAW', 'Majority Draw', '2025-11-09'),
    (3, 4, 7, 'WIN', 'Submission', '2025-12-20'),
    (3, 5, 7, 'WIN', 'Majority Decision', '2025-12-20'),
    (7, 5, 7, 'WIN', 'Unanimous Decision', '2025-12-20');
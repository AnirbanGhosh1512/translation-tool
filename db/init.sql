-- Create table
CREATE TABLE IF NOT EXISTS translations (
    SID TEXT NOT NULL,
    LangId TEXT NOT NULL,
    Text TEXT
);

-- Insert data
INSERT INTO translations (SID, LangId, Text)
VALUES 
('HelloWorldID', 'en', 'Hello World'),
('GoodMorningID', 'de', 'Guten Morgen'),
('GoodByeID', 'fr', 'Bonjour');


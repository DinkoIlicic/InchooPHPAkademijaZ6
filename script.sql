drop database if exists social_network;
create database social_network character set utf8mb4 COLLATE utf8mb4_unicode_ci;
use social_network;

create table post(
id int not null primary key auto_increment,
content text,
image text,
post_created VARCHAR(30)
)engine=InnoDB;

create table comment(
id int not null primary key auto_increment,
post_id int, FOREIGN KEY (post_id) REFERENCES post(id),
content text
)engine=InnoDB;

insert into post(content) values
('Danas je opet padao snijeg.'),
('Jedem jagode.');
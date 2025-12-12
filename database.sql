create database fitmanager;
use fitmanager;

create table cours(
	id_cours int primary key auto_increment,
    nom_cours varchar(50),
    categories_cours varchar(50),
    date_cours date,
    heure_cours time,
    duree_cours numeric(3,1),
    max_participants int
);

create table equipements(
	id_equipements int primary key auto_increment,
    nom_equipements varchar(50),
    type_equipements varchar(30),
    quantity_equipements int,
    etat_equipements varchar(20)
);

create table cours_equipements(
    id_c int,
    id_e int,
    primary key(id_c,id_e),
    foreign key(id_c) references cours(id_cours),
    foreign key (id_e) references equipements(id_equipements)
);
CREATE TABLE users (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
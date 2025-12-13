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
create table users (
    id_user INT primary key AUTO_INCREMENT,
    username varchar(50) unique not null,
    email varchar(100) unique not null,
    password varchar(255) not null,
    full_name varchar(100),
    date_created timestamp default current_timestamp
);
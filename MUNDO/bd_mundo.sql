create database bd_mundo;

use bd_mundo;

create table continentes (
    id_continente int auto_increment primary key,
    nm_continente varchar(100) not null,
    populacao bigint,
    area_km2 decimal(15,2),
    total_paises int
);

create table governantes (
    id_governante int auto_increment primary key,
    nm_governante varchar(150) not null,
    partido_politico varchar(150),
    dt_nascimento date,
    idade int,
    dt_inicio_mandato date,
    dt_final_mandato date
);

create table paises (
    id_pais int auto_increment primary key,
    nm_pais varchar(100) not null,
    continente_id int,
    populacao bigint,
    area_km2 decimal(15,2),
    idioma varchar(100),
    governante_id int,
    clima varchar(100),
    regime_politico varchar(100),
    moeda varchar(100),
    foreign key (continente_id) references continentes(id_continente),
    foreign key (governante_id) references governantes(id_governante)
);

create table cidades (
    id_cidade int auto_increment primary key,
    nome varchar(100) not null,
    pais_id int,
    populacao bigint,
    area_km2 decimal(15,2),
    clima varchar(100),
    governante_id int,
    dt_fundacao date,
    foreign key (pais_id) references paises(id_pais),
    foreign key (governante_id) references governantes(id_governante));
create table table1 (
    id serial not null primary key,
    field1 varchar not null,
    field2 varchar not null,
    field3 timestamp default now()
);

create table table2 (
    id serial not null primary key,
    field1 varchar not null,
    field2 integer not null
);
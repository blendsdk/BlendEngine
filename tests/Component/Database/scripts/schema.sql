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

create table address_table (
    zipcode varchar not null,
    housenumber varchar not null,
    street varchar not null,
    primary key(zipcode,housenumber)
);


create table const_table (
    id serial not null primary key,
    field1 varchar unique not null
)
create schema schema1;
create schema schema2;

create table p1 (
    col1 varchar
);

create table p2 (
    col1 varchar,
    col2 varchar
);

create table schema1.s1(
    col1 varchar
);

create table schema1.s2(
    col1 varchar,
    col2 varchar
);

create table schema1.s3(
    col1 varchar,
    col2 varchar,
    col3 varchar
);

create table schema2.s1();
create table schema2.s2();
create table schema2.s3();
create table schema2.s4();

create schema k;

create table k.table_with_pk (
    id serial not null primary key
);

create table k.table_with_two_pk (
    k1 varchar,
    k2 varchar,
    primary key(k1,k2)
);

create table k.table_with_unique_key (
    u1 varchar unique
);

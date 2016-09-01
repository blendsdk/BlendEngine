create table table1 (
    id serial not null primary key,
    col1 varchar
);

insert into table1(col1) values(E'a');
insert into table1(col1) values(E'a\na');
insert into table1(col1) values(E'a\ra');
insert into table1(col1) values(E'a\ta');
insert into table1(col1) values(E'a\\a');
insert into table1(col1) values(E'a''a');
insert into table1(col1) values(E'a"a');
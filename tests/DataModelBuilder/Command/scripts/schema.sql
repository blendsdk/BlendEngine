create schema customer_reporting;
create table customer_reporting.aggrigations ();

create table sys_user (
    user_id serial not null primary key,
    user_name varchar not null unique,
    user_email varchar not null unique,
    user_password varchar not null,
    date_created timestamp not null default now(),
    nullable_column varchar
);

create table sys_user_profile (
    user_profile_id serial not null primary key,
    user_id integer not null references sys_user(user_id) on update cascade on delete cascade,
    user_profile_firstname varchar,
    user_profile_lastname varchar,
    user_profile_gender char
);

create table sys_product (
    product_id serial not null primary key,
    product_name varchar not null unique,
    product_number varchar not null unique,
    product_unit_price numeric not null default 0
);

create table sys_order (
    order_id serial not null primary key,
    order_number varchar not null unique,
    order_date timestamp not null default now()
);

create index on sys_order (order_date);

create table sys_order_item (
    order_item_id serial not null primary key,
    order_id integer not null references sys_order(order_id) on update cascade on delete cascade,
    product_id integer not null references sys_product(product_id) on update cascade on delete cascade,
    order_item_amount numeric not null default 0    
);


create table sys_addess(
    id serial not null primary key,
    post_code varchar,
    house_number varchar,
    unique(post_code,house_number)
);

create or replace view sys_sample_view as
select
    md5(now()::text) as secret_key,
    *
from
    generate_series(0,100);
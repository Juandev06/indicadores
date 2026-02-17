/*
* primera ronda de cambios
*/
ALTER TABLE indicators ADD formula varchar(1024);
update indicators i join formules f on f.indicator_id = i.id set formula = f.formule;
ALTER TABLE indicators ADD tolerancia TINYINT not null default 5;
ALTER TABLE indicators ADD tendencia TINYINT not null default 1;
ALTER TABLE indicators MODIFY formula varchar(1024) not null;
ALTER TABLE indicators DROP FOREIGN KEY indicators_areas_id_foreign;
ALTER TABLE indicators DROP FOREIGN KEY indicators_calculations_id_foreign;
ALTER TABLE indicators DROP COLUMN areas_id;
ALTER TABLE indicators DROP COLUMN calculations_id;

update variables set name='GOA AA Costos Operativos y admin. generados en el periodo' where id = 24;
update variables set name='RAHCg Reporte Afectación Hídrica Asociada a Fenómenos Clima' where id = 40;
update variables set name='NAF Número Suscriptores Afectados Durante Evento Afectación' where id = 42;
update variables set name='NFRDi Número de Fallas En Redes de Conducción y Distribución' where id = 67;
update variables set name='VEPa/p Valor en pesos corr del activo y/o proyecto ejecutado' where id = 74;
ALTER TABLE variables MODIFY name varchar(60) not null;

create table `dashboard` (
    `id` bigint unsigned not null auto_increment primary key,
    `orden` tinyint not null,
    `id_indicator` bigint unsigned not null,
    `id_usuario` bigint unsigned not null,
    `chart_type` varchar(255) not null default 'line',
    `show_detail` tinyint(1) not null default '0',
    `created_at` timestamp null,
    `updated_at` timestamp null
) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
alter table `dashboard` add constraint `dashboard_id_usuario_foreign` foreign key (`id_usuario`) references `users` (`id`)
alter table `dashboard` add constraint `dashboard_id_indicator_foreign` foreign key (`id_indicator`) references `indicators` (`id`)


create table `periodicity_dets` (
    `id` bigint unsigned not null auto_increment primary key,
    `id_periodicity` bigint unsigned not null,
    `name` varchar(20) not null,
    `order` tinyint not null,
    `created_at` timestamp null,
    `updated_at` timestamp null
) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
alter table `periodicity_dets` add constraint `periodicity_dets_id_periodicity_foreign` foreign key (`id_periodicity`) references `periodicities` (`id`)

insert into periodicity_dets values (null, 1, 'ene', 1, now(), now());
insert into periodicity_dets values (null, 1, 'feb', 2, now(), now());
insert into periodicity_dets values (null, 1, 'mar', 3, now(), now());
insert into periodicity_dets values (null, 1, 'abr', 4, now(), now());
insert into periodicity_dets values (null, 1, 'may', 5, now(), now());
insert into periodicity_dets values (null, 1, 'jun', 6, now(), now());
insert into periodicity_dets values (null, 1, 'jul', 7, now(), now());
insert into periodicity_dets values (null, 1, 'ago', 8, now(), now());
insert into periodicity_dets values (null, 1, 'sep', 9, now(), now());
insert into periodicity_dets values (null, 1, 'oct', 10, now(), now());
insert into periodicity_dets values (null, 1, 'nov', 11, now(), now());
insert into periodicity_dets values (null, 1, 'dic', 12, now(), now());
insert into periodicity_dets values (null, 2, 'ene-feb', 1, now(), now());
insert into periodicity_dets values (null, 2, 'mar-abr', 2, now(), now());
insert into periodicity_dets values (null, 2, 'may-jun', 3, now(), now());
insert into periodicity_dets values (null, 2, 'jul-ago', 4, now(), now());
insert into periodicity_dets values (null, 2, 'sep-oct', 5, now(), now());
insert into periodicity_dets values (null, 2, 'nov-dic', 6, now(), now());
insert into periodicity_dets values (null, 3, 'trim 1', 1, now(), now());
insert into periodicity_dets values (null, 3, 'trim 2', 2, now(), now());
insert into periodicity_dets values (null, 3, 'trim 3', 3, now(), now());
insert into periodicity_dets values (null, 3, 'trim 4', 4, now(), now());
insert into periodicity_dets values (null, 4, 'Sem 1', 1, now(), now());
insert into periodicity_dets values (null, 4, 'Sem 2', 2, now(), now());
insert into periodicity_dets values (null, 5, 'año', 1, now(), now());

ALTER TABLE set_values_indicators DROP COLUMN formule_id;

drop table values_formules;
drop table formules;

/*
* segunda ronda de cambios
*/
ALTER TABLE variables DROP FOREIGN KEY variables_areas_id_foreign;
ALTER TABLE variables DROP COLUMN areas_id;






/* ver foreign keys constraints */
select * from information_schema.referential_constraints where constraint_schema = 'indicadores';
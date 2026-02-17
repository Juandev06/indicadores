rename table set_values_indicators to indicador_valores;
alter table indicador_valores change indicator_id id_indicador bigint(20) unsigned not null;
alter table indicador_valores change month_id mes int not null;
alter table indicador_valores drop column user;
alter table indicador_valores change value valor decimal(20,2) not null;
alter table indicador_valores add column ano int(4) not null after id_indicador;
alter table indicador_valores add column id_usuario bigint(20) unsigned not null;
alter table indicador_valores add column obs text;
update indicador_valores set ano=2022;

rename table enable_variable to variable_periodo_habilitado;
alter table variable_periodo_habilitado change status estado varchar(1) not null;
alter table variable_periodo_habilitado drop column created_at ;
alter table variable_periodo_habilitado drop column updated_at ;
alter table variable_periodo_habilitado drop column id_periodicity;
alter table variable_periodo_habilitado add column ano int not null;
alter table variable_periodo_habilitado add column mes tinyint not null;
alter table variable_periodo_habilitado add column fecha_activacion datetime;
alter table variable_periodo_habilitado add column fecha_inactivacion datetime;
alter table variable_periodo_habilitado add column id_usuario_activacion bigint(20) unsigned  not null;
alter table variable_periodo_habilitado add column id_usuario_inactivacion bigint(20) unsigned;

update roles set guard_name='admin' where id=1;
update roles set guard_name='colaborador' where id=2;
update roles set guard_name='auditor' where id=5;
update roles set guard_name='jefe_area' where id=6;
delete from roles where id=8; -- Admin Develop


alter table users change areas_id id_area bigint(20) unsigned not null;
alter table users change profiles_id id_rol bigint(20) unsigned not null;


rename table set_variables to variable_valores;
alter table variable_valores change id_month mes tinyint not null;
alter table variable_valores change value valor decimal(20,2) not null;
alter table variable_valores add column ano int not null;
update variable_valores set user=1; -- este cambio se hace porque la tabla no está guardando el id del usuario
alter table variable_valores change user id_usuario bigint(20) unsigned not null;
update variable_valores set ano=2022; -- validar que los valores ingresados en la base de datos sí sean de este año

rename table periodicities to aux_periodos;
alter table aux_periodos drop created_at;
alter table aux_periodos drop updated_at;
alter table aux_periodos change status estado varchar(1) not null default 'A';
alter table aux_periodos change name nombre varchar(255) not null;

alter table variables drop column value;
alter table variables DROP FOREIGN KEY variables_periodicities_id_foreign;
alter table variables DROP FOREIGN KEY variables_types_id_foreign;
alter table variables DROP FOREIGN KEY variables_user_id_foreign;
alter table variables change periodicities_id id_periodo bigint(20) unsigned not null;
alter table variables change user_id id_usuario bigint(20) unsigned not null;
alter table variables change types_id tipo varchar(1) not null default 'N';
update variables set tipo='N' where tipo='1';
update variables set tipo='P' where tipo='2';
alter table variables change description obs varchar(255);
alter table variables change status estado varchar(1) not null default 'A';
alter table variables change name nombre varchar(255) not null;


alter table indicators DROP FOREIGN KEY indicators_periodicities_id_foreign;
alter table indicators DROP FOREIGN KEY indicators_types_id_foreign;
alter table indicators DROP FOREIGN KEY indicators_user_id_foreign;

alter table indicadores add column ficha_tec_archivo varchar(255);
alter table indicadores add column ficha_tec_carpeta varchar(255);
alter table indicadores add column ext varchar(50);
alter table indicadores add column mimetype varchar(255);
alter table indicadores add column size int(11);


drop table types;

rename table indicators to indicadores;
alter table indicadores change name nombre varchar(255) not null;
alter table indicadores change periodicities_id id_periodo bigint(20) unsigned not null;
alter table indicadores change status estado varchar(1) not null default 'A';
alter table indicadores change types_id tipo varchar(1) not null default 'N';
alter table indicadores change user_id id_usuario bigint(20) unsigned not null;
update indicadores set tipo='N' where tipo='1';
update indicadores set tipo='P' where tipo='2';

drop table calculations;

alter table month_periodicity DROP FOREIGN KEY month_periodicity_months_id_foreign;
alter table goals DROP FOREIGN KEY goals_months_id_foreign;
drop table months;

rename table goals to metas;
alter table metas change indicator_id id_indicador bigint(20) unsigned not null;
alter table metas change periodicities_id id_periodo bigint(20) unsigned not null;

rename table category_indicators to indicador_categorias;
alter table indicador_categorias change category_id id_categoria bigint(20) unsigned not null;
alter table indicador_categorias change subcategory_id id_subcategoria bigint(20) unsigned not null;
alter table indicador_categorias change indicator_id id_indicador bigint(20) unsigned not null;

delete from variable_periodo_habilitado;

alter table enable_periodicities change periodicity_id id_periodo bigint(20) unsigned not null;
alter table enable_periodicities change month_id mes smallint not null;

rename table categories to categorias;
alter table categorias change name nombre varchar(100) not null;
alter table categorias change status estado varchar(1) not null;


rename table subcategories to subcategorias;
alter table subcategorias change Category_id id_categoria bigint(20) unsigned not null;
alter table subcategorias change name nombre varchar(100) not null;
alter table subcategorias change status estado varchar(1) not null;

/** FOREIGN KEYS **

select * from INFORMATION_SCHEMA.TABLE_CONSTRAINTS where CONSTRAINT_TYPE = 'FOREIGN KEY' and CONSTRAINT_SCHEMA='indicadores';

ALTER TABLE Orders DROP FOREIGN KEY FK_PersonOrder;
ALTER TABLE Orders ADD FOREIGN KEY (PersonID) REFERENCES Persons(PersonID); 

mysql> select TABLE_NAME, CONSTRAINT_NAME from INFORMATION_SCHEMA.TABLE_CONSTRAINTS where CONSTRAINT_TYPE = 'FOREIGN KEY' and CONSTRAINT_SCHEMA='indicadores';
mysql> select * from INFORMATION_SCHEMA.TABLE_CONSTRAINTS where CONSTRAINT_TYPE = 'FOREIGN KEY' and CONSTRAINT_SCHEMA='indicadores';
+---------------------------------------------+-----------------------+
| CONSTRAINT_NAME                             | TABLE_NAME            |
+---------------------------------------------+-----------------------+
| dashboard_id_indicator_foreign              | dashboard             |
| dashboard_id_usuario_foreign                | dashboard             |
| goals_indicator_id_foreign                  | metas                 |
| goals_periodicities_id_foreign              | metas                 |
| periodicity_dets_id_periodicity_foreign     | periodicity_dets      |

| model_has_permissions_permission_id_foreign | model_has_permissions |
| model_has_roles_role_id_foreign             | model_has_roles       |
| role_has_permissions_permission_id_foreign  | role_has_permissions  |
| role_has_permissions_role_id_foreign        | role_has_permissions  |
| users_areas_id_foreign                      | users                 |
| users_profiles_id_foreign                   | users                 |
+---------------------------------------------+-----------------------+


+-----------------------------+
| Tables_in_indicadores       |
+-----------------------------+
# areas                       |
# aux_periodos                |
# categorias                  |
# dashboard                   |
| enable_goals                |
| enable_periodicities        |
# failed_jobs                 |
# metas                       |
# indicador_categorias        |
# indicador_valores           |
# indicadores                 |
# migrations                  |
# model_has_permissions       |
# model_has_roles             |
| month_periodicity           |
# password_resets             |
| periodicity_dets            |
# permissions                 |
# personal_access_tokens      |
# profiles                    |
# role_has_permissions        |
# roles                       |
| set_values_goals_indicators |
| statuses                    |
# subcategorias               |
| users                       |
# variable_periodo_habilitado |
# variable_valores            |
# variables                   |
+-----------------------------+


*/

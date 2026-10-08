-- ========================================================
-- Banco de Dados: bdApoio.s3db
-- Exportação da Estrutura de Dados e Cargas
-- Data de Exportação: 2026-10-08 14:43:18
-- ========================================================

-- --------------------------------------------------------
-- TABELAS
-- --------------------------------------------------------

-- Estrutura da tabela `acesso_user`
CREATE TABLE "acesso_user" (
	`iduser`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`login_user`	TEXT NOT NULL,
	`pwd_user`	TEXT NOT NULL,
	`sit_ativo`	TEXT NOT NULL DEFAULT 'S'
);

-- Estrutura da tabela `dado_apoio`
CREATE TABLE [dado_apoio] (
[seq_dado_apoio] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
[seq_dado_apoio_pai] iNTEGER  NULL,
[tip_dado_apoio] VARCHAR(100)  NOT NULL,
[sig_dado_apoio] VARCHAR(25)  NULL,
[bol_cancelado] BOOLEAN DEFAULT '''''''false''''''' NOT NULL
);

-- Estrutura da tabela `helpOnLine`
CREATE TABLE [helpOnLine] (
					[help_form] VARCHAR(50)  NULL,
					[help_field] VARCHAR(50)  NOT NULL,
					[help_title] VARCHAR(50)  NULL,
					[help_text] TEXT  NULL,
					PRIMARY KEY ([help_form],[help_field])
					);

-- Estrutura da tabela `horario_atendimento`
CREATE TABLE "horario_atendimento" (
	`idhorario_atendimento`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`idpessoa_dentista`	INTEGER NOT NULL,
	`horario`	TEXT NOT NULL
);

-- Estrutura da tabela `pessoa`
CREATE TABLE "pessoa" (
	`idpessoa`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	`nom_pessoa`	TEXT NOT NULL,
	`cpf_cnpj`	TEXT NOT NULL,
	`tp_pessoa`	TEXT NOT NULL DEFAULT 'PF' CHECK(tp_pessoa IN ( 'PF' , 'PJ' ))
);

-- Estrutura da tabela `tb_arquivo`
CREATE TABLE [tb_arquivo] (
[id_arquivo] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
[nome_arquivo] varchar(200)  NULL
);

-- Estrutura da tabela `tb_blob`
CREATE TABLE [tb_blob] (
[id_blob] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
[nome_arquivo] varchar(200)  NULL,
[conteudo_arquivo] BLOB  NULL
);

-- Estrutura da tabela `tb_forma_pagamento`
CREATE TABLE [tb_forma_pagamento]
  (
     [idform_pagamento] INTEGER PRIMARY KEY NOT NULL,
     [descricao] VARCHAR(60) NOT NULL
  );

-- Estrutura da tabela `tb_mapcord`
CREATE TABLE [tb_mapcord] ( 
  [idtmapcord] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT
, [txnome] varCHAR(100) NULL
, [mapcord_lat] varCHAR(100) NOT NULL
, [mapcord_lon] varCHAR(100) NULL
, [dat_inclusao] date NOT NULL default current_timestamp
, [dat_update] date NULL
, [dat_del] date NULL
);

-- Estrutura da tabela `tb_municipio`
CREATE TABLE [tb_municipio] (
[cod_municipio] INTEGER  PRIMARY KEY NOT NULL,
[cod_uf] INTEGER  NOT NULL,
[nom_municipio] varchar(60)  NOT NULL
);

-- Estrutura da tabela `tb_paginacao`
CREATE TABLE [tb_paginacao] (
[id] INTEGER  NOT NULL PRIMARY KEY AUTOINCREMENT,
[descricao] VARCHAR(100)  NOT NULL
);

-- Estrutura da tabela `tb_pedido`
CREATE TABLE [tb_pedido] (
[id_pedido] INTEGER  NOT NULL PRIMARY KEY AUTOINCREMENT,
[data_pedido] date  NULL,
[nome_comprador] varCHAR(60)  NULL,
[forma_pagamento] char(1)  NULL
);

-- Estrutura da tabela `tb_pedido_item`
CREATE TABLE [tb_pedido_item] (
[id_item] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
[id_pedido] numeric  NOT NULL,
[produto] varchar(60)  NULL,
[quantidade] numeric(5,1)  NULL,
[preco] numeric(10,2)  NULL
);

-- Estrutura da tabela `tb_regiao`
CREATE TABLE [tb_regiao] (
[cod_regiao] inTEGER  NOT NULL PRIMARY KEY,
[nom_regiao] varchar(30)  NOT NULL
);

-- Estrutura da tabela `tb_test`
CREATE TABLE [tb_test] (
[id] INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL,
[obs] VARCHAR(2000)  NULL
);

-- Estrutura da tabela `tb_texto`
CREATE TABLE [tb_texto] ( 
  [idtexto] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT
, [txnome] varCHAR(100) NOT NULL
, [txdata] date NULL
, [stativo] char(1) NULL 
, [texto] TEXT NULL 
, [tx_data_inclusao] date NOT NULL default current_timestamp
);

-- Estrutura da tabela `tb_uf`
CREATE TABLE [tb_uf] (
[cod_uf] INTEGER  NOT NULL PRIMARY KEY AUTOINCREMENT,
[sig_uf] char(2)  NOT NULL,
[nom_uf] varchar(30)  NOT NULL,
[cod_regiao] int(1)  NOT NULL
);

-- --------------------------------------------------------
-- VIEWS
-- --------------------------------------------------------

-- Estrutura da view `vw_municipios`
CREATE VIEW [vw_municipios] AS 
select uf.cod_uf as cod_uf
      ,uf.sig_uf as sig_uf
      ,m.cod_municipio as cod_municipio
	  ,m.nom_municipio as nom_municipio
	  ,uf.cod_regiao as cod_regiao 
from tb_municipio m, tb_uf uf where m.cod_uf = uf.cod_uf;

-- Estrutura da view `vw_pedido_qtd_itens`
CREATE VIEW [vw_pedido_qtd_itens] AS
select p.id_pedido
     , p.data_pedido 
     ,nome_comprador
     ,forma_pagamento
     ,CASE forma_pagamento
       WHEN 1 THEN 'dinheiro'
       WHEN 2 THEN 'cheque'
       WHEN 3 THEN 'cartão'
       END as des_forma_pagamento
     ,(select count(id_item) from tb_pedido_item as pi where pi.id_pedido = p.id_pedido) as qtd
	 from tb_pedido as p;

-- Estrutura da view `vw_pedido_tree`
CREATE VIEW [vw_pedido_tree] AS
select null as idParent
     ,p.id_pedido as id
     ,nome_comprador as text
	  ,p.id_pedido as idGrupo
from tb_pedido as p
union
select pi.id_pedido as idParent
       ,pi.id_item as id 
	   ,pi.produto as text
		,pi.id_pedido as idGrupo
from tb_pedido_item as pi;

-- Estrutura da view `vw_tree_regiao_uf_mun`
CREATE VIEW [vw_tree_regiao_uf_mun] AS

select 're'||cod_regiao as id , null as id_pai, nom_regiao as nome
from tb_regiao

union all

select 'uf'||cod_uf as id , 're'||cod_regiao as id_pai, nom_uf as nome
from tb_uf

union all

select  'mu'||cod_municipio as id, 'uf'||cod_uf as id_pai , nom_municipio as nome
from tb_municipio;

-- Estrutura da view `vw_tree_uf_mun`
CREATE VIEW [vw_tree_uf_mun] AS 
select 'uf'||cod_uf as id , null as id_pai, nom_uf as nome
from tb_uf
union all

select  'mu'||cod_municipio as id, 'uf'||cod_uf as id_pai , nom_municipio as nome
from tb_municipio;

-- Estrutura da view `vw_tree_uf_mun2`
CREATE VIEW [vw_tree_uf_mun2] AS
select 'uf'||cod_uf as codigo , null as codigo_pai, nom_uf as descricao
from tb_uf
union all

select  'mu'||cod_municipio as codigo, 'uf'||cod_uf as codigo_pai , nom_municipio as descricao
from tb_municipio;

-- --------------------------------------------------------
-- DADOS DA TABELA `tb_mapcord`
-- --------------------------------------------------------

INSERT INTO [tb_mapcord] ([idtmapcord], [txnome], [mapcord_lat], [mapcord_lon], [dat_inclusao], [dat_update], [dat_del]) VALUES (1, 'Congresso Nacional - Brasília', '-15.799722', '-47.864167', '2026-10-08 14:43:18', NULL, NULL);
INSERT INTO [tb_mapcord] ([idtmapcord], [txnome], [mapcord_lat], [mapcord_lon], [dat_inclusao], [dat_update], [dat_del]) VALUES (2, 'Avenida Paulista - São Paulo', '-23.561414', '-46.655881', '2026-10-08 14:43:18', NULL, NULL);
INSERT INTO [tb_mapcord] ([idtmapcord], [txnome], [mapcord_lat], [mapcord_lon], [dat_inclusao], [dat_update], [dat_del]) VALUES (3, 'Cristo Redentor - Rio de Janeiro', '-22.951916', '-43.210487', '2026-10-08 14:43:18', NULL, NULL);
INSERT INTO [tb_mapcord] ([idtmapcord], [txnome], [mapcord_lat], [mapcord_lon], [dat_inclusao], [dat_update], [dat_del]) VALUES (4, 'Pelourinho - Salvador', '-12.971400', '-38.510800', '2026-10-08 14:43:18', NULL, NULL);
INSERT INTO [tb_mapcord] ([idtmapcord], [txnome], [mapcord_lat], [mapcord_lon], [dat_inclusao], [dat_update], [dat_del]) VALUES (5, 'Praça da Liberdade - Belo Horizonte', '-19.932000', '-43.937800', '2026-10-08 14:43:18', NULL, NULL);


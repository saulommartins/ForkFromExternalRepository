/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 28350 $
* $Name$
* $Author: gris $
* $Date: 2008-03-05 09:57:44 -0300 (Qua, 05 Mar 2008) $
*
* Versão 002.
*/

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2202
          , 365
          , 'FMManterElementoDespesa.php'
          , 'incluir'
          , 80
          , ''
          , 'Manter Elemento de Despesa');

UPDATE administracao.acao set ordem = 60 where cod_acao= 2194;
UPDATE administracao.acao set ordem = 70 where cod_acao= 2193;

CREATE TABLE tcepb.elemento_tribunal
             (estrutural    VARCHAR(5) NOT NULL,
             exercicio      CHAR(4)    NOT NULL,
CONSTRAINT pk_elemento_tribunal PRIMARY KEY (estrutural,exercicio));

CREATE TABLE tcepb.elemento_de_para
             (estrutural    VARCHAR(5) NOT NULL,
             exercicio      CHAR(4)    NOT NULL,
             cod_conta      INTEGER    NOT NULL,
  CONSTRAINT pk_elemento_de_para PRIMARY KEY (estrutural, exercicio, cod_conta),
  CONSTRAINT fk_elemento_de_para_1 FOREIGN KEY (estrutural, exercicio) REFERENCES tcepb.elemento_tribunal(estrutural,exercicio),
  CONSTRAINT fk_elemento_de_para_2 FOREIGN KEY (cod_conta, exercicio)  REFERENCES orcamento.conta_despesa(cod_conta,exercicio));

GRANT ALL ON tcepb.elemento_tribunal TO urbem;
GRANT ALL ON tcepb.elemento_de_para TO urbem;

INSERT INTO tcepb.elemento_tribunal VALUES('01.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('03.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('04.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('05.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('06.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('07.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('08.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('09.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('10.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('11.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('12.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('13.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('14.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('15.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('16.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('17.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('18.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('19.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('20.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('21.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('22.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('23.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('24.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('25.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('26.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('27.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('28.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.01','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.02','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.03','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.04','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.05','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.06','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.07','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.08','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.09','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.10','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.11','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.12','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.13','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.14','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.15','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.16','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.17','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.18','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('30.19','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('32.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('33.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('34.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('35.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.20','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.21','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.22','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.23','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.24','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.25','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.26','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.27','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.28','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.29','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.30','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.31','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.32','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.33','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.34','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.35','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.36','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.37','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('36.38','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('37.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('38.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.39','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.40','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.41','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.42','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.43','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.44','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.45','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.46','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.47','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.48','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.49','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.50','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.51','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.52','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.53','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.54','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.55','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.56','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.57','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.58','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.59','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.60','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('39.61','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('41.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('42.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('43.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('45.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('46.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('47.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('48.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('49.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('51.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.62','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.63','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.64','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.65','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.66','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.67','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.68','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.69','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.70','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.71','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.72','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.73','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.74','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.75','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.76','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.77','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.78','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.79','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('52.80','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('61.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('62.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('63.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('64.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('65.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('66.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('67.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('71.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('72.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('73.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('74.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('75.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('76.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('77.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('81.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('91.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('92.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('93.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('94.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('95.99','2008');
INSERT INTO tcepb.elemento_tribunal VALUES('96.99','2008');


DELETE FROM tcepb.tipo_obra WHERE exercicio='2008' AND cod_tipo = 0;

DELETE FROM tcepb.tipo_categoria_obra WHERE exercicio='2008' AND cod_tipo = 0;

DELETE FROM tcepb.tipo_fonte_obras WHERE exercicio='2008' AND cod_tipo = 0;

DELETE FROM tcepb.tipo_situacao WHERE exercicio='2008' AND cod_tipo = 0;


--Nova funcionalidade (relacionamento empenho - obras)

INSERT INTO administracao.acao ( cod_acao, cod_funcionalidade, nom_arquivo, parametro, ordem, complemento_acao, nom_acao)
VALUES ( 2212
,365
,'FMManterEmpenhoObras.php'
,'incluir'
,10
,''
,'Relacionar Empenho a Obras'
);

CREATE TABLE tcepb.empenho_obras (
    exercicio_obras     char(4)         not null,
    num_obra            integer         not null,
    exercicio_empenho   char(4)         not null,
    cod_entidade        integer         not null,
    cod_empenho         integer         not null,
    PRIMARY KEY (exercicio_empenho, cod_entidade, cod_empenho),
    FOREIGN KEY (exercicio_empenho, cod_entidade, cod_empenho) REFERENCES empenho.empenho (exercicio, cod_entidade, cod_empenho),
    FOREIGN KEY (exercicio_obras, num_obra) REFERENCES tcepb.obras (exercicio, num_obra)
);

GRANT ALL ON tcepb.empenho_obras to urbem;

---------
-- Ticket 11553
---------

 INSERT INTO administracao.acao (cod_acao
                              , cod_funcionalidade
                              , nom_arquivo
                              , parametro
                              , ordem
                              , complemento_acao
                              , nom_acao)
                         VALUES (2214
                              , 314
                              , 'FLModelosRREO.php'
                              , 'anexo7'
                              , 55
                              , ''
                              , 'Anexo 7');
----------
-- Ticket #12164
----------


CREATE TABLE stn.vinculo_stn_recurso
   (cod_vinculo INTEGER NOT NULL,
   descricao    VARCHAR(40),
   CONSTRAINT pk_vinculo PRIMARY KEY(cod_vinculo));

GRANT INSERT, DELETE, UPDATE, SELECT ON stn.vinculo_stn_recurso to GROUP urbem;

INSERT INTO stn.vinculo_stn_recurso VALUES(1, 'Fundeb');
INSERT INTO stn.vinculo_stn_recurso VALUES(2, 'MDE');

CREATE TABLE stn.vinculo_recurso
   (exercicio    char(04) NOT NULL,
   cod_entidade  integer not null,
   num_orgao     integer not null,
   num_unidade   integer not null,
   cod_recurso   integer not null,
   cod_vinculo   INTEGER NOT NULL,
   CONSTRAINT pk_vinculo_recurso PRIMARY KEY(exercicio, cod_entidade, num_orgao, num_unidade, cod_recurso),
   CONSTRAINT fk_vinculo_recurso_1 FOREIGN KEY(exercicio, cod_recurso) REFERENCES orcamento.recurso(exercicio, cod_recurso),
   CONSTRAINT fk_vinculo_recurso_2 FOREIGN KEY(cod_vinculo) REFERENCES stn.vinculo_stn_recurso(cod_vinculo),
   CONSTRAINT fk_vinculo_recurso_3 FOREIGN KEY (exercicio, num_unidade, num_orgao) REFERENCES orcamento.unidade(exercicio, num_unidade, num_orgao),
   CONSTRAINT fk_vinculo_recurso_4 FOREIGN KEY (exercicio, cod_entidade) REFERENCES orcamento.entidade(exercicio, cod_entidade)
   );

GRANT INSERT, DELETE, UPDATE, SELECT ON stn.vinculo_recurso to GROUP urbem;

------------
-- Ticket 12132
------------
UPDATE administracao.funcionalidade SET ordem = 2 WHERE cod_funcionalidade = 314;
UPDATE administracao.funcionalidade SET ordem = 3 WHERE cod_funcionalidade = 315;

INSERT INTO administracao.funcionalidade
            (cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem)
     SELECT 406
          , 36
          , 'Configuração'
          , 'instancias/configuracao/'
          , 1
      WHERE 0 = (SELECT count(*)
                   FROM administracao.funcionalidade
                  WHERE cod_funcionalidade = 406);

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2215
          , 406
          , 'FMManterRecurso.php'
          , '1'
          , 1
          , ''
          , 'Vincular Recurso com FUNDEB');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2217
          , 406
          , 'FMManterRecurso.php'
          , '2'
          , 2
          , ''
          , 'Vincular Recurso com MDE');


delete from  tcepb.relacionamento_receita_extra
 where cod_plano in (select relacionamento_receita_extra.cod_plano
   		       from contabilidade.plano_analitica
			  , contabilidade.plano_conta
			  , tcepb.relacionamento_receita_extra
		      where relacionamento_receita_extra.cod_plano = plano_analitica.cod_plano
			and relacionamento_receita_extra.exercicio = plano_analitica.exercicio
			and plano_analitica.cod_conta = plano_conta.cod_conta
			and plano_analitica.exercicio = plano_conta.exercicio
			and plano_conta.cod_estrutural LIKE '5.1.2%');

delete from  tcepb.relacionamento_despesa_extra
 where cod_plano in (select relacionamento_despesa_extra.cod_plano
   		       from contabilidade.plano_analitica
			  , contabilidade.plano_conta
			  , tcepb.relacionamento_despesa_extra
		      where relacionamento_despesa_extra.cod_plano = plano_analitica.cod_plano
			and relacionamento_despesa_extra.exercicio = plano_analitica.exercicio
			and plano_analitica.cod_conta = plano_conta.cod_conta
			and plano_analitica.exercicio = plano_conta.exercicio
			and plano_conta.cod_estrutural LIKE '5.1.2%');

-----------
--Ticket 12215
-----------

INSERT INTO administracao.funcionalidade
            (cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem)
     SELECT 407
          , 41
          , 'Relatórios'
          , 'instancias/relatorios/'
          , 3
      WHERE 0 = (SELECT count(*)
                   FROM administracao.funcionalidade
                  WHERE cod_funcionalidade = 407);

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2218
          , 407
          , 'FLObrasServicos.php'
          , 'imprimir'
          , 1
          , ''
          , 'Obras e Serviços');

INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (6
          , 41
          , 1
          , 'Obras e Serviços arquivo'
          , 'obrasServicos.rptdesign');

---------
--Ticket #12223
---------
DELETE FROM administracao.permissao
      WHERE cod_acao = 1757;

DELETE FROM administracao.auditoria
      WHERE cod_acao = 1757;

DELETE FROM administracao.acao
      WHERE cod_acao = 1757;

-------------------------------------
DELETE FROM administracao.permissao
      WHERE cod_acao = 2195;

DELETE FROM administracao.auditoria
      WHERE cod_acao = 2195;

DELETE FROM administracao.acao
      WHERE cod_acao = 2195;

DELETE FROM administracao.permissao
      WHERE cod_acao = 2189;

DELETE FROM administracao.auditoria
      WHERE cod_acao = 2189;

DELETE FROM administracao.acao
      WHERE cod_acao = 2189;

DELETE FROM administracao.permissao
      WHERE cod_acao = 2190;

DELETE FROM administracao.auditoria
      WHERE cod_acao = 2190;

DELETE FROM administracao.acao
      WHERE cod_acao = 2190;

DELETE FROM administracao.permissao
      WHERE cod_acao = 2173;

DELETE FROM administracao.auditoria
      WHERE cod_acao = 2173;

DELETE FROM administracao.acao
      WHERE cod_acao = 2173;

DELETE FROM administracao.permissao
      WHERE cod_acao = 2170;

DELETE FROM administracao.auditoria
      WHERE cod_acao = 2170;

DELETE FROM administracao.acao
      WHERE cod_acao = 2170;

DELETE FROM administracao.permissao
      WHERE cod_acao = 2182;

DELETE FROM administracao.auditoria
      WHERE cod_acao = 2182;

DELETE FROM administracao.acao
      WHERE cod_acao = 2182;

DELETE FROM administracao.permissao
      WHERE cod_acao = 1504;

DELETE FROM administracao.auditoria
      WHERE cod_acao = 1504;

DELETE FROM administracao.acao
      WHERE cod_acao = 1504;

DELETE FROM administracao.permissao
      WHERE cod_acao = 1505;

DELETE FROM administracao.auditoria
      WHERE cod_acao = 1505;

DELETE FROM administracao.acao
      WHERE cod_acao = 1505;

DELETE FROM administracao.permissao
      WHERE cod_acao = 1506;

DELETE FROM administracao.auditoria
      WHERE cod_acao = 1506;

DELETE FROM administracao.acao
      WHERE cod_acao = 1506;

DELETE FROM administracao.permissao
      WHERE cod_acao = 1507;

DELETE FROM administracao.auditoria
      WHERE cod_acao = 1507;

DELETE FROM administracao.acao
      WHERE cod_acao = 1507;










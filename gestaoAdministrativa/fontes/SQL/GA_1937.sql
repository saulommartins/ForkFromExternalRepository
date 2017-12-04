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
* $Id: GA_1936.sql 40540 2009-05-26 18:28:05Z fabio $
*
* Versão 1.93.7
*/

----------------
-- Ticket #15004
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2710
          , 170
          , 'FLConfigurarMigracaoOrganogramaDinamico.php'
          , 'configurar'
          , 7
          , ''
          , 'Configurar Migração do Organograma'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2711
          , 170
          , 'FMProcessarMigracaoOrganogramaDinamico.php'
          , 'migrar'
          , 8
          , ''
          , 'Processar Migração do Organograma'
          );

INSERT
  INTO administracao.permissao
SELECT numcgm
     , 2710   AS cod_acao
     , '2009' AS ano_exercicio
  FROM administracao.usuario
 WHERE username = 'admin';

INSERT
  INTO administracao.permissao
SELECT numcgm
     , 2711   AS cod_acao
     , '2009' AS ano_exercicio
  FROM administracao.usuario
 WHERE username = 'admin';


INSERT INTO administracao.configuracao
          ( cod_modulo
          , exercicio
          , parametro
          , valor
          )
     VALUES ( 19
          , '2009'
          , 'migra_orgao'
          , 'false'
          );


CREATE TABLE organograma.de_para_orgao (
    cod_orgao           INTEGER     NOT NULL,
    cod_organograma     INTEGER     NOT NULL,
    cod_orgao_new       INTEGER             ,
    CONSTRAINT pk_de_para_orgao     PRIMARY KEY                         (cod_orgao, cod_organograma),
    CONSTRAINT fk_de_para_orgao_1   FOREIGN KEY                         (cod_orgao)
                                    REFERENCES organograma.orgao        (cod_orgao),
    CONSTRAINT fk_de_para_orgao_2   FOREIGN KEY                         (cod_organograma)
                                    REFERENCES organograma.organograma  (cod_organograma),
    CONSTRAINT fk_de_para_orgao_3   FOREIGN KEY                         (cod_orgao_new)
                                    REFERENCES organograma.orgao        (cod_orgao)
);

GRANT ALL ON organograma.de_para_orgao TO GROUP urbem;


CREATE TABLE organograma.de_para_orgao_historico (
    timestamp               TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    cod_orgao               INTEGER         NOT NULL,
    cod_organograma         INTEGER         NOT NULL,
    cod_orgao_new           INTEGER         NOT NULL,
    numcgm                  INTEGER         NOT NULL,
    CONSTRAINT pk_de_para_orgao_historico   PRIMARY KEY                         (timestamp, cod_orgao, cod_organograma),
    CONSTRAINT fk_de_para_orgao_historico_1 FOREIGN KEY                         (cod_orgao)
                                            REFERENCES organograma.orgao        (cod_orgao),
    CONSTRAINT fk_de_para_orgao_historico_2 FOREIGN KEY                         (cod_organograma)
                                            REFERENCES organograma.organograma  (cod_organograma),
    CONSTRAINT fk_de_para_orgao_historico_3 FOREIGN KEY                         (cod_orgao_new)
                                            REFERENCES organograma.orgao        (cod_orgao)
);

GRANT ALL ON organograma.de_para_orgao_historico TO GROUP urbem;


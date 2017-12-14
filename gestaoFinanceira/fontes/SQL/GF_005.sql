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
* $Id: GF_004.sql 29950 2008-05-26 02:33:34Z melo $
*
* Versão 005.
*/


INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (2
          , 8
          , 1
          , 'Relatório SIOPS'
          , 'relatorioDespesasSIOPS.rptdesign');


--   -- Criação das tabelas empenho.tipo_vinculo, empenho.empenho_contrato, empenho.empenho_convenio
   CREATE TABLE empenho.tipo_vinculo (
     cod_tipo        integer        NOT NULL,
     descricao       varchar(40)    NOT NULL,
   CONSTRAINT pk_tipo_vinculo   PRIMARY KEY(cod_tipo));


   CREATE TABLE empenho.empenho_contrato (
     exercicio          char(4)     NOT NULL ,
     cod_entidade       integer     NOT NULL ,
     cod_empenho        integer     NOT NULL ,
     num_contrato       integer     NOT NULL ,
   CONSTRAINT pk_empenho_contrato   PRIMARY KEY(exercicio, cod_entidade, cod_empenho),
   CONSTRAINT fk_empenho_contrato_1 FOREIGN KEY(exercicio, cod_entidade, cod_empenho)  REFERENCES empenho.empenho(exercicio, cod_entidade, cod_empenho),
   CONSTRAINT fk_empenho_contrato_2 FOREIGN KEY(exercicio, cod_entidade, num_contrato) REFERENCES licitacao.contrato(exercicio, cod_entidade, num_contrato)
   );

   GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE empenho.tipo_vinculo     TO GROUP urbem;
   GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE empenho.empenho_contrato TO GROUP urbem;


   -- Criação da tabela empenho.empenho_convenio
   CREATE TABLE empenho.empenho_convenio (
     exercicio          char(04)    NOT NULL ,
     cod_entidade       integer     NOT NULL ,
     cod_empenho        integer     NOT NULL ,
     num_convenio       integer     NOT NULL   ,
   CONSTRAINT pk_empenho_convenio   PRIMARY KEY(exercicio, cod_entidade, cod_empenho),
   CONSTRAINT fk_empenho_convenio_1 FOREIGN KEY(exercicio, cod_entidade, cod_empenho) REFERENCES empenho.empenho(exercicio, cod_entidade, cod_empenho),
   CONSTRAINT fk_empenho_convenio_2 FOREIGN KEY(exercicio, num_convenio)              REFERENCES licitacao.convenio(exercicio, num_convenio));

   GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE empenho.empenho_convenio TO GROUP urbem;


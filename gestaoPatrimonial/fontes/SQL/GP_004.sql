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
* Versão 004.
*/

-- Criar estrutura para efetuar a rescisão de convênios:
   CREATE TABLE licitacao.rescisao_convenio (
     exercicio_convenio    CHAR(4)        NOT NULL,
     num_convenio          INTEGER        NOT NULL,
     exercicio             CHAR(4)        NOT NULL,
     num_rescisao          INTEGER        NOT NULL,
     responsavel_juridico  INTEGER        NOT NULL,
     dt_rescisao           DATE           NOT NULL,
     vlr_multa             NUMERIC(14,2)  NOT NULL DEFAULT 0,
     vlr_indenizacao       NUMERIC(14,2)  NOT NULL DEFAULT 0,
     motivo                TEXT           NOT NULL,
     CONSTRAINT pk_rescisao_convenio   PRIMARY KEY (exercicio_convenio, num_convenio),
     CONSTRAINT fk_rescisao_convenio_1 FOREIGN KEY (exercicio_convenio, num_convenio)   REFERENCES licitacao.convenio(exercicio, num_convenio),
     CONSTRAINT fk_rescisao_convenio_2 FOREIGN KEY (responsavel_juridico)       REFERENCES public.sw_cgm(numcgm),
     CONSTRAINT uk_rescisao_convenio_1 UNIQUE (exercicio, num_convenio)
   );


   CREATE TABLE licitacao.publicacao_rescisao_convenio (
     exercicio_convenio    CHAR(4)        NOT NULL,
     num_convenio          INTEGER        NOT NULL,
     cgm_imprensa          INTEGER        NOT NULL,
     dt_publicacao         DATE           NOT NULL,
     observacao            VARCHAR(100)   NOT NULL,
     CONSTRAINT pk_publicacao_rescisao_convenio   PRIMARY KEY (exercicio_convenio, num_convenio, cgm_imprensa, dt_publicacao),
     CONSTRAINT fk_publicacao_rescisao_convenio_1 FOREIGN KEY (exercicio_convenio, num_convenio)   REFERENCES licitacao.rescisao_convenio(exercicio_convenio, num_convenio),
     CONSTRAINT fk_publicacao_rescisao_convenio_2 FOREIGN KEY (cgm_imprensa)       REFERENCES public.sw_cgm(numcgm)
   );


   GRANT INSERT, DELETE, SELECT, UPDATE ON licitacao.rescisao_convenio              TO GROUP urbem;
   GRANT INSERT, DELETE, SELECT, UPDATE ON licitacao.publicacao_rescisao_convenio   TO GROUP urbem;

   INSERT INTO administracao.acao
               (cod_acao
             , cod_funcionalidade
             , nom_arquivo
             , parametro
             , ordem
             , complemento_acao
             , nom_acao)
        VALUES ( 2017
             , 331
             , 'FLManterConvenios.php'
             , 'rescindir'
             , 8
             , ''
             , 'Rescindir Convênio');


----------------
-- Ticket #12910
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , nom_acao)
     VALUES ( 2267
          , 279
          , 'FMManterConfiguracao.php'
          , 'alterar'
          , 0
          , 'Alterar Configuração'
          );

---------------
-- gp_atributos
---------------

CREATE TABLE almoxarifado.atributo_requisicao_item (
    exercicio        CHAR(4)       NOT NULL,
    cod_almoxarifado INTEGER       NOT NULL,
    cod_requisicao   INTEGER       NOT NULL,
    cod_item         INTEGER       NOT NULL,
    cod_marca        INTEGER       NOT NULL,
    cod_centro       INTEGER       NOT NULL,
    cod_sequencial   INTEGER       NOT NULL,
    quantidade       NUMERIC(14,4) NOT NULL,
    CONSTRAINT pk_atributo_requisicao_item PRIMARY KEY (exercicio, cod_almoxarifado, cod_requisicao, cod_item, cod_marca, cod_centro, cod_sequencial),
    CONSTRAINT fk_atributo_requisicao_item_1 FOREIGN KEY (exercicio, cod_almoxarifado, cod_requisicao, cod_item, cod_marca, cod_centro) REFERENCES almoxarifado.requisicao_item(exercicio, cod_almoxarifado, cod_requisicao, cod_item, cod_marca, cod_centro)
);

CREATE TABLE almoxarifado.atributo_requisicao_item_valor (
    exercicio        CHAR(4) NOT NULL,
    cod_almoxarifado INTEGER NOT NULL,
    cod_requisicao   INTEGER NOT NULL,
    cod_item         INTEGER NOT NULL,
    cod_marca        INTEGER NOT NULL,
    cod_centro       INTEGER NOT NULL,
    cod_sequencial   INTEGER NOT NULL,
    cod_modulo       INTEGER NOT NULL,
    cod_cadastro     INTEGER NOT NULL,
    cod_atributo     INTEGER NOT NULL,
    valor            TEXT    NOT NULL,
    CONSTRAINT pk_atributo_requisicao_item_valor PRIMARY KEY (exercicio, cod_almoxarifado, cod_requisicao, cod_item, cod_marca, cod_centro, cod_sequencial, cod_modulo, cod_cadastro, cod_atributo), 
    CONSTRAINT fk_atributo_requisicao_item_valor_1 FOREIGN KEY (cod_modulo, cod_cadastro, cod_atributo) REFERENCES administracao.atributo_dinamico(cod_modulo, cod_cadastro, cod_atributo),
    CONSTRAINT fk_atributo_requisicao_item_valor_2 FOREIGN KEY (exercicio, cod_almoxarifado, cod_requisicao, cod_item, cod_marca, cod_centro, cod_sequencial) REFERENCES almoxarifado.atributo_requisicao_item(exercicio, cod_almoxarifado, cod_requisicao, cod_item, cod_marca, cod_centro, cod_sequencial)
);

GRANT INSERT, DELETE, UPDATE, SELECT ON almoxarifado.atributo_requisicao_item TO GROUP urbem;
GRANT INSERT, DELETE, UPDATE, SELECT ON almoxarifado.atributo_requisicao_item_valor TO GROUP urbem;


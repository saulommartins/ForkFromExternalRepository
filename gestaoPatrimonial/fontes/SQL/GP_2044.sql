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
*
* Script de DDL e DML
*
* Versao 2.04.4
*
* Fabio Bertoldi - 20150923
*
*/

----------------
-- Ticket #23288
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3087
     , 342
     , 'FLManterContrato.php'
     , 'consultar'
     , 11
     , ''
     , 'Consultar Contrato'
     , TRUE
     );


----------------
-- Ticket #23340
----------------

DROP   TYPE depreciacao_automatica CASCADE;
CREATE TYPE depreciacao_automatica AS (
    cod_bem                           INTEGER,
    descricao                         VARCHAR,
    dt_incorporacao                   DATE   ,
    dt_aquisicao                      DATE   ,
    competencia_incorporacao          TEXT   ,
    vl_bem                            NUMERIC,
    quota_depreciacao_anual           NUMERIC,
    quota_depreciacao_anual_acelerada NUMERIC,
    depreciacao_acelerada             BOOLEAN,
    cod_plano                         INTEGER,
    cod_reavaliacao                   INTEGER,
    dt_reavaliacao                    DATE   ,
    vida_util                         INTEGER,
    motivo                            VARCHAR,
    exercicio_aquisicao               VARCHAR,
    mes_aquisicao                     INTEGER
);


----------------
-- Ticket #23374
----------------

UPDATE administracao.acao SET ordem = ordem + 4 WHERE cod_funcionalidade = 342;

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
     VALUES
     ( 3095
     , 342
     , 'FMManterTipoContrato.php'
     , 'incluir'
     , 1
     , ''
     , 'Incluir Tipo de Contrato'
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
     VALUES
     ( 3096
     , 342
     , 'FLManterTipoContrato.php'
     , 'alterar'
     , 2
     , ''
     , 'Alterar Tipo de Contrato'
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
     VALUES
     ( 3097
     , 342
     , 'FLManterTipoContrato.php'
     , 'excluir'
     , 3
     , ''
     , 'Excluir Tipo de Contrato'
     , TRUE
     );

ALTER TABLE licitacao.tipo_contrato ADD COLUMN tipo_tc INTEGER;
ALTER TABLE licitacao.tipo_contrato ADD COLUMN ativo   BOOLEAN NOT NULL DEFAULT TRUE;

----------------
-- Ticket #23417
----------------

INSERT
  INTO administracao.configuracao
     ( cod_modulo
     , exercicio
     , parametro
     , valor
     )
     VALUES
     ( 45
     , '2015'
     , 'tcmba_tipo_periodicidade_patrimonio'
     , ''
     );


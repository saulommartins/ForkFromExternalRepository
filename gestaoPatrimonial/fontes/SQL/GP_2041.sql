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
* Versao 2.04.1
*
* Fabio Bertoldi - 20150630
*
*/

----------------
-- Ticket #23005
----------------

INSERT
  INTO administracao.relatorio  
     (  cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo
     )
VALUES
     ( 3
     , 6
     , 24
     , 'Relatório de Reavaliação de Depreciação'
     , 'logReavaliacao.rptdesign'
     );


DROP FUNCTION patrimonio.fn_depreciacao_automatica(VARCHAR, VARCHAR, INTEGER, INTEGER, INTEGER, VARCHAR);

CREATE TYPE depreciacao_automatica AS (
      cod_bem                           INTEGER
    , descricao                         VARCHAR
    , dt_incorporacao                   DATE
    , dt_aquisicao                      DATE
    , competencia_incorporacao          TEXT
    , vl_bem                            NUMERIC
    , quota_depreciacao_anual           NUMERIC
    , quota_depreciacao_anual_acelerada NUMERIC
    , depreciacao_acelerada             BOOLEAN
    , cod_plano                         INTEGER
    , cod_reavaliacao                   INTEGER
    , dt_reavaliacao                    DATE
    , vida_util                         INTEGER
    , motivo                            VARCHAR
);

CREATE TYPE reavaliacao_depreciacao_automatica AS (
      cod_bem                           INTEGER
    , descricao                         VARCHAR
    , dt_incorporacao                   DATE
    , dt_aquisicao                      DATE
    , competencia_incorporacao          TEXT
    , vl_bem                            NUMERIC
    , quota_depreciacao_anual           NUMERIC
    , quota_depreciacao_anual_acelerada NUMERIC
    , depreciacao_acelerada             BOOLEAN
    , cod_plano                         INTEGER
    , cod_reavaliacao                   INTEGER
    , dt_reavaliacao                    DATE
    , vida_util                         INTEGER
    , motivo                            VARCHAR
);


----------------
-- Ticket #22988
----------------

ALTER TABLE compras.solicitacao ADD COLUMN registro_precos BOOLEAN NOT NULL DEFAULT FALSE;


----------------
-- Ticket #23125
----------------

INSERT INTO licitacao.tipo_contrato (cod_tipo, sigla, descricao) VALUES (45, 'TPO', 'Termo de Parceria/OSCIP'  );
INSERT INTO licitacao.tipo_contrato (cod_tipo, sigla, descricao) VALUES (46, 'OTP', 'Outros Termos de Parceria');


----------------
-- Ticket #
----------------

UPDATE compras.tipo_objeto SET descricao = 'Concessões' WHERE cod_tipo_objeto = 3;
INSERT INTO compras.tipo_objeto (cod_tipo_objeto, descricao) VALUES (5, 'Permissões');
INSERT INTO compras.tipo_objeto (cod_tipo_objeto, descricao) VALUES (6, 'Locações de Imóveis');


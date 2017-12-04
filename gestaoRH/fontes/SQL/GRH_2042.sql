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
* Versao 2.04.2
*
* Fabio Bertoldi - 20150819
*
*/

----------------
-- Ticket #23122
----------------

DROP FUNCTION resumoEmissaoAutorizacaoEmpenhoDiarias(VARCHAR,VARCHAR,VARCHAR,VARCHAR);

DROP   TYPE colunasResumoDiarias;
CREATE TYPE colunasResumoDiarias AS (
    orgao               VARCHAR,
    unidade             VARCHAR,
    red_dotacao         VARCHAR,
    rubrica_despesa     VARCHAR,
    descricao_despesa   VARCHAR,
    valor               NUMERIC,
    quantidade          NUMERIC,
    num_pao             VARCHAR,
    numcgm              VARCHAR,
    fornecedor          VARCHAR,
    periodo             VARCHAR,
    ato                 VARCHAR,
    cargo               VARCHAR,
    cod_diaria          INTEGER,
    cod_contrato        INTEGER,
    timestamp           VARCHAR,
    motivo_viagem       VARCHAR,
    cod_conta           INTEGER
);


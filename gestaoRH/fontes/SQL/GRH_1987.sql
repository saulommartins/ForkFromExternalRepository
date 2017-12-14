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
* $Id:$
*
* Versão 1.98.7
*/

----------------
-- Ticket #16336
----------------
DROP FUNCTION ultimo_contrato_servidor_padrao(character varying,integer);
DROP TYPE colunasUltimoContratoServidorPadrao;

CREATE TYPE colunasUltimoContratoServidorPadrao AS (
    cod_contrato    INTEGER,
    cod_padrao      INTEGER,
    valor           NUMERIC(14,2),
    descricao       VARCHAR
);


CREATE TYPE colunasUltimoContratoPensionistaPrevidencia AS (
    cod_contrato    INTEGER,
    cod_previdencia INTEGER
);

CREATE TYPE colunasUltimoContratoServidorCasoCausa AS (
    cod_contrato    INTEGER,
    cod_caso_causa  INTEGER,
    dt_rescisao     DATE
);

CREATE TYPE colunasUltimoServidorPisPasep AS (
    cod_servidor    INTEGER
);

CREATE TYPE colunasUltimoContratoServidorOcorrencia AS (
    cod_contrato    INTEGER,
    cod_ocorrencia  INTEGER
);

CREATE TYPE colunasUltimoContratoServidorFormaPagamento AS (
    cod_contrato         INTEGER,
    cod_forma_pagamento  INTEGER
);
CREATE TYPE colunasUltimoPrevidenciaPrevidencia AS (
    cod_previdencia  INTEGER,
    tipo_previdencia VARCHAR,
    descricao        VARCHAR
);

CREATE TYPE colunasUltimoContratoServidorPrevidencia AS (
    cod_contrato    INTEGER,
    cod_previdencia INTEGER,
    bo_excluido     BOOLEAN
);


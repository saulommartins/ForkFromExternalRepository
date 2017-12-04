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
--*
-- script de funcao PLSQL
-- 
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23095 $
-- $Name$
-- $Autor: MArcia $
-- Date: 2005/12/13 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: Retorna a situação da folha complementar informada
--
--/



CREATE OR REPLACE FUNCTION pega0UltimaComplementar(VARCHAR,INTEGER) RETURNS INTEGER as '

DECLARE
    stSituacao                  ALIAS FOR $1;
    inCodComplementar           INTEGER;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN

    
   
   inCodComplementar  := selectIntoInteger (''
        SELECT complementar_situacao.cod_complementar
          FROM folhapagamento''||stEntidade||''.complementar_situacao
             , (SELECT cod_periodo_movimentacao
                     , max(cod_complementar) as cod_complementar
                     , max(timestamp) as timestamp
                  FROM folhapagamento''||stEntidade||''.complementar_situacao
                GROUP BY cod_periodo_movimentacao) AS max_complementar_situacao
         WHERE complementar_situacao.cod_periodo_movimentacao = max_complementar_situacao.cod_periodo_movimentacao
           AND complementar_situacao.cod_complementar = max_complementar_situacao.cod_complementar
           AND complementar_situacao.timestamp   = max_complementar_situacao.timestamp
           AND complementar_situacao.situacao = ''||quote_literal(stSituacao)||''
           AND complementar_situacao.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao
                               );
    IF inCodComplementar IS NULL THEN
        inCodComplementar := 0;
    END IF;

    RETURN inCodComplementar;

END;
' LANGUAGE 'plpgsql';


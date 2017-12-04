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
-- $Autor: Marcia $
-- Date: 2005/10/04 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: Recebe o codigo do contrato e retorna o codigo do servidor.
--/
--


CREATE OR REPLACE FUNCTION pega0QuantidadeQuinqueniosMata(DATE,INTEGER) RETURNS integer as '

DECLARE
    dtLei                       ALIAS FOR $1;
    inCodContrato               ALIAS FOR $2;
    dtQuinquenio                DATE;
    dtCompetencia               DATE;
    inResultado                 INTEGER := 0;
stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN

    dtQuinquenio := selectIntoVarchar (''SELECT substr(atributo_contrato_servidor_valor.valor,7,4)||''''-''''||
                                     substr(atributo_contrato_servidor_valor.valor,4,2)||''''-''''||
                                     substr(atributo_contrato_servidor_valor.valor,1,2) as dt_quinquenio
                                FROM pessoal''||stEntidade||''.atributo_contrato_servidor_valor
                                   , (SELECT cod_contrato
                                           , cod_modulo
                                           , cod_cadastro
                                           , cod_atributo
                                           , max(timestamp) as timestamp
                                        FROM pessoal''||stEntidade||''.atributo_contrato_servidor_valor
                                      GROUP BY cod_contrato
                                             , cod_modulo
                                             , cod_cadastro
                                             , cod_atributo) as max_atributo_contrato_servidor_valor
                                WHERE atributo_contrato_servidor_valor.cod_contrato = max_atributo_contrato_servidor_valor.cod_contrato
                                  AND atributo_contrato_servidor_valor.cod_modulo   = max_atributo_contrato_servidor_valor.cod_modulo
                                  AND atributo_contrato_servidor_valor.cod_cadastro = max_atributo_contrato_servidor_valor.cod_cadastro
                                  AND atributo_contrato_servidor_valor.cod_atributo = max_atributo_contrato_servidor_valor.cod_atributo
                                  AND atributo_contrato_servidor_valor.timestamp    = max_atributo_contrato_servidor_valor.timestamp
                                  AND atributo_contrato_servidor_valor.cod_atributo = 6
                                  AND atributo_contrato_servidor_valor.cod_contrato = ''||inCodContrato);
    IF dtQuinquenio IS NOT NULL THEN    
        dtCompetencia := selectIntoVarchar(''SELECT dt_final
                                     FROM folhapagamento''||stEntidade||''.periodo_movimentacao 
                                 ORDER BY cod_periodo_movimentacao DESC 
                                    LIMIT 1'');
        IF dtQuinquenio < dtLei THEN
            SELECT INTO inResultado (SELECT TO_DATE(dtLei,''yyyy-mm-dd'')-TO_DATE(dtCompetencia,''yyyy-mm-dd''));
        ELSE
            SELECT INTO inResultado (SELECT TO_DATE(dtQuinquenio,''yyyy-mm-dd'')-TO_DATE(dtCompetencia,''yyyy-mm-dd''));        
        END IF;
        inResultado := inResultado/(365*5);
    END IF;
    RETURN inResultado;
END;
' LANGUAGE 'plpgsql';


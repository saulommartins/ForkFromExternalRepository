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
--
-- script de funcao PLSQL
--
-- URBEM Solugues de Gestco Pzblica Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23133 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2006/07/01 10:50:00 $
--
-- Caso de uso: uc-04.05.53
--
-- Objetivo: avalia o tipo de media indicado a partir dos dados temporarios
-- da geracao do registro de ferias
--
*/


CREATE OR REPLACE FUNCTION pegaFormulaMediaFerias(INTEGER,INTEGER,INTEGER,INTEGER,INTEGER) RETURNS VARCHAR AS '
DECLARE

    inCodEvento         ALIAS FOR $1;
    inCodConfiguracao   ALIAS FOR $2;
    inCodSubDivisao     ALIAS FOR $3;
    inCodCargo          ALIAS FOR $4;
    inCodEspecialidade  ALIAS FOR $5;
    stFormula           VARCHAR;
    stSql               VARCHAR;
    crCursor            REFCURSOR;
    stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
BEGIN

stSql := ''
        SELECT fptm.cod_modulo ||''''.''''|| fptm.cod_biblioteca ||''''.''''|| fptm.cod_funcao

          FROM folhapagamento''||stEntidade||''.configuracao_evento_caso_cargo         AS car

         JOIN folhapagamento''||stEntidade||''.configuracao_evento_caso               AS cas
           ON  cas.cod_caso         = car.cod_caso
           AND cas.cod_evento       = car.cod_evento
           AND cas.timestamp        = car.timestamp
           AND cas.cod_configuracao = car.cod_configuracao

         JOIN ( SELECT cod_evento
                       , max(timestamp) AS timestamp
                    FROM folhapagamento''||stEntidade||''.configuracao_evento_caso
                GROUP BY cod_evento)                                 AS max_cas
            ON cas.cod_evento       = max_cas.cod_evento
           AND cas.timestamp        = max_cas.timestamp
           AND cas.cod_evento       = max_cas.cod_evento
           AND cas.timestamp        = max_cas.timestamp

         JOIN  folhapagamento''||stEntidade||''.configuracao_evento_caso_sub_divisao   AS sub
           ON  sub.cod_caso         = cas.cod_caso
           AND sub.cod_evento       = cas.cod_evento
           AND sub.timestamp        = cas.timestamp
           AND sub.cod_configuracao = cas.cod_configuracao

         JOIN folhapagamento''||stEntidade||''.tipo_evento_configuracao_media          AS fptecm
            ON cas.cod_caso         = fptecm.cod_caso
           AND cas.cod_evento       = fptecm.cod_evento
           AND cas.timestamp        = fptecm.timestamp
           AND cas.cod_configuracao = fptecm.cod_configuracao

         JOIN folhapagamento''||stEntidade||''.tipo_media                              AS fptm
            ON fptm.cod_tipo         = fptecm.cod_tipo

     LEFT JOIN folhapagamento''||stEntidade||''.configuracao_evento_caso_especialidade AS esp
            ON car.cod_caso         = esp.cod_caso
           AND car.cod_evento       = esp.cod_evento
           AND car.timestamp        = esp.timestamp
           AND car.cod_configuracao = esp.cod_configuracao
           AND car.cod_cargo        = esp.cod_cargo
           AND esp.cod_especialidade= ''||inCodEspecialidade||''

         WHERE car.cod_evento       = ''||inCodEvento||''
           AND car.cod_configuracao = ''||inCodConfiguracao||''
           AND sub.cod_sub_divisao  = ''||inCodSubDivisao||''
           AND car.cod_cargo        = ''||inCodCargo||''
        '';





    EXECUTE stSql;

    OPEN crCursor FOR EXECUTE stSql;
         FETCH crCursor INTO stFormula ;
    CLOSE crCursor;

    RETURN stFormula;
END;
' LANGUAGE 'plpgsql';


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
CREATE OR REPLACE FUNCTION fazerlancamento ( character varying, character varying, integer, character varying, numeric, character varying, integer, character varying, integer, integer, integer ) RETURNS INTEGER AS $$
DECLARE 
    reRecord             RECORD;
    stCodPlanoDeb        ALIAS FOR $1;
    stCodPlanoCred       ALIAS FOR $2;
    inCodHistorico       ALIAS FOR $3;
    stExercicio          ALIAS FOR $4;
    nuValor              ALIAS FOR $5;
    stComplemento        ALIAS FOR $6;
    inCodLote            ALIAS FOR $7;
    stTipo               ALIAS FOR $8;
    inCodEntidade        ALIAS FOR $9;
    inCodPlanoDeb        ALIAS FOR $10;
    inCodPlanoCred       ALIAS FOR $11;
    inOut                INTEGER;
    stCodPlanoDebRed     VARCHAR := '';
    stCodPlanoCredRed    VARCHAR := '';
    stMascara            VARCHAR := '';
    stSql                VARCHAR := '';
    inPosicao            INTEGER := 1;
                                    
BEGIN
    stSql := '
        SELECT  trim(mascara) as mascara
        FROM    contabilidade.posicao_plano
        WHERE   exercicio = '|| quote_literal(stExercicio) || '
        ORDER BY cod_posicaO
    ';

    FOR reRecord IN EXECUTE stSql LOOP
        stCodPlanoCredRed := stCodPlanoCredRed || '.' || sw_fn_mascara_dinamica(reRecord.mascara, substr(stCodPlanoCred, inPosicao,length(reRecord.mascara)) );

        stCodPlanoDebRed := stCodPlanoDebRed || '.' || sw_fn_mascara_dinamica(reRecord.mascara, substr(stCodPlanoDeb, inPosicao,length(reRecord.mascara)) );
        inPosicao := inPosicao + length(reRecord.mascara);
    END LOOP;

    IF stCodPlanoDeb ~ '[.]' THEN
        stCodPlanoDebRed := stCodPlanoDeb;
    ELSE
        stCodPlanoDebRed := substr(stCodPlanoDebRed,2,length(stCodPlanoDebRed));
    END IF;
    IF stCodPlanoCred ~ '[.]' THEN
        stCodPlanoCredRed := stCodPlanoCred;
    ELSE
        stCodPlanoCredRed := substr(stCodPlanoCredRed,2,length(stCodPlanoCredRed));
    END IF;

    inOut := contabilidade.fn_insere_lancamentos(stExercicio, inCodPlanoDeb, inCodPlanoCred, '', '', nuValor, inCodLote, inCodEntidade, inCodHistorico, stTipo, stComplemento);
    return inOut;

END;
$$ language 'plpgsql';

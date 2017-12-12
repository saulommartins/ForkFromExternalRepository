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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.02.04
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION FazerLancamento(VARCHAR,VARCHAR,INTEGER,VARCHAR,NUMERIC,VARCHAR,INTEGER,VARCHAR,INTEGER) RETURNS INTEGER AS $$
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
        WHERE   exercicio = '|| quote_literal(stExercicio) ||'
        ORDER BY cod_posicao
    ';

    FOR reRecord IN EXECUTE stSql LOOP
        stCodPlanoCredRed := stCodPlanoCredRed || '.' || sw_fn_mascara_dinamica(reRecord.mascara, substr(stCodPlanoCred, inPosicao,length(reRecord.mascara)) );
        stCodPlanoDebRed  := stCodPlanoDebRed  || '.' || sw_fn_mascara_dinamica(reRecord.mascara, substr(stCodPlanoDeb , inPosicao,length(reRecord.mascara)) );
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

    inOut := contabilidade.fn_insere_lancamentos(stExercicio,0,0, stCodPlanoDebRed, stCodPlanoCredRed, nuValor, inCodLote, inCodEntidade, inCodHistorico, stTipo, stComplemento);
    return inOut;
END;
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION contabilidade.FazerLancamento(VARCHAR,VARCHAR,INTEGER,VARCHAR,NUMERIC,VARCHAR,INTEGER,VARCHAR,INTEGER) RETURNS INTEGER AS $$
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
        WHERE   exercicio = ''|| quote_literal(stExercicio) || ''
        ORDER BY cod_posicao
    ';
    FOR reRecord IN EXECUTE stSql LOOP
        stCodPlanoCredRed := stCodPlanoCredRed || quote_literal('.') || sw_fn_mascara_dinamica(reRecord.mascara, substr(stCodPlanoCred, inPosicao,length(reRecord.mascara)) );
        stCodPlanoDebRed  := stCodPlanoDebRed  || quote_literal('.') || sw_fn_mascara_dinamica(reRecord.mascara, substr(stCodPlanoDeb , inPosicao,length(reRecord.mascara)) );
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

    inOut := contabilidade.fn_insere_lancamentos(stExercicio, 0, 0, stCodPlanoDebRed, stCodPlanoCredRed, nuValor, inCodLote, inCodEntidade, inCodHistorico, stTipo, stComplemento);
    return inOut;
END;
$$ LANGUAGE 'plpgsql';

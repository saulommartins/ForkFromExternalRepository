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

CREATE OR REPLACE FUNCTION contabilidade.fn_insere_lancamentos(VARCHAR,INTEGER,INTEGER,VARCHAR,VARCHAR,NUMERIC,INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR) RETURNS INTEGER AS $$
DECLARE
    reRecord            RECORD;
    PstExercicio         ALIAS FOR $1;
    PinCodPlanoDeb       ALIAS FOR $2;
    PinCodPlanoCred      ALIAS FOR $3;
    PstCodEstruturalDeb  ALIAS FOR $4;
    PstCodEstruturalCred ALIAS FOR $5;
    PnuValor             ALIAS FOR $6;
    PinCodLote           ALIAS FOR $7;
    PinCodEntidade       ALIAS FOR $8;
    PinCodHistorico      ALIAS FOR $9;
    PstTipo              ALIAS FOR $10;
    PstComplemento       ALIAS FOR $11;
    inCodPlanoDeb        INTEGER := 0;
    inCodPlanoCred       INTEGER := 0;
    inSequencia          INTEGER := 0;
    chTipo               CHAR    := '';
    chTipoValor          CHAR    := '';
    stFiltro             VARCHAR := '';
    --vAux                 Varchar;
BEGIN
    --Caso tenha informado uma string com mais de 1 caracter trunca
    chTipo      := substr(trim(PstTipo),1,1);
    --Recupera o cod_plano a partir do cod_estrutural
    IF PinCodPlanoCred = 0 AND Btrim(PstCodEstruturalCred) <> '' THEN
        SELECT INTO
            inCodPlanoCred
            cod_plano
        FROM
            contabilidade.plano_conta       as pc
            ,contabilidade.plano_analitica  as pa
        WHERE
            pc.exercicio        = pa.exercicio
        AND pc.cod_conta        = pa.cod_conta
        AND pc.exercicio        = PstExercicio
        AND pc.cod_estrutural   = BTrim(PstCodEstruturalCred);
        IF inCodPlanoCred IS NULL THEN
            RAISE EXCEPTION 'Conta de crédito ( % ) não é analítica ou não está cadastrada no plano de contas.',PstCodEstruturalCred;
        END IF;

    ELSE
        --Seta o cod_plano a partir do parâmetro informado
        IF PinCodPlanoCred <> 0 AND Btrim(PstCodEstruturalCred) = '' THEN
            inCodPlanoCred := PinCodPlanoCred;
        END IF;
    END IF;

    --Recupera o cod_plano a partir do cod_estrutural
    IF PinCodPlanoDeb = 0 AND Btrim(PstCodEstruturalDeb) <> '' THEN

        SELECT INTO
            inCodPlanoDeb
            cod_plano
        FROM
            contabilidade.plano_conta       as pc
            ,contabilidade.plano_analitica  as pa
        WHERE
            pc.exercicio        = pa.exercicio
        AND pc.cod_conta        = pa.cod_conta
        AND pc.exercicio        = PstExercicio
        AND pc.cod_estrutural   = BTrim(PstCodEstruturalDeb) ;
        IF inCodPlanoDeb IS NULL THEN
            RAISE EXCEPTION 'Conta de débito ( % ) não é analítica ou não está cadastrada no plano de contas.',PstCodEstruturalDeb;
        END IF;
    ELSE
        --Seta o cod_plano a partir do parâmetro informado
        IF PinCodPlanoDeb <> 0 AND Btrim(PstCodEstruturalDeb) = '' THEN
            inCodPlanoDeb := PinCodPlanoDeb;
        END IF;
    END IF;

    stFiltro    := 'WHERE exercicio = '                 || quote_literal(PstExercicio);
    stFiltro    := stFiltro || ' AND tipo = '           || quote_literal(chTipo);
    stFiltro    := stFiltro || ' AND cod_entidade = '   || PinCodEntidade;
    stFiltro    := stFiltro || ' AND cod_lote = '       || PinCodLote;
    inSequencia := publico.fn_proximo_cod('sequencia','contabilidade.lancamento',stFiltro);

    INSERT INTO contabilidade.lancamento
        (sequencia,exercicio,tipo,cod_lote,cod_entidade,cod_historico,complemento)
    VALUES
        (inSequencia,PstExercicio,chTipo,PinCodLote,PinCodEntidade,PinCodHistorico,PstComplemento)
    ;

    --CONTRA_PARTIDA
    IF inCodPlanoDeb <> 0 AND inCodPlanoCred <> 0 THEN
        --Insere dados de Crédito
        INSERT INTO contabilidade.valor_lancamento
            (sequencia,exercicio,tipo,cod_lote,cod_entidade,tipo_valor,vl_lancamento)
        VALUES
            (inSequencia,PstExercicio,chTipo,PinCodLote,PinCodEntidade,'C', (PnuValor * -1) )
        ;
        INSERT INTO contabilidade.conta_credito
            (sequencia,exercicio,tipo,cod_lote,cod_entidade,tipo_valor,cod_plano )
        VALUES
            (inSequencia,PstExercicio,chTipo,PinCodLote,PinCodEntidade,'C', inCodPlanoCred )
        ;
        --Insere dados de Débito
        INSERT INTO contabilidade.valor_lancamento
            (sequencia,exercicio,tipo,cod_lote,cod_entidade,tipo_valor,vl_lancamento)
        VALUES
            (inSequencia,PstExercicio,chTipo,PinCodLote,PinCodEntidade,'D', (PnuValor) )
        ;
        INSERT INTO contabilidade.conta_debito
            (sequencia,exercicio,tipo,cod_lote,cod_entidade,tipo_valor,cod_plano )
        VALUES
            (inSequencia,PstExercicio,chTipo,PinCodLote,PinCodEntidade,'D', inCodPlanoDeb )
        ;
    ELSE
        --CONTA_SIMPLES
        IF inCodPlanoDeb <> 0 OR inCodPlanoCred <> 0 THEN
            IF inCodPlanoDeb <> 0 THEN
                --Insere dados de Débito
                INSERT INTO contabilidade.valor_lancamento
                    (sequencia,exercicio,tipo,cod_lote,cod_entidade,tipo_valor,vl_lancamento)
                VALUES
                    (inSequencia,PstExercicio,chTipo,PinCodLote,PinCodEntidade,'D', (PnuValor) )
                ;
                INSERT INTO contabilidade.conta_debito
                    (sequencia,exercicio,tipo,cod_lote,cod_entidade,tipo_valor,cod_plano )
                VALUES
                    (inSequencia,PstExercicio,chTipo,PinCodLote,PinCodEntidade,'D', inCodPlanoDeb )
                ;
            ELSE
                --Insere dados de Débito
                INSERT INTO contabilidade.valor_lancamento
                    (sequencia,exercicio,tipo,cod_lote,cod_entidade,tipo_valor,vl_lancamento)
                VALUES
                    (inSequencia,PstExercicio,chTipo,PinCodLote,PinCodEntidade,'C', (PnuValor * -1) )
                ;
                INSERT INTO contabilidade.conta_credito
                    (sequencia,exercicio,tipo,cod_lote,cod_entidade,tipo_valor,cod_plano )
                VALUES
                    (inSequencia,PstExercicio,chTipo,PinCodLote,PinCodEntidade,'C', inCodPlanoCred )
                ;
            END IF;
        ELSE
            RAISE EXCEPTION 'Deve ser informada pelo menos uma conta de débido ou crédito.';
        END IF;
    END IF;

    RETURN inSequencia;
END;
$$ LANGUAGE 'plpgsql';

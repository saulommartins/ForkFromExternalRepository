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
CREATE OR REPLACE FUNCTION tcemg.fn_relatorio_demostrativo_rcl_despesa_liquidada(stDtInicial VARCHAR, stDtFinal VARCHAR, stEntidades VARCHAR, stCondicao VARCHAR) RETURNS NUMERIC(14,2) AS 
$$

DECLARE 
    stSQL                       VARCHAR;
    reRegistro                  RECORD;
    nuVlAno                     NUMERIC(14,2);
    nuTotal                     NUMERIC(14,2);
    inCount                     INTEGER;
    stExercicioInicial          VARCHAR;
    stExercicioFinal            VARCHAR;
    crCursor                    REFCURSOR;
    boExerciciosDiferentes      BOOLEAN;
BEGIN 

    stExercicioInicial := substring(stDtInicial, 7, 4);
    stExercicioFinal   := substring(stDtFinal, 7, 4);
    nuTotal := 0.00;


    IF (stExercicioInicial != stExercicioFinal) THEN
        inCount := 2;
        boExerciciosDiferentes := TRUE;
    ELSE
        inCount := 1;
    END IF;

    FOR i IN 1..inCount LOOP

        stSql := '
        SELECT ( 
            ( coalesce (( 
            select sum(nota_liquidacao_item.vl_total)
            from
                orcamento.despesa ,
                orcamento.conta_despesa
            JOIN empenho.pre_empenho_despesa 
            ON (    conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                and conta_despesa.exercicio = pre_empenho_despesa.exercicio )
            JOIN empenho.pre_empenho
            ON (    pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                and pre_empenho_despesa.exercicio       = pre_empenho.exercicio )
            JOIN empenho.empenho
            ON (    pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                and pre_empenho.exercicio       = empenho.exercicio )
            JOIN empenho.nota_liquidacao
            ON (    empenho.exercicio    = nota_liquidacao.exercicio_empenho
                and empenho.cod_entidade = nota_liquidacao.cod_entidade
                and empenho.cod_empenho  = nota_liquidacao.cod_empenho )
            JOIN empenho.nota_liquidacao_item
            ON (    nota_liquidacao.exercicio    = nota_liquidacao_item.exercicio
                and nota_liquidacao.cod_entidade = nota_liquidacao_item.cod_entidade
                and nota_liquidacao.cod_nota     = nota_liquidacao_item.cod_nota )
            WHERE
                    despesa.cod_despesa     = pre_empenho_despesa.cod_despesa
                AND despesa.exercicio       = pre_empenho_despesa.exercicio ';

        IF (boExerciciosDiferentes = TRUE) THEN
            IF (i = 1) THEN
                stSql := stSql || '
                AND conta_despesa.exercicio = ''' || stExercicioInicial ||'''
                AND nota_liquidacao.dt_liquidacao between to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') and to_date(''31/12/'' || ''' || stExercicioInicial || ''', ''dd/mm/yyyy'') ';
            ELSE
                stSql := stSql || '
                AND conta_despesa.exercicio = ''' || stExercicioFinal ||'''
                AND nota_liquidacao.dt_liquidacao between to_date(''01/01/'' || ''' || stExercicioFinal || ''',''dd/mm/yyyy'') and to_date(''' || stDtFinal || ''', ''dd/mm/yyyy'') ';
            END IF;
        ELSE
                stSql := stSql || '
                AND conta_despesa.exercicio = ''' || stExercicioInicial ||'''
                AND nota_liquidacao.dt_liquidacao between to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') and to_date(''' || stDtFinal || ''', ''dd/mm/yyyy'') ';
        END IF;
            stSql := stSql || '

                and ' || stCondicao || ' 
                and empenho.cod_entidade IN (' || stEntidades || ') 
            ) , 0.00 ) 
            )
    
        -
    
            ( coalesce ((
            select
            sum(nota_liquidacao_item_anulado.vl_anulado)
            from
                orcamento.despesa ,
                orcamento.conta_despesa
            JOIN empenho.pre_empenho_despesa 
            ON (    conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                and conta_despesa.exercicio = pre_empenho_despesa.exercicio )
            JOIN empenho.pre_empenho
            ON (    pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                and pre_empenho_despesa.exercicio       = pre_empenho.exercicio )
            JOIN empenho.empenho
            ON (    pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                and pre_empenho.exercicio       = empenho.exercicio )
            JOIN empenho.nota_liquidacao
            ON (    empenho.exercicio    = nota_liquidacao.exercicio_empenho
                and empenho.cod_entidade = nota_liquidacao.cod_entidade
                and empenho.cod_empenho  = nota_liquidacao.cod_empenho )
            JOIN empenho.nota_liquidacao_item
            ON (    nota_liquidacao.exercicio    = nota_liquidacao_item.exercicio
                and nota_liquidacao.cod_entidade = nota_liquidacao_item.cod_entidade
                and nota_liquidacao.cod_nota     = nota_liquidacao_item.cod_nota )
            JOIN empenho.nota_liquidacao_item_anulado
            ON (    nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                and nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                and nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                and nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                and nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
                and nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota )
            WHERE
                    despesa.cod_despesa     = pre_empenho_despesa.cod_despesa
                AND despesa.exercicio       = pre_empenho_despesa.exercicio ';

        IF (boExerciciosDiferentes = TRUE) THEN
            IF (i = 1) THEN
                stSql := stSql || '
                AND conta_despesa.exercicio = ''' || stExercicioInicial ||'''
                AND to_date(to_char(nota_liquidacao_item_anulado.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') between to_date(''' || stDtInicial || ''', ''dd/mm/yyyy'') and to_date(''31/12/'' || ''' || stExercicioInicial || ''', ''dd/mm/yyyy'') ';
            ELSE
                stSql := stSql || '
                AND conta_despesa.exercicio = ''' || stExercicioFinal ||'''
                AND to_date(to_char(nota_liquidacao_item_anulado.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') between to_date(''01/01/'' || ''' || stExercicioFinal || ''', ''dd/mm/yyyy'') and to_date(''' || stDtFinal || ''', ''dd/mm/yyyy'') ';
            END IF;
        ELSE
                stSql := stSql || '
                AND conta_despesa.exercicio = ''' || stExercicioInicial ||'''
                AND to_date(to_char(nota_liquidacao_item_anulado.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') between to_date(''' || stDtInicial || ''', ''dd/mm/yyyy'') and to_date(''' || stDtFinal || ''', ''dd/mm/yyyy'') ';
        END IF;

            stSql := stSql || '
                and ' || stCondicao || ' 
                and empenho.cod_entidade IN (' || stEntidades || ') 
            ) , 0.00 ) )
        ) as valor_total 
    ';



    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO nuVlAno;
    CLOSE crCursor;

    nuTotal := nuTotal + nuVlAno;

    END LOOP;

        
    RETURN nuTotal;

END;

$$ LANGUAGE 'plpgsql';

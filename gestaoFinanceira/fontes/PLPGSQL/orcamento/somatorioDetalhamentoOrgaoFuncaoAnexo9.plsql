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
* Casos de uso: uc-02.01.16
*/

CREATE OR REPLACE FUNCTION orcamento.fn_somatorio_detalhamento_orgao_funcao(varchar,varchar,varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stSituacao          ALIAS FOR $3;
    stCodEntidades      ALIAS FOR $4;
    stTipoRelatorio     ALIAS FOR $5;
    stDataInicial       ALIAS FOR $6;
    stDataFinal         ALIAS FOR $7;
    stAuxE              VARCHAR   := '';
    stAuxEA             VARCHAR   := '';
    stAuxP              VARCHAR   := '';
    stAuxPE             VARCHAR   := '';
    stAuxL              VARCHAR   := '';
    stAuxLA             VARCHAR   := '';
    stTabela1           VARCHAR   := '';
    stTabela2           VARCHAR   := '';
    stSql               VARCHAR   := '';
    reRegistro          RECORD;
    reRegistro2         RECORD;
    arDotacao           VARCHAR[];
    arMascDespesa       VARCHAR[];

    inNivel             INTEGER := 0;
    inPosicao           INTEGER;
    stOrgao             VARCHAR;
    stCampos            VARCHAR := '';
    stExecute           VARCHAR := '';
    nuFuncao            NUMERIC(14,2);
    nuTotOrgao          NUMERIC(14,2);


BEGIN

IF (stDataInicial = stDataFinal ) THEN
    stAuxE  := ' AND TO_DATE(TO_CHAR(e.dt_empenho, '|| quote_literal('dd/mm/yyyy') || ') ,' || quote_literal('dd/mm/yyyy') || ' = TO_DATE(' || quote_literal(stDataInicial) || ', ' || quote_literal('dd/mm/yyyy') || ')  '; 
    stAuxEA := ' AND TO_DATE(TO_CHAR(EEAI.timestamp,' || quote_literal('dd/mm/yyyy') || '), ' || quote_literal('dd/mm/yyyy') || ' = TO_DATE(' || quote_literal(stDataInicial) || ', ' || quote_literal('dd/mm/yyyy') || ') ';
    stAuxP  := ' AND TO_DATE(TO_CHAR(ENLP.timestamp,' || quote_literal('dd/mm/yyyy') || '),' || quote_literal('dd/mm/yyyy') || ' = TO_DATE(' || quote_literal(stDataInicial) || ', ' || quote_literal('dd/mm/yyyy') || ') ';
    stAuxPE := ' AND TO_DATE(TO_CHAR(ENLPA.timestamp_anulada,' || quote_literal('dd/mm/yyyy') || ') ,' || quote_literal('dd/mm/yyyy')  || ' = TO_DATE(' || quote_literal(stDataInicial) || ' , ' || quote_literal('dd/mm/yyyy') ||  ') ';
    stAuxL  := ' AND TO_DATE(TO_CHAR(nl.dt_liquidacao,'|| quote_literal('dd/mm/yyyy') || '),' || quote_literal('dd/mm/yyyy') || ') = TO_DATE(' || quote_literal(stDataInicial) || ',' || quote_literal('dd/mm/yyyy') || ') ';
    stAuxLA := ' AND TO_DATE(TO_CHAR(ENLIA.timestamp,'|| quote_literal('dd/mm/yyyy') ||'),' || quote_literal('dd/mm/yyyy') || ') = TO_DATE(' ||  quote_literal(stDataInicial) || ',' || quote_literal('dd/mm/yyyy') ||') ';
ELSE
    stAuxE  := ' AND TO_DATE(TO_CHAR(e.dt_empenho,'|| quote_literal('dd/mm/yyyy') || '),' || quote_literal('dd/mm/yyyy') ||') BETWEEN TO_DATE(' || quote_literal(stDataInicial) || ',' || quote_literal('dd/mm/yyyy') || ')  AND TO_DATE(' || quote_literal(stDataFinal) || ',' || quote_literal('dd/mm/yyyy') || ') ';
    stAuxEA := ' AND TO_DATE(TO_CHAR(EEAI.timestamp,'|| quote_literal('dd/mm/yyyy') || '),' || quote_literal('dd/mm/yyyy') || ') BETWEEN TO_DATE(' || quote_literal(stDataInicial) || ',' || quote_literal('dd/mm/yyyy') || ')  AND TO_DATE(' || quote_literal(stDataFinal) || ',' || quote_literal('dd/mm/yyyy') || ') ';
    stAuxP  := ' AND TO_DATE(TO_CHAR(ENLP.timestamp,'|| quote_literal('dd/mm/yyyy') || '),'|| quote_literal('dd/mm/yyyy') || ') BETWEEN TO_DATE(' || quote_literal(stDataInicial) || ',' || quote_literal('dd/mm/yyyy') || ')  AND TO_DATE(' || quote_literal(stDataFinal) || ','|| quote_literal('dd/mm/yyyy') || ') ';
    stAuxPE := ' AND TO_DATE(TO_CHAR(ENLPA.timestamp_anulada,'|| quote_literal('dd/mm/yyyy') || '),'|| quote_literal('dd/mm/yyyy') || ') BETWEEN TO_DATE(' || quote_literal(stDataInicial) || ',' || quote_literal('dd/mm/yyyy') || ')  AND TO_DATE(' || quote_literal(stDataFinal) || ','|| quote_literal('dd/mm/yyyy') || ') ';
    stAuxL  := ' AND TO_DATE(TO_CHAR(nl.dt_liquidacao,'|| quote_literal('dd/mm/yyyy') || '),'|| quote_literal('dd/mm/yyyy') || ') BETWEEN TO_DATE(' || quote_literal(stDataInicial) || ','|| quote_literal('dd/mm/yyyy') || ')  AND TO_DATE(' || quote_literal(stDataFinal) || ',' || quote_literal('dd/mm/yyyy') || ') ';
    stAuxLA := ' AND TO_DATE(TO_CHAR(ENLIA.timestamp,' || quote_literal('dd/mm/yyyy') || '),'|| quote_literal('dd/mm/yyyy') || ') BETWEEN TO_DATE(' || quote_literal(stDataInicial) || ','|| quote_literal('dd/mm/yyyy') || ')  AND TO_DATE(' || quote_literal(stDataFinal) || ',' || quote_literal('dd/mm/yyyy') || ') ';
END IF;


IF ( stSituacao = 'empenhados' ) THEN
    stSql := 'CREATE TEMPORARY TABLE tmp_empenhado AS (
       SELECT
            coalesce(sum(ipe.vl_total),0.00) as valor,
            od.num_orgao,
            od.cod_funcao
        FROM
            orcamento.despesa           as od,
            orcamento.conta_despesa     as cd,
            empenho.pre_empenho_despesa as ped,
            empenho.empenho             as e,
            empenho.pre_empenho         as pe,
            empenho.item_pre_empenho    as ipe
        WHERE
                cd.cod_conta               = ped.cod_conta
            AND cd.exercicio               = ped.exercicio

            And od.cod_despesa             = ped.cod_despesa
            AND od.exercicio               = ped.exercicio

            And pe.exercicio               = ped.exercicio
            And pe.cod_pre_empenho         = ped.cod_pre_empenho

            And e.cod_entidade             IN (' || stCodEntidades || ')
            And e.exercicio                = ' || quote_literal(stExercicio) || '

            AND e.exercicio                = pe.exercicio
            AND e.cod_pre_empenho          = pe.cod_pre_empenho

            AND pe.exercicio               = ipe.exercicio
            AND pe.cod_pre_empenho         = ipe.cod_pre_empenho
            ' || stAuxE || '
        GROUP BY
            od.num_orgao,
            od.cod_funcao
        ORDER BY
            od.num_orgao,
            od.cod_funcao';

          stSql := stSql || ' ) ';

        EXECUTE stSql;

        stSql := 'CREATE TEMPORARY TABLE tmp_anulado AS (
            SELECT
                sum(EEAI.vl_anulado) as valor,
                OD.num_orgao,
                OD.cod_funcao

               from orcamento.despesa            as OD,
                    orcamento.conta_despesa      as OCD,
                    empenho.pre_empenho_despesa  as EPED,
                    empenho.pre_empenho          as EPE,
                    empenho.item_pre_empenho     as EIPE,
                    empenho.empenho_anulado_item as EEAI

               Where
                     OCD.cod_conta            = EPED.cod_conta
                 AND OCD.exercicio            = EPED.exercicio
                 And EPED.exercicio           = EPE.exercicio
                 And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
                 And EPE.exercicio            = EIPE.exercicio
                 And EPE.cod_pre_empenho      = EIPE.cod_pre_empenho
                 And EIPE.exercicio           = EEAI.exercicio
                 And EIPE.cod_pre_empenho     = EEAI.cod_pre_empenho
                 And EIPE.num_item            = EEAI.num_item
                 And EEAI.exercicio           = '|| quote_literal(stExercicio) ||'
                 And EEAI.cod_entidade        IN ('||stCodEntidades||')
                 And OD.cod_despesa           = EPED.cod_despesa
                 AND OD.exercicio             = EPED.exercicio
                 ' || stAuxEA || '
               GROUP BY
                    OD.num_orgao,
                    OD.cod_funcao
               ORDER BY
                    OD.num_orgao,
                    OD.cod_funcao';

              stSql := stSql || ')';
        EXECUTE stSql;
  END IF;

 IF ( stSituacao = 'pagos' ) THEN
        stSql := 'CREATE TEMPORARY TABLE tmp_pago AS (
        SELECT
            sum(ENLP.vl_pago) as valor,
            OD.num_orgao,
            OD.cod_funcao

        FROM
            orcamento.despesa               as OD,
            orcamento.conta_despesa         as OCD,
            empenho.pre_empenho_despesa     as EPED,
            empenho.empenho                 as EE,
            empenho.pre_empenho             as EPE,
            empenho.nota_liquidacao         as ENL,
            empenho.nota_liquidacao_paga    as ENLP

        WHERE
                OCD.cod_conta            = EPED.cod_conta
            AND OCD.exercicio            = EPED.exercicio

            AND OD.cod_despesa           = EPED.cod_despesa
            AND OD.exercicio             = EPED.exercicio

            And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
            And EPED.exercicio           = EPE.exercicio

            And EPE.exercicio            = EE.exercicio
            And EPE.cod_pre_empenho      = EE.cod_pre_empenho

            And EE.exercicio             = '|| quote_literal(stExercicio) ||'
            And EE.cod_entidade          IN ('||stCodEntidades||')

            And EE.cod_empenho           = ENL.cod_empenho
            And EE.exercicio             = ENL.exercicio_empenho
            And EE.cod_entidade          = ENL.cod_entidade

            And ENL.cod_nota             = ENLP.cod_nota
            And ENL.cod_entidade         = ENLP.cod_entidade
            And ENL.exercicio            = ENLP.exercicio 
            ' || stAuxP || '
        GROUP BY
            OD.num_orgao,
            OD.cod_funcao
        ORDER BY
            OD.num_orgao,
            OD.cod_funcao';

        stSql := stSql || ')';
        EXECUTE stSql;

        stSql := 'CREATE TEMPORARY TABLE tmp_estornado AS (
        SELECT
            sum(ENLPA.vl_anulado) as valor,
            OD.num_orgao,
            OD.cod_funcao

        FROM
            orcamento.despesa                    as OD,
            orcamento.conta_despesa              as OCD,
            empenho.pre_empenho_despesa          as EPED,
            empenho.empenho                      as EE,
            empenho.pre_empenho                  as EPE,
            empenho.nota_liquidacao              as ENL,
            empenho.nota_liquidacao_paga         as ENLP,
            empenho.nota_liquidacao_paga_anulada as ENLPA
        WHERE
                OCD.cod_conta            = EPED.cod_conta
            AND OCD.exercicio            = EPED.exercicio
            And OD.cod_despesa           = EPED.cod_despesa
            AND OD.exercicio             = EPED.exercicio';

            stSql := stSql || '
            And EPED.exercicio           = EPE.exercicio
            And EPED.cod_pre_empenho     = EPE.cod_pre_empenho

            And EPE.exercicio            = EE.exercicio
            And EPE.cod_pre_empenho      = EE.cod_pre_empenho

            And EE.cod_entidade          IN ('||stCodEntidades||')
            And EE.exercicio             = '|| quote_literal(stExercicio) ||'

            And EE.cod_empenho           = ENL.cod_empenho
            And EE.exercicio             = ENL.exercicio_empenho
            And EE.cod_entidade          = ENL.cod_entidade

            And ENL.exercicio            = ENLP.exercicio
            And ENL.cod_nota             = ENLP.cod_nota
            And ENL.cod_entidade         = ENLP.cod_entidade

            And ENLP.cod_entidade        = ENLPA.cod_entidade
            And ENLP.cod_nota            = ENLPA.cod_nota
            And ENLP.exercicio           = ENLPA.exercicio
            And ENLP.timestamp           = ENLPA.timestamp 
            ' || stAuxPE || '
        GROUP BY
            OD.num_orgao,
            OD.cod_funcao
        ORDER BY
            OD.num_orgao,
            OD.cod_funcao';

        stSql := stSql || ')';
        EXECUTE stSql;
  END IF;
    IF ( stSituacao = 'liquidados' ) THEN
        stSql := 'CREATE TEMPORARY TABLE tmp_liquidado AS (
                SELECT
                    sum(nli.vl_total) as valor,
                    od.num_orgao,
                    od.cod_funcao
                FROM
                    orcamento.despesa             as od,
                    orcamento.conta_despesa       as cd,
                    empenho.pre_empenho_despesa   as ped,
                    empenho.pre_empenho           as pe,
                    empenho.empenho               as e,
                    empenho.nota_liquidacao_item  as nli,
                    empenho.nota_liquidacao       as nl
                WHERE
                        cd.cod_conta               = ped.cod_conta
                    AND cd.exercicio               = ped.exercicio

                    And od.cod_despesa             = ped.cod_despesa
                    AND od.exercicio               = ped.exercicio

                    And pe.exercicio               = ped.exercicio
                    And pe.cod_pre_empenho         = ped.cod_pre_empenho

                    And e.cod_entidade             IN (' || stCodEntidades || ')
                    And e.exercicio                = ' || quote_literal(stExercicio) || '

                    AND e.exercicio                = pe.exercicio
                    AND e.cod_pre_empenho          = pe.cod_pre_empenho

                    AND e.exercicio                = nl.exercicio_empenho
                    AND e.cod_entidade             = nl.cod_entidade
                    AND e.cod_empenho              = nl.cod_empenho

                    AND nl.exercicio               = nli.exercicio
                    AND nl.cod_nota                = nli.cod_nota
                    AND nl.cod_entidade            = nli.cod_entidade
                    ' || stAuxL || '
                GROUP BY
                    od.num_orgao,
                    od.cod_funcao
                ORDER BY
                    od.num_orgao,
                    od.cod_funcao';

        stSql := stSql || ')';
        EXECUTE stSql;

        stSql := 'CREATE TEMPORARY TABLE tmp_liquidado_estornado AS (
        SELECT
            sum(ENLIA.vl_anulado) as valor,
            OD.num_orgao,
            OD.cod_funcao

        from orcamento.despesa                    as OD,
             orcamento.conta_despesa              as OCD,
             empenho.pre_empenho_despesa          as EPED,
             empenho.pre_empenho                  as EPE,
             empenho.empenho                      as EE,
             empenho.nota_liquidacao              as ENL,
             empenho.nota_liquidacao_item         as ENLI,
             empenho.nota_liquidacao_item_anulado as ENLIA

        Where OCD.cod_conta               = EPED.cod_conta
          AND OCD.exercicio               = EPED.exercicio
          And EPE.cod_pre_empenho         = EE.cod_pre_empenho
          And EPE.exercicio               = EE.exercicio

          And EE.exercicio                = ENL.exercicio_empenho
          And EE.cod_entidade             = ENL.cod_entidade
          And EE.cod_empenho              = ENL.cod_empenho
          And EE.cod_entidade             IN ('||stCodEntidades||')
          And EE.exercicio                = '|| quote_literal(stExercicio) || '

          And ENL.exercicio               = ENLI.exercicio
          And ENL.cod_nota                = ENLI.cod_nota
          And ENL.cod_entidade            = ENLI.cod_entidade';

          stSql := stSql || '
          And ENLI.exercicio              = ENLIA.exercicio
          And ENLI.cod_pre_empenho        = ENLIA.cod_pre_empenho
          And ENLI.num_item               = ENLIA.num_item
          And ENLI.cod_entidade           = ENLIA.cod_entidade
          And ENLI.exercicio_item         = ENLIA.exercicio_item
          And ENLI.cod_nota               = ENLIA.cod_nota
          And OD.cod_despesa              = EPED.cod_despesa
          AND OD.exercicio                = EPED.exercicio
          And OD.cod_entidade             IN ('||stCodEntidades||')
          And EPED.exercicio              = EPE.exercicio
          And EPED.cod_pre_empenho        = EPE.cod_pre_empenho 
          ' || stAuxLA || '
        GROUP BY        
            OD.num_orgao,
            OD.cod_funcao
        ORDER BY        
            OD.num_orgao,
            OD.cod_funcao';

        stSql := stSql || ')';
        EXECUTE stSql;
  END IF;

        stSql := 'CREATE TEMPORARY TABLE tmp_despesa AS
                SELECT
                     *
                FROM    orcamento.despesa AS OD
                WHERE   exercicio = ' || quote_literal(stExercicio) || '
                ' || stFiltro; 

        EXECUTE stSql;

        FOR reRegistro IN
            SELECT   distinct cod_funcao
            FROM     tmp_despesa
            ORDER BY cod_funcao
        LOOP
            stCampos := stCampos || ',f_' || reRegistro.cod_funcao || ' numeric(14,2) ';
        END LOOP;

        stSql := 'CREATE TEMPORARY TABLE tmp_relatorio(
                     num_orgao      INTEGER
                    ,nom_orgao      VARCHAR(100)
                 '|| stCampos ||'
                    ,vl_total       NUMERIC(14,2)
                ) ';

        EXECUTE stSql;


        FOR reRegistro IN
            SELECT   DISTINCT  oo.num_orgao
                              ,oo.nom_orgao
            FROM     orcamento.orgao     as oo
                    ,tmp_despesa         as td
            WHERE    oo.num_orgao     = td.num_orgao
            AND      oo.exercicio     = td.exercicio
            ORDER BY oo.num_orgao
        LOOP
            INSERT INTO tmp_relatorio (num_orgao, nom_orgao) VALUES (reRegistro.num_orgao, reRegistro.nom_orgao);
        END LOOP;

    --Totaliza os resultados dinamicamente com update
    IF ( stSituacao = 'empenhados' ) THEN
        stTabela1 = 'tmp_empenhado';
        stTabela2 = 'tmp_anulado';
    END IF;
    IF ( stSituacao = 'liquidados' ) THEN
        stTabela1 = 'tmp_liquidado';
        stTabela2 = 'tmp_liquidado_estornado';
    END IF;
    IF ( stSituacao = 'pagos' ) THEN
        stTabela1 = 'tmp_pago';
        stTabela2 = 'tmp_estornado';
    END IF;
    IF ( stTipoRelatorio = 'orcamento' ) THEN
        stTabela1 = '';
        stTabela2 = '';
    END IF;

    FOR reRegistro IN
        SELECT  *
        FROM    tmp_relatorio
        ORDER BY num_orgao
    LOOP
        nuTotOrgao = 0;
        FOR reRegistro2 IN
            SELECT   distinct cod_funcao
            FROM     tmp_despesa
            ORDER BY cod_funcao

        LOOP
            nuFuncao    := coalesce(orcamento.fn_totaliza_orgao_funcao(reRegistro.num_orgao,reRegistro2.cod_funcao,stTabela1,stTabela2),0);
            nuTotOrgao  := nuTotOrgao + nuFuncao;
            stExecute   := 'UPDATE tmp_relatorio SET f_'||reRegistro2.cod_funcao||' = '||coalesce(orcamento.fn_totaliza_orgao_funcao(reRegistro.num_orgao,reRegistro2.cod_funcao,stTabela1,stTabela2),0) ||' WHERE num_orgao='||reRegistro.num_orgao;
            EXECUTE stExecute;
        END LOOP;
        UPDATE tmp_relatorio SET vl_total = nuTotOrgao WHERE num_orgao = reRegistro.num_orgao;
    END LOOP;

    --Lista os resultados
    FOR reRegistro IN
        SELECT  *
        FROM    tmp_relatorio
        ORDER BY num_orgao
    LOOP
        RETURN next reRegistro;
    END LOOP;


    DROP TABLE tmp_relatorio;
    DROP TABLE tmp_despesa;

    IF ( stSituacao = 'empenhados' ) THEN
        DROP TABLE tmp_empenhado;
        DROP TABLE tmp_anulado;
    END IF;
    IF ( stSituacao = 'liquidados' ) THEN
        DROP TABLE tmp_liquidado;
        DROP TABLE tmp_liquidado_estornado;
    END IF;
    IF ( stSituacao = 'pagos' ) THEN
        DROP TABLE tmp_pago;
        DROP TABLE tmp_estornado;
    END IF;

    RETURN;
END;
$$language 'plpgsql';

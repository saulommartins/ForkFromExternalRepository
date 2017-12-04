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
* Casos de uso: uc-02.01.11
*/

/*
$Log$
Revision 1.2  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_somatorio_despesa_unidade_categoria_economica_balanco(varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,integer) RETURNS SETOF RECORD AS
$$
DECLARE
    stExercicio             ALIAS FOR $1;
    stFiltro                ALIAS FOR $2;
    stDataInicial           ALIAS FOR $3;
    stDataFinal             ALIAS FOR $4;
    stCodEntidades          ALIAS FOR $5;
    stSituacao              ALIAS FOR $6;
    inNumOrgao              ALIAS FOR $7;
    inNumUnidade            ALIAS FOR $8;
    inCategoriaEconomica    ALIAS FOR $9;
    stSql                   VARCHAR   := '';
    nuSoma                  NUMERIC(14,2);
    reRegistro              RECORD;
    reRegistro2             RECORD;

    stCampos                VARCHAR   := '';
    stExecute               VARCHAR   := '';
    nuTotUnidade            NUMERIC(14,2);
    nuFuncao                NUMERIC(14,2);

BEGIN
stDataInicial   :=  replace(stDataInicial,'''','');
stDataFinal     :=  replace(stDataFinal,'''','');

  IF ( stSituacao = 'empenhados' ) THEN
    stSql := 'CREATE TEMPORARY TABLE tmp_empenhado AS (
       SELECT
            e.dt_empenho as dataConsulta,
            coalesce(ipe.vl_total,0.00) as valor,
            cd.cod_estrutural as cod_estrutural,
            d.num_orgao as num_orgao,
            d.num_unidade as num_unidade
        FROM
            orcamento.despesa           as d,
            orcamento.conta_despesa     as cd,
            empenho.pre_empenho_despesa as ped,
            empenho.empenho             as e,
            empenho.pre_empenho         as pe,
            empenho.item_pre_empenho    as ipe
        WHERE
                cd.cod_conta               = ped.cod_conta
            AND cd.exercicio               = ped.exercicio

            And d.cod_despesa              = ped.cod_despesa
            AND d.exercicio                = ped.exercicio

            And pe.exercicio               = ped.exercicio
            And pe.cod_pre_empenho         = ped.cod_pre_empenho

            And e.cod_entidade             IN (' || stCodEntidades || ')
            And e.exercicio                = ' || quote_literal(stExercicio) || '

            AND e.exercicio                = pe.exercicio
            AND e.cod_pre_empenho          = pe.cod_pre_empenho

            AND pe.exercicio               = ipe.exercicio
            AND pe.cod_pre_empenho         = ipe.cod_pre_empenho
            ';

            if(inNumOrgao is not null and inNumOrgao <> '') then
                stSql := stSql || ' AND d.num_orgao = ' || inNumOrgao ||' ';
            end if;
            if (inNumUnidade is not null and inNumUnidade <> '') then
                stSql := stSql || ' AND d.num_unidade = ' || inNumUnidade;
            end if;

          stSql := stSql || ')';

        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_anulado AS (
            SELECT to_date(to_char(EEAI.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta
            , EEAI.vl_anulado as valor
            , OCD.cod_estrutural as cod_estrutural
            , OD.num_orgao
            , OD.num_unidade
               from orcamento.despesa           as OD,
                    orcamento.conta_despesa     as OCD,
                    empenho.pre_empenho_despesa as EPED,
                    empenho.pre_empenho         as EPE,
                    empenho.item_pre_empenho    as EIPE,
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
                 And EEAI.exercicio           ='|| quote_literal(stExercicio) || '
                 And EEAI.cod_entidade        IN ('|| stCodEntidades ||')
                 And OD.cod_despesa           = EPED.cod_despesa
                 AND OD.exercicio             = EPED.exercicio
                 ';
                if(inNumOrgao is not null and inNumOrgao <> '') then
                    stSql := stSql || ' AND OD.num_orgao = ' || inNumOrgao ||' ';
                end if;
                if (inNumUnidade is not null and inNumUnidade <> '') then
                    stSql := stSql || ' AND OD.num_unidade = ' || inNumUnidade;
                end if;

              stSql := stSql || ')';
       EXECUTE stSql;
  END IF;
  
  IF ( stSituacao = 'pagos' ) THEN
    stSql := 'CREATE TEMPORARY TABLE tmp_pago AS (
        SELECT
            to_date(to_char(ENLP.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
            ENLP.vl_pago as valor,
            OCD.cod_estrutural as cod_estrutural,
            OD.num_orgao as num_orgao,
            OD.num_unidade as num_unidade
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

            And EE.exercicio             ='|| quote_literal(stExercicio) ||'
            And EE.cod_entidade          IN ('||stCodEntidades||')

            And EE.cod_empenho           = ENL.cod_empenho
            And EE.exercicio             = ENL.exercicio_empenho
            And EE.cod_entidade          = ENL.cod_entidade

            And ENL.cod_nota             = ENLP.cod_nota
            And ENL.cod_entidade         = ENLP.cod_entidade
            And ENL.exercicio            = ENLP.exercicio
            ';

            if(inNumOrgao is not null and inNumOrgao <> '') then
                stSql := stSql || ' AND OD.num_orgao = ' || inNumOrgao ||' ';
            end if;
            if (inNumUnidade is not null and inNumUnidade <> '') then
                stSql := stSql || ' AND OD.num_unidade = ' || inNumUnidade;
            end if;

        stSql := stSql || ')';

        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_estornado AS (
        SELECT
            to_date(to_char(ENLPA.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
            ENLPA.vl_anulado as valor,
            OCD.cod_estrutural as cod_estrutural,
            OD.num_orgao as num_orgao,
            OD.num_unidade as num_unidade
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
            AND OD.exercicio             = EPED.exercicio
            ';

            stSql := stSql || '
            And EPED.exercicio           = EPE.exercicio
            And EPED.cod_pre_empenho     = EPE.cod_pre_empenho

            And EPE.exercicio            = EE.exercicio
            And EPE.cod_pre_empenho      = EE.cod_pre_empenho

            And EE.cod_entidade          IN ('|| stCodEntidades ||')
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
            ';
            if(inNumOrgao is not null and inNumOrgao <> '') then
                stSql := stSql || ' AND OD.num_orgao = ' || inNumOrgao ||' ';
            end if;
            if (inNumUnidade is not null and inNumUnidade <> '') then
                stSql := stSql || ' AND OD.num_unidade = ' || inNumUnidade;
            end if;
        stSql := stSql || ')';

        EXECUTE stSql;
  END IF;
  
  IF ( stSituacao = 'liquidados' ) THEN
    stSql := 'CREATE TEMPORARY TABLE tmp_liquidado AS (
                SELECT
                    nl.dt_liquidacao as dataConsulta,
                    nli.vl_total as valor,
                    cd.cod_estrutural as cod_estrutural,
                    d.num_orgao as num_orgao,
                    d.num_unidade as num_unidade
                FROM
                    orcamento.despesa             as d,
                    orcamento.conta_despesa       as cd,
                    empenho.pre_empenho_despesa   as ped,
                    empenho.pre_empenho           as pe,
                    empenho.empenho               as e,
                    empenho.nota_liquidacao_item  as nli,
                    empenho.nota_liquidacao       as nl
                WHERE
                        cd.cod_conta               = ped.cod_conta
                    AND cd.exercicio               = ped.exercicio

                    And d.cod_despesa              = ped.cod_despesa
                    AND d.exercicio                = ped.exercicio

                    And pe.exercicio               = ped.exercicio
                    And pe.cod_pre_empenho         = ped.cod_pre_empenho

                    And e.cod_entidade             IN (' || stCodEntidades || ')
                    And e.exercicio                = ' || quote_literal(stExercicio) || '

                    AND e.exercicio                = pe.exercicio
                    AND e.cod_pre_empenho          = pe.cod_pre_empenho

                    AND e.exercicio = nl.exercicio_empenho
                    AND e.cod_entidade = nl.cod_entidade
                    AND e.cod_empenho = nl.cod_empenho

                    AND nl.exercicio = nli.exercicio
                    AND nl.cod_nota = nli.cod_nota
                    AND nl.cod_entidade = nli.cod_entidade
                    ';
                    if(inNumOrgao is not null and inNumOrgao <> '') then
                        stSql := stSql || ' AND d.num_orgao = ' || inNumOrgao ||' ';
                    end if;
                    if (inNumUnidade is not null and inNumUnidade <> '') then
                        stSql := stSql || ' AND d.num_unidade = ' || inNumUnidade;
                    end if;

        stSql := stSql || ')';

        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_liquidado_estornado AS (
        SELECT
            to_date(to_char(ENLIA.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta
            , ENLIA.vl_anulado as valor
            , OCD.cod_estrutural as cod_estrutural
            , OD.num_orgao
            , OD.num_unidade
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
          And EE.cod_entidade             IN ('|| stCodEntidades ||')
          And EE.exercicio                = '|| quote_literal(stExercicio) || '

          And ENL.exercicio               = ENLI.exercicio
          And ENL.cod_nota                = ENLI.cod_nota
          And ENL.cod_entidade            = ENLI.cod_entidade
          ';

          stSql := stSql || '
          And ENLI.exercicio           = ENLIA.exercicio
          And ENLI.cod_pre_empenho     = ENLIA.cod_pre_empenho
          And ENLI.num_item            = ENLIA.num_item
          And ENLI.cod_entidade        = ENLIA.cod_entidade
          And ENLI.exercicio_item      = ENLIA.exercicio_item
          And ENLI.cod_nota            = ENLIA.cod_nota
          And OD.cod_despesa           = EPED.cod_despesa
          AND OD.exercicio             = EPED.exercicio
          And OD.cod_entidade          IN ('|| stCodEntidades ||')
          And EPED.exercicio           = EPE.exercicio
          And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
          ';

         if(inNumOrgao is not null and inNumOrgao <> '') then
             stSql := stSql || ' AND OD.num_orgao = ' || inNumOrgao ||' ';
         end if;
         if (inNumUnidade is not null and inNumUnidade <> '') then
             stSql := stSql || ' AND OD.num_unidade = ' || inNumUnidade;
         end if;

        stSql := stSql || ')';

        EXECUTE stSql;
  END IF;


        stSql := 'CREATE TEMPORARY TABLE tmp_despesa_despesa AS
                SELECT
                     *
                     ,orcamento.fn_consulta_class_despesa(cod_conta
                                                        , exercicio
                                                        , ((    SELECT administracao.configuracao.valor
                                                                FROM administracao.configuracao
                                                                WHERE administracao.configuracao.cod_modulo = 8
                                                                AND administracao.configuracao.parametro = ''masc_class_despesa''
                                                                AND administracao.configuracao.exercicio = ' || quote_literal(stExercicio) || '))
                        ) as classificacao
                     ,string_to_array( orcamento.fn_consulta_class_despesa(cod_conta
                                                                            , exercicio
                                                                            , ((    SELECT administracao.configuracao.valor
                                                                                    FROM administracao.configuracao
                                                                                    WHERE administracao.configuracao.cod_modulo = 8
                                                                                    AND administracao.configuracao.parametro = ''masc_class_despesa''
                                                                                    AND administracao.configuracao.exercicio = ' || quote_literal(stExercicio) || ')) ),
                        ''.'') as arClassificacao
                FROM    orcamento.despesa
                WHERE   exercicio = ' || quote_literal(stExercicio) || '
                ' || stFiltro ;
        EXECUTE stSql;

        FOR reRegistro IN
            EXECUTE 'SELECT   DISTINCT ON (arClassificacao[2]) *
                        FROM     tmp_despesa_despesa
                        ORDER BY arClassificacao[2]'
        LOOP
            IF reRegistro.arClassificacao[1]::integer = inCategoriaEconomica AND reRegistro.arClassificacao[2]::integer > 0 THEN
                stCampos := stCampos || ',g_' || reRegistro.arClassificacao[2] || ' numeric(14,2) ';
            END IF;
        END LOOP;

        stSql := 'CREATE TEMPORARY TABLE tmp_relatorio_despesa(
                     num_orgao              INTEGER
                    ,num_unidade            INTEGER
                    ,nom_unidade            VARCHAR(100)
                         '|| stCampos ||'
                    ,vl_total               NUMERIC(14,2)
                ) ';
        EXECUTE stSql;

        FOR reRegistro IN
            EXECUTE
            'SELECT   DISTINCT    ou.num_orgao
                               , ou.num_unidade
                               , ou.nom_unidade
            FROM     orcamento.unidade  as ou
                    ,tmp_despesa_despesa        as td
            WHERE    ou.num_orgao           = td.num_orgao
            AND      ou.num_unidade         = td.num_unidade
            AND      ou.exercicio           = td.exercicio
            AND      td.arClassificacao[1]::integer  =' ||inCategoriaEconomica ||'
            AND      td.arClassificacao[2]::integer  > 0
            ORDER BY ou.num_orgao, ou.num_unidade'
        LOOP
            INSERT INTO tmp_relatorio_despesa (num_orgao, num_unidade, nom_unidade)
            VALUES (reRegistro.num_orgao, reRegistro.num_unidade, reRegistro.nom_unidade);
        END LOOP;


        --Totaliza os resultados dinamicamente com update
        FOR reRegistro IN
            EXECUTE 'SELECT  *
                        FROM    tmp_relatorio_despesa
                        ORDER BY num_orgao, num_unidade'
        LOOP
            nuTotUnidade := 0;
            FOR reRegistro2 IN
                EXECUTE 'SELECT   DISTINCT ON (arClassificacao[2]) *
                            FROM     tmp_despesa_despesa
                            ORDER BY arClassificacao[2]'
            LOOP
                IF reRegistro2.arClassificacao[1]::integer = inCategoriaEconomica AND reRegistro2.arClassificacao[2]::integer > 0 THEN
                    IF ( stSituacao = 'empenhados' ) THEN
                       nuFuncao := coalesce(orcamento.fn_consolidado_empenhado(stDataInicial
                                                                                ,stDataFinal
                                                                                , reRegistro2.arClassificacao[1]||'.'||reRegistro2.arClassificacao[2]
                                                                                ,reRegistro.num_orgao,reRegistro.num_unidade),0.00)
                                                                                -
                                                                                coalesce(orcamento.fn_consolidado_anulado(stDataInicial
                                                                                                                         , stDataFinal
                                                                                                                         , reRegistro2.arClassificacao[1]||'.'||reRegistro2.arClassificacao[2]
                                                                                                                         ,reRegistro.num_orgao,reRegistro.num_unidade),0.00);
                    END IF;
                    IF ( stSituacao = 'pagos' ) THEN
                       nuFuncao := coalesce(orcamento.fn_consolidado_pago(stDataInicial
                                                                        , stDataFinal
                                                                        , reRegistro2.arClassificacao[1]||'.'||reRegistro2.arClassificacao[2]
                                                                        ,reRegistro.num_orgao,reRegistro.num_unidade),0.00)
                                                                        -
                                                                        coalesce(orcamento.fn_consolidado_estornado(stDataInicial
                                                                                                                    ,stDataFinal
                                                                                                                    , reRegistro2.arClassificacao[1]||'.'||reRegistro2.arClassificacao[2]
                                                                                                                    ,reRegistro.num_orgao,reRegistro.num_unidade),0.00);
                    END IF;
                    IF ( stSituacao = 'liquidados' )  THEN
                        nuFuncao := coalesce(orcamento.fn_consolidado_liquidado(stDataInicial
                                                                                , stDataFinal
                                                                                , reRegistro2.arClassificacao[1]||'.'||reRegistro2.arClassificacao[2]
                                                                                ,reRegistro.num_orgao,reRegistro.num_unidade),0.00)
                                                                                -
                                                                                coalesce(orcamento.fn_consolidado_liquidado_estornado(quote_literal(stDataInicial)
                                                                                ,quote_literal(stDataFinal)
                                                                                , reRegistro2.arClassificacao[1]||'.'||reRegistro2.arClassificacao[2]
                                                                                ,reRegistro.num_orgao,reRegistro.num_unidade),0.00);
                    END IF;
--                    nuFuncao := coalesce(orcamento.fn_totaliza_despesa_unidade_categoria_economica(reRegistro.num_orgao,reRegistro.num_unidade,reRegistro2.arClassificacao[1]||''.''||reRegistro2.arClassificacao[2]),0);
                    nuTotUnidade := nuTotUnidade + nuFuncao;
                    stExecute := 'UPDATE tmp_relatorio_despesa
                                  SET g_'||reRegistro2.arClassificacao[2]||' = '||nuFuncao||'
                                  WHERE num_orgao='||reRegistro.num_orgao||'
                                  AND num_unidade='||reRegistro.num_unidade;
                    EXECUTE stExecute;
                END IF;
            END LOOP;
            UPDATE tmp_relatorio_despesa SET vl_total = nuTotUnidade WHERE num_orgao = reRegistro.num_orgao AND num_unidade = reRegistro.num_unidade;
        END LOOP;


        --Lista os resultados
        FOR reRegistro IN
            EXECUTE 'SELECT  *
                     FROM    tmp_relatorio_despesa
                     ORDER BY num_orgao, num_unidade'
        LOOP
            RETURN next reRegistro;
        END LOOP;


    DROP TABLE tmp_relatorio_despesa;
    DROP TABLE tmp_despesa_despesa;

    IF ( stSituacao = 'empenhados' ) THEN
        DROP TABLE tmp_empenhado;
        DROP TABLE tmp_anulado;
    END IF;
    IF ( stSituacao = 'pagos' ) THEN
        DROP TABLE tmp_pago;
        DROP TABLE tmp_estornado;
    END IF;
    if ( stSituacao = 'liquidados' ) THEN
        DROP TABLE tmp_liquidado;
        DROP TABLE tmp_liquidado_estornado;
    END IF;

    RETURN;
END;
$$  LANGUAGE 'plpgsql';

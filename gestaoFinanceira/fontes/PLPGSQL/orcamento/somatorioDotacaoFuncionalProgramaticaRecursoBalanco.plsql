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
* $Revision: 27774 $
* $Name$
* $Author: tonismar $
* $Date: 2008-01-28 09:09:52 -0200 (Seg, 28 Jan 2008) $
*
* Casos de uso: uc-02.01.15
*/

/*
$Log$
Revision 1.6  2006/10/04 17:07:01  cako
Bug #7110#

Revision 1.5  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_somatorio_dotacao_funcional_programatica_recurso_balanco(varchar,varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stDataInicial       ALIAS FOR $3;
    stDataFinal         ALIAS FOR $4;
    stCodEntidades      ALIAS FOR $5;
    stSituacao          ALIAS FOR $6;

    stSql               VARCHAR   := '';
    reRegistro          RECORD;
    arDotacao           VARCHAR[];
    arMascDespesa       VARCHAR[];

    inOrgaoAnt          INTEGER := 0;
    inUnidadeAnt        INTEGER := 0;
    inFuncaoAnt         INTEGER := 0;
    inSubFuncaoAnt      INTEGER := 0;
    inProgramaAnt       INTEGER := 0;
    inPaoAnt            INTEGER := 0;
    inNivel             INTEGER := 0;
    inPosicao           INTEGER;
    stOrgao             VARCHAR;
    stUnidade           VARCHAR;
    stFuncao            VARCHAR;
    stSubFuncao         VARCHAR;
    stPrograma          VARCHAR;
    stPao               VARCHAR;
    stNumPao            VARCHAR;
    stClassDespesa      VARCHAR;
    stDigitoProjeto     VARCHAR;
    stDigitoAtividade   VARCHAR;
    stDigitoOperacao    VARCHAR;

BEGIN

IF ( stSituacao = 'empenhados' ) THEN
    stSql := 'CREATE TEMPORARY TABLE tmp_empenhado AS (
       SELECT
            e.dt_empenho as dataConsulta,
            coalesce(ipe.vl_total,0.00) as valor,
            conta_despesa.cod_estrutural as cod_estrutural,
            despesa.num_orgao as num_orgao,
            despesa.num_unidade as num_unidade,
            despesa.cod_recurso as cod_recurso,
            rec.tipo      as tipo_recurso,
            orcamento.fn_consulta_funcional_programatica(despesa.exercicio,despesa.cod_despesa)  as dotacao
        FROM
            orcamento.despesa           
            JOIN orcamento.recurso('||quote_literal(stExercicio)||') as rec
            ON (    rec.exercicio   = despesa.exercicio
                AND rec.cod_recurso = despesa.cod_recurso ),
            orcamento.conta_despesa     ,
            empenho.pre_empenho_despesa as ped,
            empenho.empenho             as e,
            empenho.pre_empenho         as pe,
            empenho.item_pre_empenho    as ipe
        WHERE
                conta_despesa.cod_conta               = despesa.cod_conta
            AND conta_despesa.exercicio               = despesa.exercicio

            And despesa.cod_despesa              = ped.cod_despesa
            AND despesa.exercicio                = ped.exercicio

            And pe.exercicio               = ped.exercicio
            And pe.cod_pre_empenho         = ped.cod_pre_empenho

            AND e.exercicio                = pe.exercicio
            AND e.cod_pre_empenho          = pe.cod_pre_empenho

            AND pe.exercicio               = ipe.exercicio
            AND pe.cod_pre_empenho         = ipe.cod_pre_empenho


                ' || stFiltro ;


          stSql := stSql || '
        )';

        EXECUTE stSql;

        stSql := 'CREATE TEMPORARY TABLE tmp_anulado AS (
            SELECT
            to_date(to_char(EEAI.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
            EEAI.vl_anulado as valor,
            despesa.cod_recurso as cod_recurso,
            rec.tipo       as tipo_recurso,
            orcamento.fn_consulta_funcional_programatica(despesa.exercicio,despesa.cod_despesa)  as dotacao
               from orcamento.despesa        
                    JOIN orcamento.recurso('||quote_literal(stExercicio)||') as rec
                    ON (    rec.exercicio   = despesa.exercicio
                        AND rec.cod_recurso = despesa.cod_recurso ),
                    orcamento.conta_despesa  ,
                    empenho.pre_empenho_despesa as EPED,
                    empenho.pre_empenho         as EPE,
                    empenho.item_pre_empenho    as EIPE,
                    empenho.empenho_anulado_item as EEAI

               Where
                     conta_despesa.cod_conta            = EPED.cod_conta
                 AND conta_despesa.exercicio            = EPED.exercicio
                 And EPED.exercicio           = EPE.exercicio
                 And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
                 And EPE.exercicio            = EIPE.exercicio
                 And EPE.cod_pre_empenho      = EIPE.cod_pre_empenho
                 And EIPE.exercicio           = EEAI.exercicio
                 And EIPE.cod_pre_empenho     = EEAI.cod_pre_empenho
                 And EIPE.num_item            = EEAI.num_item
                 And despesa.cod_despesa           = EPED.cod_despesa
                 AND despesa.exercicio             = EPED.exercicio
		  '||stFiltro;


              stSql := stSql || ')';
        EXECUTE stSql;
  END IF;

 IF ( stSituacao = 'pagos' ) THEN
        stSql := 'CREATE TEMPORARY TABLE tmp_pago AS (
        SELECT
            to_date(to_char(ENLP.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
            ENLP.vl_pago as valor,
            conta_despesa.cod_estrutural as cod_estrutural,
            despesa.num_orgao as num_orgao,
            despesa.num_unidade as num_unidade,
            despesa.cod_recurso as cod_recurso,
            rec.tipo       as tipo_recurso,
            orcamento.fn_consulta_funcional_programatica(despesa.exercicio,despesa.cod_despesa)  as dotacao

        FROM
            orcamento.despesa        
            JOIN orcamento.recurso('||quote_literal(stExercicio)||') as rec
            ON (    rec.exercicio   = despesa.exercicio
                AND rec.cod_recurso = despesa.cod_recurso ),
            orcamento.conta_despesa,
            empenho.pre_empenho_despesa     as EPED,
            empenho.empenho                 as EE,
            empenho.pre_empenho             as EPE,
            empenho.nota_liquidacao         as ENL,
            empenho.nota_liquidacao_paga    as ENLP

        WHERE
                conta_despesa.cod_conta            = EPED.cod_conta
            AND conta_despesa.exercicio            = EPED.exercicio

            AND despesa.cod_despesa           = EPED.cod_despesa
            AND despesa.exercicio             = EPED.exercicio

            And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
            And EPED.exercicio           = EPE.exercicio

            And EPE.exercicio            = EE.exercicio
            And EPE.cod_pre_empenho      = EE.cod_pre_empenho

            And EE.cod_empenho           = ENL.cod_empenho
            And EE.exercicio             = ENL.exercicio_empenho
            And EE.cod_entidade          = ENL.cod_entidade

            And ENL.cod_nota             = ENLP.cod_nota
            And ENL.cod_entidade         = ENLP.cod_entidade
            And ENL.exercicio            = ENLP.exercicio 
		  '||stFiltro;


        stSql := stSql || ')';
        EXECUTE stSql;

        stSql := 'CREATE TEMPORARY TABLE tmp_estornado AS (
        SELECT
            to_date(to_char(ENLPA.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
            ENLPA.vl_anulado as valor,
            conta_despesa.cod_estrutural as cod_estrutural,
            despesa.num_orgao as num_orgao,
            despesa.num_unidade as num_unidade,
            despesa.cod_recurso as cod_recurso,
            rec.tipo        as tipo_recurso,
            orcamento.fn_consulta_funcional_programatica(despesa.exercicio,despesa.cod_despesa)  as dotacao

        FROM
            orcamento.despesa
            JOIN orcamento.recurso('||quote_literal(stExercicio)||') as rec
            ON (    rec.exercicio   = despesa.exercicio
                AND rec.cod_recurso = despesa.cod_recurso ),
            orcamento.conta_despesa          ,
            empenho.pre_empenho_despesa          as EPED,
            empenho.empenho                      as EE,
            empenho.pre_empenho                  as EPE,
            empenho.nota_liquidacao              as ENL,
            empenho.nota_liquidacao_paga         as ENLP,
            empenho.nota_liquidacao_paga_anulada as ENLPA
        WHERE
                conta_despesa.cod_conta            = EPED.cod_conta
            AND conta_despesa.exercicio            = EPED.exercicio
            And despesa.cod_despesa           = EPED.cod_despesa
            AND despesa.exercicio             = EPED.exercicio';

            stSql := stSql || '
            And EPED.exercicio           = EPE.exercicio
            And EPED.cod_pre_empenho     = EPE.cod_pre_empenho

            And EPE.exercicio            = EE.exercicio
            And EPE.cod_pre_empenho      = EE.cod_pre_empenho

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

		  '||stFiltro;
        stSql := stSql || ')';
        EXECUTE stSql;
  END IF;
    IF ( stSituacao = 'liquidados' ) THEN
        stSql := 'CREATE TEMPORARY TABLE tmp_liquidado AS (
                SELECT
                    nl.dt_liquidacao as dataConsulta,
                    nli.vl_total as valor,
                    conta_despesa.cod_estrutural as cod_estrutural,
                    despesa.num_orgao as num_orgao,
                    despesa.num_unidade as num_unidade,
                    despesa.cod_recurso as cod_recurso,
                    rec.tipo        as tipo_recurso,
                    orcamento.fn_consulta_funcional_programatica(despesa.exercicio,despesa.cod_despesa)  as dotacao
                FROM
                    orcamento.despesa          
                    JOIN orcamento.recurso('||quote_literal(stExercicio)||') as rec
                    ON (    rec.exercicio   = despesa.exercicio
                        AND rec.cod_recurso = despesa.cod_recurso ),
                    orcamento.conta_despesa,
                    empenho.pre_empenho_despesa   as ped,
                    empenho.pre_empenho           as pe,
                    empenho.empenho               as e,
                    empenho.nota_liquidacao_item  as nli,
                    empenho.nota_liquidacao       as nl
                WHERE
                        conta_despesa.cod_conta               = ped.cod_conta
                    AND conta_despesa.exercicio               = ped.exercicio

                    And despesa.cod_despesa              = ped.cod_despesa
                    AND despesa.exercicio                = ped.exercicio

                    And pe.exercicio               = ped.exercicio
                    And pe.cod_pre_empenho         = ped.cod_pre_empenho

                    AND e.exercicio                = pe.exercicio
                    AND e.cod_pre_empenho          = pe.cod_pre_empenho

                    AND e.exercicio = nl.exercicio_empenho
                    AND e.cod_entidade = nl.cod_entidade
                    AND e.cod_empenho = nl.cod_empenho

                    AND nl.exercicio = nli.exercicio
                    AND nl.cod_nota = nli.cod_nota
                    AND nl.cod_entidade = nli.cod_entidade
			  '||stFiltro;

        stSql := stSql || ')';
        EXECUTE stSql;

        stSql := 'CREATE TEMPORARY TABLE tmp_liquidado_estornado AS (
        SELECT
            to_date(to_char(ENLIA.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
            ENLIA.vl_anulado as valor,
            conta_despesa.cod_estrutural as cod_estrutural,
            despesa.num_orgao,
            despesa.num_unidade,
            despesa.cod_recurso as cod_recurso,
            rec.tipo        as tipo_recurso,
            orcamento.fn_consulta_funcional_programatica(despesa.exercicio, despesa.cod_despesa)  as dotacao

        from orcamento.despesa                   
             JOIN orcamento.recurso('||quote_literal(stExercicio)||') as rec
             ON (    rec.exercicio   = despesa.exercicio
                 AND rec.cod_recurso = despesa.cod_recurso ),
             orcamento.conta_despesa           ,
             empenho.pre_empenho_despesa          as EPED,
             empenho.pre_empenho                  as EPE,
             empenho.empenho                      as EE,
             empenho.nota_liquidacao              as ENL,
             empenho.nota_liquidacao_item         as ENLI,
             empenho.nota_liquidacao_item_anulado as ENLIA

        Where conta_despesa.cod_conta               = EPED.cod_conta
          AND conta_despesa.exercicio               = EPED.exercicio
          And EPE.cod_pre_empenho         = EE.cod_pre_empenho
          And EPE.exercicio               = EE.exercicio

          And EE.exercicio                = ENL.exercicio_empenho
          And EE.cod_entidade             = ENL.cod_entidade
          And EE.cod_empenho              = ENL.cod_empenho

          And ENL.exercicio               = ENLI.exercicio
          And ENL.cod_nota                = ENLI.cod_nota
          And ENL.cod_entidade            = ENLI.cod_entidade';

          stSql := stSql || '
          And ENLI.exercicio           = ENLIA.exercicio
          And ENLI.cod_pre_empenho     = ENLIA.cod_pre_empenho
          And ENLI.num_item            = ENLIA.num_item
          And ENLI.cod_entidade        = ENLIA.cod_entidade
          And ENLI.exercicio_item      = ENLIA.exercicio_item
          And ENLI.cod_nota            = ENLIA.cod_nota
          And despesa.cod_despesa           = EPED.cod_despesa
          AND despesa.exercicio             = EPED.exercicio

          And EPED.exercicio           = EPE.exercicio
          And EPED.cod_pre_empenho     = EPE.cod_pre_empenho 
		  '||stFiltro;
        stSql := stSql || ')';
        EXECUTE stSql;
  END IF;

        CREATE TEMPORARY TABLE tmp_relatorio(
             dotacao        VARCHAR(80)
            ,cod_despesa    INTEGER
            ,descricao      VARCHAR(200)
            ,vl_ordinario   NUMERIC(14,2)
            ,vl_vinculado   NUMERIC(14,2)
            ,vl_total       NUMERIC(14,2)
            ,nivel          INTEGER
        );

        stSql := 'CREATE TEMPORARY TABLE tmp_despesa AS
                SELECT
                     despesa.*
                     ,orcamento.fn_consulta_funcional_programatica(despesa.exercicio, despesa.cod_despesa)  as dotacao
                FROM    orcamento.despesa
			left join orcamento.conta_despesa
			       ON conta_despesa.exercicio = despesa.exercicio
			      AND conta_despesa.cod_conta = despesa.cod_conta

                WHERE   despesa.exercicio = '|| quote_literal(stExercicio) || '
                ' || stFiltro ;

        EXECUTE stSql;

        FOR reRegistro IN
            SELECT   distinct on (dotacao) *
            FROM     tmp_despesa
            ORDER BY dotacao
        LOOP
            arDotacao := string_to_array(reRegistro.dotacao,'.');

            IF reRegistro.cod_funcao <> inFuncaoAnt THEN
                SELECT INTO
                    stFuncao
                    descricao
                FROM   orcamento.funcao
                WHERE  cod_funcao = reRegistro.cod_funcao
                  AND  exercicio  = reRegistro.exercicio;
                INSERT INTO tmp_relatorio ( dotacao, descricao, nivel ) values ( arDotacao[1], stFuncao, 1 );
                inSubFuncaoAnt := 0;
                inProgramaAnt  := 0;
            END IF;
            inFuncaoAnt := reRegistro.cod_funcao;

            IF reRegistro.cod_subfuncao <> inSubfuncaoAnt THEN
                SELECT INTO
                    stSubfuncao
                    descricao
                FROM   orcamento.subfuncao
                WHERE  cod_subfuncao = reRegistro.cod_subfuncao
                  AND  exercicio     = reRegistro.exercicio;
                INSERT INTO tmp_relatorio ( dotacao, descricao, nivel) values ( arDotacao[1]||'.'||
                                                                                arDotacao[2], stSubfuncao, 2 );
                inProgramaAnt  := 0;
            END IF;
            inSubfuncaoAnt := reRegistro.cod_subfuncao;

            IF reRegistro.cod_programa <> inProgramaAnt THEN
                SELECT INTO
                    stPrograma
                    descricao
                FROM   orcamento.programa
                WHERE  cod_programa  = reRegistro.cod_programa
                  AND  exercicio     = reRegistro.exercicio;
                INSERT INTO tmp_relatorio ( dotacao, descricao, nivel ) values ( arDotacao[1]||'.'||
                                                                                 arDotacao[2]||'.'||
                                                                                 arDotacao[3], stPrograma, 3 );
            END IF;
            inProgramaAnt := reRegistro.cod_programa;

        END LOOP;

    FOR reRegistro IN
        SELECT  *
        FROM    tmp_relatorio
        ORDER BY dotacao
    LOOP
        IF ( stSituacao = 'empenhados' ) THEN
            reRegistro.vl_ordinario := orcamento.fn_totaliza_dotacao_recurso_empenhado(reRegistro.dotacao,'ordinario',stDataInicial , stDataFinal ) - orcamento.fn_totaliza_dotacao_recurso_anulado(reRegistro.dotacao,'ordinario',stDataInicial , stDataFinal);
            reRegistro.vl_vinculado := orcamento.fn_totaliza_dotacao_recurso_empenhado(reRegistro.dotacao,'outros',   stDataInicial , stDataFinal ) - orcamento.fn_totaliza_dotacao_recurso_anulado(reRegistro.dotacao,'outros',stDataInicial , stDataFinal);
        END IF;
        IF ( stSituacao = 'liquidados' ) THEN
            reRegistro.vl_ordinario := orcamento.fn_totaliza_dotacao_recurso_liquidado(reRegistro.dotacao,'ordinario',stDataInicial , stDataFinal ) - orcamento.fn_totaliza_dotacao_recurso_liquidado_estornado(reRegistro.dotacao,'ordinario',stDataInicial , stDataFinal);
            reRegistro.vl_vinculado := orcamento.fn_totaliza_dotacao_recurso_liquidado(reRegistro.dotacao,'outros',   stDataInicial , stDataFinal ) - orcamento.fn_totaliza_dotacao_recurso_liquidado_estornado(reRegistro.dotacao,'outros',stDataInicial , stDataFinal);
        END IF;
        IF ( stSituacao = 'pagos' ) THEN
            reRegistro.vl_ordinario := orcamento.fn_totaliza_dotacao_recurso_pago(reRegistro.dotacao,'ordinario',stDataInicial , stDataFinal ) - orcamento.fn_totaliza_dotacao_recurso_estornado(reRegistro.dotacao,'ordinario',stDataInicial , stDataFinal);
            reRegistro.vl_vinculado := orcamento.fn_totaliza_dotacao_recurso_pago(reRegistro.dotacao,'outros',   stDataInicial , stDataFinal ) - orcamento.fn_totaliza_dotacao_recurso_estornado(reRegistro.dotacao,'outros',stDataInicial , stDataFinal);
        END IF;
        reRegistro.vl_total     := ( coalesce(reRegistro.vl_ordinario,0) + coalesce(reRegistro.vl_vinculado,0) );
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
$$ language 'plpgsql';

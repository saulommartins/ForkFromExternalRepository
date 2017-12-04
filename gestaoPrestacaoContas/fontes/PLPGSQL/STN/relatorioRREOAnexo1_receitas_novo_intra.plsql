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
* Script de função PLPGSQL - Relatório STN - RREO - Anexo 1
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 66617 $
* $Name$
* $Author: franver $
* $Date: 2016-10-06 09:12:28 -0300 (Thu, 06 Oct 2016) $
*
* Casos de uso: uc-04.05.28
*/


/*$Log$
 *Revision 1.1  2006/09/26 17:33:42  bruce
 *Colocada a tag de log
 **/

--select * from stn.fn_anexo1('2006','01/03/2006','30/04/2006','1,2');
CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo1_receitas_novo_intra(varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    dtInicial           ALIAS FOR $2;
    dtFinal             ALIAS FOR $3;
    stCodEntidades      ALIAS FOR $4;
    
    dtInicioAno         VARCHAR   := '';
    dtFimAno            VARCHAR   := '';
    stSql               VARCHAR   := '';
    stSql1              VARCHAR   := '';
    stMascClassReceita  VARCHAR   := '';
    stMascRecurso       VARCHAR   := '';
    reRegistro          RECORD;
    dtInicioExercicio   VARCHAR := '01/01/'||stExercicio;

BEGIN
    dtInicioAno := '01/01/' || stExercicio;

    stSql := '
              CREATE TEMPORARY TABLE tmp_valor AS (
              SELECT ocr.cod_estrutural as cod_estrutural
                   , lote.dt_lote       as data
                   , vl.vl_lancamento   as valor
                   , vl.oid             as primeira
                FROM contabilidade.valor_lancamento   as vl
                   , orcamento.conta_receita          as ocr
                   , orcamento.receita                as ore
                   , contabilidade.lancamento_receita as lr
                   , contabilidade.lancamento         as lan
                   , contabilidade.lote               as lote
               WHERE ore.exercicio       = ' || quote_literal(stExercicio) ;
        if ( stCodEntidades != '' ) then
            stSql := stSql || ' AND ore.cod_entidade    IN ('|| stCodEntidades ||') ';
        end if;

    stSql := stSql || '
                 AND ocr.cod_estrutural ILIKE ''7.%''
                 AND ocr.cod_conta       = ore.cod_conta
                 AND ocr.exercicio       = ore.exercicio

                 -- join lancamento receita
                 AND lr.cod_receita      = ore.cod_receita
                 AND lr.exercicio        = ore.exercicio
                 AND lr.estorno          = true
                 -- tipo de lancamento receita deve ser = A , de arrecadação
                 AND lr.tipo             = ''A''
                 -- join nas tabelas lancamento_receita e lancamento
                 AND lan.cod_lote        = lr.cod_lote
                 AND lan.sequencia       = lr.sequencia
                 AND lan.exercicio       = lr.exercicio
                 AND lan.cod_entidade    = lr.cod_entidade
                 AND lan.tipo            = lr.tipo
                 -- join nas tabelas lancamento e valor_lancamento
                 AND vl.exercicio        = lan.exercicio
                 AND vl.sequencia        = lan.sequencia
                 AND vl.cod_entidade     = lan.cod_entidade
                 AND vl.cod_lote         = lan.cod_lote
                 AND vl.tipo             = lan.tipo
                 -- na tabela valor lancamento  tipo_valor deve ser credito
                 AND vl.tipo_valor       = ''D''
                 AND lote.cod_lote       = lan.cod_lote
                 AND lote.cod_entidade   = lan.cod_entidade
                 AND lote.exercicio      = lan.exercicio
                 AND lote.tipo           = lan.tipo
               UNION
              SELECT ocr.cod_estrutural as cod_estrutural
                   , lote.dt_lote       as data
                   , vl.vl_lancamento   as valor
                   , vl.oid             as segunda
                FROM contabilidade.valor_lancamento      as vl
                   , orcamento.conta_receita             as ocr
                   , orcamento.receita                   as ore
                   , contabilidade.lancamento_receita    as lr
                   , contabilidade.lancamento            as lan
                   , contabilidade.lote                  as lote
               WHERE ore.exercicio       = '|| quote_literal(stExercicio);

        if ( stCodEntidades != '' ) then
            stSql := stSql || ' AND ore.cod_entidade    IN ('|| stCodEntidades ||') ';
        end if;
    stSql := stSql || '
                 AND ocr.cod_estrutural ILIKE ''7.%''
                 AND ocr.cod_conta       = ore.cod_conta
                 AND ocr.exercicio       = ore.exercicio
                 -- join lancamento receita
                 AND lr.cod_receita      = ore.cod_receita
                 AND lr.exercicio        = ore.exercicio
                 AND lr.estorno          = false
                 -- tipo de lancamento receita deve ser = A , de arrecadação
                 AND lr.tipo             = ''A''
                 -- join nas tabelas lancamento_receita e lancamento
                 AND lan.cod_lote        = lr.cod_lote
                 AND lan.sequencia       = lr.sequencia
                 AND lan.exercicio       = lr.exercicio
                 AND lan.cod_entidade    = lr.cod_entidade
                 AND lan.tipo            = lr.tipo
                 -- join nas tabelas lancamento e valor_lancamento
                 AND vl.exercicio        = lan.exercicio
                 AND vl.sequencia        = lan.sequencia
                 AND vl.cod_entidade     = lan.cod_entidade
                 AND vl.cod_lote         = lan.cod_lote
                 AND vl.tipo             = lan.tipo
                 -- na tabela valor lancamento  tipo_valor deve ser credito
                 AND vl.tipo_valor       = ''C''
                 -- Data Inicial e Data Final, antes iguala codigo do lote
                 AND lote.cod_lote       = lan.cod_lote
                 AND lote.cod_entidade   = lan.cod_entidade
                 AND lote.exercicio      = lan.exercicio
                 AND lote.tipo           = lan.tipo )
    '; 

    EXECUTE stSql;

    stSql := '
      CREATE TEMPORARY TABLE tmp_rreo_an1_receita AS (
      SELECT 1 as grupo
           , cod_estrutural::VARCHAR as cod_estrutural
           , nivel
           , descricao::VARCHAR as descricao
           , previsao_inicial::numeric(14,2) as previsao_inicial
           , previsao_inicial::numeric(14,2) as previsao_atualizada
           , (coalesce(no_bimestre,0.00)*-1)::numeric(14,2) as no_bimestre
           , CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00''
                  THEN CAST((coalesce(no_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
                  ELSE ''0.00''::numeric(14,2)
              END as p_no_bimestre
           , (coalesce(ate_bimestre,0.00)*-1)::numeric(14,2) as ate_bimestre
           , CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00''
                  THEN CAST((coalesce(ate_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
                  ELSE ''0.00''::numeric(14,2)
              END as p_ate_bimestre
           , coalesce(previsao_inicial,0.00) + coalesce(ate_bimestre,0.00)::numeric(14,2) as a_realizar
        FROM (
              SELECT nivel
                   , cod_estrutural
                   , descricao
                   , SUM (previsao_inicial) as previsao_inicial
                   , SUM (no_bimestre) as no_bimestre
                   , SUM (ate_bimestre) as ate_bimestre
                FROM (
                      SELECT publico.fn_nivel(ocr.cod_estrutural) as nivel
                           , ocr.cod_estrutural as cod_estrutural
                           , TRIM(ocr.descricao) AS descricao
                           , orcamento.fn_receita_valor_previsto( '||quote_literal(stExercicio)||'
                                                                , publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                , '||quote_literal(stCodEntidades)||'
                                                                ) as previsao_inicial
                           , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                     , '||quote_literal(dtInicial)||'
                                                                     , '||quote_literal(dtFinal)||'
                                                                     ) as no_bimestre
                           , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                     , '||quote_literal(dtInicioAno)||'
                                                                     , '||quote_literal(dtFinal)||'
                                                                     ) as ate_bimestre
                        FROM orcamento.conta_receita     as ocr
                            -- Filtros
                       WHERE ((ocr.cod_estrutural like ''7.%'')
                            AND publico.fn_nivel(ocr.cod_estrutural) >= 1         
                            AND publico.fn_nivel(ocr.cod_estrutural) <= 3
                            AND ocr.exercicio = '|| quote_literal(stExercicio) ||')
                    ORDER BY ocr.cod_estrutural
                     ) as tbl
            GROUP BY cod_estrutural
                   , nivel
                   , descricao
                   ) as tb
     )  
    ';
    EXECUTE stSql;

    stSql := '
    INSERT INTO tmp_rreo_an1_receita
    SELECT 0 AS grupo
         , ''0.0.0.0.00.00.00.00.00'' AS cod_estrutural
         , 0 AS nivel
         , ''RECEITAS (INTRA-ORÇAMENTÁRIAS) (II)'' AS descricao
         , sum(previsao_inicial)    AS previsao_inicial
         , sum(previsao_atualizada) AS previsao_atualizada
         , sum(no_bimestre)         AS no_bimestre
         , sum(p_no_bimestre)       AS p_no_bimestre
         , sum(ate_bimestre)        AS ate_bimestre
         , sum(p_ate_bimestre)      AS p_ate_bimestre
         , sum(a_realizar)          AS a_realizar
      FROM tmp_rreo_an1_receita
     WHERE grupo = 1
       and nivel = 2
    ';

    EXECUTE stSql;
    stSql := 'SELECT * FROM tmp_rreo_an1_receita ORDER BY  cod_estrutural';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_valor;
    DROP TABLE tmp_rreo_an1_receita ; 
    RETURN;
END;
$$ language 'plpgsql';

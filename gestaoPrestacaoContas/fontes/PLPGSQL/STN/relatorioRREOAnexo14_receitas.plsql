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
/**
    * Script de função PLPGSQL - Relatório STN - RREO - Anexo 14.
    * Data de Criação: 30/05/2008


    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-06.01.14

    $Id: $

*/

CREATE OR REPLACE FUNCTION stn.fn_anexo14_receitas(varchar, integer ,varchar ,varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    inBimestre              ALIAS FOR $2;
    stCodEntidades          ALIAS FOR $3;
    stCodRecursos           ALIAS FOR $4;
    stRelatorioNovo         ALIAS FOR $5;
    dtInicioAno         VARCHAR   := '';
    dtFimAno            VARCHAR   := '';
    stSql               VARCHAR   := '';
    stSql1              VARCHAR   := '';
    stMascClassReceita  VARCHAR   := '';
    stMascRecurso       VARCHAR   := '';
    reRegistro          RECORD;
    dtInicial           varchar := ''; 
    dtFinal             varchar := ''; 

    arDatas varchar[] ;

BEGIN
        dtInicioAno := '01/01/'||stExercicio;
        arDatas := publico.bimestre ( stExercicio, inBimestre );
        dtInicial := arDatas [ 0 ];
        dtFinal   := arDatas [ 1 ];

        stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as primeira
                , ore.cod_recurso    as recurso
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote
            WHERE

                    ore.exercicio       = '|| quote_literal(stExercicio) ||' ';
                if ( stCodEntidades != '' ) then
                   stSql := stSql || ' AND ore.cod_entidade    IN (' || stCodEntidades || ') ';
                end if;

            stSql := stSql || '

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

            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as segunda
                , ore.cod_recurso    as recurso
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote

            WHERE
                ore.exercicio       = '|| quote_literal(stExercicio) ||' ';  

                if ( stCodEntidades != '' ) then
                   stSql := stSql || ' AND ore.cod_entidade    IN (' || stCodEntidades || ') ';
                end if;
            stSql := stSql || '

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
                AND lote.tipo           = lan.tipo ) '; 

        EXECUTE stSql;



stSql := '
    SELECT
        1 as grupo,
        cod_estrutural,
        1 as nivel,
        nom_conta,
        coalesce(previsao_inicial,0.00) as previsao_atualizada,
        coalesce(receitas_realizadas,0.00)*-1 as receitas_realizadas
    FROM( ';
IF stRelatorioNovo = 'sim' THEN
    stSql := stSql || '
            SELECT
                ocr.cod_estrutural,
                ocr.descricao,
                cast(''RECEITA DE CAPITAL - ALIENAÇÃO DE ATIVOS (I)'' as varchar ) as nom_conta,
                stn.fn_anexo14_receita_valor_previsto( '|| quote_literal(stExercicio) || '
                                                    ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                    , ''' || stCodEntidades || '''
                                                    , ''' || stCodRecursos || '''
                ) as previsao_inicial,
                stn.fn_anexo14_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                         ,''' || dtInicioAno || '''
                                                         ,''' || dtFinal || '''
                                                         ,''' || stCodRecursos || '''
                ) as receitas_realizadas
            FROM
                orcamento.conta_receita     as ocr
            WHERE
                -- Filtros
                ocr.cod_estrutural like ''2.2.0%''

            AND publico.fn_nivel(ocr.cod_estrutural) >       1
            AND publico.fn_nivel(ocr.cod_estrutural) <=      3
            AND ocr.exercicio = '|| quote_literal(stExercicio) ||'

            ORDER BY
                ocr.cod_estrutural
    ';
ELSE
    stSql := stSql || '
            SELECT
                pc.cod_estrutural,
                cast(''Receitas De Capital'' as varchar ) as nom_conta,
                stn.fn_anexo14_receita_valor_previsto( '|| quote_literal(stExercicio) ||'
                                                    ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                    , ''' || stCodEntidades || '''
                                                    , ''' || stCodRecursos || '''
                ) as previsao_inicial,
                stn.fn_anexo14_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                         ,''' || dtInicioAno || '''
                                                         ,''' || dtFinal || '''
                                                         ,''' || stCodRecursos || '''
                ) as receitas_realizadas
            FROM
                contabilidade.plano_conta   as pc,
                orcamento.conta_receita     as ocr
            WHERE
                -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
                pc.cod_estrutural   = ''4.''||ocr.cod_estrutural  AND
                pc.exercicio        = ocr.exercicio             
                -- Filtros
            AND pc.cod_estrutural like ''4.2.2.0%''  
      
            AND publico.fn_nivel(pc.cod_estrutural) >       1   
            AND publico.fn_nivel(pc.cod_estrutural) <=      4   
            AND pc.exercicio = '|| quote_literal(stExercicio) ||'
        
            ORDER BY
                pc.cod_estrutural ';
END IF;
stSql := stSql || '
    ) as tbl

UNION

    SELECT
        1 as grupo,
        cod_estrutural,
        2 as nivel,
        cast(''Alienação De Ativos'' as varchar ) as nom_conta,
        coalesce(previsao_inicial,0.00) as previsao_atualizada,
        coalesce(receitas_realizadas,0.00)*-1 as receitas_realizadas
    FROM( ';
IF stRelatorioNovo = 'sim' THEN
    stSql := stSql || '
            SELECT
                ocr.cod_estrutural,
                ocr.descricao,
                stn.fn_anexo14_receita_valor_previsto( '|| quote_literal(stExercicio) ||'
                                                    ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                    , ''' || stCodEntidades || '''
                                                    , ''' || stCodRecursos || '''
                ) as previsao_inicial,
                stn.fn_anexo14_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                         ,''' || dtInicioAno || '''
                                                         ,''' || dtFinal || '''
                                                         ,''' || stCodRecursos || '''
                ) as receitas_realizadas

            FROM
                orcamento.conta_receita     as ocr
            WHERE
                -- Filtros
                ocr.cod_estrutural like ''2.2.0%''

            AND publico.fn_nivel(ocr.cod_estrutural) >       1
            AND publico.fn_nivel(ocr.cod_estrutural) <=      3
            AND ocr.exercicio = '|| quote_literal(stExercicio) ||'

            ORDER BY
                ocr.cod_estrutural
    ';
ELSE
    stSql := stSql || '
            SELECT
                pc.cod_estrutural,
                pc.nom_conta,
                stn.fn_anexo14_receita_valor_previsto( '|| quote_literal(stExercicio) ||'
                                                    ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                    , ''' || stCodEntidades || '''
                                                    , ''' || stCodRecursos || '''
                ) as previsao_inicial,
                stn.fn_anexo14_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                         ,''' || dtInicioAno || '''
                                                         ,''' || dtFinal || '''
                                                         ,''' || stCodRecursos || '''
                ) as receitas_realizadas
            FROM
                contabilidade.plano_conta   as pc,
                orcamento.conta_receita     as ocr
            WHERE
                -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
                pc.cod_estrutural   = ''4.''||ocr.cod_estrutural  AND
                pc.exercicio        = ocr.exercicio             
                -- Filtros
            AND pc.cod_estrutural like ''4.2.2.0%''  
      
            AND publico.fn_nivel(pc.cod_estrutural) >       1   
            AND publico.fn_nivel(pc.cod_estrutural) <=      4   
            AND pc.exercicio = '|| quote_literal(stExercicio) ||'
        
            ORDER BY
                pc.cod_estrutural ';
END IF;
stSql := stSql || '
    ) as tbl

UNION

    SELECT
        1 as grupo,
        cod_estrutural,
        3 as nivel,
        cast(''Alienação De Bens Móveis'' as varchar ) as nom_conta,
        coalesce(previsao_inicial,0.00) as previsao_atualizada,
        coalesce(receitas_realizadas,0.00)*-1 as receitas_realizadas
    FROM( ';
IF stRelatorioNovo = 'sim' THEN
    stSql := stSql || '
            SELECT
                ocr.cod_estrutural,
                ocr.descricao,
                stn.fn_anexo14_receita_valor_previsto( '|| quote_literal(stExercicio) ||'
                                                    ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                    , ''' || stCodEntidades || '''
                                                    , ''' || stCodRecursos || '''
                ) as previsao_inicial,
                stn.fn_anexo14_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                         ,''' || dtInicioAno ||'''
                                                         ,''' || dtFinal || '''
                                                         ,''' || stCodRecursos ||'''
                ) as receitas_realizadas
            FROM
                orcamento.conta_receita     as ocr
            WHERE
                ocr.cod_estrutural   like ''2.2.1%''

            AND publico.fn_nivel(ocr.cod_estrutural) >       1
            AND publico.fn_nivel(ocr.cod_estrutural) <=      3
            AND ocr.exercicio = '|| quote_literal(stExercicio) ||'

            ORDER BY
                ocr.cod_estrutural
    ';
ELSE
    stSql := stSql || '
            SELECT
                pc.cod_estrutural,
                pc.nom_conta,
                stn.fn_anexo14_receita_valor_previsto( '|| quote_literal(stExercicio) ||'
                                                    ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                    , ''' || stCodEntidades || '''
                                                    , ''' || stCodRecursos || '''
                ) as previsao_inicial,
                stn.fn_anexo14_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                         ,''' || dtInicioAno || '''
                                                         ,''' || dtFinal || '''
                                                         ,''' || stCodRecursos || '''
                ) as receitas_realizadas
            FROM
                contabilidade.plano_conta   as pc,
                orcamento.conta_receita     as ocr
            WHERE
                -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
                pc.cod_estrutural   = ''4.''||ocr.cod_estrutural  
            AND pc.exercicio        = ocr.exercicio          
            AND pc.cod_estrutural   like ''4.2.2.1%'' 
    
            AND publico.fn_nivel(pc.cod_estrutural) >       1   
            AND publico.fn_nivel(pc.cod_estrutural) <=      4   
            AND pc.exercicio = '|| quote_literal(stExercicio) ||'
        
            ORDER BY
                pc.cod_estrutural ';
END IF;
stSql := stSql || '
    ) as tbl

UNION

    SELECT
        1 as grupo,
        cod_estrutural,
        3 as nivel,
        cast(''Alienação De Bens Imóveis'' as varchar ) as nom_conta,
        coalesce(previsao_inicial,0.00) as previsao_atualizada,
        coalesce(receitas_realizadas,0.00)*-1 as receitas_realizadas
    FROM( ';
IF stRelatorioNovo = 'sim' THEN
    stSql := stSql || '
            SELECT
                ocr.cod_estrutural,
                ocr.descricao,
                stn.fn_anexo14_receita_valor_previsto( '|| quote_literal(stExercicio) ||'
                                                    ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                    , ''' || stCodEntidades || '''
                                                    , ''' || stCodRecursos || '''
                ) as previsao_inicial,
                stn.fn_anexo14_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                         ,''' || dtInicioAno || '''
                                                         ,''' || dtFinal || '''
                                                         ,''' || stCodRecursos || '''
                ) as receitas_realizadas
            FROM
                orcamento.conta_receita     as ocr
            WHERE
                ocr.cod_estrutural   like ''2.2.2%''

            AND publico.fn_nivel(ocr.cod_estrutural) >       1
            AND publico.fn_nivel(ocr.cod_estrutural) <=      3
            AND ocr.exercicio = '|| quote_literal(stExercicio) ||'

            ORDER BY
                ocr.cod_estrutural
    ';
ELSE
    stSql := stSql || '
            SELECT
                pc.cod_estrutural,
                pc.nom_conta,
                stn.fn_anexo14_receita_valor_previsto( '|| quote_literal(stExercicio) ||'
                                                    ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                    , ''' || stCodEntidades || '''
                                                    , ''' || stCodRecursos || '''
                ) as previsao_inicial,
                stn.fn_anexo14_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                         ,''' || dtInicioAno || '''
                                                         ,''' || dtFinal || '''
                                                         ,''' || stCodRecursos || '''
                ) as receitas_realizadas
            FROM
                contabilidade.plano_conta   as pc,
                orcamento.conta_receita     as ocr
            WHERE
                -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
                pc.cod_estrutural   = ''4.''||ocr.cod_estrutural  
            AND pc.exercicio        = ocr.exercicio          
            AND pc.cod_estrutural   like ''4.2.2.2%''
    
            AND publico.fn_nivel(pc.cod_estrutural) >       1   
            AND publico.fn_nivel(pc.cod_estrutural) <=      4   
            AND pc.exercicio = '|| quote_literal(stExercicio) ||'
        
            ORDER BY
                pc.cod_estrutural ';
END IF;
stSql := stSql || '
    ) as tbl
		
';

    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_valor;

    RETURN;
END;
$$ language 'plpgsql';

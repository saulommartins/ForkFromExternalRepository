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
    * Relatório RREO Anexo 11 - Despesas. 
    * Data de Criação: 05/06/2008


    * @author Desenvolvedor Leopoldo Braga Barreiro
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/


/*
    4.5.9.0.66.00.00.00.00 | CONCESSAO DE EMPRESTIMOS E FINANCIAMENTOS
    4.5.9.0.66.01.00.00.00 | EMPRESTIMOS CONCEDIDOS
    4.5.9.0.66.01.01.00.00 | CONCESSAO DE EMPRESTIMOS A CONTRIBUINTES (ART.32, §3°,  I, LRF)
    4.5.9.0.66.02.00.00.00 | FINANCIAMENTOS CONCEDIDOS
    4.5.9.0.66.02.01.00.00 | CONCESSAO DE FINANCIAMENTOS A CONTRIBUINTES (ART.32, §3°, I, LRF)
    4.5.9.0.66.02.02.00.00 | FINANCIAMENTOS PARA PEQUENOS PRODUTORES RURAIS
    4.5.9.0.66.03.00.00.00 | FINANCIAMENTOS - ESTUDANTE DE ENSINO SUPERIOR
    4.5.9.0.66.99.00.00.00 | OUTROS EMPRESTIMOS E FINANCIAMENTOS
*/

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo11_despesas(VARCHAR, VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE 

    stExercicio        ALIAS FOR $1;
    stEntidades        ALIAS FOR $2;
    stPerdidiocidade   ALIAS FOR $3;
    inValor            ALIAS FOR $4;
    
    stDtIniExercicio   VARCHAR := '';
    stDtIni 	       VARCHAR := '';
    stDtFim 	       VARCHAR := '';
    stSQL 	       VARCHAR := '';
    stSQLaux           VARCHAR := '';
    arDatas 	       VARCHAR[];
    inMin              INTEGER;
    inMax              INTEGER;
    
    reReg	       RECORD;
    boRotinaRP         BOOLEAN := FALSE;

BEGIN

    stDtIniExercicio := '01/01/' || stExercicio;
    
    IF stPerdidiocidade = 'mes' THEN
        arDatas := publico.mes ( stExercicio, inValor );
    ELSEIF stPerdidiocidade = 'bimestre' THEN
        arDatas := publico.bimestre ( stExercicio, inValor );
    END IF;
    
    stDtIni := arDatas [ 0 ];
    stDtFim := arDatas [ 1 ];
    
    -- ---------------------------------------------------
    -- Verifica se a rotina de inscricao de RP foi rodada
    -- ---------------------------------------------------
    SELECT CASE WHEN ( nom_lote IS NOT NULL )
                THEN TRUE
                ELSE FALSE
           END AS rotina
      INTO boRotinaRP
      FROM contabilidade.lote
     WHERE exercicio   = '''|| stExercicio ||'''
       AND  nom_lote ilike 'ENCERRAMENTO DO EXERCÍCIO'
       AND  tipo = 'M';

    -- --------------------------------------------
    -- Inicio das Tabelas Temporarias
    -- --------------------------------------------
    
    
    -- Empenhados
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_empenhado AS (
    SELECT 
        tb_cd.exercicio, 
        tb_cd.cod_estrutural, 
        tb_cd.cod_conta, 
        COALESCE(SUM(ipe.vl_total), 0.00) AS valor_empenhado 
    FROM 
        empenho.pre_empenho pe 
        INNER JOIN 
        empenho.item_pre_empenho ipe ON 
            ipe.cod_pre_empenho = pe.cod_pre_empenho AND 
            ipe.exercicio = pe.exercicio 
        INNER JOIN 
        empenho.empenho e ON 
            e.exercicio = pe.exercicio AND 
            e.cod_pre_empenho = pe.cod_pre_empenho 
        LEFT OUTER JOIN 
        (SELECT 
            ped.exercicio, 
            ped.cod_pre_empenho, 
            cd.cod_estrutural, 
            cd.cod_conta 
        FROM
            empenho.pre_empenho_despesa ped
            INNER JOIN
            orcamento.despesa d ON 
                ped.exercicio = d.exercicio AND
                ped.cod_despesa = d.cod_despesa
            INNER JOIN 
            orcamento.conta_despesa cd ON
                d.exercicio = cd.exercicio AND
                d.cod_conta = cd.cod_conta 
        ) AS tb_cd ON
            pe.exercicio = tb_cd.exercicio AND 
            pe.cod_pre_empenho = tb_cd.cod_pre_empenho 
    WHERE 
        tb_cd.cod_estrutural LIKE ''4%'' AND 
        tb_cd.exercicio < ''' || stExercicio || ''' AND 
        e.cod_entidade IN (' || stEntidades || ') AND 
        e.dt_empenho <= TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'') 
    GROUP BY 
        tb_cd.exercicio, 
        tb_cd.cod_estrutural, 
        tb_cd.cod_conta 
    ORDER BY 
        tb_cd.exercicio, 
        tb_cd.cod_estrutural, 
        tb_cd.cod_conta 
    )
    ';

    
    EXECUTE stSQL;
    
    -- Empenhados Anulados
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_anulado AS (
    SELECT 
        tb_cd.exercicio, 
        tb_cd.cod_estrutural, 
        tb_cd.cod_conta, 
        COALESCE(SUM(eai.vl_anulado), 0.00) AS valor_anulado 
    FROM
        empenho.empenho e
        INNER JOIN
        empenho.empenho_anulado ea ON
            ea.exercicio = e.exercicio AND
            ea.cod_entidade = e.cod_entidade AND
            ea.cod_empenho = e.cod_empenho
        INNER JOIN
        empenho.empenho_anulado_item eai ON
            eai.exercicio = ea.exercicio AND
            eai.cod_entidade = ea.cod_entidade AND
            eai.cod_empenho = ea.cod_empenho AND
            eai."timestamp" = ea."timestamp" 
        LEFT OUTER JOIN 
        (SELECT 
            ped.exercicio, 
            ped.cod_pre_empenho, 
            cd.cod_estrutural, 
            cd.cod_conta 
        FROM
            empenho.pre_empenho_despesa ped
            INNER JOIN
            orcamento.despesa d ON 
                ped.exercicio = d.exercicio AND
                ped.cod_despesa = d.cod_despesa
            INNER JOIN 
            orcamento.conta_despesa cd ON
                d.exercicio = cd.exercicio AND
                d.cod_conta = cd.cod_conta 
        ) AS tb_cd ON
            e.exercicio = tb_cd.exercicio AND 
            e.cod_pre_empenho = tb_cd.cod_pre_empenho 
    WHERE 
        tb_cd.cod_estrutural LIKE ''4%'' AND 
        tb_cd.exercicio < ''' || stExercicio || ''' AND 
        e.cod_entidade IN (' || stEntidades || ') AND 
        e.dt_empenho <= TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'') AND 
        TO_DATE( TO_CHAR( ea."timestamp", ''dd/mm/yyyy''), ''dd/mm/yyyy'' ) <= TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'') 
    GROUP BY 
        tb_cd.exercicio, 
        tb_cd.cod_estrutural, 
        tb_cd.cod_conta 
    ORDER BY 
        tb_cd.exercicio, 
        tb_cd.cod_estrutural, 
        tb_cd.cod_conta 
    )';
    
    EXECUTE stSQL;
    
    -- Restos Liquidados
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_liquidado AS (
    SELECT
        e.exercicio,
        tb_cd.cod_estrutural, 
        SUM(nli.vl_total) as valor_liquidado 
    FROM 
        empenho.empenho e 
        INNER JOIN 
        empenho.nota_liquidacao nl ON 
            nl.exercicio_empenho = e.exercicio AND 
            nl.cod_entidade = e.cod_entidade AND 
            nl.cod_empenho = e.cod_empenho 
        INNER JOIN 
        empenho.nota_liquidacao_item nli ON
            nli.exercicio = nl.exercicio AND 
            nli.cod_nota = nl.cod_nota AND
            nli.cod_entidade = nl.cod_entidade
        INNER JOIN 
        empenho.pre_empenho pe ON
            pe.exercicio = e.exercicio AND
            pe.cod_pre_empenho = e.cod_pre_empenho 
        LEFT OUTER JOIN (
        SELECT
            ped.exercicio, 
            ped.cod_pre_empenho, 
            cd.cod_conta,
            cd.cod_estrutural 
        FROM 
            empenho.pre_empenho_despesa as ped, 
            orcamento.despesa as d,
            orcamento.conta_despesa as cd 
        WHERE
            ped.exercicio < ''' || stExercicio || ''' AND 
            ped.cod_despesa = d.cod_despesa AND 
            ped.exercicio = d.exercicio AND 
            ped.cod_conta = cd.cod_conta AND 
            ped.exercicio = cd.exercicio 
    ) AS tb_cd ON
        pe.exercicio = tb_cd.exercicio AND
        pe.cod_pre_empenho = tb_cd.cod_pre_empenho
     
    WHERE 
        e.exercicio < ''' || stExercicio || ''' AND 
        e.cod_entidade IN (' || stEntidades || ') AND
        nl.dt_liquidacao <= to_date(''' || stDtFim || ''', ''dd/mm/yyyy'') AND
        tb_cd.cod_estrutural like ''4%'' 
    GROUP BY 
        e.exercicio,
        tb_cd.cod_estrutural 
    ORDER BY
        e.exercicio,
        tb_cd.cod_estrutural
    )
    ';
    
    EXECUTE stSQL;
    
    
    -- Restos Liquidados Estornados
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_estornado_liquidacao AS (
    SELECT
        e.exercicio,
        tb_cd.cod_conta, 
        tb_cd.cod_estrutural, 
        SUM(nlia.vl_anulado) as valor_estornado_liq 
    FROM 
        empenho.empenho e
        INNER JOIN 
        empenho.nota_liquidacao nl ON
            nl.exercicio_empenho = e.exercicio AND
            nl.cod_entidade = e.cod_entidade AND
            nl.cod_empenho = e.cod_empenho
        INNER JOIN 
        empenho.nota_liquidacao_item nli ON
            nli.exercicio = nl.exercicio AND
            nli.cod_entidade = nl.cod_entidade AND
            nli.cod_nota = nl.cod_nota
        INNER JOIN   
        empenho.nota_liquidacao_item_anulado nlia ON
            nlia.exercicio = nli.exercicio AND
            nlia.cod_nota = nli.cod_nota AND
            nlia.num_item = nli.num_item AND
            nlia.exercicio_item = nli.exercicio_item AND
            nlia.cod_pre_empenho = nli.cod_pre_empenho AND
            nlia.cod_entidade = nli.cod_entidade
        INNER JOIN 
        empenho.pre_empenho as pe ON
            pe.exercicio = e.exercicio AND
            pe.cod_pre_empenho = e.cod_pre_empenho 
        LEFT OUTER JOIN (
        SELECT
            ped.exercicio, 
            ped.cod_pre_empenho, 
            d.cod_conta,
            cd.cod_estrutural 
        FROM
            empenho.pre_empenho_despesa as ped, 
            orcamento.despesa           as d,
            orcamento.conta_despesa     as cd 
        WHERE
            ped.exercicio      < ''' || stExercicio || ''' AND
            ped.cod_despesa    = d.cod_despesa and 
            ped.exercicio      = d.exercicio   and 
            ped.cod_conta      = cd.cod_conta  and 
            ped.exercicio      = cd.exercicio
        ) AS tb_cd ON
            pe.exercicio = tb_cd.exercicio AND
            pe.cod_pre_empenho = tb_cd.cod_pre_empenho 
    WHERE 
        e.exercicio < ''' || stExercicio || ''' AND
        e.cod_entidade IN (' || stEntidades || ') AND 
        TO_DATE(TO_CHAR(nlia.timestamp, ''dd/mm/yyyy''), ''dd/mm/yyyy'') <= TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'') AND
        tb_cd.cod_estrutural like ''4%'' 
    GROUP BY 
        e.exercicio,
        tb_cd.cod_conta, 
        tb_cd.cod_estrutural 
    ORDER BY 
        e.exercicio,
        tb_cd.cod_estrutural 
    )';
    
    EXECUTE stSQL;

    
    -- Pagos
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_pago AS (
    SELECT
        e.exercicio, 
        ped_d_cd.cod_estrutural,
        ped_d_cd.cod_conta, 
        sum(nlp.vl_pago) as valor_pago     
    FROM 
        empenho.empenho e
        INNER JOIN 
        empenho.nota_liquidacao nl ON
            e.exercicio = nl.exercicio_empenho AND
            e.cod_entidade = nl.cod_entidade AND
            e.cod_empenho = nl.cod_empenho 
        INNER JOIN 
        empenho.nota_liquidacao_paga nlp ON     
            nl.exercicio = nlp.exercicio AND
            nl.cod_nota = nlp.cod_nota AND
            nl.cod_entidade = nlp.cod_entidade
--        LEFT OUTER JOIN
--        (SELECT 
--            p.cod_entidade,
--            p.cod_nota,
--            p.exercicio_liquidacao,
--            p.timestamp,
--            pa.cod_plano 
--        FROM
--            contabilidade.pagamento p
--            INNER JOIN 
--            contabilidade.lancamento_empenho le ON
--                p.cod_lote = le.cod_lote AND 
--                p.tipo = le.tipo AND 
--                p.sequencia = le.sequencia AND 
--                p.exercicio = le.exercicio AND
--                p.cod_entidade = le.cod_entidade
--            INNER JOIN 
--            contabilidade.conta_credito cc ON
--                le.cod_lote = cc.cod_lote AND
--                le.tipo = cc.tipo AND
--                le.exercicio = cc.exercicio AND
--                le.cod_entidade = cc.cod_entidade AND
--                le.sequencia = cc.sequencia 
--            INNER JOIN 
--            contabilidade.plano_analitica pa ON
--                cc.cod_plano = pa.cod_plano AND
--                cc.exercicio = pa.exercicio
--            INNER JOIN 
--            contabilidade.plano_conta pc ON
--                pa.cod_conta = pc.cod_conta AND
--                pa.exercicio = pc.exercicio 
--        WHERE
--            p.cod_entidade IN (' || stEntidades || ') AND
--            p.exercicio < ''' || stExercicio || ''' AND
--            pc.cod_estrutural LIKE ''3.4%'' AND
--            le.estorno = false 
--        ) as tmp ON 
--            nlp.cod_entidade = tmp.cod_entidade AND
--            nlp.cod_nota = tmp.cod_nota AND
--            nlp.exercicio = tmp.exercicio_liquidacao AND
--            nlp.timestamp = tmp.timestamp      
        INNER JOIN 
        empenho.pagamento_liquidacao_nota_liquidacao_paga plnlp ON
            nlp.cod_entidade = plnlp.cod_entidade AND
            nlp.cod_nota = plnlp.cod_nota AND
            nlp.exercicio = plnlp.exercicio_liquidacao AND
            nlp.timestamp = plnlp.timestamp 
        INNER JOIN 
        empenho.pagamento_liquidacao pl ON    
            pl.cod_ordem = plnlp.cod_ordem AND
            pl.exercicio = plnlp.exercicio AND
            pl.cod_entidade = plnlp.cod_entidade AND
            pl.exercicio_liquidacao = plnlp.exercicio_liquidacao AND
            pl.cod_nota = plnlp.cod_nota     
        LEFT OUTER JOIN
        (SELECT
            ped.exercicio, 
            ped.cod_pre_empenho, 
            d.cod_conta,
            cd.cod_estrutural 
        FROM
            empenho.pre_empenho_despesa ped, 
            orcamento.despesa d,
            orcamento.conta_despesa cd
        WHERE
            ped.exercicio < ''' || stExercicio || ''' AND 
            ped.cod_despesa = d.cod_despesa and 
            ped.exercicio = d.exercicio and 
            ped.cod_conta = cd.cod_conta  and 
            ped.exercicio = cd.exercicio
        ) as ped_d_cd ON
            e.exercicio = ped_d_cd.exercicio AND
            e.cod_pre_empenho = ped_d_cd.cod_pre_empenho 
    WHERE 
        e.exercicio < ''' || stExercicio || ''' AND
        e.cod_entidade IN (' || stEntidades || ') AND 
        to_date(to_char(nlp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') <= TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'') AND 
        ped_d_cd.cod_estrutural like ''4%''
    GROUP BY
        e.exercicio, 
        ped_d_cd.cod_estrutural ,
        ped_d_cd.cod_conta 
    ORDER BY
        e.exercicio, 
        ped_d_cd.cod_estrutural 
    )';
    
    EXECUTE stSQL;
    
    
    -- Estornados
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_estornado AS (
    SELECT
        e.exercicio, 
        ped_d_cd.cod_estrutural,
        ped_d_cd.cod_conta, 
        sum(nlpa.vl_anulado) as valor_estornado 
    FROM 
        empenho.empenho e 
        INNER JOIN 
        empenho.nota_liquidacao nl ON
            e.exercicio = nl.exercicio_empenho AND
            e.cod_entidade = nl.cod_entidade AND
            e.cod_empenho = nl.cod_empenho 
        INNER JOIN 
        empenho.nota_liquidacao_paga nlp ON     
            nl.exercicio = nlp.exercicio AND
            nl.cod_nota = nlp.cod_nota AND
            nl.cod_entidade = nlp.cod_entidade 
        LEFT OUTER JOIN 
        (SELECT
            p.cod_entidade,
            p.cod_nota,
            p.exercicio_liquidacao,
            p.timestamp,
            pa.cod_plano 
        FROM
            contabilidade.pagamento p
            INNER JOIN 
            contabilidade.lancamento_empenho le ON
                p.cod_lote = le.cod_lote AND 
                p.tipo = le.tipo AND 
                p.sequencia = le.sequencia AND 
                p.exercicio = le.exercicio AND
                p.cod_entidade = le.cod_entidade
            INNER JOIN 
            contabilidade.conta_credito cc ON
                le.cod_lote = cc.cod_lote AND
                le.tipo = cc.tipo AND
                le.exercicio = cc.exercicio AND
                le.cod_entidade = cc.cod_entidade AND
                le.sequencia = cc.sequencia 
            INNER JOIN 
            contabilidade.plano_analitica pa ON
                cc.cod_plano = pa.cod_plano AND
                cc.exercicio = pa.exercicio
            INNER JOIN 
            contabilidade.plano_conta pc ON
                pa.cod_conta = pc.cod_conta AND
                pa.exercicio = pc.exercicio 
        WHERE
            p.exercicio < ''' || stExercicio || ''' AND 
            p.cod_entidade IN (' || stEntidades || ') AND 
            pc.cod_estrutural LIKE ''3.4%'' AND
            le.estorno = true 
        ) AS tmp ON
            tmp.exercicio_liquidacao = nlp.exercicio AND 
            tmp.cod_entidade = nlp.cod_entidade AND 
            tmp.cod_nota = nlp.cod_nota AND 
            tmp.timestamp = nlp.timestamp 
        INNER JOIN 
        empenho.nota_liquidacao_paga_anulada nlpa ON
            nlpa.exercicio = nlp.exercicio AND 
            nlpa.cod_nota = nlp.cod_nota AND 
            nlpa.cod_entidade = nlp.cod_entidade AND 
            nlpa."timestamp" = nlp."timestamp" 
        INNER JOIN 
        empenho.pagamento_liquidacao_nota_liquidacao_paga plnlp ON
            nlp.cod_entidade = plnlp.cod_entidade AND
            nlp.cod_nota = plnlp.cod_nota AND
            nlp.exercicio = plnlp.exercicio_liquidacao AND
            nlp.timestamp = plnlp.timestamp 
        INNER JOIN 
        empenho.pagamento_liquidacao pl ON    
            pl.cod_ordem = plnlp.cod_ordem AND
            pl.exercicio = plnlp.exercicio AND
            pl.cod_entidade = plnlp.cod_entidade AND
            pl.exercicio_liquidacao = plnlp.exercicio_liquidacao AND
            pl.cod_nota = plnlp.cod_nota     
        LEFT OUTER JOIN
        (SELECT
            ped.exercicio, 
            ped.cod_pre_empenho, 
            d.cod_conta,
            cd.cod_estrutural 
        FROM
            empenho.pre_empenho_despesa ped
            INNER JOIN 
            orcamento.despesa d ON
                ped.exercicio = d.exercicio AND
                ped.cod_despesa = d.cod_despesa
            INNER JOIN 
            orcamento.conta_despesa cd ON 
                d.exercicio = cd.exercicio AND
                d.cod_conta = cd.cod_conta 
        WHERE 
            ped.exercicio < ''' || stExercicio || ''' AND
            cd.cod_estrutural LIKE ''4%''
        ) as ped_d_cd ON 
            e.exercicio = ped_d_cd.exercicio AND 
            e.cod_pre_empenho = ped_d_cd.cod_pre_empenho 
    WHERE 
        e.exercicio < ''' || stExercicio || ''' AND
        e.cod_entidade IN (' || stEntidades || ') AND
        TO_DATE(TO_CHAR(nlpa.timestamp_anulada, ''dd/mm/yyyy''), ''dd/mm/yyyy'') <= TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'') AND 
        ped_d_cd.cod_estrutural like ''4%'' 
    GROUP BY 
        e.exercicio, 
        ped_d_cd.cod_estrutural ,
        ped_d_cd.cod_conta 
    ORDER BY
        e.exercicio, 
        ped_d_cd.cod_estrutural 
    )';
    
    EXECUTE stSQL;
    
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_resto AS (
    SELECT * FROM (
    SELECT
        cd.exercicio,
        cd.cod_conta, 
        cd.cod_estrutural,
        (COALESCE(SUM(emp.valor_empenhado), 0.00) - (COALESCE(SUM(anl.valor_anulado), 0.00))) - (COALESCE(SUM(liq.valor_liquidado), 0.00) - COALESCE(SUM(est_liq.valor_estornado_liq), 0.00)) AS valor_resto 
    FROM
        orcamento.conta_despesa cd
        LEFT JOIN 
        tmp_empenhado emp ON
            emp.exercicio = cd.exercicio AND
            emp.cod_estrutural = cd.cod_estrutural 
        LEFT JOIN
        tmp_anulado anl ON
            anl.exercicio = cd.exercicio AND
            anl.cod_estrutural = cd.cod_estrutural
        LEFT JOIN
        tmp_liquidado liq ON
            liq.exercicio = cd.exercicio AND
            liq.cod_estrutural = cd.cod_estrutural 
        LEFT JOIN
        tmp_estornado_liquidacao est_liq ON
            est_liq.exercicio = cd.exercicio AND
            est_liq.cod_estrutural = cd.cod_estrutural 
    WHERE
        cd.cod_estrutural LIKE ''4%'' 
    GROUP BY 
        cd.exercicio,
        cd.cod_conta, 
        cd.cod_estrutural
    ) AS tb 
    WHERE valor_resto <> 0.00 
    )';
    
    EXECUTE stSQL;
    

    /*
    Despesas Liquidadas do primeiro dia do exercicio
    até o último dia do periodo selecionado
    */
    
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_rreo_despesa_liquidada_total AS (
    SELECT 
        pedcd.exercicio, 
        pedcd.cod_despesa, 
        COALESCE(SUM(nli.vl_total), 0.00) AS vl_liquidado, 
        COALESCE(SUM(nlia.vl_anulado), 0.00) AS vl_estornado 
    FROM 
        empenho.pre_empenho pe 
        LEFT JOIN 
        (SELECT 
            ped.exercicio, 
            ped.cod_pre_empenho, 
            d.cod_despesa 
        FROM 
            empenho.pre_empenho_despesa ped 
            INNER JOIN 
            orcamento.despesa d ON 
                ped.exercicio   = d.exercicio AND
                ped.cod_despesa = d.cod_despesa
        WHERE 
            ped.exercicio = ''' || stExercicio || ''' 
        ) AS pedcd ON 
            pe.exercicio = pedcd.exercicio AND 
            pe.cod_pre_empenho = pedcd.cod_pre_empenho 
        INNER JOIN 
        empenho.empenho e ON 
            e.exercicio = pe.exercicio AND 
            e.cod_pre_empenho = pe.cod_pre_empenho 
        INNER JOIN 
        empenho.nota_liquidacao nl ON 
            nl.exercicio_empenho = e.exercicio AND 
            nl.cod_entidade = e.cod_entidade AND 
            nl.cod_empenho = e.cod_empenho 
        INNER JOIN 
        empenho.nota_liquidacao_item nli ON 
            nli.exercicio = nl.exercicio AND 
            nli.cod_entidade = nl.cod_entidade AND 
            nli.cod_nota = nl.cod_nota 
        LEFT JOIN 
        empenho.nota_liquidacao_item_anulado nlia ON 
            nli.exercicio = nlia.exercicio AND 
            nli.cod_nota = nlia.cod_nota AND 
            nli.cod_entidade = nlia.cod_entidade AND 
            nli.num_item = nlia.num_item AND 
            nli.cod_pre_empenho = nlia.cod_pre_empenho AND 
            nli.exercicio_item = nlia.exercicio_item 
    WHERE 
        e.exercicio = ''' || stExercicio || ''' AND 
        e.cod_entidade IN (' || stEntidades || ') AND 
        nl.dt_liquidacao BETWEEN to_date(''' || stDtIniExercicio || ''', ''dd/mm/yyyy'') AND 
                                 to_date(''' || stDtFim || ''', ''dd/mm/yyyy'') 
    GROUP BY
        pedcd.exercicio, 
        pedcd.cod_despesa 
    )';
    
    EXECUTE stSQL;
    
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_rreo_despesa AS (

    SELECT grupo
                , nivel
                , cod_estrutural
                , descricao 
                , dotacao_atualizada AS dot_atu
                , vl_liquidado_total  AS liq_tot,
            CAST(0.00 AS NUMERIC(14,2)) AS resto 
         FROM (            SELECT * FROM contabilidade.fn_relatorio_balanco_orcamentario_despesa_novo(''' || stExercicio || ''',''' || stDtIni || ''' ,''' || stDtFim || ''', ''' || stEntidades || ''', '''') as
            retorno (                                                                                          
            grupo                   integer ,                                                                  
         cod_estrutural          varchar ,                                                                  
         descricao               varchar ,                                                                  
         nivel                   integer ,                                                                  
         dotacao_inicial         numeric ,                                                            
         creditos_adicionais     numeric ,                                                            
         dotacao_atualizada      numeric ,                                                            
         vl_empenhado_bimestre   numeric ,                                                            
         vl_empenhado_total      numeric ,                                                            
         vl_liquidado_bimestre   numeric ,                                                            
         vl_liquidado_total      numeric ,                                                            
         vl_pago_total	        numeric ,       
         percentual              numeric ,                                                            
         saldo_liquidar          numeric ) 
         WHERE cod_estrutural LIKE ''4%''
 ) AS tab1
    
    UNION ALL
    
    SELECT * FROM (    
        SELECT
            CAST(2 AS INTEGER) AS grupo, 
            CAST(1 AS INTEGER) AS nivel,
            CAST(''4.5.9.0.66.00.00.00.00'' AS VARCHAR) AS cod_estrutural, 
            CAST(''(-) Incentivos Fiscais a Contribuinte'' AS VARCHAR) AS descricao, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_atu, 
            CAST(0.00 AS NUMERIC(14,2)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS resto 
    ) AS tbl2
    
    UNION ALL 
    
    SELECT * FROM ( 
        SELECT
            CAST(3 AS INTEGER) AS grupo, 
            CAST(1 AS INTEGER) AS nivel,
            CAST(''0.0.0.0.00.00.00.00.00'' AS VARCHAR) AS cod_estrutural, 
            CAST(''(-) Incentivos Fiscais a Contribuinte por Instit. Financeira'' AS VARCHAR) AS descricao, 
            CAST(0.00 AS NUMERIC(14,2)) AS dot_atu, 
            CAST(0.00 AS NUMERIC(14,2)) AS liq_tot, 
            CAST(0.00 AS NUMERIC(14,2)) AS resto 
    ) AS tbl2 
    
    )';
    
    EXECUTE stSQL;
   

    stSQL := '
    UPDATE
        tmp_rreo_despesa 
    SET
    ';
    
    IF( boRotinaRP = TRUE ) THEN
        stSql := stSql || '
            resto     = COALESCE((SELECT SUM(valor_resto) FROM tmp_resto WHERE cod_estrutural LIKE ''4%''), 0.00) 
        ';
    ELSE
        stSql := stSql || '
            resto     = CAST(0.00 AS NUMERIC(14,2))
        ';
    END IF;
    stSql := stSql || '
    WHERE
        grupo = 1 AND
        nivel = 1 ';
    
    EXECUTE stSQL;

    stSQL := '
    UPDATE
        tmp_rreo_despesa 
    SET         
        resto   = COALESCE(( SELECT SUM(valor_resto) FROM tmp_resto WHERE cod_estrutural LIKE ''4.5.9.0.66%''), 0.00) 
    WHERE 
        grupo = 2 AND 
        nivel = 1 ';
    
    EXECUTE stSQL;

    
    -- Seleção de Retorno

    -- --------------------------------------
    -- Select de Retorno
    -- --------------------------------------


    stSQL := '
    SELECT
        *
    FROM
        tmp_rreo_despesa
    WHERE
        nivel = 1 
    ORDER BY
        grupo,
        nivel,
        descricao ';
    
    FOR reReg IN EXECUTE stSQL
    LOOP	
        RETURN NEXT reReg;	
    END LOOP;
    
    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_anulado;
    DROP TABLE tmp_liquidado;
    DROP TABLE tmp_estornado_liquidacao;
    DROP TABLE tmp_pago;
    DROP TABLE tmp_estornado;
    DROP TABLE tmp_resto;
    
    DROP TABLE tmp_rreo_despesa_liquidada_total;
    DROP TABLE tmp_rreo_despesa;
    
    RETURN;

END;

$$ LANGUAGE 'plpgsql';

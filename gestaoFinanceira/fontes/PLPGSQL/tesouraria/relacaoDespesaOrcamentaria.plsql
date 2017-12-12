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
 * Arquivo que efetua a consulta para o relatório de Relação de Despesa Orçamentária
 * Data de Criação   : 01/04/2009


 * @author Analista      Eduardo Paculski Schitz
 * @author Desenvolvedor Tonismar Regis Bernardo
 
 * @package URBEM
 * @subpackage 

 $Id: $
*/

CREATE OR REPLACE FUNCTION tesouraria.fn_relacao_despesa_orcamentaria(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio                    ALIAS FOR $1;
    stCodEntidades                 ALIAS FOR $2;
    stDtInicial                    ALIAS FOR $3;
    stDtFinal                      ALIAS FOR $4;
    inCodDotacao                   ALIAS FOR $5;
    stCodElementoDespesa           ALIAS FOR $6;
    stCodElementoDespesaMasc       ALIAS FOR $7;
    inCodOrgao                     ALIAS FOR $8;
    inCodUnidade                   ALIAS FOR $9;
    inCodFuncao                    ALIAS FOR $10;
    inCodSubFuncao                 ALIAS FOR $11;
    inCodPrograma                  ALIAS FOR $12;
    inCodPao                       ALIAS FOR $13;
    inCodRecursoIni                ALIAS FOR $14;
    inCodRecursoFim                ALIAS FOR $15;
    inCodContaIni                  ALIAS FOR $16;
    inCodContaFim                  ALIAS FOR $17;
    inCodOrdenacao                 ALIAS FOR $18;
    stSql               VARCHAR   := '';
    stCampo             VARCHAR   := '';
    reRegistro          RECORD;
BEGIN
    
    IF (inCodOrdenacao = '1') THEN
        stCampo = 'data';
    ELSEIF (inCodOrdenacao = '2') THEN
        stCampo = 'conta_banco';
    ELSEIF (inCodOrdenacao = '3') THEN
        stCampo = 'recurso';
    ELSEIF (inCodOrdenacao = '4') THEN
        stCampo = 'dotacao';
    ELSEIF (inCodOrdenacao = '5') THEN
        stCampo = 'elemento_despesa';
    END IF;

    stSql := 'CREATE TEMPORARY TABLE tmp_pago AS (
        SELECT p.cod_entidade as cod_entidade
             , p.cod_nota as cod_nota
             , p.exercicio_liquidacao as exercicio_liquidacao
             , p.timestamp as timestamp
             , nlcp.cod_plano as cod_plano
             , pc.nom_conta as nom_conta
          FROM contabilidade.pagamento p
             , contabilidade.lancamento_empenho le
             , contabilidade.conta_credito cc
             , contabilidade.plano_analitica pa
             , contabilidade.plano_conta pc
             , empenho.nota_liquidacao_conta_pagadora nlcp
         WHERE
            --Ligação PAGAMENTO : LANCAMENTO EMPENHO
                p.cod_entidade      IN (' || stCodEntidades || ')
            AND p.exercicio     = ' || quote_literal(stExercicio) || '
            AND p.cod_lote = le.cod_lote
            AND p.tipo = le.tipo
            AND p.sequencia = le.sequencia
            AND p.exercicio = le.exercicio
            AND p.cod_entidade = le.cod_entidade
            AND le.estorno = false

            --Ligação LANCAMENTO EMPENHO : CONTA_CREDITO
            AND le.cod_lote = cc.cod_lote
            AND le.tipo = cc.tipo
            AND le.exercicio = cc.exercicio
            AND le.cod_entidade = cc.cod_entidade
            AND le.sequencia = cc.sequencia
            
            AND nlcp.timestamp = p.timestamp 
            AND nlcp.exercicio_liquidacao = p.exercicio_liquidacao 
            AND nlcp.cod_entidade = p.cod_entidade
            AND nlcp.cod_nota = p.cod_nota

            --Ligação CONTA_CREDITO : PLANO ANALITICA
            AND nlcp.cod_plano = pa.cod_plano
            AND nlcp.exercicio = pa.exercicio';
            
            if ( inCodContaIni is not null and TRIM(inCodContaIni)<>'') then
                stSql := stSql || ' and nlcp.cod_plano >= ' || inCodContaIni || ' ';
            end if;

            if ( inCodContaFim is not null and TRIM(inCodContaFim)<>'') then
                stSql := stSql || ' and nlcp.cod_plano <= ' || inCodContaFim || ' ';
            end if;
            
            stSql := stSql || '
           --Ligação PLANO ANALITICA : PLANO CONTA
            AND pa.cod_conta = pc.cod_conta
            AND pa.exercicio = pc.exercicio
    );

    CREATE INDEX idx_tmp_pago ON tmp_pago (cod_entidade, cod_nota, exercicio_liquidacao, timestamp);

    ';

    EXECUTE stSql;

    stSql := '
    CREATE TEMPORARY TABLE tmp_estornado AS (
              SELECT nota_liquidacao_paga_anulada.cod_entidade
                   , nota_liquidacao_paga_anulada.cod_nota
                   , nota_liquidacao_paga_anulada.exercicio as exercicio_liquidacao
                   , nota_liquidacao_paga_anulada.timestamp
                   , sum (nota_liquidacao_paga_anulada.vl_anulado) as vl_anulado
                FROM empenho.nota_liquidacao_paga_anulada
               WHERE to_date(to_char(nota_liquidacao_paga_anulada.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'')
                     BETWEEN to_date(''' || stDtInicial || '''::varchar,''dd/mm/yyyy'')
                         AND to_date(''' || stDtFinal || '''::varchar,''dd/mm/yyyy'')
    ';

    if ( inCodContaIni is not null and TRIM(inCodContaIni)<>'' OR inCodContaFim is not null and TRIM(inCodContaFim)<>'') then
        stSql := stSql || '
                AND exists (  select 1
                  from contabilidade.pagamento
                  join contabilidade.lancamento_empenho
                    on ( pagamento.exercicio    = lancamento_empenho.exercicio
                   and   pagamento.cod_lote     = lancamento_empenho.cod_lote
                   and   pagamento.tipo         = lancamento_empenho.tipo
                   and   pagamento.sequencia    = lancamento_empenho.sequencia
                   and   pagamento.cod_entidade = lancamento_empenho.cod_entidade )
                  join contabilidade.conta_debito
                    on ( lancamento_empenho.cod_lote     = conta_debito.cod_lote
                   AND   lancamento_empenho.tipo         = conta_debito.tipo
                   AND   lancamento_empenho.exercicio    = conta_debito.exercicio
                   AND   lancamento_empenho.cod_entidade = conta_debito.cod_entidade
                   AND   lancamento_empenho.sequencia    = conta_debito.sequencia )
                  join contabilidade.plano_analitica
                    on ( conta_debito.cod_plano = plano_analitica.cod_plano
                   AND   conta_debito.exercicio = plano_analitica.exercicio )
                where TRUE ';
                IF (inCodContaIni is not null and TRIM(inCodContaIni)<>'') THEN
                    stSql := stSql || ' AND plano_analitica.cod_plano >= ' || inCodContaIni || ' ';
                END IF;

                IF (inCodContaFim is not null and TRIM(inCodContaFim)<>'') THEN
                    stSql := stSql || ' AND plano_analitica.cod_plano <= ' || inCodContaFim || ' ';
                END IF;

                stSql := stSql || '
                  and pagamento.exercicio_liquidacao = nota_liquidacao_paga_anulada.exercicio
                  and pagamento.cod_entidade         = nota_liquidacao_paga_anulada.cod_entidade
                  and pagamento.cod_nota             = nota_liquidacao_paga_anulada.cod_nota
                  and pagamento.timestamp            = nota_liquidacao_paga_anulada.timestamp )
             ';
    end if ;

    stSql := stSql || '  group by nota_liquidacao_paga_anulada.cod_entidade
               , nota_liquidacao_paga_anulada.cod_nota
               , exercicio_liquidacao
               , nota_liquidacao_paga_anulada.timestamp    )';
    EXECUTE stSql;

    stSql := '
        CREATE TEMPORARY TABLE tmp_retorno AS (
        SELECT cod_entidade 
             , cod_empenho 
             , CAST(tbl.exercicio AS VARCHAR)
             , CAST(data_pagamento AS VARCHAR) AS data_pagamento
             , CAST(data_anulacao AS VARCHAR) AS data_anulacao
             , cod_ordem 
             , valor_pago 
             , vl_anulado 
             , tbl.cod_conta || '' - '' ||  coalesce(nome_conta,''NÃO INFORMADO'') AS conta_banco
             , cod_recurso || '' - '' || nom_recurso AS recurso
             , cod_dotacao
             , CAST(cod_elemento_despesa AS VARCHAR) || '' - '' ||  nom_elemento_despesa AS elemento_despesa
             , cod_elemento_despesa
          FROM ( SELECT e.cod_entidade
                      , e.cod_empenho
                      , e.exercicio
                      , to_char(nlp.timestamp,''dd/mm/yyyy'') as data_pagamento
                      , to_char(nota_liquidacao_paga_anulada.timestamp_anulada,''dd/mm/yyyy'') as data_anulacao
                      , sum(nlp.vl_pago) as valor_pago
                      , pl.cod_ordem
                      , tmp.cod_plano as cod_conta
                      , tmp.nom_conta as nome_conta
                      , sum(coalesce(tmp_estornado.vl_anulado,0.00)) as vl_anulado 
                      , ped_d_cd.cod_recurso as cod_recurso
                      , ped_d_cd.nom_recurso as nom_recurso
                      , ped_d_cd.cod_despesa as cod_dotacao
                      , ped_d_cd.cod_estrutural as cod_elemento_despesa 
                      , ped_d_cd.descricao as nom_elemento_despesa
                   FROM empenho.empenho     as e 
                      , empenho.categoria_empenho
                      , empenho.tipo_empenho
                      , empenho.historico   as h 
                      , empenho.nota_liquidacao nl
                      , empenho.nota_liquidacao_paga nlp
                        --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO
                   JOIN tmp_pago as tmp 
                     ON nlp.cod_entidade = tmp.cod_entidade
                    AND nlp.cod_nota = tmp.cod_nota
                    AND nlp.exercicio = tmp.exercicio_liquidacao
                    AND nlp.timestamp = tmp.timestamp
                        --Ligação PAGAMENTO ESTORNADO : PAGAMENTO
              LEFT JOIN tmp_estornado as tmp_estornado 
                     ON tmp_estornado.cod_entidade         = tmp.cod_entidade
                    AND tmp_estornado.cod_nota             = tmp.cod_nota
                    AND tmp_estornado.exercicio_liquidacao = tmp.exercicio_liquidacao
                    AND tmp_estornado.timestamp            = tmp.timestamp
              LEFT JOIN empenho.nota_liquidacao_paga_anulada 
                     ON nota_liquidacao_paga_anulada.cod_entidade = tmp_estornado.cod_entidade
                    AND nota_liquidacao_paga_anulada.cod_nota     = tmp_estornado.cod_nota
                    AND nota_liquidacao_paga_anulada.exercicio    = tmp_estornado.exercicio_liquidacao
                    AND nota_liquidacao_paga_anulada.timestamp    = tmp_estornado.timestamp
                    AND to_date(to_char(nota_liquidacao_paga_anulada.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(''' || stDtInicial || '''::varchar,''dd/mm/yyyy'') AND to_date(''' || stDtFinal || '''::varchar,''dd/mm/yyyy'')

                    AND nota_liquidacao_paga_anulada.timestamp_anulada = ( SELECT MAX(timestamp_anulada) 
                                                                             FROM empenho.nota_liquidacao_paga_anulada nlpa
                                                                            WHERE nlpa.cod_entidade = tmp_estornado.cod_entidade
                                                                              AND nlpa.cod_nota     = tmp_estornado.cod_nota
                                                                              AND nlpa.exercicio    = tmp_estornado.exercicio_liquidacao
                                                                              AND nlpa.timestamp    = tmp_estornado.timestamp )
                      , empenho.pagamento_liquidacao_nota_liquidacao_paga plnlp
                      , empenho.pagamento_liquidacao pl
                      , empenho.nota_liquidacao_conta_pagadora nlcp
                      , empenho.pre_empenho as pe
        LEFT OUTER JOIN ( SELECT ped.exercicio
                               , ped.cod_pre_empenho
                               , d.num_pao
                               , d.num_orgao
                               , d.num_unidade
                               , d.cod_recurso
                               , d.cod_despesa
                               , rec.nom_recurso
                               , rec.cod_detalhamento
                               , rec.masc_recurso_red
                               , cd.cod_estrutural
                               , cd.descricao
                               , cd.cod_conta
                               , ppa.acao.num_acao
                               , programa.num_programa
                            FROM empenho.pre_empenho_despesa as ped
                               , orcamento.despesa           as d
                            JOIN orcamento.recurso('||quote_literal(stExercicio)||') as rec
                              ON rec.cod_recurso = d.cod_recurso
                             AND rec.exercicio = d.exercicio
                            JOIN orcamento.programa_ppa_programa
                              ON programa_ppa_programa.cod_programa = d.cod_programa
                             AND programa_ppa_programa.exercicio   = d.exercicio
                            JOIN ppa.programa
                              ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                            JOIN orcamento.pao_ppa_acao
                              ON pao_ppa_acao.num_pao = d.num_pao
                             AND pao_ppa_acao.exercicio = d.exercicio
                            JOIN ppa.acao 
                              ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                               , orcamento.conta_despesa     as cd
                           WHERE ped.exercicio      = '||quote_literal(stExercicio)||'   
                             AND ped.cod_despesa    = d.cod_despesa
                             AND ped.exercicio      = d.exercicio ';

                        IF ( inCodFuncao is not null AND TRIM(inCodFuncao)<>'') THEN
                            stSql := stSql || ' AND d.cod_funcao = ' || inCodFuncao || ' ';
                        END IF;
    
                        IF ( inCodSubFuncao is not null AND TRIM(inCodSubFuncao)<>'') THEN
                            stSql := stSql || ' AND d.cod_subfuncao = ' || inCodSubFuncao || ' ';
                        END IF;
    
                   stSql := stSql || '
                             AND ped.cod_conta = cd.cod_conta 
                             AND ped.exercicio = cd.exercicio
                      ) as ped_d_cd 
                     ON pe.exercicio = ped_d_cd.exercicio 
                    AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho
                  WHERE e.exercicio         = '||quote_literal(stExercicio)||'
                    AND e.exercicio         = pe.exercicio
                    AND e.cod_pre_empenho   = pe.cod_pre_empenho
                    AND e.cod_entidade      IN ('||stCodEntidades||')
                    AND h.cod_historico     = pe.cod_historico    
                    AND h.exercicio         = pe.exercicio   
                    AND categoria_empenho.cod_categoria = e.cod_categoria
                    AND tipo_empenho.cod_tipo = pe.cod_tipo
        
                    --Ligação EMPENHO : NOTA LIQUIDAÇÂO
                    AND e.exercicio = nl.exercicio_empenho
                    AND e.cod_entidade = nl.cod_entidade
                    AND e.cod_empenho = nl.cod_empenho
        
                    --Ligação NOTA LIQUIDAÇÂO : NOTA LIQUIDAÇÂO PAGA
                    AND nl.exercicio = nlp.exercicio
                    AND nl.cod_nota = nlp.cod_nota
                    AND nl.cod_entidade = nlp.cod_entidade
                    AND to_date(to_char(nlp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(''' || stDtInicial || '''::varchar,''dd/mm/yyyy'') AND to_date(''' || stDtFinal || '''::varchar,''dd/mm/yyyy'')
        
                    --Ligação NOTA LIQUIDAÇÂO PAGA : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA
                    AND nlp.cod_entidade = plnlp.cod_entidade
                    AND nlp.cod_nota = plnlp.cod_nota
                    AND nlp.exercicio = plnlp.exercicio_liquidacao
                    AND nlp.timestamp = plnlp.timestamp
        
                    --Ligação PAGAMENTO LIQUIDACAO : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA
                    AND pl.cod_ordem = plnlp.cod_ordem
                    AND pl.exercicio = plnlp.exercicio
                    AND pl.cod_entidade = plnlp.cod_entidade
                    AND pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
                    AND pl.cod_nota = plnlp.cod_nota

                    AND nlp.cod_entidade = nlcp.cod_entidade
                    AND nlp.cod_nota     = nlcp.cod_nota
                    AND nlp.exercicio    = nlcp.exercicio_liquidacao
                    AND nlp.timestamp    = nlcp.timestamp ';

                if (inCodOrgao is not null and inCodOrgao<>'') then
                    stSql := stSql || ' AND ped_d_cd.num_orgao = '|| inCodOrgao ||' ';
                end if;

                IF (inCodUnidade is not null AND inCodUnidade<>'') THEN
                    stSql := stSql || ' AND ped_d_cd.num_unidade = '|| inCodUnidade ||' ';
                END IF;

                IF (inCodPao is not null AND inCodPao<>'') THEN
                    stSql := stSql || ' AND ped_d_cd.num_acao = '|| inCodPao ||' ';
                END IF;
                
                IF (inCodPrograma IS NOT NULL AND inCodPrograma <> '') THEN
                    stSql := stSql || ' AND ped_d_cd.num_programa = '|| inCodPrograma || ' ';
                END IF;

                IF (inCodRecursoIni is not null AND inCodRecursoIni<>'') THEN
                    stSql := stSql || ' AND ped_d_cd.cod_recurso >= '|| inCodRecursoIni ||' ';
                END IF;

                IF (inCodRecursoFim is not null AND inCodRecursoFim<>'') THEN
                    stSql := stSql || ' AND ped_d_cd.cod_recurso <= '|| inCodRecursoFim ||' ';
                END IF;
                
                IF (inCodDotacao is not null AND inCodDotacao <>'') THEN
                    stSql := stSql || ' AND ped_d_cd.cod_despesa = ' || inCodDotacao || ' ';
                END IF;
                
                IF (stCodElementoDespesaMasc is not null AND stCodElementoDespesaMasc<>'') THEN
                    stSql := stSql || ' AND ped_d_cd.cod_estrutural like publico.fn_mascarareduzida(''' || stCodElementoDespesaMasc || ''')|| ''%'' ';
                END IF;

            stSql := stSql || '
               GROUP BY to_char(nlp.timestamp,''dd/mm/yyyy'')
                      , to_char(nota_liquidacao_paga_anulada.timestamp_anulada,''dd/mm/yyyy'')
                      , pl.cod_ordem
                      , tmp.cod_plano
                      , tmp.nom_conta
                      , e.cod_entidade
                      , e.cod_empenho 
                      , e.exercicio 
                      , ped_d_cd.cod_estrutural 
                      , ped_d_cd.cod_recurso 
                      , ped_d_cd.nom_recurso 
                      , ped_d_cd.cod_despesa
                      , ped_d_cd.descricao
                      , ped_d_cd.cod_conta
               ORDER BY to_date(to_char(nlp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'')
                      , e.cod_entidade 
                      , e.cod_empenho 
                      , e.exercicio
                      , pl.cod_ordem
                      , tmp.cod_plano
                      , tmp.nom_conta
               ) as tbl 
           WHERE valor_pago <> ''0.00''
        ORDER BY to_date(data_pagamento::varchar,''dd/mm/yyyy'')
               , cod_entidade
               , cod_empenho
               , tbl.exercicio
               , cod_ordem
    );
    ';
    EXECUTE stSql;

    stSql := ' SELECT cod_entidade
                    , TO_DATE(data::varchar, ''dd/mm/yyyy'') AS data
                    , cod_empenho
                    , cod_ordem
                    , CAST(empenho AS VARCHAR) AS empenho
                    , exercicio
                    , CAST(valor AS NUMERIC) AS valor
                    , CAST(conta_banco AS VARCHAR) AS conta_banco
                    , CAST(campo_ordenacao AS VARCHAR) AS campo_ordenacao 
                 FROM ( SELECT cod_entidade
                             , ''Pagamento de Empenho Nº '' || cod_empenho || '' OP Nº '' || cod_ordem AS empenho
                             , tmp_retorno.exercicio
                             , data_pagamento AS data
                             , cod_empenho
                             , cod_ordem
                             , valor_pago AS valor
                             , conta_banco 
                             , recurso ';
                            IF (inCodOrdenacao = '1') THEN
                                stSql := stSql || ' , data_pagamento AS campo_ordenacao ';
                            ELSEIF (inCodOrdenacao = '4') THEN
                                stSql := stSql || ' , cod_dotacao || '' - '' || conta_despesa.descricao AS campo_ordenacao ';
                            ELSEIF (inCodOrdenacao = '5' ) THEN
                                stSql := stSql || ' , conta_despesa.cod_estrutural || '' - '' || conta_despesa.descricao AS campo_ordenacao ';
                            ELSE
                                stSql := stSql || ' , ' || stCampo || ' AS campo_ordenacao ';
                            END IF;
                            stSql := stSql || '
                          FROM tmp_retorno ';
                            IF (inCodOrdenacao = '5' OR inCodOrdenacao = '4') THEN
                            stSql := stSql || '
                          JOIN orcamento.conta_despesa
                            ON publico.fn_mascarareduzida(conta_despesa.cod_estrutural) = SUBSTR(tmp_retorno.cod_elemento_despesa, 1, 10)
                           AND publico.fn_nivel(conta_despesa.cod_estrutural) = 5
                           AND conta_despesa.exercicio = tmp_retorno.exercicio
                         WHERE conta_despesa.exercicio = ' || quote_literal(stExercicio) || ' ';
                            END IF;
                        stSql := stSql || '

                     UNION ALL 

                        SELECT cod_entidade 
                             , ''Estorno do Pagamento de Empenho Nº '' || cod_empenho || '' OP Nº '' || cod_ordem AS empenho
                             , tmp_retorno.exercicio 
                             , data_anulacao AS data
                             , cod_empenho
                             , cod_ordem
                             , vl_anulado * (-1) AS valor
                             , conta_banco 
                             , recurso ';
                            IF (inCodOrdenacao = '1') THEN
                                stSql := stSql || ' , data_anulacao AS campo_ordenacao ';
                            ELSEIF (inCodOrdenacao = '4') THEN
                                stSql := stSql || ' , cod_dotacao || '' - '' || conta_despesa.descricao AS campo_ordenacao ';
                            ELSEIF (inCodOrdenacao = '5') THEN
                                stSql := stSql || ', conta_despesa.cod_estrutural || '' - '' || conta_despesa.descricao AS campo_ordenacao ';
                            ELSE
                                stSql := stSql || ' , ' || stCampo || ' AS campo_ordenacao ';
                            END IF;
                            stSql := stSql || '
                          FROM tmp_retorno ';
                            IF (inCodOrdenacao = '5' OR inCodOrdenacao = '4') THEN
                            stSql := stSql || '
                          JOIN orcamento.conta_despesa
                            ON publico.fn_mascarareduzida(conta_despesa.cod_estrutural) = SUBSTR(tmp_retorno.cod_elemento_despesa, 1, 10)
                           AND publico.fn_nivel(conta_despesa.cod_estrutural) = 5
                           AND conta_despesa.exercicio = tmp_retorno.exercicio
                         WHERE conta_despesa.exercicio = ' || quote_literal(stExercicio) || '
                           AND data_anulacao <> '''' ';
                            ELSE 
                            stSql := stSql || '
                         WHERE data_anulacao <> '''' ';
                            END IF;
                        stSql := stSql || '
                    ) AS retorno
             ORDER BY cod_entidade
                    , to_date(data::varchar,''dd/mm/yyyy'')
                    , cod_empenho
                    , cod_ordem
    ';
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_pago;
    DROP TABLE tmp_estornado;
    DROP TABLE tmp_retorno;
          
    RETURN;
END;
$$ LANGUAGE plpgsql;

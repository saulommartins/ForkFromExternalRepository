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
 * Inclui as contas sintéticas e analíticas do plano selecionado
 * Data de Criação   : 13/12/2013

 * @author Desenvolvedor Eduardo Paculski Schitz

 * @package URBEM
 * @subpackage 

 $Id: incluirEscolhaPlanoContas.plsql 64768 2016-03-30 13:48:57Z michel $
*/

CREATE OR REPLACE FUNCTION contabilidade.incluir_escolha_plano_contas(VARCHAR, INTEGER, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio            ALIAS FOR $1;
    inCodUF                ALIAS FOR $2;
    inCodPlano             ALIAS FOR $3;
    stSql                  VARCHAR   := '';
    stSqlUpdate            VARCHAR   := '';
    stSqlInsert            VARCHAR   := '';
    stSqlInsertAnalitica   VARCHAR   := '';
    stSqlClassificacao     VARCHAR   := '';
    stMascara              VARCHAR   := '';
    stTitulo               VARCHAR   := '';
    stFuncao               VARCHAR   := '';
    stAtributoTCEPE        VARCHAR   := '';
    inCount                INTEGER   := 0;
    reRecord               RECORD;
    reRecordClassificacao  RECORD;
BEGIN

    stMascara = selectIntoVarchar('
    SELECT valor
      FROM administracao.configuracao
     WHERE cod_modulo = 9
       AND exercicio = ''' || stExercicio || '''
       AND parametro = ''masc_plano_contas''
    ');

    stSql := '  SELECT  tabela.*
                        , plano_conta.cod_conta
                        , plano_analitica.cod_plano
                        , CASE WHEN plano_conta.cod_estrutural IS NULL THEN
                           ''incluir''
                        ELSE
                           ''alterar''
                        END AS acao
                        , tabela.atributo_tcepe
                        , tabela.atributo_tcemg
                FROM (
                        SELECT  plano_conta_estrutura.titulo
                                , publico.fn_mascara_completa(''' || stMascara || ''', publico.fn_mascarareduzida(plano_conta_estrutura.codigo_estrutural)) AS cod_estrutural
                                , plano_conta_estrutura.funcao
                                , CASE WHEN plano_conta_estrutura.natureza_saldo = ''C'' THEN ''credor''
                                       WHEN plano_conta_estrutura.natureza_saldo = ''D'' THEN ''devedor''
                                       WHEN plano_conta_estrutura.natureza_saldo = ''X'' THEN ''misto''
                                       ELSE ''''
                                END AS natureza_saldo

                                , CASE WHEN plano_conta_estrutura.natureza_saldo = ''C'' THEN ''C''
                                       WHEN plano_conta_estrutura.natureza_saldo = ''D'' THEN ''D''
                                       WHEN plano_conta_estrutura.natureza_saldo = ''X'' THEN ''M''
                                       ELSE ''''
                                END AS natureza_saldo_analitica

                                , CASE WHEN UPPER(plano_conta_estrutura.escrituracao) = ''S'' THEN ''analitica''
                                       ELSE ''sintetica''
                                END AS escrituracao

                                , CASE WHEN plano_conta_estrutura.natureza_informacao = ''P'' THEN 1
                                       WHEN plano_conta_estrutura.natureza_informacao = ''O'' THEN 2
                                       WHEN plano_conta_estrutura.natureza_informacao = ''C'' THEN 3
                                       ELSE 4
                                END AS natureza_informacao

                                , CASE WHEN plano_conta_estrutura.indicador_superavit = ''F'' THEN ''financeiro''
                                       WHEN plano_conta_estrutura.indicador_superavit = ''P'' THEN ''permanente''
                                       WHEN plano_conta_estrutura.indicador_superavit = ''X'' THEN ''misto''
                                       ELSE ''''
                                END AS indicador_superavit

                                , plano_conta_estrutura.atributo_tcepe
                                , plano_conta_estrutura.atributo_tcemg

                        FROM contabilidade.plano_conta_geral

                        JOIN contabilidade.plano_conta_estrutura
                             ON plano_conta_estrutura.cod_uf = plano_conta_geral.cod_uf
                            AND plano_conta_estrutura.cod_plano = plano_conta_geral.cod_plano

                        WHERE plano_conta_geral.cod_plano = ' || inCodPlano || '
                        AND plano_conta_geral.cod_uf = ' || inCodUF || '
                ) AS tabela

                LEFT JOIN ( SELECT publico.fn_mascara_completa(''' || stMascara || ''', publico.fn_mascarareduzida(plano_conta.cod_estrutural)) as codigo_estrutural, *
                              FROM contabilidade.plano_conta
                             WHERE plano_conta.exercicio = ''' || stExercicio || '''
                          ) AS plano_conta
                     ON plano_conta.cod_estrutural = tabela.cod_estrutural
                LEFT JOIN contabilidade.plano_analitica
                     ON plano_analitica.cod_conta = plano_conta.cod_conta
                    AND plano_analitica.exercicio = plano_conta.exercicio
                WHERE NOT EXISTS (  SELECT 1 FROM contabilidade.conta_debito
                                        WHERE conta_debito.exercicio = plano_analitica.exercicio
                                        AND conta_debito.cod_plano = plano_analitica.cod_plano )
                AND NOT EXISTS ( SELECT 1 FROM contabilidade.conta_credito
                                        WHERE conta_credito.exercicio = plano_analitica.exercicio
                                        AND conta_credito.cod_plano = plano_analitica.cod_plano )
                AND NOT EXISTS ( SELECT 1 FROM contabilidade.plano_banco
                                        WHERE plano_banco.exercicio = plano_analitica.exercicio
                                        AND plano_banco.cod_plano = plano_analitica.cod_plano )
                AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_receita
                                        WHERE configuracao_lancamento_receita.exercicio = plano_conta.exercicio
                                        AND configuracao_lancamento_receita.cod_conta = plano_conta.cod_conta )
                AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_credito
                                        WHERE configuracao_lancamento_credito.exercicio = plano_conta.exercicio
                                        AND configuracao_lancamento_credito.cod_conta = plano_conta.cod_conta )
                AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_debito
                                        WHERE configuracao_lancamento_debito.exercicio = plano_conta.exercicio
                                        AND configuracao_lancamento_debito.cod_conta = plano_conta.cod_conta )
                AND( (plano_conta.cod_estrutural NOT LIKE ''1.1.1%''
                AND   plano_conta.cod_estrutural NOT LIKE ''7.2.1.1.1%''
                AND   plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.1%''
                AND   plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.2%''
                AND   plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.3%''
                AND   plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.4%'') OR plano_conta.cod_estrutural IS NULL)
            ORDER BY tabela.cod_estrutural
    ';

    FOR reRecord IN EXECUTE stSql LOOP
        stTitulo := replace(reRecord.titulo, '''', '');
        stFuncao := replace(reRecord.funcao, '''', '');

        IF reRecord.acao = 'alterar' THEN
            stSqlInsert := '
                UPDATE contabilidade.plano_conta
                   SET nom_conta            = ' || quote_literal(stTitulo) || '
                     , cod_sistema          = ' || reRecord.natureza_informacao || '
                     , cod_estrutural       = ' || quote_literal(reRecord.cod_estrutural) || '
                     , escrituracao         = ' || quote_literal(reRecord.escrituracao) || '
                     , natureza_saldo       = ' || quote_literal(reRecord.natureza_saldo) || '
            ';

            IF reRecord.escrituracao = 'analitica' THEN
                stSqlInsert := stSqlInsert || ', indicador_superavit  = ' || quote_literal(reRecord.indicador_superavit) || ' ';
            ELSE
                stSqlInsert := stSqlInsert || ', indicador_superavit  = '''' ';
            END IF;

            IF reRecord.atributo_tcepe IS NULL THEN
                stSqlInsert := stSqlInsert || '
                    , atributo_tcepe = NULL ';
            ELSE
                stSqlInsert := stSqlInsert || '
                    , atributo_tcepe = ' || quote_literal(reRecord.atributo_tcepe) || ' ';
            END IF;

            IF reRecord.atributo_tcemg IS NULL THEN
                stSqlInsert := stSqlInsert || '
                    , atributo_tcemg = NULL ';
            ELSE
                stSqlInsert := stSqlInsert || '
                    , atributo_tcemg = ' || quote_literal(reRecord.atributo_tcemg) || ' ';
            END IF;

            stSqlInsert := stSqlInsert || '
                    , funcao = ' || quote_literal(stFuncao) || '
                 WHERE exercicio = ' || quote_literal(stExercicio) || '
                   AND cod_conta = ' || reRecord.cod_conta || ' ';
        ELSE
            stSqlInsert := '
                INSERT INTO contabilidade.plano_conta
                          ( cod_conta
                          , exercicio
                          , nom_conta
                          , cod_classificacao
                          , cod_sistema
                          , cod_estrutural
                          , escrituracao
                          , natureza_saldo ';
                IF reRecord.escrituracao = 'analitica' THEN
                    stSqlInsert := stSqlInsert || ' , indicador_superavit ';
                END IF;

                stSqlInsert := stSqlInsert || '
                          , funcao
                          , atributo_tcepe
                          , atributo_tcemg ) 
                     VALUES
                          ( (SELECT COALESCE(MAX(cod_conta), 0) +1 FROM contabilidade.plano_conta WHERE exercicio = ' || quote_literal(stExercicio) || ' )
                          , ' || quote_literal(stExercicio) || '
                          , ' || quote_literal(stTitulo) || '
                          , 1
                          , ' || reRecord.natureza_informacao || '
                          , ' || quote_literal(reRecord.cod_estrutural) || '
                          , ' || quote_literal(reRecord.escrituracao)   || '
                          , ' || quote_literal(reRecord.natureza_saldo) || ' ';
                IF reRecord.escrituracao = 'analitica' THEN
                    stSqlInsert := stSqlInsert || ' , ' || quote_literal(reRecord.indicador_superavit) || ' ';
                END IF;

                stSqlInsert := stSqlInsert || ', ' || quote_literal(stFuncao);

                IF reRecord.atributo_tcepe IS NULL THEN
                    stSqlInsert := stSqlInsert || ' , NULL ';
                ELSE
                    stSqlInsert := stSqlInsert || ' , ' || quote_literal(reRecord.atributo_tcepe) ||' ';
                END IF;

                IF reRecord.atributo_tcemg IS NULL THEN
                    stSqlInsert := stSqlInsert || ' , NULL  ); ';
                ELSE
                    stSqlInsert := stSqlInsert || ' , ' || quote_literal(reRecord.atributo_tcemg) ||' ); ';
                END IF;
        END IF;

        EXECUTE stSqlInsert;

        IF reRecord.escrituracao = 'analitica' THEN
            -- Conta Analítica
            IF reRecord.cod_plano IS NOT NULL THEN
                stSqlInsertAnalitica := '
                    UPDATE contabilidade.plano_analitica
                       SET natureza_saldo = ''' || reRecord.natureza_saldo_analitica || '''
                     WHERE exercicio = ''' || stExercicio || '''
                       AND cod_conta =   ' || reRecord.cod_conta || '
                       AND cod_plano =   ' || reRecord.cod_plano || '
                ';
            ELSE
                stSqlInsertAnalitica := '
                    INSERT INTO contabilidade.plano_analitica
                              ( cod_plano
                              , exercicio
                              , cod_conta
                              , natureza_saldo ) 
                         VALUES
                              ( (SELECT COALESCE(MAX(cod_plano), 0) +1 FROM contabilidade.plano_analitica WHERE exercicio = ''' || stExercicio || ''' )
                              , ''' || stExercicio || ''' ';

                IF reRecord.cod_conta IS NOT NULL THEN
                    stSqlInsertAnalitica := stSqlInsertAnalitica || ' , ' || reRecord.cod_conta;
                ELSE
                    stSqlInsertAnalitica := stSqlInsertAnalitica || ' , (SELECT MAX(cod_conta) FROM contabilidade.plano_conta WHERE exercicio = ''' || stExercicio || ''' ) ';
                END IF;

                stSqlInsertAnalitica := stSqlInsertAnalitica || '
                              , ''' || reRecord.natureza_saldo_analitica || '''
                              );
                ';

            END IF; 

            EXECUTE stSqlInsertAnalitica;
        END IF;

        stSqlClassificacao := '
                       SELECT unnest(string_to_array(plano_conta.cod_estrutural, ''.'', '''')) AS cod_classificacao
                            , plano_conta.cod_conta
                            , plano_conta.cod_estrutural
                         FROM contabilidade.plano_conta
                        WHERE plano_conta.exercicio = ''' || stExercicio || '''
                          AND plano_conta.cod_conta=(SELECT MAX(cod_conta) FROM contabilidade.plano_conta WHERE exercicio = ''' || stExercicio || ''')
        ';

        inCount := 1;

        IF reRecord.acao = 'incluir' THEN
            FOR reRecordClassificacao IN EXECUTE stSqlClassificacao LOOP
                stSqlInsert := '
                                INSERT INTO contabilidade.classificacao_plano
                                          ( cod_classificacao
                                          , exercicio
                                          , cod_conta
                                          , cod_posicao )
                                     VALUES
                                          ( ' || reRecordClassificacao.cod_classificacao || '
                                          , ' || quote_literal(stExercicio) || '
                                          , ' || reRecordClassificacao.cod_conta || '
                                          , ' || inCount || ' ); ';

                EXECUTE stSqlInsert;

                inCount := inCount+1;
            END LOOP;
        END IF;
    END LOOP;

    stSql := 'SELECT plano_conta.cod_estrutural
                   , plano_conta.cod_conta
                   , plano_conta.natureza_saldo
                FROM contabilidade.plano_conta
           LEFT JOIN contabilidade.plano_analitica
                  ON plano_analitica.cod_conta = plano_conta.cod_conta
                 AND plano_analitica.exercicio = plano_conta.exercicio
               WHERE plano_conta.exercicio = ''' || stExercicio || '''
            ORDER BY plano_conta.cod_estrutural
    ';

    -- Torna as contas analíticas que possuem filhas em sintéticas
    FOR reRecord IN EXECUTE stSql LOOP
       inCount := selectIntoInteger('SELECT count(cod_conta)
                                       FROM contabilidade.plano_conta
                                      WHERE exercicio = ''' || stExercicio || '''
                                        AND cod_estrutural LIKE ''' || publico.fn_mascarareduzida(reRecord.cod_estrutural) || '%''
                                        AND cod_estrutural <> ''' || reRecord.cod_estrutural || ''' ');

        IF (inCount > 0) THEN
            stSqlUpdate := 'UPDATE contabilidade.plano_conta
                               SET indicador_superavit = ''''
                                 , escrituracao = ''sintetica'' ';
            IF (reRecord.natureza_saldo IS NULL) THEN
                IF (SUBSTR(reRecord.cod_estrutural, 1, 5) = '1.1.1'
                 OR SUBSTR(reRecord.cod_estrutural, 1, 9) = '7.2.1.1.1') THEN
                    stSqlUpdate := stSqlUpdate || ' , natureza_saldo = ''devedor'' ';
                ELSIF (SUBSTR(reRecord.cod_estrutural, 1, 9) = '8.2.1.1.1'
                    OR SUBSTR(reRecord.cod_estrutural, 1, 9) = '8.2.1.1.2'
                    OR SUBSTR(reRecord.cod_estrutural, 1, 9) = '8.2.1.1.3'
                    OR SUBSTR(reRecord.cod_estrutural, 1, 9) = '8.2.1.1.4') THEN
                    stSqlUpdate := stSqlUpdate || ' , natureza_saldo = ''credor''  ';
                END IF;
            END IF;

            stSqlUpdate := stSqlUpdate || '
                             WHERE exercicio = ''' || stExercicio || '''
                               AND cod_estrutural = ''' || reRecord.cod_estrutural || '''
            ';

            EXECUTE stSqlUpdate;

            stSqlUpdate := 'DELETE
          FROM contabilidade.plano_analitica
         WHERE plano_analitica.exercicio = ''' || stExercicio || '''
           AND plano_analitica.cod_plano IN (    SELECT plano_analitica.cod_plano
                                                   FROM contabilidade.plano_conta
                                                   JOIN contabilidade.plano_analitica
                                                     ON plano_analitica.cod_conta = plano_conta.cod_conta
                                                    AND plano_analitica.exercicio = plano_conta.exercicio
                                                  WHERE plano_conta.exercicio = ''' || stExercicio || '''
                                                    AND NOT EXISTS ( SELECT 1 FROM contabilidade.conta_debito
                                                                             WHERE conta_debito.exercicio = plano_analitica.exercicio
                                                                               AND conta_debito.cod_plano = plano_analitica.cod_plano )
                                                    AND NOT EXISTS ( SELECT 1 FROM contabilidade.conta_credito
                                                                             WHERE conta_credito.exercicio = plano_analitica.exercicio
                                                                               AND conta_credito.cod_plano = plano_analitica.cod_plano )
                                                    AND NOT EXISTS ( SELECT 1 FROM contabilidade.plano_banco
                                                                             WHERE plano_banco.exercicio = plano_analitica.exercicio
                                                                               AND plano_banco.cod_plano = plano_analitica.cod_plano )
                                                    AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_receita
                                                                             WHERE configuracao_lancamento_receita.exercicio = plano_conta.exercicio
                                                                               AND configuracao_lancamento_receita.cod_conta = plano_conta.cod_conta )
                                                    AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_credito
                                                                             WHERE configuracao_lancamento_credito.exercicio = plano_conta.exercicio
                                                                               AND configuracao_lancamento_credito.cod_conta = plano_conta.cod_conta )
                                                    AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_debito
                                                                             WHERE configuracao_lancamento_debito.exercicio = plano_conta.exercicio
                                                                               AND configuracao_lancamento_debito.cod_conta = plano_conta.cod_conta )
                                                    AND NOT EXISTS ( SELECT 1 FROM empenho.ordem_pagamento_retencao
                                                                              WHERE ordem_pagamento_retencao.exercicio = plano_analitica.exercicio
                                                                               AND ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano )
                                                    AND NOT EXISTS ( SELECT 1 FROM patrimonio.grupo_plano_depreciacao
                                                                             WHERE grupo_plano_depreciacao.exercicio = plano_analitica.exercicio
                                                                               AND grupo_plano_depreciacao.cod_plano = plano_analitica.cod_plano )
                                                    AND NOT EXISTS ( SELECT 1 FROM empenho.responsavel_adiantamento
                                                                             WHERE responsavel_adiantamento.exercicio        = plano_analitica.exercicio
                                                                               AND responsavel_adiantamento.conta_lancamento = plano_analitica.cod_plano )
                                                    AND NOT EXISTS ( SELECT 1 FROM empenho.contrapartida_responsavel
                                                                             WHERE contrapartida_responsavel.exercicio           = plano_analitica.exercicio
                                                                               AND contrapartida_responsavel.conta_contrapartida = plano_analitica.cod_plano )
                                                    AND NOT EXISTS ( SELECT 1 FROM tesouraria.recibo_extra
                                                                             WHERE recibo_extra.exercicio = plano_analitica.exercicio
                                                                               AND recibo_extra.cod_plano = plano_analitica.cod_plano )
                                                    AND plano_conta.cod_estrutural NOT LIKE ''1.1.1%''
                                                    AND plano_conta.cod_estrutural NOT LIKE ''7.2.1.1.1%''
                                                    AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.1%''
                                                    AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.2%''
                                                    AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.3%''
                                                    AND plano_conta.cod_estrutural NOT LIKE ''8.2.1.1.4%''
                                                    AND plano_analitica.cod_conta = '|| reRecord.cod_conta || '
                                        )';

            EXECUTE stSqlUpdate;

        ELSE
            stSqlUpdate := 'UPDATE contabilidade.plano_conta
                               SET escrituracao = ''analitica'' ';

            IF (reRecord.natureza_saldo IS NULL) THEN
                IF (SUBSTR(reRecord.cod_estrutural, 1, 5) = '1.1.1'
                 OR SUBSTR(reRecord.cod_estrutural, 1, 9) = '7.2.1.1.1') THEN
                    stSqlUpdate := stSqlUpdate || ' , natureza_saldo = ''devedor'' ';
                ELSIF (SUBSTR(reRecord.cod_estrutural, 1, 9) = '8.2.1.1.1'
                    OR SUBSTR(reRecord.cod_estrutural, 1, 9) = '8.2.1.1.2'
                    OR SUBSTR(reRecord.cod_estrutural, 1, 9) = '8.2.1.1.3'
                    OR SUBSTR(reRecord.cod_estrutural, 1, 9) = '8.2.1.1.4') THEN
                    stSqlUpdate := stSqlUpdate || ' , natureza_saldo = ''credor''  ';
                END IF;
            END IF;

            stSqlUpdate := stSqlUpdate || '
                             WHERE exercicio = ''' || stExercicio || '''
                               AND cod_estrutural = ''' || reRecord.cod_estrutural || '''
            ';

            EXECUTE stSqlUpdate;

        END IF;
    END LOOP;

    stSqlUpdate := 'DELETE FROM contabilidade.posicao_plano where exercicio = ''' || stExercicio || ''';';

    EXECUTE stSqlUpdate;

    stSql := '    SELECT unnest(string_to_array(valor, ''.'', '''')) AS posicao_plano
                    FROM administracao.configuracao
                   WHERE cod_modulo = 9
                     AND exercicio = ''' || stExercicio || '''
                     AND parametro = ''masc_plano_contas'' ';

    inCount := 1;

    FOR reRecord IN EXECUTE stSql LOOP
        stSqlInsert := '
                        INSERT INTO contabilidade.posicao_plano
                                  ( exercicio
                                  , cod_posicao
                                  , mascara )
                             VALUES
                                  ( ' || quote_literal(stExercicio) || '
                                  , ' || inCount || '
                                  , ' || quote_literal(reRecord.posicao_plano) || ' ); ';

        EXECUTE stSqlInsert;

        inCount := inCount+1;
    END LOOP;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';

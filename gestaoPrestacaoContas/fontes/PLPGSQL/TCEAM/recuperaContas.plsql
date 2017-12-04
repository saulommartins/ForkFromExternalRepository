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
* $Author: Eduardo Paculski Schitz
* $Date: 24/03/2011
*/

CREATE OR REPLACE FUNCTION tceam.recupera_contas(VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio          ALIAS FOR $1;
    stCodEntidade        ALIAS FOR $2;
    stMes                ALIAS FOR $3;
    stSql                VARCHAR   := '';
    stSqlComplemento     VARCHAR   := '';
    stCodEntidadeArquivo VARCHAR   := '';
    reRegistro           RECORD;
    arRetorno            NUMERIC[];
    inCodConta           INTEGER;
BEGIN

    stSql := 'CREATE TEMPORARY TABLE tmp_debito AS
                SELECT *
                FROM (
                    SELECT
                         pc.cod_estrutural
                        ,pa.cod_plano
                        ,vl.vl_lancamento
                        ,vl.cod_entidade
                        ,lo.dt_lote
                        ,lo.exercicio
                        ,lo.tipo
                        ,vl.tipo_valor
                    FROM
                         contabilidade.plano_conta            as pc
                        ,contabilidade.plano_analitica        as pa
                        ,contabilidade.conta_debito           as cd
                        ,contabilidade.valor_lancamento       as vl
                        ,contabilidade.lancamento             as la
                        ,contabilidade.lote                   as lo
                        ,contabilidade.sistema_contabil       as sc
                    WHERE   pc.cod_conta    = pa.cod_conta
                    AND     pc.exercicio    = pa.exercicio
                    AND     pa.cod_plano    = cd.cod_plano
                    AND     pa.exercicio    = cd.exercicio
                    AND     cd.cod_lote     = vl.cod_lote
                    AND     cd.tipo         = vl.tipo
                    AND     cd.sequencia    = vl.sequencia
                    AND     cd.exercicio    = vl.exercicio
                    AND     cd.tipo_valor   = vl.tipo_valor
                    AND     cd.cod_entidade = vl.cod_entidade
                    AND     vl.cod_lote     = la.cod_lote
                    AND     vl.tipo         = la.tipo
                    AND     vl.sequencia    = la.sequencia
                    AND     vl.exercicio    = la.exercicio
                    AND     vl.cod_entidade = la.cod_entidade
                    AND     vl.tipo_valor   = ''D''
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     pa.exercicio = ''' || stExercicio || '''
                    AND     sc.cod_sistema  = pc.cod_sistema
                    AND     sc.exercicio    = pc.exercicio
                    ORDER BY pc.cod_estrutural
                  ) as tabela
              WHERE cod_entidade IN ('||stCodEntidade||') 
                AND substr(cod_estrutural,1,1)::integer in (1, 2, 3, 4, 5, 6, 9) ';

    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_credito AS
                SELECT *
                FROM (
                    SELECT
                         pc.cod_estrutural
                        ,pa.cod_plano
                        ,vl.vl_lancamento
                        ,vl.cod_entidade
                        ,lo.dt_lote
                        ,lo.exercicio
                        ,lo.tipo
                        ,vl.tipo_valor
                    FROM
                         contabilidade.plano_conta       as pc
                        ,contabilidade.plano_analitica   as pa
                        ,contabilidade.conta_credito     as cc
                        ,contabilidade.valor_lancamento  as vl
                        ,contabilidade.lancamento        as la
                        ,contabilidade.lote              as lo
                        ,contabilidade.sistema_contabil  as sc
                    WHERE   pc.cod_conta    = pa.cod_conta
                    AND     pc.exercicio    = pa.exercicio
                    AND     pa.cod_plano    = cc.cod_plano
                    AND     pa.exercicio    = cc.exercicio
                    AND     cc.cod_lote     = vl.cod_lote
                    AND     cc.tipo         = vl.tipo
                    AND     cc.sequencia    = vl.sequencia
                    AND     cc.exercicio    = vl.exercicio
                    AND     cc.tipo_valor   = vl.tipo_valor
                    AND     cc.cod_entidade = vl.cod_entidade
                    AND     vl.cod_lote     = la.cod_lote
                    AND     vl.tipo         = la.tipo
                    AND     vl.sequencia    = la.sequencia
                    AND     vl.exercicio    = la.exercicio
                    AND     vl.cod_entidade = la.cod_entidade
                    AND     vl.tipo_valor   = ''C''
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     pa.exercicio = ''' || stExercicio || '''
                    AND     sc.cod_sistema  = pc.cod_sistema
                    AND     sc.exercicio    = pc.exercicio

                    ORDER BY pc.cod_estrutural
                  ) as tabela
              WHERE cod_entidade::integer IN ('||stCodEntidade||') 
                AND substr(cod_estrutural,1,1)::integer in (1, 2, 3, 4, 5, 6, 9) ';

    EXECUTE stSql;


    stSql := '
    CREATE TEMPORARY TABLE tmp_totaliza_debito AS
        SELECT *
          FROM tmp_debito
         WHERE to_char(dt_lote,''mm'') = '''||stMes||'''
           AND tipo <> ''I''
    ';

    EXECUTE stSql;

    stSql := '
    CREATE TEMPORARY TABLE tmp_totaliza_credito AS
        SELECT *
          FROM tmp_credito
         WHERE to_char(dt_lote,''mm'') = '''||stMes||'''
           AND tipo <> ''I''
    ';

    EXECUTE stSql;

    stSqlComplemento := 'to_char(dt_lote,''mm'') = '''||stMes||''' AND tipo = ''I'' ';

    stSql := 'CREATE TEMPORARY TABLE tmp_totaliza AS
        SELECT * FROM tmp_debito
        WHERE
             ' || stSqlComplemento || '
       UNION
        SELECT * FROM tmp_credito
        WHERE
             ' || stSqlComplemento;

    EXECUTE stSql;

    stSql := ' SELECT CAST(plano_conta.exercicio AS VARCHAR) AS exercicio
                    , plano_conta.cod_estrutural
                    , plano_conta.cod_conta
                    , CAST(SUBSTR(REPLACE(plano_conta.cod_estrutural,''.'',''''),1,15) AS VARCHAR) AS conta_contabil
                    , plano_conta.nom_conta
                    , publico.fn_nivel(plano_conta.cod_estrutural) AS nivel
                    , CAST(CASE WHEN plano_analitica.cod_plano IS NULL THEN
                          ''N''
                      ELSE
                          ''S''
                      END AS VARCHAR) AS recebe_lancamento
                    , CAST(COALESCE(plano_analitica.natureza_saldo, ''D'') AS VARCHAR) AS origem_saldo
                    , CAST(RPAD(REPLACE(publico.substring_estrutural(plano_conta.cod_estrutural, ''.'', (publico.fn_nivel(plano_conta.cod_estrutural) - 1)),''.'',''''), 15, ''0'') AS VARCHAR) AS conta_superior
                    , CAST('''' AS VARCHAR) AS cod_conta_reduzido
                    , CAST(CASE WHEN SUBSTR(plano_conta.cod_estrutural, 1, 1) = ''4'' AND plano_analitica.cod_plano IS NOT NULL THEN
                        CAST(''0''||SUBSTR(SUBSTR(REPLACE(plano_conta.cod_estrutural,''.'',''''),1,15), 2) AS VARCHAR)
                      ELSE
                        ''''
                      END AS VARCHAR) AS item_orcamentario
                    , CAST(SUBSTR(banco.num_banco,1,3) AS VARCHAR) AS banco
                    , CAST(REPLACE(UPPER(LPAD(TRIM(REPLACE(agencia.num_agencia,''-'','''')),6,''0'')),''X'',''0'') AS VARCHAR) AS agencia
                    , CAST(TRIM(UPPER(conta_corrente.num_conta_corrente)) AS VARCHAR) as conta_corrente
                    , CASE WHEN ( EXISTS ( SELECT  1
                                             FROM  contabilidade.plano_analitica
                                            WHERE  plano_analitica.exercicio = plano_banco.exercicio
                                              AND  plano_analitica.cod_plano = plano_banco.cod_plano
                                                )
                                        ) THEN 1
                      ELSE
                          CASE WHEN SUBSTR(REPLACE(plano_conta.cod_estrutural,''.'',''''),1,14) like ''4%''   THEN 2 /*Receita*/      
                               WHEN SUBSTR(REPLACE(plano_conta.cod_estrutural,''.'',''''),1,14) like ''3%''   THEN 3 /*Despesa*/      
                          ELSE 9 /*Outras Contas Contábeis*/
                          END
                      END AS tipo_conta
                    , elenco_contas_tce.seq AS cod_conta_tc
                 FROM contabilidade.plano_conta
            LEFT JOIN contabilidade.plano_analitica
                   ON plano_analitica.exercicio = plano_conta.exercicio
                  and plano_analitica.cod_conta = plano_conta.cod_conta
            LEFT JOIN contabilidade.plano_banco
                   ON plano_banco.exercicio    = plano_analitica.exercicio 
                  AND plano_banco.cod_plano    = plano_analitica.cod_plano
            LEFT JOIN monetario.conta_corrente
                   ON conta_corrente.cod_banco          = plano_banco.cod_banco
                  AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                  AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
            LEFT JOIN monetario.agencia
                   ON agencia.cod_banco   = conta_corrente.cod_banco
                  AND agencia.cod_agencia = conta_corrente.cod_agencia
            LEFT JOIN monetario.banco
                   ON banco.cod_banco = agencia.cod_banco
            LEFT JOIN tceam.vinculo_elenco_plano_contas
                   ON vinculo_elenco_plano_contas.cod_plano       = plano_analitica.cod_plano
                  AND vinculo_elenco_plano_contas.exercicio_plano = plano_analitica.exercicio
            LEFT JOIN tceam.elenco_contas_tce
                   ON elenco_contas_tce.cod_elenco = vinculo_elenco_plano_contas.cod_elenco
                  AND elenco_contas_tce.exercicio  = vinculo_elenco_plano_contas.exercicio_elenco

                WHERE (NOT EXISTS (
                          SELECT 1
                            FROM tceam.arquivo_contas
                           WHERE arquivo_contas.cod_conta = plano_conta.cod_conta
                             AND arquivo_contas.exercicio = plano_conta.exercicio
                             AND arquivo_contas.mes < '''||stMes||''' 
                             AND arquivo_contas.cod_entidade::integer IN (' || stCodEntidade || '))
                  AND plano_conta.exercicio = ''' || stExercicio || ''') 
                  AND ( plano_banco.cod_plano IS NULL
                   OR ( plano_banco.cod_plano IS NOT NULL AND plano_banco.cod_entidade IN (' || stCodEntidade || ') ))


             ORDER BY plano_conta.cod_estrutural ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        arRetorno := tceam.totaliza_contas(publico.fn_mascarareduzida(reRegistro.cod_estrutural));
        IF ((( arRetorno[1] <> 0.00 ) OR
           ( arRetorno[2] <> 0.00 ) OR
           ( arRetorno[3] <> 0.00 )) OR reRegistro.tipo_conta = 1)
        THEN
            IF (STRPOS(stCodEntidade, ',') <> 0) THEN
                stCodEntidadeArquivo := selectIntoVarchar('SELECT valor 
                                                             FROM administracao.configuracao 
                                                            WHERE cod_modulo = 8 
                                                              AND exercicio = '''||stExercicio||''' 
                                                              AND parametro = ''cod_entidade_prefeitura'' ');
            ELSE
                stCodEntidadeArquivo := stCodEntidade;
            END IF;

            SELECT cod_conta
              INTO inCodConta
              FROM tceam.arquivo_contas
             WHERE arquivo_contas.cod_conta    = reRegistro.cod_conta
               AND arquivo_contas.exercicio    = stExercicio
               AND arquivo_contas.cod_entidade = stCodEntidadeArquivo::integer;

            IF (inCodConta IS NULL) THEN
                INSERT INTO tceam.arquivo_contas (cod_conta, exercicio, mes, cod_entidade) VALUES (reRegistro.cod_conta, stExercicio, stMes, stCodEntidadeArquivo::integer);
            END IF;

            RETURN NEXT reRegistro;
        END IF;
    END LOOP;

    DROP TABLE tmp_totaliza;
    DROP TABLE tmp_debito;
    DROP TABLE tmp_credito;
    DROP TABLE tmp_totaliza_debito;
    DROP TABLE tmp_totaliza_credito;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';

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
CREATE OR REPLACE FUNCTION relatorioConfiguracaoLancamentoReceita ( character varying, character varying ) RETURNS SETOF RECORD AS $$
DECLARE

    stExercicio ALIAS FOR $1;
    stCodClassificacao ALIAS FOR $2;

    stSql VARCHAR := '';
    reRecord RECORD;

BEGIN

    stSql := 'SELECT configuracao_lancamento_receita.cod_conta
                    , plano_conta.cod_estrutural AS estrutural_plano_conta
                    , ''Arrecadação''::varchar AS tipo_arrecadacao
                    , CASE WHEN configuracao_lancamento_receita.estorno = ''true'' THEN
                           ''Débito''::varchar
                      ELSE
                           ''Crédito''::varchar
                      END AS tipo_conta
                    , configuracao_lancamento_receita.cod_conta_receita
                    , conta_receita.cod_estrutural AS estrutural_receita
                    , conta_receita.descricao AS descricao_receita
                    , plano_conta.nom_conta AS nom_conta
                 FROM contabilidade.configuracao_lancamento_receita
           INNER JOIN contabilidade.plano_conta
                   ON configuracao_lancamento_receita.cod_conta = plano_conta.cod_conta
                  AND configuracao_lancamento_receita.exercicio = plano_conta.exercicio
           INNER JOIN orcamento.conta_receita
                   ON configuracao_lancamento_receita.cod_conta_receita = conta_receita.cod_conta
                  AND configuracao_lancamento_receita.exercicio = conta_receita.exercicio
                WHERE configuracao_lancamento_receita.estorno = ''false'' ';
                  
    IF stExercicio <> '' THEN
        stSql := stSql || ' AND configuracao_lancamento_receita.exercicio = '|| quote_literal(stExercicio) ||' ';
    END IF;

    IF stCodClassificacao <> '' THEN
        IF stCodClassificacao = '9' OR stCodClassificacao = '8' OR stCodClassificacao = '7' OR stCodClassificacao = '6'
        OR stCodClassificacao = '5' OR stCodClassificacao = '4' OR stCodClassificacao = '3' OR stCodClassificacao = '2'
        OR stCodClassificacao = '1' THEN
            stCodClassificacao := stCodClassificacao ||'.%';
            stSql := stSql || ' AND conta_receita.cod_estrutural LIKE '|| quote_literal(stCodClassificacao) ||' ';
        ELSE
            stSql := stSql || ' AND conta_receita.cod_estrutural LIKE publico.fn_mascarareduzida('|| quote_literal(stCodClassificacao) ||')||''%''';
        END IF;
    END IF;
   
    FOR reRecord IN EXECUTE stSql
    LOOP
        RETURN NEXT reRecord;
    END LOOP;

END;
$$ language 'plpgsql';

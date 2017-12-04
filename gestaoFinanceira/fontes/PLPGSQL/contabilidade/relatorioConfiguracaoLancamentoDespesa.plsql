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
CREATE OR REPLACE FUNCTION relatorioConfiguracaoLancamentoDespesa ( character varying, character varying, character varying ) RETURNS SETOF RECORD AS $$
DECLARE

    stExercicio ALIAS FOR $1;
    stTipoLancamento ALIAS FOR $2;
    stCodClassificacao ALIAS FOR $3;
    
    stSql VARCHAR := '';
    reRecord RECORD;

BEGIN

    stSql := ' SELECT configuracao_lancamento_debito.cod_conta_despesa as cod_conta_despesa
             , plano_conta_debito.cod_estrutural AS estrutural_debito
             , plano_conta_credito.cod_estrutural AS estrutural_credito
             , conta_despesa.cod_estrutural AS estrutural_despesa
             , conta_despesa.descricao AS descricao_despesa
             , configuracao_lancamento_credito.tipo::varchar AS tipo_lancamento
             , CASE WHEN configuracao_lancamento_credito.tipo = ''empenho'' THEN
                  ''Empenho''::varchar
               WHEN configuracao_lancamento_credito.tipo = ''liquidacao'' THEN
                  ''Liquidação''::varchar
               WHEN configuracao_lancamento_credito.tipo = ''almoxarifado'' THEN
                  ''Almoxarifado''::varchar
               END AS lancamento
             , plano_conta_credito.nom_conta AS nom_conta_credito
             , plano_conta_debito.nom_conta AS nom_conta_debito
          FROM contabilidade.configuracao_lancamento_credito
    INNER JOIN contabilidade.configuracao_lancamento_debito
            ON configuracao_lancamento_credito.exercicio = configuracao_lancamento_debito.exercicio
           AND configuracao_lancamento_credito.cod_conta_despesa = configuracao_lancamento_debito.cod_conta_despesa
           AND configuracao_lancamento_credito.tipo = configuracao_lancamento_debito.tipo
           AND configuracao_lancamento_credito.estorno = configuracao_lancamento_debito.estorno
    INNER JOIN contabilidade.plano_conta plano_conta_credito
            ON plano_conta_credito.cod_conta = configuracao_lancamento_credito.cod_conta
           AND plano_conta_credito.exercicio = configuracao_lancamento_credito.exercicio
    INNER JOIN contabilidade.plano_analitica plano_analitica_credito
            ON plano_conta_credito.cod_conta = plano_analitica_credito.cod_conta
           AND plano_conta_credito.exercicio = plano_analitica_credito.exercicio
    INNER JOIN contabilidade.plano_conta plano_conta_debito
            ON plano_conta_debito.cod_conta = configuracao_lancamento_debito.cod_conta
           AND plano_conta_debito.exercicio = configuracao_lancamento_debito.exercicio
    INNER JOIN contabilidade.plano_analitica plano_analitica_debito
            ON plano_conta_debito.cod_conta = plano_analitica_debito.cod_conta
           AND plano_conta_debito.exercicio = plano_analitica_debito.exercicio
    INNER JOIN orcamento.conta_despesa
            ON configuracao_lancamento_credito.cod_conta_despesa = conta_despesa.cod_conta
           AND configuracao_lancamento_credito.exercicio = conta_despesa.exercicio
         WHERE configuracao_lancamento_credito.estorno = ''false''
    ';

    IF stExercicio <> '' THEN
        stSql := stSql || ' AND configuracao_lancamento_credito.exercicio = '|| quote_literal(stExercicio) ||' ';
    END IF;

    IF stTipoLancamento <> '' THEN
        stSql := stSql || ' AND configuracao_lancamento_credito.tipo = '|| quote_literal(stTipoLancamento) ||' ';
    END IF;

    IF stCodClassificacao <> '' THEN
        stSql := stSql || ' AND conta_despesa.cod_estrutural = '|| quote_literal(stCodClassificacao) ||' ';
    END IF;

    FOR reRecord IN EXECUTE stSql
    LOOP
        RETURN NEXT reRecord;
    END LOOP;
    
END;
$$ language 'plpgsql';

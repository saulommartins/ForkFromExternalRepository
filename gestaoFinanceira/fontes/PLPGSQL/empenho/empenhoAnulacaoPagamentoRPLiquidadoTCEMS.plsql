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
CREATE OR REPLACE FUNCTION EmpenhoAnulacaoPagamentoRPLiquidadoTCEMS ( character varying, numeric, character varying, integer, character varying, integer, integer, character varying, character varying, character varying ) RETURNS INTEGER AS $$
DECLARE
EXERCICIO ALIAS FOR $1;
VALOR ALIAS FOR $2;
COMPLEMENTO ALIAS FOR $3;
CODLOTE ALIAS FOR $4;
TIPOLOTE ALIAS FOR $5;
CODENTIDADE ALIAS FOR $6;
CODNOTA ALIAS FOR $7;
CONTAPG ALIAS FOR $8;
EXERCICIORP ALIAS FOR $9;
EXERCICIOLIQUIDACAO ALIAS FOR $10;

    SEQEXERCICIO INTEGER;
    SEQUENCIA INTEGER;
    inCodDespesa INTEGER;
    inContConfiguracao INTEGER := 0;
    boImplantado BOOLEAN;
    SqlContaFixa VARCHAR := '';
    SqlContaLiq VARCHAR := '';
    SqlContaPg VARCHAR := '';
    StContaDeb VARCHAR := '';
    StContaCred VARCHAR := '';
    StContaPgCred VARCHAR := '';
    ReContaFixa RECORD;
    ReContaLiq RECORD;
    ReContaPg RECORD;
    stConfiguracaoEntidade VARCHAR := '';
BEGIN

    inCodDespesa := selectIntoInteger(' SELECT despesa.cod_despesa
                                          FROM empenho.nota_liquidacao
                                    INNER JOIN empenho.empenho
                                            ON empenho.cod_empenho  = nota_liquidacao.cod_empenho
                                           AND empenho.exercicio    = nota_liquidacao.exercicio_empenho
                                           AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                    INNER JOIN empenho.pre_empenho
                                            ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                                           AND pre_empenho.exercicio       = empenho.exercicio
                                    INNER JOIN empenho.pre_empenho_despesa
                                            ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                           AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                                    INNER JOIN orcamento.despesa
                                            ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                                           AND despesa.exercicio   = pre_empenho_despesa.exercicio
                                         WHERE nota_liquidacao.cod_nota = ' || CODNOTA || '
                                           AND nota_liquidacao.exercicio = '''||EXERCICIOLIQUIDACAO||'''
                                           AND nota_liquidacao.cod_entidade = ' || CODENTIDADE || '
                                           ');

    boImplantado := selectIntoBoolean(' SELECT pre_empenho.implantado
                                          FROM empenho.nota_liquidacao
                                    INNER JOIN empenho.empenho
                                            ON empenho.cod_empenho  = nota_liquidacao.cod_empenho
                                           AND empenho.exercicio    = nota_liquidacao.exercicio_empenho
                                           AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                    INNER JOIN empenho.pre_empenho
                                            ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                                           AND pre_empenho.exercicio       = empenho.exercicio
                                         WHERE nota_liquidacao.cod_nota = ' || CODNOTA || '
                                           AND nota_liquidacao.exercicio = '''||EXERCICIOLIQUIDACAO||'''
                                           AND nota_liquidacao.cod_entidade = ' || CODENTIDADE || '
                                           ');

    IF EXERCICIOLIQUIDACAO < EXERCICIO THEN
        StContaDeb := '632200000';
        StContaCred := '632100000';
        StContaPgCred := '213110200';
    END IF;

    IF EXERCICIOLIQUIDACAO = EXERCICIO THEN
        stConfiguracaoEntidade := selectIntoVarchar('
        SELECT parametro
          FROM administracao.configuracao
         WHERE exercicio = '''||EXERCICIO||'''
           AND parametro ILIKE ''cod_entidade%''
           AND valor = '''||CODENTIDADE||'''
        ');

        StContaDeb := '631400000';
        StContaCred := '631300000';

        IF EXERCICIO::INTEGER < 2014 THEN
            IF stConfiguracaoEntidade = 'cod_entidade_rpps' THEN
                StContaPgCred := '213110503';
            ELSIF stConfiguracaoEntidade = 'cod_entidade_camara' THEN
                StContaPgCred := '213110502';
            ELSE
                StContaPgCred := '213110501';
            END IF;
        ELSE
            StContaPgCred := '2131102';
        END IF;
    END IF;

    SqlContaLiq := '
        SELECT debito.cod_estrutural AS estrutural_debito
             , credito.cod_estrutural AS estrutural_credito
             , debito.cod_plano AS plano_debito
             , credito.cod_plano AS plano_credito
             , debito.exercicio
          FROM (
                 SELECT plano_conta.cod_estrutural
                      , plano_analitica.cod_plano
                      , plano_conta.exercicio
                      , plano_conta.escrituracao
                   FROM contabilidade.plano_conta
             INNER JOIN contabilidade.plano_analitica
                     ON plano_conta.cod_conta = plano_analitica.cod_conta
                    AND plano_conta.exercicio = plano_analitica.exercicio
                  WHERE REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE '''||StContaDeb||'%''
               ) AS debito
    INNER JOIN (
                 SELECT plano_conta.cod_estrutural
                      , plano_analitica.cod_plano
                      , plano_conta.exercicio
                      , plano_conta.escrituracao
                   FROM contabilidade.plano_conta
             INNER JOIN contabilidade.plano_analitica
                     ON plano_conta.cod_conta = plano_analitica.cod_conta
                    AND plano_conta.exercicio = plano_analitica.exercicio
                  WHERE REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE '''||StContaCred||'%''
               ) AS credito
             ON debito.exercicio = credito.exercicio
          WHERE debito.exercicio = '''||EXERCICIO||'''
    ';

    FOR ReContaLiq IN EXECUTE SqlContaLiq
    LOOP
        SEQUENCIA := FazerLancamento( ReContaLiq.estrutural_debito , ReContaLiq.estrutural_credito , 920 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE, ReContaLiq.plano_debito, ReContaLiq.plano_credito );
    END LOOP;

    IF boImplantado = FALSE THEN
        SqlContaPg := '
                    SELECT REPLACE(plano_analitica_debito.cod_plano::VARCHAR, ''.'', '''')::integer AS conta_credito
                         , (SELECT plano_analitica.cod_plano 
                              FROM contabilidade.plano_conta 
                              JOIN contabilidade.plano_analitica
                                ON plano_analitica.cod_conta = plano_conta.cod_conta
                               AND plano_analitica.exercicio = plano_conta.exercicio
                             WHERE plano_conta.exercicio = '''||EXERCICIO||''' 
                               AND REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE REPLACE('''||CONTAPG||'%'', ''.'', '''')
                               ) AS conta_debito
                         , configuracao_lancamento_debito.cod_conta_despesa
                         , REPLACE(plano_conta_debito.cod_estrutural, ''.'', '''') as estrutural_credito
                         , (SELECT plano_conta.cod_estrutural
                              FROM contabilidade.plano_conta 
                              JOIN contabilidade.plano_analitica
                                ON plano_analitica.cod_conta = plano_conta.cod_conta
                               AND plano_analitica.exercicio = plano_conta.exercicio
                             WHERE plano_conta.exercicio = '''||Exercicio||''' 
                               AND REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE REPLACE('''||CONTAPG||'%'', ''.'', '''')
                               ) AS estrutural_debito
                      FROM empenho.nota_liquidacao
                INNER JOIN empenho.empenho
                        ON empenho.cod_empenho  = nota_liquidacao.cod_empenho
                       AND empenho.exercicio    = nota_liquidacao.exercicio_empenho
                       AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                INNER JOIN empenho.pre_empenho
                        ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                       AND pre_empenho.exercicio       = empenho.exercicio
                INNER JOIN empenho.pre_empenho_despesa
                        ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                       AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                INNER JOIN orcamento.conta_despesa
                        ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                       AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                INNER JOIN contabilidade.configuracao_lancamento_credito
                        ON configuracao_lancamento_credito.cod_conta_despesa = conta_despesa.cod_conta
                       AND configuracao_lancamento_credito.exercicio         = '''||EXERCICIO||'''
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
                     WHERE configuracao_lancamento_credito.estorno = ''true''
                       AND configuracao_lancamento_credito.exercicio = '''||EXERCICIO||'''
                       AND configuracao_lancamento_credito.tipo = ''liquidacao''
                       AND nota_liquidacao.cod_nota = ' || CODNOTA || '
                       AND nota_liquidacao.exercicio = '''||EXERCICIOLIQUIDACAO||'''
                       AND nota_liquidacao.cod_entidade = ' || CODENTIDADE || '
                       AND nota_liquidacao.cod_empenho::TEXT =  split_part(''' || COMPLEMENTO || ''',''/'', 1)
                       AND nota_liquidacao.exercicio_empenho::TEXT =  split_part(''' || COMPLEMENTO || ''',''/'', 2);
            ';
    ELSE
        SqlContaPg := '
                    SELECT REPLACE(plano_analitica_debito.cod_plano::VARCHAR, ''.'', '''')::integer AS conta_credito
                         , (SELECT plano_analitica.cod_plano 
                              FROM contabilidade.plano_conta 
                              JOIN contabilidade.plano_analitica
                                ON plano_analitica.cod_conta = plano_conta.cod_conta
                               AND plano_analitica.exercicio = plano_conta.exercicio
                             WHERE plano_conta.exercicio = '''||EXERCICIO||''' 
                               AND REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE REPLACE('''||CONTAPG||'%'', ''.'', '''')
                               ) AS conta_debito
                         , configuracao_lancamento_debito.cod_conta_despesa
                         , REPLACE(plano_conta_debito.cod_estrutural, ''.'', '''') as estrutural_credito
                         , (SELECT plano_conta.cod_estrutural
                              FROM contabilidade.plano_conta 
                              JOIN contabilidade.plano_analitica
                                ON plano_analitica.cod_conta = plano_conta.cod_conta
                               AND plano_analitica.exercicio = plano_conta.exercicio
                             WHERE plano_conta.exercicio = '''||Exercicio||''' 
                               AND REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE REPLACE('''||CONTAPG||'%'', ''.'', '''')
                               ) AS estrutural_debito
                      FROM empenho.nota_liquidacao
                INNER JOIN empenho.empenho
                        ON empenho.cod_empenho  = nota_liquidacao.cod_empenho
                       AND empenho.exercicio    = nota_liquidacao.exercicio_empenho
                       AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                INNER JOIN empenho.pre_empenho
                        ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                       AND pre_empenho.exercicio       = empenho.exercicio
                INNER JOIN empenho.restos_pre_empenho
                        ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                       AND restos_pre_empenho.exercicio       = pre_empenho.exercicio
                INNER JOIN orcamento.conta_despesa
                        ON REPLACE(conta_despesa.cod_estrutural, ''.'', '''') = restos_pre_empenho.cod_estrutural
                       AND conta_despesa.exercicio = '''||EXERCICIO||'''
                INNER JOIN contabilidade.configuracao_lancamento_credito
                        ON configuracao_lancamento_credito.cod_conta_despesa = conta_despesa.cod_conta
                       AND configuracao_lancamento_credito.exercicio         = '''||EXERCICIO||'''
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
                     WHERE configuracao_lancamento_credito.estorno = ''true''
                       AND configuracao_lancamento_credito.exercicio = '''||EXERCICIO||'''
                       AND configuracao_lancamento_credito.tipo = ''liquidacao''
                       AND nota_liquidacao.cod_nota = ' || CODNOTA || '
                       AND nota_liquidacao.exercicio = '''||EXERCICIOLIQUIDACAO||'''
                       AND nota_liquidacao.cod_entidade = ' || CODENTIDADE || '
            ';
    END IF;

    FOR ReContaPg IN EXECUTE SqlContaPg
    LOOP
        SEQUENCIA := FazerLancamento( ReContaPg.estrutural_debito , ReContaPg.estrutural_credito , 920 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE, ReContaPg.conta_debito, ReContaPg.conta_credito );
        inContConfiguracao := inContConfiguracao + 1;
    END LOOP;

    IF (inContConfiguracao = 0) THEN
        RAISE EXCEPTION 'Configuração dos lançamentos de despesa não configurados para esta despesa.';
    END IF;

RETURN SEQUENCIA;
END;
$$ LANGUAGE 'plpgsql';

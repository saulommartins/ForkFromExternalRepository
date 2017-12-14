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
CREATE OR REPLACE FUNCTION EmpenhoLiquidacao(character varying, numeric, character varying, integer, character varying, integer, integer, character varying, integer, integer, character varying, character varying, integer, character varying) RETURNS INTEGER AS $$
DECLARE
    EXERCICIO ALIAS FOR $1;
    VALOR ALIAS FOR $2;
    COMPLEMENTO ALIAS FOR $3;
    CODLOTE ALIAS FOR $4;
    TIPOLOTE ALIAS FOR $5;
    CODENTIDADE ALIAS FOR $6;
    CODNOTA ALIAS FOR $7;
    CLASDESPESA ALIAS FOR $8;
    CODHISTORICOPATRIMON ALIAS FOR $9;
    NUMORGAO ALIAS FOR $10;
    CODESTRUTURALCONTADEBITO ALIAS FOR $11;
    CODESTRUTURALCONTACREDITO ALIAS FOR $12;
    CODDESPESA ALIAS FOR $13;
    CODCLASSDESPESA ALIAS FOR $14;
    
    SEQUENCIA INTEGER;
    SQLCONFIGURACAO VARCHAR := '';
    SQLVINCULORECURSO VARCHAR := '';
    SQLVALIDAESTRUTURALCONTADESPESA VARCHAR := '';
    SQLCONTAFIXA VARCHAR := '';
    STCODESTRUTURALVINCULOCREDITO VARCHAR := '';
    STCODESTRUTURALVINCULODEBITO VARCHAR := '';
    INCODCONTAVINCULOCREDITO INTEGER := NULL;
    INCODCONTAVINCULODEBITO INTEGER := NULL;
    REREGISTROSCONFIGURACAO RECORD;
    REREGISTROSRECURSO RECORD;
    REREGISTROSVALIDAESTRUTURALCONTADESPESA RECORD;
    REREGISTROSCONTAFIXA RECORD;
    INCONTCONFIGURACAO INTEGER := 0;
    INCONTVINCULO INTEGER := 0;
    INCONTVALIDAESTRUTURALCONTADESPESA INTEGER := 0;

BEGIN

    SQLCONTAFIXA := '
        SELECT debito.cod_estrutural AS estrutural_debito
             , credito.cod_estrutural AS estrutural_credito
             , debito.cod_plano AS plano_debito
             , credito.cod_plano AS plano_credito
             , debito.exercicio
          FROM ( 
                 SELECT plano_conta.cod_estrutural
                      , plano_analitica.cod_plano
                      , plano_conta.exercicio
                   FROM contabilidade.plano_conta
             INNER JOIN contabilidade.plano_analitica
                     ON plano_conta.cod_conta = plano_analitica.cod_conta
                    AND plano_conta.exercicio = plano_analitica.exercicio
                  WHERE REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''622130100%''
               ) AS debito
    INNER JOIN (
                 SELECT plano_conta.cod_estrutural
                      , plano_analitica.cod_plano
                      , plano_conta.exercicio
                   FROM contabilidade.plano_conta
             INNER JOIN contabilidade.plano_analitica
                     ON plano_conta.cod_conta = plano_analitica.cod_conta
                    AND plano_conta.exercicio = plano_analitica.exercicio
                  WHERE REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''622130300%'' 
               ) AS credito
             ON debito.exercicio = credito.exercicio
          WHERE debito.exercicio = '''||EXERCICIO||'''
    ';

    FOR REREGISTROSCONTAFIXA IN EXECUTE SQLCONTAFIXA
    LOOP
        --lancamento de liquidacao fixo
        SEQUENCIA := FAZERLANCAMENTO(  REREGISTROSCONTAFIXA.estrutural_debito , REREGISTROSCONTAFIXA.estrutural_credito , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE, REREGISTROSCONTAFIXA.plano_debito, REREGISTROSCONTAFIXA.plano_credito );
    END LOOP;

    SQLVALIDAESTRUTURALCONTADESPESA := '
            SELECT conta_despesa.cod_conta
                 , conta_despesa.exercicio
              FROM orcamento.conta_despesa
             WHERE conta_despesa.exercicio = '''||EXERCICIO||'''
               AND conta_despesa.cod_estrutural like ''3.1.%''
    ';


    FOR REREGISTROSVALIDAESTRUTURALCONTADESPESA IN EXECUTE SQLVALIDAESTRUTURALCONTADESPESA
    LOOP
        INCONTVALIDAESTRUTURALCONTADESPESA := INCONTVALIDAESTRUTURALCONTADESPESA + 1;
    END LOOP;

    INCONTVALIDAESTRUTURALCONTADESPESA := 0;
    
    IF INCONTVALIDAESTRUTURALCONTADESPESA = 0 THEN
        SQLCONFIGURACAO := '
                SELECT REPLACE(plano_analitica_credito.cod_plano::VARCHAR, ''.'', '''')::integer AS conta_credito
                     , REPLACE(plano_analitica_debito.cod_plano::VARCHAR, ''.'', '''')::integer AS conta_debito
                     , CASE WHEN plano_conta_debito.cod_estrutural like ''3.1.1.1%'' THEN
                                    ''entidadeRPPS''
                            WHEN plano_conta_debito.cod_estrutural like ''3.1.1.2%'' THEN
                                    ''despesaPessoal''
                            WHEN plano_conta_debito.cod_estrutural like ''3.3.2%'' THEN
                                    ''demaisDespesas''
                            WHEN plano_conta_debito.cod_estrutural like ''1.1.5.6%'' THEN
                                    ''materialConsumo''
                            WHEN plano_conta_debito.cod_estrutural like ''1.2.3%'' THEN
                                    ''materialPermanente''
                            WHEN plano_conta_debito.cod_estrutural like ''3.3.1.1%'' THEN
                                    ''almoxarifado''
                       END AS tipo_despesa
                     , configuracao_lancamento_debito.cod_conta_despesa
                     , REPLACE(plano_conta_debito.cod_estrutural, ''.'', '''') as estrutural_debito
                     , REPLACE(plano_conta_credito.cod_estrutural, ''.'', '''') as estrutural_credito
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
                   AND configuracao_lancamento_credito.exercicio = '''||EXERCICIO||'''
                   AND configuracao_lancamento_credito.tipo = ''liquidacao''
                   AND publico.fn_mascarareduzida(conta_despesa.cod_estrutural) = publico.fn_mascarareduzida('''||CODCLASSDESPESA||''')
        ';
        
        FOR REREGISTROSCONFIGURACAO IN EXECUTE SQLCONFIGURACAO
        LOOP
            SEQUENCIA := FAZERLANCAMENTO(  REREGISTROSCONFIGURACAO.estrutural_debito , REREGISTROSCONFIGURACAO.estrutural_credito , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , REREGISTROSCONFIGURACAO.conta_debito, REREGISTROSCONFIGURACAO.conta_credito );
            INCONTCONFIGURACAO := INCONTCONFIGURACAO + 1;
        END LOOP;
        
        IF ( INCONTCONFIGURACAO = 0 ) THEN
            RAISE EXCEPTION 'Configuração dos lançamentos de despesa não configurados para esta despesa.';
        END IF;
    END IF;
    
    SQLVINCULORECURSO := '
                SELECT tabela.conta_debito
                     , tabela.conta_credito
                     , tabela.cod_plano
                  FROM (
                          SELECT CASE WHEN plano_conta.cod_estrutural like ''8.2.1.1.2%'' THEN
                                      plano_conta.cod_estrutural
                                 ELSE
                                      NULL
                                 END AS conta_debito
                               , CASE WHEN plano_conta.cod_estrutural like ''8.2.1.1.3%'' THEN
                                      plano_conta.cod_estrutural
                                 ELSE
                                      NULL
                                 END AS conta_credito
                               , plano_analitica.cod_plano AS cod_plano
                            FROM contabilidade.plano_conta
                      INNER JOIN contabilidade.plano_analitica
                              ON plano_conta.cod_conta = plano_analitica.cod_conta
                             AND plano_conta.exercicio = plano_analitica.exercicio
                      INNER JOIN contabilidade.plano_recurso
                              ON plano_analitica.cod_plano = plano_recurso.cod_plano
                             AND plano_analitica.exercicio = plano_recurso.exercicio
                      INNER JOIN orcamento.recurso
                              ON plano_recurso.cod_recurso = recurso.cod_recurso
                             AND plano_recurso.exercicio = recurso.exercicio
                      INNER JOIN orcamento.despesa
                              ON recurso.cod_recurso = despesa.cod_recurso
                             AND recurso.exercicio = despesa.exercicio
                           WHERE despesa.cod_despesa = '||CODDESPESA::varchar||'
                             AND despesa.exercicio = '''||EXERCICIO||'''
                       ) AS tabela
                 WHERE ( conta_debito IS NOT NULL
                    OR conta_credito IS NOT NULL)
              GROUP BY conta_debito
                     , conta_credito
                     , cod_plano
      ';

      FOR REREGISTROSRECURSO IN EXECUTE SQLVINCULORECURSO
      LOOP
        IF REREGISTROSRECURSO.conta_debito IS NOT NULL THEN
            STCODESTRUTURALVINCULODEBITO := REREGISTROSRECURSO.conta_debito;
            INCODCONTAVINCULODEBITO := REREGISTROSRECURSO.cod_plano;
        END IF;
        
        IF REREGISTROSRECURSO.conta_credito IS NOT NULL THEN
            STCODESTRUTURALVINCULOCREDITO := REREGISTROSRECURSO.conta_credito;
            INCODCONTAVINCULOCREDITO := REREGISTROSRECURSO.cod_plano;
        END IF;
        
        IF STCODESTRUTURALVINCULOCREDITO <> '' AND STCODESTRUTURALVINCULODEBITO <> '' THEN
            SEQUENCIA := FAZERLANCAMENTO( STCODESTRUTURALVINCULODEBITO , STCODESTRUTURALVINCULOCREDITO , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , INCODCONTAVINCULODEBITO, INCODCONTAVINCULOCREDITO );
            INCONTVINCULO := INCONTVINCULO + 1;
        END IF;
        
      END LOOP;

      IF INCONTVINCULO = 0 THEN
        RAISE EXCEPTION 'Nenhum recurso vinculado a esta despesa!';
      END IF;
      
        IF EXERCICIO::integer > 2012 THEN
            SQLCONTAFIXA := '
                    SELECT debito.cod_estrutural AS estrutural_debito
                         , credito.cod_estrutural AS estrutural_credito
                         , debito.cod_plano AS plano_debito
                         , credito.cod_plano AS plano_credito
                         , debito.exercicio
                      FROM (
                             SELECT plano_conta.cod_estrutural
                                  , plano_analitica.cod_plano
                                  , plano_conta.exercicio
                               FROM contabilidade.plano_conta
                         INNER JOIN contabilidade.plano_analitica
                                 ON plano_conta.cod_conta = plano_analitica.cod_conta
                                AND plano_conta.exercicio = plano_analitica.exercicio
                              WHERE REPLACE(plano_conta.cod_estrutural, ''.'',  '''') LIKE ''622920103%''
                           ) AS debito
                INNER JOIN (
                             SELECT plano_conta.cod_estrutural
                                  , plano_analitica.cod_plano
                                  , plano_conta.exercicio
                               FROM contabilidade.plano_conta
                         INNER JOIN contabilidade.plano_analitica
                                 ON plano_conta.cod_conta = plano_analitica.cod_conta
                                AND plano_conta.exercicio = plano_analitica.exercicio
                              WHERE REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''622920101%''
                           ) AS credito
                         ON debito.exercicio = credito.exercicio
                      WHERE debito.exercicio = '''||EXERCICIO||'''
            ';

            FOR REREGISTROSCONTAFIXA IN EXECUTE SQLCONTAFIXA
            LOOP
                    SEQUENCIA := FAZERLANCAMENTO(  REREGISTROSCONTAFIXA.estrutural_debito , REREGISTROSCONTAFIXA.estrutural_credito , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , REREGISTROSCONTAFIXA.plano_debito, REREGISTROSCONTAFIXA.plano_credito );
            END LOOP;
            
            IF EXERCICIO::INTEGER < 2014 THEN
                SEQUENCIA := EMPENHOLIQUIDACAOMODALIDADESLICITACAO(EXERCICIO, VALOR, COMPLEMENTO, CODLOTE, TIPOLOTE, CODENTIDADE, CODNOTA);
            END IF;
            IF EXERCICIO::INTEGER = 2013 THEN
                SQLCONTAFIXA := '
                        SELECT debito.cod_estrutural AS estrutural_debito
                             , credito.cod_estrutural AS estrutural_credito
                             , debito.cod_plano AS plano_debito
                             , credito.cod_plano AS plano_credito
                             , debito.exercicio
                          FROM (
                                 SELECT plano_conta.cod_estrutural
                                      , plano_analitica.cod_plano
                                      , plano_conta.exercicio
                                   FROM contabilidade.plano_conta
                             INNER JOIN contabilidade.plano_analitica
                                     ON plano_conta.cod_conta = plano_analitica.cod_conta
                                    AND plano_conta.exercicio = plano_analitica.exercicio
                                  WHERE REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''622920901%''
                               ) AS debito
                    INNER JOIN (
                                 SELECT plano_conta.cod_estrutural
                                      , plano_analitica.cod_plano
                                      , plano_conta.exercicio
                                   FROM contabilidade.plano_conta
                             INNER JOIN contabilidade.plano_analitica
                                     ON plano_conta.cod_conta = plano_analitica.cod_conta
                                    AND plano_conta.exercicio = plano_analitica.exercicio
                                  WHERE REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''622920903%''
                               ) AS credito
                             ON debito.exercicio = credito.exercicio
                          WHERE debito.exercicio = '''||EXERCICIO||'''
                ';
            
                FOR REREGISTROSCONTAFIXA IN EXECUTE SQLCONTAFIXA
                LOOP
                        SEQUENCIA := FAZERLANCAMENTO(  REREGISTROSCONTAFIXA.estrutural_debito , REREGISTROSCONTAFIXA.estrutural_credito , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , REREGISTROSCONTAFIXA.plano_debito, REREGISTROSCONTAFIXA.plano_credito );
                END LOOP;
            END IF;
        END IF;

        --IF EXERCICIO::integer > 2013 THEN
        --    SQLCONTAFIXA := '
        --            SELECT debito.cod_estrutural AS estrutural_debito
        --                 , credito.cod_estrutural AS estrutural_credito
        --                 , debito.cod_plano AS plano_debito
        --                 , credito.cod_plano AS plano_credito
        --                 , debito.exercicio
        --              FROM (
        --                     SELECT plano_conta.cod_estrutural
        --                          , plano_analitica.cod_plano
        --                          , plano_conta.exercicio
        --                       FROM contabilidade.plano_conta
        --                 INNER JOIN contabilidade.plano_analitica
        --                         ON plano_conta.cod_conta = plano_analitica.cod_conta
        --                        AND plano_conta.exercicio = plano_analitica.exercicio
        --                      WHERE REPLACE(plano_conta.cod_estrutural, ''.'',  '''') LIKE ''6311%''
        --                   ) AS debito
        --        INNER JOIN (
        --                     SELECT plano_conta.cod_estrutural
        --                          , plano_analitica.cod_plano
        --                          , plano_conta.exercicio
        --                       FROM contabilidade.plano_conta
        --                 INNER JOIN contabilidade.plano_analitica
        --                         ON plano_conta.cod_conta = plano_analitica.cod_conta
        --                        AND plano_conta.exercicio = plano_analitica.exercicio
        --                      WHERE REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''6313%''
        --                   ) AS credito
        --                 ON debito.exercicio = credito.exercicio
        --              WHERE debito.exercicio = '''||EXERCICIO||'''
        --    ';
        --
        --    FOR REREGISTROSCONTAFIXA IN EXECUTE SQLCONTAFIXA
        --    LOOP
        --            SEQUENCIA := FAZERLANCAMENTO(  REREGISTROSCONTAFIXA.estrutural_debito , REREGISTROSCONTAFIXA.estrutural_credito , 824 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , REREGISTROSCONTAFIXA.plano_debito, REREGISTROSCONTAFIXA.plano_credito );
        --    END LOOP;
        --
        --    SEQUENCIA := EMPENHOLIQUIDACAOMODALIDADESLICITACAO(EXERCICIO, VALOR, COMPLEMENTO, CODLOTE, TIPOLOTE, CODENTIDADE, CODNOTA);
        --
        --    SQLCONTAFIXA := '
        --            SELECT debito.cod_estrutural AS estrutural_debito
        --                 , credito.cod_estrutural AS estrutural_credito
        --                 , debito.cod_plano AS plano_debito
        --                 , credito.cod_plano AS plano_credito
        --                 , debito.exercicio
        --              FROM (
        --                     SELECT plano_conta.cod_estrutural
        --                          , plano_analitica.cod_plano
        --                          , plano_conta.exercicio
        --                       FROM contabilidade.plano_conta
        --                 INNER JOIN contabilidade.plano_analitica
        --                         ON plano_conta.cod_conta = plano_analitica.cod_conta
        --                        AND plano_conta.exercicio = plano_analitica.exercicio
        --                      WHERE REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''82112%''
        --                   ) AS debito
        --        INNER JOIN (
        --                     SELECT plano_conta.cod_estrutural
        --                          , plano_analitica.cod_plano
        --                          , plano_conta.exercicio
        --                       FROM contabilidade.plano_conta
        --                 INNER JOIN contabilidade.plano_analitica
        --                         ON plano_conta.cod_conta = plano_analitica.cod_conta
        --                        AND plano_conta.exercicio = plano_analitica.exercicio
        --                      WHERE REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''82113%''
        --                   ) AS credito
        --                 ON debito.exercicio = credito.exercicio
        --              WHERE debito.exercicio = '''||EXERCICIO||'''
        --    ';
        --
        --    FOR REREGISTROSCONTAFIXA IN EXECUTE SQLCONTAFIXA
        --    LOOP
        --            SEQUENCIA := FAZERLANCAMENTO(  REREGISTROSCONTAFIXA.estrutural_debito , REREGISTROSCONTAFIXA.estrutural_credito , 824 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , REREGISTROSCONTAFIXA.plano_debito, REREGISTROSCONTAFIXA.plano_credito );
        --    END LOOP;
        --END IF;
    
--    SEQUENCIA := empenholiquidacaofinanceirotipocredor(  EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , CODNOTA , CLASDESPESA , NUMORGAO  );
    RETURN SEQUENCIA;
END;

$$ LANGUAGE 'plpgsql';

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
    Realiza as buscas das despesas e receitas e as deixa lado-a-lado na tabela temporaria para mostra-las em forma de listagem
    O relatorio deve buscar as receitas e despesas pela destinacao de recurso
    Ex:
    
    0.1.00.00000
    -----------------
    estrutural | descricao | valor | PAO | despesa | valor
    1.1.1.00   | lalala    |   500 |  12 |  1.1.1  |  600
    1.1.1.01   | lelele    |   200 |  14 |  1.2.1  |  500
    1.1.1.00   | lalala    |   500 |     |         |  
    1.1.1.00   | lalala    |   500 |     |         |
    
    0.1.10.00000
    -----------------
    estrutural | descricao | valor | PAO | despesa | valor
    1.1.1.00   | lalala    |   500 |  12 |  1.1.1  |  600
               |           |       |  15 |  1.2.1  |  500
               |           |       |  11 |  1.2.2  |  800
               |           |       |  17 |  1.2.5  |  400
*/

/*
    $Id: relatorioComparativoReceitaDespesa.plsql 66305 2016-08-05 19:24:10Z michel $
*/

CREATE OR REPLACE FUNCTION orcamento.fn_relatorio_compatativo_receita_despesa(stExercicio         VARCHAR,
                                                                              stEntidade          VARCHAR,
                                                                              stDestinacaoRecurso VARCHAR,
                                                                              stRecurso           VARCHAR) RETURNS SETOF record AS $$
DECLARE
    stSql      VARCHAR   := '';
    reRegistro RECORD;
    inId       INTEGER;
BEGIN

    -- Tabela temporaria onde ficará as informações necessárias para serem demonstradas da forma como deve no relatorio
    CREATE TEMPORARY TABLE tmp_receita_despesa (
        id                 SERIAL,
        cod_recurso        INTEGER,
        recurso            VARCHAR,
        uso_desc           VARCHAR,
        dest_desc          VARCHAR,
        espec_desc         VARCHAR,
        detal_desc         VARCHAR,
        cod_estrutural     VARCHAR,
        descricao          VARCHAR,
        valor_receita      NUMERIC,
        estrutural_despesa VARCHAR,
        pao                INTEGER,
        valor_despesa      NUMERIC
    );

    -- Realiza as buscas dos valores das receitas
    stSql := '  SELECT conta_receita.cod_estrutural
                     , receita.cod_receita
                     , recurso.cod_recurso
                     , recurso.masc_recurso AS recurso
                     , trim(conta_receita.descricao) AS descricao
                     , orcamento.fn_receita_valor_previsto(recurso.exercicio, publico.fn_mascarareduzida(conta_receita.cod_estrutural), CAST(receita.cod_entidade AS VARCHAR)) AS valor
                     , CASE WHEN recurso_destinacao.descricao_uso IS NOT NULL
                            THEN recurso_destinacao.descricao_uso
                            ELSE recurso.nom_recurso
                       END AS descricao_uso
                     , recurso_destinacao.descricao_destinacao
                     , recurso_destinacao.descricao_especificacao
                     , recurso_destinacao.descricao_detalhamento
                  FROM orcamento.recurso('''||stExercicio||''') AS recurso
            INNER JOIN orcamento.receita 
                    ON receita.cod_recurso = recurso.cod_recurso 
                   AND receita.exercicio   = recurso.exercicio
                   AND receita.cod_entidade IN ('||stEntidade||'::INTEGER)
            INNER JOIN orcamento.conta_receita
                    ON conta_receita.exercicio = receita.exercicio
                   AND conta_receita.cod_conta = receita.cod_conta
             LEFT JOIN (
                              SELECT recurso_destinacao.cod_recurso
                                   , recurso_destinacao.exercicio
                                   , recurso_destinacao.cod_uso
                                   , identificador_uso.descricao AS descricao_uso
                                   , recurso_destinacao.cod_especificacao
                                   , especificacao_destinacao_recurso.descricao AS descricao_especificacao
                                   , recurso_destinacao.cod_destinacao
                                   , destinacao_recurso.descricao AS descricao_destinacao
                                   , recurso_destinacao.cod_detalhamento
                                   , detalhamento_destinacao_recurso.descricao AS descricao_detalhamento
                                FROM orcamento.recurso_destinacao
                          INNER JOIN orcamento.identificador_uso
                                  ON identificador_uso.cod_uso   = recurso_destinacao.cod_uso
                                 AND identificador_uso.exercicio = recurso_destinacao.exercicio
                          INNER JOIN orcamento.especificacao_destinacao_recurso
                                  ON especificacao_destinacao_recurso.cod_especificacao = recurso_destinacao.cod_especificacao
                                 AND especificacao_destinacao_recurso.exercicio         = recurso_destinacao.exercicio
                          INNER JOIN orcamento.destinacao_recurso
                                  ON destinacao_recurso.cod_destinacao = recurso_destinacao.cod_destinacao
                                 AND destinacao_recurso.exercicio      = recurso_destinacao.exercicio
                          INNER JOIN orcamento.detalhamento_destinacao_recurso
                                  ON detalhamento_destinacao_recurso.cod_detalhamento = recurso_destinacao.cod_detalhamento
                                 AND detalhamento_destinacao_recurso.exercicio        = recurso_destinacao.exercicio
                       ) AS recurso_destinacao
                    ON recurso_destinacao.cod_recurso = receita.cod_recurso
                   AND recurso_destinacao.exercicio   = receita.exercicio

                 WHERE recurso.exercicio = '''||stExercicio||'''
                ';

    IF (stDestinacaoRecurso <> '') THEN
        stSql := stSql || '   AND recurso.masc_recurso LIKE '''||stDestinacaoRecurso||''' ';
    END IF;

    stSql := stSql || ' ORDER BY recurso.masc_recurso
                     , conta_receita.cod_estrutural';
   
    -- Insere cada dado encontrado na tabela temporario
    FOR reRegistro IN EXECUTE stSql LOOP

        INSERT INTO tmp_receita_despesa (
                cod_recurso
            ,   recurso
            ,   uso_desc    
            ,   dest_desc   
            ,   espec_desc  
            ,   detal_desc
            ,   cod_estrutural
            ,   descricao
            ,   valor_receita
        ) VALUES (
                reRegistro.cod_recurso
            ,   reRegistro.recurso
            ,   reRegistro.descricao_uso
            ,   reRegistro.descricao_destinacao
            ,   reRegistro.descricao_especificacao
            ,   reRegistro.descricao_detalhamento
            ,   reRegistro.cod_estrutural
            ,   reRegistro.descricao
            ,   reRegistro.valor
        );
        
    END LOOP;

    -- Busca os dados de despesa
    stSql := '    SELECT despesa.num_pao
                       , despesa.vl_original
                       , conta_despesa.cod_estrutural
                       , recurso.cod_recurso
                       , recurso.masc_recurso
                       , CASE WHEN recurso_destinacao.descricao_uso IS NOT NULL
                              THEN recurso_destinacao.descricao_uso
                              ELSE recurso.nom_recurso
                         END AS descricao_uso
                       , recurso_destinacao.descricao_destinacao
                       , recurso_destinacao.descricao_especificacao
                       , recurso_destinacao.descricao_detalhamento
                    FROM orcamento.recurso('''||stExercicio||''') AS recurso 
              INNER JOIN orcamento.despesa 
                      ON despesa.cod_recurso = recurso.cod_recurso 
                     AND despesa.exercicio   = recurso.exercicio
                   AND despesa.cod_entidade IN ('||stEntidade||'::INTEGER )
              INNER JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = despesa.cod_conta
                     AND conta_despesa.exercicio = despesa.exercicio
               LEFT JOIN (
                             SELECT recurso_destinacao.cod_recurso
                                  , recurso_destinacao.exercicio
                                  , recurso_destinacao.cod_uso
                                  , identificador_uso.descricao AS descricao_uso
                                  , recurso_destinacao.cod_especificacao
                                  , especificacao_destinacao_recurso.descricao AS descricao_especificacao
                                  , recurso_destinacao.cod_destinacao
                                  , destinacao_recurso.descricao AS descricao_destinacao
                                  , recurso_destinacao.cod_detalhamento
                                  , detalhamento_destinacao_recurso.descricao AS descricao_detalhamento
                               FROM orcamento.recurso_destinacao
                         INNER JOIN orcamento.identificador_uso
                                 ON identificador_uso.cod_uso   = recurso_destinacao.cod_uso
                                AND identificador_uso.exercicio = recurso_destinacao.exercicio
                         INNER JOIN orcamento.especificacao_destinacao_recurso
                                 ON especificacao_destinacao_recurso.cod_especificacao = recurso_destinacao.cod_especificacao
                                AND especificacao_destinacao_recurso.exercicio         = recurso_destinacao.exercicio
                         INNER JOIN orcamento.destinacao_recurso
                                 ON destinacao_recurso.cod_destinacao = recurso_destinacao.cod_destinacao
                                AND destinacao_recurso.exercicio      = recurso_destinacao.exercicio
                         INNER JOIN orcamento.detalhamento_destinacao_recurso
                                 ON detalhamento_destinacao_recurso.cod_detalhamento = recurso_destinacao.cod_detalhamento
                                AND detalhamento_destinacao_recurso.exercicio        = recurso_destinacao.exercicio
                         ) AS recurso_destinacao
                      ON recurso_destinacao.cod_recurso = recurso.cod_recurso
                     AND recurso_destinacao.exercicio   = recurso.exercicio

                   WHERE recurso.exercicio = '''||stExercicio||'''
                     ';

    IF (stDestinacaoRecurso <> '') THEN
        stSql := stSql || '   AND recurso.masc_recurso LIKE '''||stDestinacaoRecurso||''' ';
    END IF;

    stSql := stSql || ' ORDER BY recurso.masc_recurso
                     , conta_despesa.cod_estrutural';

    /**
        Realiza a inserção dos dados de despesas na tabela temporaria
        Porém esses dados sao inseridos a partir das destinações já cadastradas, então se você já tem receitas cadastradas para um recurso,
        é cadastrado na mesma linha.
        
        Ex:
        Já tem cadastrado as seguintes linhas (receita)
        
        tmp_receita_despesa
        
        id  |    recurso     |   cod_estrutural    |    descricao    |     valor_receita     |   pao   |  valor_despesa |  estrutural_despesa |  uso_desc  | dest_desc  |  espec_desc   |   detal_desc
        ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
         1  |  0.1.00.0000   | 1.1.11.55.55555     |   adlsdlaasdlsa |         65456.00      |         |                |                     |   alallala | aaaaaaa    |      dasdsad  |     dasiuhda
         2  |  0.1.00.0000   | 1.1.11.77.55555     |   lalalalalala  |         10000.00      |         |                |                     |   alallala | aaaaaaa    |      dasdsad  |     dasiuhda
        
        Entao a partir daqui sera inserido 3 dados da despesa, os 2 primeiros serao inserido nas 2 primeiras linhas, atualizando as linhas
        ja cadastrados e a terceira linha será inserida uma nova, ficando assim:
        
        id  |    recurso     |   cod_estrutural    |    descricao    |     valor_receita     |   pao   |  valor_despesa |  estrutural_despesa |  uso_desc  | dest_desc  |  espec_desc   |   detal_desc
        ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
         1  |  0.1.00.0000   | 1.1.11.55.55555     |   adlsdlaasdlsa |         65456.00      |  1232   |      65456.00  |    3.3.3.3.333      |   alallala | aaaaaaa    |      dasdsad  |     dasiuhda
         2  |  0.1.00.0000   | 1.1.11.77.55555     |   lalalalalala  |         10000.00      |  5564   |      89444.00  |    5.5.5.5.555      |   alallala | aaaaaaa    |      dasdsad  |     dasiuhda
         3  |  0.1.00.0000   |                     |                 |                       |  5489   |      87745.00  |    8.8.9.6.855      |   alallala | aaaaaaa    |      dasdsad  |     dasiuhda
         
         Dessa forma gera-se os dados um ao lado do outro no relatorio
        
    */

    FOR reRegistro IN EXECUTE stSql LOOP
        SELECT MIN(id)
          INTO inId
          FROM tmp_receita_despesa
         WHERE recurso = reRegistro.masc_recurso
           AND estrutural_despesa IS NULL;

        IF (inId IS NULL) THEN
            INSERT INTO tmp_receita_despesa (
                    cod_recurso
                ,   recurso
                ,   pao
                ,   valor_despesa
                ,   estrutural_despesa
                ,   uso_desc    
                ,   dest_desc   
                ,   espec_desc  
                ,   detal_desc
            ) VALUES (
                    reRegistro.cod_recurso
                ,   reRegistro.masc_recurso
                ,   reRegistro.num_pao
                ,   reRegistro.vl_original
                ,   reRegistro.cod_estrutural
                ,   reRegistro.descricao_uso
                ,   reRegistro.descricao_destinacao
                ,   reRegistro.descricao_especificacao
                ,   reRegistro.descricao_detalhamento
            );

        ELSE
            UPDATE tmp_receita_despesa
               SET pao = reRegistro.num_pao
                 , valor_despesa = reRegistro.vl_original
                 , estrutural_despesa = reRegistro.cod_estrutural
             WHERE id = inId;
            
        END IF;
        
    END LOOP;

    stSql := 'SELECT *
                FROM tmp_receita_despesa
             ';
    IF (stRecurso <> '') THEN
        stSql := stSql || '   WHERE cod_recurso = '||stRecurso||' ';
    END IF;

    FOR reRegistro IN EXECUTE stSql LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_receita_despesa;
    
    RETURN;
END;
$$ LANGUAGE plpgsql;

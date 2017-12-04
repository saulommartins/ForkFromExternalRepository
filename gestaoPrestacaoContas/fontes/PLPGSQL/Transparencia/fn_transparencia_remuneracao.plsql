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
CREATE OR REPLACE FUNCTION fn_transparencia_remuneracao(varchar, integer, varchar, integer) RETURNS SETOF RECORD AS $$
DECLARE
stEntidade                ALIAS FOR $1;
inCodPeriodoMovimentacao  ALIAS FOR $2;
stExercicio               ALIAS FOR $3;
inCodEntidade             ALIAS FOR $4;
stSql                     VARCHAR;
stSqlInsert               VARCHAR;
stSqlUpdate               VARCHAR;
arTipo                    VARCHAR[];
reRegistro                RECORD;
indice                  INT;

BEGIN

    -- LIMPA TABELA DE REGISTROS
    stSql := 'DELETE FROM temp_transparencia_remuneracao';
    EXECUTE stSql;

    stSql := 'SELECT   contrato.registro
              , contrato.cod_contrato
        , sw_cgm.nom_cgm AS cgm
        , '||quote_literal(inCodEntidade)||'::varchar as cod_entidade
         FROM pessoal'||stEntidade||'.contrato
         INNER JOIN (SELECT cod_contrato, numcgm
           FROM pessoal'||stEntidade||'.servidor, pessoal.servidor_contrato_servidor
          WHERE servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
           
          UNION 
             
         SELECT contrato_pensionista.cod_contrato, numcgm
           FROM pessoal'||stEntidade||'.contrato_pensionista, pessoal.pensionista
          WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
            AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente 
        ) AS servidor_pensionista
      ON servidor_pensionista.cod_contrato = contrato.cod_contrato
    INNER JOIN sw_cgm
      ON sw_cgm.numcgm = servidor_pensionista.numcgm
    INNER JOIN folhapagamento'||stEntidade||'.contrato_servidor_periodo
      ON contrato_servidor_periodo.cod_contrato = contrato.cod_contrato
         WHERE contrato_servidor_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
      ORDER BY contrato.registro, contrato.cod_contrato';

    FOR reRegistro IN EXECUTE stSql LOOP
        IF reRegistro.cod_entidade = '' THEN
            reRegistro.cod_entidade := NULL;
        END IF;
        stSqlInsert := 'INSERT INTO temp_transparencia_remuneracao (registro, cod_contrato, cgm, cod_periodo_movimentacao, cod_entidade, exercicio) VALUES ('||reRegistro.registro||', '||reRegistro.cod_contrato||', '||quote_literal(reRegistro.cgm)||', '||inCodPeriodoMovimentacao||', '||inCodEntidade||', '||quote_literal(stExercicio)||')';
        EXECUTE stSqlInsert;
    END LOOP; 

    arTipo[1]  := 'remuneracao_bruta';
    arTipo[2]  := 'redutor_teto';
    arTipo[3]  := 'remuneracao_natalina';
    arTipo[4]  := 'remuneracao_ferias';
    arTipo[5]  := 'remuneracao_outras';
    arTipo[6]  := 'deducoes_irrf';
    arTipo[7]  := 'deducoes_obrigatorias';
    arTipo[8]  := 'demais_deducoes';
    arTipo[9]  := 'salario_familia';
    arTipo[10] := 'jetons';
    arTipo[11] := 'verbas';

    FOR indice IN 1..array_upper(arTipo,1) LOOP        
            stSql := ' SELECT contrato.registro,
                              COALESCE(SUM(folhas.valor), 0) AS valor_calculado
                
             FROM pessoal'||stEntidade||'.contrato
             
             JOIN (SELECT cod_contrato
                 FROM pessoal'||stEntidade||'.servidor, pessoal.servidor_contrato_servidor
                 WHERE servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
                                   
                  UNION
                              
                  SELECT contrato_pensionista.cod_contrato
              FROM pessoal'||stEntidade||'.contrato_pensionista, pessoal.pensionista
                   WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                                 AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
                  ) AS servidor_pensionista
               ON servidor_pensionista.cod_contrato = contrato.cod_contrato
               
             JOIN ( 
       
                   SELECT ''salario''::varchar as ind_fl                 
                , registro_evento_periodo.cod_contrato 
                , evento.codigo               
                , evento.descricao 
                , evento.natureza 
                , evento_calculado.valor 
                , evento_calculado.quantidade
                , CASE WHEN evento_calculado.desdobramento IS NULL THEN ''''::varchar
                       ELSE evento_calculado.desdobramento END AS desdobramento
       
                    FROM folhapagamento'||stEntidade||'.registro_evento 
                  
              INNER JOIN folhapagamento'||stEntidade||'.registro_evento_periodo
                ON registro_evento_periodo.cod_registro = registro_evento.cod_registro
       
              INNER JOIN folhapagamento'||stEntidade||'.evento
                ON evento.cod_evento = registro_evento.cod_evento
       
              INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento
                ON ultimo_registro_evento.cod_evento   = registro_evento.cod_evento        
                     AND ultimo_registro_evento.cod_registro = registro_evento.cod_registro
                     AND ultimo_registro_evento.timestamp    = registro_evento.timestamp
       
              INNER JOIN folhapagamento'||stEntidade||'.evento_calculado
                ON evento_calculado.cod_evento     = registro_evento.cod_evento
                     AND evento_calculado.cod_registro         = registro_evento.cod_registro
                     AND evento_calculado.timestamp_registro   = registro_evento.timestamp
       
              INNER JOIN pessoal'||stEntidade||'.contrato
                ON contrato.cod_contrato = registro_evento_periodo.cod_contrato
            
                   WHERE registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                     AND contrato.registro                IN (SELECT registro FROM temp_transparencia_remuneracao)
                   
                  UNION
                  
              SELECT ''ferias''::varchar as ind_fl        
                    , registro_evento_ferias.cod_contrato
                    , evento.codigo
                    , evento.descricao
                    , evento.natureza
                    , evento_ferias_calculado.valor
                    , evento_ferias_calculado.quantidade
                    , evento_ferias_calculado.desdobramento
       
                    FROM folhapagamento'||stEntidade||'.registro_evento_ferias 
       
              INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento_ferias
                ON ultimo_registro_evento_ferias.cod_evento    = registro_evento_ferias.cod_evento        
                     AND ultimo_registro_evento_ferias.cod_registro  = registro_evento_ferias.cod_registro
                     AND ultimo_registro_evento_ferias.timestamp     = registro_evento_ferias.timestamp
                     AND ultimo_registro_evento_ferias.desdobramento = registro_evento_ferias.desdobramento
       
              INNER JOIN folhapagamento'||stEntidade||'.evento_ferias_calculado
                ON evento_ferias_calculado.cod_evento    = ultimo_registro_evento_ferias.cod_evento
                     AND evento_ferias_calculado.cod_registro  = ultimo_registro_evento_ferias.cod_registro
                     AND evento_ferias_calculado.timestamp_registro     = ultimo_registro_evento_ferias.timestamp
                     AND evento_ferias_calculado.desdobramento = ultimo_registro_evento_ferias.desdobramento
       
              INNER JOIN folhapagamento'||stEntidade||'.evento
                ON evento.cod_evento = registro_evento_ferias.cod_evento
       
              INNER JOIN pessoal'||stEntidade||'.contrato
                ON contrato.cod_contrato = registro_evento_ferias.cod_contrato
                   
                   WHERE registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                     AND contrato.registro                 IN (SELECT registro FROM temp_transparencia_remuneracao)
                   
                  UNION
                  
              SELECT ''decimo''::varchar as ind_fl        
                    , registro_evento_decimo.cod_contrato
                    , evento.codigo
                    , evento.descricao
                    , evento.natureza
                    , evento_decimo_calculado.valor
                    , evento_decimo_calculado.quantidade
                    , evento_decimo_calculado.desdobramento
       
                    FROM folhapagamento'||stEntidade||'.registro_evento_decimo 
       
              INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento_decimo
                ON ultimo_registro_evento_decimo.cod_evento    = registro_evento_decimo.cod_evento        
                     AND ultimo_registro_evento_decimo.cod_registro  = registro_evento_decimo.cod_registro
                     AND ultimo_registro_evento_decimo.timestamp     = registro_evento_decimo.timestamp
                     AND ultimo_registro_evento_decimo.desdobramento = registro_evento_decimo.desdobramento
       
              INNER JOIN folhapagamento'||stEntidade||'.evento_decimo_calculado
                ON evento_decimo_calculado.cod_evento         = ultimo_registro_evento_decimo.cod_evento
                     AND evento_decimo_calculado.cod_registro       = ultimo_registro_evento_decimo.cod_registro
                     AND evento_decimo_calculado.timestamp_registro = ultimo_registro_evento_decimo.timestamp
                     AND evento_decimo_calculado.desdobramento      = ultimo_registro_evento_decimo.desdobramento
       
              INNER JOIN folhapagamento'||stEntidade||'.evento
                ON evento.cod_evento = registro_evento_decimo.cod_evento
       
              INNER JOIN pessoal'||stEntidade||'.contrato
                ON contrato.cod_contrato = registro_evento_decimo.cod_contrato
                   
                   WHERE registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                     AND contrato.registro               IN (SELECT registro FROM temp_transparencia_remuneracao)
                     
                  UNION
       
              SELECT ''rescisao''::varchar as ind_fl
                    , registro_evento_rescisao.cod_contrato
                    , evento.codigo
                    , evento.descricao
                    , evento.natureza   
                    , evento_rescisao_calculado.valor
                    , evento_rescisao_calculado.quantidade
                    , evento_rescisao_calculado.desdobramento
       
                    FROM folhapagamento'||stEntidade||'.registro_evento_rescisao 
       
              INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento_rescisao
                ON ultimo_registro_evento_rescisao.cod_evento    = registro_evento_rescisao.cod_evento        
                     AND ultimo_registro_evento_rescisao.cod_registro  = registro_evento_rescisao.cod_registro
                     AND ultimo_registro_evento_rescisao.timestamp     = registro_evento_rescisao.timestamp
                     AND ultimo_registro_evento_rescisao.desdobramento = registro_evento_rescisao.desdobramento
       
              INNER JOIN folhapagamento'||stEntidade||'.evento_rescisao_calculado
                ON evento_rescisao_calculado.cod_evento   = ultimo_registro_evento_rescisao.cod_evento
                     AND evento_rescisao_calculado.cod_registro       = ultimo_registro_evento_rescisao.cod_registro
                     AND evento_rescisao_calculado.timestamp_registro = ultimo_registro_evento_rescisao.timestamp
                     AND evento_rescisao_calculado.desdobramento      = ultimo_registro_evento_rescisao.desdobramento
       
              INNER JOIN folhapagamento'||stEntidade||'.evento
                ON evento.cod_evento = registro_evento_rescisao.cod_evento
       
              INNER JOIN pessoal'||stEntidade||'.contrato
                ON contrato.cod_contrato = registro_evento_rescisao.cod_contrato
                   
                   WHERE registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                     AND contrato.registro                 IN (SELECT registro FROM temp_transparencia_remuneracao)
                  
                  UNION
                  
              SELECT ''complementar''::varchar as ind_fl
                    , registro_evento_complementar.cod_contrato
                    , evento.codigo
                    , evento.descricao
                    , evento.natureza
                    , evento_complementar_calculado.valor
                    , evento_complementar_calculado.quantidade
                    , CASE WHEN evento_complementar_calculado.desdobramento IS NULL THEN ''''::varchar
                     ELSE evento_complementar_calculado.desdobramento END AS desdobramento
       
                    FROM folhapagamento'||stEntidade||'.registro_evento_complementar 
       
              INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento_complementar
                ON ultimo_registro_evento_complementar.cod_evento    = registro_evento_complementar.cod_evento        
                     AND ultimo_registro_evento_complementar.cod_registro  = registro_evento_complementar.cod_registro
                     AND ultimo_registro_evento_complementar.timestamp     = registro_evento_complementar.timestamp
                     AND ultimo_registro_evento_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao
       
              INNER JOIN folhapagamento'||stEntidade||'.evento_complementar_calculado
                ON evento_complementar_calculado.cod_evento       = ultimo_registro_evento_complementar.cod_evento
                     AND evento_complementar_calculado.cod_registro       = ultimo_registro_evento_complementar.cod_registro
                     AND evento_complementar_calculado.timestamp_registro = ultimo_registro_evento_complementar.timestamp
                     AND evento_complementar_calculado.cod_configuracao   = ultimo_registro_evento_complementar.cod_configuracao
       
              INNER JOIN folhapagamento'||stEntidade||'.evento
                ON evento.cod_evento = registro_evento_complementar.cod_evento
       
              INNER JOIN pessoal'||stEntidade||'.contrato
                ON contrato.cod_contrato = registro_evento_complementar.cod_contrato
                   
                   WHERE registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                     AND contrato.registro                     IN (SELECT registro FROM temp_transparencia_remuneracao)
                  ) AS folhas
               ON contrato.cod_contrato = folhas.cod_contrato ';
    -- COLUNA 'remuneracao_bruta'
        IF indice = 1 THEN
             stSql := stSql || ' WHERE folhas.natureza = ''P''
                 AND folhas.desdobramento IN ('''', ''S'')
                 AND folhas.codigo NOT IN ( SELECT evento.codigo
                                        FROM folhapagamento'||stEntidade||'.ferias_evento
                      INNER JOIN folhapagamento'||stEntidade||'.evento
                        ON evento.cod_evento = ferias_evento.cod_evento
                           WHERE cod_tipo = 2
                       AND timestamp <= (SELECT * FROM ultimotimestampperiodomovimentacao('||inCodPeriodoMovimentacao||', '''||stEntidade||'''))::timestamp
                        ORDER BY timestamp DESC LIMIT 1)
                
                 AND NOT (folhas.codigo::integer = ANY(string_to_array((SELECT CASE WHEN configuracao.valor != '''' THEN configuracao.valor ELSE ''0'' END
                                     FROM administracao.configuracao
                                    WHERE cod_modulo = 8
                                      AND exercicio = '''|| stExercicio ||'''
                                      AND parametro = ''remuneracao_eventual''), '','')::integer[]))';
        END IF;     
    -- COLUNA 'redutor_teto'      
        IF indice = 2 THEN
             stSql := stSql || ' WHERE folhas.codigo::integer = ANY(string_to_array((SELECT CASE WHEN configuracao.valor != '''' THEN configuracao.valor ELSE ''0'' END
                                FROM administracao.configuracao
                               WHERE cod_modulo = 8
                                 AND exercicio = '''|| stExercicio ||'''
                                 AND parametro = ''redutor_teto''), '','')::integer[])';
        END IF;
    -- COLUNA 'remuneracao_natalina'
        IF indice = 3 THEN
            stSql := stSql || ' WHERE folhas.desdobramento = ''D'' AND folhas.natureza = ''P'' AND ind_fl != ''ferias''';
        END IF;
    -- COLUNA 'remuneracao_ferias'
        IF indice = 4 THEN
                stSql := stSql || ' WHERE folhas.natureza = ''P''
                                      AND ((desdobramento in (''D'', ''A'', ''F'') and ind_fl ilike ''%ferias%'') 
                                        OR (desdobramento in (''P'', ''V'') and ind_fl ilike ''%rescisao%''))
                                      ';
        END IF;
    -- COLUNA 'remuneracao_outras';
        IF indice = 5 THEN
              stSql := stSql || ' WHERE folhas.codigo::integer = ANY(string_to_array((SELECT CASE WHEN configuracao.valor != '''' THEN configuracao.valor ELSE ''0'' END
                                  FROM administracao.configuracao
                                 WHERE cod_modulo = 8
                             AND exercicio = '''|| stExercicio ||'''
                             AND parametro = ''remuneracao_eventual''), '','')::integer[])';
        END IF;
    -- COLUNA 'deducoes_irrf'
        IF indice = 6 THEN
              stSql := stSql || ' WHERE folhas.codigo IN (SELECT evento.codigo 
                                                            FROM folhapagamento'||stEntidade||'.tabela_irrf_evento 
                                                          INNER JOIN folhapagamento'||stEntidade||'.evento
                                                              ON evento.cod_evento = tabela_irrf_evento.cod_evento
                                                           WHERE cod_tipo IN (3,6) 
                                                             AND cod_tabela = 1 
                                                             AND timestamp = (SELECT timestamp 
                                                                        FROM folhapagamento'||stEntidade||'.tabela_irrf 
                                                                       WHERE cod_tabela = 1 
                                                                         AND vigencia <= ultimotimestampperiodomovimentacao('||inCodPeriodoMovimentacao||', '''||stEntidade||''')::date
                                                                    ORDER BY timestamp DESC LIMIT 1))
                                  ';
        END IF;
    -- COLUNA 'deducoes_obrigatorias'
        IF indice = 7 THEN
                stSql := stSql || ' WHERE folhas.codigo IN (SELECT codigo 
                                                            FROM folhapagamento'||stEntidade||'.previdencia_evento 
                                                          INNER JOIN folhapagamento'||stEntidade||'.evento
                                                                  ON evento.cod_evento = previdencia_evento.cod_evento
                                                               WHERE cod_tipo = 1
                                                                                   
                                             AND cod_previdencia = (SELECT cod_previdencia 
                                                                      FROM ultimo_contrato_servidor_previdencia('''||stEntidade||''', '||inCodPeriodoMovimentacao||') 
                                                                     WHERE cod_contrato = folhas.cod_contrato
                                                                       AND bo_excluido = false) 
                                                       
                                            AND timestamp = (SELECT timestamp
                                                           FROM folhapagamento'||stEntidade||'.previdencia_previdencia
                                                          WHERE vigencia <= ultimotimestampperiodomovimentacao('||inCodPeriodoMovimentacao||', '''||stEntidade||''')::date
                                                            AND cod_previdencia = previdencia_evento.cod_previdencia
                                                       ORDER BY timestamp DESC LIMIT 1))
                              ';      
        END IF;
    -- COLUNA 'demais_deducoes'
        IF indice = 8 THEN
            stSql := stSql || ' WHERE folhas.codigo IN ( SELECT codigo
                                                                    FROM folhapagamento.evento 
                                                                    WHERE cod_evento = ANY (string_to_array((SELECT CASE WHEN configuracao.valor != ''''   
                                                                                                                        THEN configuracao.valor
                                                                                                                    ELSE ''0''
                                                                                                                    END
                                                                                                             FROM administracao.configuracao
                                                                                                             WHERE cod_modulo = 8
                                                                                                             AND exercicio = '''||stExercicio||'''
                                                                                                             AND parametro = ''demais_deducoes''), '','')::integer[] ))';
        END IF;
    -- COLUNA 'salario_familia';
        IF indice = 9 THEN
             stSql := stSql || ' WHERE folhas.codigo IN (SELECT codigo
                                                                 FROM folhapagamento'||stEntidade||'.salario_familia
                                                                 
                                                           INNER JOIN folhapagamento'||stEntidade||'.salario_familia_evento  
                                                                   ON salario_familia_evento.cod_regime_previdencia = salario_familia.cod_regime_previdencia     
                                                                  AND salario_familia_evento.timestamp              = salario_familia.timestamp                              
                                                                  AND salario_familia_evento.cod_tipo               = 1
                                                                  AND salario_familia.cod_regime_previdencia        = (SELECT cod_regime_previdencia 
                                                                                                                         FROM folhapagamento'||stEntidade||'.previdencia 
                                                                                                                        WHERE cod_previdencia = (SELECT cod_previdencia 
                                                                                                                                                   FROM ultimo_contrato_servidor_previdencia('''||stEntidade||''', '||inCodPeriodoMovimentacao||') 
                                                                                                                                                  WHERE cod_contrato = folhas.cod_contrato
                                                                                                                                                    AND bo_excluido = false))
                                                           INNER JOIN folhapagamento'||stEntidade||'.evento
                                                                   ON evento.cod_evento = salario_familia_evento.cod_evento
                                                                WHERE salario_familia.vigencia <= ultimotimestampperiodomovimentacao('||inCodPeriodoMovimentacao||', '''||stEntidade||''')::date
                                                             ORDER BY salario_familia.timestamp DESC LIMIT 1)';     
        END IF;
    -- COLUNA 'jetons'
        IF indice = 10 THEN
            stSql := stSql || ' WHERE folhas.codigo::integer = ANY(string_to_array((SELECT CASE WHEN configuracao.valor != ''''   
                                                                                                  THEN configuracao.valor
                                                                                                  ELSE ''0'' 
                                                                                                  END
                                                                                             FROM administracao.configuracao
                                                                                            WHERE cod_modulo = 8
                                                                                              AND exercicio = '''||stExercicio||'''
                                                                                              AND parametro = ''pagamento_jetons''), '','')::integer[])';  
        END IF;
    -- COLUNA 'verbas'
        IF indice = 11 THEN
            stSql := stSql || ' WHERE folhas.codigo::integer = ANY(string_to_array((SELECT CASE WHEN configuracao.valor != ''''   
                                                                                                  THEN configuracao.valor
                                                                                                  ELSE ''0'' 
                                                                                                  END
                                                                                             FROM administracao.configuracao
                                                                                            WHERE cod_modulo = 8
                                                                                              AND exercicio = '''||stExercicio||'''
                                                                                              AND parametro = ''verbas_indenizatorias''), '','')::integer[])';  
        END IF;
    
        stSql := stSql || ' GROUP BY registro';
        
        FOR reRegistro IN EXECUTE stSql LOOP
            stSqlUpdate := 'UPDATE temp_transparencia_remuneracao SET '||arTipo[indice]||' = '||quote_literal(reRegistro.valor_calculado)||' WHERE registro = '||reRegistro.registro;
            EXECUTE stSqlUpdate;
        END LOOP;
        
END LOOP;         

    stSql := 'SELECT *, ((coalesce(remuneracao_bruta::NUMERIC, 0.00) +
                           coalesce(remuneracao_natalina::NUMERIC, 0.00) +
                           coalesce(remuneracao_ferias::NUMERIC, 0.00) + 
                           coalesce(remuneracao_outras::NUMERIC, 0.00)) -
                          (coalesce(redutor_teto::NUMERIC, 0.00) + coalesce(deducoes_irrf::NUMERIC, 0.00) + coalesce(deducoes_obrigatorias::NUMERIC, 0.00) + coalesce(demais_deducoes::NUMERIC, 0.00))
                        ) as remuneracao_apos_deducoes
                FROM temp_transparencia_remuneracao';

    FOR reRegistro IN EXECUTE stSql 
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;
       
    RETURN;
    
END;
$$ LANGUAGE 'plpgsql';

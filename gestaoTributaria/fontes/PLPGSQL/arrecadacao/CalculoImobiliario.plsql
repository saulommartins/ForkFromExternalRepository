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
CREATE OR REPLACE FUNCTION CalculoImobiliario( ) RETURNS boolean AS $$
DECLARE
    inExercicio             INTEGER;
    inCodGrupo              INTEGER;
    inCodCredito            INTEGER;
    inCodEspecie            INTEGER;
    inCodGenero             INTEGER;
    inCodNatureza           INTEGER;
    inSandVar               INTEGER;
    stSandVar               NUMERIC;
    boRetorno               BOOLEAN;
    stSql                   VARCHAR;
    reRecord                RECORD;
    reRecord1               RECORD;
    inInscricaoBaixa        INTEGER;
    inCodCalculoCorrente    INTEGER;
    inRegistroCorrente      INTEGER;
    nuValor                 NUMERIC;
    stCredito               VARCHAR;
    stFuncao                VARCHAR;
    boErro                  BOOLEAN := FALSE;
    boFlagVenal             BOOLEAN := NULL;
    tsTimestamp             TIMESTAMP;
    stErro                  VARCHAR :='';
    arTemp                  VARCHAR[];
    stTemp                  VARCHAR;
    inTemp                  INTEGER;
    arFormula               VARCHAR[];
    inCodModulo1            INTEGER;
    inCodBiblioteca1        INTEGER;
    inCodFuncao1            INTEGER;
    boErroAux               BOOLEAN := FALSE;
BEGIN
    inInscricaoBaixa := null;
    boFlagVenal      := null;
    inCodGrupo       := recuperarbufferinteiro( 'inCodGrupo'  );
    inExercicio      := recuperarbufferinteiro( 'inExercicio' );
    PERFORM removerbuffertexto( 'sterro' );
    
    IF ( inCodGrupo > 0 )  THEN
        inCodGrupo := recuperarbufferinteiro( 'inCodGrupo' );
        stSql := '  SELECT apc.cod_modulo
                          , apc.cod_biblioteca
                          , apc.cod_funcao
                          , credito_grupo.*
                          , credito.*
                       FROM arrecadacao.credito_grupo
                       JOIN (
                              SELECT arrecadacao.parametro_calculo.*
                                FROM arrecadacao.parametro_calculo
                                   , (   SELECT MAX (ocorrencia_credito) AS ocorrencia
                                              , cod_credito
                                              , cod_especie
                                              , cod_genero
                                              , cod_natureza
                                           FROM arrecadacao.parametro_calculo
                                       GROUP BY cod_credito
                                              , cod_especie
                                              , cod_genero
                                              , cod_natureza
                                     ) AS apc
                               WHERE arrecadacao.parametro_calculo.cod_credito        = apc.cod_credito
                                 AND arrecadacao.parametro_calculo.cod_especie        = apc.cod_especie
                                 AND arrecadacao.parametro_calculo.cod_genero         = apc.cod_genero
                                 AND arrecadacao.parametro_calculo.cod_natureza       = apc.cod_natureza
                                 AND arrecadacao.parametro_calculo.ocorrencia_credito = apc.ocorrencia
                            ) AS apc
                         ON apc.cod_credito  = credito_grupo.cod_credito
                        AND apc.cod_especie  = credito_grupo.cod_especie
                        AND apc.cod_genero   = credito_grupo.cod_genero
                        AND apc.cod_natureza = credito_grupo.cod_natureza
                       JOIN monetario.credito
                         ON apc.cod_credito  = credito.cod_credito
                        AND apc.cod_especie  = credito.cod_especie
                        AND apc.cod_genero   = credito.cod_genero
                        AND apc.cod_natureza = credito.cod_natureza
                      WHERE credito_grupo.cod_grupo     = '|| inCodGrupo  ||'
                        AND credito_grupo.ano_exercicio = '''|| inExercicio ||'''
                   ORDER BY credito_grupo.ordem
                          ;
                 ';
    ELSE
        inCodCredito := recuperarbufferinteiro( 'inCodCredito'  );
        inCodEspecie := recuperarbufferinteiro( 'inCodEspecie'  );
        inCodGenero  := recuperarbufferinteiro( 'inCodGenero'   );
        inCodNatureza:= recuperarbufferinteiro( 'inCodNatureza' );
        stSql := ' SELECT parametro_calculo.cod_modulo
                        , parametro_calculo.cod_biblioteca
                        , parametro_calculo.cod_funcao
                        , credito.*
                     FROM (
                            SELECT parametro_calculo.*
                              FROM arrecadacao.parametro_calculo
                                 , (
                                       SELECT max (ocorrencia_credito) AS ocorrencia
                                            , cod_credito
                                            , cod_especie
                                            , cod_genero
                                            , cod_natureza
                                         FROM arrecadacao.parametro_calculo
                                     GROUP BY cod_credito
                                            , cod_especie
                                            , cod_genero
                                            , cod_natureza
                                   ) AS apc
                             WHERE parametro_calculo.cod_credito        = apc.cod_credito
                               AND parametro_calculo.cod_especie        = apc.cod_especie
                               AND parametro_calculo.cod_genero         = apc.cod_genero
                               AND parametro_calculo.cod_natureza       = apc.cod_natureza
                               AND parametro_calculo.ocorrencia_credito = apc.ocorrencia
                          ) AS parametro_calculo
                     JOIN monetario.credito
                       ON parametro_calculo.cod_credito  = credito.cod_credito
                      AND parametro_calculo.cod_especie  = credito.cod_especie
                      AND parametro_calculo.cod_genero   = credito.cod_genero
                      AND parametro_calculo.cod_natureza = credito.cod_natureza
                    WHERE parametro_calculo.cod_credito  = '|| inCodCredito  ||'
                      AND parametro_calculo.cod_especie  = '|| inCodEspecie  ||'
                      AND parametro_calculo.cod_genero   = '|| inCodGenero   ||'
                      AND parametro_calculo.cod_natureza = '|| inCodNatureza ||'
                        ;
                 ';
    END IF;

    inRegistroCorrente := recuperarbufferinteiro ( 'inRegistro' );

    FOR reRecord IN EXECUTE stSql LOOP
            IF reRecord.cod_natureza = 1 AND reRecord.cod_genero = 1 THEN
                boFlagVenal := true;
            ELSE
                boFlagVenal := false;
            END IF;
            
        BEGIN
        
            SELECT inscricao_municipal
              INTO inInscricaoBaixa
              FROM imobiliario.baixa_imovel
             WHERE inscricao_municipal = inRegistroCorrente
               AND dt_termino IS NULL
                 ;
                 
            IF inInscricaoBaixa IS NULL THEN
            
                stCredito := reRecord.cod_credito ||'.'|| reRecord.cod_especie ||'.'|| reRecord.cod_genero ||'.'|| reRecord.cod_natureza;
                stCredito := stCredito ||' '|| reRecord.descricao_credito;
                stFuncao  := reRecord.cod_modulo ||'.'|| reRecord.cod_biblioteca ||'.'|| reRecord.cod_funcao;
                
                stSandVar := executagcnumericotributario(stFuncao);
                nuValor   := stSandVar::NUMERIC;
                
                -- recuperar proximo codigo de calculo
                SELECT COALESCE(MAX(cod_calculo),0) + 1
                  INTO inCodCalculoCorrente
                  FROM arrecadacao.calculo
                     ;
                     
                stErro := getErro('');
                
                IF nuValor IS NULL OR stErro != '' THEN
                    arTemp := string_to_array(stErro, '#');
                    stTemp := 'Erro na Função ' || arTemp[1] || '(' || arTemp[2] || ')';
                    IF stTemp IS NULL THEN
                       stTemp := 'Erro na Função ' || stErro;
                    END IF;
                    
                    stErro := stTemp;
                    INSERT
                      INTO calculos_mensagem
                    VALUES
                         ( inCodCalculoCorrente
                         , stErro
                         );
                    boErro := TRUE;
                    stErro := getErro(stErro);
                    
                END IF;
                
                -- guardar codigo de calculo
                INSERT
                  INTO calculos_correntes
                VALUES
                     ( inCodCalculoCorrente
                     , nuValor
                     );

                -- calculo
                INSERT
                  INTO arrecadacao.calculo
                     ( cod_calculo
                     , cod_credito
                     , cod_especie
                     , cod_genero
                     , cod_natureza
                     , exercicio
                     , valor
                     , nro_parcelas
                     , ativo
                     )
                VALUES
                     ( inCodCalculoCorrente
                     , reRecord.cod_credito
                     , reRecord.cod_especie
                     , reRecord.cod_genero
                     , reRecord.cod_natureza
                     , inExercicio
                     , nuValor
                     , 0
                     , TRUE
                     );
                     
                IF ( stErro != '' ) THEN
                    IF (boErroAux = TRUE) THEN
                        UPDATE arrecadacao.log_calculo SET cod_calculo = inCodCalculoCorrente, valor = stFuncao || ' (' || stErro || ')' WHERE cod_calculo = inCodCalculoCorrente;
                    ELSE
                        boErroAux := TRUE;
                        INSERT
                        INTO arrecadacao.log_calculo
                        VALUES
                            ( inCodCalculoCorrente
                            , stFuncao || ' (' || stErro || ')'
                            );
                    END IF;
                ELSE
                    INSERT
                      INTO arrecadacao.log_calculo
                    VALUES
                         ( inCodCalculoCorrente
                         , 'Ok'
                         );
                END IF;
                
                IF ( boFlagVenal = TRUE ) AND ( stErro = '' ) THEN
                    -- imovel calculo
                    INSERT
                      INTO arrecadacao.imovel_calculo
                         ( cod_calculo
                         , inscricao_municipal
                         )
                    VALUES
                         ( inCodCalculoCorrente
                         , inRegistroCorrente
                         );
                ELSE

                      SELECT timestamp
                        INTO tsTimestamp
                        FROM arrecadacao.imovel_v_venal
                       WHERE inscricao_municipal = inRegistroCorrente
                    ORDER BY timestamp DESC LIMIT 1
                           ;
                           
                    -- imovel calculo
                    INSERT
                      INTO arrecadacao.imovel_calculo
                         ( cod_calculo
                         , inscricao_municipal
                         , timestamp
                         )
                    VALUES
                         ( inCodCalculoCorrente
                         , inRegistroCorrente
                         , tsTimestamp
                         );
                         
                END IF;
                
                FOR reRecord IN EXECUTE ' SELECT *
                                            FROM imobiliario.proprietario
                                           WHERE promitente = FALSE
                                             AND inscricao_municipal = '|| inRegistroCorrente ||'
                                               ;
                                        '
                LOOP
                        INSERT INTO arrecadacao.calculo_cgm VALUES (inCodCalculoCorrente, reRecord.numcgm);
                END LOOP;
                
                IF boErro =  true then
                    INSERT INTO calculos_erro VALUES (inRegistroCorrente, stCredito, stFuncao, TRUE,  nuValor);
                ELSE
                    INSERT INTO calculos_erro VALUES (inRegistroCorrente, stCredito, stFuncao, FALSE, nuValor);
                END IF;

            ELSE
                boErro := TRUE;
            END IF;

        EXCEPTION
            WHEN OTHERS THEN
                SELECT COALESCE(MAX(cod_calculo),0) + 1 INTO inCodCalculoCorrente FROM arrecadacao.calculo;
                stErro = getErro(stErro);
                INSERT INTO calculos_correntes VALUES (inCodCalculoCorrente, 0.00);
                INSERT INTO calculos_erro      VALUES (inRegistroCorrente, stErro, stFuncao, TRUE, 0.00);
                INSERT INTO calculos_mensagem  VALUES (inCodCalculoCorrente, stErro);
                
                INSERT
                  INTO arrecadacao.calculo
                     ( cod_calculo
                     , cod_credito
                     , cod_especie
                     , cod_genero
                     , cod_natureza
                     , exercicio
                     , valor
                     , nro_parcelas
                     , ativo
                     )
                VALUES
                     ( inCodCalculoCorrente
                     , reRecord.cod_credito
                     , reRecord.cod_especie
                     , reRecord.cod_genero
                     , reRecord.cod_natureza
                     , inExercicio
                     , 0.00
                     , 0
                     , FALSE
                     );
                     
                IF ( stErro != '' ) THEN
                    IF (boErroAux = TRUE) THEN
                        UPDATE arrecadacao.log_calculo SET cod_calculo = inCodCalculoCorrente, valor = stFuncao || ' (' || stErro || ')' WHERE cod_calculo = inCodCalculoCorrente;
                    ELSE
                        boErroAux := TRUE;
                        INSERT
                        INTO arrecadacao.log_calculo
                        VALUES
                            ( inCodCalculoCorrente
                            , stFuncao || ' (' || stErro || ')'
                            );
                    END IF;
                END IF;
                
                SELECT timestamp
                    INTO tsTimestamp
                    FROM arrecadacao.imovel_v_venal
                   WHERE inscricao_municipal = inRegistroCorrente
                ORDER BY timestamp DESC LIMIT 1;
                
                IF ( tsTimestamp IS NOT NULL ) THEN
                    -- imovel calculo
                    INSERT
                    INTO arrecadacao.imovel_calculo
                        ( cod_calculo
                        , inscricao_municipal
                        , timestamp
                        )
                    VALUES
                        ( inCodCalculoCorrente
                        , inRegistroCorrente
                        , tsTimestamp
                        );
                ELSE
                
                    IF (boErroAux = TRUE) THEN
                        UPDATE arrecadacao.log_calculo SET cod_calculo = inCodCalculoCorrente, valor = stFuncao || ' (' || stErro || ')' WHERE cod_calculo = inCodCalculoCorrente;
                    ELSE
                        boErroAux := TRUE;
                        INSERT
                        INTO arrecadacao.log_calculo
                        VALUES
                            ( inCodCalculoCorrente
                            , stFuncao || ' (' || stErro || ')'
                            );
                    END IF;
                END IF;
                
                FOR reRecord IN EXECUTE ' SELECT *
                                            FROM imobiliario.proprietario
                                           WHERE promitente = FALSE
                                             AND inscricao_municipal = '|| inRegistroCorrente ||'
                                               ;
                                        '
                LOOP
                        INSERT INTO arrecadacao.calculo_cgm VALUES (inCodCalculoCorrente, reRecord.numcgm);
                END LOOP;
                
                boErro = TRUE;
        END;
    END LOOP;


    IF ( boErro = FALSE ) THEN
        RETURN TRUE;
    ELSE
        FOR reRecord1 IN EXECUTE ' SELECT * FROM calculos_correntes;' LOOP
           UPDATE arrecadacao.calculo
              SET ativo = FALSE
            WHERE cod_calculo = reRecord1.cod_calculo
                ;
        END LOOP;
        RETURN FALSE;
    END IF;

END;
$$ LANGUAGE 'plpgsql';

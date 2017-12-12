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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id:  $
*
* Versão 1.98.0
*/

-----------------------------------------------------------------------------------------
-- REPOSICIONANDO FUNCAO RegraRemissaoForo NA BIBLIOTECA DE REGRAS P/ REMISSAO AUTOMATICA
-----------------------------------------------------------------------------------------

-- CORRIGE BIBLIOTECA DAS FUNCOES DOS MODULOS DA GESTAO TRIBUTARIA
CREATE OR REPLACE FUNCTION corrigeFuncaoGT( inCodFuncao     INTEGER
                                          , inCodBiblio     INTEGER
                                          , inCodModulo     INTEGER
                                          , inNewFuncao     INTEGER
                                          , inNewBiblio     INTEGER
                                          , inNewModulo     INTEGER
                                          , stNomTabela     VARCHAR
                                          ) RETURNS         INTEGER AS $$
DECLARE
    stSql       VARCHAR;
    inCount     INTEGER := 0;
    inRetorno   INTEGER;
BEGIN

    stSql := '
                UPDATE '|| stNomTabela ||'
                   SET cod_funcao     = '|| inNewFuncao ||'
                     , cod_biblioteca = '|| inNewBiblio ||'
                     , cod_modulo     = '|| inNewModulo ||'
                 WHERE cod_funcao     = '|| inCodFuncao ||'
                   AND cod_biblioteca = '|| inCodBiblio ||'
                   AND cod_modulo     = '|| inCodModulo ||'
                     ;
             ';
    EXECUTE stSql;

    GET DIAGNOSTICS inCount = ROW_COUNT;

    IF inCount > 0 THEN
        inRetorno := 0;
    ELSE
        inRetorno := 1;
    END IF;

    RETURN inRetorno;

END;
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION corrigeDuplicidade( stNomeFuncao     VARCHAR
                                             ) RETURNS          VOID AS $$
DECLARE
    inNewFuncao     INTEGER;
    inNewBiblio     INTEGER;
    inNewModulo     INTEGER;
    inCodBiblio     INTEGER;
    inCodModulo     INTEGER;

    stSql           VARCHAR;
    crCursor        REFCURSOR;
    reRecord        RECORD;

    stSqlRH         VARCHAR;
    crCursorRH      REFCURSOR;
    reRecordRH      RECORD;

    inRetorno       INTEGER;
BEGIN

      SELECT cod_funcao
           , cod_biblioteca
           , cod_modulo
        INTO inNewFuncao
           , inNewBiblio
           , inNewModulo
        FROM administracao.funcao
       WHERE nom_funcao = stNomeFuncao
    ORDER BY cod_funcao
       LIMIT 1
           ;

    stSql := '
                SELECT *
                  FROM administracao.funcao
                 WHERE nom_funcao =  \''|| stNomeFuncao ||'\'
                   AND cod_funcao <>   '|| inNewFuncao  ||'
                     ;
             ';
    OPEN crCursor FOR EXECUTE stSql;
    LOOP
        FETCH crCursor INTO reRecord;
        EXIT WHEN NOT FOUND;

        SELECT cod_biblioteca
             , cod_modulo
          INTO inCodBiblio
             , inCodModulo
          FROM administracao.funcao
         WHERE nom_funcao = reRecord.nom_funcao
           AND cod_funcao = reRecord.cod_funcao
             ;

        inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'arrecadacao.desoneracao'            );
        inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'arrecadacao.parametro_calculo'      );
        inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'arrecadacao.regra_desoneracao_grupo');
        inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'divida.modalidade_acrescimo'        );
        inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'divida.modalidade_reducao'          );
        inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'divida.modalidade_reducao_acrescimo');
        inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'divida.modalidade_reducao_credito'  );
        inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'divida.modalidade_vigencia'         );
        inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'fiscalizacao.penalidade_multa'      );
        inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'monetario.formula_acrescimo'        );
        inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'monetario.formula_indicador'        );
        inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'monetario.regra_conversao_moeda'    );
        inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'monetario.regra_desoneracao_credito');
        inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'monetario.tipo_convenio'            );

        stSqlRH := '
                    SELECT nspname
                      FROM pg_namespace
                     WHERE nspname ilike \'pessoal%\'
                         ;
                   ';
        OPEN crCursorRH FOR EXECUTE stSqlRH;
        LOOP
            FETCH crCursorRH INTO reRecordRH;
            EXIT WHEN NOT FOUND;

            inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, reRecordRH.nspname||'.assentamento_vinculado_funcao'); 
            inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, reRecordRH.nspname||'.pensao_funcao'                );

        END LOOP;
        CLOSE crCursorRH;

        stSqlRH := '
                    SELECT nspname
                      FROM pg_namespace
                     WHERE nspname ilike \'folhapagamento%\'
                         ;
                   ';
        OPEN crCursorRH FOR EXECUTE stSqlRH;
        LOOP
            FETCH crCursorRH INTO reRecordRH;
            EXIT WHEN NOT FOUND;

            inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, reRecordRH.nspname||'.bases'                   );
            inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, reRecordRH.nspname||'.configuracao_evento_caso');
            inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, reRecordRH.nspname||'.pensao_funcao_padrao'    );
            inRetorno := corrigeFuncaoGT(reRecord.cod_funcao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, reRecordRH.nspname||'.tipo_media'              );

        END LOOP;
        CLOSE crCursorRH;

        DELETE
          FROM administracao.funcao
         WHERE cod_funcao     = reRecord.cod_funcao
           AND cod_biblioteca = reRecord.cod_biblioteca
           AND cod_modulo     = reRecord.cod_modulo
             ;

        DELETE
          FROM administracao.funcao_externa
         WHERE cod_funcao     = reRecord.cod_funcao
           AND cod_biblioteca = reRecord.cod_biblioteca
           AND cod_modulo     = reRecord.cod_modulo
             ;

        DELETE
          FROM administracao.corpo_funcao_externa
         WHERE cod_funcao     = reRecord.cod_funcao
           AND cod_biblioteca = reRecord.cod_biblioteca
           AND cod_modulo     = reRecord.cod_modulo
             ;

        DELETE
          FROM administracao.variavel
         WHERE cod_funcao     = reRecord.cod_funcao
           AND cod_biblioteca = reRecord.cod_biblioteca
           AND cod_modulo     = reRecord.cod_modulo
             ;

        DELETE
          FROM administracao.parametro
         WHERE cod_funcao     = reRecord.cod_funcao
           AND cod_biblioteca = reRecord.cod_biblioteca
           AND cod_modulo     = reRecord.cod_modulo
             ;

        DELETE
          FROM administracao.atributo_funcao
         WHERE cod_funcao     = reRecord.cod_funcao
           AND cod_biblioteca = reRecord.cod_biblioteca
           AND cod_modulo     = reRecord.cod_modulo
             ;

        DELETE
          FROM administracao.funcao_referencia
         WHERE cod_funcao     = reRecord.cod_funcao
           AND cod_biblioteca = reRecord.cod_biblioteca
           AND cod_modulo     = reRecord.cod_modulo
             ;

    END LOOP;
    CLOSE crCursor;

END;
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION migraFuncaoGT( stNomeFuncao    VARCHAR
                                        , inNewBiblio     INTEGER
                                        , inNewModulo     INTEGER
                                        ) RETURNS         INTEGER AS $$
DECLARE
    inCodFuncao     INTEGER;
    inCodBiblio     INTEGER;
    inCodModulo     INTEGER;
    inNewFuncao     INTEGER;
    inCountFuncao   INTEGER;
    inRetorno       INTEGER := 0;

    stSqlRH         VARCHAR;
    crCursorRH      REFCURSOR;
    reRecordRH      RECORD;
BEGIN

    SELECT COUNT(*)
      INTO inCountFuncao
      FROM administracao.funcao
     WHERE nom_funcao = stNomeFuncao;

    IF inCountFuncao > 1 THEN

        EXECUTE corrigeDuplicidade(stNomeFuncao);

    END IF;

    SELECT cod_funcao
         , cod_biblioteca
         , cod_modulo
      INTO inCodFuncao
         , inCodBiblio
         , inCodModulo
      FROM administracao.funcao
     WHERE nom_funcao = stNomeFuncao
         ;

    IF FOUND AND inCodBiblio <> inNewBiblio OR inCodModulo <> inNewModulo THEN

        PERFORM 1
           FROM administracao.funcao
          WHERE cod_funcao     = inCodFuncao
            AND cod_biblioteca = inNewBiblio
            AND cod_modulo     = inNewModulo
              ;

        IF FOUND THEN
            SELECT MAX(cod_funcao) + 1
              INTO inNewFuncao
              FROM administracao.funcao
             WHERE cod_biblioteca = inNewBiblio
               AND cod_modulo     = inNewModulo
                 ;
        ELSE
            inNewFuncao := inCodFuncao;
        END IF;

        UPDATE administracao.funcao
           SET cod_funcao     = inNewFuncao
             , cod_biblioteca = inNewBiblio
             , cod_modulo     = inNewModulo
         WHERE cod_funcao     = inCodFuncao
           AND cod_biblioteca = inCodBiblio
           AND cod_modulo     = inCodModulo
             ;

        UPDATE administracao.funcao_externa
           SET cod_funcao     = inNewFuncao
             , cod_biblioteca = inNewBiblio
             , cod_modulo     = inNewModulo
         WHERE cod_funcao     = inCodFuncao
           AND cod_biblioteca = inCodBiblio
           AND cod_modulo     = inCodModulo
             ;

        UPDATE administracao.corpo_funcao_externa
           SET cod_funcao     = inNewFuncao
             , cod_biblioteca = inNewBiblio
             , cod_modulo     = inNewModulo
         WHERE cod_funcao     = inCodFuncao
           AND cod_biblioteca = inCodBiblio
           AND cod_modulo     = inCodModulo
             ;

        UPDATE administracao.variavel
           SET cod_funcao     = inNewFuncao
             , cod_biblioteca = inNewBiblio
             , cod_modulo     = inNewModulo
         WHERE cod_funcao     = inCodFuncao
           AND cod_biblioteca = inCodBiblio
           AND cod_modulo     = inCodModulo
             ;

        UPDATE administracao.parametro
           SET cod_funcao     = inNewFuncao
             , cod_biblioteca = inNewBiblio
             , cod_modulo     = inNewModulo
         WHERE cod_funcao     = inCodFuncao
           AND cod_biblioteca = inCodBiblio
           AND cod_modulo     = inCodModulo
             ;

        UPDATE administracao.atributo_funcao
           SET cod_funcao     = inNewFuncao
             , cod_biblioteca = inNewBiblio
             , cod_modulo     = inNewModulo
         WHERE cod_funcao     = inCodFuncao
           AND cod_biblioteca = inCodBiblio
           AND cod_modulo     = inCodModulo
             ;

        UPDATE administracao.funcao_referencia
           SET cod_funcao     = inNewFuncao
             , cod_biblioteca = inNewBiblio
             , cod_modulo     = inNewModulo
         WHERE cod_funcao     = inCodFuncao
           AND cod_biblioteca = inCodBiblio
           AND cod_modulo     = inCodModulo
             ;

        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'arrecadacao.desoneracao'            ) + inRetorno;
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'arrecadacao.parametro_calculo'      ) + inRetorno;
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'arrecadacao.regra_desoneracao_grupo') + inRetorno;
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'divida.modalidade_acrescimo'        ) + inRetorno;
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'divida.modalidade_reducao'          ) + inRetorno;
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'divida.modalidade_reducao_acrescimo') + inRetorno;
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'divida.modalidade_reducao_credito'  ) + inRetorno;
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'divida.modalidade_vigencia'         ) + inRetorno;
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'fiscalizacao.penalidade_multa'      ) + inRetorno;
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'monetario.formula_acrescimo'        ) + inRetorno;
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'monetario.formula_indicador'        ) + inRetorno;
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'monetario.regra_conversao_moeda'    ) + inRetorno;
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'monetario.regra_desoneracao_credito') + inRetorno;
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'monetario.tipo_convenio'            ) + inRetorno;

        stSqlRH := '
                    SELECT nspname
                      FROM pg_namespace
                     WHERE nspname ilike \'pessoal%\'
                         ;
                   ';
        OPEN crCursorRH FOR EXECUTE stSqlRH;
        LOOP
            FETCH crCursorRH INTO reRecordRH;
            EXIT WHEN NOT FOUND;

            inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, reRecordRH.nspname||'.assentamento_vinculado_funcao') + inRetorno;
            inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, reRecordRH.nspname||'.pensao_funcao'                ) + inRetorno;

        END LOOP;
        CLOSE crCursorRH;

        stSqlRH := '
                    SELECT nspname
                      FROM pg_namespace
                     WHERE nspname ilike \'folhapagamento%\'
                         ;
                   ';
        OPEN crCursorRH FOR EXECUTE stSqlRH;
        LOOP
            FETCH crCursorRH INTO reRecordRH;
            EXIT WHEN NOT FOUND;

            inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, reRecordRH.nspname||'.bases'                   ) + inRetorno;
            inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, reRecordRH.nspname||'.configuracao_evento_caso') + inRetorno;
            inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, reRecordRH.nspname||'.pensao_funcao_padrao'    ) + inRetorno;
            inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, reRecordRH.nspname||'.tipo_media'              ) + inRetorno;

        END LOOP;
        CLOSE crCursorRH;

    END IF;

    RETURN inRetorno;

END;
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION dropaChavesRH() RETURNS VOID AS $$
DECLARE
    stSqlRH     VARCHAR;
    crCursorRH  REFCURSOR;
    reRecordRH  RECORD;
BEGIN
        stSqlRH := '
                    SELECT nspname
                      FROM pg_namespace
                     WHERE nspname ilike \'pessoal%\'
                         ;
                   ';
        OPEN crCursorRH FOR EXECUTE stSqlRH;
        LOOP
            FETCH crCursorRH INTO reRecordRH;
            EXIT WHEN NOT FOUND;

            stSqlRH := '
                        ALTER TABLE '|| reRecordRH.nspname ||'.assentamento_vinculado_funcao DROP CONSTRAINT fk_assentamento_vinculado_funcao_1;
                       ';
            EXECUTE stSqlRH;
            stSqlRH := '
                        ALTER TABLE '|| reRecordRH.nspname ||'.pensao_funcao                 DROP CONSTRAINT fk_pensao_funcao_2;
                       ';
            EXECUTE stSqlRH;

        END LOOP;
        CLOSE crCursorRH;

        stSqlRH := '
                    SELECT nspname
                      FROM pg_namespace
                     WHERE nspname ilike \'folhapagamento%\'
                         ;
                   ';
        OPEN crCursorRH FOR EXECUTE stSqlRH;
        LOOP
            FETCH crCursorRH INTO reRecordRH;
            EXIT WHEN NOT FOUND;

            stSqlRH := '
                        ALTER TABLE '|| reRecordRH.nspname ||'.bases                         DROP CONSTRAINT fk_bases_1;
                       ';
            EXECUTE stSqlRH;
            stSqlRH := '
                        ALTER TABLE '|| reRecordRH.nspname ||'.configuracao_evento_caso      DROP CONSTRAINT fk_configuracao_evento_caso_2;
                       ';
            EXECUTE stSqlRH;
            stSqlRH := '
                        ALTER TABLE '|| reRecordRH.nspname ||'.pensao_funcao_padrao          DROP CONSTRAINT fk_pensao_funcao_padrao_1;
                       ';
            EXECUTE stSqlRH;
            stSqlRH := '
                        ALTER TABLE '|| reRecordRH.nspname ||'.tipo_media                    DROP CONSTRAINT fk_tipo_media_1;
                       ';
            EXECUTE stSqlRH;

        END LOOP;
        CLOSE crCursorRH;
END;
$$ LANGUAGE 'plpgsql';


SELECT        dropaChavesRH();
DROP FUNCTION dropaChavesRH();

DROP TRIGGER tr_atualiza_ultima_modalidade_divida ON divida.modalidade_vigencia;

ALTER TABLE monetario.tipo_convenio                 DROP CONSTRAINT fk_banco_1;
ALTER TABLE monetario.regra_desoneracao_credito     DROP CONSTRAINT fk_regra_desoneracao_credito_2;
ALTER TABLE monetario.regra_conversao_moeda         DROP CONSTRAINT fk_regra_conversao_moeda_2;
ALTER TABLE monetario.formula_indicador             DROP CONSTRAINT fk_formula_indicador_2;
ALTER TABLE monetario.formula_acrescimo             DROP CONSTRAINT fk_formula_acrescimo_2;
ALTER TABLE fiscalizacao.penalidade_multa           DROP CONSTRAINT fk_penalidade_multa_3;
ALTER TABLE divida.modalidade_vigencia              DROP CONSTRAINT fk_modalidade_vigencia_5;
ALTER TABLE divida.modalidade_reducao_acrescimo     DROP CONSTRAINT fk_modalidade_reducao_acrescimo_1;
ALTER TABLE divida.modalidade_reducao_credito       DROP CONSTRAINT fk_modalidade_reducao_credito_1;
ALTER TABLE divida.modalidade_reducao               DROP CONSTRAINT fk_modalidade_reducao_2;
ALTER TABLE divida.modalidade_acrescimo             DROP CONSTRAINT fk_modalidade_acrescimo_3;
ALTER TABLE arrecadacao.regra_desoneracao_grupo     DROP CONSTRAINT fk_regra_grupo_2;
ALTER TABLE arrecadacao.parametro_calculo           DROP CONSTRAINT fk_parametro_calculo_2;
ALTER TABLE arrecadacao.desoneracao                 DROP CONSTRAINT fk_desoneracao_4;
ALTER TABLE administracao.parametro                 DROP CONSTRAINT fk_parametro_1;
ALTER TABLE administracao.variavel                  DROP CONSTRAINT fk_variavel_1;
ALTER TABLE administracao.funcao_referencia         DROP CONSTRAINT fk_funcao_referencia_2;
ALTER TABLE administracao.corpo_funcao_externa      DROP CONSTRAINT fk_corpo_funcao_externa_1;
ALTER TABLE administracao.funcao_externa            DROP CONSTRAINT fk_funcao_externa_1;
ALTER TABLE administracao.atributo_funcao           DROP CONSTRAINT fk_atributo_funcao_2;

-- FUNCOES DIVIDA
SELECT migraFuncaoGT('RegraRemissaoForo'                                                         , 4, 33);-- 33  1   8 

-- RECRIA CHAVES DAS TABELAS E DROPA FUNCOES CRIADAS
CREATE OR REPLACE FUNCTION criaChavesRH() RETURNS VOID AS $$
DECLARE
    stSqlRH     VARCHAR;
    crCursorRH  REFCURSOR;
    reRecordRH  RECORD;
BEGIN
        stSqlRH := '
                    SELECT nspname
                      FROM pg_namespace
                     WHERE nspname ilike \'pessoal%\'
                         ;
                   ';
        OPEN crCursorRH FOR EXECUTE stSqlRH;
        LOOP
            FETCH crCursorRH INTO reRecordRH;
            EXIT WHEN NOT FOUND;

            stSqlRH := '
                        ALTER TABLE '|| reRecordRH.nspname ||'.assentamento_vinculado_funcao ADD  CONSTRAINT fk_assentamento_vinculado_funcao_1 FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
                       ';
            EXECUTE stSqlRH;
            stSqlRH := '
                        ALTER TABLE '|| reRecordRH.nspname ||'.pensao_funcao                 ADD  CONSTRAINT fk_pensao_funcao_2 FOREIGN KEY (cod_biblioteca, cod_modulo, cod_funcao) REFERENCES administracao.funcao(cod_biblioteca, cod_modulo, cod_funcao);
                       ';
            EXECUTE stSqlRH;

        END LOOP;
        CLOSE crCursorRH;

        stSqlRH := '
                    SELECT nspname
                      FROM pg_namespace
                     WHERE nspname ilike \'folhapagamento%\'
                         ;
                   ';
        OPEN crCursorRH FOR EXECUTE stSqlRH;
        LOOP
            FETCH crCursorRH INTO reRecordRH;
            EXIT WHEN NOT FOUND;

            stSqlRH := '
                        ALTER TABLE '|| reRecordRH.nspname ||'.bases                         ADD  CONSTRAINT fk_bases_1 FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
                       ';
            EXECUTE stSqlRH;
            stSqlRH := '
                        ALTER TABLE '|| reRecordRH.nspname ||'.configuracao_evento_caso      ADD  CONSTRAINT fk_configuracao_evento_caso_2 FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
                       ';
            EXECUTE stSqlRH;
            stSqlRH := '
                        ALTER TABLE '|| reRecordRH.nspname ||'.pensao_funcao_padrao          ADD  CONSTRAINT fk_pensao_funcao_padrao_1 FOREIGN KEY (cod_funcao, cod_biblioteca, cod_modulo) REFERENCES administracao.funcao(cod_funcao, cod_biblioteca, cod_modulo);
                       ';
            EXECUTE stSqlRH;
            stSqlRH := '
                        ALTER TABLE '|| reRecordRH.nspname ||'.tipo_media                    ADD  CONSTRAINT fk_tipo_media_1 FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
                       ';
            EXECUTE stSqlRH;

        END LOOP;
        CLOSE crCursorRH;
END;
$$ LANGUAGE 'plpgsql';


SELECT        criaChavesRH();
DROP FUNCTION criaChavesRH();

ALTER TABLE administracao.atributo_funcao           ADD  CONSTRAINT fk_atributo_funcao_2              FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
ALTER TABLE administracao.funcao_externa            ADD  CONSTRAINT fk_funcao_externa_1               FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
ALTER TABLE administracao.corpo_funcao_externa      ADD  CONSTRAINT fk_corpo_funcao_externa_1         FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao_externa(cod_modulo, cod_biblioteca, cod_funcao);
ALTER TABLE administracao.funcao_referencia         ADD  CONSTRAINT fk_funcao_referencia_2            FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
ALTER TABLE administracao.variavel                  ADD  CONSTRAINT fk_variavel_1                     FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
ALTER TABLE administracao.parametro                 ADD  CONSTRAINT fk_parametro_1                    FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao, cod_variavel) REFERENCES administracao.variavel(cod_modulo, cod_biblioteca, cod_funcao, cod_variavel);
ALTER TABLE arrecadacao.desoneracao                 ADD  CONSTRAINT fk_desoneracao_4                  FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
ALTER TABLE arrecadacao.parametro_calculo           ADD  CONSTRAINT fk_parametro_calculo_2            FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
ALTER TABLE arrecadacao.regra_desoneracao_grupo     ADD  CONSTRAINT fk_regra_grupo_2                  FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
ALTER TABLE divida.modalidade_acrescimo             ADD  CONSTRAINT fk_modalidade_acrescimo_3         FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
ALTER TABLE divida.modalidade_reducao               ADD  CONSTRAINT fk_modalidade_reducao_2           FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
ALTER TABLE divida.modalidade_reducao_credito       ADD  CONSTRAINT fk_modalidade_reducao_credito_1   FOREIGN KEY (timestamp, cod_modalidade, cod_funcao, cod_biblioteca, cod_modulo) REFERENCES divida.modalidade_reducao(timestamp, cod_modalidade, cod_funcao, cod_biblioteca, cod_modulo);
ALTER TABLE divida.modalidade_reducao_acrescimo     ADD  CONSTRAINT fk_modalidade_reducao_acrescimo_1 FOREIGN KEY (timestamp, cod_modalidade, cod_funcao, cod_biblioteca, cod_modulo) REFERENCES divida.modalidade_reducao(timestamp, cod_modalidade, cod_funcao, cod_biblioteca, cod_modulo);
ALTER TABLE divida.modalidade_vigencia              ADD  CONSTRAINT fk_modalidade_vigencia_5          FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
ALTER TABLE fiscalizacao.penalidade_multa           ADD  CONSTRAINT fk_penalidade_multa_3             FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
ALTER TABLE monetario.formula_acrescimo             ADD  CONSTRAINT fk_formula_acrescimo_2            FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
ALTER TABLE monetario.formula_indicador             ADD  CONSTRAINT fk_formula_indicador_2            FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
ALTER TABLE monetario.regra_conversao_moeda         ADD  CONSTRAINT fk_regra_conversao_moeda_2        FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
ALTER TABLE monetario.regra_desoneracao_credito     ADD  CONSTRAINT fk_regra_desoneracao_credito_2    FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);
ALTER TABLE monetario.tipo_convenio                 ADD  CONSTRAINT fk_banco_1                        FOREIGN KEY (cod_modulo, cod_biblioteca, cod_funcao) REFERENCES administracao.funcao(cod_modulo, cod_biblioteca, cod_funcao);

CREATE TRIGGER tr_atualiza_ultima_modalidade_divida BEFORE INSERT OR UPDATE ON divida.modalidade_vigencia FOR EACH ROW EXECUTE PROCEDURE divida.fn_atualiza_ultima_modalidade_divida();

DROP FUNCTION migraFuncaoGT(VARCHAR, INTEGER, INTEGER);
DROP FUNCTION corrigeFuncaoGT(INTEGER, INTEGER, INTEGER, INTEGER, INTEGER, INTEGER, VARCHAR);
DROP FUNCTION corrigeDuplicidade(VARCHAR);


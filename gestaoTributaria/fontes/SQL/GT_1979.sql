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
* Versão 1.97.9
*/

----------------
-- Ticket #15554
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND parametro = 'cnpj'
        AND exercicio = '2009'
        AND valor     = '94068418000184'
          ;

    IF FOUND THEN

        ALTER TABLE divida.emissao_documento DROP CONSTRAINT fk_emissao_documento_1;
        
        UPDATE divida.emissao_documento    SET cod_documento = 9  WHERE cod_documento = 13 AND cod_tipo_documento = 3;
        UPDATE divida.modalidade_documento SET cod_documento = 9  WHERE cod_documento = 13 AND cod_tipo_documento = 3;
        
        UPDATE divida.emissao_documento    SET cod_documento = 25 WHERE cod_documento = 26 AND cod_tipo_documento = 5;
        UPDATE divida.modalidade_documento SET cod_documento = 25 WHERE cod_documento = 26 AND cod_tipo_documento = 5;
        
        UPDATE divida.emissao_documento    SET cod_documento = 8  WHERE cod_documento = 12 AND cod_tipo_documento = 4;
        UPDATE divida.modalidade_documento SET cod_documento = 8  WHERE cod_documento = 12 AND cod_tipo_documento = 4;
        
        UPDATE divida.emissao_documento    SET cod_documento = 6  WHERE cod_documento = 10 AND cod_tipo_documento = 2;
        UPDATE divida.modalidade_documento SET cod_documento = 6  WHERE cod_documento = 10 AND cod_tipo_documento = 2;
        
        ALTER TABLE divida.emissao_documento ADD  CONSTRAINT fk_emissao_documento_1 FOREIGN KEY (num_parcelamento, cod_tipo_documento, cod_documento)
                                                                                    REFERENCES divida.documento(num_parcelamento, cod_tipo_documento, cod_documento);
        
        -- DOC 13 - Certidao de Divida Mariana - duplicado
        DELETE FROM administracao.modelo_arquivos_documento WHERE cod_documento = 13 AND cod_tipo_documento = 3;
        DELETE FROM administracao.arquivos_documento        WHERE cod_arquivo   = 13 AND sistema = true;
        DELETE FROM administracao.modelo_documento          WHERE cod_documento = 13 AND cod_tipo_documento = 3;
        
        -- DOC 26 - Notificacao DA - duplicado
        DELETE FROM administracao.modelo_arquivos_documento WHERE cod_documento = 26 AND cod_tipo_documento = 5;
        DELETE FROM administracao.arquivos_documento        WHERE cod_arquivo   = 29 AND sistema = true;
        DELETE FROM administracao.modelo_documento          WHERE cod_documento = 26 AND cod_tipo_documento = 5;
        
        -- DOC 12 - Notificacao de Divida Marnaia - duplicado
        DELETE FROM administracao.modelo_arquivos_documento WHERE cod_documento = 12 AND cod_tipo_documento = 4;
        DELETE FROM administracao.arquivos_documento        WHERE cod_arquivo   = 12 AND sistema = true;
        DELETE FROM administracao.modelo_documento          WHERE cod_documento = 12 AND cod_tipo_documento = 4;
        
        -- DOC 10 - Termo de Inscricao Mariana - duplicado
        DELETE FROM administracao.modelo_arquivos_documento WHERE cod_documento = 10 AND cod_tipo_documento = 2;
        DELETE FROM administracao.arquivos_documento        WHERE cod_arquivo   = 10 AND sistema = true;
        DELETE FROM administracao.modelo_documento          WHERE cod_documento = 10 AND cod_tipo_documento = 2;
        
        -- DOC 7 - Termo Parcelamento Mariana
        DELETE FROM divida.modalidade_documento             WHERE cod_documento = 7  AND cod_tipo_documento = 4;
        DELETE FROM divida.documento_parcela                WHERE cod_documento = 7  AND cod_tipo_documento = 4;
        DELETE FROM divida.emissao_documento                WHERE cod_documento = 7  AND cod_tipo_documento = 4;
        DELETE FROM divida.documento                        WHERE cod_documento = 7  AND cod_tipo_documento = 4;
        DELETE FROM administracao.modelo_arquivos_documento WHERE cod_documento = 7  AND cod_tipo_documento = 4;
        DELETE FROM administracao.arquivos_documento        WHERE cod_arquivo   = 7  AND sistema = true;
        DELETE FROM administracao.modelo_documento          WHERE cod_documento = 7  AND cod_tipo_documento = 4;
        
        -- DOC 11 - Notificacao de Acordo Mariana
        DELETE FROM divida.modalidade_documento             WHERE cod_documento = 11 AND cod_tipo_documento = 4;
        DELETE FROM divida.documento_parcela                WHERE cod_documento = 11 AND cod_tipo_documento = 4;
        DELETE FROM divida.emissao_documento                WHERE cod_documento = 11 AND cod_tipo_documento = 4;
        DELETE FROM divida.documento                        WHERE cod_documento = 11 AND cod_tipo_documento = 4;
        DELETE FROM administracao.modelo_arquivos_documento WHERE cod_documento = 11 AND cod_tipo_documento = 4;
        DELETE FROM administracao.arquivos_documento        WHERE cod_arquivo   = 11 AND sistema = true;
        DELETE FROM administracao.modelo_documento          WHERE cod_documento = 11 AND cod_tipo_documento = 4;
        
        
        DELETE FROM administracao.modelo_arquivos_documento WHERE cod_documento = 6 AND cod_tipo_documento = 2 and cod_acao NOT IN (1634, 1635, 1637, 1639, 1725);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1639, 6, 6, TRUE, TRUE, 2);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1725, 6, 6, TRUE, TRUE, 2);
        
        DELETE FROM administracao.modelo_arquivos_documento WHERE cod_documento = 8 AND cod_tipo_documento = 4 and cod_acao NOT IN (1634, 1635, 1639, 1648, 1849, 1725);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1639, 8, 8, TRUE, TRUE, 4);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1849, 8, 8, TRUE, TRUE, 4);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1725, 8, 8, TRUE, TRUE, 4);
        
        DELETE FROM administracao.modelo_arquivos_documento WHERE cod_documento = 9 AND cod_tipo_documento = 3 and cod_acao NOT IN (1634, 1635, 1639, 1648, 1849, 1725);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1639, 9, 9, TRUE, TRUE, 3);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1849, 9, 9, TRUE, TRUE, 3);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1725, 9, 9, TRUE, TRUE, 3);
        
        
        DELETE FROM administracao.modelo_arquivos_documento WHERE cod_documento = 19 AND cod_tipo_documento = 5 and cod_acao NOT IN (1634, 1635, 1637, 1639, 1725);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1639, 19, 22, TRUE, TRUE, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1725, 19, 22, TRUE, TRUE, 5);
        
        DELETE FROM administracao.modelo_arquivos_documento WHERE cod_documento = 20 AND cod_tipo_documento = 5 and cod_acao NOT IN (1634, 1635, 1639, 1648, 1849, 1725);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1639, 20, 23, TRUE, TRUE, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1849, 20, 23, TRUE, TRUE, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1725, 20, 23, TRUE, TRUE, 5);
        
        DELETE FROM administracao.modelo_arquivos_documento WHERE cod_documento = 21 AND cod_tipo_documento = 5 and cod_acao NOT IN (1634, 1635, 1639, 1648, 1849, 1725);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1639, 21, 24, TRUE, TRUE, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1849, 21, 24, TRUE, TRUE, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1725, 21, 24, TRUE, TRUE, 5);
        
        DELETE FROM administracao.modelo_arquivos_documento WHERE cod_documento = 22 AND cod_tipo_documento = 5 and cod_acao NOT IN (1634, 1635, 1639, 1648, 1849, 1725);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1639, 22, 25, TRUE, TRUE, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1849, 22, 25, TRUE, TRUE, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1725, 22, 25, TRUE, TRUE, 5);
        
        DELETE FROM administracao.modelo_arquivos_documento WHERE cod_documento = 23 AND cod_tipo_documento = 5 and cod_acao NOT IN (1634, 1635, 1639, 1648, 1849, 1725);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1639, 23, 26, TRUE, TRUE, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1849, 23, 26, TRUE, TRUE, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1725, 23, 26, TRUE, TRUE, 5);
        
        DELETE FROM administracao.modelo_arquivos_documento WHERE cod_documento = 25 AND cod_tipo_documento = 5 and cod_acao NOT IN (1634, 1635, 1639, 1648, 1849, 1725);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1639, 25, 28, TRUE, TRUE, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1849, 25, 28, TRUE, TRUE, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1725, 25, 28, TRUE, TRUE, 5);

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #15607
----------------
INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
VALUES ( '2009'
     , 33
     , 'documento_remissao'
     , ''
     );


----------------
-- Ticket #15542
----------------

ALTER TABLE economico.servico_atividade ADD COLUMN ativo BOOLEAN NOT NULL DEFAULT TRUE;


----------------------------------
-- MANUTENCAO DAS FUNCOES/FORMULAS
----------------------------------

-- CAD. IMOBILIARIO
UPDATE administracao.biblioteca SET nom_biblioteca = 'Cad. Imobiliário - Geral'               WHERE cod_modulo = 12 and cod_biblioteca = 1;
UPDATE administracao.biblioteca SET nom_biblioteca = 'Cad. Imobiliário - Fórmulas de Cálculo' WHERE cod_modulo = 12 and cod_biblioteca = 2;

-- CAD. ECONOMICO
UPDATE administracao.biblioteca SET nom_biblioteca = 'Cad. Econômico - Geral'                 WHERE cod_modulo = 14 and cod_biblioteca = 1;
INSERT
  INTO administracao.biblioteca
     ( cod_modulo
     , cod_biblioteca
     , nom_biblioteca
     )
VALUES ( 14
     , 2
     , 'Cad. Econômico - Fórmulas de Cálculo'
     );

-- ARRECADACAO
UPDATE administracao.biblioteca SET nom_biblioteca = 'Arrecadação - Fórmulas de Cálculo'      WHERE cod_modulo = 25 and cod_biblioteca = 1;
UPDATE administracao.biblioteca SET nom_biblioteca = 'Arrecadação - Regras de Desoneração'    WHERE cod_modulo = 25 and cod_biblioteca = 2;
UPDATE administracao.biblioteca SET nom_biblioteca = 'Arrecadação - Fórmulas de Desoneração'  WHERE cod_modulo = 25 and cod_biblioteca = 3;
INSERT
  INTO administracao.biblioteca
     ( cod_modulo
     , cod_biblioteca
     , nom_biblioteca
     )
VALUES ( 25
     , 4
     , 'Arrecadação - Geral'
     );

-- CAD. MONETARIO
UPDATE administracao.biblioteca SET nom_biblioteca = 'Cad. Monetário - Geral'                 WHERE cod_modulo = 28 and cod_biblioteca = 1;
INSERT
  INTO administracao.biblioteca
     ( cod_modulo
     , cod_biblioteca
     , nom_biblioteca
     )
VALUES ( 28
     , 2
     , 'Cad. Monetário - Acréscimos'
     );
INSERT
  INTO administracao.biblioteca
     ( cod_modulo
     , cod_biblioteca
     , nom_biblioteca
     )
VALUES ( 28
     , 3
     , 'Cad. Monetário - Indicadores Econômicos'
     );
INSERT
  INTO administracao.biblioteca
     ( cod_modulo
     , cod_biblioteca
     , nom_biblioteca
     )
VALUES ( 28
     , 4
     , 'Cad. Monetário - Moedas'
     );

-- DIVIDA ATIVA
UPDATE administracao.biblioteca SET nom_biblioteca = 'Dívida Ativa - Regras p/ Modalidade'    WHERE cod_modulo = 33 and cod_biblioteca = 1;
INSERT
  INTO administracao.biblioteca
     ( cod_modulo
     , cod_biblioteca
     , nom_biblioteca
     )
VALUES ( 33
     , 2
     , 'Dívida Ativa - Regras p/ Acréscimos'
     );
INSERT
  INTO administracao.biblioteca
     ( cod_modulo
     , cod_biblioteca
     , nom_biblioteca
     )
VALUES ( 33
     , 3
     , 'Dívida Ativa - Regras p/ Reduções'
     );
INSERT
  INTO administracao.biblioteca
     ( cod_modulo
     , cod_biblioteca
     , nom_biblioteca
     )
VALUES ( 33
     , 4
     , 'Dívida Ativa - Regras p/ Remissão Automática'
     );

-- FISCALIZACAO
INSERT
  INTO administracao.biblioteca
     ( cod_modulo
     , cod_biblioteca
     , nom_biblioteca
     )
VALUES ( 34
     , 1
     , 'Fiscalização - Multas de Infração'
     );


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


SELECT migraFuncaoGT('recuperaCadastroImobiliarioImovelIsencaoIPTU'                              , 1, 12);-- 12  1  36 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioImovelIsencaoTSU'                               , 1, 12);-- 12  1  37 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioImovelLimitacao'                                , 1, 12);-- 12  1  38 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioImovelTipoDeIsencao'                            , 1, 12);-- 12  1  39 
SELECT migraFuncaoGT('atributoTipoEdificacaoValorClassificacaoEdificacao'                        , 1, 12);-- 12  1   5 
SELECT migraFuncaoGT('atributoTipoEdificacaoValorClassificacaoForro'                             , 1, 12);-- 12  1   6 
SELECT migraFuncaoGT('atributoTipoEdificacaoValorClassificacaoInstalacaoEletrica'                , 1, 12);-- 12  1   7 
SELECT migraFuncaoGT('atributoTipoEdificacaoValorClassificacaoPiso'                              , 1, 12);-- 12  1   8 
SELECT migraFuncaoGT('atributoTipoEdificacaoValorClassificacaoRevestimento'                      , 1, 12);-- 12  1   9 
SELECT migraFuncaoGT('atributoTipoEdificacaoValorClassificacaoInstalacaSanitaria'                , 1, 12);-- 12  1  10 
SELECT migraFuncaoGT('atributoTipoEdificacaoValorClassificacaoEstrutura'                         , 1, 12);-- 12  1  11 
SELECT migraFuncaoGT('atributoTipoEdificacaoValorClassificacaoFachada'                           , 1, 12);-- 12  1  12 
SELECT migraFuncaoGT('atributoTipoEdificacaoValorClassificacaoOcupacaoEdificacao'                , 1, 12);-- 12  1  13 
SELECT migraFuncaoGT('atributoTipoEdificacaoValorClassificacaoPosicionamento'                    , 1, 12);-- 12  1  14 
SELECT migraFuncaoGT('atributoTipoEdificacaoValorClassificacaoSituacaoEdificacao'                , 1, 12);-- 12  1  15 
SELECT migraFuncaoGT('imobiliario.fn_area_real'                                                  , 1, 12);-- 12  1  29 
SELECT migraFuncaoGT('imobiliario.fn_calcula_area_imovel'                                        , 1, 12);-- 12  1  30 
SELECT migraFuncaoGT('imobiliario.fn_calcula_area_imovel_lote'                                   , 1, 12);-- 12  1  31 
SELECT migraFuncaoGT('imobiliario.fn_busca_lote_imovel'                                          , 1, 12);-- 12  1  45 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioImovelUtilizacaoDoImovel'                       , 1, 12);-- 12  1  40 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioImovelZona'                                     , 1, 12);-- 12  1  41 
SELECT migraFuncaoGT('recuperaTrechoValorMetroQuadradoTerritorial'                               , 1, 12);-- 12  1  32 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioImovelOcupacaoDoTerreno'                        , 1, 12);-- 12  1  42 
SELECT migraFuncaoGT('recuperaQuantidadeImovelPorLote'                                           , 1, 12);-- 12  1  34 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioImovelUsoDoSolo'                                , 1, 12);-- 12  1  35 
SELECT migraFuncaoGT('imobiliario.buscaAliquotaIPTU'                                             , 4, 25);-- 12  1  53 
SELECT migraFuncaoGT('calculafracaoIdeal'                                                        , 1, 12);-- 12  1  54 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioImovelEspecificacaoComercial'                   , 1, 12);-- 12  1  43 
SELECT migraFuncaoGT('imobiliario.recuperaPrimeiroNivelLocalizacaoImovel'                        , 1, 12);-- 12  1  44 
SELECT migraFuncaoGT('CalculaImpostoPredial'                                                     , 2, 12);-- 12  1  20 
SELECT migraFuncaoGT('CalculaImpostoTerritorial'                                                 , 2, 12);-- 12  1  19 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioLoteUrbanoDescontoValorVenal'                   , 1, 12);-- 12  1  46 
SELECT migraFuncaoGT('CalculaIluminacaoPublica'                                                  , 2, 12);-- 12  1  21 
SELECT migraFuncaoGT('atributoTipoEdificacao'                                                    , 1, 12);-- 12  1  50 
SELECT migraFuncaoGT('atributoTipoEdificacaoValorCobertura'                                      , 1, 12);-- 12  1  47 
SELECT migraFuncaoGT('atributoTipoEdificacaoValorConstrucao'                                     , 1, 12);-- 12  1  48 
SELECT migraFuncaoGT('imobiliario.fn_busca_codigo_edificacao'                                    , 1, 12);-- 12  1  49 
SELECT migraFuncaoGT('CalculaTaxaDeLimpeza'                                                      , 2, 12);-- 12  1  22 
SELECT migraFuncaoGT('recuperaTrechoValorMetroQuadradoPredial'                                   , 1, 12);-- 12  1  51 
SELECT migraFuncaoGT('recuperaFaceQuadraImovel'                                                  , 1, 12);-- 12  1  31 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioTipoDeEdificacaoCOBERTURA'                      , 1, 12);-- 12  1  39 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioTipoDeEdificacaoPAREDES'                        , 1, 12);-- 12  1  40 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioLoteUrbanoSITUACAO'                             , 1, 12);-- 12  1  32 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioLoteUrbanoTOPOGRAFIA'                           , 1, 12);-- 12  1  33 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioLoteUrbanoPEDOLOGIA'                            , 1, 12);-- 12  1  34 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioTipoDeEdificacaoUTILIZACAO'                     , 1, 12);-- 12  1  35 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioTipoDeEdificacaoLOCACAO'                        , 1, 12);-- 12  1  36 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioTipoDeEdificacaoREVESTIMENTOEXTERNO'            , 1, 12);-- 12  1  37 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioTipoDeEdificacaoFORRO'                          , 1, 12);-- 12  1  38 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioTipoDeEdificacaoESTRUTURA'                      , 1, 12);-- 12  1  41 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioTipoDeEdificacaoVEDACOES'                       , 1, 12);-- 12  1  42 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioTipoDeEdificacaoSITUACAO'                       , 1, 12);-- 12  1  43 
SELECT migraFuncaoGT('imobiliario.busca_testada.maior_extensao_imovel'                           , 1, 12);-- 12  1  52 
SELECT migraFuncaoGT('imobiliario.busca_testada_extensao_endereco_imovel'                        , 1, 12);-- 12  1  53 
SELECT migraFuncaoGT('TaxadeLixo'                                                                , 2, 12);-- 12  1  51 
SELECT migraFuncaoGT('CalculaTaxadeGuia'                                                         , 1, 25);-- 12  1  57 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioLoteUrbanoLOTE'                                 , 1, 12);-- 12  1  54 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioLoteUrbanoQUADRA'                               , 1, 12);-- 12  1  55 
SELECT migraFuncaoGT('CalculaIPTU'                                                               , 2, 12);-- 12  1  30 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioImovelVALORVENALDOIMOVEL'                       , 1, 12);-- 12  1  14 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioImovelVALORVENALURM'                            , 1, 12);-- 12  1  15 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioImovelUtilizacao'                               , 1, 12);-- 12  1  18 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioImovelTIPODEIMOVEL'                             , 1, 12);-- 12  1  19 
SELECT migraFuncaoGT('bkp_calculaiptu'                                                           , 2, 12);-- 12  1  42 
SELECT migraFuncaoGT('calculaIPTUmariana'                                                        , 2, 12);-- 12  1  52 
SELECT migraFuncaoGT('calculaiptu'                                                               , 2, 12);-- 12  1   6 
SELECT migraFuncaoGT('TaxaLixo'                                                                  , 2, 12);-- 12  1  13 
SELECT migraFuncaoGT('recuperaLogradouroTrechoImovel'                                            , 1, 12);-- 12  1  31 
SELECT migraFuncaoGT('recuperaTrechoImovel'                                                      , 1, 12);-- 12  1  32 
SELECT migraFuncaoGT('imobiliario.busca_localizacao_por_nivel'                                   , 1, 12);-- 12  1  33 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioTrechoColetaDeLixo'                             , 1, 12);-- 12  1  36 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioTrechoEsgoto'                                   , 1, 12);-- 12  1  37 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioTrechoGuiasESarjetas'                           , 1, 12);-- 12  1  38 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioTrechoIluminacaoPublica'                        , 1, 12);-- 12  1  39 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioTrechoPavimentacao'                             , 1, 12);-- 12  1  40 
SELECT migraFuncaoGT('recuperaCadastroImobiliarioTipoDeEdificacaoEstadoDeConservacao'            , 1, 12);-- 12  1  41 
SELECT migraFuncaoGT('TaxaServicosDiversos'                                                      , 1, 25);-- 12  1  42 
SELECT migraFuncaoGT('calculataxalixo'                                                           , 2, 12);-- 12  1  43 
        
-- FUNCOES ECONOMICO
SELECT migraFuncaoGT('atributoElementoCadastroEconomicoValorQuantidadeApartamento'               , 1, 14);-- 14  1   1 
SELECT migraFuncaoGT('atributoElementoCadastroEconomicoValorISSAnual'                            , 1, 14);-- 14  1   2 
SELECT migraFuncaoGT('atributoElementoCadastroEconomicoValorCREA'                                , 1, 14);-- 14  1   3 
SELECT migraFuncaoGT('atributoElementoCadastroEconomicoValorPlacaVeiculo'                        , 1, 14);-- 14  1   4 
SELECT migraFuncaoGT('atributoElementoCadastroEconomivoValor'                                    , 1, 14);-- 14  1   5 
SELECT migraFuncaoGT('atributoElementoCadastroEconomivoValorQtdCaixasRegistradoras'              , 1, 14);-- 14  1   6 
SELECT migraFuncaoGT('atributoElementoCadastroEconomivoValorClasse'                              , 1, 14);-- 14  1   7 
SELECT migraFuncaoGT('atributoElementoCadastroEconomivoValorMetragem'                            , 1, 14);-- 14  1   8 
SELECT migraFuncaoGT('atributoElementoCadastroEconomivoValorQtdVeiculos'                         , 1, 14);-- 14  1   9 
SELECT migraFuncaoGT('buscaQtdTotalAtividadeDaInscricaoEconomica'                                , 1, 14);-- 14  1  10 
SELECT migraFuncaoGT('buscaCodigoAtividadeDaInscricaoEconomica'                                  , 1, 14);-- 14  1  11 
SELECT migraFuncaoGT('buscaTipoDaInscricaoEconomica'                                             , 1, 14);-- 14  1  12 
SELECT migraFuncaoGT('buscaAberturaInscricaoMunicipalData'                                       , 1, 14);-- 14  1  23 
SELECT migraFuncaoGT('buscaAberturaInscricaoMunicipalDia'                                        , 1, 14);-- 14  1  24 
SELECT migraFuncaoGT('buscaAberturaInscricaoMunicipalMes'                                        , 1, 14);-- 14  1  25 
SELECT migraFuncaoGT('buscaAberturaInscricaoMunicipalAno'                                        , 1, 14);-- 14  1  26 
SELECT migraFuncaoGT('buscaInicioInscricaoMunicipalData'                                         , 1, 14);-- 14  1  27 
SELECT migraFuncaoGT('buscaInicioInscricaoMunicipalDia'                                          , 1, 14);-- 14  1  28 
SELECT migraFuncaoGT('buscaInicioInscricaoMunicipalMes'                                          , 1, 14);-- 14  1  29 
SELECT migraFuncaoGT('buscaInicioInscricaoMunicipalAno'                                          , 1, 14);-- 14  1  30 
SELECT migraFuncaoGT('CalculaTLF'                                                                , 2, 14);-- 14  1  14 
SELECT migraFuncaoGT('CalculaTLL'                                                                , 2, 14);-- 14  1  31 
SELECT migraFuncaoGT('recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoIsentoDeTaxas'     , 1, 14);-- 14  1  44 
SELECT migraFuncaoGT('isencaoTaxasEconomico'                                                     , 2, 14);-- 14  1  33 
SELECT migraFuncaoGT('CalculaISSFixo'                                                            , 2, 14);-- 14  1  13 
SELECT migraFuncaoGT('CalculaTFF'                                                                , 2, 14);-- 14  1  32 
SELECT migraFuncaoGT('economico.fn_busca_atributos_elementos'                                    , 1, 14);-- 14  1  36 
SELECT migraFuncaoGT('buscaAliquotaAtividade'                                                    , 1, 14);-- 14  1  37 
SELECT migraFuncaoGT('recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoIsentoDeTaxas'  , 1, 14);-- 14  1  45 
SELECT migraFuncaoGT('recuperaCadastroEconomicoInscricaoEconomicaAutonomoIsentoDeTaxas'          , 1, 14);-- 14  1  46 
SELECT migraFuncaoGT('atributoElementoAtividade'                                                 , 1, 14);-- 14  1  38 
SELECT migraFuncaoGT('buscaOcorrenciaAtividade'                                                  , 1, 14);-- 14  1  39 
SELECT migraFuncaoGT('buscaCodigoElemento'                                                       , 1, 14);-- 14  1  40 
SELECT migraFuncaoGT('buscaOcorrenciaElemento'                                                   , 1, 14);-- 14  1  41 
SELECT migraFuncaoGT('buscaValorAtributoElemento'                                                , 1, 14);-- 14  1  42 
SELECT migraFuncaoGT('buscaBairroEmpresa'                                                        , 1, 14);-- 14  1  43 
SELECT migraFuncaoGT('CalculaIssPF'                                                              , 2, 14);-- 14  1  47 
SELECT migraFuncaoGT('economico.buscaMesAberturaEmpresa'                                         , 1, 14);-- 14  1 331 
SELECT migraFuncaoGT('Calcula_TLF'                                                               , 2, 14);-- 14  1 329 
SELECT migraFuncaoGT('CalculaTaxasDiversas'                                                      , 1, 25);-- 14  1  23 
SELECT migraFuncaoGT('CalculaTaxaExpediente'                                                     , 1, 25);-- 14  1 330 
SELECT migraFuncaoGT('CalculaTLLF'                                                               , 2, 14);-- 14  1  21 
SELECT migraFuncaoGT('recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoValorDeclarado' , 1, 14);-- 14  1   3 
SELECT migraFuncaoGT('CalculaISS'                                                                , 2, 14);-- 14  1   2 
SELECT migraFuncaoGT('economico.buscabairroempresa'                                              , 1, 14);-- 14  1  37 
          
-- FUNCOES ARRECADACAO
SELECT migraFuncaoGT(' recuperaArrecadacaoDesoneracaoAbono'                                      , 3, 25);-- 25  1  38 
SELECT migraFuncaoGT('fn_honorarios'                                                             , 2, 28);-- 25  1  94 
SELECT migraFuncaoGT('desoneracaoAbono'                                                          , 3, 25);-- 25  1  40 
SELECT migraFuncaoGT('numeracaoConsolidacao'                                                     , 4, 25);-- 25  1  41 
SELECT migraFuncaoGT('arrecadacao.fn_busca_aliquota_valor_financiado_itbi'                       , 4, 25);-- 25  1  43 
SELECT migraFuncaoGT('arrecadacao.fn_busca_valor_financiado_itbi'                                , 4, 25);-- 25  1  44 
SELECT migraFuncaoGT('arrecadacao.fn_verifica_tbl4'                                              , 4, 25);-- 25  1  45 
SELECT migraFuncaoGT('arrecadacao.fn_busca_tabela_conversao'                                     , 4, 25);-- 25  1  46 
SELECT migraFuncaoGT('arrecadacao.fn_tbl_p1_p2atep3_int'                                         , 4, 25);-- 25  1  47 
SELECT migraFuncaoGT('arrecadacao.fn_tbl_p1_p2atep3_num'                                         , 4, 25);-- 25  1  48 
SELECT migraFuncaoGT('arrecadacao.fn_tbl_p1_p2atep3'                                             , 4, 25);-- 25  1  49 
SELECT migraFuncaoGT('arrecadacao.fn_verifica_tbl1p2'                                            , 4, 25);-- 25  1  50 
SELECT migraFuncaoGT('arrecadacao.fn_verifica_tbl'                                               , 4, 25);-- 25  1  51 
SELECT migraFuncaoGT('arrecadacao.verificaEdificacaoImovel'                                      , 1, 12);-- 25  1  52 
SELECT migraFuncaoGT('ultimaNumeracaoFebraban'                                                   , 4, 25);-- 25  1  55 
SELECT migraFuncaoGT('arrecadacao.fn_busca_valor_venal_territorial_calculado'                    , 4, 25);-- 25  1  56 
SELECT migraFuncaoGT('arrecadacao.fn_grava_venal'                                                , 4, 25);-- 25  1  57 
SELECT migraFuncaoGT('arrecadacao.fn_atualiza_venal'                                             , 4, 25);-- 25  1  58 
SELECT migraFuncaoGT('NumeracaoFebraban'                                                         , 4, 25);-- 25  1  54 
SELECT migraFuncaoGT('NumeracaoBradesco'                                                         , 4, 25);-- 25  1  59 
SELECT migraFuncaoGT('arrecadacao.fn_busca_aliquota_valor_avaliado_itbi'                         , 4, 25);-- 25  1  75 
SELECT migraFuncaoGT('arrecadacao.fn_busca_valor_declarado_itbi'                                 , 4, 25);-- 25  1  76 
SELECT migraFuncaoGT('arrecadacao.fn_busca_valor_calculado_itbi'                                 , 4, 25);-- 25  1  77 
SELECT migraFuncaoGT('arrecadacao.fn_busca_valor_informado_itbi'                                 , 4, 25);-- 25  1  78 
SELECT migraFuncaoGT('arrecadacao.fn_vc2num'                                                     , 4, 25);-- 25  1  79 
SELECT migraFuncaoGT('arrecadacao.fn_vc2int'                                                     , 4, 25);-- 25  1  80 
SELECT migraFuncaoGT('fn_multa_2006'                                                             , 2, 28);-- 25  1  86 
SELECT migraFuncaoGT('fn_acrescimo_indice'                                                       , 2, 28);-- 25  1  85 
SELECT migraFuncaoGT('fn_multa_10_porcento'                                                      , 2, 28);-- 25  1  91 
SELECT migraFuncaoGT('arrecadacao.fn_retorna_valor_IP'                                           , 4, 25);-- 25  1  87 
SELECT migraFuncaoGT('arrecadacao.fn_retorna_valor_IT'                                           , 4, 25);-- 25  1  88 
SELECT migraFuncaoGT('imobiliario.buscaQuantidadeUnidadesDependentes'                            , 1, 12);-- 25  1 101 
SELECT migraFuncaoGT('arrecadacao.buscaValorCalculoCredito'                                      , 4, 25);-- 25  1  95 
SELECT migraFuncaoGT('arrecadacao.fn_num2vc'                                                     , 4, 25);-- 25  1  96 
SELECT migraFuncaoGT('fn_juros_0pt50_porcento'                                                   , 2, 28);-- 25  1  93 
SELECT migraFuncaoGT('regraConcessaoDesoneracaoGeral'                                            , 2, 25);-- 25  2  97 
SELECT migraFuncaoGT('fn_juros_0pt75_porcento'                                                   , 2, 28);-- 25  1  92 
SELECT migraFuncaoGT('arrecadacao.fn_int2vc'                                                     , 4, 25);-- 25  1  97 
SELECT migraFuncaoGT('fn_multa_simples_quinze_por_cento'                                         , 2, 28);-- 25  1 399 
SELECT migraFuncaoGT('regraConcessaoDesoneracaoIPTU2008'                                         , 2, 25);-- 25  2  98 
SELECT migraFuncaoGT('arrecadacao.fn_tbl_p1_p2_p3_p4_num'                                        , 4, 25);-- 25  1  98 
SELECT migraFuncaoGT('arrecadacao.fn_tipo_edificacao'                                            , 1, 12);-- 25  1  99 
SELECT migraFuncaoGT('diff_datas_em_dias'                                                        , 1, 28);-- 25  1  89 
SELECT migraFuncaoGT('retornaDataAtual'                                                          , 1, 28);-- 25  1  90 
SELECT migraFuncaoGT('calculaitbi'                                                               , 1, 25);-- 25  1  83 
SELECT migraFuncaoGT('arrecadacao.buscaForo'                                                     , 4, 25);-- 25  1 403 
SELECT migraFuncaoGT('fn_juros_1_porcento'                                                       , 2, 28);-- 25  1  81 
SELECT migraFuncaoGT('fn_multa_mora'                                                             , 2, 28);-- 25  1  82 
SELECT migraFuncaoGT('fn_correcao_indice'                                                        , 2, 28);-- 25  1 404 
SELECT migraFuncaoGT('arrecadacao.buscaValorCreditoLancamento'                                   , 4, 25);-- 25  1 401 
SELECT migraFuncaoGT('arrecadacao.buscaInscricaoLancamento'                                      , 4, 25);-- 25  1 402 
SELECT migraFuncaoGT('imobiliario.fn_busca_codigo_unidade_dependente'                            , 1, 12);-- 25  1 102 
SELECT migraFuncaoGT('imobiliario.fn_calcula_area_unidade_dependente'                            , 1, 12);-- 25  1 103 
SELECT migraFuncaoGT('imobiliario.fn_calcula_area_unidade_autonoma'                              , 1, 12);-- 25  1 104 
SELECT migraFuncaoGT('atributoEdificacao'                                                        , 1, 12);-- 25  1 105 
SELECT migraFuncaoGT('fn_juros_simples_um_porcento'                                              , 2, 28);-- 25  1 109 
SELECT migraFuncaoGT('fn_multa_020_por_cento_ao_dia'                                             , 2, 28);-- 25  1 110 
SELECT migraFuncaoGT('fn_multa_simples_dois_por_cento'                                           , 2, 28);-- 25  1 111 
SELECT migraFuncaoGT('fn_correcao_mariana'                                                       , 2, 28);-- 25  1 400 
SELECT migraFuncaoGT('regraConcessaoDesoneracaoIPTU2009'                                         , 2, 25);-- 25  2  99 
SELECT migraFuncaoGT('desoneracaoIPTU2009'                                                       , 3, 25);-- 25  3 102 
SELECT migraFuncaoGT('notaAvulsa'                                                                , 1, 25);-- 25  1 397 
SELECT migraFuncaoGT('fn_multa_2_porcento_mariana'                                               , 2, 28);-- 25  1 398 
SELECT migraFuncaoGT('desoneracaoIPTU2008'                                                       , 3, 25);-- 25  3 101 
SELECT migraFuncaoGT('monetario.buscaValorAcrescimo'                                             , 1, 28);-- 25  1 405 
SELECT migraFuncaoGT('arrecadacao.fn_retorna_valor_calculo_grupo_credito'                        , 4, 25);-- 25  1 406 
SELECT migraFuncaoGT('imobiliario.fn_buscaDataInscricaoImovel'                                   , 1, 12);-- 25  1 407 
SELECT migraFuncaoGT('fn_multa_simples_dez_por_cento'                                            , 2, 28);-- 25  1 323 
SELECT migraFuncaoGT('fn_juros_mata'                                                             , 2, 28);-- 25  1  66 
SELECT migraFuncaoGT('fn_multa_mata'                                                             , 2, 28);-- 25  1  67 
SELECT migraFuncaoGT('fn_multa_mata_2006'                                                        , 2, 28);-- 25  1  70 
SELECT migraFuncaoGT('arrecadacao.fn_busca_testada'                                              , 1, 12);-- 25  1 318 
SELECT migraFuncaoGT('fn_comissao_cobranca_mariana'                                              , 2, 28);-- 25  1 403 
SELECT migraFuncaoGT('arrecadacao.fn_ultimo_venal_por_im'                                        , 4, 25);-- 25  1  42 
SELECT migraFuncaoGT('arrecadacao.fn_busca_valores_itbi'                                         , 4, 25);-- 25  1  45 
SELECT migraFuncaoGT('arrecadacao.fn_grava_faturamento'                                          , 4, 25);-- 25  1  46 
SELECT migraFuncaoGT('ITBI'                                                                      , 1, 25);-- 25  1  44 
SELECT migraFuncaoGT('TaxaExpediente'                                                            , 1, 25);-- 25  1  47 
SELECT migraFuncaoGT('imobiliario.busca_testada_maior_extensao_imovel'                           , 1, 12);-- 25  1  95 
SELECT migraFuncaoGT('fn_juros_mariana'                                                          , 2, 28);-- 25  1  49 
SELECT migraFuncaoGT('fn_multa_vigencia_atual'                                                   , 2, 28);-- 25  1  50 
SELECT migraFuncaoGT('fn_multa_mariana'                                                          , 2, 28);-- 25  1 395 

-- FUNCOES MONETARIO
-- FUNCOES DIVIDA
SELECT migraFuncaoGT('regraReemissaoMata2008'                                                    , 4, 33);-- 33  1   8 
SELECT migraFuncaoGT('NumeracaoDivida'                                                           , 4, 25);-- 33  1   1 
SELECT migraFuncaoGT('Migração Dívida'                                                           , 0, 33);-- 33  0   0 
SELECT migraFuncaoGT('regraModalidadeGeralDivida'                                                , 1, 33);-- 33  1   2 
SELECT migraFuncaoGT('regraReducaoGeralDivida'                                                   , 3, 33);-- 33  1   4 
SELECT migraFuncaoGT('fn_acrescimo_infracao_iptu'                                                , 2, 28);-- 33  1   5 
SELECT migraFuncaoGT('fn_acrescimo_infracao_tff'                                                 , 2, 28);-- 33  1   6 
SELECT migraFuncaoGT('fn_acrescimo_infracao_iss'                                                 , 2, 28);-- 33  1   7 
SELECT migraFuncaoGT('regraAcrescimoGeralDivida'                                                 , 2, 33);-- 33  1   3 
SELECT migraFuncaoGT('RegraReducaoAcrescimos45'                                                  , 3, 33);-- 33  1  14 
SELECT migraFuncaoGT('RegraReducaoAcrescimos90'                                                  , 3, 33);-- 33  1  10 
SELECT migraFuncaoGT('RegraReducaoAcrescimos100'                                                 , 3, 33);-- 33  1   9 
SELECT migraFuncaoGT('RegraReducaoAcrescimos50'                                                  , 3, 33);-- 33  1  13 
SELECT migraFuncaoGT('RegraReducaoAcrescimos35'                                                  , 3, 33);-- 33  1  16 
SELECT migraFuncaoGT('RegraReducaoAcrescimos70'                                                  , 3, 33);-- 33  1  12 
SELECT migraFuncaoGT('regraInscricaoGeralDivida'                                                 , 1, 33);-- 33  1   2 
SELECT migraFuncaoGT('regraAcrescimoFalseDivida'                                                 , 2, 33);-- 33  1   6 
SELECT migraFuncaoGT('RegraReducaoAcrescimos40'                                                  , 3, 33);-- 33  1  15 
SELECT migraFuncaoGT('RegraReducaoAcrescimos80'                                                  , 3, 33);-- 33  1  11 


-- ELIMINA FUNCAO 'regraReemissaoMata2008' DOS DEMAIS MUNICIPIOS QUE NAO 'Mata de Sao Joao'
CREATE OR REPLACE FUNCTION deletaFuncao( stNomFuncao    VARCHAR
                                       ) RETURNS        INTEGER AS $$
DECLARE
    stSql           VARCHAR;

    inCodFuncao     INTEGER;
    inCodBiblio     INTEGER;
    inCodModulo     INTEGER;
    inNewFuncao     INTEGER;
    inNewBiblio     INTEGER;
    inNewModulo     INTEGER;

    inRetorno       INTEGER;
BEGIN

    SELECT cod_funcao
         , cod_biblioteca
         , cod_modulo
      INTO inCodFuncao
         , inCodBiblio
         , inCodModulo
      FROM administracao.funcao
     WHERE nom_funcao = stNomFuncao
         ;

    SELECT cod_funcao
         , cod_biblioteca
         , cod_modulo
      INTO inNewFuncao
         , inNewBiblio
         , inNewModulo
      FROM administracao.funcao
     WHERE nom_funcao ilike '%o% %nformad%';

        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'arrecadacao.desoneracao'            );
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'arrecadacao.parametro_calculo'      );
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'arrecadacao.regra_desoneracao_grupo');
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'divida.modalidade_acrescimo'        );
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'divida.modalidade_reducao'          );
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'divida.modalidade_reducao_acrescimo');
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'divida.modalidade_reducao_credito'  );
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'divida.modalidade_vigencia'         );
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'fiscalizacao.penalidade_multa'      );
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'monetario.formula_acrescimo'        );
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'monetario.formula_indicador'        );
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'monetario.regra_conversao_moeda'    );
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'monetario.regra_desoneracao_credito');
        inRetorno := corrigeFuncaoGT(inCodFuncao, inCodBiblio, inCodModulo, inNewFuncao, inNewBiblio, inNewModulo, 'monetario.tipo_convenio'            );

    DELETE
      FROM administracao.parametro
     WHERE cod_funcao     = inCodFuncao
       AND cod_biblioteca = inCodBiblio
       AND cod_modulo     = inCodModulo
         ;

    DELETE
      FROM administracao.variavel
     WHERE cod_funcao     = inCodFuncao
       AND cod_biblioteca = inCodBiblio
       AND cod_modulo     = inCodModulo
         ;

    DELETE
      FROM administracao.atributo_funcao
     WHERE cod_funcao     = inCodFuncao
       AND cod_biblioteca = inCodBiblio
       AND cod_modulo     = inCodModulo
         ;

    DELETE
      FROM administracao.corpo_funcao_externa
     WHERE cod_funcao     = inCodFuncao
       AND cod_biblioteca = inCodBiblio
       AND cod_modulo     = inCodModulo
         ;

    DELETE
      FROM administracao.funcao_referencia
     WHERE cod_funcao     = inCodFuncao
       AND cod_biblioteca = inCodBiblio
       AND cod_modulo     = inCodModulo
         ;

    DELETE
      FROM administracao.funcao_externa
     WHERE cod_funcao     = inCodFuncao
       AND cod_biblioteca = inCodBiblio
       AND cod_modulo     = inCodModulo
         ;

    DELETE
      FROM administracao.funcao
     WHERE cod_funcao     = inCodFuncao
       AND cod_biblioteca = inCodBiblio
       AND cod_modulo     = inCodModulo
         ;

    GET DIAGNOSTICS inRetorno = ROW_COUNT;

    PERFORM 1
       FROM pg_proc
      WHERE proname = LOWER(stNomFuncao);

    IF FOUND THEN
        stSql := ' DROP FUNCTION '|| stNomFuncao ||'(INTGER, NUMERIC);';
        EXECUTE stSql;
    END IF;
   
    RETURN inRetorno; 

END;
$$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    inExecute   INTEGER;
BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE parametro = 'cnpj'
        AND exercicio = '2009'
        AND valor     = '13805528000180';

    IF NOT FOUND THEN
        inExecute := deletaFuncao('regraReemissaoMata2008');
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao(); 
DROP FUNCTION manutencao();


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
DROP FUNCTION deletaFuncao(VARCHAR);


CREATE OR REPLACE FUNCTION cadastraRegras() RETURNS VOID AS $$
DECLARE
    stPL            VARCHAR;
    inCodFuncao     INTEGER;
BEGIN

-- REGRA MODALIDADE GERAL
    PERFORM 1
       FROM administracao.funcao
      WHERE nom_funcao = 'regraModalidadeGeralDivida'
          ;

    IF NOT FOUND THEN
        SELECT COALESCE( MAX(cod_funcao) + 1, 1 )
          INTO inCodFuncao
          FROM administracao.funcao
         WHERE cod_modulo     = 33
           AND cod_biblioteca = 1
             ;

        INSERT
          INTO administracao.funcao
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_tipo_retorno
             , nom_funcao )
        VALUES ( 33
             , 1
             , inCodFuncao
             , 3
             , 'regraModalidadeGeralDivida'
             );
        INSERT
          INTO administracao.funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , comentario
             , corpo_pl
             , corpo_ln )
        VALUES ( 33
             , 1
             , inCodFuncao
             , ''
             , 'FUNCTION regraModalidadeGeralDivida(INTEGER) RETURNS BOOLEAN as \'
                DECLARE
                INREGISTRO ALIAS FOR $1;
                
                BORETORNO BOOLEAN := TRUE;
                BEGIN
                RETURN BORETORNO;
                END;
                \' LANGUAGE \'plpgsql\';'
             , ''
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 33
             , 1
             , inCodFuncao
             , 1
             , '0'
             , 'RETORNA #boRetorno'
             );
        INSERT
          INTO administracao.variavel
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , cod_tipo
             , nom_variavel
             , valor_inicial )
        VALUES ( 33
             , 1
             , inCodFuncao
             , 1
             , 3
             , 'boRetorno'
             , 'VERDADEIRO'
             );
        INSERT
          INTO administracao.variavel
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , cod_tipo
             , nom_variavel
             , valor_inicial )
        VALUES ( 33
             , 1
             , inCodFuncao
             , 2
             , 1
             , 'inRegistro'
             , ''
             );
        INSERT
          INTO administracao.parametro
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , ordem )
        VALUES ( 33
             , 1
             , inCodFuncao
             , 2
             , 0
             );
    END IF;

-- REGRA ACRESCIMO GERAL
    PERFORM 1
       FROM administracao.funcao
      WHERE nom_funcao = 'regraAcrescimoGeralDivida'
          ;

    IF NOT FOUND THEN
        SELECT COALESCE( MAX(cod_funcao) + 1, 1 )
          INTO inCodFuncao
          FROM administracao.funcao
         WHERE cod_modulo     = 33
           AND cod_biblioteca = 2
             ;

        INSERT
          INTO administracao.funcao
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_tipo_retorno
             , nom_funcao )
        VALUES ( 33
             , 2
             , inCodFuncao
             , 3
             , 'regraAcrescimoGeralDivida'
             );
        INSERT
          INTO administracao.funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , comentario
             , corpo_pl
             , corpo_ln )
        VALUES ( 33
             , 2
             , inCodFuncao
             , ''
             , 'FUNCTION regraAcrescimoGeralDivida(INTEGER) RETURNS BOOOLEAN as \'
                DECLARE
                INREGISTRO ALIAS FOR $1;
                
                  BORETORNO BOOLEAN := TRUE;
                BEGIN
                RETURN BORETORNO;
                END;
                 \' LANGUAGE \'plpgsql\';'
             , ''
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 33
             , 2
             , inCodFuncao
             , 1
             , '0'
             , 'RETORNA #boRetorno'
             );
        INSERT
          INTO administracao.variavel
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , cod_tipo
             , nom_variavel
             , valor_inicial )
        VALUES ( 33
             , 2
             , inCodFuncao
             , 1
             , 3
             , 'boRetorno'
             , 'VERDADEIRO'
             );
        INSERT
          INTO administracao.variavel
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , cod_tipo
             , nom_variavel
             , valor_inicial )
        VALUES ( 33
             , 2
             , inCodFuncao
             , 2
             , 1
             , 'inRegistro'
             , ''
             );
        INSERT
          INTO administracao.parametro
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , ordem )
        VALUES ( 33
             , 2
             , inCodFuncao
             , 2
             , 0
             );

    END IF;

    PERFORM 1
       FROM administracao.funcao
      WHERE nom_funcao = 'regraReducaoGeralDivida'
          ;

    IF NOT FOUND THEN
        SELECT COALESCE( MAX(cod_funcao) + 1, 1 )
          INTO inCodFuncao
          FROM administracao.funcao
         WHERE cod_modulo     = 33
           AND cod_biblioteca = 3
             ;

        INSERT
          INTO administracao.funcao
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_tipo_retorno
             , nom_funcao )
        VALUES ( 33
             , 3
             , inCodFuncao
             , 3
             , 'regraReducaoGeralDivida'
             );
        INSERT
          INTO administracao.funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , comentario
             , corpo_pl
             , corpo_ln )
        VALUES ( 33
             , 3
             , inCodFuncao
             , ''
             , 'FUNCTION regraReducaoGeralDivida(INTEGER,DATE,INTEGER) RETURNS BOOLEAN as \' 
                DECLARE
                INREGISTRO ALIAS FOR $1;
                DTVENCIMENTO ALIAS FOR $2;
                INQUANTPARCELAS ALIAS FOR $3;
                
                  BORETORNO BOOLEAN;
                BEGIN
                BORETORNO := TRUE;
                RETURN BORETORNO;
                END;
                \' LANGUAGE \'plpgsql\';'
             , ''
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 33
             , 3
             , inCodFuncao
             , 1
             , '0'
             , '#boRetorno <- VERDADEIRO;'
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 33
             , 3
             , inCodFuncao
             , 2
             , '0'
             , 'RETORNA #boRetorno'
             );
        INSERT
          INTO administracao.variavel
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , cod_tipo
             , nom_variavel
             , valor_inicial )
        VALUES ( 33
             , 3
             , inCodFuncao
             , 1
             , 3
             , 'boRetorno'
             , ''
             );
        INSERT
          INTO administracao.variavel
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , cod_tipo
             , nom_variavel
             , valor_inicial )
        VALUES ( 33
             , 3
             , inCodFuncao
             , 2
             , 1
             , 'inRegistro'
             , ''
             );
        INSERT
          INTO administracao.variavel
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , cod_tipo
             , nom_variavel
             , valor_inicial )
        VALUES ( 33
             , 3
             , inCodFuncao
             , 3
             , 5
             , 'dtVencimento'
             , ''
             );
        INSERT
          INTO administracao.variavel
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , cod_tipo
             , nom_variavel
             , valor_inicial )
        VALUES ( 33
             , 3
             , inCodFuncao
             , 4
             , 1
             , 'inQuantParcelas'
             , ''
             );
        INSERT
          INTO administracao.parametro
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , ordem )
        VALUES ( 33
             , 3
             , inCodFuncao
             , 2
             , 0
             );
        INSERT
          INTO administracao.parametro
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , ordem )
        VALUES ( 33
             , 3
             , inCodFuncao
             , 3
             , 1
             );
        INSERT
          INTO administracao.parametro
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , ordem )
        VALUES ( 33
             , 3
             , inCodFuncao
             , 4
             , 2
             );

    END IF;

    PERFORM 1
       FROM administracao.funcao
      WHERE nom_funcao = 'calculaitbi'
          ;

    IF NOT FOUND THEN
        SELECT COALESCE( MAX(cod_funcao) + 1, 1 )
          INTO inCodFuncao
          FROM administracao.funcao
         WHERE cod_modulo     = 25
           AND cod_biblioteca = 1
             ;

        INSERT
          INTO administracao.funcao
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_tipo_retorno
             , nom_funcao )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 4
             , 'calculaitbi'
             );
        INSERT
          INTO administracao.funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , comentario
             , corpo_pl
             , corpo_ln )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 'Calculo de ITBI'
             , 'FUNCTION calculaitbi(INTEGER,INTEGER) RETURNS NUMERIC as \'
                DECLARE
                INIMOVEL ALIAS FOR $1;
                INEXERCICIO ALIAS FOR $2;
                
                  NUALIQUOTAAVALIADADO NUMERIC;
                  NUALIQUOTAFINANCIADO NUMERIC;
                  NURETORNO NUMERIC;
                  NUVALORFINANCIADO NUMERIC;
                BEGIN
                NURETORNO := ARRECADACAO.FN_BUSCA_VALOR_AVALIADO_ITBI(  INIMOVEL  );
                NUALIQUOTAFINANCIADO := ARRECADACAO.FN_BUSCA_ALIQUOTA_VALOR_FINANCIADO_ITBI(  INIMOVEL  );
                NUALIQUOTAAVALIADADO := ARRECADACAO.FN_BUSCA_ALIQUOTA_VALOR_AVALIADO_ITBI(  INIMOVEL  );
                NUVALORFINANCIADO := ARRECADACAO.FN_BUSCA_VALOR_FINANCIADO_ITBI(  INIMOVEL  );
                NURETORNO := NURETORNO-NUVALORFINANCIADO ;
                NUVALORFINANCIADO := NUVALORFINANCIADO*NUALIQUOTAFINANCIADO ;
                IF   NUVALORFINANCIADO  >  0 THEN
                    NUVALORFINANCIADO := NUVALORFINANCIADO/100;
                END IF;
                NURETORNO := NURETORNO*NUALIQUOTAAVALIADADO ;
                IF   NURETORNO  >  0 THEN
                    NURETORNO := NURETORNO/100;
                END IF;
                NURETORNO := NURETORNO+NUVALORFINANCIADO ;
                RETURN NURETORNO;
                END;
                 \' LANGUAGE \'plpgsql\';'
            , '');
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 1
             , '0'
             , '#nuRetorno <- arrecadacao.fn_busca_valor_avaliado_itbi(  #inImovel  ); '
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 2
             , '0'
             , '#nuAliquotaFinanciado <- arrecadacao.fn_busca_aliquota_valor_financiado_itbi(  #inImovel  ); '
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 3
             , '0'
             , '#nuAliquotaAvaliadado <- arrecadacao.fn_busca_aliquota_valor_avaliado_itbi(  #inImovel  ); '
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 4
             , '0'
             , '#nuValorFinanciado <- arrecadacao.fn_busca_valor_financiado_itbi(  #inImovel  ); '
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 5
             , '0'
             , '#nuRetorno <- #nuRetorno-#nuValorFinanciado ;'
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 6
             , '0'
             , '#nuValorFinanciado <- #nuValorFinanciado*#nuAliquotaFinanciado ;'
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 7
             , '1'
             , 'SE   #nuValorFinanciado  >  0 ENTAO'
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 8
             , '1'
             , '#nuValorFinanciado <- #nuValorFinanciado/100;'
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 9
             , '0'
             , 'FIMSE'
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 10
             , '0'
             , '#nuRetorno <- #nuRetorno*#nuAliquotaAvaliadado ;'
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 11
             , '1'
             , 'SE   #nuRetorno  >  0 ENTAO'
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 12
             , '1'
             , '#nuRetorno <- #nuRetorno/100;'
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 13
             , '0'
             , 'FIMSE'
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 14
             , '0'
             , '#nuRetorno <- #nuRetorno+#nuValorFinanciado ;'
             );
        INSERT
          INTO administracao.corpo_funcao_externa
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_linha
             , nivel
             , linha )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 15
             , '0'
             , 'RETORNA #nuRetorno'
             );
        INSERT
          INTO administracao.variavel
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , cod_tipo
             , nom_variavel
             , valor_inicial )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 1
             , 4
             , 'nuAliquotaAvaliadado'
             , ''
             );
        INSERT
          INTO administracao.variavel
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , cod_tipo
             , nom_variavel
             , valor_inicial )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 2
             , 4
             , 'nuAliquotaFinanciado'
             , ''
             );
        INSERT
          INTO administracao.variavel
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , cod_tipo
             , nom_variavel
             , valor_inicial )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 3
             , 4
             , 'nuRetorno'
             , ''
             );
        INSERT
          INTO administracao.variavel
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , cod_tipo
             , nom_variavel
             , valor_inicial )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 4
             , 4
             , 'nuValorFinanciado'
             , ''
             );
        INSERT
          INTO administracao.variavel
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , cod_tipo
             , nom_variavel
             , valor_inicial )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 5
             , 1
             , 'inImovel'
             , ''
             );
        INSERT
          INTO administracao.variavel
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , cod_tipo
             , nom_variavel
             , valor_inicial )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 6
             , 1
             , 'inExercicio'
             , ''
             );
        INSERT
          INTO administracao.parametro
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , ordem )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 5
             ,0
             );
        INSERT
          INTO administracao.parametro
             ( cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , ordem )
        VALUES ( 25
             , 1
             , inCodFuncao
             , 6
             , 1
             );

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        cadastraRegras();
DROP FUNCTION cadastraRegras();

CREATE OR REPLACE FUNCTION regramodalidadegeraldivida( INTEGER ) RETURNS BOOLEAN AS '
DECLARE
INREGISTRO ALIAS FOR $1;

  BORETORNO BOOLEAN := TRUE;
BEGIN
RETURN BORETORNO;
END;
' LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION regraAcrescimoGeralDivida( INTEGER ) RETURNS BOOLEAN AS '
DECLARE
INREGISTRO ALIAS FOR $1;

  BORETORNO BOOLEAN := TRUE;
BEGIN
RETURN BORETORNO;
END;
' LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION regramodalidadegeraldivida( INTEGER, DATE, INTEGER ) RETURNS BOOLEAN AS '
DECLARE
INREGISTRO ALIAS FOR $1;
DTVENCIMENTO ALIAS FOR $2;
INQUANTPARCELAS ALIAS FOR $3;

  BORETORNO BOOLEAN;
BEGIN
BORETORNO := TRUE;
RETURN BORETORNO;
END;
' LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION calculaitbi( INTEGER, INTEGER ) RETURNS NUMERIC AS '
DECLARE
INIMOVEL ALIAS FOR $1;
INEXERCICIO ALIAS FOR $2;

  NUALIQUOTAAVALIADADO NUMERIC;
  NUALIQUOTAFINANCIADO NUMERIC;
  NURETORNO NUMERIC;
  NUVALORFINANCIADO NUMERIC;
BEGIN
NURETORNO := ARRECADACAO.FN_BUSCA_VALOR_AVALIADO_ITBI(  INIMOVEL  );
NUALIQUOTAFINANCIADO := ARRECADACAO.FN_BUSCA_ALIQUOTA_VALOR_FINANCIADO_ITBI(  INIMOVEL  );
NUALIQUOTAAVALIADADO := ARRECADACAO.FN_BUSCA_ALIQUOTA_VALOR_AVALIADO_ITBI(  INIMOVEL  );
NUVALORFINANCIADO := ARRECADACAO.FN_BUSCA_VALOR_FINANCIADO_ITBI(  INIMOVEL  );
NURETORNO := NURETORNO-NUVALORFINANCIADO ;
NUVALORFINANCIADO := NUVALORFINANCIADO*NUALIQUOTAFINANCIADO ;
IF   NUVALORFINANCIADO  >  0 THEN
    NUVALORFINANCIADO := NUVALORFINANCIADO/100;
END IF;
NURETORNO := NURETORNO*NUALIQUOTAAVALIADADO ;
IF   NURETORNO  >  0 THEN
    NURETORNO := NURETORNO/100;
END IF;
NURETORNO := NURETORNO+NUVALORFINANCIADO ;
RETURN NURETORNO;
END;
' LANGUAGE 'plpgsql';


-------------------------------------------------------------------------------------------
-- ATRIBUINDO FORMULA DE CALCULO AO CREDITO PRINCIPAL DO GRUPO DE CREDITO REFERENTE AO ITBI
-------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stValorITBI     VARCHAR;
    stGrupoITBI     VARCHAR[];

    inCodCredito    INTEGER;
    inCodEspecie    INTEGER;
    inCodGenero     INTEGER;
    inCodNatureza   INTEGER;
    inCodFuncao     INTEGER;
BEGIN

    SELECT valor
      INTO stValorITBI
      FROM administracao.configuracao
     WHERE exercicio  = '2009'
       AND cod_modulo = 25
       AND parametro  = 'grupo_credito_itbi'
       AND valor <> ''
         ;

    IF FOUND THEN

        stGrupoITBI := string_to_array( stValorITBI, '/');
        RAISE NOTICE 'itbi: % / %', stGrupoITBI[1], stGrupoITBI[2];

        SELECT cod_credito
             , cod_especie
             , cod_genero
             , cod_natureza
          INTO inCodCredito
             , inCodEspecie
             , inCodGenero
             , inCodNatureza
          FROM arrecadacao.credito_grupo
         WHERE cod_grupo     = CAST(stGrupoITBI[1] AS INTEGER)
           AND ano_exercicio = stGrupoITBI[2]
           AND ordem         = 1
             ;
        RAISE NOTICE 'credito: % - % - % - %', inCodCredito, inCodEspecie, inCodGenero, inCodNatureza;

        SELECT cod_funcao
          INTO inCodFuncao
          FROM administracao.funcao
         WHERE cod_modulo     = 25
           AND cod_biblioteca = 1
           AND nom_funcao     = 'calculaitbi'
             ;

        INSERT
          INTO arrecadacao.parametro_calculo
             ( cod_credito
             , cod_especie
             , cod_genero
             , cod_natureza
             , ocorrencia_credito
             , cod_funcao
             , cod_modulo
             , cod_biblioteca
             , timestamp
             )
        VALUES
             ( inCodCredito
             , inCodEspecie
             , inCodGenero
             , inCodNatureza
             , ( SELECT COALESCE( MAX(ocorrencia_credito) + 1, 1 )
                   FROM arrecadacao.parametro_calculo
                  WHERE cod_credito  = inCodCredito
                    AND cod_especie  = inCodEspecie
                    AND cod_genero   = inCodGenero
                    AND cod_natureza = inCodNatureza )
             , inCodFuncao
             , 25
             , 1
             , now()::timestamp(3)
             );

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


--------------------------------------------------------------------
-- ADICIONANDO COLUNA origem_reducao NA PK DE divida.parcela_reducao
--------------------------------------------------------------------

ALTER TABLE divida.parcela_reducao DROP CONSTRAINT pk_parcela_reducao;
ALTER TABLE divida.parcela_reducao ADD CONSTRAINT pk_parcela_reducao PRIMARY KEY (num_parcelamento, num_parcela, origem_reducao);


----------------------------------------------------------
-- ADICIONADO DROP P/ FUNCAO lista_inscricao_por_documento
----------------------------------------------------------

DROP FUNCTION lista_inscricao_por_documento(integer,integer,integer);


-----------------------------------------------------
-- INSERE REGISTROS REMIDOS EM divida.divida_remissao
-----------------------------------------------------

    INSERT
      INTO divida.divida_remissao
         ( cod_inscricao
         , exercicio
         , cod_norma
         , numcgm
         , dt_remissao
         , observacao
         )
    SELECT divida_ativa.cod_inscricao
         , divida_ativa.exercicio
         , 41
         , 97
         , TO_DATE('2008-11-19','YYYY-MM-DD')
         , ''
      FROM (
                 SELECT lancamento.cod_calculo
                      , lancamento.cod_lancamento
                      , lancamento.valor
                      , min(parcela_origem.num_parcelamento) AS num_parcelamento
                   FROM ( 
                             SELECT lancamento.cod_calculo
                                  , lancamento.cod_lancamento
                                  , lancamento.valor
                                  , parcela.cod_parcela
                               FROM (
                                          SELECT MIN(cod_calculo) AS cod_calculo
                                               , lancamento.cod_lancamento
                                               , lancamento.valor
                                            FROM (
                                                   SELECT lancamento.cod_lancamento
                                                        , lancamento.valor
                                                     FROM arrecadacao.lancamento
                                                          --tmp_remidos_nao_divida as lancamento
                                                    WHERE lancamento.situacao = 'R'
                                                 ) AS lancamento
                                      INNER JOIN arrecadacao.lancamento_calculo
                                              ON lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
                                        GROUP BY lancamento.cod_lancamento
                                               , lancamento.valor
                                   ) AS lancamento
                          INNER JOIN arrecadacao.parcela
                                  ON lancamento.cod_lancamento = parcela.cod_lancamento
                        ) AS lancamento
             INNER JOIN divida.parcela_origem
                     ON lancamento.cod_parcela = parcela_origem.cod_parcela
               GROUP BY lancamento.cod_calculo
                      , lancamento.cod_lancamento 
                      , lancamento.valor 
           ) AS lancamento
INNER JOIN divida.divida_parcelamento
        ON lancamento.num_parcelamento = divida_parcelamento.num_parcelamento
INNER JOIN divida.divida_ativa
        ON divida_parcelamento.cod_inscricao = divida_ativa.cod_inscricao
       AND divida_parcelamento.exercicio = divida_ativa.exercicio
 LEFT JOIN divida.divida_remissao
        ON divida_ativa.cod_inscricao = divida_remissao.cod_inscricao
       AND divida_ativa.exercicio = divida_remissao.exercicio
     WHERE divida_remissao.cod_inscricao IS NULL
         ;


---------------------------------------------------------------------------
-- INSERE REGISTROS DE DOCUMENTOS P/ INSCRICOES REMIDAS EM divida.documento
---------------------------------------------------------------------------

SET search_path = divida;

    INSERT
      INTO documento(num_parcelamento,cod_tipo_documento,cod_documento)
    SELECT parcelamento_remissao.num_parcelamento::int
         , configuracao.cod_tipo_documento::int
         , configuracao.cod_documento::int
      FROM (
              SELECT MIN(divida_parcelamento.num_parcelamento) AS num_parcelamento
                   , divida_ativa.cod_inscricao
                   , divida_ativa.exercicio
                FROM divida_remissao
          INNER JOIN divida_ativa
                  ON divida_remissao.cod_inscricao = divida_ativa.cod_inscricao
                 AND divida_remissao.exercicio = divida_ativa.exercicio
          INNER JOIN divida_parcelamento
                  ON divida_parcelamento.cod_inscricao = divida_ativa.cod_inscricao
                 AND divida_parcelamento.exercicio = divida_ativa.exercicio
           LEFT JOIN documento
                  ON documento.num_parcelamento = divida_parcelamento.num_parcelamento
                 AND documento.cod_tipo_documento = 7
                 AND documento.cod_documento = (
                                                 SELECT cod_documento
                                                   FROM administracao.modelo_documento
                                                  WHERE cod_tipo_documento = 7
                                               )
               WHERE (1=1)
                 AND documento.num_parcelamento IS NULL
            GROUP BY divida_ativa.cod_inscricao
                   , divida_ativa.exercicio
            ORDER BY divida_ativa.exercicio
                   , divida_ativa.cod_inscricao 
           ) AS parcelamento_remissao
CROSS JOIN (
             SELECT cod_documento
                  , cod_tipo_documento
               FROM administracao.modelo_documento
              WHERE cod_tipo_documento = 7
           ) AS configuracao
           ;

RESET search_path ;


----------------
-- Ticket #15839
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio = '2009'
        AND parametro = 'cnpj'
        AND valor     = '13805528000180'
          ;

    IF NOT FOUND THEN

        ALTER TABLE monetario.credito_norma ADD   COLUMN dt_inicio_vigencia DATE;
        UPDATE      monetario.credito_norma SET          dt_inicio_vigencia = timestamp::DATE;
        ALTER TABLE monetario.credito_norma ALTER COLUMN dt_inicio_vigencia SET NOT NULL;

    END IF;

END;
$$ LANGUAGE 'plpgsql';
SELECT        manutencao();
DROP FUNCTION manutencao();

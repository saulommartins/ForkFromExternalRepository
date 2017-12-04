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
*
* funcao imobiliario.retornaAtributoPorCadastro(INTEGER, INTEGER, INTEGER, VARCHAR, VARCHAR, INTEGER)
* retorno: VARCHAR contendo o valor correspondente ao atributo passado
*
* Fabio Bertoldi - 20130328
*
*/

CREATE OR REPLACE FUNCTION imobiliario.retornaAtributoPorCadastro( inCodModulo       INTEGER
                                                                 , inCodCadastro     INTEGER
                                                                 , inCodAtributo     INTEGER
                                                                 , stMapeamento      VARCHAR
                                                                 , stColuna          VARCHAR
                                                                 , inRegistro        INTEGER
                                                                 , inConstrucao      INTEGER
                                                                 ) RETURNS           VARCHAR AS $$
DECLARE
    inCodTipo       INTEGER;
    stSQL           VARCHAR;
    reRecord        RECORD;
    crCursor        REFCURSOR;
    stRetorno       VARCHAR;
BEGIN
    SELECT cod_tipo
      INTO inCodTipo
      FROM administracao.atributo_dinamico
     WHERE cod_modulo   = inCodModulo
       AND cod_cadastro = inCodCadastro
       AND cod_atributo = inCodAtributo
         ;
    IF inCodTipo = 3 THEN
        stSQL := '
                               SELECT atributo_valor_padrao.valor_padrao
                                 FROM administracao.atributo_valor_padrao
                                 JOIN (
                                        SELECT '|| stMapeamento ||'.cod_modulo
                                             , '|| stMapeamento ||'.cod_cadastro
                                             , '|| stMapeamento ||'.cod_atributo
                                             , '|| stMapeamento ||'.'|| stColuna ||' AS coluna
                                             , '|| stMapeamento ||'.valor 
                                          FROM '|| stMapeamento ||'
                                          JOIN (
                                                   SELECT cod_modulo
                                                        , cod_cadastro
                                                        , cod_atributo
                                                        , '|| stColuna ||' AS coluna
                                                        , MAX(timestamp) AS timestamp
                                                     FROM '|| stMapeamento ||'
                                                 GROUP BY cod_modulo
                                                        , cod_cadastro
                                                        , cod_atributo
                                                        , '|| stColuna ||'
                                               ) AS max_timestamp
                                            ON max_timestamp.cod_modulo       = '|| stMapeamento ||'.cod_modulo
                                           AND max_timestamp.cod_cadastro     = '|| stMapeamento ||'.cod_cadastro
                                           AND max_timestamp.cod_atributo     = '|| stMapeamento ||'.cod_atributo
                                           AND max_timestamp.coluna           = '|| stMapeamento ||'.'|| stColuna ||'
                                           AND max_timestamp.timestamp        = '|| stMapeamento ||'.timestamp
                                      ) AS tabela_atributo
                                   ON atributo_valor_padrao.cod_modulo   = tabela_atributo.cod_modulo
                                  AND atributo_valor_padrao.cod_cadastro = tabela_atributo.cod_cadastro
                                  AND atributo_valor_padrao.cod_atributo = tabela_atributo.cod_atributo
                                  AND atributo_valor_padrao.cod_valor    = tabela_atributo.valor
                                WHERE tabela_atributo.cod_modulo     = '|| inCodModulo   ||'
                                  AND tabela_atributo.cod_cadastro   = '|| inCodCadastro ||'
                                  AND tabela_atributo.cod_atributo   = '|| inCodAtributo ||'
                                  AND tabela_atributo.coluna         = '|| inRegistro    ||'
                                    ;
                         ';
    ELSE
        stSQL := '
                               SELECT '|| stMapeamento ||'.valor AS valor_padrao
                                 FROM '|| stMapeamento ||'
                                 JOIN (
                                          SELECT cod_modulo
                                               , cod_cadastro
                                               , cod_atributo
                                               , '|| stColuna ||' AS coluna
                                               , MAX(timestamp) AS timestamp
                                            FROM '|| stMapeamento ||'
                                        GROUP BY cod_modulo
                                               , cod_cadastro
                                               , cod_atributo
                                               , '|| stColuna ||'
                                      ) AS max_timestamp
                                   ON max_timestamp.cod_modulo       = '|| stMapeamento ||'.cod_modulo
                                  AND max_timestamp.cod_cadastro     = '|| stMapeamento ||'.cod_cadastro
                                  AND max_timestamp.cod_atributo     = '|| stMapeamento ||'.cod_atributo
                                  AND max_timestamp.coluna           = '|| stMapeamento ||'.'|| stColuna ||'
                                  AND max_timestamp.timestamp        = '|| stMapeamento ||'.timestamp
                                WHERE '|| stMapeamento ||'.cod_modulo       = '|| inCodModulo   ||'
                                  AND '|| stMapeamento ||'.cod_cadastro     = '|| inCodCadastro ||'
                                  AND '|| stMapeamento ||'.cod_atributo     = '|| inCodAtributo ||'
                                  AND '|| stMapeamento ||'.'|| stColuna ||' = '|| inRegistro    ||'
                                    ;
                         ';
    END IF;

    OPEN crCursor FOR EXECUTE stSQL;
    LOOP
        FETCH crCursor INTO reRecord;
        EXIT WHEN NOT FOUND;

        stRetorno := reRecord.valor_padrao;

    END LOOP;
    CLOSE crCursor;

    RETURN stRetorno;
END;
$$ LANGUAGE 'plpgsql';

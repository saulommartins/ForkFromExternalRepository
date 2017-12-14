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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: CalculoEconomico.plsql 30334 2008-06-12 20:44:48Z andrem $
*
* Função Abstrata de Calculo Economico
*

* Casos d uso: uc-05.03.05
*/

/*
$Log$

*/

CREATE OR REPLACE FUNCTION formata_string(VARCHAR) RETURNS VARCHAR AS $$
DECLARE
  stTemp  VARCHAR:=$1;
BEGIN
  stTemp := upper(trim(stTemp));
  stTemp := replace(stTemp, '.', '');
  stTemp := replace(stTemp, ',', '');
  stTemp := replace(stTemp, '-', '');
  stTemp := replace(stTemp, '/', '');
  stTemp := replace(stTemp, ' ', '');

  RETURN stTemp ;
END;
$$ LANGUAGE plpgsql IMMUTABLE;





DROP FUNCTION getErro(varchar);
CREATE OR REPLACE FUNCTION getErro(stErrosLocal VARCHAR) RETURNS VARCHAR AS $$
DECLARE
  stErroBuffer VARCHAR;
  stErrosTemp  VARCHAR:='';
BEGIN
    stErrosTemp := recuperarBufferTexto('sterro');

    IF stErrosTemp IS NULL THEN
       stErrosTemp := '';
       IF stErrosLocal != '' THEN
          stErroBuffer := criarBufferTexto('sterro', stErrosLocal);
          stErrosTemp := recuperarBufferTexto('sterro');
       END IF;
    END IF;

    RETURN stErrosTemp;
END;
$$ LANGUAGE plpgsql;





CREATE OR REPLACE FUNCTION executagcnumericotributario(stFormula VARCHAR) RETURNS CHARACTER VARYING as $$
DECLARE
    inCodModulo     INTEGER;
    inCodBiblioteca INTEGER;
    inCodFuncao     INTEGER;
    arFormula       VARCHAR[];
    stFuncao        VARCHAR;
    stErrosBuffer   VARCHAR;
    stErrosTemp     VARCHAR:='';
    retornoTemp     CHARACTER VARYING;
BEGIN

    stErrosTemp := getErro('');
    IF stErrosTemp != '' THEN
        retornoTemp := 0;
    ELSE
        arFormula       := string_to_array(stFormula,'.');
        inCodModulo     := arFormula[1];
        inCodBiblioteca := arFormula[2];
        inCodFuncao     := arFormula[3];

        SELECT nom_funcao
          INTO stFuncao
          FROM administracao.funcao
         WHERE cod_modulo       = inCodModulo
           AND cod_biblioteca   = inCodBiblioteca
           AND cod_funcao       = inCodFuncao;


        retornoTemp := trata_erros(stFuncao, 'NUMERIC');
        
        IF retornoTemp IS NULL THEN
           stErrosTemp := stFuncao;
        END IF;

        stErrosTemp := getErro(stErrosTemp);

        IF stErrosTemp != '' THEN
            retornoTemp := 0;
        END IF;
    END IF;
  RETURN retornoTemp;
END;
$$ LANGUAGE plpgsql;





--DROP FUNCTION trata_erros(VARCHAR, varchar, varchar);
CREATE OR REPLACE FUNCTION trata_erros(funcao VARCHAR, tpRetorno VARCHAR ) RETURNS CHARACTER VARYING as $$
DECLARE
    stSituacao      VARCHAR;
    stSql           VARCHAR;
    crCursor        REFCURSOR;
    inNParams       INTEGER;
    i               INTEGER;
    stTipo          VARCHAR;
    stTmp           VARCHAR;
    stErrosTemp     VARCHAR := '';
    retornoTemp     VARCHAR;
BEGIN
    stErrosTemp := getErro('');
    IF stErrosTemp != '' THEN
        retornoTemp := 0;
    ELSE
        stSql := 'SELECT ' ||funcao||'()';
        
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO stSituacao;
        CLOSE crCursor;
        stErrosTemp := getErro('');
        IF stSituacao IS NOT NULL THEN
            IF tpRetorno = 'DATE' THEN
                retornoTemp := stSituacao::date;
            END IF;
            IF tpRetorno = 'NUMERIC' THEN
                stSituacao := to_number(stSituacao,'9999999999999.9999');
                retornoTemp := stSituacao::numeric(14,2);
            END IF;
            IF tpRetorno = 'INTEGER' THEN
                retornoTemp := stSituacao::INTEGER;
            ELSE
                retornoTemp := stSituacao;
            END IF;
        ELSE
            stErrosTemp := funcao ||'#';
            IF stErrosTemp IS NULL THEN
                stErrosTemp := funcao;
            END IF;
            stErrosTemp := getErro(stErrosTemp);
            retornoTemp := 0;
        END IF;
    END IF;
    RETURN retornoTemp;
END;
$$ LANGUAGE plpgsql;





CREATE OR REPLACE FUNCTION trata_erros(funcao VARCHAR, tpRetorno VARCHAR, arParams character varying[] ) RETURNS CHARACTER VARYING as $$
DECLARE
    stSituacao  VARCHAR;
    stSql       VARCHAR;
    stParams    VARCHAR;
    crCursor    REFCURSOR;
    inNParams   INTEGER;
    i           INTEGER;
    stTipo      VARCHAR;
    stTmp       VARCHAR;
    stErrosTemp VARCHAR := '';
    retornoTemp VARCHAR;
BEGIN
    stErrosTemp := getErro('');
    IF stErrosTemp != '' THEN
        retornoTemp   := NULL;
    ELSE
        inNParams = array_upper(arParams,1);
        FOR i IN 1..inNParams LOOP
            stTmp := arParams[i];
            IF stTmp =  '''' THEN
                stTmp :=  ''||''||'';
            --ELSE
                -- stTmp := ''''||stTmp||'''';
            END IF;
            i := i + 1;
            IF arParams[i] = 'date' THEN
                stTmp := ''''||stTmp|| '''' || '::date'||',';
            ELSE
                IF arParams[i] = 'VARCHAR' THEN
                    stTmp := ''''||stTmp|| '''' || '::VARCHAR'||',';
                ELSE
                 stTmp := stTmp || '::' || arParams[i]||',';
                END IF;
            END IF;
            IF stParams IS NULL THEN
                stParams := stTmp; --|| ',';
            ELSE
                stParams := stParams || stTmp; --|| ',';
            END IF;
        END LOOP;

        stParams := substr(stParams, 1, length(stParams)-1);
        stSql    := 'SELECT ' ||funcao||'('||stParams||')';
        
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO stSituacao;
        CLOSE crCursor;

        IF stSituacao IS NOT NULL THEN
            IF tpRetorno = 'DATE' THEN
                retornoTemp := stSituacao::date;
            END IF;
            IF tpRetorno = 'NUMERIC' THEN
                retornoTemp = stSituacao::numeric(14,2);
            END IF;
            IF tpRetorno = 'INTEGER' THEN
                retornoTemp = stSituacao::INTEGER;
            ELSE
                retornoTemp := stSituacao;
            END IF;
        ELSE
            stErrosTemp := funcao ||'#'||stParams;
            IF stErrosTemp IS NULL THEN
                stErrosTemp := funcao;
            END IF;
            stErrosTemp := getErro(stErrosTemp);
            retornoTemp := 0;
        END IF;
    END IF;
    return retornoTemp;
END;
$$ LANGUAGE plpgsql;


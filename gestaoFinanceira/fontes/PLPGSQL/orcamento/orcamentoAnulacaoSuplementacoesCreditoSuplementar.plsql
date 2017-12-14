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
CREATE OR REPLACE FUNCTION orcamentoanulacaosuplementacoescreditosuplementar (character varying, numeric, character varying, integer, character varying, integer, character varying) RETURNS INTEGER AS $$
DECLARE
    EXERCICIO       ALIAS FOR $1;
    VALOR           ALIAS FOR $2;
    COMPLEMENTO     ALIAS FOR $3;
    CODLOTE         ALIAS FOR $4;
    TIPOLOTE        ALIAS FOR $5;
    CODENTIDADE     ALIAS FOR $6;
    TIPOANULACAO    ALIAS FOR $7;

    ANUCREDSUP VARCHAR := '';
    SEQUENCIA INTEGER;
BEGIN
    ANUCREDSUP := '' || TIPOANULACAO || '';
    IF EXERCICIO::integer > 2013 THEN
        IF ANUCREDSUP = 'Reducao' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '522190109' , '622110000' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '622110000' , '522120100' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '522139900' , '522130300' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'Operacao de Credito' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '622110000' , '522120100'   , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '522139900' , '52213040000' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'Auxilios' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '622110000' , '522120100'   , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '522139900' , '52213020000' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'Excesso' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '622110000' , '522120100'   , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '522139900' , '52213020000' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'Superavit' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '622110000' , '522120100'   , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '522139900' , '52213010000' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'AnulacaoExternaReduzida' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '522190109' , '622110000' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'AnulacaoExternaSuplementada' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '622110000' , '522130300' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
    ELSIF EXERCICIO::integer = 2013 THEN
        IF ANUCREDSUP = 'Reducao' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '522190109' , '622110000' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '622110000' , '522120100' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '522139900' , '522130104' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'Operacao de Credito' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '622110000' , '522120100'   , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '522139900' , '52213010300' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'Auxilios' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '622110000' , '522120100'   , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '522139900' , '52213010203' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'Excesso' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '622110000' , '522120100'   , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '522139900' , '52213010201' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'Superavit' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '622110000' , '522120100'   , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '522139900' , '52213010100' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'AnulacaoExternaReduzida' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '522190109' , '622110000' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'AnulacaoExternaSuplementada' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '622110000' , '522130104' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
    ELSE
        IF ANUCREDSUP = 'Reducao' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '292110000000000' , '192120100010000' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '192190209000000' , '292110000000000' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'Excesso' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '292110000000000' , '192120100020000' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '291120000000000' , '191110000000000' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'Operacao de Credito' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '292110000000000' , '192120100030000' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'Superavit' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '292110000000000' , '192120200010000' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'Doacoes' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '292110000000000' , '192120200020000' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDSUP = 'Auxilios' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '292110000000000' , '192120200030000' , 911 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
    END IF;

    RETURN SEQUENCIA;
END;
$$ LANGUAGE 'plpgsql';

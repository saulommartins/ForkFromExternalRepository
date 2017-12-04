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
CREATE OR REPLACE FUNCTION orcamentoanulacaosuplementacoescreditoespecial (character varying, numeric, character varying, integer, character varying, integer, character varying) RETURNS INTEGER AS $$
DECLARE
    EXERCICIO ALIAS FOR $1;
    VALOR ALIAS FOR $2;
    COMPLEMENTO ALIAS FOR $3;
    CODLOTE ALIAS FOR $4;
    TIPOLOTE ALIAS FOR $5;
    CODENTIDADE ALIAS FOR $6;
    TIPOANULACAO ALIAS FOR $7;

    SEQUENCIA INTEGER;
    ANUCREDESP VARCHAR := '';
BEGIN
    ANUCREDESP := '' || TIPOANULACAO || '';
    IF EXERCICIO::integer > 2013 THEN
        IF ANUCREDESP = 'Reducao' THEN
            Sequencia := FazerLancamento(  '522190109' , '622110000' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '622110000' , '522120201' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '522139900' , '522130300' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF ANUCREDESP = 'Operacao de Credito' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '622110000' , '52212020100' , 912 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '522139900' , '52213040000' , 912 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDESP = 'Auxilios' THEN
            Sequencia := FazerLancamento(  '622110000' , '52212020100' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '522139900' , '52213020203' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF ANUCREDESP = 'Excesso' THEN
            Sequencia := FazerLancamento(  '622110000' , '52212020100' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '522139900' , '52213020000' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF ANUCREDESP = 'Superavit' THEN
            Sequencia := FazerLancamento(  '622110000' , '52212020100' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '522139900' , '52213010000' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF ANUCREDESP = 'Especial Reaberto' THEN
            Sequencia := FazerLancamento(  '622110000' , '522120202' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
    ELSIF EXERCICIO::integer = 2013 THEN
        IF ANUCREDESP = 'Reducao' THEN
            Sequencia := FazerLancamento(  '522190109' , '622110000' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '622110000' , '522120201' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '522139900' , '522130204' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF ANUCREDESP = 'Operacao de Credito' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '622110000' , '52212020100' , 912 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '522139900' , '52213020300' , 912 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDESP = 'Auxilios' THEN
            Sequencia := FazerLancamento(  '622110000' , '52212020100' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '522139900' , '52213020203' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF ANUCREDESP = 'Excesso' THEN
            Sequencia := FazerLancamento(  '622110000' , '52212020100' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '522139900' , '52213020201' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF ANUCREDESP = 'Superavit' THEN
            Sequencia := FazerLancamento(  '622110000' , '52212020100' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
            Sequencia := FazerLancamento(  '522139900' , '52213020100' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF ANUCREDESP = 'Especial Reaberto' THEN
            Sequencia := FazerLancamento(  '622110000' , '522120202' , 912 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
    ELSE
        IF ANUCREDESP =  'Reducao' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '292110000000000' , '192130100020000' , 912 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '192190209000000' , '292110000000000' , 912 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDESP =  'Excesso' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '292110000000000' , '192130100030000' , 912 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            SEQUENCIA := FAZERLANCAMENTO(  '291120000000000' , '191110000000000' , 912 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDESP =  'Operacao de Credito' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '292110000000000' , '192130100040000' , 912 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDESP =  'Superavit' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '292110000000000' , '192130100010000' , 912 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDESP =  'Doacoes' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '292110000000000' , '192130100060000' , 912 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDESP =  'Auxilios' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '292110000000000' , '192130100050000' , 912 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF ANUCREDESP =  'Especial Reaberto' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '292110000000000' , '192130200000000' , 912 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
    END IF;

    RETURN SEQUENCIA;
END;
$$ LANGUAGE 'plpgsql';

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
CREATE OR REPLACE FUNCTION orcamentosuplementacoescreditoespecial (character varying, numeric, character varying, integer, character varying, integer, character varying) RETURNS INTEGER AS $$
DECLARE
    stExercicio           ALIAS FOR $1;
    nuValor               ALIAS FOR $2;
    stComplemento         ALIAS FOR $3;
    inCodLote             ALIAS FOR $4;
    stTipoLote            ALIAS FOR $5;
    inCodEntidade         ALIAS FOR $6;
    stCredSuplementar     ALIAS FOR $7;

    inUF                  INTEGER;
    inSequencia           INTEGER;
    stTipoCredSuplementar VARCHAR := '';
BEGIN
   
    SELECT INTO inUF
                configuracao.valor
     FROM administracao.configuracao
    WHERE configuracao.cod_modulo = 2
      AND configuracao.parametro = 'cod_uf'
      AND configuracao.exercicio = stExercicio;
    
    stTipoCredSuplementar := stCredSuplementar;
    
    IF stExercicio::integer > 2013 THEN
        IF stTipoCredSuplementar = 'Reducao' THEN
            inSequencia := FazerLancamento(  '622110000' , '522190109' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            inSequencia := FazerLancamento(  '522120201' , '622110000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            
            IF inUF <> 2 THEN
                inSequencia := FazerLancamento(  '522130300' , '522139900' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            ELSE
                inSequencia := FazerLancamento(  '522130204' , '522139900' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            END IF;
            
        END IF;
        IF stTipoCredSuplementar = 'Operacao de Credito' THEN
            inSequencia := FAZERLANCAMENTO(  '52212020100' , '622110000' , 908 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            
            IF inUF <> 2 THEN
                inSequencia := FAZERLANCAMENTO(  '52213040000' , '522139900' , 908 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            ELSE
                inSequencia := FAZERLANCAMENTO(  '52213020300' , '522139900' , 908 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            END IF;
            
        END IF;
        IF stTipoCredSuplementar = 'Auxilios' THEN
            inSequencia := FazerLancamento(  '52212020100' , '622110000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            inSequencia := FazerLancamento(  '52213020203' , '522139900' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
        END IF;
        IF stTipoCredSuplementar = 'Excesso' THEN
            inSequencia := FazerLancamento(  '52212020100' , '622110000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            
            IF inUF <> 2 THEN
                inSequencia := FazerLancamento(  '52213020000' , '522139900' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            ELSE
                inSequencia := FazerLancamento(  '52213020201' , '522139900' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            END IF;
            
        END IF;
        IF stTipoCredSuplementar = 'Superavit' THEN
            inSequencia := FazerLancamento(  '52212020100' , '622110000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            
            IF inUF <> 2 THEN
                inSequencia := FazerLancamento(  '52213010000' , '522139900' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            ELSE
                inSequencia := FazerLancamento(  '52213020100' , '522139900' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            END IF;
            
        END IF;
        IF stTipoCredSuplementar = 'Especial Reaberto' THEN
            inSequencia := FazerLancamento(  '522120202' , '622110000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
        END IF;
    ELSIF stExercicio::integer = 2013 THEN
        IF stTipoCredSuplementar = 'Reducao' THEN
            inSequencia := FazerLancamento(  '622110000' , '522190109' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            inSequencia := FazerLancamento(  '522120201' , '622110000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            inSequencia := FazerLancamento(  '522130204' , '522139900' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
        END IF;
        IF stTipoCredSuplementar = 'Operacao de Credito' THEN
            inSequencia := FAZERLANCAMENTO(  '52212020100' , '622110000' , 908 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            inSequencia := FAZERLANCAMENTO(  '52213020300' , '522139900' , 908 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
        END IF;
        IF stTipoCredSuplementar = 'Auxilios' THEN
            inSequencia := FazerLancamento(  '52212020100' , '622110000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            inSequencia := FazerLancamento(  '52213020203' , '522139900' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
        END IF;
        IF stTipoCredSuplementar = 'Excesso' THEN
            inSequencia := FazerLancamento(  '52212020100' , '622110000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            inSequencia := FazerLancamento(  '52213020201' , '522139900' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
        END IF;
        IF stTipoCredSuplementar = 'Superavit' THEN
            inSequencia := FazerLancamento(  '52212020100' , '622110000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            inSequencia := FazerLancamento(  '52213020100' , '522139900' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
        END IF;
        IF stTipoCredSuplementar = 'Especial Reaberto' THEN
            inSequencia := FazerLancamento(  '522120202' , '622110000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
        END IF;
    ELSE
        IF stTipoCredSuplementar = 'Reducao' THEN
            inSequencia := FazerLancamento(  '192130100020000' , '292110000000000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            inSequencia := FazerLancamento(  '292110000000000' , '192190209000000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
        END IF;
        IF stTipoCredSuplementar = 'Excesso' THEN
            inSequencia := FazerLancamento(  '192130100030000' , '292110000000000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
            inSequencia := FazerLancamento(  '191110000000000' , '291120000000000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
        END IF;
        IF stTipoCredSuplementar = 'Operacao de Credito' THEN
            inSequencia := FazerLancamento(  '192130100040000' , '292110000000000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
        END IF;
        IF stTipoCredSuplementar = 'Superavit' THEN
            inSequencia := FazerLancamento(  '192130101010000' , '292110000000000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
        END IF;
        IF stTipoCredSuplementar = 'Doacoes' THEN
            inSequencia := FazerLancamento(  '192130100060000' , '292110000000000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
        END IF;
        IF stTipoCredSuplementar = 'Auxilios' THEN
            inSequencia := FazerLancamento(  '192130100050000' , '292110000000000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
        END IF;
        IF stTipoCredSuplementar = 'Especial Reaberto' THEN
            inSequencia := FazerLancamento(  '192130200000000' , '292110000000000' , 909 , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade  );
        END IF;
    END IF;

    RETURN inSequencia;
END;

$$ LANGUAGE 'plpgsql';
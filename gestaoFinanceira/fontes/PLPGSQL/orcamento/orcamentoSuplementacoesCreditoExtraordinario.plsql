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
CREATE OR REPLACE FUNCTION orcamentosuplementacoescreditoextraordinario (character varying, numeric, character varying, integer, character varying, integer, character varying) RETURNS INTEGER AS $$
DECLARE
    EXERCICIO ALIAS FOR $1;
    VALOR ALIAS FOR $2;
    COMPLEMENTO ALIAS FOR $3;
    CODLOTE ALIAS FOR $4;
    TIPOLOTE ALIAS FOR $5;
    CODENTIDADE ALIAS FOR $6;
    CREDEXTRAORDINARIO ALIAS FOR $7;

    SEQUENCIA INTEGER;
    TIPOCREDSUPLEMENTAR VARCHAR := '';
BEGIN
    TIPOCREDSUPLEMENTAR := '' || CREDEXTRAORDINARIO || '';

    IF EXERCICIO::integer > 2012 THEN
        IF TIPOCREDSUPLEMENTAR = 'Extraordinario' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '522120301' , '622110000' , 910 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF TIPOCREDSUPLEMENTAR = 'Reabertura' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '52212030200' , '622110000' , 910 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
    ELSE
        IF TIPOCREDSUPLEMENTAR = 'Extraordinario' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '192140100000000' , '292110000000000' , 910 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF TIPOCREDSUPLEMENTAR = 'Reabertura' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '192140200000000' , '292110000000000' , 910 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
    END IF;

    RETURN SEQUENCIA;
END;
$$ LANGUAGE 'plpgsql'

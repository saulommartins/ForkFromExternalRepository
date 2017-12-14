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
CREATE OR REPLACE FUNCTION empenhoAnulacaoLiquidacao(character varying, numeric, character varying, integer, character varying, integer, integer, character varying, integer, integer) RETURNS INTEGER AS $$

DECLARE

    EXERCICIO               ALIAS FOR $1;
    VALOR                   ALIAS FOR $2;
    COMPLEMENTO             ALIAS FOR $3;
    CODLOTE                 ALIAS FOR $4;
    TIPOLOTE                ALIAS FOR $5;
    CODENTIDADE             ALIAS FOR $6;
    CODNOTA                 ALIAS FOR $7;
    CLASDESPESA             ALIAS FOR $8;
    CODHISTORICOPATRIMON    ALIAS FOR $9;
    NUMORGAO                ALIAS FOR $10;

    SEQUENCIA               INTEGER;

BEGIN

    SEQUENCIA := FAZERLANCAMENTO(  '292130201000000' , '292130100000000' , 905 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    SEQUENCIA := FAZERLANCAMENTO(  '292410102000000' , '292410101000000' , 905 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    SEQUENCIA := FAZERLANCAMENTO(  '292410402000000' , '292410401000000' , 905 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );   
    SEQUENCIA := EMPENHOANULACAOLIQUIDACAOMODALIDADESLICITACAO(  EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , CODNOTA  );
    IF EXERCICIO::integer > 2008 THEN
       SEQUENCIA := EMPENHOANULACAOLIQUIDACAOPATRIMONIAL(  EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , CODNOTA , CODHISTORICOPATRIMON  );
    ELSE
        IF CODESTRUTURALCONTADEBITO  !=  ''  AND  CODESTRUTURALCONTACREDITO  !=  '' THEN
           SEQUENCIA := FAZERLANCAMENTO(  CODESTRUTURALCONTACREDITO , CODESTRUTURALCONTADEBITO, CODHISTORICOPATRIMON , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE ,CODENTIDADE  );
        END IF;   
    END IF;


    SEQUENCIA := EMPENHOANULACAOLIQUIDACAOFINANCEIROTIPOCREDOR(  EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , CODNOTA , CLASDESPESA , NUMORGAO  );
    RETURN SEQUENCIA;
END;

$$ LANGUAGE 'plpgsql';

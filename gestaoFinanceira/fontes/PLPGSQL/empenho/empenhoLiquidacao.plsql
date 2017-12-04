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
CREATE OR REPLACE FUNCTION  empenhoLiquidacao(character varying, numeric, character varying, integer, character varying, integer, integer, character varying, integer, integer, character varying, character varying ) RETURNS INTEGER AS $$

 DECLARE
 EXERCICIO ALIAS FOR $1;
 VALOR ALIAS FOR $2;
 COMPLEMENTO ALIAS FOR $3;
 CODLOTE ALIAS FOR $4;
 TIPOLOTE ALIAS FOR $5;
 CODENTIDADE ALIAS FOR $6;
 CODNOTA ALIAS FOR $7;
 CLASDESPESA ALIAS FOR $8;
 CODHISTORICOPATRIMON ALIAS FOR $9;
 NUMORGAO ALIAS FOR $10;
 CODESTRUTURALCONTADEBITO ALIAS FOR $11;
 CODESTRUTURALCONTACREDITO ALIAS FOR $12;

 SEQUENCIA INTEGER;
BEGIN


 SEQUENCIA := FAZERLANCAMENTO(  '292130100000000' , '292130201000000' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
 SEQUENCIA := FAZERLANCAMENTO(  '292410101000000' , '292410102000000' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
 SEQUENCIA := FAZERLANCAMENTO(  '292410401000000' , '292410402000000' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
 SEQUENCIA := empenholiquidacaomodalidadeslicitacao(EXERCICIO, VALOR, COMPLEMENTO, CODLOTE, TIPOLOTE, CODENTIDADE, CODNOTA);

 IF     CODESTRUTURALCONTADEBITO  !=  ''  AND  CODESTRUTURALCONTACREDITO  !=  '' THEN
 SEQUENCIA := FAZERLANCAMENTO(  CODESTRUTURALCONTADEBITO , CODESTRUTURALCONTACREDITO , CODHISTORICOPATRIMON , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
 END IF;


 SEQUENCIA := empenholiquidacaofinanceirotipocredor(  EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE , CODNOTA , CLASDESPESA , NUMORGAO  );

 RETURN SEQUENCIA;
 END;

$$ LANGUAGE 'plpgsql';

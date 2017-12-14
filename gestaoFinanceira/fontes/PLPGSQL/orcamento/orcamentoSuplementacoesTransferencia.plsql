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
* $Id: orcamentoSuplementacoesTransferencia.plsql 62487 2015-05-14 12:23:10Z franver $
*/

CREATE OR REPLACE FUNCTION orcamentosuplementacoestransferencia(character varying, numeric, character varying, integer, character varying, integer, integer)  RETURNS INTEGER AS $$
DECLARE
    EXERCICIO       ALIAS FOR $1;
    VALOR           ALIAS FOR $2;
    COMPLEMENTO     ALIAS FOR $3;
    CODLOTE         ALIAS FOR $4;
    TIPOLOTE        ALIAS FOR $5;
    CODENTIDADE     ALIAS FOR $6;
    CODHISTORICO    ALIAS FOR $7;

    SEQUENCIA INTEGER;
BEGIN
    IF EXERCICIO::integer > 2013 THEN
        SEQUENCIA := FAZERLANCAMENTO(  '622110000' , '522190109' , CODHISTORICO , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        SEQUENCIA := FAZERLANCAMENTO(  '522120100' , '622110000' , CODHISTORICO , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        SEQUENCIA := FAZERLANCAMENTO(  '522130300' , '522139900' , CODHISTORICO , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    ELSIF EXERCICIO::integer = 2013 THEN
        SEQUENCIA := FAZERLANCAMENTO(  '522190101' , '622110000' , CODHISTORICO , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        SEQUENCIA := FAZERLANCAMENTO(  '522120100' , '522190109' , CODHISTORICO , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    ELSE
        SEQUENCIA := FAZERLANCAMENTO(  '192190201000000' , '292110000000000' , CODHISTORICO , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        SEQUENCIA := FAZERLANCAMENTO(  '292110000000000' , '192190209000000' , CODHISTORICO , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    END IF;

    RETURN SEQUENCIA;
END;
$$ LANGUAGE 'plpgsql';

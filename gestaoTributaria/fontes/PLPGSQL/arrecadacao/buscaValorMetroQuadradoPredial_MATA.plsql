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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: buscaValorMetroQuadradoPredial_MATA.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.1  2007/01/23 11:03:03  fabio
correção da tag de caso de uso
e movidos do cadastro econômico


*/

CREATE OR REPLACE FUNCTION imobiliario.fn_busca_valor_m2_predial( INTEGER, INTEGER, INTEGER )  RETURNS varchar AS '
DECLARE
    inCodContrucao      ALIAS FOR $1;
    inCodTipoConstrucao ALIAS FOR $2;
    inExercicio         ALIAS FOR $3;
    stRetorno           VARCHAR;

    
BEGIN

    stRetorno := null;

    SELECT                     
        atcv.valor
    INTO
        stRetorno
    FROM

        imobiliario.atributo_tipo_edificacao_valor as atev

        INNER JOIN arrecadacao.tabela_conversao_valores as atcv
        ON atcv.parametro_1 = atev.valor

        LEFT JOIN (
                     SELECT
                             MAX(timestamp) AS timestamp
                       FROM  imobiliario.atributo_tipo_edificacao_valor
                      WHERE  atributo_tipo_edificacao_valor.cod_construcao = inCodContrucao
                        AND  atributo_tipo_edificacao_valor.cod_atributo = 3
                        AND  atributo_tipo_edificacao_valor.cod_tipo = inCodTipoConstrucao
                  ) AS atevf
        ON atevf.timestamp = atev.timestamp

    WHERE
        atev.cod_construcao = inCodContrucao
        AND atev.cod_tipo =  inCodTipoConstrucao
        AND atev.cod_atributo = 3
        AND atcv.exercicio = ''||quote_literal(inExercicio)|''
        AND atcv.cod_tabela = 30
        AND atevf.timestamp IS NOT NULL 
        
    ;


    RETURN stRetorno;
END;
' LANGUAGE 'plpgsql';

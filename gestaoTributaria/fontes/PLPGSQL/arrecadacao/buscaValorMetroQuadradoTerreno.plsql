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
* $Id: buscaValorMetroQuadradoTerreno.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.1  2007/01/23 11:03:03  fabio
correção da tag de caso de uso
e movidos do cadastro econômico


*/

CREATE OR REPLACE FUNCTION imobiliario.fn_busca_valor_m2_terreno( INTEGER )  RETURNS varchar AS $$
DECLARE
    inImovel    ALIAS FOR $1;
    stRetorno   VARCHAR;
    stSql       VARCHAR;
    reRecord                record;
    
BEGIN


        SELECT trecho_valor_m2.valor_m2_territorial ||'§'|| trecho_valor_m2.valor_m2_predial
          INTO stRetorno
          FROM imobiliario.imovel_confrontacao
    INNER JOIN imobiliario.confrontacao_trecho
            ON confrontacao_trecho.cod_confrontacao = imovel_confrontacao.cod_confrontacao
           AND confrontacao_trecho.cod_lote         = imovel_confrontacao.cod_lote
    INNER JOIN (
                   SELECT trecho_valor_m2.cod_logradouro
                        , trecho_valor_m2.cod_trecho
                        , MAX(timestamp)    AS timestamp
                        , MAX(dt_vigencia)  AS dt_vigencia
                     FROM imobiliario.trecho_valor_m2
                    WHERE dt_vigencia < NOW()::DATE
                 GROUP BY trecho_valor_m2.cod_logradouro
                        , trecho_valor_m2.cod_trecho
               ) AS max_trecho
            ON max_trecho.cod_logradouro = confrontacao_trecho.cod_logradouro
           AND max_trecho.cod_trecho     = confrontacao_trecho.cod_trecho
    INNER JOIN imobiliario.trecho_valor_m2
            ON trecho_valor_m2.cod_trecho     = max_trecho.cod_trecho
           AND trecho_valor_m2.cod_logradouro = max_trecho.cod_logradouro
           AND trecho_valor_m2.timestamp      = max_trecho.timestamp
           AND trecho_valor_m2.dt_vigencia    = max_trecho.dt_vigencia
         WHERE imovel_confrontacao.inscricao_municipal = inImovel
             ;


    RETURN stRetorno;
END;
$$ LANGUAGE 'plpgsql';

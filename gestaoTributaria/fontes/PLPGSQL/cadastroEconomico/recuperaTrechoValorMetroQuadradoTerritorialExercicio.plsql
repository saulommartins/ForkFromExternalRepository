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
* $Id: recuperaTrechoValorMetroQuadradoTerritorial.sql 29200 2008-04-15 13:48:27Z fabio $
*
* Casos de uso: uc-05.03.05
*
*/
--
-- Criação das Funções no banco.
--
CREATE OR REPLACE FUNCTION recuperaTrechoValorMetroQuadradoTerritorialExercicio( intInscricaoMunicipal INTEGER, intExercicio INTEGER )
RETURNS CHARACTER VARYING AS $$
DECLARE
    varValor          VARCHAR;
    intCodTrecho      INTEGER;
    intCodLogradouro  INTEGER;
BEGIN

        SELECT trecho_valor_m2.valor_m2_territorial
          INTO varValor
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
                    WHERE EXTRACT(year FROM trecho_valor_m2.dt_vigencia ) <= intExercicio
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
         WHERE imovel_confrontacao.inscricao_municipal = intInscricaoMunicipal
             ;

    RETURN varValor;
END;
$$ LANGUAGE 'plpgsql';


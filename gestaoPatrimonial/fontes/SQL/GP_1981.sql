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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id:  $
*
* Versão 1.98.1
*/


----------------
-- Ticket #16034
----------------

UPDATE almoxarifado.lancamento_material
   SET valor_mercado = ajusta_lanc_material.vlr_atualizado
  FROM (
             SELECT lancamento_material.cod_lancamento
                  , lancamento_material.cod_item
                  , lancamento_material.cod_marca
                  , lancamento_material.cod_almoxarifado
                  , lancamento_material.cod_centro
                  , lancamento_material.quantidade 
                  , lancamento_material.valor_mercado 
                  , lancamento_material.tipo_natureza   AS tipo
                  , (
                        (
                              SELECT CASE WHEN lancamento_material.tipo_natureza = 'S' THEN
                                         COALESCE((SUM(lm.valor_mercado) / SUM(lm.quantidade))::numeric(14,2), 0.00)*-1
                                     ELSE
                                         COALESCE((SUM(lm.valor_mercado) / SUM(lm.quantidade))::numeric(14,2), 0.00)
                                     END                              AS vlr_unitario
                                FROM almoxarifado.lancamento_material AS lm
                          INNER JOIN almoxarifado.natureza_lancamento AS nl
                                  ON nl.exercicio_lancamento = lm.exercicio_lancamento
                                 AND nl.num_lancamento       = lm.num_lancamento
                                 AND nl.cod_natureza         = lm.cod_natureza
                                 AND nl.tipo_natureza        = lm.tipo_natureza
                               WHERE lm.cod_item      = lancamento_material.cod_item
                                 AND lm.tipo_natureza = 'E'
                                 AND nl.timestamp < natureza_lancamento.timestamp
                        )
                        * lancamento_material.quantidade
                    )::numeric(14,2)                    AS vlr_atualizado
               FROM almoxarifado.lancamento_material
         INNER JOIN almoxarifado.natureza_lancamento
                 ON natureza_lancamento.exercicio_lancamento = lancamento_material.exercicio_lancamento
                AND natureza_lancamento.num_lancamento       = lancamento_material.num_lancamento
                AND natureza_lancamento.cod_natureza         = lancamento_material.cod_natureza
                AND natureza_lancamento.tipo_natureza        = lancamento_material.tipo_natureza
              WHERE 1=1
                AND lancamento_material.valor_mercado = 0
                AND lancamento_material.quantidade <> 0
           ORDER BY lancamento_material.cod_item
                  , lancamento_material.cod_centro
       )                                                AS ajusta_lanc_material
 WHERE ajusta_lanc_material.cod_lancamento   = lancamento_material.cod_lancamento
   AND ajusta_lanc_material.cod_item         = lancamento_material.cod_item
   AND ajusta_lanc_material.cod_marca        = lancamento_material.cod_marca
   AND ajusta_lanc_material.cod_almoxarifado = lancamento_material.cod_almoxarifado
   AND ajusta_lanc_material.cod_centro       = lancamento_material.cod_centro
     ;


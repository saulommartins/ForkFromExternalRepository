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
* Versao 2.05.2
*
* Fabio Bertoldi - 20160601
*
*/

----------------
-- Ticket #23794
----------------

ALTER TABLE arrecadacao.imovel_calculo DROP CONSTRAINT pk_imovel_calculo;
ALTER TABLE arrecadacao.imovel_calculo ADD  CONSTRAINT pk_imovel_calculo PRIMARY KEY (cod_calculo, inscricao_municipal);

ALTER TABLE arrecadacao.cadastro_economico_calculo DROP CONSTRAINT pk_cadastro_economico_calculo;
ALTER TABLE arrecadacao.cadastro_economico_calculo ADD  CONSTRAINT pk_cadastro_economico_calculo PRIMARY KEY (cod_calculo, inscricao_economica);


INSERT
  INTO arrecadacao.imovel_calculo
     ( cod_calculo
     , timestamp
     , inscricao_municipal
     )

  SELECT lancamento_calculo.cod_calculo
       , ( SELECT MAX(imovel_v_venal.timestamp) FROM arrecadacao.imovel_v_venal WHERE timestamp < divida.timestamp AND inscricao_municipal = divida.inscricao_municipal) AS timestamp
       , divida.inscricao_municipal
    FROM arrecadacao.lancamento
    JOIN arrecadacao.lancamento_calculo
      ON lancamento_calculo.cod_lancamento = lancamento.cod_lancamento
    JOIN arrecadacao.calculo
      ON calculo.cod_calculo = lancamento_calculo.cod_calculo
    JOIN (
               SELECT divida_imovel.inscricao_municipal
                    , divida_parcelamento.cod_inscricao
                    , divida_parcelamento.exercicio
                    , divida_parcelamento.num_parcelamento
                    , parcelamento.timestamp
                    , parcela_calculo.cod_calculo
                FROM divida.divida_imovel
                JOIN divida.divida_parcelamento
                  ON divida_parcelamento.cod_inscricao = divida_imovel.cod_inscricao
                 AND divida_parcelamento.exercicio     = divida_imovel.exercicio
                JOIN divida.parcelamento
                  ON parcelamento.num_parcelamento = divida_parcelamento.num_parcelamento
                JOIN divida.parcela_calculo
                  ON parcela_calculo.num_parcelamento = parcelamento.num_parcelamento
--                WHERE divida_imovel.inscricao_municipal = 9
            GROUP BY divida_imovel.inscricao_municipal
                   , divida_parcelamento.cod_inscricao
                   , divida_parcelamento.exercicio
                   , divida_parcelamento.num_parcelamento
                    , parcelamento.timestamp
                   , parcela_calculo.cod_calculo
         ) AS divida
      ON divida.cod_calculo = lancamento_calculo.cod_calculo
   WHERE lancamento.divida = TRUE
     AND lancamento_calculo.cod_calculo NOT IN (
                                                 SELECT cod_calculo
                                                   FROM arrecadacao.imovel_calculo
                                               )
GROUP BY lancamento_calculo.cod_calculo
       , divida.timestamp
       , divida.inscricao_municipal
       ;


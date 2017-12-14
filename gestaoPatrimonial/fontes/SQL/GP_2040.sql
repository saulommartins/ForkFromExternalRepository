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
* Versao 2.04.0
*
* Fabio Bertoldi - 20150617
*
*/

----------------
-- Ticket #22576
----------------


ALTER TABLE compras.ordem_item DROP CONSTRAINT fk_ordem_item_1;
ALTER TABLE compras.ordem_item ADD CONSTRAINT fk_ordem_item_1 FOREIGN KEY                        (exercicio_pre_empenho, cod_pre_empenho, num_item)
                                                              REFERENCES empenho.item_pre_empenho(exercicio            , cod_pre_empenho, num_item);


ALTER TABLE compras.ordem_item ADD COLUMN cod_marca  INTEGER;
ALTER TABLE compras.ordem_item ADD COLUMN cod_centro INTEGER;
ALTER TABLE compras.ordem_item ADD COLUMN cod_item   INTEGER;

ALTER TABLE compras.ordem_item ADD CONSTRAINT fk_ordem_item_3 FOREIGN KEY                                 (cod_item, cod_marca)
                                                              REFERENCES almoxarifado.catalogo_item_marca (cod_item, cod_marca);

ALTER TABLE compras.ordem_item ADD CONSTRAINT fk_ordem_item_4 FOREIGN KEY                          (cod_centro)
                                                              REFERENCES almoxarifado.centro_custo (cod_centro);


ALTER TABLE compras.ordem ADD COLUMN numcgm_entrega INTEGER;

ALTER TABLE compras.ordem ADD CONSTRAINT fk_ordem_2 FOREIGN KEY       (numcgm_entrega)
                                                    REFERENCES sw_cgm (numcgm);


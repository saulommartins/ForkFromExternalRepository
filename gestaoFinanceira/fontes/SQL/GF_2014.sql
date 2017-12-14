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
* Versao 2.01.4
*
* Fabio Bertoldi - 20121220
*
*/

----------------
-- Ticket #19956
----------------

CREATE TABLE contabilidade.configuracao_lancamento_conta_despesa_item (
    cod_item            INTEGER NOT NULL,
    cod_conta_despesa   INTEGER NOT NULL,
    exercicio           CHAR(4) NOT NULL,
    CONSTRAINT pk_configuracao_lancamento_conta_despesa_item    PRIMARY KEY                             (cod_item, cod_conta_despesa, exercicio),
    CONSTRAINT fk_configuracao_lancamento_conta_despesa_item_1  FOREIGN KEY                             (cod_item)
                                                                REFERENCES almoxarifado.catalogo_item   (cod_item),
    CONSTRAINT fk_configuracao_lancamento_conta_despesa_item_2  FOREIGN KEY                             (cod_conta_despesa, exercicio)
                                                                REFERENCES orcamento.conta_despesa      (cod_conta, exercicio),
    CONSTRAINT uk_configuracao_lancamento_conta_despesa_item    UNIQUE                                  (cod_item, exercicio)
);
GRANT ALL ON contabilidade.configuracao_lancamento_conta_despesa_item TO siamweb;

INSERT
  INTO contabilidade.historico_contabil
     ( cod_historico
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
     )
VALUES
     ( 960
     , '2013'
     , 'Vlr. Ref. a Saída do Almoxarifado.'
     , TRUE
     , FALSE
     );

INSERT
  INTO contabilidade.historico_contabil
     ( cod_historico
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
     )
VALUES
     ( 961
     , '2013'
     , 'Vlr ref. a Entrada do Almoxarifado'
     , TRUE
     , FALSE
     );


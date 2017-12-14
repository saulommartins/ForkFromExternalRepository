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
* Versao 2.03.9
*
* Fabio Bertoldi - 20150513
*
*/

----------------
-- Ticket #22872
----------------

SELECT atualizarbanco('ALTER TABLE ima.configuracao_dirf_inss DROP CONSTRAINT pk_configuracao_dirf_inss;');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_dirf_inss ADD  CONSTRAINT pk_configuracao_dirf_inss PRIMARY KEY (exercicio, cod_conta);');

SELECT atualizarbanco('ALTER TABLE ima.configuracao_dirf_irrf DROP CONSTRAINT pk_configuracao_dirf_irrf;');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_dirf_irrf ADD  CONSTRAINT pk_configuracao_dirf_irrf PRIMARY KEY (exercicio, cod_conta);');


----------------
-- Ticket #22872
----------------

SELECT atualizarbanco('ALTER TABLE ima.configuracao_dirf_irrf RENAME TO configuracao_dirf_irrf_plano_conta;');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_dirf_irrf_conta_receita(
    exercicio       char(4)     not null,
    cod_conta       integer     not null,
    CONSTRAINT pk_configuracao_dirf_irrf_conta_receita      PRIMARY KEY (exercicio, cod_conta),
    CONSTRAINT fk_configuracao_dirf_irrf_conta_receita_1    FOREIGN KEY (exercicio)
                                                            REFERENCES ima.configuracao_dirf(exercicio),
    CONSTRAINT fk_configuracao_dirf_irrf_conta_receita_2    FOREIGN KEY (exercicio, cod_conta)
                                                            REFERENCES orcamento.conta_receita(exercicio, cod_conta)
);
');
SELECT atualizarbanco('GRANT ALL ON ima.configuracao_dirf_irrf_conta_receita TO urbem;');


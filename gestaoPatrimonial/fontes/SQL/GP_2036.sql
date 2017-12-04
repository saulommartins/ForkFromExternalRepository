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
* Versao 2.03.6
*
* Fabio Bertoldi - 201502127
*
*/

----------------
-- Ticket #22680
----------------

CREATE TABLE patrimonio.bem_processo (
    cod_bem             INTEGER NOT NULL,
    ano_exercicio       VARCHAR(4) NOT NULL,
    cod_processo        INTEGER NOT NULL,
    CONSTRAINT pk_bem_processo      PRIMARY KEY (cod_bem),
    CONSTRAINT fk_bem_processo_1    FOREIGN KEY (cod_bem)
                                    REFERENCES patrimonio.bem (cod_bem),
    CONSTRAINT fk_bem_processo_2    FOREIGN KEY (cod_processo,ano_exercicio)
                                    REFERENCES sw_processo (cod_processo,ano_exercicio)
);

GRANT ALL ON patrimonio.bem_processo TO GROUP urbem;

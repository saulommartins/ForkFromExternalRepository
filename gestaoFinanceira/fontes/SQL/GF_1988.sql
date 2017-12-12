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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id:$
*
* Versão 1.98.8
*/

CREATE TABLE empenho.ordem_pagamento_recibo_extra (
    exercicio          CHAR(4)     NOT NULL,
    cod_entidade       INTEGER     NOT NULL,
    cod_ordem          INTEGER     NOT NULL,
    cod_recibo_extra   INTEGER     NOT NULL,
    tipo_recibo        CHAR(1)     NOT NULL,
    CONSTRAINT pk_ordem_pagamento_recibo_extra   PRIMARY KEY                        (exercicio, cod_entidade, cod_ordem, cod_recibo_extra, tipo_recibo),
    CONSTRAINT fk_ordem_pagamento_recibo_extra_1 FOREIGN KEY                        (exercicio, cod_entidade, cod_ordem)
                                                 REFERENCES empenho.ordem_pagamento (exercicio, cod_entidade, cod_ordem),
    CONSTRAINT fk_ordem_pagamento_recibo_extra_2 FOREIGN KEY                        (cod_recibo_extra, exercicio, cod_entidade, tipo_recibo)
                                                 REFERENCES tesouraria.recibo_extra (cod_recibo_extra, exercicio, cod_entidade, tipo_recibo)
);

GRANT ALL ON empenho.ordem_pagamento_recibo_extra TO GROUP urbem;


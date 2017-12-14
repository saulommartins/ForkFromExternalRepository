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
* Versao 2.04.2
*
* Fabio Bertoldi - 20150731
*
*/

----------------
-- Ticket #23111
----------------

DROP FUNCTION contabilidade.fn_totaliza_variacao_patrimonial(VARCHAR);


----------------
-- Ticket #
----------------

CREATE TABLE tcmba.nota_fiscal_liquidacao(
    cod_nota_liquidacao     INTEGER         NOT NULL,
    exercicio_liquidacao    VARCHAR(4)      NOT NULL,
    cod_entidade            INTEGER         NOT NULL,
    ano                     CHAR(4)                 ,
    nro_nota                VARCHAR(20)             ,
    nro_serie               VARCHAR(8)              ,
    nro_subserie            VARCHAR(8)              ,
    data_emissao            DATE                    ,
    vl_nota                 NUMERIC(14,2)           ,
    descricao               TEXT                    ,
    cod_uf                  INTEGER                 ,
    CONSTRAINT pk_tcmba_nota_fiscal_liquidacao      PRIMARY KEY (cod_nota_liquidacao, exercicio_liquidacao, cod_entidade),
    CONSTRAINT fk_tcmba_nota_fiscal_liquidacao_1    FOREIGN KEY (cod_nota_liquidacao, exercicio_liquidacao, cod_entidade)
                                                    REFERENCES empenho.nota_liquidacao(cod_nota, exercicio, cod_entidade)
);
GRANT ALL ON tcmba.nota_fiscal_liquidacao TO urbem;


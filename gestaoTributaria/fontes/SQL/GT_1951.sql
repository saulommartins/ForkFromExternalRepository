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
* $Id: GT_1951.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.95.1
*/

----------------------------------------------------------------------------------------
-- Ticket #13978
-- EXCLUINDO RELACIONAMENTO ENTRE administracao.modelo_documento E fiscalizacao.infracao
----------------------------------------------------------------------------------------

ALTER TABLE fiscalizacao.infracao DROP CONSTRAINT fk_infracao_3;
ALTER TABLE fiscalizacao.infracao DROP COLUMN     cod_tipo_documento;
ALTER TABLE fiscalizacao.infracao DROP COLUMN     cod_documento;


--------------------------------------------
-- TABELA P/ OBSERVAÇÔES DEVEDOR/NAO DEVEDOR
-- FERNANDO CERCATO - 20081217 -------------

CREATE TABLE arrecadacao.observacao_debito_layout_carne (
    cod_modelo              INTEGER         NOT NULL,
    observacao_devedor      VARCHAR(100)    NOT NULL,
    observacao_nao_devedor  VARCHAR(100)    NOT NULL,
    CONSTRAINT pk_observacao_debitos        PRIMARY KEY                             (cod_modelo),
    CONSTRAINT fk_observacao_debitos_1      FOREIGN KEY                             (cod_modelo)
                                            REFERENCES arrecadacao.modelo_carne     (cod_modelo)
);

GRANT ALL ON arrecadacao.observacao_debito_layout_carne TO GROUP urbem;


----------------
-- Ticket #14134
----------------

UPDATE fiscalizacao.tipo_penalidade SET descricao = 'Demolição' WHERE cod_tipo = 3;

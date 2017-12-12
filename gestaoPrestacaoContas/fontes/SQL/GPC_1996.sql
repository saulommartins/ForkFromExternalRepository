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
* Versão 1.99.6
*/

----------------
-- Ticket #16988
----------------

ALTER TABLE tcmgo.obra ADD COLUMN fiscal VARCHAR(50);


----------------
-- Ticket #16986
----------------

ALTER TABLE tcmgo.orgao_gestor ADD COLUMN cargo VARCHAR(50);


----------------
-- Ticket #17121
----------------

ALTER TABLE tcmgo.contrato ADD COLUMN numero_termo CHAR(4);


----------------
-- Ticket #17202
----------------

CREATE TABLE tcmgo.tipo_nota_fiscal(
    cod_tipo        INTEGER         NOT NULL,
    descricao       VARCHAR(30)     NOT NULL,
    CONSTRAINT pk_tipo_nota_fiscal  PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tcmgo.tipo_nota_fiscal TO GROUP urbem;

INSERT INTO tcmgo.tipo_nota_fiscal VALUES (1, 'Nota Fiscal'           );
INSERT INTO tcmgo.tipo_nota_fiscal VALUES (2, 'Cupom Fiscal'          );
INSERT INTO tcmgo.tipo_nota_fiscal VALUES (3, 'Nota Fiscal Eletrônica');
INSERT INTO tcmgo.tipo_nota_fiscal VALUES (4, 'Recibo'                );
INSERT INTO tcmgo.tipo_nota_fiscal VALUES (5, 'Folha Pagamento'       );
INSERT INTO tcmgo.tipo_nota_fiscal VALUES (6, 'Despesas bancárias'    );

ALTER TABLE tcmgo.nota_fiscal ADD   COLUMN cod_tipo INTEGER;
UPDATE      tcmgo.nota_fiscal SET          cod_tipo = 1;
ALTER TABLE tcmgo.nota_fiscal ALTER COLUMN cod_tipo SET NOT NULL;


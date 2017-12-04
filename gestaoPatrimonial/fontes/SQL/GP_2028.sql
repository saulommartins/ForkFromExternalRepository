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
* Versao 2.02.8
*
* Fabio Bertolri - 20140725
*
*/

----------------
-- Ticket #21952
----------------

CREATE TABLE patrimonio.tipo_natureza(
    codigo      INTEGER             NOT NULL,
    descricao   VARCHAR(20)         NOT NULL,
    CONSTRAINT pk_tipo_natureza     PRIMARY KEY (codigo)
);
GRANT ALL ON patrimonio.tipo_natureza TO urbem;

INSERT INTO patrimonio.tipo_natureza VALUES (0, 'Não Informado'   );
INSERT INTO patrimonio.tipo_natureza VALUES (1, 'Bens Móveis'     );
INSERT INTO patrimonio.tipo_natureza VALUES (2, 'Bens Imóveis'    );
INSERT INTO patrimonio.tipo_natureza VALUES (3, 'Bens Intangíveis');
INSERT INTO patrimonio.tipo_natureza VALUES (9, 'Outros'          );


ALTER TABLE patrimonio.natureza ADD COLUMN cod_tipo INTEGER;
UPDATE patrimonio.natureza SET cod_tipo = 0;
ALTER TABLE patrimonio.natureza ALTER COLUMN cod_tipo SET NOT NULL;
ALTER TABLE patrimonio.natureza ADD CONSTRAINT fk_natureza_1 FOREIGN KEY (cod_tipo)
                                                             REFERENCES patrimonio.tipo_natureza(codigo);


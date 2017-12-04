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
* Fabio Bertoldi - 20150428
*
*/

----------------
-- Ticket #22887
----------------

CREATE TABLE licitacao.tipo_chamada_publica(
    cod_tipo        INTEGER             NOT NULL,
    descricao       VARCHAR(50)         NOT NULL,
    CONSTRAINT pk_tipo_chamada_publica  PRIMARY KEY (cod_tipo)
);
GRANT ALL ON licitacao.tipo_chamada_publica TO urbem;

INSERT INTO licitacao.tipo_chamada_publica VALUES (0, 'Não'                                );
INSERT INTO licitacao.tipo_chamada_publica VALUES (1, 'Dispensa por Chamada Pública'       );
INSERT INTO licitacao.tipo_chamada_publica VALUES (2, 'Inexigibilidade por Chamada Pública');

ALTER TABLE licitacao.licitacao ADD COLUMN tipo_chamada_publica INTEGER NOT NULL DEFAULT 0;
ALTER TABLE licitacao.licitacao ADD CONSTRAINT fk_licitacao_11  FOREIGN KEY (tipo_chamada_publica)
                                                                REFERENCES licitacao.tipo_chamada_publica (cod_tipo);



---------------------------------------------------------------
-- CORRECAO DAS PEMISSOES DE patrimonio.grupo_plano_depreciacao
---------------------------------------------------------------

GRANT ALL ON TABLE patrimonio.grupo_plano_depreciacao TO urbem;


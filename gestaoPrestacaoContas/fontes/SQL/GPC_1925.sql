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
* $Id:  $
*
* Versão 1.92.5
*/

------------------------------------------
-- ADICIONANDO TABELA tcepb.arquivo_cargos
------------------------------------------

CREATE TABLE tcepb.arquivo_cargos (
    cod_cargo       INTEGER         NOT NULL,
    periodo         VARCHAR         NOT NULL,
    CONSTRAINT pk_arquivo_cargos    PRIMARY KEY             (cod_cargo),
    CONSTRAINT fk_arquivo_cargos_1  FOREIGN KEY             (cod_cargo)
                                    REFERENCES pessoal.cargo(cod_cargo)
);

GRANT ALL ON tcepb.arquivo_cargos TO GROUP urbem;


--------------------------------------
-- ADICIONANDO TABELA tcepb.servidores
--------------------------------------

CREATE TABLE tcepb.servidores (
    numcgm      INTEGER         NOT NULL,
    periodo     VARCHAR(6)      NOT NULL,
    CONSTRAINT pk_servidores    PRIMARY KEY       (numcgm),
    CONSTRAINT fk_servidores_1  FOREIGN KEY       (numcgm)
                                REFERENCES sw_cgm (numcgm)
);

GRANT ALL ON tcepb.servidores TO GROUP urbem;


--------------------------------------------------------------
-- ADICIONANDO TABELA tcepb.arquivo_codigo_vantagens_descontos
--------------------------------------------------------------

CREATE TABLE tcepb.arquivo_codigo_vantagens_descontos (
    cod_vantagem_desconto     VARCHAR       NOT NULL,
    periodo                   VARCHAR       NOT NULL,
    CONSTRAINT pk_arquivo_codigo_vantagens_descontos    PRIMARY KEY                      (cod_vantagem_desconto),
    CONSTRAINT fk_arquivo_codigo_vantagens_descontos_1  FOREIGN KEY                      (cod_vantagem_desconto)
                                                        REFERENCES folhapagamento.evento (codigo)
);

GRANT ALL ON tcepb.arquivo_codigo_vantagens_descontos TO GROUP urbem;


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
* Versao 2.02.6
*
* Fabio Bertolri - 20140605
*
*/

----------------
-- Ticket #21788
----------------

CREATE TABLE licitacao.tipo_adesao_ata(
    codigo      INTEGER         NOT NULL,
    descricao   VARCHAR(40)     NOT NULL,
    CONSTRAINT pk_tipo_adesao_ata   PRIMARY KEY (codigo)
);
GRANT ALL ON licitacao.tipo_adesao_ata TO urbem;

INSERT INTO licitacao.tipo_adesao_ata VALUES (0, 'Não Informado'                    );
INSERT INTO licitacao.tipo_adesao_ata VALUES (1, 'Adesão ata própria (PARTICIPANTE)');
INSERT INTO licitacao.tipo_adesao_ata VALUES (2, 'Adesão ata EXTERNA (CARONA)'      );

ALTER TABLE licitacao.ata ADD   COLUMN dt_validade_ata DATE;
UPDATE      licitacao.ata SET          dt_validade_ata = '2099-12-31';
ALTER TABLE licitacao.ata ALTER COLUMN dt_validade_ata SET NOT NULL;

ALTER TABLE licitacao.ata ADD COLUMN tipo_adesao     INTEGER NOT NULL DEFAULT 0;
ALTER TABLE licitacao.ata ADD CONSTRAINT fk_ata_2 FOREIGN KEY (tipo_adesao)
                                                  REFERENCES licitacao.tipo_adesao_ata(codigo);


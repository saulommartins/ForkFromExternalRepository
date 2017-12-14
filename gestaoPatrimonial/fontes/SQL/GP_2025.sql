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
* Versao 2.02.5
*
* Fabio Bertolri - 20140429
*
*/

----------------
-- Ticket #21693
----------------

CREATE TABLE licitacao.publicacao_ata(
    id              INTEGER         NOT NULL,
    ata_id          INTEGER         NOT NULL,
    numcgm          INTEGER         NOT NULL,
    dt_publicacao   DATE            NOT NULL,
    observacao      VARCHAR(80)     NOT NULL DEFAULT '',
    num_publicacao  INTEGER                 ,
    CONSTRAINT pk_publicacao_ata    PRIMARY KEY                                 (id),
    CONSTRAINT fk_publicacao_ata_1  FOREIGN KEY                                 (ata_id)
                                    REFERENCES licitacao.ata                    (id),
    CONSTRAINT fk_publicacao_ata_2  FOREIGN KEY                                 (numcgm)
                                    REFERENCES licitacao.veiculos_publicidade   (numcgm),
    CONSTRAINT uk_publicacao_ata_1  UNIQUE                                      (ata_id,numcgm,dt_publicacao)
);
GRANT ALL ON licitacao.publicacao_ata TO GROUP urbem;


----------------
-- Ticket #21707
----------------

ALTER TABLE patrimonio.bem_comprado ADD COLUMN num_orgao    INTEGER;
ALTER TABLE patrimonio.bem_comprado ADD COLUMN num_unidade  INTEGER;
ALTER TABLE patrimonio.bem_comprado ADD CONSTRAINT fk_bem_comprado_3    FOREIGN KEY (exercicio, num_orgao, num_unidade)
                                                                        REFERENCES orcamento.unidade (exercicio, num_orgao, num_unidade);


----------------
-- Ticket #21708
----------------

CREATE TABLE patrimonio.veiculo_uniorcam(
    cod_veiculo         INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    cod_entidade        INTEGER         NOT NULL,
    num_orgao           INTEGER         NOT NULL,
    num_unidade         INTEGER         NOT NULL,
    CONSTRAINT pk_veiculo_uniorcam      PRIMARY KEY                     (cod_veiculo),
    CONSTRAINT fk_veiculo_uniorcam_1    FOREIGN KEY                     (cod_veiculo)
                                        REFERENCES frota.veiculo        (cod_veiculo),
    CONSTRAINT fk_veiculo_uniorcam_2    FOREIGN KEY                     (exercicio, cod_entidade)
                                        REFERENCES  orcamento.entidade  (exercicio, cod_entidade),
    CONSTRAINT fk_veiculo_uniorcam_3    FOREIGN KEY                     (exercicio, num_orgao, num_unidade)
                                        REFERENCES  orcamento.unidade   (exercicio, num_orgao, num_unidade)
);
GRANT ALL ON patrimonio.veiculo_uniorcam TO GROUP urbem;


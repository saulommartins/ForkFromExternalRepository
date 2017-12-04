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
* Versao 2.03.2
*
* Fabio Bertoldi - 20141001
*
*/

----------------
-- Ticket #20569
----------------

ALTER TABLE patrimonio.grupo_plano_analitica ADD COLUMN cod_plano_doacao             INTEGER;
ALTER TABLE patrimonio.grupo_plano_analitica ADD COLUMN cod_plano_perda_involuntaria INTEGER;
ALTER TABLE patrimonio.grupo_plano_analitica ADD COLUMN cod_plano_transferencia      INTEGER;

ALTER TABLE patrimonio.grupo_plano_analitica ADD CONSTRAINT fk_grupo_plano_analitica_3 FOREIGN KEY                              (cod_plano_doacao            , exercicio)
                                                                                       REFERENCES contabilidade.plano_analitica (cod_plano                   , exercicio);
ALTER TABLE patrimonio.grupo_plano_analitica ADD CONSTRAINT fk_grupo_plano_analitica_4 FOREIGN KEY                              (cod_plano_perda_involuntaria, exercicio)
                                                                                       REFERENCES contabilidade.plano_analitica (cod_plano                   , exercicio);
ALTER TABLE patrimonio.grupo_plano_analitica ADD CONSTRAINT fk_grupo_plano_analitica_5 FOREIGN KEY                              (cod_plano_transferencia     , exercicio)
                                                                                       REFERENCES contabilidade.plano_analitica (cod_plano                   , exercicio);


CREATE TABLE patrimonio.tipo_baixa(
    cod_tipo        INTEGER     NOT NULL,
    descricao       VARCHAR(40) NOT NULL,
    CONSTRAINT pk_tipo_baixa    PRIMARY KEY (cod_tipo)
);
GRANT ALL ON patrimonio.tipo_baixa TO urbem;

INSERT INTO patrimonio.tipo_baixa VALUES (0, 'Não Informado'                     );
INSERT INTO patrimonio.tipo_baixa VALUES (1, 'Doação de Bens Imóveis'            );
INSERT INTO patrimonio.tipo_baixa VALUES (2, 'Doação de Bens Móveis'             );
INSERT INTO patrimonio.tipo_baixa VALUES (3, 'Transferência de Bens Imóveis'     );
INSERT INTO patrimonio.tipo_baixa VALUES (4, 'Transferência de Bens Móveis'      );
INSERT INTO patrimonio.tipo_baixa VALUES (5, 'Perda Involuntária de Bens Imóveis');
INSERT INTO patrimonio.tipo_baixa VALUES (6, 'Perda Involuntária de Bens Móveis' );


ALTER TABLE patrimonio.bem_baixado ADD COLUMN     tipo_baixa INTEGER NOT NULL DEFAULT 0;
ALTER TABLE patrimonio.bem_baixado ADD CONSTRAINT fk_bem_baixado_2 FOREIGN KEY                      (tipo_baixa)
                                                                   REFERENCES patrimonio.tipo_baixa (cod_tipo);


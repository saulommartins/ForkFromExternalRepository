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
* Versao 2.04.6
*
* Fabio Bertoldi - 20160115
*
*/

----------------
-- Ticket #23331
----------------

INSERT INTO tcemg.consideracao_arquivo VALUES (43, 'INCPRO'   );
INSERT INTO tcemg.consideracao_arquivo VALUES (44, 'INCAMP'   );
INSERT INTO tcemg.consideracao_arquivo VALUES (45, 'LPP'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (47, 'LOA'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (48, 'LDO'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (49, 'UOC'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (50, 'PRO'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (51, 'AMP'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (52, 'DSP'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (53, 'RFIS'     );
INSERT INTO tcemg.consideracao_arquivo VALUES (54, 'MTFIS'    );
INSERT INTO tcemg.consideracao_arquivo VALUES (55, 'MTBIARREC');
INSERT INTO tcemg.consideracao_arquivo VALUES (56, 'PERC'     );
INSERT INTO tcemg.consideracao_arquivo VALUES (57, 'IUOC'     );
INSERT INTO tcemg.consideracao_arquivo VALUES (58, 'CRONEM'   );
INSERT INTO tcemg.consideracao_arquivo VALUES (59, 'METAREAL' );

ALTER TABLE tcemg.consideracao_arquivo_descricao ALTER COLUMN modulo_sicom TYPE VARCHAR(21);


----------------
-- Ticket #23332
----------------

ALTER TABLE tcemg.metas_fiscais ADD COLUMN valor_corrente_receita_primaria_adv     NUMERIC (14,2);
ALTER TABLE tcemg.metas_fiscais ADD COLUMN valor_corrente_despesa_primaria_gerada  NUMERIC (14,2);
ALTER TABLE tcemg.metas_fiscais ADD COLUMN valor_constante_receita_primaria_adv    NUMERIC (14,2);
ALTER TABLE tcemg.metas_fiscais ADD COLUMN valor_constante_despesa_primaria_gerada NUMERIC (14,2);
ALTER TABLE tcemg.metas_fiscais ADD COLUMN percentual_pib_receita_primaria_adv     NUMERIC (7,3);
ALTER TABLE tcemg.metas_fiscais ADD COLUMN percentual_pib_despesa_primaria_adv     NUMERIC (7,3);


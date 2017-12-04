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
* Versao 2.04.4
*
* Fabio Bertoldi - 20151204
*
*/

----------------
-- Ticket #23429
----------------

SELECT atualizarbanco('
CREATE TABLE ima.tipo_convenio_caixa(
    cod_tipo    INTEGER     NOT NULL,
    descricao   VARCHAR(50) NOT NULL,
    CONSTRAINT pk_tipo_convenio_caixa   PRIMARY KEY (cod_tipo)
);
GRANT ALL ON ima.tipo_convenio_caixa TO urbem;
');

SELECT atualizarbanco('INSERT INTO ima.tipo_convenio_caixa VALUES (1, ''SIACC 150''                      );');
SELECT atualizarbanco('INSERT INTO ima.tipo_convenio_caixa VALUES (2, ''SICOV 150 - PADRAO 150 FEBRABAN'');');

SELECT atualizarbanco('ALTER TABLE ima.configuracao_convenio_caixa_economica_federal ADD COLUMN   cod_tipo INTEGER;'     );
SELECT atualizarbanco('UPDATE      ima.configuracao_convenio_caixa_economica_federal SET          cod_tipo = 1;'         );
SELECT atualizarbanco('ALTER TABLE ima.configuracao_convenio_caixa_economica_federal ALTER COLUMN cod_tipo SET NOT NULL;');


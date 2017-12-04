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
* Versao 2.02.0
*
* Fabio Bertoldi - 20121105
*
*/

----------------
-- Ticket #19050
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo
     )
VALUES
     ( 6
     , 36
     , 56
     , 'RREO - Anexo VII - Demonstrativo do Resultado Primário'
     , 'RREOAnexoVII.rptdesign'
     );


----------------
-- Ticket #19048
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo
     )
VALUES
     ( 6
     , 36
     , 57
     , 'RREO - Anexo V'
     , 'RREOAnexoV.rptdesign'
     );


----------------
-- Ticket #20677
----------------

ALTER TABLE tcern.nota_fiscal ALTER COLUMN nro_nota         DROP NOT NULL;
ALTER TABLE tcern.nota_fiscal ALTER COLUMN nro_serie        DROP NOT NULL;
ALTER TABLE tcern.nota_fiscal ALTER COLUMN data_emissao     DROP NOT NULL;
ALTER TABLE tcern.nota_fiscal ALTER COLUMN cod_validacao    DROP NOT NULL;
ALTER TABLE tcern.nota_fiscal ALTER COLUMN modelo           DROP NOT NULL;


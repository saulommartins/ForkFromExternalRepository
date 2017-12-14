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
* Versao 2.01.0
*
* Fabio Bertoldi - 20120816
*
*/

-----------------------------------
-- ALTERANDO BOLETOS PADRAO SIAMWEB
-----------------------------------

UPDATE arrecadacao.modelo_carne SET nom_modelo = 'Carnê ITBI - Urbem'           , nom_arquivo = 'RCarneItbiUrbem.class.php'           WHERE nom_arquivo = 'RCarneItbiSiamweb.class.php'          ;
UPDATE arrecadacao.modelo_carne SET nom_modelo = 'Carnê IPTU - Urbem'           , nom_arquivo = 'RCarneIptuUrbem.class.php'           WHERE nom_arquivo = 'RCarneIptuSiamweb.class.php'          ;
UPDATE arrecadacao.modelo_carne SET nom_modelo = 'Carnê Diversos - Layout Urbem', nom_arquivo = 'RCarneDiversosLayoutUrbem.class.php' WHERE nom_arquivo = 'RCarneDiversosLayoutSiamweb.class.php';
UPDATE arrecadacao.modelo_carne SET nom_modelo = 'Carnê Diversos - Urbem'       , nom_arquivo = 'RCarneDiversosUrbem.class.php'       WHERE nom_arquivo = 'RCarneDiversosSiamweb.class.php'      ;
UPDATE arrecadacao.modelo_carne SET nom_modelo = 'Carnê ISS - Urbem'            , nom_arquivo = 'RCarneIssUrbem.class.php'            WHERE nom_arquivo = 'RCarneIssSiamweb.class.php'           ;
UPDATE arrecadacao.modelo_carne SET nom_modelo = 'Carnê Divida - Urbem'         , nom_arquivo = 'RCarneDividaUrbem.class.php'         WHERE nom_arquivo = 'RCarneDividaSiamweb.class.php'        ;


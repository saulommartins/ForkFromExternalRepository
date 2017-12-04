<?php
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
?>
<?php
/**
*
* Data de Criação: 01/12/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.00.00
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkErro.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkRecordset.inc.php';

include_once CLA_SESSAO_LEGADA;
include_once CLA_SISTEMA_LEGADO;
include_once CLA_SESSAO;

include_once CLA_CAMPOTABELA;
include_once CLA_AUDITORIA;
include_once CLA_CONEXAO;
include_once CLA_PERSISTENTE;
include_once CLA_PERSISTENTE_SIAM;
include_once CLA_PERSISTENTE_ATRIBUTOS;
include_once CLA_PERSISTENTE_ATRIBUTOS_VALORES;
include_once CLA_TRANSACAO;
include_once CLA_CONEXAO_SIAM;
include_once CLA_TRANSACAO_SIAM;

?>

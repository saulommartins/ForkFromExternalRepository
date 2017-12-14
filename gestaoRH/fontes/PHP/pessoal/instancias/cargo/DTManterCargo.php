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
* Arquivo instância para popup de Servidor
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 30921 $
$Name$
$Author: souzadl $
$Date: 2007-06-12 17:04:22 -0300 (Ter, 12 Jun 2007) $

Casos de uso: uc-04.04.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

$rsServidores = new recordset;
if (Sessao::read("boEspecialidade")) {
    if (Sessao::read("inCodEspecialidade") != "") {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidadeSubDivisao.class.php");
        $obTPessoalEspecialidadeSubDivisao = new TPessoalEspecialidadeSubDivisao();
        $obTPessoalEspecialidadeSubDivisao->setDado("cod_regime",$_GET["cod_regime"]);
        $obTPessoalEspecialidadeSubDivisao->setDado("cod_sub_divisao",$_GET["cod_sub_divisao"]);
        $obTPessoalEspecialidadeSubDivisao->setDado("cod_especialidade",Sessao::read("inCodEspecialidade"));
        $obTPessoalEspecialidadeSubDivisao->consultarServidoresPorEspecialidade($rsServidores);
    }
} else {
    if ($_GET["cod_regime"] != "" and $_GET["cod_sub_divisao"] != "" and Sessao::read("inCodCargo") != "") {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCargoSubDivisao.class.php");
        $obTPessoalCargoSubDivisao = new TPessoalCargoSubDivisao();
        $obTPessoalCargoSubDivisao->setDado("cod_regime",$_GET["cod_regime"]);
        $obTPessoalCargoSubDivisao->setDado("cod_sub_divisao",$_GET["cod_sub_divisao"]);
        $obTPessoalCargoSubDivisao->setDado("cod_cargo",Sessao::read("inCodCargo"));
        $obTPessoalCargoSubDivisao->consultarServidoresPorCargo($rsServidores);
    }
}

$obLista = new Table;
$obLista->setHeadFixed(true);
$obLista->setBodyHeight(150);

$obLista->setRecordset($rsServidores);
$obLista->setSummary("Servidores Cadastrados");

$obLista->Head->addCabecalho("Matrícula"    ,20);
$obLista->Head->addCabecalho("Servidor"     ,80);
$obLista->Body->addCampo( 'registro'      , 'E' );
$obLista->Body->addCampo( '[numcgm]-[nom_cgm]'      , 'E' );
$obLista->montaHTML();

echo $obLista->getHtml();

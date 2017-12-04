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

    * Pagina executada no frame oculto para retornar valores para o principal
    * Data de Criação   : 01/09/2014
    * @author Analista: Luciana
    * @author Desenvolvedor: Carolina Schwaab Marçal
    * @package URBEM
    * @subpackage Regra

    * $Id: OCLicencasAlvaras.php 59835 2014-09-15 14:41:31Z carolina $

    * Casos de uso: uc-05.02.12

*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_MPDF;
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicenca.class.php" ); 
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "LicencasAlvaras";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";


$boTransacao = new Transacao();
$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "", "", $boTransacao);

foreach ($rsEntidade->getElementos() as $entidades) {
    $arCodEntidades[]  = $entidades['cod_entidade'];
}

sort($arCodEntidades);
$inCodEntidades = implode(",", $arCodEntidades);
$stDataHoje= date("d/m/Y");
  
$obTCEMLicenca = new TCEMLicenca();
if($_REQUEST["stLicenca"] != ""){
        $stLicenca = explode ( "/" , $_REQUEST["stLicenca"] );
        $obTCEMLicenca->setDado( 'stLicenca' , $stLicenca[0]);
        $obTCEMLicenca->setDado( 'exercicio' , $stLicenca[1]);
}

$obTCEMLicenca->setDado( 'stDataInicial'      , $_REQUEST["stDataInicial"]);
$obTCEMLicenca->setDado( 'stDataFinal'      , $_REQUEST["stDataFinal"]);
$obTCEMLicenca->setDado( 'stSituacao'      , $_REQUEST["stSituacao"]);
$obTCEMLicenca->setDado( 'inInscricaoEconomica' , $_REQUEST["inInscricaoEconomica"]);
$obTCEMLicenca->setDado( 'stTipoLicenca' , $_REQUEST["stTipoLicenca"]);

$obTCEMLicenca->recuperaLicencasAlvaras($rsLicencas);
$arLicencasAlvaras['arLicencas']=$rsLicencas->getElementos();


Sessao::write('arLicencasAlvaras', $arLicencasAlvaras);
Sessao::write('inCodEntidades', $inCodEntidades);
Sessao::write('stDataInicial', $_REQUEST["stDataInicial"] );
Sessao::write('stDataFinal'  , $_REQUEST["stDataFinal"]);

SistemaLegado::LiberaFrames(true,true);
$stCaminho = CAM_GT_CEM_INSTANCIAS."relatorios/OCGeraRelatorioLicencasAlvaras.php";

SistemaLegado::mudaFramePrincipal($stCaminho);

?>

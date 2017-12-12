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
    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Carolina Schwaab Marçal
    * @package URBEM
    * @subpackage Regra

    * $Id: OCDadosCompraDireta.php 60612 2014-11-03 20:09:42Z evandro $

    * Casos de uso: uc-05.02.12

*/
include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_MPDF;
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
include_once (CAM_GP_COM_MAPEAMENTO."TComprasCompraDireta.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "Licencas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$boTransacao = new Transacao();
$obTComprasCompraDireta = new TComprasCompraDireta();
$arCompraDireta = Sessao::read('arCompraDireta');

$obTComprasCompraDireta->setDado( 'cod_compra_direta', $arCompraDireta["cod_compra"]);
$obTComprasCompraDireta->setDado( 'cod_entidade'     , $arCompraDireta["cod_entidade"]);
$obTComprasCompraDireta->setDado( 'cod_modalidade'   , $arCompraDireta["cod_modalidade"]);
$obTComprasCompraDireta->setDado( 'cod_mapa'         , $arCompraDireta["cod_mapa"]);
$obTComprasCompraDireta->setDado( 'cod_mapa'         , $arCompraDireta["cod_mapa"]);
$obTComprasCompraDireta->setDado( 'exercicio_mapa'   , $arCompraDireta['exercicio_mapa']);

$obTComprasCompraDireta->recuperaCompraDiretaAutorizacaoEmpenhoItens($rsDadosCompra);

Sessao::write('arDadosCompra', $rsDadosCompra->getElementos());

SistemaLegado::LiberaFrames(true,true);
$stCaminho = CAM_GP_COM_INSTANCIAS."compraDireta/OCGeraRelatorioDadosCompraDireta.php";

SistemaLegado::mudaFramePrincipal($stCaminho);
?>

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

    * $Id: OCDadosLicitacao.php 60189 2014-10-06 13:31:59Z carolina $

    * Casos de uso: uc-05.02.12

*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_MPDF;
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "Licencas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";



Sessao::read("inCodLicitacao");
Sessao::read("stExercicioLicitacao" );
Sessao::read("inCodEntidade" );
Sessao::read("inCodModalidade");


$boTransacao = new Transacao();

$obTLicitacaoLicitacao = new TLicitacaoLicitacao();



$obTLicitacaoLicitacao->setDado( 'inCodEntidade'      , Sessao::read("inCodEntidade" ));
$obTLicitacaoLicitacao->setDado( 'inCodLicitacao'      , Sessao::read("inCodLicitacao"));
$obTLicitacaoLicitacao->setDado( 'stExercicioLicitacao', Sessao::read("stExercicioLicitacao" ));
$obTLicitacaoLicitacao->setDado( 'inCodModalidade' , Sessao::read("inCodModalidade"));

$obTLicitacaoLicitacao->recuperaDadosLicitacaoItens($rsDadosLicitacao);

Sessao::write('arLicitacao', $rsDadosLicitacao->getElementos());
Sessao::write('inCodLicitacao', $rsDadosLicitacao->getCampo("cod_licitacao"));
Sessao::write('processo', $rsDadosLicitacao->getCampo("processo"));
Sessao::write('entidade', $rsDadosLicitacao->getCampo("entidade"));
Sessao::write('mapa', $rsDadosLicitacao->getCampo("mapa_compra"));
Sessao::write('dt_licitacao', $rsDadosLicitacao->getCampo("dt_licitacao"));
Sessao::write('modalidade', $rsDadosLicitacao->getCampo("modalidade"));
Sessao::write('tipo_objeto', $rsDadosLicitacao->getCampo("tipo_objeto"));
Sessao::write('objeto', $rsDadosLicitacao->getCampo("objeto"));
Sessao::write('dt_homologacao', Sessao::read("dt_homologacao"));



SistemaLegado::LiberaFrames(true,true);
$stCaminho = CAM_GP_LIC_INSTANCIAS."processoLicitatorio/OCGeraRelatorioDadosLicitacao.php";

SistemaLegado::mudaFramePrincipal($stCaminho);

?>

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
 * Data de Criação: 27/10/2005

 * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
 * @author Documentor: Cassiano de Vasconcellos Ferreira

 * @package framework
 * @subpackage componentes

Casos de uso: uc-01.01.00
 */
if (!defined('URBEM_ROOT_PATH')) {
	include '../../../../../../config.php';
}
include URBEM_ROOT_PATH."gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php";
include CAM_GA_ADM_NEGOCIO."RAdministracaoAcao.class.php";
include CAM_GA_ADM_NEGOCIO."RAdministracaoMenu.class.php";

/*
 * Include da classe Request e inicialização do objeto.
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/request/Request.class.php';
$request = new Request($_REQUEST);

Sessao::open();

if (array_key_exists('acao', $_REQUEST)) {
	Sessao::write('acao', $_REQUEST['acao']);
}
$obRAdministracaoAcao = new RAdministracaoAcao;
if (Sessao::read('acao')) {
	$obRAdministracaoAcao->setCodigoAcao(Sessao::read('acao'));
	$obRAdministracaoAcao->consultarAcao();
}
$obRAdministracaoMenu = new RAdministracaoMenu;
$obRAdministracaoMenu->listarGestoesPorOrdem();
$rsGestoes = $obRAdministracaoMenu->getRAdministracaoGestao();
$arGestoes = array(array(VALIDADE_GA, VERSAO_GA),
	array(VALIDADE_GF, VERSAO_GF),
	array(VALIDADE_GP, VERSAO_GP),
	array(VALIDADE_GRH, VERSAO_GRH),
	array(VALIDADE_GT, VERSAO_GT),
	array(VALIDADE_GPC, VERSAO_GPC));
$inI = 0;

if (constant('ENV_TYPE') != 'dev') {
	while (!$rsGestoes->eof()) {
		$obGestao = $rsGestoes->getObjeto();
		if (array_key_exists('cod_gestao_pass', $_REQUEST)) {
			if ($_REQUEST['cod_gestao_pass'] == $obGestao->getCodigoGestao()) {
				$arDataExpiracao = explode('/', $arGestoes[$inI][0]);
				$inTimestamp     = mktime(23, 59, 59, $arDataExpiracao[1], (int) $arDataExpiracao[0], $arDataExpiracao[2]);
				if (time() > $inTimestamp) {
					$stURL = Sessao::getId().'&stGestao='.$obGestao->getNomeGestao().'&dtData='.urlencode($arGestoes[$inI][0]);
					header('location:'.CAM_FW_INSTANCIAS.'index/validade.php?'.$stURL);
					exit();
				}

				if (filter_var($obGestao->getVersao(), FILTER_SANITIZE_NUMBER_INT) != filter_var($arGestoes[$inI][1], FILTER_SANITIZE_NUMBER_INT)) {
					header('location:'.CAM_FW_INSTANCIAS.'index/versao.php?'.Sessao::getId().'&stGestao='.$obGestao->getNomeGestao());
					exit();
				}
			}
		}
		++$inI;
		$rsGestoes->proximo();
	}
}
?>

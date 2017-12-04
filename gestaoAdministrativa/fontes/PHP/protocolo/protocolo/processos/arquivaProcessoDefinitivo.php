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
    * Arquivo de instância para Relatorio.
    * Data de Criação: 20/03/2008

    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-01.06.98

    $Id: arquivaProcessoDefinitivo.php 62506 2015-05-15 16:23:58Z michel $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";

$preview = new PreviewBirt(1,5,8);
$preview->setVersaoBirt('4.4.0');

$codProcesso           = $_REQUEST['codProcesso'] != 'undefined' ? $_REQUEST['codProcesso'] : '';
$anoExercicio          = $_REQUEST['anoExercicio'] != 'undefined' ? $_REQUEST['anoExercicio'] : '';
$historicoArquivamento = $_REQUEST['historicoArquivamento'] != 'undefined' ? $_REQUEST['historicoArquivamento'] : '';
$stEntidadePrincipal   = SistemaLegado::pegaConfiguracao("nom_prefeitura",2,Sessao::getExercicio());

$preview->addParametro('pCodProcesso'           , $codProcesso);
$preview->addParametro('pAnoExercicio'          , $anoExercicio);
$preview->addParametro('pHistoricoArquivamento' , $historicoArquivamento);
$preview->addParametro('pEntidadePrincipal'     , $stEntidadePrincipal);

$msgEnvio = Sessao::read('texto_complementar');
$preview->addParametro('pMsgEnvio', $msgEnvio);

$stDataHoje = dataExtenso(date("Y-m-d"));
$preview->addParametro('pDataHoje', $stDataHoje);

$preview->preview();

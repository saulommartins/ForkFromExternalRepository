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
    * Página oculta do formulário Iniciar Processo Fiscal
    * Data de Criação   : 28/07/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * @ignore

    * Casos de uso:
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once ( CAM_GT_FIS_NEGOCIO."RFISIniciarProcessoFiscal.class.php" );
require_once ( CAM_GT_FIS_VISAO."VFISIniciarProcessoFiscal.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
require_once ( CAM_GT_MON_COMPONENTES."IPopUpCredito.class.php" );
require_once ( CAM_GT_ARR_COMPONENTES."MontaGrupoCredito.class.php" );
require_once ( CAM_GT_FIS_NEGOCIO."RFISProcessoFiscal.class.php" );
require_once ( CAM_GT_FIS_VISAO."VFISProcessoFiscal.class.php" );

//Instanciando a Classe de Controle e de Visao
if ($_REQUEST['stCtrl'] == 'montaForm') {
    //montaForm
    $obController = new RFISIniciarProcessoFiscal;
    $obVisao = new VFISIniciarProcessoFiscal( $obController );
    $stFuncao = $_REQUEST['stCtrl'];

    $retorno = $obVisao->$stFuncao( $_REQUEST );

    print( $retorno );
} else {
    //MostraCredito
    //MostraGrupoCredito
    $obController = new RFISProcessoFiscal;
    $obVisao = new VFISProcessoFiscal($obController);

    $funcao = $_REQUEST['stCtrl'];

    if ($funcao == "IncluirGrupoCredito") {
        $obRARRConfiguracao = new RARRConfiguracao;
        $boEncontrado = $obRARRConfiguracao->listaGrupoCredito( 25, "escrituracao_receita", $_REQUEST["inCodGrupo"] );
        if (!$boEncontrado) {
            $retorno = " document.frm.inCodGrupo.value='';\n";
            $retorno.= " document.getElementById('stGrupo').innerHTML = '&nbsp';\n";
            $retorno .= "alertaAviso('Grupo selecionado inválido (Não é um grupo de escrituração).','form','erro','".Sessao::getId()."');              \n";
            print($retorno);
            exit;
        }
    }

    $retorno = $obVisao->$funcao($_REQUEST);

    print($retorno);
}
?>

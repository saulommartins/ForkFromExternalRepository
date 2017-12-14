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
/*
    * Arquivo de Processamento do Formulario
    * Data de Criação: 10/02/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor André Machado

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterNotasFiscais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

switch ($_REQUEST['stAcao']) {
case 'incluir' :
    $obErro = new Erro;

    if ($_REQUEST['numEmpenho'] == '') {
        $obErro->setDescricao('Campo Número do Empenho Inválido!()');
    } elseif ($_REQUEST['comboLiquidacao'] == '') {
        $obErro->setDescricao('Campo Liquidação Inválido!()');
    } else {
        $arRegistro = array();
        $arEmpenhos = array();
        $arRequest  = array();
        $arRequest  = explode('/', $_REQUEST['numEmpenho']);
        $boIncluir  = true;

        $arEmpenhos = Sessao::read('arEmpenhos');

        if ($_REQUEST['stExercicioEmpenho'] and $arRequest[0] != "") {
            include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $obTEmpenhoEmpenho->setDado( 'dt_emissao'  , $_REQUEST['dtEmissao']           );
            $obTEmpenhoEmpenho->setDado( 'cod_empenho' , $arRequest[0]                    );
            $obTEmpenhoEmpenho->setDado( 'exercicio'   , $_REQUEST['stExercicioEmpenho']  );
            $obTEmpenhoEmpenho->setDado( 'dt_final'    , $_REQUEST['dtEmissao']           );
            $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenho($rsRecordSet, $stFiltro);

            if ($rsRecordSet->getNumLinhas() > 0) {
                if (count( $arEmpenhos ) > 0) {
                    foreach ($arEmpenhos as $key => $array) {
                        $stCod = $array['cod_empenho'];
                        if ($arRequest[0] == $stCod) {
                            $boIncluir = false;
                            $stJs .= "alertaAviso('Empenho já incluso na lista.','form','erro','".Sessao::getId()."');";
                            break;
                        }
                    }
                }

                $arRegistro['cod_entidade' ] = $rsRecordSet->getCampo('cod_entidade');
                $arRegistro['cod_empenho'  ] = $rsRecordSet->getCampo('cod_empenho');
                $arRegistro['data_empenho' ] = $rsRecordSet->getCampo('dt_empenho');
                $arRegistro['nom_cgm'      ] = $rsRecordSet->getCampo('credor');
                $arRegistro['exercicio'    ] = $rsRecordSet->getCampo('exercicio');
                $arRegistro['nuVlAssociado'] = Sessao::read('nuVlassociado');
                $arRegistro['cod_nota'     ] = $_REQUEST['comboLiquidacao'];
                $arEmpenhos[] = $arRegistro ;

                Sessao::write('arEmpenhos', $arEmpenhos);
                $stJs .= "f.cod_entidade.disabled = true; ";
                $stJs .= "f.stNomEntidade.disabled = true; ";
                $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                $stJs .= "f.stEmpenho.value = '';";
                $stJs .= "f.numEmpenho.value = '';";
                $stJs .= "f.numEmpenho.focus();";
                $stJs .= "f.dtEmissao.disabled = true; ";
            } else {
                $stJs .= "alertaAviso('Empenho informado inválido.','form','erro','".Sessao::getId()."');";
            }
        } else {
            if (!$_REQUEST['stExercicioEmpenho']) {
                $stJs .= "alertaAviso('Informe o exercício do empenho.','form','erro','".Sessao::getId()."');";
            }
            if (!$arRequest[0]) {
                $stJs .= "alertaAviso('Informe o número do empenho.','form','erro','".Sessao::getId()."');";
            }
        }

        Sessao::setTrataExcecao(true);

        $arEmpenhos = array();
        $arEmpenhos = Sessao::read('arEmpenhos');

        include_once CAM_GPC_TPB_MAPEAMENTO.'TCEPBNotaFiscal.class.php';

        $obTTCMGONotaFiscal = new TCEPBNotaFiscal;

        $stFiltro  = " WHERE nro_nota           = ".$_REQUEST['inNumNota'];
        $obTTCMGONotaFiscal->recuperaTodos($rsRecordSet, $stFiltro);

        $obTTCMGONotaFiscal->proximoCod($inCodNota);
        $obTTCMGONotaFiscal->setDado('cod_nota'           , $inCodNota);
        $obTTCMGONotaFiscal->setDado('nro_nota'           , $_REQUEST['inNumNota']);
        $obTTCMGONotaFiscal->setDado('nro_serie'          , $_REQUEST['inNumSerie']);
        $obTTCMGONotaFiscal->setDado('cod_nota_liquidacao', $_REQUEST['comboLiquidacao']);
        $obTTCMGONotaFiscal->setDado('cod_entidade'       , $_REQUEST['inCodEntidade']);
        $obTTCMGONotaFiscal->setDado('exercicio'          , Sessao::getExercicio());
        $obTTCMGONotaFiscal->setDado('data_emissao'       , $_REQUEST['dtEmissao']);
        $obErro = $obTTCMGONotaFiscal->inclusao();
    }

    if ($obErro->ocorreu()) {
        $stMensagem = urlencode($obErro->getDescricao());
        $stTipo = 'n_incluir';
        if (strstr($stMensagem, 'Campo') !== false) {
            $stTipo = 'form';
        }
        SistemaLegado::exibeAviso($stMensagem, $stTipo, 'erro');
    } else {
        Sessao::remove('arEmpenhos');
        sistemaLegado::alertaAviso($pgForm."?stAcao=".$_REQUEST['stAcao'], $_REQUEST['inNumNota'] ,"incluir", "aviso", Sessao::getId(), "../");
        Sessao::encerraExcecao();
    }
    break;

case "alterar":
    Sessao::setTrataExcecao ( true );

    $obErro = new Erro;

    include_once(  CAM_GPC_TPB_MAPEAMENTO."TCEPBNotaFiscal.class.php" );

    $obTTCMGONotaFiscal = new TCEPBNotaFiscal;
    $obTTCMGONotaFiscal->setDado( 'cod_nota' , $_REQUEST['inCodNota']              );
    $obTTCMGONotaFiscal->setDado( 'cod_nota'     , $_REQUEST['inCodNota']      );
    $obTTCMGONotaFiscal->setDado( 'data_emissao'    ,$_REQUEST['dtEmissao'] );
    $obTTCMGONotaFiscal->setDado( 'nro_nota'     , $_REQUEST['inNumNota']     );
    $obTTCMGONotaFiscal->setDado( 'exercicio'    , $_REQUEST['stExercicioEmpenho']     );
    $obTTCMGONotaFiscal->setDado( 'nro_serie'    , $_REQUEST['inNumSerie']     );
    $obTTCMGONotaFiscal->setDado( 'cod_entidade' , $_REQUEST['inCodEntidade']  );
    $obTTCMGONotaFiscal->setDado( 'cod_nota_liquidacao'  , $_REQUEST['inCodNotaLiquidacao']);

    $obErro = $obTTCMGONotaFiscal->alteracao();

    if ( $obErro->ocorreu() ) {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    } else {
        sistemaLegado::alertaAviso($pgFilt."?stAcao=".$_REQUEST['stAcao'], $_REQUEST['inNumNota'] ,"incluir","aviso", Sessao::getId(), "../");
    }

    Sessao::encerraExcecao();
    break;

case "excluir":

    Sessao::setTrataExcecao ( true );

    $obErro = new Erro;

    include_once( CAM_GPC_TPB_MAPEAMENTO."TCEPBNotaFiscalEmpenho.class.php" );
    $obTTCMGONotaFiscalEmpenho = new TCEPBNotaFiscalEmpenho;
    $obTTCMGONotaFiscalEmpenho->setDado('cod_nota' , $_REQUEST['inCodNota']);
    $obErro = $obTTCMGONotaFiscalEmpenho->exclusao();

    if ( !$obErro->ocorreu() ) {
        include_once( CAM_GPC_TPB_MAPEAMENTO."TCEPBNotaFiscal.class.php" );
        $obTTCMGONotaFiscal = new TCEPBNotaFiscal;
        $obTTCMGONotaFiscal->setDado('cod_nota' , $_REQUEST['inCodNota'] );
        $obErro = $obTTCMGONotaFiscal->exclusao();
    }

    if ( $obErro->ocorreu() ) {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    } else {
        sistemaLegado::alertaAviso($pgFilt."?stAcao=".$_REQUEST['stAcao'], $_REQUEST['inNumNota'] ,"excluir","aviso", Sessao::getId(), "../");
    }

    Sessao::encerraExcecao();
    break;

}

?>

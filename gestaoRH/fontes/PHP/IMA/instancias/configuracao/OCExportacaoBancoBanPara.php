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
    * Página de oculto do IMA configuração - BanPará
    * Data de Criação: 01/04/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    * Casos de uso: uc-04.08.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ExportacaoBancoBanPara";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function preencherOrgaos($inCodEmpresa)
{
    include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanpara.class.php"      );
    include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanparaOrgao.class.php" );
    include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanparaLocal.class.php" );

    $obTIMAConfiguracaoBanpara      = new TIMAConfiguracaoBanpara();
    $obTIMAConfiguracaoBanparaLocal = new TIMAConfiguracaoBanparaLocal();
    $obTIMAConfiguracaoBanparaOrgao = new TIMAConfiguracaoBanparaOrgao();

    $rsBanparaOrgao = new RecordSet();
    $arSessaoOrgaos = array();

    $obTIMAConfiguracaoBanpara->setDado("vigencia"   , Sessao::read("dtVigencia"));
    $obTIMAConfiguracaoBanpara->recuperaRelacionamento($rsConfiguracaoBanpara);

    if ($rsConfiguracaoBanpara->getNumLinhas() > 0) {
        $inCountOrgaos=1;
        while (!$rsConfiguracaoBanpara->eof()) {
            $rsBanparaLotacao = new RecordSet();
            $rsBanparaLocal   = new RecordSet();

            $arOrgao                             = array();
            $arOrgao['inId']                     = $inCountOrgaos;
            $arOrgao['inCodigoOrgao']            = $rsConfiguracaoBanpara->getCampo('num_orgao_banpara');
            $arOrgao['stDescricao']              = $rsConfiguracaoBanpara->getCampo('descricao');
            $arOrgao['inCodLotacaoSelecionados'] = array();
            $arOrgao['inCodLocalSelecionados']   = array();
            $arOrgao['assinatura']               = serialize($arOrgao);

            $stFiltro  = " WHERE cod_empresa = ".$rsConfiguracaoBanpara->getCampo('cod_empresa');
            $stFiltro .= "   AND timestamp = '".$rsConfiguracaoBanpara->getCampo('timestamp')."'";
            $stFiltro .= "   AND num_orgao_banpara = ".$rsConfiguracaoBanpara->getCampo('num_orgao_banpara');
            $obTIMAConfiguracaoBanparaOrgao->recuperaTodos($rsConfiguracaoBanparaOrgao, $stFiltro);

            if ($rsConfiguracaoBanparaOrgao->getNumLinhas() > 0) {
                while (!$rsConfiguracaoBanparaOrgao->eof()) {
                    $arOrgao['inCodLotacaoSelecionados'][] = $rsConfiguracaoBanparaOrgao->getCampo('cod_orgao');
                    $rsConfiguracaoBanparaOrgao->proximo();
                }
            }

            $stFiltro  = " WHERE cod_empresa = ".$rsConfiguracaoBanpara->getCampo('cod_empresa');
            $stFiltro .= "   AND timestamp = '".$rsConfiguracaoBanpara->getCampo('timestamp')."'";
            $stFiltro .= "   AND num_orgao_banpara = ".$rsConfiguracaoBanpara->getCampo('num_orgao_banpara');
            $obTIMAConfiguracaoBanparaLocal->recuperaTodos($rsConfiguracaoBanparaLocal, $stFiltro);

            if ($rsConfiguracaoBanparaLocal->getNumLinhas() > 0) {
                while (!$rsConfiguracaoBanparaLocal->eof()) {
                    $arOrgao['inCodLocalSelecionados'][] = $rsConfiguracaoBanparaLocal->getCampo('cod_local');
                    $rsConfiguracaoBanparaLocal->proximo();
                }
            }
            $arSessaoOrgaos[] = $arOrgao;

            $rsConfiguracaoBanpara->proximo();
            $inCountOrgaos++;
        }
        Sessao::write('arOrgaos', $arSessaoOrgaos);
        $stJs = montaListaOrgaos(Sessao::read('arOrgaos'));
    }

    return $stJs;
}

function incluirOrgao()
{
    $obErro    = new erro;
    if ( !$obErro->ocorreu() ) {
        $arOrgaos = ( is_array(Sessao::read('arOrgaos')) ) ? Sessao::read('arOrgaos') : array();
        foreach ($arOrgaos as $arOrgao) {
            if( $arOrgao['inCodigoOrgao'] == $_REQUEST['inCodigoOrgao'] &&
                addslashes($arOrgao['assinatura']) != $_REQUEST['stAssinatura']){
                $obErro->setDescricao("Código do Órgão já inserido na lista.");
                break;
            }
            foreach ($arOrgao['inCodLotacaoSelecionados'] as $stCodLotacaoCorrente) {
                if (in_array($stCodLotacaoCorrente, $_REQUEST['inCodLotacaoSelecionados'])) {
                    include_once( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgao.class.php" );
                    $obTOrganogramaOrgao = new TOrganogramaOrgao();
                    $obTOrganogramaOrgao->setDado('data_atual', date('Y-m-d'));
                    $obTOrganogramaOrgao->setDado('cod_orgao', $stCodLotacaoCorrente);
                    $obTOrganogramaOrgao->recuperaUltimaCriacao($rsOrganogramaOrgao);

                    $obErro->setDescricao("Lotação ".$rsOrganogramaOrgao->getCampo("orgao")."-".$rsOrganogramaOrgao->getCampo("descricao")." já configurada para outro orgão.");
                    break;
                }
            }
        }
    }

    if ( !$obErro->ocorreu() ) {
        $arOrgaos                            = Sessao::read('arOrgaos');

        $arOrgao                             = array();
        $arOrgao['inId']                     = count($arOrgaos)+1;
        $arOrgao['inCodOrgao']               = $_REQUEST['inCodOrgao'];
        $arOrgao['inCodigoOrgao']            = $_REQUEST['inCodigoOrgao'];
        $arOrgao['stDescricao']              = $_REQUEST['stDescricao'];
        $arOrgao['inCodLotacaoSelecionados'] = $_REQUEST['inCodLotacaoSelecionados'];
        $arOrgao['inCodLocalSelecionados']   = $_REQUEST['inCodLocalSelecionados'];
        $arOrgao['assinatura']               = serialize($arOrgao);

        $arOrgaos[]                          = $arOrgao;
        Sessao::write('arOrgaos', $arOrgaos);

        $stJs .= montaListaOrgaos(Sessao::read('arOrgaos'));
        $stJs .= "parent.frames[2].limpaFormularioOrgao();";
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function alterarOrgao()
{
    $obErro    = new erro;
    if ( !$obErro->ocorreu() ) {
        $arOrgaos = ( is_array(Sessao::read('arOrgaos')) ) ? Sessao::read('arOrgaos') : array();

        $arrayTemp = array();
        $inCounter = 0;
        foreach ($_REQUEST['inCodLotacaoSelecionados'] as $stSelecionados) {
            if ($stSelecionados != '') {
                $arrayTemp[$inCounter] = $stSelecionados;
                $inCounter++;
            }
        }

        $_REQUEST['inCodLotacaoSelecionados'] = $arrayTemp;

        foreach ($arOrgaos as $arOrgao) {
            if($arOrgao['inCodigoOrgao'] == $_REQUEST['inCodigoOrgao'] &&
               stripslashes($arOrgao['assinatura']) != stripslashes($_REQUEST['stAssinatura']))
            {

                $obErro->setDescricao("Código do Órgão ou assinatura diferente!");
                break;
            }
        }
    }

    if ( !$obErro->ocorreu() ) {
        foreach ($arOrgaos as $arOrgaoKey => $arOrgao) {
            if (stripslashes($arOrgao['assinatura']) == stripslashes($_REQUEST['stAssinatura'])) {
                $arOrgaos                            = Sessao::read('arOrgaos');

                $arOrgao['inCodOrgao']               = $_REQUEST['inCodOrgao'];
                $arOrgao['inCodigoOrgao']            = $_REQUEST['inCodigoOrgao'];
                $arOrgao['stDescricao']              = $_REQUEST['stDescricao'];
                $arOrgao['inCodLotacaoSelecionados'] = $_REQUEST['inCodLotacaoSelecionados'];
                $arOrgao['inCodLocalSelecionados']   = $_REQUEST['inCodLocalSelecionados'];
                $arOrgao['assinatura']               = serialize($arOrgao);

                $arOrgaos[$arOrgaoKey]               = $arOrgao;
                Sessao::write('arOrgaos', $arOrgaos);

                $stJs .= montaListaOrgaos(Sessao::read('arOrgaos'));
                $stJs .= "parent.frames[2].limpaFormularioOrgao();";
                break;
            }
        }
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirOrgao()
{
    $arOrgaos = ( is_array(Sessao::read('arOrgaos')) ? Sessao::read('arOrgaos') : array());
    $arSessaoOrgaos = array();
    foreach ($arOrgaos as $arOrgao) {
        if ($arOrgao['inId'] != $_REQUEST['inId']) {
            $arOrgao['inId'] = sizeof($arSessaoOrgaos)+1;
            $arSessaoOrgaos[] = $arOrgao;
        }
    }
    Sessao::write('arOrgaos', $arSessaoOrgaos);

    return montaListaOrgaos(Sessao::read('arOrgaos'));
}

function preencherAlteraOrgao()
{
    $stJs = "";
    $stVigencia = Sessao::read("dtVigencia");

    if (is_array(Sessao::read('arOrgaos'))) {
        $stJs .= "jQuery('#inCodLotacaoSelecionados').removeOption(/./);    \n";
        $stJs .= "jQuery('#inCodLotacaoDisponiveis').removeOption(/./);     \n";
        $stJs .= atualizarLotacao();

        foreach (Sessao::read('arOrgaos') as $arOrgao) {
            if ($arOrgao['inId'] == $_REQUEST['inId']) {
                $stJs .= "limpaFormularioOrgao();                                                   \n";
                if (isset($arOrgao['inCodOrgao'])) {
                    $stJs .= "jQuery('#inCodOrgao').val('".$arOrgao['inCodOrgao']."');                  \n";
                }
                if (isset($arOrgao['inCodigoOrgao'])) {
                    $stJs .= "jQuery('#inCodigoOrgao').val('".$arOrgao['inCodigoOrgao']."');            \n";
                }
                if (isset($arOrgao['stDescricao'])) {
                    $stJs .= "jQuery('#stDescricao').val('".addslashes($arOrgao['stDescricao'])."');    \n";
                }
                if (isset($arOrgao['assinatura'])) {
                    $stJs .= "jQuery('#stAssinatura').val('".addslashes($arOrgao['assinatura'])."');    \n";
                }

                $stFiltro  = " WHERE dt_inicial <= to_date('".$stVigencia."','dd/mm/yyyy')	\n";
                $stOrdem  = " ORDER BY dt_inicial::date DESC LIMIT 1						\n";
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
                $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
                $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro,$stOrdem);
                list($inDia,$inMes,$inAno) = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));
                $dtCompetencia = $inAno."-".$inMes."-".$inDia;

                include_once(CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgao.class.php");
                $obTOrganogramaOrgao = new TOrganogramaOrgao();
                $obTOrganogramaOrgao->setDado("vigencia",$dtCompetencia);

                $stJs .= "passaItem('document.frm.inCodLotacaoSelecionados','document.frm.inCodLotacaoDisponiveis','tudo',''); \n";
                $stJs .= "jQuery('#inCodLotacaoSelecionados').removeOption(/./);\n";
                if (is_array($arOrgao['inCodLotacaoSelecionados'])) {
                    foreach ($arOrgao['inCodLotacaoSelecionados'] as $inCodLotacaoSelecionado) {
                        $stFiltro = " AND orgao.cod_orgao = ".$inCodLotacaoSelecionado;
                        $obTOrganogramaOrgao->recuperaOrgaos($rsOrgao,$stFiltro);
                        $stDesc = $rsOrgao->getCampo("cod_estrutural")."-".$rsOrgao->getCampo("descricao");
                        $stJs .= "jQuery('#inCodLotacaoSelecionados').addOption('".$inCodLotacaoSelecionado."','$stDesc');   \n";
                        $stJs .= "jQuery('#inCodLotacaoDisponiveis').removeOption('".$inCodLotacaoSelecionado."');           \n";
                    }

                }

                if (is_array($arOrgao['inCodLocalSelecionados'])) {
                    foreach ($arOrgao['inCodLocalSelecionados'] as $inCodLocalSelecionado) {
                        $stJs .= "validaCombo($inCodLocalSelecionado, d.getElementById('inCodLocalDisponiveis'));";
                    }
                    $stJs .= 'passaItem("document.getElementById(\'inCodLocalDisponiveis\')","document.getElementById(\'inCodLocalSelecionados\')","selecao","");';
                }
                $stJs .= "d.getElementById('inCodigoOrgao').focus();";
                $stJs .= "f.btAlterarOrgao.disabled = false;";
                $stJs .= "f.btIncluirOrgao.disabled = true;";
                $stJs .= "LiberaFrames();\n";
            }
        }//

        return $stJs;
    }
}

function montaListaOrgaos($arOrgaos)
{
    global $pgOcul;
    $rsOrgaos = new Recordset;
    $rsOrgaos->preenche($arOrgaos);

    $obLista = new Lista;
    $obLista->setTitulo("Lista de Órgãos");
    $obLista->setRecordSet($rsOrgaos);
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código do Órgão");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Descrição");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "[inCodigoOrgao]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[stDescricao]" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:JavaScript:BloqueiaFrames(true,false);executaFuncaoAjax('preencherAlteraOrgao');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirOrgao');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "d.getElementById('spnOrgaos').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function submeter()
{
    $obErro = new Erro();
    $rsBanparaEmpresa = new RecordSet();

    if (trim($_REQUEST['inCodigoEmpresa']) == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Código da Empresa inválido!");
    }

    if (trim($_REQUEST['dtVigencia']) == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Vigência inválido!");
    }

    if (!$obErro->ocorreu()) {
        include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanparaEmpresa.class.php" );
        $obTIMAConfiguracaoBanparaEmpresa = new TIMAConfiguracaoBanparaEmpresa();
        $stFiltro = " WHERE codigo = ".$_REQUEST['inCodigoEmpresa'];
        $obTIMAConfiguracaoBanparaEmpresa->recuperaTodos($rsConfiguracaoBanparaEmpresa, $stFiltro);

        $tAcao="";
        if (isset($_GET["stAcao"])) {
            $tAcao=$_GET["stAcao"];
        } elseif (isset($_POST["stAcao"])) {
            $tAcao=$_POST["stAcao"];
        }

        switch ($tAcao) {
            case "incluir":
                if ($rsConfiguracaoBanparaEmpresa->getNumLinhas() > 0) {
                    $obErro->setDescricao($obErro->getDescricao()."@Já existe uma empresa configurada com mesmo código e vigência!");
                }
                break;
            case "alterar":
                if ($rsConfiguracaoBanparaEmpresa->getNumLinhas() > 0) {

                    while (!$rsConfiguracaoBanparaEmpresa->eof()) {

                        if ($rsConfiguracaoBanparaEmpresa->getCampo('cod_empresa') != $_REQUEST['inCodEmpresa']) {
                            $obErro->setDescricao($obErro->getDescricao()."@Já existe uma empresa configurada com mesmo código e vigência!");
                        }

                        if (!$obErro->ocorreu()) {
                            $rsConfiguracaoBanparaEmpresa->proximo();
                        } else {
                            break;
                        }
                    }
                }
                break;
        }
    }

    if ( !$obErro->ocorreu() ) {
        if (!is_array(Sessao::read('arOrgaos')) || count(Sessao::read('arOrgaos')) < 1) {
            $obErro->setDescricao($obErro->getDescricao()."@Ao menos um Órgão deve ser informado na listagem!");
        }
    }

    if ( $obErro->ocorreu() ) {
        $stJs = "alertaAviso('".$obErro->getDescricao()."','form','aviso','".Sessao::getId()."');";
    } else {
        $stJs = "parent.frames[2].Salvar(); BloqueiaFrames(true,false);\n";
    }

    return $stJs;
}

function limparForm()
{
    Sessao::write('arOrgaos', '');

    return $stJs;
}

function atualizarLotacao()
{
    include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"			);
    include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php"	    );
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );

    $obTPessoalContratoServidorOrgao 	  = new TPessoalContratoServidorOrgao();
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();

    $obTPessoalContratoServidorOrgao->recuperaDataPrimeiroCadastro($rsContratoServidorOrgao);

        $stJs="";
        $stAcao="";
        if (isset($_GET['stAcao'])) {
          $stAcao=$_GET['stAcao'];
        } elseif (isset($_POST['stAcao'])) {
          $stAcao=$_POST['stAcao'];
        }

    $arSelectMultiploLotacao = Sessao::read("arSelectMultiploLotacao");
    $obErro = new Erro();

    if (isset($_GET["dtVigencia"])&&trim($_GET["dtVigencia"])!="") {
        $stVigencia = $_GET["dtVigencia"];
        Sessao::write("dtVigencia",$stVigencia);
    } else {
        $stVigencia = Sessao::read("dtVigencia");
    }

    $stFiltro  = " WHERE dt_inicial <= to_date('".$stVigencia."','dd/mm/yyyy')	\n";
    $stOrdem  = " ORDER BY dt_inicial::date DESC LIMIT 1						\n";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro,$stOrdem);

    $stFiltro  = " WHERE dt_inicial <= to_date('".$rsContratoServidorOrgao->getCampo("dt_cadastro")."','dd/mm/yyyy')	\n";
    $stOrdem  = " ORDER BY dt_inicial::date DESC LIMIT 1																\n";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacaoInicioOrganograma,$stFiltro,$stOrdem);

    if ($rsPeriodoMovimentacao->getNumLinhas() != -1) {
        $obTPessoalContratoServidorOrgao->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
        $obTPessoalContratoServidorOrgao->recuperaOrganogramaVigente($rsOrganograma);

        if ($rsOrganograma->getNumLinhas() != -1) {
            $inCodOrganograma = $rsOrganograma->getCampo("cod_organograma");
        } else {
            $obErro->setDescricao("Não existem servidores vinculados a nenhum orgão em ".$stVigencia.". A vigência informada deve ser a partir de ".$rsPeriodoMovimentacaoInicioOrganograma->getCampo("dt_inicial").".");
            $obTPessoalContratoServidorOrgao->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacaoInicioOrganograma->getCampo("cod_periodo_movimentacao"));
            $obTPessoalContratoServidorOrgao->recuperaOrganogramaVigente($rsOrganograma);
            $inCodOrganograma = $rsOrganograma->getCampo("cod_organograma");
        }

        list($inDia,$inMes,$inAno)= explode("/", $rsPeriodoMovimentacao->getCampo("dt_final"));
        $stDataFinal = $inAno."-".$inMes."-".$inDia;

        // Atualizando o componente na tela!
        if (is_array($arSelectMultiploLotacao) && !empty($arSelectMultiploLotacao)) {
            foreach ($arSelectMultiploLotacao as $obSelectMultiploLotacao) {
                $stJs .= $obSelectMultiploLotacao->atualizarLotacao($stDataFinal, $inCodOrganograma);
            }
        }
    } else {
        $obErro->setDescricao("Vigência anterior ao primeiro período de movimentação. A vigência informada deve ser a partir de ".$rsPeriodoMovimentacaoInicioOrganograma->getCampo("dt_inicial").".");
    }

    if ($obErro->ocorreu() && $rsPeriodoMovimentacao->getNumLinhas()>0) {
        $obTPessoalContratoServidorOrgao->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacaoInicioOrganograma->getCampo("cod_periodo_movimentacao"));
        $obTPessoalContratoServidorOrgao->recuperaOrganogramaVigente($rsOrganograma);
        $inCodOrganograma = $rsOrganograma->getCampo("cod_organograma");

        list($inDia,$inMes,$inAno)= explode("/", $rsContratoServidorOrgao->getCampo("dt_cadastro"));
        $stDataFinal = $inAno."-".$inMes."-".$inDia;

        // Atualizando o componente na tela!
        Sessao::write('dtVigencia',$rsPeriodoMovimentacaoInicioOrganograma->getCampo("dt_inicial"));
        $stJs .= " jQuery('#dtVigencia').val('".$rsPeriodoMovimentacaoInicioOrganograma->getCampo("dt_inicial")."'); \n";
        if (is_array($arSelectMultiploLotacao) && !empty($arSelectMultiploLotacao)) {
            foreach ($arSelectMultiploLotacao as $obSelectMultiploLotacao) {
                $stJs .= $obSelectMultiploLotacao->atualizarLotacao($stDataFinal, $inCodOrganograma);
            }
        }

        $stJs .= " alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."'); 	\n";
    }
    else if($rsPeriodoMovimentacao->getNumLinhas()<0){
        $stJs .= " alertaAviso('Período de movimentação não existente.','form','erro','".Sessao::getId()."'); 	\n";
    }

    return $stJs;
}

$boExecutaFrameOculto = false;
switch ($_REQUEST['stCtrl']) {
    case "incluirOrgao":
        $stJs = incluirOrgao();
        $boExecutaFrameOculto = true;
        break;
    case "excluirOrgao":
        $stJs = excluirOrgao();
        break;
    case "alterarOrgao":
        $stJs = alterarOrgao();
        $boExecutaFrameOculto = true;
        break;
    case "preencherOrgaos":
        $stJs = preencherOrgaos($request->get('inCodEmpresa'));
        break;
    case "preencherAlteraOrgao":
        $stJs = preencherAlteraOrgao();
        break;
    case "submeter":
        $stJs = submeter();
        break;
    case "limparForm":
        $stJs = limparForm();
        break;
    case "atualizarLotacao":
        $stJs = atualizarLotacao();
        break;
}

if ($stJs) {
   if($boExecutaFrameOculto)
        SistemaLegado::executaFrameOculto($stJs);
   else
        echo $stJs;
}

?>

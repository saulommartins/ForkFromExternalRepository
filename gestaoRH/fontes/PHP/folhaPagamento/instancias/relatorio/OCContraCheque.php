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
    * Página de Oculto do Contra-Cheque
    * Data de Criação: 29/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: alex $
    $Date: 2008-03-12 16:32:18 -0300 (Qua, 12 Mar 2008) $

    * Casos de uso: uc-04.05.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                       );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."ISelectRegSubCarEsp.class.php"                                   );
include_once( CAM_GRH_PES_COMPONENTES."ISelectPadrao.class.php"                                         );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                                );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma = "ContraCheque";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

###########################LIMPA SPANS#####################################

function limparSpans()
{
    #Cadastro
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '';\n";

    return $stJs;
}

###########################ATIVOS / APOSENTADOS#####################################

function gerarSpanAtivosAposentados()
{
    $stSituacao = $_GET["stSituacao"];

    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setMatricula();
    $obIFiltroComponentes->setCGMMatricula();
    $obIFiltroComponentes->setLocal();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setGrupoLocal();
    $obIFiltroComponentes->setGrupoLotacao();
    $obIFiltroComponentes->setTodos();

    $obFormulario = new Formulario();

    switch ($stSituacao) {
        case 'todos':
            $obFormulario->addTitulo("Todos");
            break;
        case 'ativos':
                $obFormulario->addTitulo("Ativos");
                $obIFiltroComponentes->setAtivos();
            break;
        case 'aposentados':
                $obFormulario->addTitulo("Aposentados");
                $obIFiltroComponentes->setAposentados();
            break;
        case 'rescindidos':
                $obFormulario->addTitulo("Rescindidos");
                $obIFiltroComponentes->setRescisao();
            break;
    }

    $obIFiltroComponentes->geraFormulario($obFormulario);
    $stEval = $obFormulario->getInnerJavaScript();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";
    $stJs .= "f.hdnTipoFiltroExtra.value = '$stEval';\n";

    return $stJs;

}

###########################PENSIONISTAS#####################################

function gerarSpanPensionistas()
{
    $stSituacao = $_GET["stSituacao"];

    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setMatriculaPensionista();
    $obIFiltroComponentes->setCGMMatriculaPensionista();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setGrupoLotacao();

    $obFormulario = new Formulario();
    $obFormulario->addTitulo("Pensionistas");
    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";
    //$obIFiltroComponentes->getOnload($stJs);
    return $stJs;
}

function gerarCampoCadastro(&$obFormulario)
{
    $obRdoTodos = new Radio;
    $obRdoTodos->setName( "stCadastro" );
    $obRdoTodos->setTitle( "Selecione o cadastro que deve ser listado: ativos, pensionistas, aposentador, estagiários ou todos." );
    $obRdoTodos->setRotulo( "Cadastro" );
    $obRdoTodos->setLabel( "Todos" );
    $obRdoTodos->setValue( "todos" );
    $obRdoTodos->setChecked( true );

    $obRdoAtivos = new Radio;
    $obRdoAtivos->setName( "stCadastro" );
    $obRdoAtivos->setTitle( "Selecione o cadastro que deve ser listado: ativos, pensionistas, aposentador, estagiários ou todos." );
    $obRdoAtivos->setRotulo( "Cadastro" );
    $obRdoAtivos->setLabel( "Ativos" );
    $obRdoAtivos->setValue( "ativos" );

    $obRdoAposentados = new Radio;
    $obRdoAposentados->setName( "stCadastro" );
    $obRdoAposentados->setTitle( "Selecione o cadastro que deve ser listado: ativos, pensionistas, aposentador, estagiários ou todos." );
    $obRdoAposentados->setRotulo( "Cadastro" );
    $obRdoAposentados->setLabel( "Aposentados" );
    $obRdoAposentados->setValue( "aposentados" );

    $obRdoPensionistas = new Radio;
    $obRdoPensionistas->setName( "stCadastro" );
    $obRdoPensionistas->setTitle( "Selecione o cadastro que deve ser listado: ativos, pensionistas, aposentador, estagiários ou todos." );
    $obRdoPensionistas->setRotulo( "Cadastro" );
    $obRdoPensionistas->setLabel( "Pensionistas" );
    $obRdoPensionistas->setValue( "pensionistas" );

    $obFormulario->agrupaComponentes(array($obRdoTodos,$obRdoAtivos,$obRdoAposentados,$obRdoPensionistas));
}

function gerarSpanMensagem()
{
    $stHtml = "";
    $stJs = "";    
    if ( isset($_GET['boMensagemAniversariante']) ) {
        if($_GET['boMensagemAniversariante']=='off'){
            include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php");
            $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao();
            $obRFolhaPagamentoConfiguracao->consultar();
    
            $obHdnMensagem = new Hidden();
            $obHdnMensagem->setName("stMensagemAniversario");
            $obHdnMensagem->setValue($obRFolhaPagamentoConfiguracao->getMensagemAniversariantes());
    
            $obLblMensagem = new Label();
            $obLblMensagem->setRotulo("Mensagem");
            $obLblMensagem->setValue($obRFolhaPagamentoConfiguracao->getMensagemAniversariantes());
    
            $obFormulario = new Formulario();
            $obFormulario->addHidden($obHdnMensagem);
            $obFormulario->addComponente($obLblMensagem);
            $obFormulario->montaInnerHTML();
            $stHtml = $obFormulario->getHTML();
            
            $value = 'on';
        }else
           $value = 'off';
        
        $stJs .= "d.getElementById('boMensagemAniversariante').value = '$value';  \n";
    }
    $stJs .= "d.getElementById('spnMensagem').innerHTML = '$stHtml';  \n";

    return $stJs;
}

function limparFormulario()
{
    ;

    $stJs  = "d.frm.stTipoFiltro.value = 'contrato'; 														\n";
    $stJs .= "d.frm.inCodConfiguracao.value = 1;															\n";
    $stJs .= "d.frm.stConfiguracao.value = 1; 																\n";
    $stJs .= "d.frm.boMensagemAniversariante.checked = false;												\n";
    $stJs .= "d.getElementById('spnMensagem').innerHTML = ''; 												\n";
    $stJs .= "d.frm.stOrdenacao[0].checked = true;															\n";
    $stJs .= "d.frm.inContratoReemissao.value = ''; 														\n";
    $stJs .= "d.frm.stMensagem.value = '';																	\n";
    $stJs .= "d.getElementById('spnTipoFolha').innerHTML = ''; 												\n";
        $stJs .= "montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');";

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "gerarSpanMensagem":
        $stJs .= gerarSpanMensagem();
    break;
    case "gerarSpanAtivosAposentados":
        $arDados['inAno']  = $_REQUEST['inAno'];
        $arDados['inCodMes'] = $_REQUEST['inCodMes'];
        Sessao::write('Competencia', $arDados);
        $stJs .= gerarSpanAtivosAposentados();
        break;
    case "gerarSpanPensionistas":
        $arDados['inAno']  = $_REQUEST['inAno'];
        $arDados['inCodMes'] = $_REQUEST['inCodMes'];
        Sessao::write('Competencia', $arDados);
        $stJs .= gerarSpanPensionistas();
        break;
    case "limparSpans":
        $stJs .= limparSpans();
        break;
    case "limparFormulario":
        $stJs = limparFormulario();
        break;
    case "atualizaCompetencia":
        $arDados['inAno']  = $_REQUEST['inAno'];
        $arDados['inCodMes'] = $_REQUEST['inCodMes'];
        Sessao::write('Competencia', $arDados);
        break;
}

if ($stJs) {
   echo $stJs;
}
?>

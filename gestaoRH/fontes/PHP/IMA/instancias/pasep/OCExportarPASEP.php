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
    * Arquivo de Filtro
    * Data de Criação: 29/10/2007

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.08.21

    $Id: OCExportarPASEP.php 59612 2014-09-02 12:00:51Z gelson $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ExportarPASEP";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

function gerarSpanExportar($obFormulario)
{
    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setMatricula();
    $obIFiltroComponentes->setCGMMatricula();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setLocal();
    $obIFiltroComponentes->setAtributoServidor();
    $obIFiltroComponentes->setTodos();

    $obDtCredito = new Data();
    $obDtCredito->setRotulo("Data do Crédito");
    $obDtCredito->setName("dtCredito");
    $obDtCredito->setTitle("Informe a data (provável) do crédito na folha de pagamento.");
    $obDtCredito->setNull(false);

    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario = addGeracaoArquivo($obFormulario);
    $obFormulario->addComponente($obDtCredito);

    return $obFormulario;
}

function addGeracaoArquivo($obFormulario)
{
    $obDtGeracaoArquivo = new Data();
    $obDtGeracaoArquivo->setRotulo("Data da Geração do Arquivo");
    $obDtGeracaoArquivo->setName("dtGeracaoArquivo");
    $obDtGeracaoArquivo->setTitle("Informe a data da geração do arquivo.");
    $obDtGeracaoArquivo->setNull(false);

    $obFormulario->addComponente($obDtGeracaoArquivo);

    return $obFormulario;
}

function gerarSpanImportar($obFormulario)
{
    $obFilArquivo = new FileBox;
    $obFilArquivo->setRotulo        ( "Caminho" );
    $obFilArquivo->setName          ( "stCaminho" );
    $obFilArquivo->setId            ( "stCaminho" );
    $obFilArquivo->setSize          ( 40          );
    $obFilArquivo->setMaxLength     ( 100         );
    $obFilArquivo->setTitle("Informe o caminho do arquivo à importar para verificação dos erros.");
    $obFilArquivo->setNull(false);

    $obFormulario->addComponente ( $obFilArquivo      );

    return $obFormulario;
}

function gerarSpanFolhaPagamento($obFormulario)
{
    $obCmbTipoFolha = new Select;
    $obCmbTipoFolha->setName                  ( "inCodTipoFolha"                     );
    $obCmbTipoFolha->setValue                 ( 1                                    );
    $obCmbTipoFolha->setRotulo                ( "Pagar em:"                          );
    $obCmbTipoFolha->setTitle                 ( "Em qual folha o benefício deve ser pago." );
    $obCmbTipoFolha->setCampoId               ( "[cod_tipo]"                         );
    $obCmbTipoFolha->addOption                ( "1", "Folha Salário"                 );
    $obCmbTipoFolha->addOption                ( "3", "Folha Complementar"            );
    $obCmbTipoFolha->setStyle                 ( "width: 200px"                       );

    $obFormulario->addComponente ( $obCmbTipoFolha );

    return $obFormulario;
}

function montarSpanFiltro()
{
    $obFormulario = new Formulario();
    switch ($_GET["inEtapaProcessamento"]) {
        case 3:
            $obFormulario = gerarSpanFolhaPagamento($obFormulario);
        case 2:
        case 5:
        case 6:
            $obFormulario = gerarSpanImportar($obFormulario);
            break;
        case 4:
            $obFormulario = addGeracaoArquivo($obFormulario);
            break;
        default:
            $obFormulario = gerarSpanExportar($obFormulario);
            break;
    }
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stHtml = $obFormulario->getHTML();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stJs  = "$('spnEtapaProcessamento').innerHTML = '".$stHtml."';\n";
    $stJs .= "$('hdnEtapaProcessamento').value = '".$stEval."';\n";

    return $stJs;
}

function submeter()
{
    $obErro = new Erro();
    $stJs   = "";

    if ((int) $_REQUEST["inEtapaProcessamento"] == 3) {
        switch ((int) $_REQUEST["inCodTipoFolha"]) {
            case 1: // Folha Salario
                include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php" );
                $obTFolhaSituacao = new TFolhaPagamentoFolhaSituacao;
                $obTFolhaSituacao->recuperaUltimaFolhaSituacao($rsFolhaSalario);

                if (trim($rsFolhaSalario->getCampo("situacao")) != "a") {
                    $obErro->setDescricao("Folha Salário está fechada. Para lançamento do pasep na competência, deve reabrí-la.");
                }
                break;
            case 3: // Folha Complementar
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementarSituacao.class.php");
                $obTFolhaPagamentoComplementarSituacao = new TFolhaPagamentoComplementarSituacao;
                $obTFolhaPagamentoComplementarSituacao->recuperaUltimaFolhaComplementarSituacao($rsFolhaComplementar);

                if (trim($rsFolhaComplementar->getCampo("situacao")) != "a") {
                    $obErro->setDescricao("Para o lançamento do PASEP é necessário que exista uma folha complementar aberta.");
                }
                break;
        }
    }

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','aviso','".Sessao::getId()."');";
    } else {
        $stJs .= "BloqueiaFrames(true,false);	\n";
        $stJs .= "parent.frames[2].Salvar();    \n";
    }

    return $stJs;
}

$stJs = "";
switch ($_GET['stCtrl']) {
    case "montarSpanFiltro":
        $stJs = montarSpanFiltro();
        break;
    case "submeter":
        $stJs = submeter();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>

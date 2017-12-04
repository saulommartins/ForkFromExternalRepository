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
    * Formulario para Inclusão de Terminais - Tesouraria
    * Data de Criação   : 06/09/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Cleisson da Silva Barboza
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 31060 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_TES_NEGOCIO."RTesourariaTerminal.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterTerminal";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FMConsultarTerminal.php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once($pgJs);

$stAcao = $request->get('stAcao');
if (empty($stAcao)) {
    $stAcao = "consultar";
}

$arFiltro = Sessao::read('filtro');

if (count($arFiltro) > 0) {
    $stFiltro = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                $stFiltro .= "&".$stCampo2."=".@urlencode($stValor2);
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

$stCodVerificador    = $_GET['stCodVerificador'];
$inNumTerminal       = $_GET['inNumTerminal'];
$stSituacao          = $_GET['stSituacao'];
$inCgm               = $_GET['inCgm'];
$stTimestampTerminal = $_GET['stTimestampTerminal'];

$obRTesourariaTerminal = new RTesourariaTerminal();
$obRTesourariaTerminal->setCodTerminal      ($inNumTerminal);
$obRTesourariaTerminal->setTimestampTerminal($stTimestampTerminal);
$obRTesourariaTerminal->setCodVerificador   ($stCodVerificador);
$obRTesourariaTerminal->consultar();
$arUsuario = array();
$inCount = 0;
$arRTesourariaUsuarioTerminal = $obRTesourariaTerminal->getUsuarioTerminal();
foreach ($arRTesourariaUsuarioTerminal as $obRTesourariaUsuarioTerminal) {
    $arUsuario[$inCount]['id_usuario']  = $inCount;
    $arUsuario[$inCount]['numcgm']      = $obRTesourariaUsuarioTerminal->obRCGM->getNumCGM();
    $obRTesourariaUsuarioTerminal->obRCGM->consultar($rsCGM);
    $arUsuario[$inCount]['nom_cgm']     = $obRTesourariaUsuarioTerminal->obRCGM->getNomCGM();
    $arUsuario[$inCount]['responsavel'] = $obRTesourariaUsuarioTerminal->getResponsavel();
    $inCount++;
}

Sessao::write('arUsuario', $arUsuario);
Sessao::write('situacao', $stSituacao);

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl");
$obHdnCtrl->setValue($_REQUEST["stCtrl"]);

$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

// DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

// DEFINE OBJETOS DO FORMULARIO - INCLUIR
if ($stAcao=="consultar") {
   //Define Objeto Label para Nr. do Terminal
   $obLblTerminalLogado = new Label;
   $obLblTerminalLogado->setName  ("inNumTerminalLogado");
   $obLblTerminalLogado->setValue ($_REQUEST['inNumTerminal']);
   $obLblTerminalLogado->setRotulo("Nr. Terminal Logado");

   //Define Objeto Label para Nr. do Terminal
   $obLblResponsavelTerminal = new Label;
   $obLblResponsavelTerminal->setName  ("stResponsavelTerminal");
   $obLblResponsavelTerminal->setValue ($stResponsavelTerminal);
   $obLblResponsavelTerminal->setRotulo("Responsável");
}

//Define Objeto Label para Nr. do Terminal
$obLblNroTerminal = new Label;
$obLblNroTerminal->setName  ("inNumTerminal");
$obLblNroTerminal->setValue ($inNumTerminal);
$obLblNroTerminal->setRotulo("Nr. Terminal");

$obLblCodVerificador = new Label;
$obLblCodVerificador->setName  ("stCodVerificador");
$obLblCodVerificador->setValue ($stCodVerificador );
$obLblCodVerificador->setRotulo("Código Verificador");

$obLblSituacao = new Label;
$obLblSituacao->setName  ("stSituacao");
$obLblSituacao->setValue ($stSituacao);
$obLblSituacao->setRotulo("Situação");

$arRecordSet = Sessao::read('arUsuario');

for ($x = 0; $x < count($arRecordSet); $x++) {
    if ($arRecordSet[$x]['responsavel'] == 't') {
        $arRecordSet[$x]['responsavel'] = "Sim";
    } else {
        $arRecordSet[$x]['responsavel'] = "Não";
    }
}

$rsLista = new RecordSet;
$rsLista->preenche($arRecordSet);
$obLista = new Lista;
$obLista->setMostraPaginacao(false);
$obLista->setRecordSet($rsLista);
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome");
$obLista->ultimoCabecalho->setWidth(60);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Responsável");
$obLista->ultimoCabecalho->setWidth(27);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo("nom_cgm");
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo("responsavel");
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDado();

$obLista->montaHTML();
$stHTML = $obLista->getHTML();
$stHTML = str_replace("\n" ,"" ,$stHTML);
$stHTML = str_replace(chr(13) ,"<br>" ,$stHTML);
$stHTML = str_replace("  " ,"" ,$stHTML);
$stHTML = str_replace("'","\\'",$stHTML);

// Define objeto span para lista de usuários
$obSpnLista = new Span();
$obSpnLista->setId("spnLista");
$obSpnLista->setValue($stHTML);

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
$obVoltar = new Button;
$obVoltar->setName ("Voltar");
$obVoltar->setValue("Voltar");
$obVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ($obForm);
$obFormulario->addHidden    ($obHdnCtrl);
$obFormulario->addHidden    ($obHdnAcao);
if ($stAcao=="consultar") {
    $obFormulario->addTitulo    ("Dados do Terminal Logado");
    $obFormulario->addComponente($obLblTerminalLogado);
}
$obFormulario->addTitulo    ("Dados para Terminal e Usuários");
$obFormulario->addComponente($obLblNroTerminal);
$obFormulario->addComponente($obLblCodVerificador);
$obFormulario->addComponente($obLblSituacao);
$obFormulario->addTitulo    ("Usuários de Terminal de Caixa");
$obFormulario->addSpan      ($obSpnLista);
$obFormulario->defineBarra  (array($obVoltar), "left", "");

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

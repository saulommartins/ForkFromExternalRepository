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
    * Página Oculta de Inclusao/Alteracao de PAO
    * Data de Criação   : 14/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Id: OCPAO.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoProjetoAtividade.class.php";

//Define o nome dos arquivos PHP
$stProjeto = "PAO";
$pgFilt = "FL".$stProjeto.".php";
$pgList = "LS".$stProjeto.".php";
$pgForm = "FM".$stProjeto.".php";
$pgProc = "PR".$stProjeto.".php";
$pgOcul = "OC".$stProjeto.".php";
$pgJS   = "JS".$stProjeto.".js";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

$obROrcamentoProjetoAtividade = new ROrcamentoProjetoAtividade;

// Acoes por pagina
$stJs = "";
switch ($stCtrl) {

case "tipoProjeto":
    if ($_GET['inNumeroProjeto'] != "") {
        $arTipo = explode(',', $_GET['inTipoPAO']);

        $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
        $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->consultarConfiguracao();

        $inPosicao = $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->getNumPAOPosicaoDigitoID();

        $boMesmoTipo = false;
        foreach ($arTipo as $inCodTipo) {
            if ($_GET['inNumeroProjeto']{$inPosicao-1} == $inCodTipo) {
                $boMesmoTipo = true;
            }
        }

        // Caso não seja do mesmo tipo, apaga o valor do campo Código e coloca o foco novamente no campo para que seja inserido o novo
        // valor para o mesmo, além de lançar a mensagem de aviso.
        if (!$boMesmoTipo) {
            $stJs .= "jq('#inNumeroProjeto').val('').focus(); \n";
            $stJs .= "alertaAviso('O Código ".$_GET['inNumeroProjeto']." não pertence ao tipo selecionado!','form','erro','".Sessao::getId()."');\n";
        }
    }

    // É liberado o frame principal, caso tenha sido bloqueado por algum evento antes de ser chamado esse case.
    $stJs .= "LiberaFrames();";
    break;

case "montaSpan":
    if ($_GET['inTipoPAO'] != "") {
        $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->setExercicio( Sessao::getExercicio() );
        $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->consultarConfiguracao();
        $inPosicao = $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->getNumPAOPosicaoDigitoID();
        $stDigitoProjeto   = $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->getNumPAODigitosIDProjeto();
        $stDigitoAtividade = $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->getNumPAODigitosIDAtividade();
        $stDigitoOrperacao = $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->getNumPAODigitosIDOperEspeciais();

        $stMascara = $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->getMascDespesa();
        $arMarcara = preg_split( "/[^a-zA-Z0-9]/", $stMascara );
        // Grupo X;
        $stMascara = $arMarcara[5];

        $obROrcamentoProjetoAtividade->setExercicio( Sessao::getExercicio() );
        if ($stAcao == 'incluir') {
            $inTipoPAO = $_GET['inTipoPAO'];
            $obROrcamentoProjetoAtividade->setCodTipo  ( $inTipoPAO );
            $obROrcamentoProjetoAtividade->buscaProximoCodPorTipo( $inNumPAO );
            $stOrder = " ORDER BY exercicio, num_pao DESC";
            $obROrcamentoProjetoAtividade->listarPorTipo( $rsLista, $stOrder );

            $arProjetoAtividade = $rsLista->arElementos;
            Sessao::write('arProjetoAtividade', $arProjetoAtividade);

            $obLista = new Lista;

            if ($inNumPAO) {
                $arTipoPAO = explode( ',', $inTipoPAO );
            } else {
                if ( settype($inNumPAO, "integer") == 1 ) {
                    $inNumPAO = $arTipoPAO[0]."001";
                }
            }

            foreach ($arTipoPAO as $inTipo) {
                if ( in_array( $inTipo, explode( ',', $stDigitoProjeto ) ) ) {
                    $stTipoPAO = 'Projetos';
                }
                if ( in_array( $inTipo, explode( ',', $stDigitoAtividade ) ) ) {
                    $stTipoPAO = 'Atividades';
                }
                if ( in_array( $inTipo, explode( ',', $stDigitoOrperacao ) ) ) {
                    $stTipoPAO = 'Operações Especiais';
                }
            }

            $obLista->setRecordSet( $rsLista );
            $obLista->setTitulo( $stTipoPAO.$stIncluso = ($stTipoPAO == "Projetos" ? " já Inclusos" : " já Inclusas") );
            $obLista->setMostraPaginacao(false);
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Código");
            $obLista->ultimoCabecalho->setWidth( 15 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Descrição");
            $obLista->ultimoCabecalho->setWidth( 75 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "num_pao" );
            $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "nom_pao" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();

            $obLista->montaHTML();

            $stHTMLLista = $obLista->getHTML();
            $stHTMLLista = str_replace( "\n" ,"" ,$stHTMLLista );
            $stHTMLLista = str_replace( chr(13) ,"<br>" ,$stHTMLLista );
            $stHTMLLista = str_replace( "  " ,"" ,$stHTMLLista );
            $stHTMLLista = str_replace( "'","\\'",$stHTMLLista );

        }
        if ($stAcao == 'alterar') {
            $obROrcamentoProjetoAtividade = new ROrcamentoProjetoAtividade;
            $obROrcamentoProjetoAtividade->setNumeroProjeto( $_GET['inNumeroProjeto'] );
            $inNumeroProjeto = $_GET['inNumeroProjeto']."/".Sessao::getExercicio();

            $obROrcamentoProjetoAtividade->consultar( $rsProjeto );

            $stNome         = $rsProjeto->getCampo('nom_pao'     );
            $stDetalhamento = $rsProjeto->getCampo('detalhamento');
        }

        if ($stAcao == 'incluir') {
            $obTxtCodProjeto = new TextBox;
            $obTxtCodProjeto->setName              ('inNumeroProjeto'         );
            $obTxtCodProjeto->setId                ('inNumeroProjeto'         );
            $obTxtCodProjeto->setRotulo            ('Código'                  );
            $obTxtCodProjeto->setSize              (strlen($stMascara)        );
            $obTxtCodProjeto->setMaxLength         (strlen($stMascara)        );
            $obTxtCodProjeto->setNull              (false                     );
            $obTxtCodProjeto->setInteiro           (true                      );
            $obTxtCodProjeto->setTitle             ('Informe o código do PAO.');
            $obTxtCodProjeto->obEvento->setOnChange('BloqueiaFrames(true, false); preencheComZeros(\''.$stMascara.'\', this, \'E\'); montaParametrosGET(\'tipoProjeto\');');
            //$obTxtCodProjeto->obEvento->setOnChange(' ');
        } else {
            $obHdnCodProjeto = new Hidden;
            $obHdnCodProjeto->setName ('inNumeroProjeto');
            $obHdnCodProjeto->setValue($inNumeroProjeto );

            $obLblCodProjeto = new Label;
            $obLblCodProjeto->setRotulo('Código'        );
            $obLblCodProjeto->setName  ('lblCodProjeto' );
            $obLblCodProjeto->setValue ($inNumeroProjeto);
        }

        $obTxtTipoProjeto = new TextBox;
        $obTxtTipoProjeto->setName     ( "stTipoProjeto" );
        $obTxtTipoProjeto->setValue    ( $stTipoProjeto  );
        $obTxtTipoProjeto->setId       ( "stTipoProjeto" );
        $obTxtTipoProjeto->setSize     ( 40              );
        $obTxtTipoProjeto->setMaxLength( 40              );
        $obTxtTipoProjeto->setNull     ( false           );
        $obTxtTipoProjeto->setReadOnly ( true            );
        $obTxtTipoProjeto->setTabIndex ( 0               );

        $obTxtDescProjeto = new TextBox;
        $obTxtDescProjeto->setName     ( "stNome"           );
        $obTxtDescProjeto->setId       ( "stNome"           );
        $obTxtDescProjeto->setValue    ( $stNome            );
        $obTxtDescProjeto->setRotulo   ( "Descrição"        );
        $obTxtDescProjeto->setTitle    ( "Informe a descrição do PAO." );
        $obTxtDescProjeto->setSize     ( 80                 );
        $obTxtDescProjeto->setMaxLength( 80                 );
        $obTxtDescProjeto->setNull     ( false              );

        $obTxtDetalhamento = new TextArea;
        $obTxtDetalhamento->setName     ( "stDetalhamento"   );
        $obTxtDetalhamento->setId       ( "stDetalhamento"   );
        $obTxtDetalhamento->setValue    ( $stDetalhamento    );
        $obTxtDetalhamento->setRotulo   ( "Detalhamento"     );
        $obTxtDetalhamento->setTitle    ( "Informe os detalhes do PAO." );
        $obTxtDetalhamento->setNull     ( false              );

        // Define Objeto Button para Incluir Item na lista
        $obBtnIncluir = new Button;
        $obBtnIncluir->setValue            ( "Incluir"          );
        $obBtnIncluir->obEvento->setOnClick( "montaParametrosGET('incluirPAO');" );

        // Define Objeto Button para Incluir Item na lista
        $obBtnLimpar = new Button;
        $obBtnLimpar->setValue            ( "Limpar"          );
        $obBtnLimpar->obEvento->setOnClick( "montaParametrosGET('limpaCampos');" );

        $arBtn[] = $obBtnIncluir;
        $arBtn[] = $obBtnLimpar;

        $obFormulario = new Formulario;
        if ($stAcao == "alterar") {
            $obFormulario->addComponente ( $obLblCodProjeto );
            $obFormulario->addHidden     ( $obHdnCodProjeto );
        } else {
            $obFormulario->addComponente ( $obTxtCodProjeto );
        }
        $obFormulario->addComponente( $obTxtDescProjeto  );
        $obFormulario->addComponente( $obTxtDetalhamento );
        $obFormulario->defineBarra( $arBtn );
        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();
    } else {
        $stHTML = '';
    }
    $stJs .= "d.getElementById('spnPAO').innerHTML='".$stHTML."'; \n ";
    $stJs .= "d.getElementById('spnListaPAO').innerHTML='".$stHTMLLista."'";
    break;

case "limpaCampos":
    $stJs .= "jq('#inNumeroProjeto').val('');";
    $stJs .= "jq('#stNome').val('');";
    $stJs .= "jq('#stDetalhamento').val('');";
    $stJs .= "jq('#inNumeroProjeto').focus('');";
    break;

case "incluirPAO":
    if ($_REQUEST['inNumeroProjeto'] && $_REQUEST['stNome'] && $_REQUEST['stDetalhamento']) {
        $boIncluir = true;
        $arProjetoAtividade = Sessao::read('arProjetoAtividade');

        foreach ($arProjetoAtividade AS $arProjetoAtividadeTMP) {
            if ($arProjetoAtividadeTMP['num_pao'] == $_REQUEST['inNumeroProjeto']) {
                $boIncluir = false;
            }
        }

        if ($boIncluir) {
            // É criado um array contendendo os dados necessários de acordo com os dados da listagem para poder inseri-los temporariamente na
            // listagem
            $arDados = array();
            $arDados['num_pao']      = $_REQUEST['inNumeroProjeto'];
            $arDados['exercicio']    = Sessao::getExercicio();
            $arDados['nom_pao']      = $_REQUEST['stNome'];
            $arDados['detalhamento'] = $_REQUEST['stDetalhamento'];

            // Insere os dados incluidos no topo da listagem para que o usuário possa ver o dado que ele acabou de cadastrar entrando na listagem
            array_unshift($arProjetoAtividade, $arDados);

            Sessao::write('arProjetoAtividade', $arProjetoAtividade);

            $stJs .= montaLista();
            $stJs .= "jq('#inNumeroProjeto').val('');";
            $stJs .= "jq('#stNome').val('');";
            $stJs .= "jq('#stDetalhamento').val('');";
            $stJs .= "jq('#inNumeroProjeto').focus();";
        } else {
            $stJs .= "alertaAviso('Este código já foi inserido na lista!','form','erro','".Sessao::getId()."');\n";
        }
    } else {
        $stJs .= "alertaAviso('Preencha todos os campos com *','form','erro','".Sessao::getId()."');\n";
    }
    break;
}

if (trim($stJs) != '') {
    echo $stJs;
}

function montaLista()
{
    $obROrcamentoProjetoAtividade = new ROrcamentoProjetoAtividade;
    $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->setExercicio( Sessao::getExercicio() );
    $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->consultarConfiguracao();
    $inPosicao = $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->getNumPAOPosicaoDigitoID();
    $stDigitoProjeto   = $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->getNumPAODigitosIDProjeto();
    $stDigitoAtividade = $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->getNumPAODigitosIDAtividade();
    $stDigitoOrperacao = $obROrcamentoProjetoAtividade->obRConfiguracaoOrcamento->getNumPAODigitosIDOperEspeciais();

    $inTipoPAO = $_GET['inTipoPAO'];
    $obROrcamentoProjetoAtividade->setCodTipo  ( $inTipoPAO );
    $obROrcamentoProjetoAtividade->buscaProximoCodPorTipo( $inNumPAO );

    $arProjetoAtividade = Sessao::read('arProjetoAtividade');
    $obLista = new Lista;

    $rsLista = new RecordSet;
    $rsLista->preenche($arProjetoAtividade);

    if ($inNumPAO) {
        $arTipoPAO = explode( ',', $inTipoPAO );
    } else {
        if ( settype($inNumPAO, "integer") == 1 ) {
            $inNumPAO = $arTipoPAO[0]."001";
        }
    }

    foreach ($arTipoPAO as $inTipo) {
        if (in_array($inTipo, explode(',', $stDigitoProjeto))) {
            $stTipoPAO = 'Projetos';
        }
        if (in_array($inTipo, explode(',', $stDigitoAtividade))) {
            $stTipoPAO = 'Atividades';
        }
        if (in_array($inTipo, explode(',', $stDigitoOrperacao))) {
            $stTipoPAO = 'Operações Especiais';
        }
    }

    $obLista->setRecordSet( $rsLista );
    $obLista->setTitulo( $stTipoPAO.$stIncluso = ($stTipoPAO == "Projetos" ? " já Inclusos" : " já Inclusas") );
    $obLista->setMostraPaginacao(false);
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Descrição");
    $obLista->ultimoCabecalho->setWidth( 75 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "num_pao" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_pao" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->montaInnerHtml();

    return "d.getElementById('spnListaPAO').innerHTML='".$obLista->getHTML()."';";
}

?>

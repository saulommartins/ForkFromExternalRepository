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
    * Classe Oculta de Plano Banco
    * Data de Criação   : 06/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: OCManterPlanoConta.php 66258 2016-08-03 14:25:21Z evandro $

    * Casos de uso: uc-02.02.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php";
include_once CAM_GF_CONT_NEGOCIO."RContabilidadeLancamento.class.php";
include_once CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoBanco.class.php";
include_once (CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php";
include_once CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoBanco.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterPlanoConta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "CO".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
$obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );

function desbloqueiaAbas($boExecuta = false)
{
    $stJs .=
"window.parent.frames['telaPrincipal'].document.links['id_layer_2'].href
= \"javascript:HabilitaLayer('layer_2');\";";

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

$stFiltroEntidade = " AND entidade.cod_entidade = ".Sessao::getCodEntidade($boTransacao)." AND entidade.exercicio = '".Sessao::getExercicio()."'";
$obTEntidade = new TEntidade;
$obTEntidade->recuperaEntidades($rsEntidades, $stFiltroEntidade);

switch ($stCtrl) {
    case "tipoContaSintetica":

        $obHdnCodSistemaContabil = new Hidden;
        $obHdnCodSistemaContabil->setName ( "inCodSistemaContabil" );
    if ( Sessao::getExercicio() > '2012' ) {
        $obHdnCodSistemaContabil->setValue( "4" );
        $obHdnIndicadorSuperavit = new Hidden;
        $obHdnIndicadorSuperavit->setName( "stIndicadorSuperavit" );
        $obHdnIndicadorSuperavit->setValue( null );
    } else {
        $obHdnCodSistemaContabil->setValue( "5"                    );
    }

/*      Não há necessidade de mostrar. */
/*      $obTxtSistemaContabil = new Label;
        $obTxtSistemaContabil->setName      ( "stSistemaContabil" );
        $obTxtSistemaContabil->setValue     ( "5 - Não informado" );
        $obTxtSistemaContabil->setRotulo    ( "Sistema Contábil"  );  */

        $obFormulario = new Formulario;

    if ( Sessao::getExercicio() > '2012' ) {
        $obFormulario->addHidden ($obHdnIndicadorSuperavit);
    }
        $obFormulario->addHidden ($obHdnCodSistemaContabil);
        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();

        // BloqueiaAbas
        $stJs = "window.parent.frames['telaPrincipal'].document.links['id_layer_2'].href = \"javascript:exibeAvisoAbaBloqueada();\";";
        SistemaLegado::executaFrameOculto($stJs);

        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        $stHTML = str_replace( "\\\\'","\\'",$stHTML );
        $stHTML = str_replace( "\\\\'","'",$stHTML );

        SistemaLegado::executaFrameOculto("d.getElementById('spnSistemaContabil').innerHTML = '".$stHTML."';");
    break;

    case "tipoContaAnalitica":

        $obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->setExercicio( Sessao::getExercicio() );
        $obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->listarSistemaContaAnalitica( $rsSistemaContabil );
        if ($_REQUEST['inCodSistContab'] == 5) {
            unset($_REQUEST['inCodSistContab']);
        }
        // Define Objeto TextBox para Codigo do Sistema Contabil
        $obTxtSistemaContabil = new TextBox;
        $obTxtSistemaContabil->setName   ( "inCodSistemaContabil"         );
        $obTxtSistemaContabil->setId     ( "inCodSistemaContabil"         );
        $obTxtSistemaContabil->setValue  ( $_REQUEST['inCodSistContab']   );
        if ( strtolower($rsEntidades->getCampo('nom_cgm')) == 'tribunal de contas estado de mato grosso do sul' && Sessao::getExercicio() > 2011 ) {
            $obTxtSistemaContabil->setRotulo ( "Natureza Contábil"             );
        } else {
            $obTxtSistemaContabil->setRotulo ( "Sistema Contábil"             );
        }
        $obTxtSistemaContabil->setTitle  ( "Selecione o sistema contábil" );
        $obTxtSistemaContabil->setInteiro( true                           );
        $obTxtSistemaContabil->setNull   ( false );

        // Define Objeto Select para Nome do Sistema Contabil
        $obCmbSistemaContabil = new Select;
        $obCmbSistemaContabil->setName      ( "stNomeSistemaContabil"  );
        $obCmbSistemaContabil->setID        ( "stNomeSistemaContabil"  );
        $obCmbSistemaContabil->setValue     ( $_REQUEST['inCodSistContab'] );
        $obCmbSistemaContabil->addOption    ( "", "Selecione"          );
        $obCmbSistemaContabil->setCampoId   ( "cod_sistema"            );
        $obCmbSistemaContabil->setCampoDesc ( "nom_sistema"            );
        $obCmbSistemaContabil->preencheCombo( $rsSistemaContabil       );
        $obCmbSistemaContabil->setNull      ( false );

        // Faz a verificação do primeiro número do codigo de classificação
        // Se for 2,4 ou 9, ao inves de aparecer a combo, deve aparecer numa label a natureza do solo
        // Pois vai existir somente uma opção para cada uma delas, então não tem pq ser uma combo se não vai ser modificado o valor.
        $boLabel = false;
        switch ($_REQUEST['stCodClass'][0]) {
        case 3:
        case 4:
        case 9:
            $boLabel = true;
        }

        /* MONTA FORMULÁRIO */
        $obFormulario = new Formulario;
        $obFormulario->addComponenteComposto( $obTxtSistemaContabil,$obCmbSistemaContabil );

        $obRContabilidadePlanoBanco->setCodConta( $_REQUEST['inCodConta'] );
        $obRContabilidadePlanoBanco->setCodPlano( $_REQUEST['inCodPlano'] );
        $obRContabilidadePlanoBanco->consultar();

        $stIndicadorSuperavit = $obRContabilidadePlanoBanco->getIndicadorSuperavit();

        if ( Sessao::getExercicio() > '2012' ) {

            // Define Objeto Select para o Indicador Superávit
            $obCmbIndicadorSuperavit = new Select;
            $obCmbIndicadorSuperavit->setName      ( "stIndicadorSuperavit"    );
            $obCmbIndicadorSuperavit->setId        ( "stIndicadorSuperavit"    );
            $obCmbIndicadorSuperavit->setRotulo    ( "Indicador Superávit"     );
            $obCmbIndicadorSuperavit->addOption    ( "", "Selecione"    );
            $obCmbIndicadorSuperavit->addOption    ( "permanente", "Permanente" );
            $obCmbIndicadorSuperavit->addOption    ( "financeiro", "Financeiro" );
            $obCmbIndicadorSuperavit->addOption    ( "misto", "Misto" );
            $obCmbIndicadorSuperavit->setValue     ( $stIndicadorSuperavit      );
            $obFormulario->addComponente( $obCmbIndicadorSuperavit );
        }

        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();
        SistemaLegado::executaFrameOculto("d.getElementById('spnSistemaContabil').innerHTML = '".$stHTML."';");
        desbloqueiaAbas(true);
    break;

    case "mascaraEstrutural":
        $obRContabilidadePlanoBanco->recuperaMascaraConta( $stMascara );
        $arMascEstrutural = Mascara::validaMascaraDinamica( $stMascara , $_POST['stCodClass'] );
        $js .= "f.stCodClass.value = '".$arMascEstrutural[1]."'; \n";
        SistemaLegado::executaFrameOculto( $js );
    break;
    case 'montaListaEntidadeValor':

        if (!$_POST['inCodPlano']) {
            $_POST['inCodPlano'] = 0;
        }
        $obRContabilidadePlanoBanco->setCodPlano     ( $_POST['inCodPlano']      );
        $obRContabilidadePlanoBanco->setCodEstrutural( $_POST['stCodEstrutural'] );
        $obRContabilidadePlanoBanco->setDtSaldo      ( $_POST['dtSaldo']         );
        $obRContabilidadePlanoBanco->obROrcamentoEntidade->obRCGMPessoaFisica->setNumCGM( Sessao::read('numCgm') );
        $obRContabilidadePlanoBanco->buscaSaldo( $rsLista );
        $rsLista->addFormatacao( "valor", "NUMERIC_BR" );
        if ($_POST['dtSaldo']) {
            $rsLista->addFormatacao( "valor_dt_saldo", "NUMERIC_BR" );
        }

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsLista );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Código");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Descrição ");
        $obLista->ultimoCabecalho->setWidth( 50 );
        $obLista->commitCabecalho();

        if ($_POST['dtSaldo']) {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Saldo até ".$_POST['dtSaldo']);
            $obLista->ultimoCabecalho->setWidth( 15 );
            $obLista->commitCabecalho();
        }

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Saldo Atual");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_entidade" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nom_cgm" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        if ($_POST['dtSaldo']) {
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "valor_dt_saldo" );
            $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
            $obLista->commitDado();
        }
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "valor" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obLista->montaInnerHTML();
        $stHTML = $obLista->getHTML();

        SistemaLegado::executaFrameOculto("d.getElementById('spnLista').innerHTML = '".$stHTML."';");
    break;
    case "MontaAgencia":
        if ($_REQUEST["inNumBanco"] != '') {
            $stSelecionado = $_REQUEST['inNumAgencia'];
            $stJs .= "limpaSelect(f.stNomeAgencia,0); \n";
            $stJs .= "f.inNumAgencia.value=''; \n";
            $stJs .= "f.stNomeAgencia.options[0] = new Option('Selecione','', 'selected');\n";

            $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inNumBanco"] );
            $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->listarBanco( $rsBanco );
            $stJs .= "f.inCodBanco.value = '".$rsBanco->getCampo('cod_banco')."';\n";

            $obRContabilidadePlanoBanco->obRMONAgencia->listarAgencia( $rsCombo , $stFiltro, $obTransacao);

            $inCount = 0;
            while (!$rsCombo->eof()) {
                $inCount++;
                $inId   = $rsCombo->getCampo("num_agencia");
                $stDesc = $rsCombo->getCampo("nom_agencia");
                if( $stSelecionado == $inId )
                    $stSelected = 'selected';
                else
                    $stSelected = '';
                $stJs .= "f.stNomeAgencia.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                $rsCombo->proximo();
            }

            $stJs .= "limpaSelect(f.stContaCorrente, 1);";
        } else {
            $stJs .= "limpaSelect(f.stNomeAgencia,0); \n";
            $stJs .= "f.inNumAgencia.value=''; \n";
            $stJs .= "f.stNomeAgencia.options[0] = new Option('Selecione','', 'selected');\n";
            $stJs .= "limpaSelect(f.stContaCorrente,0);";
            $stJs .= "f.stContaCorrente.options[0] = new Option('Selecione','', 'selected');\n";
        }
        echo $stJs;
    break;
    case "MontaContaCorrente":
        if ($_REQUEST["inNumAgencia"] != '') {

            $obRContabilidadePlanoBanco->setCodConta( $_REQUEST['inCodConta'] );
            $obRContabilidadePlanoBanco->setCodPlano( $_REQUEST['inCodPlano'] );

            $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setCodBanco( $_REQUEST["inCodBanco"] );
            $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inNumBanco"] );
            $obRContabilidadePlanoBanco->obRMONAgencia->setNumAgencia( $_REQUEST["inNumAgencia"] );
            $obRContabilidadePlanoBanco->obRMONAgencia->listarAgencia( $rsCombo , $stFiltro, $obTransacao);
            $stJs .= "f.inCodAgencia.value = '".$rsCombo->getCampo('cod_agencia')."';\n";

            $obRContabilidadePlanoBanco->consultar();

            $stCombo  = "stContaCorrente";
            $stSelecionado = $_REQUEST['stContaCorrente'];
            $stJs .= "limpaSelect(f.$stCombo,0); \n";
            $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

            include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
            $obRMONContaCorrente = new RMONContaCorrente();
            $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setCodBanco( $_REQUEST['inCodBanco'] );
            $obRMONContaCorrente->obRMONAgencia->setNumAgencia( $_REQUEST['inNumAgencia'] );

            $rsCCorrente = new RecordSet();
            $obRMONContaCorrente->listarContaCorrente( $rsCCorrente, $obTransacao );

            $inCount - 0;
            while ( !$rsCCorrente->eof() ) {
                $inCount++;
                $inId = $rsCCorrente->getCampo("num_conta_corrente");
                $stDesc = $rsCCorrente->getCampo("num_conta_corrente");
                if ($stSelecionado == $inId) {
                    $stSelected = 'selected';
                } else {
                    $stSelected = '';
                }
                $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                $rsCCorrente->proximo();
            }

            //$stJs .= "f.stContaCorrente.innerHTML = '".$obRContabilidadePlanoBanco->getContaCorrente()."'; \n";
        } else {
            $stJs .= "limpaSelect(f.stContaCorrente,0);";
            $stJs .= "f.stContaCorrente.options[0] = new Option('Selecione','', 'selected');\n";
        }
        echo $stJs;
    break;

    case "limpaCombo2":
        $stJs .= "limpaSelect('f.stNomeAgencia',0);\n";
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "BuscaContaCorrente":

            if ($_REQUEST['inCodBanco']) {

                //$obRContabilidadePlanoBanco->setCodConta( $_REQUEST['inCodConta'] );
                $obTContabilidadePlanoBanco = new TContabilidadePlanoBanco;
                $obTContabilidadePlanoBanco->setDado( 'cod_banco'	 	, $_REQUEST['inCodBanco']);
                $obTContabilidadePlanoBanco->setDado( 'cod_agencia'	 	, $_REQUEST['inCodAgencia']);
                $obTContabilidadePlanoBanco->setDado( 'conta_corrente'	, $_REQUEST['stContaCorrente']);
                $obTContabilidadePlanoBanco->setDado( 'exercicio'	 	, Sessao::getExercicio());

                if ( substr( $_REQUEST['stCodClass'],0,9 ) == '1.1.1.1.1' ) {
                    $stFiltro = " AND ( cod_estrutural like '1.1.1.1.2%' or cod_estrutural like '1.1.1.1.3%' ) ";
                }

                if ( substr( $_REQUEST['stCodClass'],0,9 ) == '1.1.1.1.2' ) {
                    $stFiltro = " AND ( cod_estrutural like '1.1.1.1.1%' or cod_estrutural like '1.1.1.1.2%' ) ";
                }

                if ( substr( $_REQUEST['stCodClass'],0,9 ) == '1.1.1.1.3' ) {
                    $stFiltro = " AND ( cod_estrutural like '1.1.1.1.1%' or cod_estrutural like '1.1.1.1.3%' ) ";
                }

                $obTContabilidadePlanoBanco->listarPorEstrutural( $rsPlanoBanco , $stFiltro );

                //if ( $rsPlanoBanco->eof() ) { retirada validação para aceitar contas correntes repetidas para o mesmo banco e agencia, para atender necessidade do Tribunal de contas do estado de Goias

                    $obRContabilidadePlanoBanco->setCodPlano( $_REQUEST['inCodPlano'] );

                    include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
                    $obRMONContaCorrente = new RMONContaCorrente();
                    $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setCodBanco( $_REQUEST['inCodBanco'] );
                    $obRMONContaCorrente->obRMONAgencia->setNumAgencia( $_REQUEST['inNumAgencia'] );
                    $obRMONContaCorrente->obRMONAgencia->setCodAgencia( $_REQUEST['inCodAgencia'] );
                    $obRMONContaCorrente->setNumeroConta( $_REQUEST['stContaCorrente']);
                    $obRMONContaCorrente->consultarContaCorrente( $rsCCorrente, $obTransacao );

                    $stJs .= "f.inContaCorrente.value = ".$rsCCorrente->getCampo( 'cod_conta_corrente')."; \n";
                    $stJs .= "alertaAviso('','','','".Sessao::getId()."'); \n";
                /*} else {
                    $stJs .= "alertaAviso('Já existe a <i><b>conta plano: ".$rsPlanoBanco->getCampo('cod_plano')."</b></i> cadastrada para a <i><b>conta corrente ".$_REQUEST['stContaCorrente']."</b></i> informada.','form','erro','".Sessao::getId()."');";
                    $stJs .= "f.stContaCorrente.selectedIndex = 0;";
                }*/
            }
            echo $stJs;
    break;

    case "HabilitaCampos":

        if ($_REQUEST['stTipoConta'] == 'S') {

                $stJs = "desabilitaCampo('boContaBanco');";
                $stJs.= "desabilitaCampo('inCodRecurso,stNomeRecurso');";

                if ($_REQUEST['stCodClass']) {
                    include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php");
                    $obTContabilidadePlanoConta = new TContabilidadePlanoConta();
                    $obTContabilidadePlanoConta->setDado( 'exercicio',Sessao::getExercicio() );
                    $obTContabilidadePlanoConta->setDado( 'cod_estrutural',$_REQUEST['stCodClass'] );
                    $obTContabilidadePlanoConta->verificaContaDesdobrada( $rsContas, $stFiltro, $obTransacao );

                    // Verifica se a conta tem desdobramentos
                    if ( $rsContas->getCampo('retorno') == 't' ) {
                        $stJs .= "desabilitaCampo('txtCodClassContabil,stNomeClassContabil');";
                        $stJs .= "desabilitaCampo('txtCodClassContabil,stNomeClassContabil');";
                    } else {
                        $stJs .= "habilitaCampo('txtCodClassContabil,stNomeClassContabil');";
                    }
                }

        }

        if ($_REQUEST['stTipoConta'] == 'A') {
            $stJs = "habilitaCampo('boContaBanco');";
            $stJs.= "habilitaCampo('inCodRecurso,stNomeRecurso');";
            $stJs.= "habilitaCampo('txtCodClassContabil,stNomeClassContabil');";
        }

        echo $stJs;
    break;

    case "MontaAgencia":
            if ($_REQUEST["inNumBanco"] != '') {
                $stSelecionado = $_REQUEST['inNumAgencia'];
                $stJs .= "limpaSelect(f.stNomeAgencia,0); \n";
                $stJs .= "f.inNumAgencia.value=''; \n";
                $stJs .= "f.stNomeAgencia.options[0] = new Option('Selecione','', 'selected');\n";

                $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inNumBanco"] );
                $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->listarBanco( $rsBanco );
                $stJs .= "f.inCodBanco.value = '".$rsBanco->getCampo('cod_banco')."';\n";

                $obRContabilidadePlanoBanco->obRMONAgencia->listarAgencia( $rsCombo , $stFiltro, $obTransacao);

                $inCount = 0;
                while (!$rsCombo->eof()) {
                    $inCount++;
                    $inId   = $rsCombo->getCampo("num_agencia");
                    $stDesc = $rsCombo->getCampo("nom_agencia");
                    if( $stSelecionado == $inId )
                        $stSelected = 'selected';
                    else
                        $stSelected = '';
                    $stJs .= "f.stNomeAgencia.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                    $rsCombo->proximo();
                }

                $stJs .= "limpaSelect(f.stContaCorrente, 1);";
            } else {
                $stJs .= "limpaSelect(f.stNomeAgencia,0); \n";
                $stJs .= "f.inNumAgencia.value=''; \n";
                $stJs .= "f.stNomeAgencia.options[0] = new Option('Selecione','', 'selected');\n";
                $stJs .= "limpaSelect(f.stContaCorrente,0);";
                $stJs .= "f.stContaCorrente.options[0] = new Option('Selecione','', 'selected');\n";
            }
            echo $stJs;
        break;
        case "MontaContaCorrente":
            if ($_REQUEST["inNumAgencia"] != '') {

                $obRContabilidadePlanoBanco->setCodConta( $_REQUEST['inCodConta'] );
                $obRContabilidadePlanoBanco->setCodPlano( $_REQUEST['inCodPlano'] );

                $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setCodBanco( $_REQUEST["inCodBanco"] );
                $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inNumBanco"] );
                $obRContabilidadePlanoBanco->obRMONAgencia->setNumAgencia( $_REQUEST["inNumAgencia"] );
                $obRContabilidadePlanoBanco->obRMONAgencia->listarAgencia( $rsCombo , $stFiltro, $obTransacao);
                $stJs .= "f.inCodAgencia.value = '".$rsCombo->getCampo('cod_agencia')."';\n";

                $obRContabilidadePlanoBanco->consultar();

                $stCombo  = "stContaCorrente";
                $stSelecionado = $_REQUEST['stContaCorrente'];
                $stJs .= "limpaSelect(f.$stCombo,0); \n";
                $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

                include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
                $obRMONContaCorrente = new RMONContaCorrente();
                $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setCodBanco( $_REQUEST['inCodBanco'] );
                $obRMONContaCorrente->obRMONAgencia->setNumAgencia( $_REQUEST['inNumAgencia'] );

                $rsCCorrente = new RecordSet();
                $obRMONContaCorrente->listarContaCorrente( $rsCCorrente, $obTransacao );

                $inCount - 0;
                while ( !$rsCCorrente->eof() ) {
                    $inCount++;
                    $inId = $rsCCorrente->getCampo("num_conta_corrente");
                    $stDesc = $rsCCorrente->getCampo("num_conta_corrente");
                    if ($stSelecionado == $inId) {
                        $stSelected = 'selected';
                    } else {
                        $stSelected = '';
                    }
                    $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                    $rsCCorrente->proximo();
                }

              
            } else {
                $stJs .= "limpaSelect(f.stContaCorrente,0);";
                $stJs .= "f.stContaCorrente.options[0] = new Option('Selecione','', 'selected');\n";
            }
            echo $stJs;
        break;

        case "limpaCombo2":
            $stJs .= "limpaSelect('f.stNomeAgencia',0);\n";
            SistemaLegado::executaFrameOculto($stJs);
        break;

        case "BuscaContaCorrente":

                if ($_REQUEST['inCodBanco']) {

                    
                    $obTContabilidadePlanoBanco = new TContabilidadePlanoBanco;
                    $obTContabilidadePlanoBanco->setDado( 'cod_banco'       , $_REQUEST['inCodBanco']);
                    $obTContabilidadePlanoBanco->setDado( 'cod_agencia'     , $_REQUEST['inCodAgencia']);
                    $obTContabilidadePlanoBanco->setDado( 'conta_corrente'  , $_REQUEST['stContaCorrente']);
                    $obTContabilidadePlanoBanco->setDado( 'exercicio'       , Sessao::getExercicio());

                    if ( substr( $_REQUEST['stCodClass'],0,9 ) == '1.1.1.1.1' ) {
                        $stFiltro = " AND ( cod_estrutural like '1.1.1.1.2%' or cod_estrutural like '1.1.1.1.3%' ) ";
                    }

                    if ( substr( $_REQUEST['stCodClass'],0,9 ) == '1.1.1.1.2' ) {
                        $stFiltro = " AND ( cod_estrutural like '1.1.1.1.1%' or cod_estrutural like '1.1.1.1.2%' ) ";
                    }

                    if ( substr( $_REQUEST['stCodClass'],0,9 ) == '1.1.1.1.3' ) {
                        $stFiltro = " AND ( cod_estrutural like '1.1.1.1.1%' or cod_estrutural like '1.1.1.1.3%' ) ";
                    }

                    $obTContabilidadePlanoBanco->listarPorEstrutural( $rsPlanoBanco , $stFiltro );

                      $obRContabilidadePlanoBanco->setCodPlano( $_REQUEST['inCodPlano'] );

                        include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
                        $obRMONContaCorrente = new RMONContaCorrente();
                        $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setCodBanco( $_REQUEST['inCodBanco'] );
                        $obRMONContaCorrente->obRMONAgencia->setNumAgencia( $_REQUEST['inNumAgencia'] );
                        $obRMONContaCorrente->obRMONAgencia->setCodAgencia( $_REQUEST['inCodAgencia'] );
                        $obRMONContaCorrente->setNumeroConta( $_REQUEST['stContaCorrente']);
                        $obRMONContaCorrente->consultarContaCorrente( $rsCCorrente, $obTransacao );

                        $stJs .= "f.inContaCorrente.value = ".$rsCCorrente->getCampo( 'cod_conta_corrente')."; \n";
                        $stJs .= "alertaAviso('','','','".Sessao::getId()."'); \n";
             
                }
                echo $stJs;
        break;
}
?>

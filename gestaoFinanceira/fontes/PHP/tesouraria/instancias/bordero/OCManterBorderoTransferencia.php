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
    * Paginae Oculta para funcionalidade Manter Transferencia
    * Data de Criação   : 09/01/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 30835 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.20

*/

/*
$Log$
Revision 1.13  2006/07/05 20:39:07  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );
include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterBorderoTransferencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$obRMONAgencia = new RMONAgencia;
$obRMONConta = new RMONContaCorrente;

$rsBanco = new RecordSet;
$rsAgencia = new RecordSet;

$obRMONConta->obRMONAgencia->obRMONBanco->listarBanco($rsBanco);

function montaListaDiverso($arRecordSet , $boExecuta = true)
{
    $rsLista = new RecordSet;
    $rsLista->preenche( $arRecordSet );

    if ( !$rsLista->eof() ) {
        $obLista = new Lista;
        $obLista->setTitulo( "Registros" );
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsLista );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Entidade" );
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Credor" );
        $obLista->ultimoCabecalho->setWidth( 35 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Bco/Ag/Cta" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "(X)" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inCodigoEntidade" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stCredor" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[inNumBancoCredor]/[inNumAgenciaCredor]/[stNumeroContaCredor]" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inValor" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirTransferencia();" );
        $obLista->ultimaAcao->addCampo( "1", "num_item" );
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        $stHTML = str_replace( "\\\'","\\'",$stHTML );

        SistemaLegado::executaFrameOculto("d.getElementById('spnLista').innerHTML = '".$stHTML."'");
    } else {

        SistemaLegado::executaFrameOculto("d.getElementById('spnLista').innerHTML = ''");
    }

}

switch ($_REQUEST["stCtrl"]) {

    case 'incluiTransferencia':

        $boContaIgual   = true;
        $boContaIgualLista = true;

        $arItens = Sessao::read('arItens');

        if (count($arItens) > 0 ) {

            foreach ($arItens as $value) {
                if ($value['inCodContaCredor'] != $_POST['inCodContaCredor']) {
                    $boContaIgualLista = false;
                }
            }
        } else {
            $boContaIgualLista = false;
        }
        if ($_POST['inCodContaCredor'] != $_POST['inCodConta']) {
            $boContaIgual = false;
        } else {
            $boContaIgual = true;
        }

        if (!$boContaIgual && !$boContaIgualLista) {

            $obRTesourariaBoletim = new RTesourariaBoletim();
            $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
            $obRTesourariaBoletim->setDataBoletim( date( 'd/m/'.Sessao::getExercicio() ) );
            $obRTesourariaBoletim->addArrecadacao();
            $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );

            if ($_POST['stTipoTransacao'] == "6") {
                $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
                $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->listar( $rsEntidade );

                $_POST['inCodCredor'] = $rsEntidade->getCampo("numcgm");
            }

            $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->setNumCGM( $_POST['inCodCredor'] );
            $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->listar( $rsCgm );

            $inCount = count($arItens);
            $arItens[$inCount]['num_item']                     = $inCount+1;
            $arItens[$inCount]['inCodigoEntidade']             = $_POST['inCodEntidade'];
            $arItens[$inCount]['inCodCredor']                  = $_POST['inCodCredor'];
            $arItens[$inCount]['stCredor']                     = $_POST['inCodCredor'] ." - ". $rsCgm->getCampo('nom_cgm');
            $arItens[$inCount]['inValor' ]                     = $_POST['inValor'];
            $arItens[$inCount]['inNumBancoCredor']             = $_POST['inNumBancoCredor'];
            $arItens[$inCount]['inNumAgenciaCredor']           = $_POST['inNumAgenciaCredor'];
            $arItens[$inCount]['stNumeroContaCredor']          = $_POST['stNumeroContaCredor'];
            $arItens[$inCount]['stObservacao']                 = $_POST['stObservacao'];
            $arItens[$inCount]['inCodBancoCredor']             = $_POST['inCodBancoCredor'];
            $arItens[$inCount]['inCodAgenciaCredor']           = $_POST['inCodAgenciaCredor'];
            $arItens[$inCount]['stObservacao']                 = $_POST['stObservacao'];
            $arItens[$inCount]['stNumeroContaCredor']          = $_POST['stNumeroContaCredor'];
            $arItens[$inCount]['inNrDocumento']                = $_POST['inNrDocumento'];
            $arItens[$inCount]['stCPF/CNPJ']                   = $rsCgm->getCampo("cpf")."".$rsCgm->getCampo("cnpj");
            $arItens[$inCount]['stTipoTransacao']              = $_POST['stTipoTransacao'];
            $arItens[$inCount]['inCodContaCredor']             = $_POST['inCodContaCredor'];

            $stHTML = montaListaDiverso( Sessao::read('arItens') );

            if ( count( Sessao::read('arItens')) == 1 ) {
                SistemaLegado::executaFrameOculto(" f.inCodEntidade.disabled = true;" );
            }
        }
        if ($boContaIgual) {
            SistemaLegado::exibeAviso("A Conta de Transferência não poder ser a mesma Conta do Borderô!()","","erro");
        }
        if ($boContaIgualLista) {
            SistemaLegado::exibeAviso("A Conta de Transferência já foi inclusa na Lista!()","","erro");
        }
    break;

    case "preencheCamposCodigosCredor":

        $NumAgencia = $_REQUEST['cmbAgenciaCredor'];
        $obRMONAgencia->setNumAgencia ( $NumAgencia );
        $obRMONAgencia->consultarAgencia ( $rsListaAgencia );

        $CodBanco   = $rsListaAgencia->getCampo ("cod_banco");
        $CodAgencia = $rsListaAgencia->getCampo ("cod_agencia");

        $js .= "f.inCodBancoCredor.value='". $CodBanco ."'; \n";
        $js .= "f.inCodAgenciaCredor.value='". $CodAgencia ."'; \n";
        $js .= "f.inNumAgenciaCredor.value ='".$NumAgencia."';\n";

        SistemaLegado::executaFrameOculto($js);
    break;

    case "preencheAgenciaCredor":

        $js .= "f.inNumAgenciaCredor.value=''; \n";
        $js .= "limpaSelect(f.cmbAgenciaCredor,1); \n";
        $js .= "f.cmbAgenciaCredor[0] = new Option('Selecione','', 'selected'); \n";

        if ($_REQUEST['inNumBancoCredor']) {

            $obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inNumBancoCredor"] );
            $obRMONAgencia->listarAgencia( $rsAgencia );

            $inContador = 1;

            while ( !$rsAgencia->eof() ) {
                $inNumAgencia = $rsAgencia->getCampo( "num_agencia" );
                $stNomAgencia = $rsAgencia->getCampo( "nom_agencia" );

                $js .= "f.inCodAgenciaCredor.value='".$inCodAgencia."'; \n";
                $js .= "f.cmbAgenciaCredor.options[$inContador] = new Option('".$stNomAgencia."','".$inNumAgencia."'); \n";
                $inContador++;
                $rsAgencia->proximo();
            }
        }

        $js .= "f.inNumBancoCredor.value = '".$_REQUEST["cmbBancoCredor"]."'; \n";

        if ($_REQUEST["stLimpar"] == "limpar") {
            $js .= "f.inNumAgenciaCredor.value='".$_REQUEST["inNumAgenciaCredor"]."'; \n";
            $js .= "f.cmbAgenciaCredor.options[".$_REQUEST["inNumAgenciaCredor"]."].selected = true; \n";
        }
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'excluirTransferencia':

        $arItens = array();
        $inCount = 0;
        foreach ( Sessao::read('arItens') as $value ) {
            if ( ($value['num_item'] ) != $_GET['inNumItem'] ) {

                $arItens[$inCount]['num_item']                  = $inCount + 1;
                $arItens[$inCount]['inCodigoEntidade']          = $value['inCodigoEntidade'];
                $arItens[$inCount]['inCodCredor']               = $value['inCodCredor'];
                $arItens[$inCount]['stCredor']                  = $value['stCredor'];
                $arItens[$inCount]['inValor' ]                  = $value['inValor' ];
                $arItens[$inCount]['inNumBancoCredor']          = $value['inNumBancoCredor'];
                $arItens[$inCount]['inNumAgenciaCredor']        = $value['inNumAgenciaCredor'];
                $arItens[$inCount]['stNumeroContaCredor']       = $value['stNumeroContaCredor'];
                $arItens[$inCount]['stObservacao']              = $value['stObservacao'];
                $arItens[$inCount]['inCodBancoCredor']          = $value['inCodBancoCredor'];
                $arItens[$inCount]['inCodAgenciaCredor']        = $value['inCodAgenciaCredor'];
                $arItens[$inCount]['stObservacao']              = $value['stObservacao'];
                $arItens[$inCount]['stNumeroContaCredor']       = $value['stNumeroContaCredor'];
                $arItens[$inCount]['inNrDocumento']             = $value['inNrDocumento'];
                $arItens[$inCount]['stCPF/CNPJ']                = $value['stCPF/CNPJ'];
                $arItens[$inCount]['stTipoTransacao']           = $value['stTipoTransacao'];
                $arItens[$inCount]['inCodContaCredor']          = $value['inCodContaCredor'];

                $inCount++;
            }
        }
        Sessao::write('arItens',$arItens);

        montaListaDiverso( Sessao::read('arItens'));

        if ( sizeof( Sessao::read('arItens')) == 0 ) {
            SistemaLegado::executaFrameOculto(" f.inCodEntidade.disabled = false; ");
        }
    break;

    case 'mostraSpanBoletim':

        if ($_REQUEST['inCodEntidade']) {

            $obRTesourariaBoletim = new RTesourariaBoletim;
            $obRTesourariaBoletim->setExercicio( $_REQUEST['stExercicio'] );
            $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );

            $obRTesourariaBoletim->addBordero();
            $obRTesourariaBoletim->roUltimoBordero->addAssinatura();
            $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setExercicio( $_REQUEST['stExercicio'] );
            $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setTipo('BR');
            $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setEntidades( $_REQUEST['inCodEntidade'] );
            $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->listar( $rsAssinatura );

            if ( $rsAssinatura->getNumLinhas() > 0 ) {

                for ($x=1; $x<=$rsAssinatura->getNumLinhas(); $x++) {

                    $js .= "f.inNumAssinante_".$x.".value = '".$rsAssinatura->getCampo("numcgm")."'; \n";
                    $js .= "d.getElementById('stNomAssinante_".$x."').innerHTML = '".$rsAssinatura->getCampo("nom_cgm")."'; \n";
                    $js .= "f.stNomAssinante_".$x.".value = '".$rsAssinatura->getCampo("nom_cgm")."'; \n";
                    $js .= "f.inNumMatricula_".$x.".value = '".$rsAssinatura->getCampo("num_matricula")."'; \n";
                    $js .= "f.stCargo_".$x.".value = '".$rsAssinatura->getCampo("cargo")."'; \n";

                    $rsAssinatura->proximo();
                }
            }

            //Define o objeto INNER para armazenar a Conta Banco
            $obBscConta = new BuscaInner;
            $obBscConta->setRotulo( "Conta" );
            $obBscConta->setTitle( "Informe a Conta" );
            $obBscConta->setNull( false );
            $obBscConta->setId( "stConta" );
            $obBscConta->setValue( '' );
            $obBscConta->obCampoCod->setName("inCodConta");
            $obBscConta->obCampoCod->setValue( "" );
            $obBscConta->setFuncaoBusca ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodConta','stConta','banco','".Sessao::getId()."','800','550');" );
            $obBscConta->setValoresBusca( CAM_GF_CONT_POPUPS.'planoConta/OCPlanoConta.php?'.Sessao::getId(), 'frm', 'banco' );

            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obBscConta );

            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();

            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );

            $js .= "d.getElementById('spnContaBanco').innerHTML = '".$stHTML."' \n";

            $obRTesourariaBoletim->listarBoletimAberto( $rsLista );

            if ( $rsLista->getNumLinhas() > -1 ) {

                $obHdnCodBoletim = new Hidden;
                $obHdnCodBoletim->setName( "inCodBoletim" );
                $obHdnCodBoletim->setValue( $rsLista->getCampo("cod_boletim") );

                $obHdnExercicioBoletim = new Hidden;
                $obHdnExercicioBoletim->setName( "stExercicioBoletim" );
                $obHdnExercicioBoletim->setValue( $rsLista->getCampo("exercicio") );

                $obHdnDtBoletim = new Hidden;
                $obHdnDtBoletim->setName( "stDtBoletim" );
                $obHdnDtBoletim->setValue( $rsLista->getCampo("dt_boletim") );

                // Define Objeto Numeric para valor
                $obLblBoletim = new Label();
                $obLblBoletim->setRotulo( "Boletim"        );
                $obLblBoletim->setId    ( "inCodBoletim"   );
                $obLblBoletim->setValue ( $rsLista->getCampo("cod_boletim") );

                // Define Objeto Label para Valor Geral
                $obLblDtBoletim = new Label();
                $obLblDtBoletim->setRotulo( "Data do Boletim"        );
                $obLblDtBoletim->setId    ( "stDtBoletim"   );
                $obLblDtBoletim->setValue ( $rsLista->getCampo("dt_boletim") );

                $obFormulario = new Formulario;
                $obFormulario->addHidden     ( $obHdnCodBoletim  );
                $obFormulario->addHidden     ( $obHdnExercicioBoletim   );
                $obFormulario->addHidden     ( $obHdnDtBoletim   );
                $obFormulario->addComponente ( $obLblBoletim   );
                $obFormulario->addComponente ( $obLblDtBoletim );

                $obFormulario->montaInnerHTML ();
                $stHTML = $obFormulario->getHTML ();

                $stHTML = str_replace( "\n" ,"" ,$stHTML );
                $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
                $stHTML = str_replace( "  " ,"" ,$stHTML );
                $stHTML = str_replace( "'","\\'",$stHTML );
                $stHTML = str_replace( "\\\\'","\\'",$stHTML );

                SistemaLegado::executaFrameOculto("d.getElementById('spnBoletim').innerHTML = '".$stHTML."';".$js);
            } else {

                $obRTesourariaBoletim->listarBoletimFechado( $rsLista, "timestamp_fechamento DESC LIMIT 1" );

                $obRTesourariaBoletim->setExercicio($_REQUEST['stExercicio'] );
                $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']) ;
                $obRTesourariaBoletim->buscaProximoCodigo( $boTransacao );

                $stData = date("d/m/Y");
                $inCodBoletim = $obRTesourariaBoletim->getCodBoletim();

                $dt_fechamento = explode("/",substr($rsLista->getCampo("dt_fechamento"),0,10));

                $inData = $dt_fechamento[2] . $dt_fechamento[1] . $dt_fechamento[0];

                if ( $inData <  date("Ymd")) {

                    $obHdnCodBoletim = new Hidden;
                    $obHdnCodBoletim->setName( "inCodBoletim" );
                    $obHdnCodBoletim->setValue( $inCodBoletim );

                    $obHdnExercicioBoletim = new Hidden;
                    $obHdnExercicioBoletim->setName( "stExercicioBoletim" );
                    $obHdnExercicioBoletim->setValue( Sessao::getExercicio() );

                    $obHdnDtBoletim = new Hidden;
                    $obHdnDtBoletim->setName( "stDtBoletim" );
                    $obHdnDtBoletim->setValue( $stData );

                    // Define Objeto Numeric para valor
                    $obLblBoletim = new Label();
                    $obLblBoletim->setRotulo( "Boletim"        );
                    $obLblBoletim->setId    ( "inCodBoletim"   );
                    $obLblBoletim->setValue ( $inCodBoletim );

                    // Define Objeto Label para Valor Geral
                    $obLblDtBoletim = new Label();
                    $obLblDtBoletim->setRotulo( "Data do Boletim"        );
                    $obLblDtBoletim->setId    ( "stDtBoletim"   );
                    $obLblDtBoletim->setValue ( $stData );

                    $obFormulario = new Formulario;
                    $obFormulario->addHidden     ( $obHdnCodBoletim  );
                    $obFormulario->addHidden     ( $obHdnExercicioBoletim   );
                    $obFormulario->addHidden     ( $obHdnDtBoletim   );
                    $obFormulario->addComponente ( $obLblBoletim   );
                    $obFormulario->addComponente ( $obLblDtBoletim );

                    $obFormulario->montaInnerHTML ();
                    $stHTML = $obFormulario->getHTML ();

                    $stHTML = str_replace( "\n" ,"" ,$stHTML );
                    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
                    $stHTML = str_replace( "  " ,"" ,$stHTML );
                    $stHTML = str_replace( "'","\\'",$stHTML );
                    $stHTML = str_replace( "\\\\'","\\'",$stHTML );

                    SistemaLegado::executaFrameOculto("d.getElementById('spnBoletim').innerHTML = '".$stHTML."';".$js);
                } else {

                    SistemaLegado::exibeAviso("Não há boletim aberto","n_incluir","erro");
                }
            }

        } else {

            SistemaLegado::executaFrameOculto("d.getElementById('spnBoletim').innerHTML = ''; \n
                                               d.getElementById('spnContaBanco').innerHTML = ''; \n
                                              ");
        }

    break;

}
?>

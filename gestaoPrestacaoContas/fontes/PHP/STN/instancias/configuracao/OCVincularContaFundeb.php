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
    * Página de Vínculo de Conta Fundeb
    * Data de Criação   : 01/06/2011

    * @author

    * @ignore

    * Casos de uso :

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "VincularContaFundeb";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function mostraSpanConta()
{
    if ($_REQUEST['inCodEntidade']) {
        //Define o objeto INNER para armazenar a Conta Banco
        $obBscConta = new BuscaInner;
        $obBscConta->setRotulo( "Conta" );
        $obBscConta->setTitle( "Informe a Conta" );
        $obBscConta->setNull( false );
        $obBscConta->setId( "stConta" );
        $obBscConta->setValue( '' );
        $obBscConta->obCampoCod->setName("inCodConta");
        $obBscConta->obCampoCod->setValue( "" );
        $obBscConta->setFuncaoBusca ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodConta','stConta','banco&inCodEntidade='+document.frm.inCodigoEntidade.value,'".Sessao::getId()."','800','550');" );
        $obBscConta->obCampoCod->obEvento->setOnChange("return false;");

        $obFormulario = new Formulario;
        $obFormulario->addComponente ( $obBscConta );

        $obFormulario->montaInnerHTML ();
        $stHTML = $obFormulario->getHTML ();

        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        $stHTML = str_replace( "\\\\'","\\'",$stHTML );

    //verificar com a silvia
        //$js .= "d.getElementById('spnContaBanco').innerHTML = '".$stHTML."' \n";
        $js .= "f.inCodigoEntidade.value = '".$_REQUEST['inCodEntidade']."'; \n";

        return $js;
        //SistemaLegado::executaFrameOculto("f.inCodigoEntidade.value = '".$_REQUEST['inCodEntidade']."'; \n".$js);

    } else {
        return "d.getElementById('spnContaBanco').innerHTML           = ''; \n";
        //SistemaLegado::executaFrameOculto("d.getElementById('spnContaBanco').innerHTML           = ''; \n");
    }
}

function deletaContaFundeb()
{
    $arContas = Sessao::read('arContas');
    $arTmp = array();

    foreach ($arContas as $conta) {
        if ($_GET['inCodPlano'] != $conta['cod_plano']) {
            $arTmp[] = $conta;
        }
    }

    Sessao::write('arContas', $arTmp);

    return montaSpanListaConta();
}

function montaSpanListaConta($stJs = "")
{
    $rsRecordSet = new RecordSet;

    if ( count ( Sessao::read('arContas') ) > 0 ) {
        $rsRecordSet->preenche( Sessao::read('arContas') );
    }

    $obLista = new Lista;

    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( 'Conta Caixa das Entidades');

    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código Entidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código da Conta Banco");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Conta Fundeb");
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "cod_entidade" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "cod_plano" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento ( 'ESQUERDA' );
    $obLista->ultimoDado->setCampo( "nom_conta" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: excluirConta();" );
    //$obLista->ultimaAcao->addCampo("1","inId");
    $obLista->ultimaAcao->addCampo("1","[cod_plano]");
    $obLista->ultimaAcao->addCampo("2","[cod_entidade]");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs  .= "d.getElementById('spnListaConta').innerHTML = '".$html."';\n";

    return $stJs;
    //SistemaLegado::executaFrameOculto($stJs);
}

function incluiContaLista()
{
    if (!$_REQUEST['inCodPlano']) {
        SistemaLegado::executaFrameOculto("alertaAviso( 'Campo Conta de Banco inválido!','form','erro','".Sessao::getId()."' );".$stJsAux);
        exit;
    }

    if (!$_REQUEST['inCodEntidade']) {
        SistemaLegado::executaFrameOculto("alertaAviso( 'Campo Entidade inválido!','form','erro','".Sessao::getId()."' );".$stJsAux);
        exit;
    }

    $conta = array();
    $conta['cod_plano'] = $_REQUEST['inCodPlano'];
    $conta['cod_entidade'] = $_REQUEST['inCodEntidade'];
    $conta['nom_conta'] = $_REQUEST['stNomConta'];

    $arContas = Sessao::read('arContas');
    foreach ($arContas as $contasVinculadas) {
        if ($contasVinculadas['cod_plano'] == $conta['cod_plano']) {
            $stJsAux = limpaContaEntidade();
            SistemaLegado::executaFrameOculto("alertaAviso( 'Esta conta já esta configurada na lista!','form','erro','".Sessao::getId()."' );".$stJsAux);
            exit;
        }
    }
    $arContas[] = $conta;
    Sessao::write('arContas', $arContas);

    $stJs  = montaSpanListaConta();
    $stJs .= limpaContaEntidade();

    return $stJs;
}

function limpaContaEntidade()
{
    $stJs  = 'd.getElementById( "inCodPlano" ).value = ""; ';
    $stJs .= 'd.getElementById( "stNomConta" ).innerHTML = "&nbsp;"; ';
    $stJs .= 'd.getElementById( "inCodEntidade" ).value = ""; ';

    return $stJs;
}

$stJs = '';
switch ($_REQUEST["stCtrl"]) {
   case 'mostraSpanContaBanco':
        $stJs = mostraSpanConta();
    break;
    case 'montaSpanListaConta':
        $stJs = montaSpanListaConta();
    break;
    case 'delContaFundeb':
        $stJs = deletaContaFundeb();
    break;
    case 'incluiContaLista':
        $stJs = incluiContaLista();
    break;
}

SistemaLegado::executaFrameOculto($stJs);

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
    * Oculto da configuração de Obras e Serviços de Engenharia
    * Data de Criação   : 15/09/2015
    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Michel Teixeira
    * 
    * $Id: OCManterConfiguracaoObrasServicos.php 63809 2015-10-19 16:52:56Z lisiane $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoCEP.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAObraAndamento.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAObraFiscal.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAObraMedicao.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAObraContratos.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBASituacaoObra.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATipoContratacaoObra.class.php';
include_once CAM_GA_CGM_NEGOCIO."RCGM.class.php";
include_once CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoObrasServicos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');

function montaBairro()
{
    $stJs  = "jQuery('input[name=HdninCEP]').val('".$_REQUEST['inCEP']."');                                                     \n";
    $stJs .= "jQuery('#inCodBairro').empty().append(new Option('Selecione',''));                                                \n";
    $stJs .= "jQuery('input[name=inCEP]').val('');                                                                              \n";
    $stJs .= "jQuery('#stCEP').html('&nbsp;');                                                                                  \n";

    if($_REQUEST['inCEP']!=''){
        $obTCEP = new TCEP();
        $obTCEP->setDado('cep', $_REQUEST['inCEP']);
        $obTCEP->recuperaCepBairro($rsCEP);

        if ( $rsCEP->eof() ) {
            $stJs .= "alertaAviso('CEP Inválido!','form','erro','".Sessao::getId()."');                                         \n";
        }else{
            $inCep = $_REQUEST['inCEP'];
            $stCep = "";
            for($i = 0; $i<=strlen($inCep)-1; $i++){
                if($i==5)
                    $stCep .= '-'.$inCep[$i];
                else
                    $stCep .= $inCep[$i];
            }

            $stJs .= "jQuery('input[name=inCEP]').val('".$inCep."');                                                            \n";
            $stJs .= "jQuery('#stCEP').html('".$stCep."');                                                                      \n";

            foreach( $rsCEP->getElementos() as $key => $arBairro) {
                $stChaveBairro = $arBairro['cod_uf'].'_'.$arBairro['cod_municipio'].'_'.$arBairro['cod_bairro'];
                $stNomeBairro  = $arBairro['nom_uf'].' / '.$arBairro['nom_municipio'].' / '.$arBairro['nom_bairro'];
                $stJs .= "jQuery('#inCodBairro').append(new Option('".$stNomeBairro."','".$stChaveBairro."'));                  \n";
            }
        }
    }

    return $stJs;
}

function carregaLicitacao()
{
    $inCodEntidade              = ( isset($_REQUEST['inCodEntidade'])               ) ? $_REQUEST['inCodEntidade']              : NULL;
    $stExercicioLicitacao       = ( isset($_REQUEST['stExercicioLicitacao'])        ) ? $_REQUEST['stExercicioLicitacao']       : NULL;
    $inCodModalidade            = ( isset($_REQUEST['inCodModalidade'])             ) ? $_REQUEST['inCodModalidade']            : NULL;
    $inCodLicitacao             = ( isset($_REQUEST['inCodLicitacao'])              ) ? $_REQUEST['inCodLicitacao']             : NULL;

    //Filtro para Modalidades de Licitação e Licitação
    include_once TLIC."TLicitacaoLicitacao.class.php";
    $obTLicitacaoLicitacao = new TLicitacaoLicitacao();

    $inCount = 0;
    $stJs  = "jQuery('#inCodLicitacao').empty().append(new Option('Selecione',''));                                                         \n";

    if($stExercicioLicitacao!=NULL&&$inCodEntidade!=NULL&&$inCodModalidade!=NULL){
        $obTLicitacaoLicitacao->setDado( 'exercicio'        , $stExercicioLicitacao );
        $obTLicitacaoLicitacao->setDado( 'cod_entidade'     , $inCodEntidade        );
        $obTLicitacaoLicitacao->setDado( 'cod_modalidade'   , $inCodModalidade      );

        $obTLicitacaoLicitacao->recuperaLicitacao( $rsLicitacao );

        while (!($rsLicitacao->eof())) {
            $inCount++;
            $stJs .= "jQuery('#inCodLicitacao').append(new Option('".$rsLicitacao->getCampo('cod_licitacao')."','".$rsLicitacao->getCampo('cod_licitacao')."'));    \n";

            $rsLicitacao->proximo();
        }
    }

    return $stJs;
}

function LimparForm()
{
    Sessao::write('arAndamento' , array());
    Sessao::write('arFiscal'    , array());
    Sessao::write('arMedicao'   , array());
    Sessao::write('arContrato'  , array());

    $stJs  = limparAndamento();
    $stJs .= limparFiscal();
    $stJs .= limparMedicao();
    $stJs .= limparContrato();
    $stJs .= "jQuery('#spnListaFiscal').html('');   \n";
    $stJs .= "jQuery('#spnListaAndamento').html('');\n";
    $stJs .= "jQuery('#spnListaMedicao').html('');  \n";
    $stJs .= "jQuery('#spnListaContrato').html(''); \n";

    return $stJs;
}

function incluirAndamento()
{
    $obErro  = new Erro();
    $arAndamento = Sessao::read('arAndamento');
    $arAndamento = (is_array($arAndamento)) ? $arAndamento : array();
    $inId = count($arAndamento);

    if(($_REQUEST['inSituacaoObra']==2||$_REQUEST['inSituacaoObra']==3)&&$_REQUEST['stJustificativa']=='')
        $obErro->setDescricao('Informe a Justificativa da Situação da Obra.');
    if($_REQUEST['dtSituacao']=='')
        $obErro->setDescricao('Informe a Data da Situação da Obra.');
    if($_REQUEST['inSituacaoObra']=='')
        $obErro->setDescricao('Informe a Situação da Obra.');

    if(!$obErro->ocorreu()){
        foreach( $arAndamento as $key => $value) {
            if($value['inSituacaoObra']==$_REQUEST['inSituacaoObra']&&$value['dtSituacao']==$_REQUEST['dtSituacao'])
                $obErro->setDescricao('O Andamento informado, já está na Lista de Andamentos da Obra!');
        }
    }

    if(!$obErro->ocorreu()){
        $obTTCMBASituacaoObra = new TTCMBASituacaoObra;
        $stFiltro = " WHERE cod_situacao = ".$_REQUEST['inSituacaoObra'];
        $obTTCMBASituacaoObra->recuperaTodos($rsSituacaoObra, $stFiltro, $stOrder);

        $arAndamentoTemp = array();
        $arAndamentoTemp['inSituacaoObra']  = $_REQUEST['inSituacaoObra'];
        $arAndamentoTemp['stSituacaoObra']  = $rsSituacaoObra->getCampo('descricao');
        $arAndamentoTemp['dtSituacao']      = $_REQUEST['dtSituacao'];
        $arAndamentoTemp['stJustificativa'] = $_REQUEST['stJustificativa'];
        $arAndamentoTemp['inId']            = $inId;
    }

    if ($obErro->ocorreu()) {
        $stJs  = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');       \n";
    } else {
        $arAndamento[] = $arAndamentoTemp;
        Sessao::write('arAndamento', $arAndamento);

        $stJs  = limparAndamento();
        $stJs .= montaListaAndamento();
    }

    return $stJs;
}

function montaAlteraAndamento()
{
    $arAndamento = Sessao::read('arAndamento');

    $stJs  = "var jQuery = window.parent.frames['telaPrincipal'].jQuery;                                    \n";
    $stJs .= limparAndamento();
    $stJs .= "jQuery('#btnIncluirAndamento').val('Alterar');                                                \n";
    $stJs .= "jQuery('#btnIncluirAndamento').attr('onclick', 'montaParametrosGET(\'alteraAndamento\');');   \n";
    $stJs .= "jQuery('#inIdAndamento').val('".$_REQUEST['inId']."');                                        \n";

    foreach( $arAndamento as $key => $value) {
        if($value['inId']==$_REQUEST['inId']){
            $stJs .= "jQuery('#inSituacaoObra').val('".$value['inSituacaoObra']."');                        \n";
            $stJs .= "jQuery('#dtSituacao').val('".$value['dtSituacao']."');                                \n";

            if($value['inSituacaoObra']==2||$value['inSituacaoObra']==3){
                $stJs .= "jQuery('#stJustificativa').removeAttr('disabled');                                \n"; 
                $stJs .= "jQuery('#stJustificativa').val('".$value['stJustificativa']."');                  \n";
            }
            break;
        }
    }

    return $stJs;
}

function alteraAndamento()
{
    $obErro  = new Erro();
    $arAndamento = Sessao::read('arAndamento');
    $arAndamento = (is_array($arAndamento)) ? $arAndamento : array();
    $inId = $_REQUEST['inIdAndamento']; 

    if(($_REQUEST['inSituacaoObra']==2||$_REQUEST['inSituacaoObra']==3)&&$_REQUEST['stJustificativa']=='')
        $obErro->setDescricao('Informe a Justificativa da Situação da Obra.');
    if($_REQUEST['dtSituacao']=='')
        $obErro->setDescricao('Informe a Data da Situação da Obra.');
    if($_REQUEST['inSituacaoObra']=='')
        $obErro->setDescricao('Informe a Situação da Obra.');

    if(!$obErro->ocorreu()){
        foreach( $arAndamento as $key => $value) {
            if($value['inSituacaoObra']==$_REQUEST['inSituacaoObra']&&$value['dtSituacao']==$_REQUEST['dtSituacao']&&$value['inId']!=$inId)
                $obErro->setDescricao('O Andamento informado, já está na Lista de Andamentos da Obra!');
        }
    }

    if(!$obErro->ocorreu()){
        $obTTCMBASituacaoObra = new TTCMBASituacaoObra;
        $stFiltro = " WHERE cod_situacao = ".$_REQUEST['inSituacaoObra'];
        $obTTCMBASituacaoObra->recuperaTodos($rsSituacaoObra, $stFiltro, $stOrder);

        foreach( $arAndamento as $key => $value) {
            if($value['inId']==$inId){
                $arAndamento[$key]['inSituacaoObra']  = $_REQUEST['inSituacaoObra'];
                $arAndamento[$key]['stSituacaoObra']  = $rsSituacaoObra->getCampo('descricao');
                $arAndamento[$key]['dtSituacao']      = $_REQUEST['dtSituacao'];
                $arAndamento[$key]['stJustificativa'] = $_REQUEST['stJustificativa'];
                break;
            }
        }
    }

    if ($obErro->ocorreu()) {
        $stJs  = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');       \n";
    } else {
        Sessao::write('arAndamento', $arAndamento);

        $stJs  = limparAndamento();
        $stJs .= montaListaAndamento();
    }

    return $stJs;
}

function excluirAndamento()
{
    $obErro  = new Erro();
    $arAndamento = Sessao::read('arAndamento');
    $arAndamento = (is_array($arAndamento)) ? $arAndamento : array();
    $arAndamentoTemp = array();
    $inId=0;

    foreach( $arAndamento as $key => $value) {
        if($value['inId']!=$_REQUEST['inId']){
            $arAndamentoTemp[$inId] = $value;
            $arAndamentoTemp[$inId]['inId'] = $inId;
            $inId++;
        }
    }

    Sessao::write('arAndamento', $arAndamentoTemp);
    $stJs  = limparAndamento();
    $stJs .= montaListaAndamento();

    return $stJs;
}

function montaJustificativa()
{
    $stJs  = "jQuery('#stJustificativa').attr('disabled', 'disabled');  \n";
    $stJs .= "jQuery('#stJustificativa').val('');                       \n";
    if($_REQUEST['inSituacaoObra']==2||$_REQUEST['inSituacaoObra']==3)
        $stJs .= " jQuery('#stJustificativa').removeAttr('disabled');   \n";

    return $stJs;
}

function limparAndamento()
{
    $stJs  = "jQuery('#inSituacaoObra').val('');                                                            \n";
    $stJs .= "jQuery('#dtSituacao').val('');                                                                \n";
    $stJs .= "jQuery('#stJustificativa').val('');                                                           \n";
    $stJs .= "jQuery('#stJustificativa').attr('disabled', 'disabled');                                      \n";
    $stJs .= "jQuery('#btnIncluirAndamento').val('Incluir');                                                \n";
    $stJs .= "jQuery('#btnIncluirAndamento').attr('onclick', 'montaParametrosGET(\'incluirAndamento\');');  \n";
    $stJs .= "jQuery('#inIdAndamento').val('');                                                             \n";

    return $stJs;
}

function montaListaAndamento()
{
    $arAndamento = Sessao::read('arAndamento');
    $arAndamento = (is_array($arAndamento)) ? $arAndamento : array();
    $arAndamentoTemp = array();
    $inId=0;

    foreach( $arAndamento as $key => $value) {
        $arAndamentoTemp[$inId] = $value;
        list($inDia, $inMes, $inAno) = explode("/",$value['dtSituacao']);
        $arAndamentoTemp[$inId]['dataOrdena'] = $inAno.$inMes.$inDia;
        $inId++;
    }

    $rsRecordSet = new RecordSet();
    $rsRecordSet->preenche($arAndamentoTemp);
    $rsRecordSet->ordena('dataOrdena');

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de Andamento da Obra" );

    $obLista->setRecordSet( $rsRecordSet );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data da Situação" );
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Situação" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Justificativa" );
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 1 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtSituacao" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stSituacaoObra" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stJustificativa" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('montaAlteraAndamento');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirAndamento');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $stHtml = "";
    if(count($arAndamentoTemp)>0){
        $stHtml = str_replace("\n","",$obLista->getHTML());
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }

    $stJs = "jQuery('#spnListaAndamento').html('".$stHtml."');  \n";

    return $stJs;
}

function buscaResponsavelFiscal()
{
    $obRCGM = new RCGM;

    $stNull = "&nbsp;";

    if ($_REQUEST['inNumResponsavelFiscal'] != "" AND $_REQUEST['inNumResponsavelFiscal'] != "0") {
        $obRCGM->setNumCGM ($_REQUEST['inNumResponsavelFiscal']);
        $obRCGM->listar ($rsCGM);

        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs  = "jQuery('input[name=inNumResponsavelFiscal]').val('');                                                                             \n";
            $stJs .= "jQuery('input[name=inNumResponsavelFiscal]').focus();                                                                             \n";
            $stJs .= "jQuery('#inNomResponsavelFiscal').html('".$stNull."');                                                                            \n";
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST['inNumResponsavelFiscal'].")','form','erro','".Sessao::getId()."');                    \n";
        } else {
            $stJs .= "jQuery('#inNomResponsavelFiscal').html('".($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull)."');                  \n";
        }
    } else {
        $stJs  = "jQuery('input[name=inNumResponsavelFiscal]').val('');                                                                                 \n";
        $stJs .= "jQuery('#inNomResponsavelFiscal').html('".$stNull."');                                                                                \n";
        if($_REQUEST['inNumResponsavelFiscal']=='0')
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST['inNumResponsavelFiscal'].")','form','erro','".Sessao::getId()."');                    \n";
    }

    return $stJs;
}

function incluirFiscal()
{
    $obErro  = new Erro();
    $arFiscal = Sessao::read('arFiscal');
    $arFiscal = (is_array($arFiscal)) ? $arFiscal : array();
    $inId = count($arFiscal);

    if($_REQUEST['dtFinalFiscal']=='')
        $obErro->setDescricao('Informe a Data Final do Fiscal na Obra.');
    else
        list($inDiaFinal, $inMesFinal, $inAnoFinal) = explode("/",$_REQUEST['dtFinalFiscal']);
    if($_REQUEST['dtInicioFiscal']=='')
        $obErro->setDescricao('Informe a Data de Início do Fiscal na Obra.');
    else
        list($inDiaInicio, $inMesInicio, $inAnoInicio) = explode("/",$_REQUEST['dtInicioFiscal']);

    if($_REQUEST['inNumResponsavelFiscal']=='')
        $obErro->setDescricao('Informe o CGM do Responsável Fiscal na Obra.');

    if(!$obErro->ocorreu()){
        if($inAnoFinal.$inMesFinal.$inDiaFinal < $inAnoInicio.$inMesInicio.$inDiaInicio)
            $obErro->setDescricao('A Data Final do Fiscal na Obra, deve ser igual ou maior que a Data de Início do Fiscal na Obra!');
        else{
            $dtInicio = $inAnoInicio.$inMesInicio.$inDiaInicio;
            $dtFinal  = $inAnoFinal.$inMesFinal.$inDiaFinal;
        }
    }

    if(!$obErro->ocorreu()){
        foreach( $arFiscal as $key => $value) {
            list($inDiaInicioAnterior, $inMesInicioAnterior, $inAnoInicioAnterior) = explode("/",$value['dtInicioFiscal']);
            list($inDiaFinalAnterior, $inMesFinalAnterior, $inAnoFinalAnterior) = explode("/",$value['dtFinalFiscal']);
            $dtInicioAnt = $inAnoInicioAnterior.$inMesInicioAnterior.$inDiaInicioAnterior;
            $dtFinalAnt  = $inAnoFinalAnterior.$inMesFinalAnterior.$inDiaFinalAnterior;

            if($value['inNumResponsavelFiscal']==$_REQUEST['inNumResponsavelFiscal']){
                if( $dtInicio >= $dtInicioAnt  && $dtInicio <= $dtFinalAnt )
                    $obErro->setDescricao('O Fiscal informado, já está na Lista de Fiscais da Obra!');

                if( $dtFinal >= $dtInicioAnt  && $dtFinal <= $dtFinalAnt )
                    $obErro->setDescricao('O Fiscal informado, já está na Lista de Fiscais da Obra!');
            }
        }
    }

    if(!$obErro->ocorreu()){
        $obRCGM = new RCGM;
        $obRCGM->setNumCGM ($_REQUEST['inNumResponsavelFiscal']);
        $obRCGM->listar ($rsCGM);

        $arFiscalTemp = array();
        $arFiscalTemp['inNumResponsavelFiscal'] = $_REQUEST['inNumResponsavelFiscal'];
        $arFiscalTemp['inNomResponsavelFiscal'] = $rsCGM->getCampo('nom_cgm');
        $arFiscalTemp['stMatricula']            = $_REQUEST['stMatricula'];
        $arFiscalTemp['stRegistro']             = $_REQUEST['stRegistro'];
        $arFiscalTemp['dtInicioFiscal']         = $_REQUEST['dtInicioFiscal'];
        $arFiscalTemp['dtFinalFiscal']          = $_REQUEST['dtFinalFiscal'];
        $arFiscalTemp['inId']                   = $inId;
    }

    if ($obErro->ocorreu()) {
        $stJs  = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');       \n";
    } else {
        $arFiscal[] = $arFiscalTemp;
        Sessao::write('arFiscal', $arFiscal);

        $stJs  = limparFiscal();
        $stJs .= montaListaFiscal();
    }

    return $stJs;
}

function montaListaFiscal()
{
    $arFiscal = Sessao::read('arFiscal');
    $arFiscal = (is_array($arFiscal)) ? $arFiscal : array();
    $arFiscalTemp = array();
    $inId=0;

    foreach( $arFiscal as $key => $value) {
        $arFiscalTemp[$inId] = $value;
        list($inDia, $inMes, $inAno) = explode("/",$value['dtInicioFiscal']); 
        $arFiscalTemp[$inId]['dataOrdena'] = $inAno.$inMes.$inDia;
        $inId++;
    }

    $rsRecordSet = new RecordSet();
    $rsRecordSet->preenche($arFiscalTemp);
    $rsRecordSet->ordena('dataOrdena');

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de Fiscais" );

    $obLista->setRecordSet( $rsRecordSet );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "CGM" );
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Matrícula" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Registro" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Início" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Final" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 1 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[inNumResponsavelFiscal] - [inNomResponsavelFiscal]" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stMatricula" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stRegistro" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtInicioFiscal" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtFinalFiscal" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('montaAlteraFiscal');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirFiscal');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $stHtml = "";
    if(count($arFiscalTemp)>0){
        $stHtml = str_replace("\n","",$obLista->getHTML());
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }

    $stJs = "jQuery('#spnListaFiscal').html('".$stHtml."');                                             \n";

    return $stJs;
}

function limparFiscal()
{
    $stJs  = "jQuery('#inNumResponsavelFiscal').val('');                                                \n";
    $stJs .= "jQuery('#inNomResponsavelFiscal').html('&nbsp;');                                         \n";
    $stJs .= "jQuery('#stMatricula').val('');                                                           \n";
    $stJs .= "jQuery('#stRegistro').val('');                                                            \n";
    $stJs .= "jQuery('#dtInicioFiscal').val('');                                                        \n";
    $stJs .= "jQuery('#dtFinalFiscal').val('');                                                         \n";
    $stJs .= "jQuery('#btnIncluirFiscal').val('Incluir');                                               \n";
    $stJs .= "jQuery('#btnIncluirFiscal').attr('onclick', 'montaParametrosGET(\'incluirFiscal\');');    \n";
    $stJs .= "jQuery('#inIdFiscal').val('');                                                            \n";

    return $stJs;
}

function montaAlteraFiscal()
{
    $arFiscal = Sessao::read('arFiscal');

    $stJs  = "var jQuery = window.parent.frames['telaPrincipal'].jQuery;                                \n";
    $stJs .= limparFiscal();
    $stJs .= "jQuery('#btnIncluirFiscal').val('Alterar');                                               \n";
    $stJs .= "jQuery('#btnIncluirFiscal').attr('onclick', 'montaParametrosGET(\'alteraFiscal\');');     \n";
    $stJs .= "jQuery('#inIdFiscal').val('".$_REQUEST['inId']."');                                       \n";

    foreach( $arFiscal as $key => $value) {
        if($value['inId']==$_REQUEST['inId']){
            $stJs .= "jQuery('#inNumResponsavelFiscal').val('".$value['inNumResponsavelFiscal']."');    \n";
            $stJs .= "jQuery('#inNomResponsavelFiscal').html('".$value['inNomResponsavelFiscal']."');   \n";
            $stJs .= "jQuery('#stMatricula').val('".$value['stMatricula']."');                          \n";
            $stJs .= "jQuery('#stRegistro').val('".$value['stRegistro']."');                            \n";
            $stJs .= "jQuery('#dtInicioFiscal').val('".$value['dtInicioFiscal']."');                    \n";
            $stJs .= "jQuery('#dtFinalFiscal').val('".$value['dtFinalFiscal']."');                      \n";
            break;
        }
    }

    return $stJs;
}

function alteraFiscal()
{
    $obErro  = new Erro();
    $arFiscal = Sessao::read('arFiscal');
    $arFiscal = (is_array($arFiscal)) ? $arFiscal : array();
    $inId = $_REQUEST['inIdFiscal']; 

    if($_REQUEST['dtFinalFiscal']=='')
        $obErro->setDescricao('Informe a Data Final do Fiscal na Obra.');
    else
        list($inDiaFinal, $inMesFinal, $inAnoFinal) = explode("/",$_REQUEST['dtFinalFiscal']);
    if($_REQUEST['dtInicioFiscal']=='')
        $obErro->setDescricao('Informe a Data de Início do Fiscal na Obra.');
    else
        list($inDiaInicio, $inMesInicio, $inAnoInicio) = explode("/",$_REQUEST['dtInicioFiscal']);

    if($_REQUEST['inNumResponsavelFiscal']=='')
        $obErro->setDescricao('Informe o CGM do Responsável Fiscal na Obra.');

    if(!$obErro->ocorreu()){
        if($inAnoFinal.$inMesFinal.$inDiaFinal < $inAnoInicio.$inMesInicio.$inDiaInicio)
            $obErro->setDescricao('A Data Final do Fiscal na Obra, deve ser igual ou maior que a Data de Início do Fiscal na Obra!');
        else{
            $dtInicio = $inAnoInicio.$inMesInicio.$inDiaInicio;
            $dtFinal  = $inAnoFinal.$inMesFinal.$inDiaFinal;
        }
    }

    if(!$obErro->ocorreu()){
        foreach( $arFiscal as $key => $value) {
            list($inDiaInicioAnterior, $inMesInicioAnterior, $inAnoInicioAnterior) = explode("/",$value['dtInicioFiscal']);
            list($inDiaFinalAnterior, $inMesFinalAnterior, $inAnoFinalAnterior) = explode("/",$value['dtFinalFiscal']);
            $dtInicioAnt = $inAnoInicioAnterior.$inMesInicioAnterior.$inDiaInicioAnterior;
            $dtFinalAnt  = $inAnoFinalAnterior.$inMesFinalAnterior.$inDiaFinalAnterior;

            if($value['inNumResponsavelFiscal']==$_REQUEST['inNumResponsavelFiscal'] && $value['inId']!=$inId){
                if( $dtInicio >= $dtInicioAnt  && $dtInicio <= $dtFinalAnt )
                    $obErro->setDescricao('O Fiscal informado, já está na Lista de Fiscais da Obra!');

                if( $dtFinal >= $dtInicioAnt  && $dtFinal <= $dtFinalAnt )
                    $obErro->setDescricao('O Fiscal informado, já está na Lista de Fiscais da Obra!');
            }
        }
    }

    if(!$obErro->ocorreu()){
        $obRCGM = new RCGM;
        $obRCGM->setNumCGM ($_REQUEST['inNumResponsavelFiscal']);
        $obRCGM->listar ($rsCGM);

        foreach( $arFiscal as $key => $value) {
            if($value['inId']==$inId){
                $arFiscal[$key]['inNumResponsavelFiscal'] = $_REQUEST['inNumResponsavelFiscal'];
                $arFiscal[$key]['inNomResponsavelFiscal'] = $rsCGM->getCampo('nom_cgm');
                $arFiscal[$key]['stMatricula']            = $_REQUEST['stMatricula'];
                $arFiscal[$key]['stRegistro']             = $_REQUEST['stRegistro'];
                $arFiscal[$key]['dtInicioFiscal']         = $_REQUEST['dtInicioFiscal'];
                $arFiscal[$key]['dtFinalFiscal']          = $_REQUEST['dtFinalFiscal'];
                break;
            }
        }
    }

    if ($obErro->ocorreu()) {
        $stJs  = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');       \n";
    } else {
        Sessao::write('arFiscal', $arFiscal);

        $stJs  = limparFiscal();
        $stJs .= montaListaFiscal();
    }

    return $stJs;
}

function excluirFiscal()
{
    $obErro  = new Erro();
    $arFiscal = Sessao::read('arFiscal');
    $arFiscal = (is_array($arFiscal)) ? $arFiscal : array();
    $arFiscalTemp = array();
    $inId=0;

    foreach( $arFiscal as $key => $value) {
        if($value['inId']!=$_REQUEST['inId']){
            $arFiscalTemp[$inId] = $value;
            $arFiscalTemp[$inId]['inId'] = $inId;
            $inId++;
        }
    }

    Sessao::write('arFiscal', $arFiscalTemp);
    $stJs  = "var jQuery = window.parent.frames['telaPrincipal'].jQuery; \n";
    $stJs .= limparFiscal();
    $stJs .= montaListaFiscal();

    return $stJs;
}

function buscaAtestadorMedicao()
{
    $obRCGMPessoaFisica = new RCGMPessoaFisica;

    $stNull = "&nbsp;";

    if ($_REQUEST['inNumAtestadorMedicao'] != "" AND $_REQUEST['inNumAtestadorMedicao'] != "0") {
        $obRCGMPessoaFisica->setNumCGM ( $_REQUEST['inNumAtestadorMedicao'] );
        $obRCGMPessoaFisica->consultarCGM($rsCGM);

        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs  = "jQuery('input[name=inNumAtestadorMedicao]').val('');                                                              \n";
            $stJs .= "jQuery('input[name=inNumAtestadorMedicao]').focus();                                                              \n";
            $stJs .= "jQuery('#inNomAtestadorMedicao').html('".$stNull."');                                                             \n";
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST['inNumAtestadorMedicao'].")','form','erro','".Sessao::getId()."');     \n";
        } else {
            $stJs .= "jQuery('#inNomAtestadorMedicao').html('".($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull)."');   \n";
        }
    } else {
        $stJs  = "jQuery('input[name=inNumAtestadorMedicao]').val('');                                                                  \n";
        $stJs .= "jQuery('#inNomAtestadorMedicao').html('".$stNull."');                                                                 \n";
        if($_REQUEST['inNumAtestadorMedicao']=='0')
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST['inNumAtestadorMedicao'].")','form','erro','".Sessao::getId()."');     \n";
    }

    return $stJs;
}

function incluirMedicao()
{
    $obErro  = new Erro();
    $arMedicao = Sessao::read('arMedicao');
    $arMedicao = (is_array($arMedicao)) ? $arMedicao : array();
    $inId = count($arMedicao);

    if($_REQUEST['inNumAtestadorMedicao']=='')
        $obErro->setDescricao('Informe o CGM do Atestador da Medição da Obra.');

    if($_REQUEST['dtNFMedicao']=='')
        $obErro->setDescricao('Informe a Data da Nota Fiscal da Medição da Obra.');

    if($_REQUEST['stNFMedicao']=='')
        $obErro->setDescricao('Informe o Número da Nota Fiscal da Medição da Obra.');

    if($_REQUEST['nuVlMedicao']=='')
        $obErro->setDescricao('Informe o Valor da Medição da Obra.');

    if($_REQUEST['dtFinalMedicao']=='')
        $obErro->setDescricao('Informe a Data Final da Medição da Obra.');

    if($_REQUEST['dtMedicao']=='')
        $obErro->setDescricao('Informe a Data da Medição da Obra.');

    if($_REQUEST['dtInicioMedicao']=='')
        $obErro->setDescricao('Informe a Data de Início da Medição da Obra.');

    if($_REQUEST['inCodMedidaObra']=='')
        $obErro->setDescricao('Informe a Unidade de Medida da Obra.');

    if($_REQUEST['inNroMedicao']=='')
        $obErro->setDescricao('Informe o Número da Medição de Obra.');

    if(!$obErro->ocorreu()){
        foreach( $arMedicao as $key => $value) {            
            if($value['inNroMedicao']==$_REQUEST['inNroMedicao']){
                $obErro->setDescricao('A Medição informada, já está na Lista de Medições de Obras!');
            }
        }
    }

    if(!$obErro->ocorreu()){
        $obRCGM = new RCGM;
        $obRCGM->setNumCGM ($_REQUEST['inNumAtestadorMedicao']);
        $obRCGM->listar ($rsCGM);

        $arMedicaoTemp = array();
        $arMedicaoTemp['inNroMedicao']          = $_REQUEST['inNroMedicao'];
        $arMedicaoTemp['inCodMedidaObra']       = $_REQUEST['inCodMedidaObra'];
        $arMedicaoTemp['dtInicioMedicao']       = $_REQUEST['dtInicioMedicao'];
        $arMedicaoTemp['dtFinalMedicao']        = $_REQUEST['dtFinalMedicao'];
        $arMedicaoTemp['dtMedicao']        = $_REQUEST['dtMedicao'];
        $arMedicaoTemp['nuVlMedicao']           = $_REQUEST['nuVlMedicao'];
        $arMedicaoTemp['stNFMedicao']           = $_REQUEST['stNFMedicao'];
        $arMedicaoTemp['dtNFMedicao']           = $_REQUEST['dtNFMedicao'];
        $arMedicaoTemp['inNumAtestadorMedicao'] = $_REQUEST['inNumAtestadorMedicao'];
        $arMedicaoTemp['inNomAtestadorMedicao'] = $rsCGM->getCampo('nom_cgm');
        $arMedicaoTemp['inId']                  = $inId;   
    }

    if ($obErro->ocorreu()) {
        $stJs  = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');       \n";
    } else {
        $arMedicao[] = $arMedicaoTemp;
        Sessao::write('arMedicao', $arMedicao);

        $stJs  = limparMedicao();
        $stJs .= montaListaMedicao();
    }

    return $stJs;
}

function montaListaMedicao()
{
    $arMedicao = Sessao::read('arMedicao');
    $arMedicao = (is_array($arMedicao)) ? $arMedicao : array();

    $rsRecordSet = new RecordSet();
    $rsRecordSet->preenche($arMedicao);
    $rsRecordSet->ordena('inNroMedicao');

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de Medições de Obras e Serviços de Engenharia" );

    $obLista->setRecordSet( $rsRecordSet );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Número" );
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Início" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Final" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data da medição" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor da medição" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 1 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inNroMedicao" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtInicioMedicao" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtFinalMedicao" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtMedicao" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuVlMedicao" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('montaAlteraMedicao');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirMedicao');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $stHtml = "";
    if(count($arMedicao)>0){
        $stHtml = str_replace("\n","",$obLista->getHTML());
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }

    $stJs = "jQuery('#spnListaMedicao').html('".$stHtml."');                                            \n";

    return $stJs;
}

function montaAlteraMedicao()
{
    $arMedicao = Sessao::read('arMedicao');

    $stJs  = "var jQuery = window.parent.frames['telaPrincipal'].jQuery;                                \n";
    $stJs .= limparMedicao();
    $stJs .= "jQuery('#btnIncluirMedicao').val('Alterar');                                              \n";
    $stJs .= "jQuery('#btnIncluirMedicao').attr('onclick', 'montaParametrosGET(\'alteraMedicao\');');   \n";
    $stJs .= "jQuery('#inIdMedicao').val('".$_REQUEST['inId']."');                                      \n";

    foreach( $arMedicao as $key => $value) {
        if($value['inId']==$_REQUEST['inId']){
            $stJs .= "jQuery('#inNroMedicao').val('".$value['inNroMedicao']."');                        \n";
            $stJs .= "jQuery('#inCodMedidaObra').val('".$value['inCodMedidaObra']."');                  \n";
            $stJs .= "jQuery('#dtInicioMedicao').val('".$value['dtInicioMedicao']."');                  \n";
            $stJs .= "jQuery('#dtFinalMedicao').val('".$value['dtFinalMedicao']."');                    \n";
            $stJs .= "jQuery('#dtMedicao').val('".$value['dtMedicao']."');                              \n";
            $stJs .= "jQuery('#nuVlMedicao').val('".$value['nuVlMedicao']."');                          \n";
            $stJs .= "jQuery('#stNFMedicao').val('".$value['stNFMedicao']."');                          \n";
            $stJs .= "jQuery('#dtNFMedicao').val('".$value['dtNFMedicao']."');                          \n";
            $stJs .= "jQuery('#inNumAtestadorMedicao').val('".$value['inNumAtestadorMedicao']."');      \n";
            $stJs .= "jQuery('#inNomAtestadorMedicao').html('".$value['inNomAtestadorMedicao']."');     \n";
            break;
        }
    }

    return $stJs;
}

function alteraMedicao()
{
    $obErro  = new Erro();
    $arMedicao = Sessao::read('arMedicao');
    $arMedicao = (is_array($arMedicao)) ? $arMedicao : array();
    $inId = $_REQUEST['inIdMedicao']; 

    if($_REQUEST['inNumAtestadorMedicao']=='')
        $obErro->setDescricao('Informe o CGM do Atestador da Medição da Obra.');

    if($_REQUEST['dtNFMedicao']=='')
        $obErro->setDescricao('Informe a Data da Nota Fiscal da Medição da Obra.');

    if($_REQUEST['stNFMedicao']=='')
        $obErro->setDescricao('Informe o Número da Nota Fiscal da Medição da Obra.');

    if($_REQUEST['nuVlMedicao']=='')
        $obErro->setDescricao('Informe o Valor da Medição da Obra.');

    if($_REQUEST['dtFinalMedicao']=='')
        $obErro->setDescricao('Informe a Data Final da Medição da Obra.');

    if($_REQUEST['dtInicioMedicao']=='')
        $obErro->setDescricao('Informe a Data de Início da Medição da Obra.');

    if($_REQUEST['dtMedicao']=='')
        $obErro->setDescricao('Informe a Data da Medição da Obra.');

    if($_REQUEST['inCodMedidaObra']=='')
        $obErro->setDescricao('Informe a Unidade de Medida da Obra.');

    if($_REQUEST['inNroMedicao']=='')
        $obErro->setDescricao('Informe o Número da Medição de Obra.');

    if(!$obErro->ocorreu()){
        foreach( $arMedicao as $key => $value) {
            if($value['inNroMedicao']==$_REQUEST['inNroMedicao'] && $value['inId']!=$inId){
                $obErro->setDescricao('A Medição informada, já está na Lista de Medições de Obras!');
            }
        }
    }

    if(!$obErro->ocorreu()){
        $obRCGM = new RCGM;
        $obRCGM->setNumCGM ($_REQUEST['inNumAtestadorMedicao']);
        $obRCGM->listar ($rsCGM);

        foreach( $arMedicao as $key => $value) {
            if($value['inId']==$inId){
                $arMedicao[$key]['inNroMedicao']          = $_REQUEST['inNroMedicao'];
                $arMedicao[$key]['inCodMedidaObra']       = $_REQUEST['inCodMedidaObra'];
                $arMedicao[$key]['dtInicioMedicao']       = $_REQUEST['dtInicioMedicao'];
                $arMedicao[$key]['dtFinalMedicao']        = $_REQUEST['dtFinalMedicao'];
                $arMedicao[$key]['dtMedicao']             = $_REQUEST['dtMedicao'];
                $arMedicao[$key]['nuVlMedicao']           = $_REQUEST['nuVlMedicao'];
                $arMedicao[$key]['stNFMedicao']           = $_REQUEST['stNFMedicao'];
                $arMedicao[$key]['dtNFMedicao']           = $_REQUEST['dtNFMedicao'];
                $arMedicao[$key]['inNumAtestadorMedicao'] = $_REQUEST['inNumAtestadorMedicao'];
                $arMedicao[$key]['inNomAtestadorMedicao'] = $rsCGM->getCampo('nom_cgm');
                break;
            }
        }
    }

    if ($obErro->ocorreu()) {
        $stJs  = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');       \n";
    } else {
        Sessao::write('arMedicao', $arMedicao);

        $stJs  = limparMedicao();
        $stJs .= montaListaMedicao();
    }

    return $stJs;
}

function excluirMedicao()
{
    $obErro  = new Erro();
    $arMedicao = Sessao::read('arMedicao');
    $arMedicao = (is_array($arMedicao)) ? $arMedicao : array();
    $arMedicaoTemp = array();
    $inId=0;

    foreach( $arMedicao as $key => $value) {
        if($value['inId']!=$_REQUEST['inId']){
            $arMedicaoTemp[$inId] = $value;
            $arMedicaoTemp[$inId]['inId'] = $inId;
            $inId++;
        }
    }

    Sessao::write('arMedicao', $arMedicaoTemp);
    $stJs  = "var jQuery = window.parent.frames['telaPrincipal'].jQuery; \n";
    $stJs .= limparMedicao();
    $stJs .= montaListaMedicao();

    return $stJs;
}

function limparMedicao()
{
    $stJs  = "jQuery('#inNroMedicao').val('');                                                          \n";
    $stJs .= "jQuery('#inCodMedidaObra').val('');                                                       \n";
    $stJs .= "jQuery('#dtInicioMedicao').val('');                                                       \n";
    $stJs .= "jQuery('#dtFinalMedicao').val('');                                                        \n";
    $stJs .= "jQuery('#dtMedicao').val('');                                                             \n";
    $stJs .= "jQuery('#nuVlMedicao').val('');                                                           \n";
    $stJs .= "jQuery('#stNFMedicao').val('');                                                           \n";
    $stJs .= "jQuery('#dtNFMedicao').val('');                                                           \n";
    $stJs .= "jQuery('#inNumAtestadorMedicao').val('');                                                 \n";
    $stJs .= "jQuery('#inNomAtestadorMedicao').html('&nbsp;');                                          \n";
    $stJs .= "jQuery('#btnIncluirMedicao').val('Incluir');                                              \n";
    $stJs .= "jQuery('#btnIncluirMedicao').attr('onclick', 'montaParametrosGET(\'incluirMedicao\');');  \n";
    $stJs .= "jQuery('#inIdMedicao').val('');                                                           \n";

    return $stJs;
}

function montaNroTipoContratacao($inCodTipoContratacao="")
{
    $stJs  = "jQuery('#spnNroTipoContratacao').html('');                \n";
    $inCodTipoContratacao = ($inCodTipoContratacao!='') ? $inCodTipoContratacao : $_REQUEST['inCodTipoContratacao'];

    $stNomeTipoContratacao = "stNroTipoContratacao";
    $obTxtNroTipoContratacao = new TextBox();
    $obTxtNroTipoContratacao->setMaxLength  ( 16                        );
    $obTxtNroTipoContratacao->setSize       ( 21                        );
    $obTxtNroTipoContratacao->setNull       ( true                      );

    if($inCodTipoContratacao==4||$inCodTipoContratacao==5){
        $obTxtNroTipoContratacao->setRotulo ( '**Número do Instrumento' );
        $stNomeTipoContratacao = "stNroInstrumento";
        $boMontaTipoContratacao = true;        
    }else if($inCodTipoContratacao==1){
        $obTxtNroTipoContratacao->setRotulo ( '**Número do Contrato'    );
        $stNomeTipoContratacao = "stNroContrato";
        $boMontaTipoContratacao = true;        
    }else if($inCodTipoContratacao==2){
        $obTxtNroTipoContratacao->setRotulo ( '**Número do Convênio'    );
        $stNomeTipoContratacao = "stNroConvenio";
        $boMontaTipoContratacao = true;        
    }

    if($inCodTipoContratacao==3||$inCodTipoContratacao==4||$inCodTipoContratacao==5){
        $obTxtNroTermo = new TextBox();
        $obTxtNroTermo->setName     ( 'stNroTermo'                      );
        $obTxtNroTermo->setId       ( 'stNroTermo'                      );
        $obTxtNroTermo->setMaxLength( 16                                );
        $obTxtNroTermo->setSize     ( 21                                );
        $obTxtNroTermo->setNull     ( true                              );
        $obTxtNroTermo->setRotulo   ( '**Número do Termo de Parceria'   );
        $boMontaTermo = true;        
    }

    $obTxtNroTipoContratacao->setName       ( $stNomeTipoContratacao    );
    $obTxtNroTipoContratacao->setId         ( $stNomeTipoContratacao    );

    if($boMontaTipoContratacao||$boMontaTermo){
        $obFormulario = new Formulario();
        if($boMontaTipoContratacao)
            $obFormulario->addComponente( $obTxtNroTipoContratacao );
        if($boMontaTermo)
            $obFormulario->addComponente( $obTxtNroTermo );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        $stJs .= "jQuery('#spnNroTipoContratacao').html('".$stHtml."'); \n";
    }

    return $stJs;
}

function buscaContratado()
{
    $obRCGMPessoaFisica = new RCGMPessoaFisica;

    $stNull = "&nbsp;";

    if ($_REQUEST['inNumContratado'] != "" AND $_REQUEST['inNumContratado'] != "0") {
        $obRCGMPessoaFisica->setNumCGM ( $_REQUEST['inNumContratado'] );
        $obRCGMPessoaFisica->consultarCGM($rsCGM);

        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs  = "jQuery('input[name=inNumContratado]').val('');                                                            \n";
            $stJs .= "jQuery('input[name=inNumContratado]').focus();                                                            \n";
            $stJs .= "jQuery('#inNomContratado').html('".$stNull."');                                                           \n";
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST['inNumContratado'].")','form','erro','".Sessao::getId()."');   \n";
        } else {
            $stJs .= "jQuery('#inNomContratado').html('".($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull)."'); \n";
        }
    } else {
        $stJs  = "jQuery('input[name=inNumContratado]').val('');                                                                \n";
        $stJs .= "jQuery('#inNomContratado').html('".$stNull."');                                                               \n";
        if($_REQUEST['inNumContratado']=='0')
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST['inNumContratado'].")','form','erro','".Sessao::getId()."');   \n";
    }

    return $stJs;
}

function incluirContrato()
{
    $obErro  = new Erro();
    $arContrato = Sessao::read('arContrato');
    $arContrato = (is_array($arContrato)) ? $arContrato : array();
    $inId = count($arContrato);

    if($_REQUEST['dtFinalContrato']=='')
        $obErro->setDescricao('Informe a Data Final do Contrato da Obra.');
    else
        list($inDiaFinal, $inMesFinal, $inAnoFinal) = explode("/",$_REQUEST['dtFinalContrato']);

    if($_REQUEST['dtInicioContrato']=='')
        $obErro->setDescricao('Informe a Data de Início do Contrato da Obra.');
    else
        list($inDiaInicio, $inMesInicio, $inAnoInicio) = explode("/",$_REQUEST['dtInicioContrato']);

    if($_REQUEST['stFuncaoContratada']=='')
        $obErro->setDescricao('Informe a Função Contratada do Contrato da Obra.');

    if($_REQUEST['inNumContratado']=='')
        $obErro->setDescricao('Informe o CGM do Contratado da Contrato da Obra.');

    if(isset($_REQUEST['stNroTermo'])&&$_REQUEST['stNroTermo']=='')
        $obErro->setDescricao('Informe o Número do Termo de Parceria do Contrato da Obra.');

    if(isset($_REQUEST['stNroConvenio'])&&$_REQUEST['stNroConvenio']=='')
        $obErro->setDescricao('Informe o Número do Convênio do Contrato da Obra.');

    if(isset($_REQUEST['stNroContrato'])&&$_REQUEST['stNroContrato']=='')
        $obErro->setDescricao('Informe o Número do Contrato da Obra.');

    if(isset($_REQUEST['stNroInstrumento'])&&$_REQUEST['stNroInstrumento']=='')
        $obErro->setDescricao('Informe o Número do Instrumento do Contrato da Obra.');

    if($_REQUEST['inCodTipoContratacao']=='')
        $obErro->setDescricao('Informe o Tipo de Contratação do Contrato da Obra.');

    if(!$obErro->ocorreu()){
        if($inAnoFinal.$inMesFinal.$inDiaFinal < $inAnoInicio.$inMesInicio.$inDiaInicio)
            $obErro->setDescricao('A Data Final do Contrato na Obra, deve ser igual ou maior que a Data de Início do Contrato na Obra!');
        else{
            $dtInicio = $inAnoInicio.$inMesInicio.$inDiaInicio;
            $dtFinal  = $inAnoFinal.$inMesFinal.$inDiaFinal;
        }
    }

    if(!$obErro->ocorreu()){
        foreach( $arContrato as $key => $value) {
            list($inDiaInicioAnterior, $inMesInicioAnterior, $inAnoInicioAnterior) = explode("/",$value['dtInicioContrato']);
            list($inDiaFinalAnterior, $inMesFinalAnterior, $inAnoFinalAnterior) = explode("/",$value['dtFinalContrato']);
            $dtInicioAnt = $inAnoInicioAnterior.$inMesInicioAnterior.$inDiaInicioAnterior;
            $dtFinalAnt  = $inAnoFinalAnterior.$inMesFinalAnterior.$inDiaFinalAnterior;

            if($value['inNumContratado']==$_REQUEST['inNumContratado']){
                if( $dtInicio >= $dtInicioAnt  && $dtInicio <= $dtFinalAnt )
                    $obErro->setDescricao('O CGM Contratado informado, já está na Lista de Contratos da Obra!');

                if( $dtFinal >= $dtInicioAnt  && $dtFinal <= $dtFinalAnt )
                    $obErro->setDescricao('O CGM Contratado informado, já está na Lista de Contratos da Obra!');
            }
        }
    }

    if(!$obErro->ocorreu()){
        $obRCGM = new RCGM;
        $obRCGM->setNumCGM ($_REQUEST['inNumContratado']);
        $obRCGM->listar ($rsCGM);

        $obTTCMBATipoContratacaoObra = new TTCMBATipoContratacaoObra;
        $stFiltro = " WHERE cod_contratacao = ".$_REQUEST['inCodTipoContratacao'];
        $obTTCMBATipoContratacaoObra->recuperaTodos($rsTipoContratacao, "", $stOrder);

        $arContratoTemp = array();
        $arContratoTemp['inCodTipoContratacao']     = $_REQUEST['inCodTipoContratacao'];
        $arContratoTemp['inNomTipoContratacao']     = $rsTipoContratacao->getCampo('descricao');
        $arContratoTemp['stNroInstrumento']         = (isset($_REQUEST['stNroInstrumento']) ? $_REQUEST['stNroInstrumento'] : '');
        $arContratoTemp['stNroContrato']            = (isset($_REQUEST['stNroContrato']) ? $_REQUEST['stNroContrato'] : '');
        $arContratoTemp['stNroConvenio']            = (isset($_REQUEST['stNroConvenio']) ? $_REQUEST['stNroConvenio'] : '');
        $arContratoTemp['stNroTermo']               = (isset($_REQUEST['stNroTermo']) ? $_REQUEST['stNroTermo'] : '');
        $arContratoTemp['inNumContratado']          = $_REQUEST['inNumContratado'];
        $arContratoTemp['inNomContratado']          = $rsCGM->getCampo('nom_cgm');
        $arContratoTemp['stFuncaoContratada']       = $_REQUEST['stFuncaoContratada'];
        $arContratoTemp['dtInicioContrato']         = $_REQUEST['dtInicioContrato'];
        $arContratoTemp['dtFinalContrato']          = $_REQUEST['dtFinalContrato'];
        $arContratoTemp['stLotacao']                = $_REQUEST['stLotacao'];
        $arContratoTemp['inId']                     = $inId;   
    }

    if ($obErro->ocorreu()) {
        $stJs  = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');       \n";
    } else {
        $arContrato[] = $arContratoTemp;
        Sessao::write('arContrato', $arContrato);

        $stJs  = limparContrato();
        $stJs .= montaListaContrato();
    }

    return $stJs;
}

function montaListaContrato()
{
    $arContrato = Sessao::read('arContrato');
    $arContrato = (is_array($arContrato)) ? $arContrato : array();

    $rsRecordSet = new RecordSet();
    $rsRecordSet->preenche($arContrato);
    $rsRecordSet->ordena('inNumContratado');

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de Contratos de Mão de Obra e Serviços de Engenharia" );
    $obLista->setRecordSet( $rsRecordSet );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo de Contratação" );
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "CGM" );
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Início" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Final" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 1 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inNomTipoContratacao" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[inNumContratado] - [inNomContratado]" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtInicioContrato" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtFinalContrato" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('montaAlteraContrato');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirContrato');" );
    $obLista->ultimaAcao->addCampo("1" , "inId");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $stHtml = "";
    if(count($arContrato)>0){
        $stHtml = str_replace("\n","",$obLista->getHTML());
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }

    $stJs = "jQuery('#spnListaContrato').html('".$stHtml."');                                               \n";

    return $stJs;
}

function montaAlteraContrato()
{
    $arContrato = Sessao::read('arContrato');

    $stJs  = "var jQuery = window.parent.frames['telaPrincipal'].jQuery;                                    \n";
    $stJs .= limparContrato();
    $stJs .= "jQuery('#btnIncluirContrato').val('Alterar');                                                 \n";
    $stJs .= "jQuery('#btnIncluirContrato').attr('onclick', 'montaParametrosGET(\'alteraContrato\');');     \n";
    $stJs .= "jQuery('#inIdContrato').val('".$_REQUEST['inId']."');                                         \n";

    foreach( $arContrato as $key => $value) {
        if($value['inId']==$_REQUEST['inId']){
            $stJs .= "jQuery('#inCodTipoContratacao').val('".$value['inCodTipoContratacao']."');            \n";
            $stJs .= montaNroTipoContratacao($value['inCodTipoContratacao']);
            $stJs .= "if(jQuery('#stNroInstrumento'))                                                       \n";
            $stJs .= "  jQuery('#stNroInstrumento').val('".$value['stNroInstrumento']."');                  \n";
            $stJs .= "if(jQuery('#stNroContrato'))                                                          \n";
            $stJs .= "  jQuery('#stNroContrato').val('".$value['stNroContrato']."');                        \n";
            $stJs .= "if(jQuery('#stNroConvenio'))                                                          \n";
            $stJs .= "  jQuery('#stNroConvenio').val('".$value['stNroConvenio']."');                        \n";
            $stJs .= "if(jQuery('#stNroTermo'))                                                             \n";
            $stJs .= "  jQuery('#stNroTermo').val('".$value['stNroTermo']."');                              \n";
            $stJs .= "jQuery('#inNumContratado').val('".$value['inNumContratado']."');                      \n";
            $stJs .= "jQuery('#inNomContratado').html('".$value['inNomContratado']."');                     \n";
            $stJs .= "jQuery('#stFuncaoContratada').val('".$value['stFuncaoContratada']."');                \n";
            $stJs .= "jQuery('#dtInicioContrato').val('".$value['dtInicioContrato']."');                    \n";
            $stJs .= "jQuery('#dtFinalContrato').val('".$value['dtFinalContrato']."');                      \n";
            $stJs .= "jQuery('#stLotacao').val('".$value['stLotacao']."');                                  \n";
            break;
        }
    }

    return $stJs;
}

function alteraContrato()
{
    $obErro  = new Erro();
    $arContrato = Sessao::read('arContrato');
    $arContrato = (is_array($arContrato)) ? $arContrato : array();
    $inId = $_REQUEST['inIdContrato'];

    if($_REQUEST['dtFinalContrato']=='')
        $obErro->setDescricao('Informe a Data Final do Contrato da Obra.');
    else
        list($inDiaFinal, $inMesFinal, $inAnoFinal) = explode("/",$_REQUEST['dtFinalContrato']);

    if($_REQUEST['dtInicioContrato']=='')
        $obErro->setDescricao('Informe a Data de Início do Contrato da Obra.');
    else
        list($inDiaInicio, $inMesInicio, $inAnoInicio) = explode("/",$_REQUEST['dtInicioContrato']);

    if($_REQUEST['stFuncaoContratada']=='')
        $obErro->setDescricao('Informe a Função Contratada do Contrato da Obra.');

    if($_REQUEST['inNumContratado']=='')
        $obErro->setDescricao('Informe o CGM do Contratado da Contrato da Obra.');

    if(isset($_REQUEST['stNroTermo'])&&$_REQUEST['stNroTermo']=='')
        $obErro->setDescricao('Informe o Número do Termo de Parceria do Contrato da Obra.');

    if(isset($_REQUEST['stNroConvenio'])&&$_REQUEST['stNroConvenio']=='')
        $obErro->setDescricao('Informe o Número do Convênio do Contrato da Obra.');

    if(isset($_REQUEST['stNroContrato'])&&$_REQUEST['stNroContrato']=='')
        $obErro->setDescricao('Informe o Número do Contrato da Obra.');

    if(isset($_REQUEST['stNroInstrumento'])&&$_REQUEST['stNroInstrumento']=='')
        $obErro->setDescricao('Informe o Número do Instrumento do Contrato da Obra.');

    if($_REQUEST['inCodTipoContratacao']=='')
        $obErro->setDescricao('Informe o Tipo de Contratação do Contrato da Obra.');

    if(!$obErro->ocorreu()){
        if($inAnoFinal.$inMesFinal.$inDiaFinal < $inAnoInicio.$inMesInicio.$inDiaInicio)
            $obErro->setDescricao('A Data Final do Contrato na Obra, deve ser igual ou maior que a Data de Início do Contrato na Obra!');
        else{
            $dtInicio = $inAnoInicio.$inMesInicio.$inDiaInicio;
            $dtFinal  = $inAnoFinal.$inMesFinal.$inDiaFinal;
        }
    }

    if(!$obErro->ocorreu()){
        foreach( $arContrato as $key => $value) {
            list($inDiaInicioAnterior, $inMesInicioAnterior, $inAnoInicioAnterior) = explode("/",$value['dtInicioContrato']);
            list($inDiaFinalAnterior, $inMesFinalAnterior, $inAnoFinalAnterior) = explode("/",$value['dtFinalContrato']);
            $dtInicioAnt = $inAnoInicioAnterior.$inMesInicioAnterior.$inDiaInicioAnterior;
            $dtFinalAnt  = $inAnoFinalAnterior.$inMesFinalAnterior.$inDiaFinalAnterior;

            if($value['inNumContratado']==$_REQUEST['inNumContratado'] && $inId!=$value['inId']){
                if( $dtInicio >= $dtInicioAnt  && $dtInicio <= $dtFinalAnt )
                    $obErro->setDescricao('O CGM Contratado informado, já está na Lista de Contratos da Obra!');

                if( $dtFinal >= $dtInicioAnt  && $dtFinal <= $dtFinalAnt )
                    $obErro->setDescricao('O CGM Contratado informado, já está na Lista de Contratos da Obra!');
            }
        }
    }

    if(!$obErro->ocorreu()){
        $obRCGM = new RCGM;
        $obRCGM->setNumCGM ($_REQUEST['inNumContratado']);
        $obRCGM->listar ($rsCGM);

        $obTTCMBATipoContratacaoObra = new TTCMBATipoContratacaoObra;
        $stFiltro = " WHERE cod_contratacao = ".$_REQUEST['inCodTipoContratacao'];
        $obTTCMBATipoContratacaoObra->recuperaTodos($rsTipoContratacao, "", $stOrder);

        foreach( $arContrato as $key => $value) {
            if($inId==$value['inId']){
                $arContrato[$key]['inCodTipoContratacao']   = $_REQUEST['inCodTipoContratacao'];
                $arContrato[$key]['inNomTipoContratacao']   = $rsTipoContratacao->getCampo('descricao');
                $arContrato[$key]['stNroInstrumento']       = (isset($_REQUEST['stNroInstrumento']) ? $_REQUEST['stNroInstrumento'] : '');
                $arContrato[$key]['stNroContrato']          = (isset($_REQUEST['stNroContrato']) ? $_REQUEST['stNroContrato'] : '');
                $arContrato[$key]['stNroConvenio']          = (isset($_REQUEST['stNroConvenio']) ? $_REQUEST['stNroConvenio'] : '');
                $arContrato[$key]['stNroTermo']             = (isset($_REQUEST['stNroTermo']) ? $_REQUEST['stNroTermo'] : '');
                $arContrato[$key]['inNumContratado']        = $_REQUEST['inNumContratado'];
                $arContrato[$key]['inNomContratado']        = $rsCGM->getCampo('nom_cgm');
                $arContrato[$key]['stFuncaoContratada']     = $_REQUEST['stFuncaoContratada'];
                $arContrato[$key]['dtInicioContrato']       = $_REQUEST['dtInicioContrato'];
                $arContrato[$key]['dtFinalContrato']        = $_REQUEST['dtFinalContrato'];
                $arContrato[$key]['stLotacao']              = $_REQUEST['stLotacao'];
            }
        }
    }

    if ($obErro->ocorreu()) {
        $stJs  = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');           \n";
    } else {
        Sessao::write('arContrato', $arContrato);

        $stJs  = limparContrato();
        $stJs .= montaListaContrato();
    }

    return $stJs;
}

function excluirContrato()
{
    $obErro  = new Erro();
    $arContrato = Sessao::read('arContrato');
    $arContrato = (is_array($arContrato)) ? $arContrato : array();
    $arContratoTemp = array();
    $inId=0;

    foreach( $arContrato as $key => $value) {
        if($value['inId']!=$_REQUEST['inId']){
            $arContratoTemp[$inId] = $value;
            $arContratoTemp[$inId]['inId'] = $inId;
            $inId++;
        }
    }

    Sessao::write('arContrato', $arContratoTemp);
    $stJs  = "var jQuery = window.parent.frames['telaPrincipal'].jQuery;                                    \n";
    $stJs .= limparContrato();
    $stJs .= montaListaContrato();

    return $stJs;
}

function limparContrato()
{
    $stJs  = "jQuery('#inCodTipoContratacao').val('');                                                      \n";
    $stJs .= "jQuery('#spnNroTipoContratacao').html('');                                                    \n";
    $stJs .= "jQuery('#inNumContratado').val('');                                                           \n";
    $stJs .= "jQuery('#inNomContratado').html('&nbsp;');                                                    \n";
    $stJs .= "jQuery('#stFuncaoContratada').val('');                                                        \n";
    $stJs .= "jQuery('#dtInicioContrato').val('');                                                          \n";
    $stJs .= "jQuery('#dtFinalContrato').val('');                                                           \n";
    $stJs .= "jQuery('#stLotacao').val('');                                                                 \n";
    $stJs .= "jQuery('#btnIncluirContrato').val('Incluir');                                                 \n";
    $stJs .= "jQuery('#btnIncluirContrato').attr('onclick', 'montaParametrosGET(\'incluirContrato\');');    \n";
    $stJs .= "jQuery('#inIdContrato').val('');                                                              \n";

    return $stJs;
}

function montaObra()
{
    $stFiltro  = " WHERE cod_entidade = ".$_REQUEST['inCodEntidade'];
    $stFiltro .= "   AND exercicio = '".$_REQUEST['stExercicio']."'";
    $stFiltro .= "   AND cod_tipo = ".$_REQUEST['inCodTipoObra'];
    $stFiltro .= "   AND cod_obra = ".$_REQUEST['inCodObra'];

    $obTTCMBAObraAndamento = new TTCMBAObraAndamento();
    $obTTCMBAObraAndamento->recuperaTodos($rsObraAndamento, $stFiltro);
    $arAndamento = array();
    $inId = 0;
    while (!($rsObraAndamento->eof())) {
        $obTTCMBASituacaoObra = new TTCMBASituacaoObra;
        $stFiltroSituacao = " WHERE cod_situacao = ".$rsObraAndamento->getCampo('cod_situacao');
        $obTTCMBASituacaoObra->recuperaTodos($rsSituacaoObra, $stFiltroSituacao);

        $arAndamento[$inId]['inSituacaoObra']  = $rsObraAndamento->getCampo('cod_situacao');
        $arAndamento[$inId]['stSituacaoObra']  = $rsSituacaoObra->getCampo('descricao');
        $arAndamento[$inId]['dtSituacao']      = $rsObraAndamento->getCampo('data_situacao');
        $arAndamento[$inId]['stJustificativa'] = $rsObraAndamento->getCampo('justificativa');
        $arAndamento[$inId]['inId']            = $inId;
        $inId++;

        $rsObraAndamento->proximo();
    }

    $obTTCMBAObraFiscal = new TTCMBAObraFiscal();
    $obTTCMBAObraFiscal->recuperaTodos($rsObraFiscal, $stFiltro);
    $arFiscal = array();
    $inId = 0;
    while (!($rsObraFiscal->eof())) {
        $obRCGM = new RCGM;
        $obRCGM->setNumCGM ($rsObraFiscal->getCampo('numcgm'));
        $obRCGM->listar ($rsCGM);

        $arFiscal[$inId]['inNumResponsavelFiscal'] = $rsObraFiscal->getCampo('numcgm');
        $arFiscal[$inId]['inNomResponsavelFiscal'] = $rsCGM->getCampo('nom_cgm');
        $arFiscal[$inId]['stMatricula']            = $rsObraFiscal->getCampo('matricula');
        $arFiscal[$inId]['stRegistro']             = $rsObraFiscal->getCampo('registro_profissional');
        $arFiscal[$inId]['dtInicioFiscal']         = $rsObraFiscal->getCampo('data_inicio');
        $arFiscal[$inId]['dtFinalFiscal']          = $rsObraFiscal->getCampo('data_final');
        $arFiscal[$inId]['inId']                   = $inId;
        $inId++;

        $rsObraFiscal->proximo();
    }

    $obTTCMBAObraMedicao = new TTCMBAObraMedicao();
    $obTTCMBAObraMedicao->recuperaTodos($rsObraMedicao, $stFiltro);
    $arMedicao = array();
    $inId = 0;
    while (!($rsObraMedicao->eof())) {
        $obRCGM = new RCGM;
        $obRCGM->setNumCGM ($rsObraMedicao->getCampo('numcgm'));
        $obRCGM->listar ($rsCGM);

        $arMedicao[$inId]['inNroMedicao']          = $rsObraMedicao->getCampo('cod_medicao');
        $arMedicao[$inId]['inCodMedidaObra']       = $rsObraMedicao->getCampo('cod_medida');
        $arMedicao[$inId]['dtInicioMedicao']       = $rsObraMedicao->getCampo('data_inicio');
        $arMedicao[$inId]['dtFinalMedicao']        = $rsObraMedicao->getCampo('data_final');
        $arMedicao[$inId]['dtMedicao']             = $rsObraMedicao->getCampo('data_medicao');
        $arMedicao[$inId]['nuVlMedicao']           = number_format($rsObraMedicao->getCampo('vl_medicao'), 2, ",", ".");
        $arMedicao[$inId]['stNFMedicao']           = $rsObraMedicao->getCampo('nro_nota_fiscal');
        $arMedicao[$inId]['dtNFMedicao']           = $rsObraMedicao->getCampo('data_nota_fiscal');
        $arMedicao[$inId]['inNumAtestadorMedicao'] = $rsObraMedicao->getCampo('numcgm');
        $arMedicao[$inId]['inNomAtestadorMedicao'] = $rsCGM->getCampo('nom_cgm');
        $arMedicao[$inId]['inId']                  = $inId;
        $inId++;

        $rsObraMedicao->proximo();
    }

    $obTTCMBAObraContratos = new TTCMBAObraContratos();
    $obTTCMBAObraContratos->recuperaTodos($rsObraContratos, $stFiltro);
    $arContrato = array();
    $inId = 0;
    while (!($rsObraContratos->eof())) {
        $obRCGM = new RCGM;
        $obRCGM->setNumCGM ($rsObraContratos->getCampo('numcgm'));
        $obRCGM->listar ($rsCGM);

        $obTTCMBATipoContratacaoObra = new TTCMBATipoContratacaoObra;
        $stFiltro = " WHERE cod_contratacao = ".$rsObraContratos->getCampo('cod_contratacao');
        $obTTCMBATipoContratacaoObra->recuperaTodos($rsTipoContratacao, "", $stOrder);

        $arContrato[$inId]['inCodTipoContratacao']     = $rsObraContratos->getCampo('cod_contratacao');
        $arContrato[$inId]['inNomTipoContratacao']     = $rsTipoContratacao->getCampo('descricao');
        $arContrato[$inId]['stNroInstrumento']         = $rsObraContratos->getCampo('nro_instrumento');
        $arContrato[$inId]['stNroContrato']            = $rsObraContratos->getCampo('nro_contrato');
        $arContrato[$inId]['stNroConvenio']            = $rsObraContratos->getCampo('nro_convenio');
        $arContrato[$inId]['stNroTermo']               = $rsObraContratos->getCampo('nro_parceria');
        $arContrato[$inId]['inNumContratado']          = $rsObraContratos->getCampo('numcgm');
        $arContrato[$inId]['inNomContratado']          = $rsCGM->getCampo('nom_cgm');
        $arContrato[$inId]['stFuncaoContratada']       = $rsObraContratos->getCampo('funcao_cgm');
        $arContrato[$inId]['dtInicioContrato']         = $rsObraContratos->getCampo('data_inicio');
        $arContrato[$inId]['dtFinalContrato']          = $rsObraContratos->getCampo('data_final');
        $arContrato[$inId]['stLotacao']                = $rsObraContratos->getCampo('lotacao');
        $arContrato[$inId]['inId']                     = $inId;
        $inId++;

        $rsObraContratos->proximo();
    }

    Sessao::write('arAndamento' , $arAndamento  );
    Sessao::write('arFiscal'    , $arFiscal     );
    Sessao::write('arMedicao'   , $arMedicao    );
    Sessao::write('arContrato'  , $arContrato   );

    $stJs  = montaListaAndamento();
    $stJs .= montaListaFiscal();
    $stJs .= montaListaMedicao();
    $stJs .= montaListaContrato();

    return $stJs;
}

// Acoes por pagina
switch ($stCtrl) {
    case "montaBairro":
        $stJs .= montaBairro();
    break;

    case "carregaLicitacao":
        $stJs .= carregaLicitacao();
    break;

    case "incluirAndamento":
        $stJs .= incluirAndamento();
    break;

    case "montaAlteraAndamento":
        $js .= montaAlteraAndamento();
    break;

    case "alteraAndamento":
        $stJs .= alteraAndamento();
    break;

    case "excluirAndamento":
        $js .= excluirAndamento();
    break;

    case "limparAndamento":
        $stJs .= limparAndamento();
    break;

    case "LimparForm":
        $stJs .= LimparForm();
    break;

    case "montaJustificativa":
        $stJs .= montaJustificativa();
    break;

    case "buscaResponsavelFiscal":
        $stJs .= buscaResponsavelFiscal();
    break;

    case "incluirFiscal":
        $stJs .= incluirFiscal();
    break;

    case "limparFiscal":
        $stJs .= limparFiscal();
    break;

    case "montaAlteraFiscal":
        $js .= montaAlteraFiscal();
    break;

    case "alteraFiscal":
        $stJs .= alteraFiscal();
    break;

    case "excluirFiscal":
        $js .= excluirFiscal();
    break;

    case "buscaAtestadorMedicao":
        $stJs .= buscaAtestadorMedicao();
    break;

    case "incluirMedicao":
        $stJs .= incluirMedicao();
    break;

    case "montaAlteraMedicao":
        $js .= montaAlteraMedicao();
    break;

    case "alteraMedicao":
        $stJs .= alteraMedicao();
    break;

    case "excluirMedicao":
        $js .= excluirMedicao();
    break;

    case "limparMedicao":
        $stJs .= limparMedicao();
    break;

    case "montaNroTipoContratacao":
        $stJs .= montaNroTipoContratacao();
    break;

    case "buscaContratado":
        $stJs .= buscaContratado();
    break;

    case "incluirContrato":
        $stJs .= incluirContrato();
    break;

    case "montaAlteraContrato":
        $js .= montaAlteraContrato();
    break;

    case "alteraContrato":
        $stJs .= alteraContrato();
    break;

    case "excluirContrato":
        $js .= excluirContrato();
    break;

    case "limparContrato":
        $stJs .= limparContrato();
    break;

    case "montaObra":
        $stJs .= montaObra();
    break;
}

if ($stJs) {
    echo ($stJs);
}
if ($js) {
    SistemaLegado::executaFrameOculto($js);
}

?>

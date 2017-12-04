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
    * Página do Oculto
    * Data de Criação   : 01/09/2008

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
     * @subpackage

    * @ignore

    $Id:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

switch ($stCtrl) {

    case "carregaDados":
        if ($_REQUEST['inNumContrato'] and $_REQUEST['stExercicioContrato']) {

            include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContrato.class.php" );
            $obTTCMGOContrato = new TTCMGOContrato;
            $stFiltro  = " WHERE nro_contrato =  ".$_REQUEST['inNumContrato'];
            $stFiltro .= "   AND exercicio    = '".$_REQUEST['stExercicioContrato']."'";
            $stFiltro .= "   AND cod_entidade = '".$_REQUEST['inCodEntidade']."'";
            $obTTCMGOContrato->recuperaTodos($rsContrato, $stFiltro);
            $rsContrato->addFormatacao( 'vl_contrato', 'NUMERIC_BR');

            include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContratoAssunto.class.php" );
            $obTTCMGOContratoAssunto = new TTCMGOContratoAssunto;
            $stFiltro  = " WHERE cod_assunto =  ".$rsContrato->getCampo('cod_assunto');
            $obTTCMGOContratoAssunto->recuperaTodos($rsAssunto, $stFiltro);

            include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContratoModalidadeLicitacao.class.php" );
            $obTTCMGOContratoModalidadeLicitacao = new TTCMGOContratoModalidadeLicitacao;
            $stFiltro  = " WHERE cod_modalidade =  ".$rsContrato->getCampo('cod_modalidade');
            $obTTCMGOContratoModalidadeLicitacao->recuperaTodos($rsModalidade, $stFiltro);

            include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContratoTipo.class.php" );
            $obTTCMGOContratoTipo = new TTCMGOContratoTipo;
            $stFiltro  = " WHERE cod_tipo =  ".$rsContrato->getCampo('cod_tipo');
            $obTTCMGOContratoTipo->recuperaTodos($rsTipo, $stFiltro);

            include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContratoEmpenho.class.php" );
            $obTTCMGOContratoEmpenho = new TTCMGOContratoEmpenho;
            $stFiltro  = " WHERE cod_contrato =  ".$rsContrato->getCampo('cod_contrato');
            $stFiltro .= "   AND exercicio    = '".$rsContrato->getCampo('exercicio')."'";
            $stFiltro .= "   AND cod_entidade = '".$_REQUEST['inCodEntidade']."'";
            $obTTCMGOContratoEmpenho->recuperaTodos($rsEmpenhoContrato, $stFiltro);

            $arEmpenhos = array();
            $inCount = 0;

            include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            while ( !$rsEmpenhoContrato->eof()) {
                $stFiltro  = "   AND e.exercicio    = '".$rsEmpenhoContrato->getCampo('exercicio')."'";
                $stFiltro .= "   AND e.cod_entidade =  ".$rsEmpenhoContrato->getCampo('cod_entidade');
                $stFiltro .= "   AND e.cod_empenho  =  ".$rsEmpenhoContrato->getCampo('cod_empenho');
                $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenhoCgm($rsEmpenho, $stFiltro);

                $arEmpenhos[$inCount]['cod_entidade'] = $rsEmpenho->getCampo('cod_entidade');
                $arEmpenhos[$inCount]['cod_empenho']  = $rsEmpenho->getCampo('cod_empenho');
                $arEmpenhos[$inCount]['exercicio']    = $rsEmpenho->getCampo('exercicio');
                $arEmpenhos[$inCount]['nom_cgm']      = $rsEmpenho->getCampo('credor');

                $inCount++;
                $rsEmpenhoContrato->proximo();
            }

            $stJs .= "f.cod_modalidade.value = '".$rsModalidade->getCampo('cod_modalidade')."';\n";
            $stJs .= "f.cod_tipo.value       = '".$rsTipo->getCampo('cod_tipo')."';\n";

            if (($rsContrato->getCampo('numero_termo') != '') && ( $rsTipo->getCampo('cod_tipo') == 2 )) {
                $obTxtTermoAditivo = new TextBox;
                $obTxtTermoAditivo->setName   ( "stTermoAditivo"            );
                $obTxtTermoAditivo->setId     ( "stTermoAditivo"            );
                $obTxtTermoAditivo->setRotulo ( "Número Termo Aditivo"      );
                $obTxtTermoAditivo->setNull   ( true                         );
                $obTxtTermoAditivo->setInteiro   ( true                         );
                $obTxtTermoAditivo->setMaxLength( 4                          );
                $obTxtTermoAditivo->setSize   ( 10                           );
                $obTxtTermoAditivo->setValue($rsContrato->getCampo('numero_termo')) ;
                $obFormulario = new Formulario();
                $obFormulario->addComponente( $obTxtTermoAditivo );
                $obFormulario->montaInnerHtml();

                $stJs .= "d.getElementById('spnTermoAditivo').innerHTML = '".$obFormulario->getHTML()."';\n";
            }

            $stJs .= "f.cod_assunto.value    = '".$rsAssunto->getCampo('cod_assunto')."';\n";
            $stJs .= "f.stSubAssunto.value   = '".$rsContrato->getCampo('cod_sub_assunto')."';\n";
            $stJs .= "f.stObjContrato.value  = '".stripslashes($rsContrato->getCampo('objeto_contrato'))."';\n";
            $stJs .= "f.dtPublicacao.value   = '".$rsContrato->getCampo('data_publicacao')."';\n";
            $stJs .= "f.dtInicial.value      = '".$rsContrato->getCampo('data_inicio')."';\n";
            $stJs .= "f.dtFinal.value        = '".$rsContrato->getCampo('data_final')."';\n";
            $stJs .= "f.nuVlContrato.value   = '".number_format($rsContrato->getCampo('vl_contrato'),2,',','.')."';\n";
            $stJs .= "f.inCodContrato.value  = '".$rsContrato->getCampo('cod_contrato')."';\n";
            $stJs .= "f.cod_entidade.value   = '".$rsContrato->getCampo('cod_entidade')."';\n";
            $stJs .= "f.inNumProcesso.value  = '".$rsContrato->getCampo('nro_processo')."';\n";
            $stJs .= "f.stAnoProcesso.value  = '".$rsContrato->getCampo('ano_processo')."';\n";

            $stJs .= "f.inPrazo.value            = '".$rsContrato->getCampo('prazo')."';\n";
            $stJs .= "f.nuVlAcrescimo.value      = '".number_format($rsContrato->getCampo('vl_acrescimo'),2,',','.')."';\n";
            $stJs .= "f.nuVlDecrescimo.value     = '".number_format($rsContrato->getCampo('vl_decrescimo'),2,',','.')."';\n";
            $stJs .= "f.dtRescisao.value         = '".$rsContrato->getCampo('dt_rescisao')."';\n";
            $stJs .= "f.nuVlContratual.value     = '".number_format($rsContrato->getCampo('vl_contratual'),2,',','.')."';\n";
            $stJs .= "f.nuVlFinalContrato.value  = '".number_format($rsContrato->getCampo('vl_final_contrato'),2,',','.')."';\n";
            $stJs .= "f.dtFirmatura.value        = '".$rsContrato->getCampo('dt_firmatura')."';\n";
            $stJs .= "f.dtLancamento.value       = '".$rsContrato->getCampo('dt_lancamento')."';\n";

            Sessao::write('arEmpenhos', $arEmpenhos);
            $stJs .= "f.btnLimpar.disabled = false; ";
            $stJs .= "f.btnIncluir.disabled = false; ";
            $stJs .= "f.inCodEntidade.disabled = true; ";
            $stJs .= "f.stNomEntidade.disabled = true; ";

            $stJs .= montaListaEmpenhos();

        }

        echo $stJs;
    break;

    case "limpaCampoEmpenho":

        $stJs  = 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
        $stJs .= "f.numEmpenho.value = '';";

        echo $stJs;

    break;

    case "incluirEmpenhoLista":

        $arRegistro = array();
        $arEmpenhos = array();
        $arRequest  = array();
        $arRequest  = explode('/', $_REQUEST['numEmpenho']);
        $boIncluir  = true;

        $arEmpenhos = Sessao::read('arEmpenhos');

        if ($_REQUEST['stExercicioEmpenho'] and $arRequest[0] != "") {

            include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContratoEmpenho.class.php" );
            $obTTCMGOContratoEmpenho = new TTCMGOContratoEmpenho;
            $stFiltro  = " WHERE cod_empenho       =  ".$arRequest[0];
            $stFiltro .= "   AND cod_entidade      =  ".$_REQUEST['inCodEntidade'];
            $stFiltro .= "   AND exercicio_empenho = '".$_REQUEST['stExercicioEmpenho']."'";
            $obTTCMGOContratoEmpenho->recuperaTodos($rsEmpenhoContrato, $stFiltro);

            if ($rsEmpenhoContrato->getNumLinhas() == -1) {

                include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
                $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
                $stFiltro  = "   AND e.exercicio    = '".$_REQUEST['stExercicioEmpenho']."'";
                $stFiltro .= "   AND e.cod_entidade =  ".$_REQUEST['inCodEntidade'];
                $stFiltro .= "   AND e.cod_empenho  =  ".$arRequest[0];
                $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenhoCgm($rsRecordSet, $stFiltro);

                if ( $rsRecordSet->getNumLinhas() > 0 ) {
                    if ( !SistemaLegado::comparaDatas($_REQUEST['dtInicial'],$rsRecordSet->getCampo('dt_empenho') )) {
                        if ( count( $arEmpenhos ) > 0 ) {
                            foreach ($arEmpenhos as $key => $array) {
                                $stCod = $array['cod_empenho'];
                                $stEnt = $array['cod_entidade'];

                                if ($arRequest[0] == $stCod and $_REQUEST['inCodEntidade'] == $stEnt) {
                                    $boIncluir = false;
                                    $stJs .= "alertaAviso('Empenho já incluso na lista.','form','erro','".Sessao::getId()."');";
                                    break;
                                }
                            }
                        }
                        if ($boIncluir) {
                            $arRegistro['cod_entidade'] = $rsRecordSet->getCampo('cod_entidade');
                            $arRegistro['cod_empenho' ] = $rsRecordSet->getCampo('cod_empenho');
                            $arRegistro['data_empenho'] = $rsRecordSet->getCampo('dt_empenho');
                            $arRegistro['nom_cgm'     ] = $rsRecordSet->getCampo('credor');
                            $arRegistro['exercicio'   ] = $rsRecordSet->getCampo('exercicio');
                            $arEmpenhos[] = $arRegistro ;

                            Sessao::write('arEmpenhos', $arEmpenhos);
                            $stJs .= "f.inCodEntidade.disabled = true; ";
                            $stJs .= "f.stNomEntidade.disabled = true; ";
                            $stJs .= "f.cod_entidade.value = ".$_REQUEST['inCodEntidade']."; ";
                            $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                            $stJs .= "f.stEmpenho.value = '';";
                            $stJs .= "f.numEmpenho.value = '';";
                            $stJs .= "f.numEmpenho.focus();";
                            $stJs .= montaListaEmpenhos();
                        }
                    } else {
                        $stJs .= "alertaAviso('Início do período do contrato posterior a data do empenho.','form','erro','".Sessao::getId()."');";
                    }
                } else {
                    $stJs .= "alertaAviso('Empenho informado inválido.','form','erro','".Sessao::getId()."');";
                }
            } else {
                $stJs .= "alertaAviso('Empenho já vinculado a um contrato.','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "alertaAviso('Informe o código de empenho e exercício.','form','erro','".Sessao::getId()."');";
        }
        echo $stJs;
    break;

    case "excluirEmpenhoLista":

        $arTempEmp = array();
        $arEmpenhos = Sessao::read('arEmpenhos');

        foreach ($arEmpenhos as $registro) {
            if ($registro['cod_empenho'].$registro['cod_entidade'].$registro['exercicio'] != $_REQUEST['codEmpenho'].$_REQUEST['codEntidade'].$_REQUEST['stExercicio']) {
                $arTempEmp[] = $registro;
            }
        }

        if (count($arTempEmp) == 0) {
            $stJs .= "f.inCodEntidade.disabled = false; ";
            $stJs .= "f.stNomEntidade.disabled = false; ";
        }

        Sessao::write('arEmpenhos', $arTempEmp);
        $stJs .= montaListaEmpenhos();

        echo $stJs;
    break;

    case "limpar":

             $stJs  = 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
             $stJs .= "f.numEmpenho.value = '';";
             $stJs .= "f.numEmpenho.focus();";

        echo $stJs;
    break;

    case "comparaData":

        $arData = array();
        $arData = explode('/',$_REQUEST['dtInicial']);

        if ($arData[2] !=  Sessao::getExercicio()) {
            $stJs  = "f.dtInicial.value = '';";
            $stJs .= "f.dtInicial.focus();\n";
            $stJs .= "alertaAviso('Data Inicial do contrato deve estar no mesmo período do exercício.','form','erro','".Sessao::getId()."');\n";
        }

        if ($_REQUEST['dtInicial'] != "" and $_REQUEST['dtFinal'] != "") {
            if ( SistemaLegado::comparaDatas($_REQUEST['dtInicial'],$_REQUEST['dtFinal']) ) {
                $stJs  = "f.dtFinal.value = '';";
                $stJs .= "f.dtFinal.focus();\n";
                $stJs .= "alertaAviso('Data Final do contrato anterior a Data Inicial.','form','erro','".Sessao::getId()."');\n";
            } else {
                if ($_REQUEST['dtPublicacao'] != "") {
                    if (SistemaLegado::comparaDatas($_REQUEST['dtPublicacao'],$_REQUEST['dtInicial'])) {
                        $stJs  = "f.dtPublicacao.value = '';";
                        $stJs .= "f.dtPublicacao.focus();\n";
                        $stJs .= "alertaAviso('Data de Publicação posterior a Data Inicial do contrato.','form','erro','".Sessao::getId()."');\n";
                    } else {
                        $stJs  = "f.btnLimpar.disabled = false; ";
                        $stJs .= "f.btnIncluir.disabled = false; ";
                    }
                }
            }
        } elseif ($_REQUEST['dtInicial'] == "") {
            $stJs  = "f.btnLimpar.disabled = true; ";
            $stJs .= "f.btnIncluir.disabled = true; ";
        }
        echo $stJs;

    break;

    case "preencheInner":

        if ($_REQUEST['inCodEntidade'] and $_REQUEST['stExercicioEmpenho']) {

            include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $stFiltro  = "   AND e.exercicio    = '".$_REQUEST['stExercicioEmpenho']."'";
            $stFiltro .= "   AND e.cod_entidade =  ".$_REQUEST['inCodEntidade'];
            $stFiltro .= "   AND e.cod_empenho  =  ".$_REQUEST['numEmpenho'];
            $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenhoCgm($rsRecordSet, $stFiltro);

            if ($rsRecordSet->getNumLinhas() > 0) {
                $stJs  = 'd.getElementById("stEmpenho").innerHTML = "'.$rsRecordSet->getCampo('credor').'";';
            } else {
                $stJs  = "alertaAviso('Empenho inexistente.','form','erro','".Sessao::getId()."');\n";
                $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                $stJs .= "f.numEmpenho.value = '';";
                $stJs .= "f.numEmpenho.focus();\n";
            }
        } else {
            if (!$_REQUEST['inCodEntidade']) {
                $stJs  = "alertaAviso('Informe a entidade.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.inCodEntidade.focus();\n";
                $stJs .= "f.numEmpenho.value = '';";
            }
            if (!$_REQUEST['stExercicioEmpenho']) {
                $stJs  = "alertaAviso('Informe o exercício do empenho.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.stExercicioEmpenho.focus();\n";
                $stJs .= "f.numEmpenho.value = '';";
            }
        }

        echo $stJs;

    break;
    case 'subAssunto' :
        if ($_REQUEST['subAssunto'] == 99) {
            $obTxtDetAssunto = new TextArea;
            $obTxtDetAssunto->setName   ( "stDetSubAssunto"            );
            $obTxtDetAssunto->setId     ( "stDetSubAssunto"            );
            $obTxtDetAssunto->setRotulo ( "Detalhamento Sub-Assunto"   );
            $obTxtDetAssunto->setNull   ( true                         );
            $obTxtDetAssunto->setRows   ( 6                            );
            $obTxtDetAssunto->setCols   ( 100                          );
            $obTxtDetAssunto->setMaxCaracteres( 200                    );

            $obFormulario = new Formulario();
            $obFormulario->addComponente( $obTxtDetAssunto );
            $obFormulario->montaInnerHtml();

            $stJs = "d.getElementById('spnSubAssunto').innerHTML = '".$obFormulario->getHTML()."';";

        } else {
            $stJs = "d.getElementById('spnSubAssunto').innerHTML = '';";
        }
        echo $stJs;
    break;

    case 'tipoContrato':
        if (( $_REQUEST['cod_tipo'] == 2 ) && (Sessao::getExercicio() > 2010 )) {
            $obTxtTermoAditivo = new TextBox;
            $obTxtTermoAditivo->setName   ( "stTermoAditivo"            );
            $obTxtTermoAditivo->setId     ( "stTermoAditivo"            );
            $obTxtTermoAditivo->setRotulo ( "* Número Termo Aditivo"      );
            $obTxtTermoAditivo->setNull   ( true                         );
            $obTxtTermoAditivo->setInteiro   ( true                         );
            $obTxtTermoAditivo->setMaxLength( 4                          );
            $obTxtTermoAditivo->setSize   ( 10                           );

            $obFormulario = new Formulario();
            $obFormulario->addComponente( $obTxtTermoAditivo );
            $obFormulario->montaInnerHtml();

            $stJs = "d.getElementById('spnTermoAditivo').innerHTML = '".$obFormulario->getHTML()."';";

        } else {
            $stJs = "d.getElementById('spnTermoAditivo').innerHTML = '';";

        }
        echo $stJs;
    break;

}

function montaListaEmpenhos()
{
    $obLista = new Lista;
    $rsLista = new RecordSet;
    $rsLista->preenche ( Sessao::read('arEmpenhos') );

    $obLista->setRecordset( $rsLista );
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo ( 'Lista de empenhos' );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Entidade");
    $obLista->ultimoCabecalho->setWidth( 5);
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Empenho");
    $obLista->ultimoCabecalho->setWidth( 10);
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome do Credor");
    $obLista->ultimoCabecalho->setWidth( 80 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_entidade" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio]" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('excluirEmpenhoLista');" );
    $obLista->ultimaAcao->addCampo("","&codEmpenho=[cod_empenho]&codEntidade=[cod_entidade]&stExercicio=[exercicio]");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs .= "d.getElementById('spnLista').innerHTML = '';\n";
    $stJs .= "d.getElementById('spnLista').innerHTML = '".$html."';\n";

    return $stJs;

}

?>

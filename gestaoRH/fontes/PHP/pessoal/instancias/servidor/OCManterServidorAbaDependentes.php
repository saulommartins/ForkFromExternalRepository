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
    * Página processamento ocuto Pessoal ServidorP
    * Data de Criação   : 14/12/2004
    *

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @ignore

    $Revision: 30962 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-25 11:14:08 -0300 (Ter, 25 Mar 2008) $

    * Casos de uso: uc-04.04.07
*/

include_once ('../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php'    );   
include_once ('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php' );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"                                     );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoSalarioFamilia.class.php"                       );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCID.class.php"                                      );

function preencheDadosCGMDependente()
{
    $stDtNascimento = "&nbsp;";
    $stSexoDependente = "&nbsp;";
    $stNomeCGM        = "&nbsp;";
    $rsCGMPessoaFisica = new Recordset;
    $inCGM = $_GET['inCGMDependente'];
    if ($inCGM != "") {
        $RCGMPessoaFisica = new RCGMPessoaFisica;
        $RCGMPessoaFisica->setNumCGM( $inCGM );
        $RCGMPessoaFisica->consultarCGM( $rsCGMPessoaFisica );
        if ( $rsCGMPessoaFisica->getNumLinhas() > 0 ) {
            if ( $rsCGMPessoaFisica->getCampo('dt_nascimento') != "" ) {
                Sessao::write('boincluirDataNascimentoDespendente',false);
            } else {
                Sessao::write('boincluirDataNascimentoDespendente',true);
            }
            $stJs .= montaSpanDataNascimentoDependente($rsCGMPessoaFisica->getCampo('dt_nascimento'));

            if ( $rsCGMPessoaFisica->getCampo("sexo") == "f" ) {
                $stSexoDependente = "Feminino";
            } else {
                $stSexoDependente = "Masculino";
            }
            $stNomeCGM = addslashes($rsCGMPessoaFisica->getCampo('nom_cgm'));
        } else {
            $stJs .= "d.getElementById('spnDataNascimentoDependente').innerHTML = '';\n";
        }
    } else {
        $stJs .= "d.getElementById('spnDataNascimentoDependente').innerHTML = '';\n";
    }

    $stJs .= "d.getElementById('stNomDependente').innerHTML = '".$stNomeCGM."';\n";
    $stJs .= "d.getElementById('stSexoDependente').innerHTML = '$stSexoDependente'; \n";
    $stJs .= "f.stNomDependente.value                        = '$stNomeCGM'; \n";
    $stJs .= "f.stSexoDependente.value = '$stSexoDependente';\n";
    echo $stJs;
}

function montaSpanDataNascimentoDependente($dtNascimentoDependente)
{
    if ( !Sessao::read('boincluirDataNascimentoDespendente') ) {
        if ( strpos($dtNascimentoDependente,"-") ) {
            $arDtNascimento = explode( '-' , $dtNascimentoDependente );
            $stDtNascimento = $arDtNascimento[2] . '/' . $arDtNascimento[1] . '/' . $arDtNascimento[0];
        } else {
            $stDtNascimento = $dtNascimentoDependente;
        }

        $obLblDataNascimentoDependente = new Label;
        $obLblDataNascimentoDependente->setRotulo ( "Data de Nascimento"         );
        $obLblDataNascimentoDependente->setValue  ( $stDtNascimento              );
        $obLblDataNascimentoDependente->setId     ( "stDataNascimentoDependente" );

        $obHdnDataNascimentoDependente = new Hidden;
        $obHdnDataNascimentoDependente->setName("stDtNascimentoDependente");
        $obHdnDataNascimentoDependente->setValue( $stDtNascimento );

        $obFormulario = new Formulario;
        $obFormulario->addComponente        ( $obLblDataNascimentoDependente                                    );
        $obFormulario->addHidden            ( $obHdnDataNascimentoDependente                                    );
        $obFormulario->montaInnerHTML();
        $obFormulario->obJavaScript->montaJavaScript();
        $stHtml = $obFormulario->getHTML();
    } else {
        $obDtaNascimentoDependente = new Data;
        $obDtaNascimentoDependente->setName   ( 'stDtNascimentoDependente'                             );
        $obDtaNascimentoDependente->setTitle  ( 'Informe a data de nascimento do dependente.'          );
        $obDtaNascimentoDependente->setNull   ( false                                                  );
        $obDtaNascimentoDependente->setRotulo ( 'Data de Nascimento'                                   );
        $obDtaNascimentoDependente->setValue  ( $dtNascimentoDependente                                );
        $obDtaNascimentoDependente->obEvento->setOnChange("buscaValor('calculaDataLimiteSalarioFamilia',5);");

        $obFormulario = new Formulario;
        $obFormulario->addComponente        ( $obDtaNascimentoDependente                                        );
        $obFormulario->montaInnerHTML();
        $obFormulario->obJavaScript->montaJavaScript();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnDataNascimentoDependente').innerHTML = '$stHtml';\n";

    return $stJs;
}

function validaDataLimiteSalarioFamilia()
{
    $stMensagem  = "";
    $arPrevidencia = Sessao::read('PREVIDENCIA');
    if ( is_array($arPrevidencia) ) {
        $obRFolhaPagamentoSalarioFamilia = new RFolhaPagamentoSalarioFamilia;
        foreach ($arPrevidencia as $inCodPrevidencia) {
            $obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->setCodPrevidencia( $inCodPrevidencia );
            $obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->consultarPrevidencia();
            if ( $obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->getTipo() == 'o' ) {
                $inCodPrevidencia = $arPrevidencia[0];
                break;
            }
        }
        if ($inCodPrevidencia) {
            $obRFolhaPagamentoSalarioFamilia->listarSalarioFamilia( $rsSalariosFamilia );
            if ( $rsSalariosFamilia->getNumLinhas() < 0 ) {
                $stMensagem = "Não existe configuração para salário família.";
            }
        } else {
            $stMensagem = "Selecione uma previdência para usar a configuração de salário família.";
        }
    }
    $stJs .= "d.getElementById('stDataLimiteSalarioFamilia').innerHTML = '".$stMensagem."';\n";

    return $stJs;
}

function montaCID()
{
    $arDependentes = Sessao::read("DEPENDENTE");
    $inId = Sessao::read('inId');
    $arDependente = $arDependentes[$inId];
    
    $obTPessoalCID = new TPessoalCID;
    $obTPessoalCID->setDado("cod_cid",$arDependente['inCodCIDDependente']);
    $obTPessoalCID->recuperaPorChave($rsCID);
        
    if( count($rsCID->arElementos) > 0 ){
        $stDescricaoCIDDependente = $rsCID->getCampo('descricao');
        $inSiglaCIDDependente     = $rsCID->getCampo('sigla');
        $inCodCIDDependente       = $rsCID->getCampo('cod_cid');        
    }else{
        $stDescricaoCIDDependente = "&nbsp;";
        $inSiglaCIDDependente     = "";
        $inCodCIDDependente       = "";        
    }
    
    $obHdnCodCID = new Hidden;
    $obHdnCodCID->setName                           ( "inCodCIDDependente"                  );
    $obHdnCodCID->setId                             ( "inCodCIDDependente"                  );
    $obHdnCodCID->setValue                          ( $inCodCIDDependente                   );
    
    //CID Dependente
    $obBscCID = new BuscaInner;
    $obBscCID->setRotulo                            ( "CID"                                 );
    $obBscCID->setTitle                             ( "Informe o CID para o dependente."    );
    $obBscCID->setNull                              ( false                                 );
    $obBscCID->setName                              ( "stCIDDependente"                     );
    $obBscCID->setId                                ( "stCIDDependente"                     );
    $obBscCID->obCampoCod->setName                  ( "inSiglaCIDDependente"                );
    $obBscCID->obCampoCod->setId                    ( "inSiglaCIDDependente"                );
    $obBscCID->obCampoCod->setValue                 ( $inSiglaCIDDependente                 );
    $obBscCID->obCampoCod->setSize                  ( 10                                    );
    $obBscCID->obCampoCod->setAlign                 ( "left"                                );
    $obBscCID->obCampoCod->setInteiro               ( false                                 );
    $obBscCID->obCampoCod->setToUpperCase           ( true                                  );
    $obBscCID->obCampoCod->obEvento->setOnBlur      ( "buscaValor('buscaCIDDependente',5);" );
    $obBscCID->setFuncaoBusca                       ( "abrePopUp('".CAM_GRH_PES_POPUPS."CID/FLProcurarCID.php','frm','&inCodCID=inCodCIDDependente&campoNum=inSiglaCIDDependente','stCIDDependente','','".Sessao::getId()."','800','550')" );
    
    $obFormulario = new Formulario;
    $obFormulario->addHidden                        ( $obHdnCodCID        );
    $obFormulario->addComponente                    ( $obBscCID           );
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stHtml = $obFormulario->getHTML();
    
    $stJs .= "d.getElementById('spnCID').innerHTML = '".$stHtml."'; \n";
    $stJs .= "d.getElementById('stCIDDependente').innerHTML = '".$stDescricaoCIDDependente."'; \n";

    return $stJs;
}

function buscaCIDDependente(){
    global $request;
            
    $inSiglaCIDDependente = strtoupper($request->get('inSiglaCIDDependente'));
    $stDescricao = "&nbsp;";
    
    if(!empty($inSiglaCIDDependente)){
        $stFiltro = " WHERE sigla ILIKE '".$inSiglaCIDDependente."%' ";
        $obTPessoalCID = new TPessoalCID;
        $obTPessoalCID->recuperaTodos($rsCID, $stFiltro);
        
        if(count($rsCID->arElementos) > 0){
            $stDescricao = $rsCID->getCampo('descricao');
            $stJs .= "d.getElementById('inCodCIDDependente').value = '".$rsCID->getCampo('cod_cid')."'; \n";
        }else{
            $stDescricao = "&nbsp;";
            $stJs .= "d.getElementById('inSiglaCIDDependente').value = ''; \n";
            $stJs .= "alertaAviso('CID ".$inSiglaCIDDependente." não encontrado!','form','erro','".Sessao::getId()."'); \n";
        }
    }else{
        $stJs .= "d.getElementById('inCodCIDDependente').value = ''; \n";
    }
    $stJs .= " d.getElementById('stCIDDependente').innerHTML = '".$stDescricao."'; \n";
    
    return $stJs;
}

function geraSpnDependenteSalarioFamilia($boExecuta=false)
{
    $chkFilhoEquiparado = new CheckBox;
    $chkFilhoEquiparado->setRotulo  ( "Portador Necess. Especiais" );
    $chkFilhoEquiparado->setName    ( "boFilhoEquiparado"          );
    $chkFilhoEquiparado->setId      ( "boFilhoEquiparado"          );
    $chkFilhoEquiparado->setValue   ( "t"                          );
    $chkFilhoEquiparado->setChecked ( false                        );
    $chkFilhoEquiparado->obEvento->setOnChange("buscaValor('montaCID', 5)");

    $obTxtDataInicioSalarioFamilia =  new Data;
    $obTxtDataInicioSalarioFamilia->setName   ( "dtInicioSalarioFamilia"                     );
    $obTxtDataInicioSalarioFamilia->setNull   ( true                                         );
    $obTxtDataInicioSalarioFamilia->setRotulo ( "*Data-Início para Salário Família"                );
    $obTxtDataInicioSalarioFamilia->setTitle  ( "Informe a data-início para salário família." );
    $obTxtDataInicioSalarioFamilia->obEvento->setOnChange("buscaValor('validaInicioSalarioFamilia',5);");
    $obTxtDataInicioSalarioFamilia->setValue  ( Sessao::read('dtInicioSalarioFamilia') );

    $obLblDataLimiteSalarioFamilia = new Label;
    $obLblDataLimiteSalarioFamilia->setRotulo ( "Data-Limite para Salário Família" );
    $obLblDataLimiteSalarioFamilia->setId     ( "stDataLimiteSalarioFamilia"       );

    $obSpnCID = new Span;
    $obSpnCID->setId ( "spnCID" );

    $inId = Sessao::read('inId');
    if ($inId != '') {
        $arDependentes = Sessao::read("DEPENDENTE");
        $arDependente = $arDependentes[$inId];
        $obTxtDataInicioSalarioFamilia->setValue  ( $arDependente['dtInicioSalarioFamilia'] );
        $chkFilhoEquiparado->setChecked           ( ($arDependente['boFilhoEquiparado'] == "t") or ($arDependente['boFilhoEquiparado'] == 1) );
        $obLblDataLimiteSalarioFamilia->setValue  ( $arDependente['dtLimiteSalarioFamilia'] );
    }

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $chkFilhoEquiparado            );
    $obFormulario->addSpan($obSpnCID);
    $obFormulario->addComponente( $obTxtDataInicioSalarioFamilia );
    $obFormulario->addComponente( $obLblDataLimiteSalarioFamilia );

    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $stHtml = $obFormulario->getHTML();

    $stJs  = "f.stEvalDependenteSalarioFamilia.value = '".$stEval."'; \n";
    $stJs .= "d.getElementById('spnDependenteSalarioFamilia').innerHTML = '".$stHtml."';";
    $stJs .= validaDataLimiteSalarioFamilia();
    if ($_POST['stDtNascimentoDependente'] != "") {
        $stJs .= calculaDataLimiteSalarioFamilia();
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function limpaSpnDependenteSalarioFamilia()
{
    $stJs  = "f.stEvalDependenteSalarioFamilia.value = ''; \n";
    $stJs .= "d.getElementById('spnDependenteSalarioFamilia').innerHTML = '';";

    return $stJs;
}

function habilitaCarteiraVacinacao()
{
    if ($_POST['boCarteiraVacinacao']) {
        $stJs .= "f.dtApresentacaoCarteiraVacinacao.disabled = false;   \n";
    } else {
        $stJs .= "f.dtApresentacaoCarteiraVacinacao.disabled = true;    \n";
    }

    return $stJs;
}

function habilitaComprovanteMatricula()
{
    if ($_POST['boComprovanteMatricula']) {
        $stJs .= "f.dtApresentacaoComprovanteMatricula.disabled = false;    \n";
    } else {
        $stJs .= "f.dtApresentacaoComprovanteMatricula.disabled = true;     \n";
    }

    return $stJs;
}

function incluirVacinacao()
{
    $rsRecordSet = new Recordset;
    $arRecordSet = ( is_array(Sessao::read('VACINACAO')) ) ? Sessao::read('VACINACAO') : array();
    $rsRecordSet->preenche( $arRecordSet );
    $rsRecordSet->setUltimoElemento();
    $inUltimoId = $rsRecordSet->getCampo("inId");
    if ( $rsRecordSet->getNumLinhas() < 0 ) {
        $inProxId = 0;
    } else {
        $inProxId = $inUltimoId + 1;
    }
    $ultimaDataIncluida = $rsRecordSet->getCampo("dtApresentacaoCarteiraVacinacao");

    $ultimaDataIncluida = explode('/',$ultimaDataIncluida);
    $ultimaDataIncluida = $ultimaDataIncluida[2].$ultimaDataIncluida[1].$ultimaDataIncluida[0];

    if (!$_REQUEST["dtApresentacaoCarteiraVacinacao"]) {
        $stJs .= "alertaAviso('Digite uma data de apresentação para a carteira de vacinação.','form','erro','".Sessao::getId()."');";
    } else {
        $novaData = explode('/',$_REQUEST["dtApresentacaoCarteiraVacinacao"]);
        $novaData = $novaData[2].$novaData[1].$novaData[0];

        if ( ($ultimaDataIncluida >= $novaData) && (count (Sessao::read("VACINACAO")) > 0 )) {
            $stMensagem = "A data informada deve ser maior que o da última data cadastrada.";
            $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');";

        } else {
            $arVacinacao = Sessao::read("VACINACAO");
            $arElementos['inId']                            = $inProxId;
            $arElementos['dtApresentacaoCarteiraVacinacao'] = $_POST['dtApresentacaoCarteiraVacinacao'];
            $boApresentadaVacinacao                         = "boApresentadaVacinacao_" . $inProxId;
            $arElementos['boApresentadaVacinacao']          = $_POST['boApresentadaVacinacao'];
            $arElementos['vacinacaoInserida']               = 'on';
            $arVacinacao[]                  = $arElementos;
            Sessao::write("VACINACAO",$arVacinacao);
            $stJs .= listarVacinacao();
        }
    }

    return $stJs;
}

function excluirVacinacao()
{
    $id  = $_GET['inLinha'];
    $inId = 0;
    $arVacinacoes = Sessao::read('VACINACAO');
    $arTemp       = array();
    foreach ($arVacinacoes as $arVacinacao) {
        if ($arVacinacao["inId"] != $id) {
            $arVacinacao["inId"] = $inId;
            $arTMP[]             = $arVacinacao;
            $inId++;
        }
    }
    Sessao::write('VACINACAO', $arTMP);
    $stJs .= listarVacinacao();

    return $stJs;
}

function listarVacinacao($boExecuta=true)
{
    $arRecordSet = ( is_array(Sessao::read('VACINACAO')) ) ? Sessao::read('VACINACAO') : array();
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( $arRecordSet );
    if ($rsRecordSet->getNumLinhas() > 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Controle de carteiras de vacinação" );
        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Data de apresentação" );
        $obLista->ultimoCabecalho->setWidth( 65 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obChkApresentadaVacinacao = new CheckBox;
        $obChkApresentadaVacinacao->setName           ( "boApresentadaVacinacao"   );
        $obChkApresentadaVacinacao->setValue          ("true"  );
        $obLista->addDadoComponente( $obChkApresentadaVacinacao );
        $obLista->ultimoDado->setCampo('[boApresentadaVacinacao]');
        $obLista->ultimoDado->setAlinhamento('CENTRO');
        $obLista->commitDadoComponente();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "dtApresentacaoCarteiraVacinacao" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarDado('excluirVacinacao',5);" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnVacinacao').innerHTML = '".$stHtml."';";
    $stJs .= "f.dtApresentacaoCarteiraVacinacao.value    = '';";

    return $stJs;
}

function incluirMatricula()
{
    $rsRecordSet = new Recordset;
    $arRecordSet = ( is_array(Sessao::read('MATRICULA')) ) ? Sessao::read('MATRICULA') : array();
    $rsRecordSet->preenche( $arRecordSet );
    $rsRecordSet->setUltimoElemento();

    $inUltimoId = $rsRecordSet->getCampo("inId");
    if ( $rsRecordSet->getNumLinhas() < 0 ) {
        $inProxId = 0;
    } else {
        $inProxId = $inUltimoId + 1;
    }

    $ultimaDataIncluida = $rsRecordSet->getCampo("dtApresentacaoComprovanteMatricula");

    $ultimaDataIncluida = explode('/',$ultimaDataIncluida);
    $ultimaDataIncluida = $ultimaDataIncluida[2].$ultimaDataIncluida[1].$ultimaDataIncluida[0];

    if (!$_REQUEST["dtApresentacaoComprovanteMatricula"]) {
        $stJs .= "alertaAviso('Digite uma data de apresentação para a matrícula.','form','erro','".Sessao::getId()."');";
    } else {

        $novaData = explode('/',$_REQUEST["dtApresentacaoComprovanteMatricula"]);
        $novaData = $novaData[2].$novaData[1].$novaData[0];

        if ( ($ultimaDataIncluida >= $novaData) && (count (Sessao::read('MATRICULA')) > 0 )) {
            $stMensagem = "A data informada deve ser maior que o da última data cadastrada.";
            $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');";

        } else {
            $arMatricula = Sessao::read("MATRICULA");
            $arElementos['inId']                               = $inProxId;
            $arElementos['dtApresentacaoComprovanteMatricula'] = $_POST['dtApresentacaoComprovanteMatricula'];
            $boApresentadaMatricula = "boApresentadaMatricula_" . $inProxId;
            $arElementos['boApresentadaMatricula']             = $_POST["$boApresentadaMatricula"];
            $arElementos['matriculaInserida']                  = 'on';
            $arMatricula[]                     = $arElementos;
            Sessao::write("MATRICULA",$arMatricula);
            $stJs .= listarMatricula();
        }
    }

    return $stJs;
}

function excluirMatricula()
{
    $id  = $_GET['inLinha'];
    $inId = 0;
    $arMatriculas = Sessao::read('MATRICULA');
    $arTemp       = array();
    foreach ($arMatriculas as $arMatricula) {
        if ($arMatricula["inId"] != $id) {
            $arMatricula["inId"] = $inId;
            $arTMP[]             = $arMatricula;
            $inId++;
        }
    }
    Sessao::write('MATRICULA',$arTMP);
    $stJs .= listarMatricula();

    return $stJs;
}

function listarMatricula($boExecuta=true)
{
    $rsRecordSet = new Recordset;
    $arRecordSet = ( is_array(Sessao::read('MATRICULA')) ) ? Sessao::read('MATRICULA') : array();
    $rsRecordSet->preenche( $arRecordSet );
    if ($rsRecordSet->getNumLinhas() > 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Controle de comprovante de matrícula" );
        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Data de apresentação" );
        $obLista->ultimoCabecalho->setWidth( 65 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obChkApresentadaMatricula = new CheckBox;
        $obChkApresentadaMatricula->setName           ( "boApresentadaMatricula"   );
        $obChkApresentadaMatricula->setValue          ("true"   );
        $obLista->addDadoComponente( $obChkApresentadaMatricula );
        $obLista->ultimoDado->setCampo('boApresentadaMatricula');
        $obLista->ultimoDado->setAlinhamento('CENTRO');
        $obLista->commitDadoComponente();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "dtApresentacaoComprovanteMatricula" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarDado('excluirMatricula',5);" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnMatricula').innerHTML = '".$stHtml."'; \n";
    $stJs .= "f.dtApresentacaoComprovanteMatricula.value = ''; \n";

    return $stJs;
}

function addDependente($inId,$arDependenteCadastrado=array())
{
    $arVacinacoes = Sessao::read('VACINACAO');
    $arMatriculas = Sessao::read('MATRICULA');
    if ( count($arDependenteCadastrado) == 0 ) {
        if ( is_array($arVacinacoes) ) {
            foreach ($arVacinacoes as $inIndex=>$arVacinacao) {
                if ( $_POST["boApresentadaVacinacao_".($inIndex+1)] == "on" ) {
                    $arVacinacoes[$inIndex]['boApresentadaVacinacao'] = true;
                } else {
                    $arVacinacoes[$inIndex]['boApresentadaVacinacao'] = false;
                }
            }
        } else {
            $arVacinacoes = array();
        }
        if ( is_array($arMatriculas) ) {
            foreach ($arMatriculas as $inIndex=>$arMatricula) {
                if ( $_POST["boApresentadaMatricula_".($inIndex+1)] == "on" ) {
                    $arMatriculas[$inIndex]['boApresentadaMatricula'] = true;
                } else {
                    $arMatriculas[$inIndex]['boApresentadaMatricula'] = false;
                }
            }
        } else {
            $arMatriculas = array();
        }
    }
    
    $arDependente['inId']                               = $inId;
    $arDependente['stGrauParentesco']                   = ($_POST['stGrauParentesco']                          != "")  ? $_POST['stGrauParentesco']                          : $arDependenteCadastrado["cod_grau"];
    $arDependente['stNomGrauParentesco']                = SistemaLegado::pegaDado('nom_grau', 'cse.grau_parentesco', 'where cod_grau = '.$arDependente['stGrauParentesco']); 
    $arDependente['inCGMDependente']                    = ($_POST['inCGMDependente']                           != "")  ? $_POST['inCGMDependente']                           : $arDependenteCadastrado["numcgm"];
    $arDependente['stNomeDependente']                   = ($_POST['stNomDependente']                           != "")  ? stripslashes($_POST['stNomDependente'])             : $arDependenteCadastrado["nom_cgm"]    ;
    $arDependente['stSexoDependente']                   = ($_POST['stSexoDependente']                          != "")  ? $_POST['stSexoDependente']                          : $arDependenteCadastrado["sexo"];
    $arDependente['stDataNascimentoDependente']         = ($_POST['stDtNascimentoDependente']                  != "")  ? $_POST['stDtNascimentoDependente']                  : $arDependenteCadastrado["dt_nascimento"];
    $arDependente['boincluirDataNascimentoDespendente'] = (Sessao::read('boincluirDataNascimentoDespendente')  != "")  ? Sessao::read('boincluirDataNascimentoDespendente')  : false;
    $arDependente['boDependenteSalarioFamilia']         = ($_POST['boDependenteSalarioFamilia']                != "")  ? $_POST['boDependenteSalarioFamilia']                : $arDependenteCadastrado["dependente_sal_familia"];
    $arDependente['boFilhoEquiparado']                  = ($_POST['boFilhoEquiparado']                         != "")  ? $_POST['boFilhoEquiparado']                         : $arDependenteCadastrado["dependente_invalido"];
    $arDependente['dtInicioSalarioFamilia']             = ($_POST['dtInicioSalarioFamilia']                    != "")  ? $_POST['dtInicioSalarioFamilia']                    : $arDependenteCadastrado["dt_inicio_sal_familia"];
    $arDependente['dtLimiteSalarioFamilia']             = (Sessao::read('stDataLimiteSalarioFamilia')          != "")  ? Sessao::read('stDataLimiteSalarioFamilia')          : $stDataLimiteSalarioFamilia;
    $arDependente['inCodDependenteIR']                  = ($_POST['inCodDependenteIR']                         != "")  ? $_POST['inCodDependenteIR']                         : $arDependenteCadastrado["cod_vinculo"];
    $arDependente['boCarteiraVacinacao']                = ($_POST['boCarteiraVacinacao']                       != "")  ? $_POST['boCarteiraVacinacao']                       : $arDependenteCadastrado["carteira_vacinacao"];
    $arDependente['boComprovanteMatricula']             = ($_POST['boComprovanteMatricula']                    != "")  ? $_POST['boComprovanteMatricula']                    : $arDependenteCadastrado["comprovante_matricula"];
    $arDependente['boDependentePrev']             	= ($_POST['boDependentePrev']                          != "")  ? $_POST['boDependentePrev']                          : $arDependenteCadastrado["dependente_prev"];
    $arDependente['inCodCIDDependente']                 = ($_POST['inCodCIDDependente']                        != "")  ? $_POST['inCodCIDDependente']                        : $arDependenteCadastrado["cod_cid"];
    $arDependente['inSiglaCIDDependente']               = ($_POST['inSiglaCIDDependente']                      != "")  ? $_POST['inSiglaCIDDependente']                      : $arDependenteCadastrado["sigla_cid_dependente"];
    $arDependente['inCodDependente']                    = ($_POST['inCodDependente']                           != "")  ? $_POST['inCodDependente']                           : $arDependenteCadastrado["cod_dependente"];
    
    $arDependente['VACINACAO']                          = $arVacinacoes;
    $arDependente['MATRICULA']                          = $arMatriculas;
    Sessao::write("VACINACAO",array());
    Sessao::write("MATRICULA",array());
    Sessao::write("inCodDependente","");

    return $arDependente;
}

function validarDependente()
{
    global $request;
    
    $obErro = new erro;
    if ($request->get("inCodGrauParentesco") == "") {
        $obErro->setDescricao("Campo Grau Parentesco da guia Dependentes inválido!()");
    }
    if ( !$obErro->ocorreu() and $request->get("inCGMDependente") == "" ) {
        $obErro->setDescricao("Campo CGM do Dependente da guia Dependentes inválido!()");
    }
    if ( !$obErro->ocorreu() and $request->get("stDtNascimentoDependente") == "" ) {
        $obErro->setDescricao("Campo Data de Nascimento da guia Dependentes inválido!()");
    }
    if ( !$obErro->ocorreu() and $request->get("boDependenteSalarioFamilia") == "1" and $request->get("dtInicioSalarioFamilia") == "" ) {
        $obErro->setDescricao("Campo Data-Início para Salário Família da guia Dependentes inválido!()");
    }
    if ( !$obErro->ocorreu() and $request->get("inCodDependenteIR") == "" ) {
        $obErro->setDescricao("Campo Dependente IR da guia Dependentes inválido!()");
    }
    if ( !$obErro->ocorreu() and $request->get("inCodCIDDependente") == ""  and ($request->get("boFilhoEquiparado") == 1 or $request->get("boFilhoEquiparado") == "t")) {
        $obErro->setDescricao("Campo CID da guia Dependentes inválido!()");
    }

    return $obErro;
}

function incluirDependente()
{
    $obErro = new erro;
    $arElementos = array ();
    $arVacina    = array();
    $arMatricula = array();
    if ( Sessao::read('boAlterarDependente') ) {
        $obErro->setDescricao("Alteração em processo, clique em alterar para confirmar alteração ou limpar para cancelar.");
    }
    
    if ( !$obErro->ocorreu() ) {
        $obErro = validarDependente();
    }
    if ( !$obErro->ocorreu() ) {
        Sessao::write('DEPENDENTE',( is_array(Sessao::read('DEPENDENTE')) ) ? Sessao::read('DEPENDENTE') : array());
        foreach (Sessao::read('DEPENDENTE') as $arDependente) {
            if ($arDependente["inCGMDependente"] == $_POST['inCGMDependente']) {
                $obErro->setDescricao("Esse dependente já está inserido na lista.");
                break;
            }
        }
    }

    if ( !$obErro->ocorreu() ) {
        $arDependentes = Sessao::read('DEPENDENTE');
        $arDependentes[] = addDependente(count($arDependentes));
        Sessao::write('DEPENDENTE',$arDependentes);
        $stJs .= listarDependente();
        $stJs .= limparDependente();
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');      \n";
    }

    return $stJs;
}

function listarDependente()
{
    $rsRecordSet = new Recordset;
    $arRecordSet = ( is_array(Sessao::read('DEPENDENTE')) ) ? Sessao::read('DEPENDENTE') : array();
    $rsRecordSet->preenche($arRecordSet);
    if ($rsRecordSet->getNumLinhas() > 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Dependentes Cadastrados" );
        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Nome" );
        $obLista->ultimoCabecalho->setWidth( 45 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Grau de Parentesco" );
        $obLista->ultimoCabecalho->setWidth( 25 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Data de nascimento" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stNomeDependente" );
        $obLista->ultimoDado->setAlinhamento( 'LEFT' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stNomGrauParentesco" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stDataNascimentoDependente" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarDado('montaAlterarDependente',5);" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarDado('excluirDependente',5);" );
        $obLista->ultimaAcao->addCampo("2","inId");
        $obLista->commitAcao();
        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();

        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
        $stHtml = str_replace(chr(13),"",$stHtml);
        $stHtml = str_replace(chr(13).chr(10),"",$stHtml);

    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnDependente').innerHTML = '".$stHtml."';";

    return $stJs;
}

function alterarDependente()
{
    $obErro = new erro;

    $stJs .= "d.getElementById('btnIncluirDependente').disabled = false; \n";
    $stJs .= "d.getElementById('btnAlterarDependente').disabled = true; \n";
    
    if ( !$obErro->ocorreu() ) {
        $obErro = validarDependente();
    }

    if ( !$obErro->ocorreu() ) {
        $inId = Sessao::read('inId');
        $arDependentes = Sessao::read('DEPENDENTE');
        $arDependentes[$inId] = addDependente($inId);

        Sessao::write('DEPENDENTE',$arDependentes);
        Sessao::write('boAlterarDependente',false);
        
        $stJs .= listarDependente();
        $stJs .= limparDependente();
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');      \n";
    }
       
    return $stJs;
}

function excluirDependente()
{
    $id  = $_GET['inLinha'];
    $inId = 0;
    $arDependentes = Sessao::read('DEPENDENTE');
    $arTemp       = array();
    foreach ($arDependentes as $arDependente) {
        if ($arDependente["inId"] != $id) {
            $arDependente["inId"] = $inId;
            $arTMP[]             = $arDependente;
            $inId++;
        }
    }
    Sessao::write('DEPENDENTE',$arTMP);
    $stJs .= listarDependente();

    return $stJs;
}

function limparDependente()
{
    Sessao::remove('VACINACAO');
    Sessao::remove('MATRICULA');
    Sessao::write('boAlterarDependente',false);
    Sessao::remove('inId');
    
    $stJs .= "d.getElementById('spnVacinacao').innerHTML                 = '';       \n";
    $stJs .= "d.getElementById('spnMatricula').innerHTML                 = '';       \n";
    $stJs .= "d.getElementById('spnDataNascimentoDependente').innerHTML  = '';       \n";
    $stJs .= "d.getElementById('stSexoDependente').innerHTML             = '&nbsp;'; \n";
    $stJs .= "d.getElementById('stNomDependente').innerHTML              = '&nbsp;'; \n";
    $stJs .= "f.inCodDependente.value                                    = '';       \n";
    $stJs .= "f.inCGMDependente.value                                    = '';       \n";
    $stJs .= "f.inCodGrauParentesco.value                                = '';       \n";
    $stJs .= "f.stGrauParentesco.value                                   = '';       \n";
    $stJs .= "f.inCodDependenteIR.value                                  = '';       \n";
    $stJs .= "f.stDependenteIR.value                                     = '';       \n";
    $stJs .= "f.boCarteiraVacinacao.checked                              = false;    \n";
    $stJs .= "f.boComprovanteMatricula.checked                           = false;    \n";
    $stJs .= "f.inCodCIDDependente.value                                 = '';       \n";
    $stJs .= "f.inSiglaCIDDependente.value                               = '';       \n";
    $stJs .= "f.dtInicioSalarioFamilia.value                             = '';       \n";
    $stJs .= "f.inTimestamp.value                                        = '';       \n";
    $stJs .= "f.dtApresentacaoCarteiraVacinacao.value                    = '';       \n";
    $stJs .= "f.dtApresentacaoCarteiraVacinacao.disabled                 = true;     \n";
    $stJs .= "f.dtApresentacaoComprovanteMatricula.value                 = '';       \n";
    $stJs .= "f.dtApresentacaoComprovanteMatricula.disabled              = true;     \n";
    $stJs .= "d.getElementById('boDependenteSalarioFamiliaSim').disabled = true;     \n";
    $stJs .= "d.getElementById('boDependenteSalarioFamiliaSim').checked  = false;    \n";
    $stJs .= "d.getElementById('boDependenteSalarioFamiliaNao').checked  = true;     \n";
    $stJs .= "d.getElementById('spnDependenteSalarioFamilia').innerHTML  = '';       \n";
    $stJs .= "d.getElementById('boDependentePrevSim').checked            = false;    \n";
    $stJs .= "d.getElementById('boDependentePrevNao').checked            = true;     \n";
    $stJs .= "f.stEvalDependenteSalarioFamilia.value                     = '';       \n";    
    
    return $stJs;
}

function montaAlterarDependente()
{
    $arDependentes = Sessao::read("DEPENDENTE");
    $inId = $_GET['inLinha'];
    
    Sessao::write('inId',$inId);
    Sessao::write('boAlterarDependente',true);
    
    $arDependente = $arDependentes[$inId];
        
    Sessao::write('stSexoDependente',$arDependente['stSexoDependente']);
    Sessao::write('stDataNascimentoDependente',$arDependente['stDataNascimentoDependente']);
    Sessao::write('boincluirDataNascimentoDespendente',( $arDependente['stDataNascimentoDependente'] != "" ) ? false : true);
    
    $stJs .= "d.getElementById('btnIncluirDependente').disabled = true; \n";
    $stJs .= "d.getElementById('btnAlterarDependente').disabled = false; \n";
    
    $stJs .= "f.inCodDependente.value                       = '".$arDependente['inCodDependente']."';                       \n";
    $stJs .= "f.inCodGrauParentesco.value                   = '".$arDependente['stGrauParentesco']."';                      \n";
    $stJs .= "f.stGrauParentesco.value                      = '".$arDependente['stGrauParentesco']."';                      \n";
    $stJs .= "f.inCGMDependente.value                       = '".$arDependente['inCGMDependente']."';                       \n";
    $stJs .= "d.getElementById('stNomDependente').innerHTML = '".addslashes($arDependente['stNomeDependente'])."';          \n";
    $stJs .= "f.stNomDependente.value                       = '".addslashes($arDependente['stNomeDependente'])."';          \n";
    $stJs .= montaSpanDataNascimentoDependente($arDependente['stDataNascimentoDependente']);
    $stJs .= "d.getElementById('stSexoDependente').innerHTML = '".$arDependente['stSexoDependente']."';                     \n";
    $stJs .= "f.stSexoDependente.value                       = '".$arDependente['stSexoDependente']."';                     \n";
    $stJs .= habilitaDependenteSalarioFamilia($arDependente['stGrauParentesco']);
    
    $stJs .= "f.inCodDependenteIR.value = '".$arDependente['inCodDependenteIR']."';                                         \n";
    $stJs .= "f.stDependenteIR.value    = '".$arDependente['inCodDependenteIR']."';                                         \n";
    
    if ($arDependente['boDependenteSalarioFamilia'] == 't' or $arDependente['boDependenteSalarioFamilia'] == 1) {
        $stJs .= geraSpnDependenteSalarioFamilia();
        $stJs .= "f.boDependenteSalarioFamilia[0].checked = true;                                                           \n";
        
        if ($arDependente['boFilhoEquiparado'] == "t" or $arDependente['boFilhoEquiparado'] == 1) {
            $_REQUEST['boFilhoEquiparado'] = $arDependente['boFilhoEquiparado'];
            $stJs .= "d.getElementById('boFilhoEquiparado').checked                   = true;                               \n";
            $stJs .= montaCID();        
        } else {
            $stJs .= "d.getElementById('boFilhoEquiparado').checked                   = false;                              \n";
            $stJs .= "d.getElementById('spnCID').innerHTML = '';";
        }

        $stJs .= "f.dtInicioSalarioFamilia.value = '".$arDependente['dtInicioSalarioFamilia']."';                           \n";
        $stJs .= calculaDataLimiteSalarioFamilia($arDependente['stDataNascimentoDependente']);
    } else {
        $stJs .= "f.boDependenteSalarioFamilia[1].checked = true;                                                           \n";
        $stJs .= limpaSpnDependenteSalarioFamilia();
    }
   
    if ($arDependente['boCarteiraVacinacao'] == "t" or  $arDependente['boCarteiraVacinacao'] == 1) {
        $stJs .= "f.boCarteiraVacinacao.checked    = true;                                                                  \n";
        $stJs .= "f.dtApresentacaoCarteiraVacinacao.disabled = false;                                                       \n";
    } else {
        $stJs .= "f.boCarteiraVacinacao.checked    = false;                                                                 \n";

    }
    if ($arDependente['boComprovanteMatricula'] == "t" or $arDependente['boComprovanteMatricula'] == 1) {
        $stJs .= "f.boComprovanteMatricula.checked = true;                                                                  \n";
        $stJs .= "f.dtApresentacaoComprovanteMatricula.disabled  = false;                                                   \n";
    } else {
        $stJs .= "f.boComprovanteMatricula.checked = false;                                                                 \n";
    }

    if ($arDependente['boDependentePrev'] == "t" or  $arDependente['boDependentePrev'] == 1) {
        $stJs .= "d.getElementById('boDependentePrevSim').checked                   = true;				    \n";
        $stJs .= "d.getElementById('boDependentePrevNao').checked                   = false;                                \n";
    } else {
        $stJs .= "d.getElementById('boDependentePrevSim').checked                   = false;				    \n";
        $stJs .= "d.getElementById('boDependentePrevNao').checked                   = true;                                 \n";
    }
    Sessao::write('VACINACAO',$arDependente['VACINACAO']);
    $stJs .= listarVacinacao();
    Sessao::write('MATRICULA',$arDependente['MATRICULA']);
    $stJs .= listarMatricula();

    return $stJs;
}

function listarAlterarDependente()
{
    global $inCodServidor;

    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addDependente();
    $obRPessoalServidor->setCodServidor($inCodServidor);
    $obRPessoalServidor->roUltimoDependente->listarPessoalDependente($rsDependente,$boTransacao);
    $obRPessoalServidor->roUltimoDependente->addRPessoalCarteiraVacinacao();
    $obRPessoalServidor->roUltimoDependente->addRPessoalComprovanteMatricula();
    $arDependentes = ( is_array($rsDependente->getElementos()) ) ? $rsDependente->getElementos() : array();
    $arTemp2 = array();
    foreach ($arDependentes as $inIndex=>$arDependente) {
        $obRPessoalServidor->roUltimoDependente->setCodDependente($arDependente['cod_dependente']);
        $obRPessoalServidor->roUltimoDependente->roRPessoalCarteiraVacinacao->listarCarteira($rsVacinacao);
        $arVacinacoes = ( is_array($rsVacinacao->getElementos()) ) ? $rsVacinacao->getElementos() : array();
        $arTemp = array();
        foreach ($arVacinacoes as $inIndex2=>$arVacinacao) {
            $arElementos['inId']                            = $inIndex2;
            $arElementos['dtApresentacaoCarteiraVacinacao'] = $arVacinacao['dt_apresentacao'];
            $arElementos['boApresentadaVacinacao']          = ($arVacinacao['apresentada'] == 't') ? true : false;
            $arElementos['vacinacaoInserida']               = 'on';
            $arTemp[] = $arElementos;
        }
        Sessao::write("VACINACAO",$arTemp);
        $arElementos = array();
        $obRPessoalServidor->roUltimoDependente->roRPessoalComprovanteMatricula->listarComprovante($rsComprovantes);
        $arComprovantes = ( is_array($rsComprovantes->getElementos()) ) ? $rsComprovantes->getElementos() : array();
        $arTemp = array();
        foreach ($arComprovantes as $inIndex2=>$arComprovante) {
            $arElementos['inId']                               = $inIndex2;
            $arElementos['dtApresentacaoComprovanteMatricula'] = $arComprovante['dt_apresentacao'];
            $arElementos['boApresentadaMatricula']             = ($arComprovante['apresentada'] == 't') ? true : false;
            $arElementos['matriculaInserida']                  = 'on';
            $arTemp[] = $arElementos;
        }
        Sessao::write("MATRICULA",$arTemp);
        $arTemp2[] = addDependente($inIndex,$arDependente);
    }
    Sessao::write("DEPENDENTE",$arTemp2);
    $stJs     .= listarDependente();
   
    return $stJs;
}

function calculaDataLimiteSalarioFamilia($dtNascimentoDependente="")
{
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoSalarioFamilia.class.php" );

    $obRFolhaPagamentoSalarioFamilia = new RFolhaPagamentoSalarioFamilia;
    $js = "d.getElementById('stDataLimiteSalarioFamilia').innerHTML = '&nbsp;'; \n";
    Sessao::write("stDataLimiteSalarioFamilia","&nbsp;");

    if ($dtNascimentoDependente != "") {
        //Verifica se a previdencia oficial foi selecionada
        $arPrevidencias = Sessao::read("PREVIDENCIA");
        if ( is_array($arPrevidencias) ) {
            foreach ($arPrevidencias as $inCodPrevidencia) {
                $obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->setCodPrevidencia( $inCodPrevidencia );
                $obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->consultarPrevidencia();
                if ( $obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->getTipo() == 'o' ) {
                    $inCodPrevidencia = $arPrevidencias[0];
                    break;
                }
            }
        }

        //Lista o salário família para da previdencia e calcula a data limite
        if ($inCodPrevidencia) {
            $obRFolhaPagamentoSalarioFamilia->listarSalarioFamilia( $rsSalariosFamilia );

            $dtNascimentoDependente = ( $_POST['stDtNascimentoDependente'] ) ? $_POST['stDtNascimentoDependente'] : $dtNascimentoDependente;
            $arDataNascimentoDependente = explode( "/", $dtNascimentoDependente );
            $stAnoLimte = $arDataNascimentoDependente[2] + $rsSalariosFamilia->getCampo("idade_limite");
            $stDataLimiteSalarioFamilia = $arDataNascimentoDependente[0] ."/". $arDataNascimentoDependente[1] ."/". $stAnoLimte;

            Sessao::write('stDataLimiteSalarioFamilia',$stDataLimiteSalarioFamilia);

            if ($rsSalariosFamilia->getCampo("idade_limite") ) {
               $js = "d.getElementById('stDataLimiteSalarioFamilia').innerHTML = '".$stDataLimiteSalarioFamilia."'; \n";
            } else {
               $js = "d.getElementById('stDataLimiteSalarioFamilia').innerHTML = '' \n";
            }
        }
    }

    return $js;
}

function validaInicioSalarioFamilia()
{
    if ( sistemaLegado::comparaDatas($_POST['stDtNascimentoDependente'],$_POST['dtInicioSalarioFamilia']) ) {
        $stJs .= "f.dtInicioSalarioFamilia.value='';\n";
        Sessao::write('dtInicioSalarioFamilia','');
        $stJs .= "alertaAviso('@Data-Início para Salário Família deve ser maior que a Data de Nascimento.','form','erro','".Sessao::getId()."');      \n";
    } else {
        Sessao::write('dtInicioSalarioFamilia',$_POST['dtInicioSalarioFamilia']);
        $stJs .= calculaDataLimiteSalarioFamilia($_POST['dtInicioSalarioFamilia']);
    }

    return $stJs;
}

function habilitaDependenteSalarioFamilia($inCodGrauParentesco)
{
    if ($inCodGrauParentesco == 15 or  $inCodGrauParentesco == 17 or $inCodGrauParentesco == 20 or $inCodGrauParentesco == 4) {
        $stJs .= "d.getElementById('boDependenteSalarioFamiliaSim').disabled = false;\n";
    } else {
        $stJs .= "d.getElementById('boDependenteSalarioFamiliaSim').disabled = true;\n";
        $stJs .= "d.getElementById('boDependenteSalarioFamiliaSim').checked  = false;\n";
        $stJs .= "d.getElementById('boDependenteSalarioFamiliaNao').checked  = true;\n";
        $stJs .= "d.getElementById('spnDependenteSalarioFamilia').innerHTML  = ''; \n";
    }

    return $stJs;
}

function comparaComDataNascimentoDependente($stCampo,$stRotulo)
{
    $dtComparacao = $_POST[$stCampo];
    $dtNascimento = $_POST['stDtNascimentoDependente'];
    $stJs = "";
    if ($dtNascimento == "") {
        $stMensagem = "campo Data de Nascimento da Guia Dependentes inválido()!";
        $stJs .= "f.".$stCampo.".value = '';\n";
        $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
    } else {
        if ( $dtComparacao != "" and sistemaLegado::comparaDatas($dtNascimento,$dtComparacao) ) {
            $stMensagem = $stRotulo." (".$dtComparacao.") não pode ser anterior à Data de Nascimento do Dependente(".$dtNascimento.")!";
            $stJs .= "f.".$stCampo.".value = '';\n";
            $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
        }
    }

    return $stJs;
}

switch ($request->get("stCtrl")) {
    case 'preencheDadosCGMDependente':
        preencheDadosCGMDependente();
    break;
    case 'geraSpnDependenteSalarioFamilia':
        $stJs .= geraSpnDependenteSalarioFamilia();
    break;
    case 'limpaSpnDependenteSalarioFamilia':
        $stJs .= limpaSpnDependenteSalarioFamilia();
    break;
    case "habilitaCarteiraVacinacao":
        $stJs .= habilitaCarteiraVacinacao();
    break;
    case "habilitaComprovanteMatricula":
        $stJs .= habilitaComprovanteMatricula();
    break;
    case "incluirVacinacao":
        $stJs .= incluirVacinacao();
    break;
    case "excluirVacinacao":
        $stJs .= excluirVacinacao();
    break;
    case "limparVacinacao":
        $stJs .= limparVacinacao();
    break;
    case "incluirMatricula":
        $stJs .= incluirMatricula();
    break;
    case "excluirMatricula":
        $stJs .= excluirMatricula();
    break;
    case "limparMatricula":
        $stJs .= limparMatricula();
    break;
    case "incluirDependente":
        $stJs .= incluirDependente();
    break;
    case "alterarDependente":        
        $stJs .= alterarDependente();
    break;
    case "limparDependente":
        $stJs .= limparDependente();
    break;
    case "excluirDependente":
        $stJs .= excluirDependente();
    break;
    case "montaAlterarDependente":
        $stJs .= montaAlterarDependente();
    break;
    case "calculaDataLimiteSalarioFamilia":
        $stJs .= calculaDataLimiteSalarioFamilia();
    break;
    case "validaInicioSalarioFamilia":
        $stJs .= validaInicioSalarioFamilia();
    break;
    case "habilitaDependenteSalarioFamilia":
        $stJs .= habilitaDependenteSalarioFamilia($_POST['inCodGrauParentesco']);
    break;
    case "validarDataComprovanteMatricula":
        $stJs .= comparaComDataNascimentoDependente("dtApresentacaoComprovanteMatricula","Data de Apresentação da Carteira de Vacinação");
    break;
    case "validarDataCarteiraVacinacao":
        $stJs .= comparaComDataNascimentoDependente("dtApresentacaoCarteiraVacinacao","Data de Apresentação do Comprovante de Matrícula");
    break;
    case "montaCID":
        $stJs .= montaCID();
    break;
    case "buscaCIDDependente":
        $stJs .= buscaCIDDependente();
    break;
}
if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>

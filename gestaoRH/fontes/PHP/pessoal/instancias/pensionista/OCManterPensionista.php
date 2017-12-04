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
    * Página de Oculto de Manter Cadastro de Pensionista
    * Data de Criação: 14/08/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30894 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-16 11:06:40 -0200 (Ter, 16 Out 2007) $

    * Casos de uso: uc-04.04.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php"                            );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."ISelectFuncao.class.php"                                         );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionistaContaSalario.class.php"               );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCID.class.php"                                       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPensionista";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function processarForm()
{
    ;
    include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
    $obTCGM = new TCGM;
    $stFiltro = " WHERE CGM.numcgm = ".$_GET['inCGM'];
    $obTCGM->recuperaRelacionamento($rsCGM,$stFiltro);
    $arDataNascimento = explode("-",$rsCGM->getCampo("dt_nascimento"));
    $dtNascimento     = $arDataNascimento[2]."/".$arDataNascimento[1]."/".$arDataNascimento[0];
    $stJs .= "d.getElementById('dtNascimento').innerHTML = '".$dtNascimento."';  \n";
    $stSexo  = ($rsCGM->getCampo("sexo") == "f") ?  "Feminino" : "Masculino";
    $stJs .= "d.getElementById('stSexo').innerHTML       = '".$stSexo."';\n";
    $stJs .= "d.getElementById('stRG').innerHTML         = '".$rsCGM->getCampo("rg")."';\n";
    $stJs .= "d.getElementById('stCPF').innerHTML        = '".$rsCGM->getCampo("cpf")."';\n";
    $stJs .= "d.getElementById('stEndereco').innerHTML   = '".$rsCGM->getCampo("endereco").", ".$rsCGM->getCampo("bairro").", ".$rsCGM->getCampo("nom_municipio").", ".$rsCGM->getCampo("nom_uf")."';\n";
    $stJs .= "d.getElementById('stTelefone').innerHTML   = '".$rsCGM->getCampo("fone_residencial")."';\n";
    $stJs .= "d.getElementById('stCelular').innerHTML    = '".$rsCGM->getCampo("fone_celular")."';\n";
    $stJs .= montaListaPrevidencias($_GET['inCodContrato']);

    if ($_GET['stAcao'] == "alterar") {
        $stJs .= processarProcesso($_GET['stChaveProcesso']);
        $stJs .= "d.getElementById('nuPercentualPagamentoPensao').disabled = false;\n";
        $obTPessoalContratoPensionistaContaSalario = new TPessoalContratoPensionistaContaSalario;
        $stFiltro = " AND contrato_pensionista_conta_salario.cod_contrato = ".$_GET['inCodContrato'];
        $obTPessoalContratoPensionistaContaSalario->recuperaRelacionamento($rsContaSalario,$stFiltro);
        $stFiltro = " WHERE cod_banco = ".$rsContaSalario->getCampo("cod_banco");
                include_once(CAM_GT_MON_MAPEAMENTO."TMONAgencia.class.php");
                $obTMONAgencia = new TMONAgencia;
        $obTMONAgencia->recuperaTodos($rsAgencia,$stFiltro);
        $stJs .= "limpaSelect(f.stNumAgencia,0);                                    \n";
        $stJs .= "f.stNumAgencia[0] = new Option('Selecione','', 'selected');       \n";
        $inIndex = 1;
        while (!$rsAgencia->eof()) {
            $stJs .= "f.stNumAgencia[".$inIndex."] = new Option('".$rsAgencia->getCampo("nom_agencia")."','".$rsAgencia->getCampo("num_agencia")."', 'selected');       \n";
            $inIndex++;
            $rsAgencia->proximo();
        }

        $stJs .= "f.inCodBancoTxt.value   = '".$rsContaSalario->getCampo("num_banco")."';\n";
        $stJs .= "f.inCodBanco.value      = '".$rsContaSalario->getCampo("num_banco")."';\n";
        $stJs .= "f.stNumAgenciaTxt.value = '".$rsContaSalario->getCampo("num_agencia")."';\n";
        $stJs .= "f.stNumAgencia.value    = '".$rsContaSalario->getCampo("num_agencia")."';\n";
        $stJs .= "f.stNumConta.value = '".$rsContaSalario->getCampo("nr_conta")."';\n";
    }

    return $stJs;
}

function processarProcesso($stChaveProcesso="")
{
    ;
    $stChaveProcesso = ( $_GET['stChaveProcesso'] != "" ) ? $_GET['stChaveProcesso'] : $stChaveProcesso;
    $arProcesso              = explode("/",$stChaveProcesso);
    $inNumProcessaoConcessao = $arProcesso[0];
    $inAno                   = $arProcesso[1];
    if ($inNumProcessaoConcessao != "" and $inAno != "") {
        include_once(CAM_GA_PROT_MAPEAMENTO."TProcesso.class.php");
        $obTProcesso = new TProcesso;
        $stFiltro  = " WHERE ano_exercicio = '".$inAno."'";
        $stFiltro .= "   AND cod_processo = ".$inNumProcessaoConcessao;
        $obTProcesso->recuperaTodos($rsProcesso,$stFiltro);
        if ( $rsProcesso->getNumLinhas() == 1 ) {
            $arData = explode(" ",$rsProcesso->getCampo("timestamp"));
            $arData = explode("-",$arData[0]);
            $dtProcesso = ( $rsProcesso->getNumLinhas() == 1 ) ? $arData[2]."/".$arData[1]."/".$arData[0] : "";
            $stJs .= "d.getElementById('dtInclusaoProcesso').innerHTML = '".$dtProcesso."';    \n";
            $stJs .= "f.stChaveProcesso.value = '".$stChaveProcesso."';                        \n";
        } else {
            $stJs .= "f.stChaveProcesso.value = '';                             \n";
            $stJs .= "d.getElementById('dtInclusaoProcesso').innerHTML = '';    \n";
        }
    }

    return $stJs;
}

function montaListaPrevidencias($inCodContrato="")
{
    ;
    $rsLista = new RecordSet;
    include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $stFiltro = " AND previdencia.cod_vinculo = 3";
    $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoPrevidencia->obTPrevidencia->setDado('cod_contrato',$_REQUEST['inCodContrato']);
    $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoPrevidencia->obTPrevidencia->recuperaLista($rsLista,$stFiltro);
    if ($inCodContrato != "") {
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionistaPrevidencia.class.php" );
        $obTPessoalContratoPensionistaPrevidencia = new TPessoalContratoPensionistaPrevidencia;
        $stFiltro = " AND contrato_pensionista_previdencia.cod_contrato = ".$inCodContrato;
        $obTPessoalContratoPensionistaPrevidencia->recuperaRelacionamento($rsPrevidencia,$stFiltro);

        $arPrevidencias = array();
        $arPrevidenciaBoExcluido = array();

        while (!$rsPrevidencia->eof()) {
            $arPrevidencias[] = $rsPrevidencia->getCampo("cod_previdencia");
            $arPrevidenciaBoExcluido[] = $rsPrevidencia->getCampo("bo_excluido");
            $rsPrevidencia->proximo();
        }

        $arLista = (is_array($rsLista->getElementos())) ? $rsLista->getElementos() : array();
        foreach ($arLista as $inIndex=>$arTemp) {
            if ( in_array($arTemp['cod_previdencia'],$arPrevidencias) ) {
                if ($arPrevidenciaBoExcluido[0]== f) {
                    $arTemp["booleano"] = true;
                } else {
                    $arTemp["booleano"] = false;
                }
            }
            $arLista[$inIndex] = $arTemp;
        }
        $rsLista->preenche($arLista);
    }
    $obLista = new Lista;
    $obLista->setTitulo("Previdência");
    $obLista->setRecordSet( $rsLista );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Código" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Descrição" );
    $obLista->ultimoCabecalho->setWidth( 70 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo" );
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obChkPrevidencia = new CheckBox;
    $obChkPrevidencia->setName           ( "inCodPrevidencia_[tipo_previdencia]_[cod_previdencia]_"  );
    $obChkPrevidencia->setChecked ( $arTemp["booleano"] );

    $obLista->addDadoComponente( $obChkPrevidencia );
    $obLista->ultimoDado->setCampo( "booleano" );
    $obLista->ultimoDado->setAlinhamento('CENTRO');
    $obLista->commitDadoComponente();

    $obHdnPrevidencia = new Hidden;
    $obHdnPrevidencia->setName           ( "stTipoAbaPrevidencia"   );
    $obHdnPrevidencia->setValue          ( 'tipo_previdencia' );

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "cod_previdencia" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "descricao" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "tipo_previdencia" );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnPrevidencia').innerHTML = '".$stHtml."';";

    return $stJs;
}

function validarPercentual()
{
    ;
    $nuPercentualPagamentoPensao = str_replace(",",".",$_GET['nuPercentualPagamentoPensao']);
    if ($nuPercentualPagamentoPensao > 100) {
        $stJs .= "f.nuPercentualPagamentoPensao.value = '';                 \n";
        $stJs .= "d.getElementById('nuPercentualPagamentoPensao').focus();  \n";
    } else {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionista.class.php");
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato;
        $stFiltro = " WHERE registro = ".$_GET['inContrato'];
        $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
        $obTPessoalContratoPensionista = new TPessoalContratoPensionista;
        $stFiltro  = " AND contrato_pensionista.cod_contrato_cedente = ".$rsContrato->getCampo("cod_contrato");
        $stFiltro .= ( $_GET['inCodContratoPensionista'] != "" ) ? " AND contrato_pensionista.cod_contrato != ".$_GET['inCodContratoPensionista'] : "";
        $obTPessoalContratoPensionista->recuperaRelacionamento($rsContratoPensionista,$stFiltro);
        $nuTotalPercentual = 0;
        while (!$rsContratoPensionista->eof()) {
            $nuTotalPercentual += $rsContratoPensionista->getCampo("percentual_pagamento");
            $stFiltro = " WHERE cod_contrato = ".$rsContratoPensionista->getCampo("cod_contrato");
            $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
            $stContratos       .= $rsContrato->getCampo("registro").",";
            $rsContratoPensionista->proximo();
        }
        if ( ($nuPercentualPagamentoPensao + $nuTotalPercentual) > 100 ) {
            $stContratos = substr($stContratos,0,strlen($stContratos)-1);
            $stJs .= "f.nuPercentualPagamentoPensao.value = '';                 \n";
            $stJs .= "d.getElementById('nuPercentualPagamentoPensao').focus();  \n";
            $stJs .= "alertaAviso('Já existe um contrato (".$stContratos.") utilizando ".$nuTotalPercentual."% do valor do benefício, você pode utilizar apenas ".(100-$nuTotalPercentual)."%.','form','erro','".Sessao::getId()."');\n";
        }
    }

    return $stJs;
}

function validarDatas()
{
    ;
    if ($_GET['dtInicioBeneficio'] != "" and $_GET['dtEncerramentoBeneficio'] != "") {
        if ( SistemaLegado::comparaDatas($_GET['dtInicioBeneficio'],$_GET['dtEncerramentoBeneficio']) ) {
            $stJs .= "f.dtEncerramentoBeneficio.value = ''; \n";
            $stJs .= "alertaAviso('A Data de Encerramento do Benefício deve ser maior que Data de Início do Benefício.','form','erro','".Sessao::getId()."');\n";
        }
    }

    return $stJs;
}

function liberarPercentual()
{
    ;
    if ($_GET['inContrato'] != "") {
        $stJs .= "d.getElementById('nuPercentualPagamentoPensao').disabled = false;\n";
    } else {
        $stJs .= "d.getElementById('nuPercentualPagamentoPensao').disabled = true;\n";
    }

    return $stJs;
}

function buscaCID(){
    global $request;
            
    $inSiglaCID = strtoupper($request->get('inSiglaCID'));
    $stDescricao = "&nbsp;";
    
    if(!empty($inSiglaCID)){
        $stFiltro = " WHERE sigla ILIKE '".$inSiglaCID."%' ";
        $obTPessoalCID = new TPessoalCID;
        $obTPessoalCID->recuperaTodos($rsCID, $stFiltro);

        if(count($rsCID->arElementos) > 0){
            $stDescricao = $rsCID->getCampo('descricao');
            $stJs .= "d.getElementById('inCodCID').value = '".$rsCID->getCampo('cod_cid')."'; \n";
            $stJs .= "d.getElementById('dtDataLaudo').disabled = false; \n";
        }else{
            $stDescricao = "&nbsp;";
            $stJs .= "d.getElementById('inSiglaCID').value = ''; \n";
            $stJs .= "d.getElementById('dtDataLaudo').disabled = true; \n";
            $stJs .= "alertaAviso('CID ".$inSiglaCID." não encontrado!','form','erro','".Sessao::getId()."'); \n";
        }
    }else{
        $stJs .= "d.getElementById('inCodCID').value = ''; \n";
    }
    $stJs .= " d.getElementById('stCID').innerHTML = '".$stDescricao."'; \n";
    
    return $stJs;
}

function limparForm()
{
    ;
    $stJs .= "f.inCodProfissao.value = '';                                      \n";
    $stJs .= "f.inCodCID.value = '';                                            \n";
    $stJs .= "f.inCIDDependente.value = '';                                     \n";
    $stJs .= "f.inCodGrauParentesco.value = '';                                 \n";
    $stJs .= "f.stGrauParentesco.value = '';                                    \n";
    $stJs .= "f.stNumConta.value = '';                                     \n";
    $stJs .= "f.inCodBancoTxt.value = '';                                       \n";
    $stJs .= "f.inCodBanco.value = '';                                          \n";
    $stJs .= "limpaSelect(f.stNumAgencia,0);                                    \n";
    $stJs .= "f.stNumAgencia[0] = new Option('Selecione','', 'selected');       \n";
    $stJs .= "f.stNumAgenciaTxt.value = '';                                     \n";
    $stJs .= "f.stNumAgencia.value = '';                                        \n";
    $stJs .= "d.getElementById('inNomCGM').innerHTML = '';                      \n";
    $stJs .= "f.inContrato.value = '';                                          \n";
    //$stJs .= "f.inContratoPensionista.value = '';                               \n";
    $stJs .= "f.inNumBeneficio.value = '';                                      \n";
    $stJs .= "f.stChaveProcesso.value = '';                                     \n";
    //    $stJs .= "f.inNumProcessaoConcessao.value = '';                             \n";
    //    $stJs .= "f.inAno.value = '';                                               \n";
    $stJs .= "d.getElementById('dtInclusaoProcesso').innerHTML = '';            \n";
    $stJs .= "f.inCodTipoDependencia.value = '';                                \n";
    $stJs .= "f.nuPercentualPagamentoPensao.value = '';                         \n";
    $stJs .= "d.getElementById('nuPercentualPagamentoPensao').disabled = true;  \n";
    $stJs .= "f.dtInicioBeneficio.value = '';                                   \n";
    $stJs .= "f.dtEncerramentoBeneficio.value = '';                             \n";
    $stJs .= "d.getElementById('spnCalculoPensao').innerHTML = '';              \n";
    $stJs .= "f.stMotivoEncerramento.value = '';                                \n";
    //$stJs .= "f.inCodLotacao.value = '';                                        \n";
    //$stJs .= "d.getElementById('stLotacao').innerHTML = '';                     \n";
    $stJs .= "if( document.frm.inCodOrganogramaClassificacao )	  	    \n";
    $stJs .= "document.frm.inCodOrganogramaClassificacao.value='0.00.00';       \n";
    $stJs .= "document.frm.inCodOrganograma_1.selectedIndex = 0 ;     	    \n";
    $stJs .= "document.frm.inCodOrganograma_2.selectedIndex = 0 ;     	    \n";
    $stJs .= "document.frm.inCodOrganograma_3.selectedIndex = 0 ;     	    \n";
    $stJs .= "for (i=0 ; i<f.elements.length ; i++) {                           \n";
    $stJs .= "    if (f.elements[i].type == 'checkbox') {                       \n";
    $stJs .= "        f.elements[i].checked = false;                            \n";
    $stJs .= "    }                                                             \n";
    $stJs .= "}                                                                 \n";

    return $stJs;
}

function salvarForm()
{
    ;
    if ($_GET['inContrato'] != "") {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionista.class.php");
        $obTPessoalContratoPensionista = new TPessoalContratoPensionista;
        $stFiltro  = " AND pensionista.numcgm = ".$_GET['inCGM'];
        $stFiltro .= " AND registro_servidor.registro = ".$_GET['inContrato'];
        $obTPessoalContratoPensionista->recuperaPensionistas($rsContratoPensionista,$stFiltro);
        if ( $rsContratoPensionista->getNumLinhas() < 0 ) {
            $stJs .= "boSalvar = false ;                                                            \n";
            $stJs .= "inPrevidenciaOficial = 0;                                                     \n";
            $stJs .= "for (i=0 ; i<f.elements.length ; i++) {                                       \n";
            $stJs .= "    if (f.elements[i].type == 'checkbox' && f.elements[i].checked == true) {  \n";
            $stJs .= "        if (f.elements[i].name.search('Oficial')) {                             \n";
            $stJs .= "            inPrevidenciaOficial++;                                           \n";
            $stJs .= "        }                                                                     \n";
            $stJs .= "        boSalvar = true;                                                      \n";
            $stJs .= "    }                                                                         \n";
            $stJs .= "}                                                                             \n";
            $stJs .= "if (inPrevidenciaOficial > 1) {                                           \n";
            $stJs .= "    alertaAviso('Selecione apenas uma previdência oficial!()','form','erro','".Sessao::getId()."');\n";
            $stJs .= "} else {                                                                    \n";
            $stJs .= "    parent.frames[2].Salvar();                                            \n";
            $stJs .= "}                                                                         \n";
        } else {
            $stJs .= "alertaAviso('O CGM ".$_GET['inCGM']."-".$rsContratoPensionista->getCampo("nom_cgm_pensionista")." já está cadastrado como pensionista para o contrato ".$_GET['inContrato']."!()','form','erro','".Sessao::getId()."');\n";
        }
    } else {
        $stJs .= "alertaAviso('@Campo Matrícula da guia Informações do Pensionista inválido!()','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function verificaContrato()
{
    ;
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");

    if ( !empty($_GET['inContrato']) ) {
        $obTPessoalContrato = new TPessoalContrato;
        $stFiltro = " AND contrato.registro = ".$_GET['inContrato'];
        $obTPessoalContrato->recuperaCgmDoRegistroServidor( $rsContrato, $stFiltro);

        $stJs = '';
        if ( $rsContrato->getNumLinhas() === -1 ) {
            $stJs .= "d.getElementById('inNomCGM').innerHTML = '';                      							 	   \n";
            $stJs .= "f.inContrato.value = '';                                          								   \n";
            $stJs .= "f.inContrato.focus();  													   \n";
            $stJs .= "alertaAviso('@Campo Matrícula do Gerador do Benefício inválida!(".$_GET['inContrato'].")','form','erro','".Sessao::getId()."');  \n";
        } else {
            $stJs .= liberarPercentual();
        }
    } else {
        $stJs .= "d.getElementById('inNomCGM').innerHTML = '';                      									\n";
        $stJs .= "f.inContrato.value = '';                                          									\n";
        $stJs .= "f.inContrato.focus();  														\n";
        $stJs .= "alertaAviso('@Campo Matrícula do Gerador do Benefício inválida!(".$_GET['inContrato'].")','form','erro','".Sessao::getId()."');  \n";
    }

    return $stJs;
}

function geraHTMLCalculoPensao()
{
    $obChkCalculoPensao = new SimNao();
    $obChkCalculoPensao->setRotulo             ( "Calcular Saldo da Pensão"                                 );
    $obChkCalculoPensao->setTitle              ( "Marcar como SIM, para que o sistema processe o saldo de salário e décimo na folha rescisão."   );
    $obChkCalculoPensao->setName               ( "boCalculoPensao"                                          );
    $obChkCalculoPensao->setChecked            ( true                                                       );
    $obChkCalculoPensao->obRadioSim->setId     ( "boCalculoPensaoSim"					);
    $obChkCalculoPensao->obRadioSim->setValue  ( "true"							);
    $obChkCalculoPensao->obRadioNao->setId     ( "boCalculoPensaoNao"					);
    $obChkCalculoPensao->obRadioNao->setValue  ( "false"							);
    $obChkCalculoPensao->montaHtml();

    $obFormulario = new Formulario();
    $obFormulario->addComponente($obChkCalculoPensao);
    $obFormulario->montaInnerHTML();

    $stJs .= "d.getElementById('spnCalculoPensao').innerHTML = '".$obFormulario->getHTML()."';";
    $stJs .= "d.getElementById('spnCalculoPensao').style.display = 'block';";

    return $stJs;
}

function displayNoneHTMLCalculoPensao()
{
    $stJs .= "d.getElementById('spnCalculoPensao').style.display = 'none';";

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "processarForm":
        $stJs .= processarForm();
        break;
    case "processarProcesso":
        $stJs .= processarProcesso();
        break;
    case "validarPercentual":
        $stJs .= validarPercentual();
        break;
    case "validarDatas":
        $stJs .= validarDatas();
        break;
    case "geraHTMLCalculoPensao":
        $stJs .= validarDatas();
        if (!empty($_GET['dtEncerramentoBeneficio'])) {
            $stJs .= geraHTMLCalculoPensao();
        } else {
            $stJs .= displayNoneHTMLCalculoPensao();
        }
        break;
    case "liberarPercentual":
        $stJs .= liberarPercentual();
        break;
    case "limparForm":
        $stJs .= limparForm();
        break;
    case "salvarForm":
        $stJs .= salvarForm();
        break;
    case "verificaContrato":
        $stJs .= verificaContrato();
        break;
    case "buscaLotacao":
        $stJs .= buscaLotacao();
        break;
    case "buscaCID":
        $stJs .= buscaCID();
    break;

}

if ($stJs) {
    echo $stJs;
}
?>

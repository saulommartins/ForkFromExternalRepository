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
    * Página de Processamento do Férias
    * Data de Criação: 09/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 31464 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-03-26 15:16:30 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-04.04.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionista.class.php"                           );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalPensionista.class.php"                                   );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalPensionistaCid.class.php"                                );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAtributoContratoPensionista.class.php"                   );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionistaPrevidencia.class.php"                );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionistaOrgao.class.php"                      );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionistaProcesso.class.php"                   );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionistaContaSalario.class.php"               );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"                                      );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                                         );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalRescisaoContrato.class.php"                                 );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php"                                              );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONAgencia.class.php"                                            );

$arLink = Sessao::read('link');
$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterPensionista";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS   = "JS".$stPrograma.".js";

$obErro = new Erro;
$obTPessoalContrato                         = new TPessoalContrato;
$obTPessoalPensionista                      = new TPessoalPensionista;
$obTPessoalPensionistaCid                   = new TPessoalPensionistaCid;
$obTPessoalContratoPensionista              = new TPessoalContratoPensionista;
$obTPessoalContratoPensionistaOrgao         = new TPessoalContratoPensionistaOrgao;
$obTPessoalContratoPensionistaPrevidencia   = new TPessoalContratoPensionistaPrevidencia;
$obTPessoalContratoPensionistaProcessao     = new TPessoalContratoPensionistaProcesso;
$obTPessoalContratoPensionistaContaSalario  = new TPessoalContratoPensionistaContaSalario;
$obTPessoalAtributoContratoPensionista      = new TPessoalAtributoContratoPensionista;
$obRCadastroDinamico                        = new RCadastroDinamico;
$obRCadastroDinamico->setPersistenteValores ( new TPessoalAtributoContratoPensionista );
$obRPessoalRescisaoContrato                 = new RPessoalRescisaoContrato;

$obTPessoalPensionistaCid->obTPessoalPensionista                            = &$obTPessoalPensionista;
$obTPessoalContratoPensionista->obTPessoalPensionista                       = &$obTPessoalPensionista;
$obTPessoalContratoPensionista->obTPessoalContrato                          = &$obTPessoalContrato;
$obTPessoalContratoPensionistaOrgao->obTPessoalContratoPensionista          = &$obTPessoalContratoPensionista;
$obTPessoalContratoPensionistaPrevidencia->obTPessoalContratoPensionista    = &$obTPessoalContratoPensionista;
$obTPessoalContratoPensionistaProcessao->obTPessoalContratoPensionista      = &$obTPessoalContratoPensionista;
$obTPessoalContratoPensionistaContaSalario->obTPessoalContratoPensionista   = &$obTPessoalContratoPensionista;
$obTPessoalAtributoContratoPensionista->obTPessoalContratoPensionista       = &$obTPessoalContratoPensionista;


        
switch ($stAcao) {
    case "incluir":
        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $obTransacao->begin();
        $boTransacao = $obTransacao->getTransacao();
        $obErro = new Erro();
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        
        $obTPessoalContrato->proximoCod( $inCodContrato );
        $obTPessoalContrato->setDado("registro" ,($_POST['inContratoPensionista'])?$_POST['inContratoPensionista'] : $_REQUEST['inCodContratoPensionista']);
        $obTPessoalContrato->setDado("cod_contrato",$inCodContrato);
        $obTPessoalContrato->inclusao($boTransacao);
       
        $stFiltro = " WHERE registro = ".$_POST['inContrato'];
        $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro,'',$boTransacao);
        $obTPessoalPensionista->setDado("cod_contrato_cedente"  ,$rsContrato->getCampo("cod_contrato"));
        $obTPessoalPensionista->setDado("numcgm"                ,$_POST['inCGM']);
        $obTPessoalPensionista->setDado("cod_grau"              ,$_POST['inCodGrauParentesco']);
        $obTPessoalPensionista->setDado("cod_profissao"         ,$_POST['inCodProfissao']);
        $obTPessoalPensionista->inclusao($boTransacao);
        if ($_REQUEST['inSiglaCID'] != "") {
            $inCodCID = SistemaLegado::pegaDado('cod_cid', 'pessoal.cid',' WHERE sigla = '."'".$_REQUEST['inSiglaCID']."'",$boTransacao);
            $obTPessoalPensionistaCid->setDado("cod_cid"            ,$inCodCID);
        }
        
        if ($_REQUEST['dtDataLaudo'] != "") {
            $obTPessoalPensionistaCid->setDado("data_laudo"         ,$_REQUEST['dtDataLaudo']);
        }
        if ($_REQUEST['dtDataLaudo'] != "" || $_REQUEST['inSiglaCID'] != "") {
            $obTPessoalPensionistaCid->inclusao($boTransacao);
        }

        $obTPessoalContratoPensionista->setDado("cod_dependencia"       ,$_POST['inCodTipoDependencia']);
        $obTPessoalContratoPensionista->setDado("num_beneficio"         ,$_POST['inNumBeneficio']);
        $obTPessoalContratoPensionista->setDado("percentual_pagamento"  ,$_POST['nuPercentualPagamentoPensao']);
        $obTPessoalContratoPensionista->setDado("dt_inicio_beneficio"   ,$_POST['dtInicioBeneficio']);
        $obTPessoalContratoPensionista->setDado("dt_encerramento"       ,$_POST['dtEncerramentoBeneficio']);
        $obTPessoalContratoPensionista->setDado("motivo_encerramento"   ,$_POST['stMotivoEncerramento']);
        $obTPessoalContratoPensionista->inclusao($boTransacao);
        $obTPessoalContratoPensionistaOrgao->setDado("cod_orgao",$_POST["hdnUltimoOrgaoSelecionado"]);
        $obTPessoalContratoPensionistaOrgao->inclusao($boTransacao);
        foreach ($_POST as $stCampo=>$stValor) {
            if ( substr($stCampo,0,16) == "inCodPrevidencia" ) {
                $arPrevidencia = explode("_",$stCampo);
                $obTPessoalContratoPensionistaPrevidencia->setDado("cod_previdencia",$arPrevidencia[2]);
                $obTPessoalContratoPensionistaPrevidencia->inclusao($boTransacao);
            }
        }
        if ($_POST['stChaveProcesso'] != "") {
            $arChaveProcesso = explode("/",$_POST['stChaveProcesso']);
            $obTPessoalContratoPensionistaProcessao->setDado("ano_exercicio",$arChaveProcesso[1]);
            $obTPessoalContratoPensionistaProcessao->setDado("cod_processo",$arChaveProcesso[0] );
            $obTPessoalContratoPensionistaProcessao->inclusao($boTransacao);
        }
        $obTMONBanco = new TMONBanco;
        $stFiltro = " WHERE num_banco = '".$_POST['inCodBancoTxt']."'";
        $obTMONBanco->recuperaTodos($rsBanco,$stFiltro);
        $stFiltro = " WHERE num_agencia = '".$_POST['stNumAgenciaTxt']."'";

        $obTMONAgencia = new TMONAgencia;
        $obTMONAgencia->recuperaTodos($rsAgencia,$stFiltro,'',$boTransacao);

        $obTPessoalContratoPensionistaContaSalario->setDado("cod_banco",$rsBanco->getCampo("cod_banco"));
        $obTPessoalContratoPensionistaContaSalario->setDado("cod_agencia",$rsAgencia->getCampo("cod_agencia"));
        $obTPessoalContratoPensionistaContaSalario->setDado("nr_conta",$_POST['stNumConta']);
        $obTPessoalContratoPensionistaContaSalario->inclusao($boTransacao);
        $obAtributos = new MontaAtributos;
        $obAtributos->setName      ( "Atributo_" );
        $obAtributos->recuperaVetor( $arChave    );
        //monta array de atributos dinamicos
        foreach ($arChave as $key => $value) {
            $arChaves = preg_split( "[^a-zA-Z0-9]" , $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode( "," , $value );
            }
            $obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
        $arChaveAtributoCandidato =  array( "cod_contrato" => $inCodContrato );
        $obRCadastroDinamico->setCodCadastro(7);
        $obRCadastroDinamico->obRModulo->setCodModulo ( 22 );
        $obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
        $obRCadastroDinamico->salvarValores($boTransacao);
        $stMensagem = "Pensionista (".$request->get("inCodContratoPensionista").") cadastrado com sucesso.";
        sistemaLegado::alertaAviso($pgFilt,$stMensagem ,"incluir","aviso", Sessao::getId(), "../");
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalPensionista );
    break;
    case "alterar":
        //Abre uma transacao para salvar na auditoria os dados da alteracao
        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $obTransacao->begin();
        $boTransacao = $obTransacao->getTransacao();
        $obErro = new Erro();
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        $obTPessoalPensionista->setDado("cod_pensionista"       ,$_POST['inCodPensionista']);
        $obTPessoalPensionista->setDado("cod_contrato_cedente"  ,$_POST['inCodContratoServidor']);
        $obTPessoalPensionista->setDado("numcgm"                ,$_POST['inCGM']);
        $obTPessoalPensionista->setDado("cod_grau"              ,$_POST['inCodGrauParentesco']);
        $obTPessoalPensionista->setDado("cod_profissao"         ,$_POST['inCodProfissao']);
        $obTPessoalPensionista->alteracao($boTransacao);
        if ($_POST['inCodCID'] != "") {
            $obTPessoalPensionistaCid->setDado("cod_cid"            ,$_POST['inCodCID']);
            $inCodCID = SistemaLegado::pegaDado('cod_cid', 'pessoal.pensionista_cid',
                                                ' WHERE cod_pensionista = '.$obTPessoalPensionista->getDado("cod_pensionista"). " and cod_contrato_cedente = ".$obTPessoalPensionista->getDado("cod_contrato_cedente"),$boTransacao);
            //if (empty($inCodCID)) {
           //     $obTPessoalPensionistaCid->inclusao();
           // } else {
           //     $obTPessoalPensionistaCid->alteracao();
           // }
        }
        if ($_REQUEST['dtDataLaudo']) {
            $obTPessoalPensionistaCid->setDado("data_laudo"         ,$_POST['dtDataLaudo']);
        }
        if ($_POST['dtDataLaudo'] != "" || $_POST['inCodCID'] != "") {
            if (empty($inCodCID)) {
                $obTPessoalPensionistaCid->inclusao($boTransacao);
            } else {
                $obTPessoalPensionistaCid->alteracao($boTransacao);
            }
        }

        $obTPessoalContrato->setDado("cod_contrato"          ,$_POST['inCodContratoPensionista']);
        $obTPessoalContratoPensionista->setDado("cod_dependencia"       ,$_POST['inCodTipoDependencia']);
        $obTPessoalContratoPensionista->setDado("num_beneficio"         ,$_POST['inNumBeneficio']);
        $obTPessoalContratoPensionista->setDado("percentual_pagamento"  ,$_POST['nuPercentualPagamentoPensao']);
        $obTPessoalContratoPensionista->setDado("dt_inicio_beneficio"   ,$_POST['dtInicioBeneficio']);
        $obTPessoalContratoPensionista->setDado("dt_encerramento"       ,$_POST['dtEncerramentoBeneficio']);
        $obTPessoalContratoPensionista->setDado("motivo_encerramento"   ,$_POST['stMotivoEncerramento']);
        $obTPessoalContratoPensionista->alteracao($boTransacao);
        $stFiltro  = " AND contrato_pensionista_orgao.cod_contrato = ".$_POST['inCodContratoPensionista'];
        $obTPessoalContratoPensionistaOrgao->recuperaRelacionamento($rsPensionistaOrgao,$stFiltro,'',$boTransacao);
        if ( $rsPensionistaOrgao->getCampo("cod_orgao") != $_POST["hdnUltimoOrgaoSelecionado"] ) {
            $obTPessoalContratoPensionistaOrgao->setDado("cod_orgao",$_POST["hdnUltimoOrgaoSelecionado"]);
            $obTPessoalContratoPensionistaOrgao->inclusao($boTransacao);
        }
        $boPrevidenciaExclusao = true;
        foreach ($_POST as $stCampo=>$stValor) {
            $stFiltro = " WHERE cod_contrato = ".$_POST['inCodContratoPensionista'];
            $obTPessoalContratoPensionistaPrevidencia->recuperaTodos($rsContratoPensionistaPrevidencia,$stFiltro,'',$boTransacao);
            if ( substr($stCampo,0,16) == "inCodPrevidencia") {
                $arPrevidencia = explode("_",$stCampo);
                $obTPessoalContratoPensionistaPrevidencia->setDado("cod_previdencia",$arPrevidencia[2]);
                $obTPessoalContratoPensionistaPrevidencia->inclusao($boTransacao);
                $boPrevidenciaExclusao = false;
            }
        }
        if ($rsContratoPensionistaPrevidencia->getCampo("cod_contrato") != null && $boPrevidenciaExclusao) {
            $obTPessoalContratoPensionistaPrevidencia->setDado("cod_contrato",$rsContratoPensionistaPrevidencia->getCampo("cod_contrato"));
            $obTPessoalContratoPensionistaPrevidencia->setDado("cod_previdencia",$rsContratoPensionistaPrevidencia->getCampo("cod_previdencia"));
            $obTPessoalContratoPensionistaPrevidencia->setDado("bo_excluido",true);
            $obTPessoalContratoPensionistaPrevidencia->inclusao($boTransacao);
        }
        if ($_POST['stChaveProcesso'] != "") {
            $arChaveProcesso = explode("/",$_POST['stChaveProcesso']);
            $obTPessoalContratoPensionistaProcessao->setDado("ano_exercicio",$arChaveProcesso[1]);
            $obTPessoalContratoPensionistaProcessao->setDado("cod_processo",$arChaveProcesso[0] );
            $obTPessoalContratoPensionistaProcessao->alteracao($boTransacao);
        }
        $stFiltro = " AND contrato_pensionista_conta_salario.cod_contrato = ".$_POST['inCodContratoPensionista'];
        $obTPessoalContratoPensionistaContaSalario->recuperaRelacionamento($rsContaSalario,$stFiltro,'',$boTransacao);
        if( $rsContaSalario->getCampo("num_agencia") != $_POST['stNumAgenciaTxt']
         or $rsContaSalario->getCampo("num_banco") != $_POST['inCodBancoTxt']
         or $rsContaSalario->getCampo("nr_conta") != $_POST['stNumConta'] ){
            $obTMONBanco = new TMONBanco;
            $stFiltro = " WHERE num_banco = '".$_POST['inCodBancoTxt']."'";
            $obTMONBanco->recuperaTodos($rsBanco,$stFiltro,'',$boTransacao);

            $stFiltro  = " WHERE num_agencia = '".$_POST['stNumAgenciaTxt']."'";
            $stFiltro .= "   AND cod_banco = ".$rsBanco->getCampo("cod_banco");
            $obTMONAgencia = new TMONAgencia;
            $obTMONAgencia->recuperaTodos($rsAgencia,$stFiltro,'',$boTransacao);

            $obTPessoalContratoPensionistaContaSalario->setDado("cod_banco",$rsBanco->getCampo("cod_banco"));
            $obTPessoalContratoPensionistaContaSalario->setDado("cod_agencia",$rsAgencia->getCampo("cod_agencia"));
            $obTPessoalContratoPensionistaContaSalario->setDado("nr_conta",$_POST['stNumConta']);
            $obTPessoalContratoPensionistaContaSalario->inclusao($boTransacao);
        }
        $obAtributos = new MontaAtributos;
        $obAtributos->setName      ( "Atributo_" );
        $obAtributos->recuperaVetor( $arChave    );
        //monta array de atributos dinamicos
        foreach ($arChave as $key => $value) {
            $arChaves = preg_split( "[^a-zA-Z0-9]" , $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode( "," , $value );
            }
            $obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
        $arChaveAtributoCandidato =  array( "cod_contrato" => $_POST['inCodContratoPensionista'] );
        $obRCadastroDinamico->setCodCadastro(7);
        $obRCadastroDinamico->obRModulo->setCodModulo ( 22 );
        $obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
        $obRCadastroDinamico->alterarValores($boTransacao);

        // Se a pensionista já tem rescisão de contrato com opção para calculo, resgata valor da data de rescisão
        $dataRescisaoContrato = SistemaLegado::pegaDado("dt_rescisao", "pessoal.contrato_pensionista_caso_causa", "WHERE cod_contrato = ".$_REQUEST['inCodContratoPensionista'],$boTransacao);
        $dataRescisaoContrato = SistemaLegado::dataToBr($dataRescisaoContrato);
        // Caso sua data de encerramento tenha sido alterada e possua rescisão, é necessário excluir seus antigos valores de calculo
        if (!empty($dataRescisaoContrato)) {
            if ($dataRescisaoContrato != $obTPessoalContratoPensionista->getDado("dt_encerramento")) {
                $obRPessoalRescisaoContrato->obRPessoalContrato->setCodContrato( $_POST['inCodContratoPensionista'] );
                $obRPessoalRescisaoContrato->excluirRescisaoContratoPensionista($boTransacao);
            }
        }


        // Caso tenha marcado opção de Rescisão de contrato encaminhará para a tela de rescindir para que sejam calculados os valores
        if ($_REQUEST['boCalculoPensao'] == 'true') {
            // Cria sessão para quando encaminhar para a página de rescisão saber qual método utilizar. Em (PRRescindirContrato.php).
            // Também é utilizado para a geração de relatório. Em (PREmitirTermoRescisao.php).
            sessao::write('incluirRescisaoContratoPensionista','incluirRescisaoPensionista');
            $obRPessoalRescisaoContrato->recuperaDadosRescisaoPensionista($_REQUEST['inCodContratoPensionista']);
            $dataPensionista = $obRPessoalRescisaoContrato->obTPessoalContratoPensionista->getDado('dt_inicio_beneficio');

            $stCampos  = "?".Sessao::getId()."&stAcao=incluir&inRegistro=".$_REQUEST['inRegistroRescisao'];
            $stCampos .= "&inNumCGM=".$_REQUEST['inCGM']."&stNomCGM=".$_REQUEST['stNomCGMRescisao'];
            $stCampos .= "&inCodContrato=".$_REQUEST['inCodContratoPensionista']."&dtPosse=".$dataPensionista;
            $stCampos .= "&dtNomeacao=".$dataPensionista."&dtAdmissao=".$dataPensionista;
            $stCampos .= "&dtRescisao=".$_REQUEST['dtEncerramentoBeneficio'];

            SistemaLegado::alertaAviso('../rescisaoContrato/FMRescindirContrato.php'.$stCampos, 'Redirecionando para calculo de saldo de salário e décimo na folha rescisão', 'incluir', 'aviso', Sessao::getId(), '../');
        } else {
            $stMensagem = "Pensionista alterado com sucesso.";
            sistemaLegado::alertaAviso($pgList,$stMensagem ,"incluir","aviso", Sessao::getId(), "../");
        }
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalPensionista );

    break;
    case "excluir":
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConcessaoDecimo.class.php"   );
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorComplementar.class.php"   );
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorPeriodo.class.php"   );
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDescontoExternoIRRF.class.php"   );
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDescontoExternoPrevidencia.class.php"   );
        
        $boFlagTransacao = false;
        $obTransacao = new Transacao;
      //  $obTransacao->begin();
        $boTransacao = $obTransacao->getTransacao();
        $obErro = new Erro();
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        
        //Início da verificação da exclusão do pensionista
        $arTabelasVerificacao = array(  "TFolhaPagamentoConcessaoDecimo",
                                        "TFolhaPagamentoContratoServidorComplementar",
                                        "TFolhaPagamentoContratoServidorPeriodo",
                                        "TFolhaPagamentoDescontoExternoIRRF",
                                        "TFolhaPagamentoDescontoExternoPrevidencia"
                                    );

        $stFiltro = " WHERE cod_contrato = ".$_GET['inCodContratoPensionista'];
        foreach ($arTabelasVerificacao as $stTabela) {
            $obTVerificacaoExclusao = new $stTabela;
            $obTVerificacaoExclusao->recuperaTodos($rsVerificacao,$stFiltro,"",$boTransacao);
            if ($rsVerificacao->getNumLinhas() > 0) {
                $stMensagem = "Exclusão não permitida, pensionista possui histórico de dados no sistema.";
                sistemaLegado::alertaAviso($pgList,$stMensagem ,"","error", Sessao::getId(), "../");
                $obErro->setDescricao($stMensagem);
                break;
            }
        }
        //Fim da verificação da exclusão do pensionista
   
        if(!$obErro->ocorreu()) {
              $obTPessoalPensionista->setDado("cod_pensionista",  $_GET['inCodPensionista']);
              $obTPessoalPensionista->setDado("cod_contrato_cedente", $_GET['inCodContratoServidor']);
              $obTPessoalContratoPensionista->setDado("cod_contrato",$_GET['inCodContratoPensionista']);
              $obTPessoalContratoPensionistaOrgao->exclusao($boTransacao);
              $obTPessoalContratoPensionistaProcessao->exclusao($boTransacao);
              $obTPessoalContratoPensionistaPrevidencia->exclusao($boTransacao);
              $obTPessoalContratoPensionistaContaSalario->exclusao($boTransacao);
              $obTPessoalAtributoContratoPensionista->exclusao($boTransacao);
              $obTPessoalContratoPensionista->exclusao($boTransacao);
              $obTPessoalPensionistaCid->exclusao($boTransacao);
              $obTPessoalPensionista->exclusao($boTransacao);
              $obTPessoalContrato->exclusao($boTransacao);
              $stMensagem = "Pensionista excluído com sucesso.";
              sistemaLegado::alertaAviso($pgList,$stMensagem ,"incluir","aviso", Sessao::getId(), "../");
              $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalPensionista );
        }
    break;
}

?>

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
    * Página de Formulário para cadastro de servdor
    * Data de criação : 16/12/2004

    * @author Programador: Rafael Almeida

    * Casos de uso: uc-04.04.07

    $Id: FMManterServidor.php 66017 2016-07-07 17:31:31Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php";
include_once CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorCid.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalCID.class.php";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
  
//Define o nome dos arquivos PHP
$stPrograma = "ManterServidor";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";
$pgProc              = "PR".$stPrograma.".php";
$pgOcul              = "OC".$stPrograma.".php";
$pgOculIdentificacao = "OC".$stPrograma."AbaIdentificacao.php";
$pgOculDocumentacao  = "OC".$stPrograma."AbaDocumentacao.php";
$pgOculContrato      = "OC".$stPrograma."AbaContrato.php";
$pgOculPrevidencia   = "OC".$stPrograma."AbaPrevidencia.php";
$pgOculDependentes   = "OC".$stPrograma."AbaDependentes.php";
$pgOculAtributos     = "OC".$stPrograma."AbaAtributos.php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

$request->set('stCtrl', '');

$inPaginacaoPg  = $request->get('pg');
$inPaginacaoPos = $request->get('pos');

SistemaLegado::BloqueiaFrames();

$obRPessoalServidor = new RPessoalServidor;
$obRPessoalServidor->addContratoServidor();
$boTransacao = "";
$obRConfiguracaoPessoal = new RConfiguracaoPessoal;
$obRConfiguracaoPessoal->Consultar( $boTransacao );
$stMascaraRegistro = $obRConfiguracaoPessoal->getMascaraRegistro();
$boGeracaoRegistro = $obRConfiguracaoPessoal->getGeracaoRegistro();

$inNumCGM = $request->get('inNumCGM');

$obRPessoalServidor->obRCGMPessoaFisica->setNumCGM( $inNumCGM );
$obRPessoalServidor->obRCGMPessoaFisica->consultarCGM($rsCGM);

if ( $rsCGM->getCampo("cod_escolaridade") != "" ) {
    $obRPessoalServidor->obRCGMPessoaFisica->consultarEscolaridade($stEscolaridade);
}
if ( $rsCGM->getCampo("cod_nacionalidade") != "" ) {
    $obRPessoalServidor->obRCGMPessoaFisica->consultarNacionalidade($stNacionalidade);
}
if ( $rsCGM->getCampo("dt_nascimento") != "" ) {
    $arDataNascimento = explode("-",$rsCGM->getCampo("dt_nascimento"));
    $stDataNascimento = $arDataNascimento[2]."/".$arDataNascimento[1]."/".$arDataNascimento[0];
}

$stSexo                     = ($rsCGM->getCampo("sexo") == "f") ?  "Feminino" : "Masculino" ;
$stCPF                      = $rsCGM->getCampo("cpf");
$stNomCGM                   = $rsCGM->getCampo("nom_cgm");
$stEnderecoIdentificacao    = $rsCGM->getCampo("tipo_logradouro"). " " .$rsCGM->getCampo("logradouro").", ".$rsCGM->getCampo("numero"). " " .$rsCGM->getCampo("complemento");
$stBairroIdentificacao      = $rsCGM->getCampo("bairro");
$stUFIdentificacao          = $rsCGM->getCampo("sigla_uf");
$stMunicipioIdentificacao   = $rsCGM->getCampo("nom_municipio");
$stCEPIdentificacao         = $rsCGM->getCampo("cep");
$stTelefoneIdentificacao    = $rsCGM->getCampo("fone_residencial");
$stRG                       = $rsCGM->getCampo("rg");
$stOrgaoEmissor             = $rsCGM->getCampo("orgao_emissor");
$stNumeroCnh                = $rsCGM->getCampo("num_cnh");
$stCategoriaCnh             = $rsCGM->getCampo("nom_cnh");
$stPisPasep                 = trim($rsCGM->getCampo("servidor_pis_pasep"));

$stAcao = $request->get('stAcao');
$inAba  = $request->get('inAba');

if ($stAcao == "incluir") {
    $inCodTipoPagamento = 3;
    $inCodTipoSalario = 3;
    $obRPessoalServidor->roUltimoContratoServidor->listarContratoServidor($rsContratoServidor,"");
    if ( $rsContratoServidor->getNumLinhas() > 0 ) {
        $stAcao = "alterar_servidor";
        $inCodServidor = $rsContratoServidor->getCampo("cod_servidor");
    }
}

$hdnContagemInicial = new Hidden;
$hdnContagemInicial->setName  ( 'stContagemInicial' );
$hdnContagemInicial->setValue ( $obRConfiguracaoPessoal->getContagemInicial() );

// mostra atributos selecionados
if ($stAcao == "incluir" or $stAcao == "alterar_servidor") {
    $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos, $stOrder = "", $boTransacao = "" );
} else {
    $arChaveAtributoCandidato =  array( "cod_contrato" => $request->get("inCodContrato") );
    $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
    $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos, $stFiltro="" ,$stOrder=" ORDER BY AD.cod_atributo ASC " ,$boTransacao);

    $arAtributos = $rsAtributos->getElementos();

    $arDadosNovosAtributo = array();
    foreach ($arAtributos as $inIndex=>$arAtributo) {
        if ($arAtributo["cod_tipo"] == 4) {
            $arDadosNovosAtributo[$arAtributo["cod_atributo"]]["valor"]             .= $arAtributo["valor"].",";
            $arDadosNovosAtributo[$arAtributo["cod_atributo"]]["valor_desc"]        .= $arAtributo["valor_desc"]."[][][]";
            if ($arDadosNovosAtributo[$arAtributo["cod_atributo"]]["posicao"] == "")
                $arDadosNovosAtributo[$arAtributo["cod_atributo"]]["posicao"]            = $inIndex;
            else
                $arDadosNovosAtributo[$arAtributo["cod_atributo"]]["posicao_excluir"]   .= $inIndex.",";
        }
    }

    include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoValorPadrao.class.php";
    $obTAdministracaoAtributoValorPadrao = new TAdministracaoAtributoValorPadrao();

    $arExcluirTotal = array();
    foreach ($arDadosNovosAtributo as $inCodAtributo=>$arDadosCadastrados) {
        $stFiltro  = " WHERE cod_modulo = 22";
        $stFiltro .= " AND cod_cadastro = 5";
        $stFiltro .= " AND cod_atributo = ".$inCodAtributo;
        $stFiltro .= " AND ativo is true";
        $stValorAtributoFiltro = substr($arDadosCadastrados["valor"],0,strlen($arDadosCadastrados["valor"])-1);
        if (trim($stValorAtributoFiltro) != "")
            $stFiltro .= " AND cod_valor NOT IN (".$stValorAtributoFiltro.")";
        $obTAdministracaoAtributoValorPadrao->recuperaTodos($rsValorPadrao,$stFiltro);

        while ( !$rsValorPadrao->eof() ) {
            $stValorPadrao     .= $rsValorPadrao->getCampo("cod_valor").",";
            $stValorDescPadrao .= $rsValorPadrao->getCampo("valor_padrao")."[][][]";
            $rsValorPadrao->proximo();
        }

        $arAtributos[$arDadosCadastrados["posicao"]]["valor"]               = substr($arDadosCadastrados["valor"],0,strlen($arDadosCadastrados["valor"])-1);
        $arAtributos[$arDadosCadastrados["posicao"]]["valor_desc"]          = substr($arDadosCadastrados["valor_desc"],0,strlen($arDadosCadastrados["valor_desc"])-6);
        $arAtributos[$arDadosCadastrados["posicao"]]["valor_padrao"]        = substr($stValorPadrao,0,strlen($stValorPadrao)-1);
        $arAtributos[$arDadosCadastrados["posicao"]]["valor_padrao_desc"]   = substr($stValorDescPadrao,0,strlen($stValorDescPadrao)-6);
        $arExcluir = explode(",",$arDadosCadastrados["posicao_excluir"]);

        foreach ($arExcluir as $inIndexExcluir) {
            if ($inIndexExcluir !== '')
                $arExcluirTotal[] = $inIndexExcluir;
        }

        $stValorPadrao = "";
        $stValorDescPadrao = "";
    }
    $arTemp = array ();
    foreach ($arAtributos as $inIndex=>$arAtributo) {
        if ( !in_array($inIndex,$arExcluirTotal) ) {
            $arTemp[] = $arAtributo;
        }
    }

    $rsAtributos = new RecordSet();
    $rsAtributos->preenche($arTemp);
}

if ($stAcao == "alterar" or $stAcao == "alterar_servidor") {
    ##############################################################################################################
    #                              Preenche informações da aba identificação                                     #
    ##############################################################################################################
    if($stAcao == "alterar")
        $inCodServidor = $request->get("inCodServidor");

    $stFiltro = " WHERE cod_servidor = ".$inCodServidor;
    $stOrdem = " GROUP BY cod_servidor, cod_cid, data_laudo ORDER BY cod_norma desc limit 1";
    $obTPessoalServidorCid = new TPessoalServidorCid;
    $obTPessoalServidorCid->recuperaCid($rsServidorCid, $stFiltro, $stOrdem);
    $obTPessoalCID = new TPessoalCID;
    $obTPessoalCID->setDado("cod_cid",$rsServidorCid->getCampo("cod_cid"));
    $obTPessoalCID->recuperaPorChave($rsCID);
    $obRPessoalServidor->setCodServidor( $inCodServidor );
    $obRPessoalServidor->consultarServidor( $rsServidor, $boTransacao );
    $stNomeFoto                     = $rsServidor->getCampo("caminho_foto");
    $inNumCGM                       = $rsServidor->getCampo("numcgm");
    $inCodEstadoCivil               = $rsServidor->getCampo("cod_estado_civil");
    $inCGMConjuge                   = $rsServidor->getCampo("numcgm_conjuge");
    $stNomConjuge                   = addslashes($rsServidor->getCampo("nome_conjuge"));
    $stNomePai                      = $rsServidor->getCampo("nome_pai");
    $stNomePai                      = stripslashes($stNomePai);           /* Retira as barras invertidas em caso de apostrofe */
    $stNomeMae                      = $rsServidor->getCampo("nome_mae");
    $stNomeMae                      = stripslashes($stNomeMae);           /* Retira as barras invertidas em caso de apostrofe */
    $inCodRaca                      = $rsServidor->getCampo("cod_rais");
    $inCodCID                       = $rsServidor->getCampo("cod_cid");
    $inSiglaCID                     = $rsCID->getCampo('sigla');
    $stDescricaoCID                 = $rsCID->getCampo('descricao');
    $inCodUF                        = $rsServidor->getCampo("cod_uf");
    $inCodTxtMunicipio              = $rsServidor->getCampo("cod_municipio");
    $inCodMunicipio                 = $rsServidor->getCampo("cod_municipio");
    $dtDataLaudo                    = SistemaLegado::dataToBr($rsServidorCid->getCampo("data_laudo"));

    ##############################################################################################################
    #                              Preenche informações da aba documentacao                                      #
    ##############################################################################################################
    $dtCadastroPis                  = $rsServidor->getCampo("dt_pis_pasep");
    $stCertificadoReservista        = trim($rsServidor->getCampo("nr_carteira_res"));
    $inCodCategoriaCertificado      = trim($rsServidor->getCampo("cat_reservista"));
    $inCodOrgaoExpedidorCertificado = trim($rsServidor->getCampo("origem_reservista"));
    $inTituloEleitor                = trim($rsServidor->getCampo("nr_titulo_eleitor"));
    $inZonaTitulo                   = trim($rsServidor->getCampo("zona_titulo"));
    $inSecaoTitulo                  = trim($rsServidor->getCampo("secao_titulo"));

    ##############################################################################################################
    #                              Preenche informações da aba contrato                                          #
    ##############################################################################################################
    if ($stAcao == "alterar") {
        $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $request->get("inCodContrato") );

        $obRPessoalServidor->roUltimoContratoServidor->listarDadosAbaContratoServidor( $rsContrato,$boTransacao );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalOcorrencia->setCodOcorrencia($rsContrato->getCampo("cod_ocorrencia"));
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalOcorrencia->listarOcorrencia( $rsOcorrencia,$boTransacao );
        $obRPessoalServidor->roUltimoContratoServidor->consultarContratoServidorSubDivisaoFuncao( $rsContratoServidorSubDivisaoFuncao, $boTransacao );
        $obRPessoalServidor->roUltimoContratoServidor->consultarContratoServidorRegimeFuncao( $rsContratoServidorRegimeFuncao, $boTransacao );

        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        list($stDia, $stMes, $stAno) = explode("/", $rsPeriodoMovimentacao->getCampo("dt_final"));
        $stVigencia = $stAno."-".$stMes."-".$stDia;

        $rsAgenciaBancariaFGTS = new recordset;
        if ( $rsContrato->getCampo("cod_agencia_fgts") and $rsContrato->getCampo("cod_banco_fgts") ) {
            $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaFGTS->setCodAgencia( $rsContrato->getCampo("cod_agencia_fgts") );
            $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaFGTS->obRMONBanco->setCodBanco( $rsContrato->getCampo("cod_banco_fgts") );
            $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaFGTS->listarAgencia( $rsAgenciaBancariaFGTS );
        }
        $rsAgenciaBancariaSalario = new recordset;
        if ( $rsContrato->getCampo("cod_agencia_salario") and  $rsContrato->getCampo("cod_banco_salario") ) {
            $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaSalario->setCodAgencia( $rsContrato->getCampo("cod_agencia_salario") );
            $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaSalario->obRMONBanco->setCodBanco( $rsContrato->getCampo("cod_banco_salario") );
            $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaSalario->listarAgencia( $rsAgenciaBancariaSalario );
        }
        $obRPessoalServidor->roUltimoContratoServidor->consultarContratoServidorConselho( $rsContratoServidorConselho, "", "", $boTransacao );
        $rsContrato->addFormatacao('salario','NUMERIC_BR');

        //Informações contratuais
        $inContrato                 = $rsContrato->getCampo("registro");
        $inCartaoPonto              = trim($rsContrato->getCampo("nr_cartao_ponto"));
        $inCodCedencia              = trim($rsContrato->getCampo("cod_tipo"));
        $dtDataNomeacao             = $rsContrato->getCampo("dt_nomeacao");
        Sessao::write("inCodVinculo", $obRPessoalServidor->roUltimoContratoServidor->consultarVinculoDoServidor($request->get("inCodContrato")));
        $stSituacao = $rsContrato->getCampo("situacao");

        $inCodNorma                 = $rsContrato->getCampo("cod_norma");
        $dtDataPosse                = $rsContrato->getCampo("dt_posse");
        $dtAdmissao                 = $rsContrato->getCampo("dt_admissao");
        $dtValidadeExameMedico      = $rsContrato->getCampo("dt_validade_exame");
        $inCodTipoAdmissao          = $rsContrato->getCampo("cod_tipo_admissao");
        $stFiltro                   = " WHERE cod_tipo_admissao =".$inCodTipoAdmissao;
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoAdmissao->listarTipoAdmissao($rsTipoAdmissao,$stFiltro);
        $stTipoAdmissao             = $rsTipoAdmissao->getCampo('descricao');
        $inCodVinculoEmpregaticio   = $rsContrato->getCampo("cod_vinculo");
        $stFiltro                   = " WHERE cod_vinculo = ".$inCodVinculoEmpregaticio;
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalVinculoEmpregaticio->listarVinculoEmpregaticio( $rsVinculoEmpregaticio,$stFiltro );
        $stVinculoEmpregaticio      = $rsVinculoEmpregaticio->getCampo("descricao");
        $inCodCategoria             = $rsContrato->getCampo("cod_categoria");
        $stFiltro                   = " AND cod_categoria = ".$inCodCategoria;
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCategoria->listarCategoria( $rsCategoria ,$stFiltro);
        $stCategoria                = $rsCategoria->getCampo("descricao");
        $stNumClassificacao         = $rsOcorrencia->getCampo("num_ocorrencia");
        //Informações do cargo
        $inCodRegime                = $rsContrato->getCampo("cod_regime");
        $inCodSubDivisao            = $rsContrato->getCampo("cod_sub_divisao");
        $inCodCargo                 = $rsContrato->getCampo("cod_cargo");
        $inCodEspecialidadeCargo    = $rsContrato->getCampo("cod_especialidade_cargo");
        //Informações da função
        $inCodRegimeFuncao          = $rsContratoServidorRegimeFuncao->getCampo("cod_regime");
        $inCodSubDivisaoFuncao      = $rsContratoServidorSubDivisaoFuncao->getCampo("cod_sub_divisao");
        $inCodFuncao                = $rsContrato->getCampo("cod_funcao");
        $inCodEspecialidadeFuncao   = ($rsContrato->getCampo("cod_especialidade_funcao") != "") ? $rsContrato->getCampo("cod_especialidade_funcao") : $rsContrato->getCampo("cod_especialidade_cargo");
        $dtDataAlteracaoFuncao      = $rsContrato->getCampo("ultima_vigencia");
        //Informações de lotação
        $inCodLotacao               = $rsContrato->getCampo("cod_orgao");
        $inCodLocal                 = $rsContrato->getCampo("cod_local");
        //Informações de FGTS
        $dtDataFGTS                 = $rsContrato->getCampo("dtopcaofgts");
        $inCodBancoFGTS             = $rsContrato->getCampo("num_banco_fgts");
        $inCodAgenciaFGTS           = $rsAgenciaBancariaFGTS->getCampo("num_agencia");
        $inContaCreditoFGTS         = trim($rsContrato->getCampo("conta_fgts"));
        //Informações salariais

        $rsContrato->addFormatacao("horas_mensais", "NUMERIC_BR");
        $rsContrato->addFormatacao("horas_semanais", "NUMERIC_BR");
        $stHorasMensais             = $rsContrato->getCampo("horas_mensais");
        $stHorasSemanais            = $rsContrato->getCampo("horas_semanais");
        $dtVigenciaSalario          = $rsContrato->getCampo("vigencia");
        Sessao::write('dtVigenciaSalario',$dtVigenciaSalario);
        $dtDataProgressao           = $rsContrato->getCampo("dt_inicio_progressao");
        $inCodPadrao                = $rsContrato->getCampo("cod_padrao");
        $inCodProgressao            = $rsContrato->getCampo("cod_nivel_padrao");
        $inSalario                  = $rsContrato->getCampo("salario");
        $inCodFormaPagamento        = $rsContrato->getCampo("cod_forma_pagamento");
        $inCodBancoSalario          = $rsContrato->getCampo("num_banco_salario");
        $inCodAgenciaSalario        = $rsAgenciaBancariaSalario->getCampo("num_agencia");
        $inContaSalario             = trim($rsContrato->getCampo("conta_salario"));
        $inCodTipoPagamento         = $rsContrato->getCampo("cod_tipo_pagamento");
        $inCodTipoSalario           = $rsContrato->getCampo("cod_tipo_salario");
        $boAdiantamento             = $rsContrato->getCampo("adiantamento");
        //Informações do quadro de horário
        $inCodGradeHorario          = $rsContrato->getCampo("cod_grade");
        //Dados do sindicato
        $inNumCGMSindicato          = $rsContrato->getCampo("numcgm_sindicato");
        //Conselho profissional
        $inCodConselho              = $rsContratoServidorConselho->getCampo("cod_conselho");
        $inNumeroConselho       = $rsContratoServidorConselho->getCampo("nr_conselho");
        $dtDataValidadeConselho = $rsContratoServidorConselho->getCampo("dt_validade");
    }
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );
$obForm->setEncType     ( "multipart/form-data" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

$obHdnAba = new Hidden;
$obHdnAba->setName     ( "inAba" );
$obHdnAba->setValue    ( $inAba  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( ""       );

include_once 'FMManterServidorAbaIdentificao.php';
include_once 'FMManterServidorAbaDocumentacao.php';
include_once 'FMManterServidorAbaContrato.php';
include_once 'FMManterServidorAbaDependentes.php';
include_once 'FMManterServidorAbaPrevidencia.php';
include_once $pgOcul;

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm   ( $obForm );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );

$obFormulario->addHidden ( $obHdnCtrl                );
$obFormulario->addHidden ( $obHdnAcao                );
$obFormulario->addHidden ( $obHdnAba                 );
$obFormulario->addHidden ( $hdnContagemInicial       );

#########################################################################################################
#                               ABA IDENTIFICAÇÃO                                                       #
#########################################################################################################
$obFormulario->addAba                ( "Identificação"                                                  );
$obFormulario->addTitulo             ( "Dados de Identificação"                                         );
$obFormulario->addSpan               ( $obSpnFoto                                                       );
$obFormulario->addHidden             ( $obHdnCGM                                                        );
$obFormulario->addComponente         ( $obLblCGM                                                        );
if ($stDataNascimento != "") {
    $obFormulario->addComponente     ( $obLblDataNascimento                                             );
    $obFormulario->addHidden         ( $obHdnDataNascimento                                             );
} else
    $obFormulario->addComponente     ( $obDtaNascimento                                                 );
$obFormulario->addComponente         ( $obLblSexo                                                       );
$obFormulario->addComponente         ( $obTxtPai                                                        );
$obFormulario->addComponente         ( $obTxtMae                                                        );
$obFormulario->addComponenteComposto ( $obTxtCodEstadoCivil , $obCmbEstadoCivil                         );
$obFormulario->addComponente         ( $obBscCGMConjuge                                                 );
$obFormulario->addComponenteComposto ( $obTxtCodRaca , $obCmbRaca                                       );
$obFormulario->addComponente         ( $obBscCID                                                        );
$obFormulario->addComponente         ( $obDtDataLaudo                                                   );
$obFormulario->addComponente         ( $obTxtNacionalidade                                              );
$obFormulario->addComponenteComposto ( $obTxtCodUFOrigem , $obCmbCodUFOrigem                            );
$obFormulario->addComponenteComposto ( $obTxtCodMunicipioOrigem , $obCmbCodMunicipioOrigem              );
$obFormulario->addComponente         ( $obLblEndereco                                                   );
$obFormulario->addComponente         ( $obLblBairro                                                     );
$obFormulario->addComponente         ( $obLblUF                                                         );
$obFormulario->addComponente         ( $obLblMunicipio                                                  );
$obFormulario->addComponente         ( $obLblCEP                                                        );
$obFormulario->addComponente         ( $obLblTelefone                                                   );
$obFormulario->addComponente         ( $obLblEscolaridade                                               );
$obFormulario->addHidden             ( $obHdnCodCID                                                     );
if ($stAcao == 'alterar' or $stAcao == 'alterar_servidor') {
    $obFormulario->addHidden         ( $obHdnCodServidor                                                );
    $obFormulario->addHidden         ( $obHdnCodContrato                                                );
    $obFormulario->addHidden         ( $obHdnCodSubDivisao                                              );
    $obFormulario->addHidden         ( $obHdnCodRegime                                                  );
    $obFormulario->addHidden         ( $obHdnCodCargo                                                   );
    $obFormulario->addHidden         ( $obHdnRegistro                                                   );
    $obFormulario->addHidden         ( $obHdnNomeFoto                                                   );
    $obFormulario->addHidden         ( $obHdnCodOrganograma                                             );
}

#########################################################################################################
#                               ABA DOCUMENTAÇÃO                                                        #
#########################################################################################################
$obFormulario->addAba               ( "Documentação" );
$obFormulario->addTitulo            ( "Documentação do Servidor"                                        );
if ($stCPF != "")
    $obFormulario->addComponente    ( $obLblCPF                                                         );
else
    $obFormulario->addComponente    ( $obTxtCPF                                                         );
$obFormulario->addComponente        ( $obLblRG                                                          );
$obFormulario->addComponente        ( $obLblOrgaoEmissor                                                );
$obFormulario->addComponente        ( $obLblCNH                                                         );
$obFormulario->addComponente        ( $obLblCategoriaCNH                                                );

if (trim($stPisPasep) == "" || trim(preg_replace("/[^0-9]/","",$stPisPasep)) === "00000000000")
    $obFormulario->addComponente        ( $obTxtPisPasep                                                );
else {
    $obFormulario->addComponente        ( $obLblPisPasep                                                );
    $obFormulario->addHidden            ( $obHdnPisPasep                                                );
}

$obFormulario->addComponente        ( $obTxtCadastroPis                                                 );
$obFormulario->addComponente        ( $obTxtTituloEleitor                                               );
$obFormulario->addComponente        ( $obTxtSecaoTitulo                                                 );
$obFormulario->addComponente        ( $obTxtZonaTitulo                                                  );

if ($rsCGM->getCampo("sexo") == "m" or $rsCGM->getCampo("sexo") == "") {
    $obFormulario->addComponente        ( $obTxtCertificadoReservista                                       );
    $obFormulario->addComponenteComposto( $obTxtCategoriaCertificado , $obCmbCategoriaCertificado           );
    $obFormulario->addComponenteComposto( $obTxtOrgaoExpedidorCertificado , $obCmbOrgaoExpedidorCertificado );
}

$obFormulario->addTitulo            ( "Dados de CTPS"                                                   );
$obFormulario->addComponente        ( $obTxtNumeroCTPS                                                  );
$obFormulario->addComponente        ( $obTxtSerieCTPS                                                   );
$obFormulario->addComponente        ( $obTxtDataCTPS                                                    );
$obFormulario->addComponente        ( $obTxtOrgaoExpedidorCTPS                                          );
$obFormulario->addComponente        ( $obCmbCodUFCTPS                                                   );
$obFormulario->defineBarraAba       ( array( $obBtnIncluirCTPS, $obBtnAlterarCTPS, $obBtnLimparCTPS ) ,'','' );
$obFormulario->addSpan              ( $obSpnCTPS                                                        );

$obFormulario->addTitulo            ( "Cópias digitais de documentos"                                   );
$obFormulario->addComponente        ( $obTipoArqDocDigital                                              );
$obFormulario->addComponente        ( $obLnkTipoArqDocDigital                                           );
$obFormulario->addComponente        ( $obLblArqValidos                                                  );
$obFormulario->addComponente        ( $obFileArqDigital                                                 );
$obFormulario->defineBarraAba       ( array( $obBtnIncluirArqDigital, $obBtnLimparArqDigital), '', ''   );
$obFormulario->addSpan              ( $obSpnListaArqDigital                                             );

#########################################################################################################
#                               ABA CONTRATO                                                            #
#########################################################################################################
$obFormulario->addAba               ( "Contrato", true                                                  );
$obFormulario->addTitulo            ( "Informações Contratuais"                                         );
if ($obIContratoDigitoVerificador->obRConfiguracaoPessoal->getGeracaoRegistro() != 'A')
    $obIContratoDigitoVerificador->geraFormulario( $obFormulario                                        );
elseif ($stAcao == 'alterar')
    $obFormulario->addComponente    ( $obIContratoDigitoVerificador                                     );
$obFormulario->addComponente        ( $obTxtCartaoPonto                                                 );
$obFormulario->addComponente        ( $obLblCedencia                                                    );
$obFormulario->addSpan              ( $obSpnCedencia                                                    );
$obFormulario->addHidden            ( $obHdnSituacao                                                    );
$obFormulario->addHidden            ( $obHdnContratoAlterar                                             );
$obFormulario->addComponente        ( $obLblSituacao                                                    );
$obFormulario->addSpan              ( $obSpanAposentadoria                                              );
$obFormulario->addComponente        ( $obTxtDataNomeacao                                                );
$obTipoNormaNorma->geraFormulario   ( $obFormulario                                                     );
$obFormulario->addComponente        ( $obTxtDataPosse                                                   );
$obFormulario->addComponente        ( $obDtAdmissao                                                     );
$obFormulario->addComponente        ( $obTxtDataProgressao                                              );
$obFormulario->addComponente        ( $obTxtDataExameMedico                                             );
$obFormulario->addComponente        ( $obBscTipoAdmissao                                                );
$obFormulario->addComponente        ( $obBscVinculoEmpregaticio                                         );
$obFormulario->addComponente        ( $obBscCategoria                                                   );
$obFormulario->addComponenteComposto( $obTxtAgentesNocivos,$obCmbAgentesNocivos                         );
$obFormulario->addSpan              (  $obSpnRescisao                                                   );
$obFormulario->addTitulo            ( "Informações do Cargo"                                            );
$obFormulario->addComponenteComposto( $obTxtCodRegime,$obCmbCodRegime                                   );
$obFormulario->addComponenteComposto( $obTxtCodSubDivisao,$obCmbCodSubDivisao                           );
$obFormulario->addComponenteComposto( $obTxtCargo,$obCmbCargo                                           );
$obFormulario->addComponenteComposto( $obTxtCodEspecialidadeCargo, $obCmbCodEspecialidadeCargo          );
$obFormulario->addTitulo            ( "Informações da Função"                                           );
$obFormulario->addComponenteComposto( $obTxtCodRegimeFuncao,$obCmbCodRegimeFuncao                       );
$obFormulario->addComponenteComposto( $obTxtCodSubDivisaoFuncao,$obCmbCodSubDivisaoFuncao               );
$obFormulario->addComponenteComposto( $obTxtCodFuncao,$obCmbCodFuncao                                   );
$obFormulario->addComponenteComposto( $obTxtCodEspecialidadeFuncao, $obCmbCodEspecialidadeFuncao        );
if ($stAcao == 'alterar') {
    $obFormulario->addComponente    ( $obDataAlteracaoFuncao                                            );
    $obFormulario->addHidden        ( $obHdnDataAlteracaoFuncao                                         );
}
if ($stAcao == 'incluir' && $boGeracaoRegistro == 'A')
    $obFormulario->addHidden        ( $obHdnRegistro                                                    );
$obFormulario->addTitulo            ( "Informações Salariais"                                           );
$obFormulario->addComponente        ( $obTxtHorasMensais                                                );
$obFormulario->addComponente        ( $obTxtHorasSemanais                                               );
$obFormulario->addComponenteComposto( $obTxtCodPadrao,$obCmbCodPadrao                                   );
$obFormulario->addHidden            ( $obHdnProgressao                                                  );
$obFormulario->addComponente        ( $obLblProgressao                                                  );
$obFormulario->addComponente        ( $obTxtSalario                                                     );
$obFormulario->addComponente        ( $obDtVigenciaSalario                                              );
$obFormulario->addComponenteComposto( $obTxtCodFormaPagamento,$obCmbCodFormaPagamento                   );
$obFormulario->addComponenteComposto( $obTxtCodBancoSalario,$obCmbCodBancoSalario                       );
$obFormulario->addComponenteComposto( $obTxtCodAgenciaSalario,$obCmbCodAgenciaSalario                   );
$obFormulario->addComponente        ( $obTxtContaSalario                                                );
$obFormulario->addComponenteComposto( $obTxtCodTipoPagamento,$obCmbCodTipoPagamento                     );
$obFormulario->addComponenteComposto( $obTxtCodTipoSalario,$obCmbCodTipoSalario                         );
$obFormulario->addComponente        ( $obChkAdiantamento                                                );
$obFormulario->addTitulo            ( "Informações de Lotação"                                          );
$obIMontaOrganograma->geraFormulario($obFormulario);

$obFormulario->addComponente        ( $obBscLocal                                                       );
$obFormulario->addTitulo            ( "Informações de FGTS"                                             );
$obFormulario->addComponente        ( $obTxtDataFGTS                                                    );
$obFormulario->addComponenteComposto( $obTxtCodBanco,$obCmbCodBanco                                     );
$obFormulario->addComponenteComposto( $obTxtCodAgenciaBanco,$obCmbAgenciaBanco                          );
$obFormulario->addComponente        ( $obTxtContaCredito                                                );
$obFormulario->addTitulo            ( "Quadro de Horário"                                               );
$obFormulario->addComponenteComposto( $obTxtGradeHorario,$obCmbGradeHorario                             );
$obFormulario->addSpan              ( $obSpnTurnos                                                      );
$obFormulario->addTitulo            ( "Dados do Sindicato"                                              );
$obFormulario->addComponente        ( $obBscCgmSindicato                                                );
$obFormulario->addComponente        ( $obTxtDataBase                                                    );
$obFormulario->addTitulo            ( "Conselho Profissional"                                           );
$obFormulario->addComponenteComposto( $obTxtConselho , $obCmbConselho                                   );
$obFormulario->addComponente        ( $obTxtNumeroConselho                                              );
$obFormulario->addComponente        ( $obTxtDataValidade                                                );

#########################################################################################################
#                               ABA PREVIDENCIA                                                         #
#########################################################################################################
$obFormulario->addAba               ( "Previdência"                                                     );
$obFormulario->addTitulo            ( "Tipos de Previdência"                                            );
$obFormulario->addSpan              ( $obSpnPrevidencia                                                 );

#########################################################################################################
#                               ABA DEPENDENTE                                                          #
#########################################################################################################
$obFormulario->addAba               ( "Dependentes"                                                     );
$obFormulario->addTitulo            ( "Dados do Dependente"                                             );
$obFormulario->addHidden            ( $obHdnTimestamp                                                   );
$obFormulario->addHidden            ( $obHdnCodDependente                                               );
$obFormulario->addComponenteComposto( $obTxtCodParentesco,$obCmbCodParentesco                           );
$obFormulario->addComponente        ( $obBscCGMDependente                                               );
$obFormulario->addSpan              ( $obSpnDataNascimentoDependente                                    );
$obFormulario->addComponente        ( $obLblSexoDependente                                              );
$obFormulario->addHidden            ( $obHdnSexoDependente                                              );
$obFormulario->addComponenteComposto( $obRdoDependenteSalarioFamiliaSim, $obRdoDependenteSalarioFamiliaNao );
$obFormulario->addHidden            ( $obHdnEvalDependenteSalarioFamilia                                );
$obFormulario->addSpan              ( $obSpnDependenteSalarioFamilia                                    );
$obFormulario->addComponenteComposto( $obTxtCodDependenteIR, $obCmbCodDependenteIR                      );
$obFormulario->addComponente        ( $obChkCarteiraVacinacao                                           );
$obFormulario->addComponente        ( $obChkComprovanteMatricula                                        );
$obFormulario->agrupaComponentes    ( array($obRdoDependentePrevSim, $obRdoDependentePrevNao)           );
$obFormulario->addTitulo            ( "Carteira de Vacinação"                                           );
$obFormulario->addComponente        ( $obTxtApresentacaoCarteiraVacinacao                               );
$obFormulario->agrupaComponentes    ( array( $obBtnIncluirVacinacao,$obBtnLimparVacinacao ) ,'',''      );
$obFormulario->addSpan              ( $obSpnVacinacao                                                   );
$obFormulario->addTitulo            ( "Comprovante de Matrícula"                                        );
$obFormulario->addComponente        ( $obTxtApresentacaoComprovanteMatricula                            );
$obFormulario->agrupaComponentes    ( array( $obBtnIncluirMatricula,$obBtnLimparMatricula ) ,'',''      );
$obFormulario->addSpan              ( $obSpnMatricula                                                   );
$obFormulario->defineBarraAba       ( array( $obBtnIncluirDependente ,$obBtnAlterarDependente, $obBtnLimparDependente ));
$obFormulario->addSpan              ( $obSpnDependente                                                  );

if ( $request->get('actVoltar') ) {
    $hdnVoltar = new Hidden;
    $hdnVoltar->setName  ( 'actVoltar' );
    $hdnVoltar->setValue ( $request->get('actVoltar') );
    $obFormulario->addHidden( $hdnVoltar );
}

#########################################################################################################
#                               ABA ATRIBUTOS                                                           #
#########################################################################################################
$obFormulario->addAba               ( "Atributos"                                                       );
$obMontaAtributos->geraFormulario   ( $obFormulario                                                     );

$obBtnIncluir = new Button;
$obBtnIncluir->setName                     ( "btnIncluirCampos"                        );
$obBtnIncluir->setValue                    ( "    Ok    "                              );
$obBtnIncluir->setTipo                     ( "button"                                  );
$obBtnIncluir->obEvento->setOnClick        ( "Salvar();"                               );

$obBtnLimpar = new Button;
$obBtnLimpar->setName                      ( "btnLimparCampos"                         );
$obBtnLimpar->setValue                     ( "Limpar"                                  );
$obBtnLimpar->setTipo                      ( "button"                                  );
$obBtnLimpar->obEvento->setOnClick         ( "limparCampos();"                         );

$stLink = '&pg='.$inPaginacaoPg.'&pos='.$inPaginacaoPos;
if ($stAcao == 'incluir' or $stAcao == "alterar_servidor")
    $obFormulario->defineBarra   (array( $obBtnIncluir, $obBtnLimpar ));
else {
    if ( $request->get('actVoltar') )
        $obFormulario->Cancelar('FLManterControlePensaoAlimenticia.php'."?".Sessao::getId()."&stAcao=alterar".$stLink);
    else
        $obFormulario->Cancelar($pgList."?".Sessao::getId()."&stAcao=alterar".$stLink);
}

$obFormulario->setFormFocus( $obFilFoto->getId() );
$obFormulario->show();

processarForm(true,$stAcao,$inAba);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

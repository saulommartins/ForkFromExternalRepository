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
inCategoriaCertificado

* Página de Processamento de Pessoal Servidor
* Data de Criação   : 21/12/2004

* @author Analista: Leandro Oliveira.
* @author Desenvolvedor: Rafael Almeida

* @ignore

$Id: PRManterServidor.php 66017 2016-07-07 17:31:31Z michel $

* Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php";
include_once CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php";
include_once CAM_GA_NORMAS_NEGOCIO."RNorma.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php";
include_once CAM_GA_CGM_MAPEAMENTO."TCGM.class.php";

$stAcao = $request->get('stAcao');
$inAba  = $request->get('inAba');

//Define o nome dos arquivos PHP
$stPrograma = "ManterServidor";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao."&inAba=".$inAba;
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao."&inAba=".$inAba;
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao."&inAba=".$inAba;
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao."&inAba=".$inAba;
$pgOcul = "OC".$stPrograma.".php";

$obRPessoalServidor = new RPessoalServidor;
$obRConfiguracaoPessoal = new RConfiguracaoPessoal;

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_" );
$obAtributos->recuperaVetor( $arChave    );

$boTransacao = "";
$obErro = new Erro;

$obErro = $obRConfiguracaoPessoal->Consultar( $boTransacao );

if (!$obErro->ocorreu()){
    $obTCGM = new TCGM;
    $stFiltro = " AND cgm.numcgm = ".$request->get('inNumCGM');
    $obErro = $obTCGM->recuperaRelacionamentoSintetico( $rsCGM, $stFiltro, '', $boTransacao );
}

if ( $obErro->ocorreu() ){
    sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_".$stAcao,"erro");
    exit();
}

if (trim($stAcao)=="alterar_servidor") {
    // Quando incluir mais de uma matrícula para o mesmo CGM voltar para a tela de inclusão
    $request->set('actVoltar', "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=incluir");
}

switch ($stAcao) {
    case "incluir":
        $nomeFoto = Sessao::read('FOTO_NAME');
        if ($nomeFoto) {
            //NOME DO ARQUIVO DA MINIATURA
            $nome_foto = sistemaLegado::getmicrotime() . "_" . rand(0, getrandmax());
            $imagem_gerada = CAM_GRH_PES_ANEXOS. $nome_foto . "_mini.jpg";
            $nome_foto = $nome_foto ."_mini.jpg";
            
            if(!copy(Sessao::read('FOTO_URL'), $imagem_gerada)){
                $obErro->setDescricao("Erro ao gravar foto. Contactar o administrador do sistema.");
            }
        }

        $obErro = $obRPessoalServidor->recuperaTodosRaca( $rsRaca, $boTransacao );

        if (!$obErro->ocorreu()){
            $obRPessoalServidor->setCodUF                          ( $request->get('inCodUF'));
            $obRPessoalServidor->setCodMunicipio                   ( $request->get('inCodMunicipio') );
            $obRPessoalServidor->setCodEstadoCivil                 ( $request->get('inCodEstadoCivil'));
            $obRPessoalServidor->setCodRais                        ( $request->get('inCodRaca'));
            $obRPessoalServidor->setCodRaca                        ( $rsRaca->getCampo('cod_raca') );
            $obRPessoalServidor->obRPessoalCID->setCodCID          ( $request->get('inCodCID'));
            $obRPessoalServidor->setCodEdital                      ( '0' );
            $obRPessoalServidor->obRCGMPessoaFisica->setNumCGM     ( $request->get('inNumCGM') );
            $obRPessoalServidor->obRCGMPessoaFisica->setCPF        ( $request->get('stCPF') );
            $obRPessoalServidor->setDataNascimento                 ( $request->get('stDataNascimento') );
            $obRPessoalServidor->setNomePai                        ( $request->get('stNomePai') );
            $obRPessoalServidor->setNomeMae                        ( $request->get('stNomeMae') );
            $obRPessoalServidor->obRCGMPessoaFisicaConjuge->setNumCgm( $request->get('inCGMConjuge') );

            //dados aba documentacao
            if (!$obErro->ocorreu() && !checkPIS($request->get('stPisPasep'), false)) {
                $obErro->setDescricao("Campo PIS/PASEP da guia Documentação é inválido(".$request->get('stPisPasep').").");
            }
    
            if (!$obErro->ocorreu()){
                $obRPessoalServidor->setPisPasep                       ( $request->get('stPisPasep')                  );
                $obRPessoalServidor->setDataPisPasep                   ( $request->get('dtCadastroPis')               );
                $obRPessoalServidor->setCarteiraReservista             ( $request->get('stCertificadoReservista')     );
                $obRPessoalServidor->setCategoriaReservista            ( $request->get('inCategoriaCertificado')      );
                $obRPessoalServidor->setOrigemReservista               ( $request->get('inOrgaoExpedidorCertificado') );
                $obRPessoalServidor->setNrTituloEleitor                ( $request->get('inTituloEleitor')             );
                $obRPessoalServidor->setZonaTitulo                     ( $request->get('inZonaTitulo')                );
                $obRPessoalServidor->setSecaoTitulo                    ( $request->get('inSecaoTitulo')               );
                $obRPessoalServidor->setCaminhoFoto                    ( $nome_foto                                   );

                $arrCTPS = Sessao::read('CTPS');
                if (is_array($arrCTPS) ) {
                    foreach ($arrCTPS as $arCTPS) {
                        $obRPessoalServidor->addRPessoalCTPS();
                        $obRPessoalServidor->roRPessoalCTPS->setNumero          ( $arCTPS['inNumeroCTPS']           );
                        $obRPessoalServidor->roRPessoalCTPS->setOrgaoExpedidor  ( $arCTPS['stOrgaoExpedidorCTPS']   );
                        $obRPessoalServidor->roRPessoalCTPS->setSerie           ( $arCTPS['stSerieCTPS']            );
                        $obRPessoalServidor->roRPessoalCTPS->setEmissao         ( $arCTPS['dtDataCTPS']             );
                        $obRPessoalServidor->roRPessoalCTPS->setUfCTPS          ( $arCTPS['inCodUF']                );
                    }
                }
                if ( !$obErro->ocorreu() and $stAcao != 'incluir' ) {
                    // verificando se a data de alteração da função é maior que a data de nomeação
                    if ($request->get('stContagemInicial') == 'dtNomeacao') {
                        if ( compData($request->get('dtDataAlteracaoFuncao') , $request->get('dtDataNomeacao') ) == 2 ) {
                            $obErro->setDescricao("A data de Alteração da função deve ser maior ou igual a Data de Nomeação!");
                        }
                    } else {
                        $dtDataAlteracaoFuncao = ( $request->get("dtDataAlteracaoFuncao") != "" ) ? $request->get("dtDataAlteracaoFuncao") : $request->get("hdnDataAlteracaoFuncao");
                        if ( compData( $dtDataAlteracaoFuncao, $request->get('dtDataPosse')) == 2 ) {
                            $obErro->setDescricao("A data de Alteração da função deve ser maior ou igual a Data da Posse!");
                        }
                    }
                }

                if ( !$obErro->ocorreu() ) {
                    //Dados aba contrato
                    //Informações contratuais
                    $obRPessoalServidor->addContratoServidor();

                    if ( $obRConfiguracaoPessoal->getGeracaoRegistro() == 'A' ) {
                        include_once CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php";
                        $obIContratoDigitoVerificadorAutomatico = new IContratoDigitoVerificador("", false, false, $boTransacao);
                        $obErro = $obIContratoDigitoVerificadorAutomatico->obRPessoalContrato->proximoRegistro($boTransacao);
                        if ( !$obErro->ocorreu() ) {
                            $inContratoAutomatico = $obIContratoDigitoVerificadorAutomatico->obRPessoalContrato->getRegistro();
                            $inContrato =  explode("-",$inContratoAutomatico);
                        }
                    } else {
                        $inContrato =  explode("-",$request->get("inContrato"));
                    }

                    if ( !$obErro->ocorreu() ) {
                        //Verifica Norma
                        $arCodNorma = explode("/",$request->get('stCodNorma'));
                        if (count($arCodNorma)>0) {
                            $stNumNorma = ltrim($arCodNorma[0],'0');
                            if ($stNumNorma == "") {
                                $stNumNorma = "0";
                            }
                            $obRNorma = new RNorma();
                            $obRNorma->setNumNorma( $stNumNorma );
                            $obRNorma->setExercicio( $arCodNorma[1] );
                            $obRNorma->listar($rsNorma, $boTransacao);
                            $stCodNorma = $rsNorma->getCampo('cod_norma');
                        }

                        $obRPessoalServidor->roUltimoContratoServidor->setRegistro                                              ( $inContrato[0]                             );
                        $obRPessoalServidor->roUltimoContratoServidor->setNroCartaoPonto                                        ( $request->get('inCartaoPonto')             );
                        $obRPessoalServidor->roUltimoContratoServidor->setAtivo                                                 ( "true"                                     );
                        $obRPessoalServidor->roUltimoContratoServidor->setNomeacao                                              ( $request->get('dtDataNomeacao')            );
                        $obRPessoalServidor->roUltimoContratoServidor->obRNorma->setCodNorma                                    ( $stCodNorma                                );
                        $obRPessoalServidor->roUltimoContratoServidor->setPosse                                                 ( $request->get('dtDataPosse')               );
                        $obRPessoalServidor->roUltimoContratoServidor->setAdmissao                                              ( $request->get('dtAdmissao')                );
                        $obRPessoalServidor->roUltimoContratoServidor->setValidadeExameMedico                                   ( $request->get('dtValidadeExameMedico')     );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoAdmissao->setCodTipoAdmissao               ( $request->get('inCodTipoAdmissao')         );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalVinculoEmpregaticio->setCodVinculoEmpregaticio ( $request->get('inCodVinculoEmpregaticio')  );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCategoria->setCodCategoria                     ( $request->get('inCodCategoria')            );
                        $obRPessoalServidor->roUltimoContratoServidor->setCodConselho                                           ( $request->get('inCodConselho')             );
                        $obRPessoalServidor->roUltimoContratoServidor->setNroConselho                                           ( $request->get('inNumeroConselho')          );
                        $obRPessoalServidor->roUltimoContratoServidor->setValidadeConselho                                      ( $request->get('dtDataValidadeConselho')    );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalOcorrencia->setCodOcorrencia                   ( $request->get('stNumClassificacao')        );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->setCodGrade                      ( $request->get('inCodGradeHorario')         );
                        $obRPessoalServidor->roUltimoContratoServidor->setVigenciaSalario                                       ( $request->get('dtVigenciaSalario')         );

                        //Informações do cargo
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->setCodRegime                 ( $request->get('inCodRegime')                 );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $request->get('inCodSubDivisao'));
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo                   ( $request->get('inCodCargo')                  );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->addEspecialidadeSubDivisao();
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade( $request->get('inCodEspecialidadeCargo')   );

                        //Informações da Função
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegimeFuncao->setCodRegime         ( $request->get('inCodRegimeFuncao')         );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->setCodCargo           ( $request->get('inCodFuncao')               );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->addEspecialidade();
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoEspecialidade->addEspecialidadeSubDivisao();
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoEspecialidade->setCodEspecialidade( $request->get('inCodEspecialidadeFuncao')  );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->addCargoSubDivisao();
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $request->get('inCodSubDivisaoFuncao'));

                        $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioBancoSalario->setNumBanco        ( $request->get('inCodBancoSalario')         );
                        $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaSalario->setCodAgencia    ( $request->get('inCodAgenciaSalario')       );

                        $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioBancoFGTS->setNumBanco           ( $request->get('inCodBancoFGTS')            );
                        $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaFGTS->setCodAgencia       ( $request->get('inCodAgenciaFGTS')          );

                        $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoSindicato->obRCGM->setNumCGM( $request->get('inNumCGMSindicato')         );

                        $obRPessoalServidor->roUltimoContratoServidor->obROrganogramaOrgao->setCodOrgao             ( $request->get("hdnUltimoOrgaoSelecionado") );

                        $obRPessoalServidor->roUltimoContratoServidor->obROrganogramaLocal->setCodLocal               ( $request->get('inCodLocal')                );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalFormaPagamento->setCodFormaPagamento ( $request->get('inCodFormaPagamento')       );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoPagamento->setCodTipoPagamento   ( $request->get('inCodTipoPagamento')        );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoSalario->setCodTipoSalario       ( $request->get('inCodTipoSalario')          );
                        $obRPessoalServidor->roUltimoContratoServidor->setDataBase                                    ( $request->get('dtDataBase')                );
                        $obRPessoalServidor->roUltimoContratoServidor->setOpcaoFgts                                   ( $request->get('dtDataFGTS')                );
                        $obRPessoalServidor->roUltimoContratoServidor->setContaCorrenteFgts                           ( $request->get('inContaCreditoFGTS')        );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->setCodPadrao( $request->get('inCodPadrao'));
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->addNivelPadrao();
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setCodNivelPadrao( $request->get('inCodProgressao') );
                        $obRPessoalServidor->roUltimoContratoServidor->setSalario                                     ( $request->get('inSalario')                 );
                        $obRPessoalServidor->roUltimoContratoServidor->setHrMensal                                    ( $request->get('stHorasMensais')            );
                        $obRPessoalServidor->roUltimoContratoServidor->setHrSemanal                                   ( $request->get('stHorasSemanais')           );
                        $obRPessoalServidor->roUltimoContratoServidor->setInicioProgressao                            ( $request->get('dtDataProgressao')          );
                        $obRPessoalServidor->roUltimoContratoServidor->setContaCorrenteSalario                        ( $request->get('inContaSalario')            );
                        $obRPessoalServidor->roUltimoContratoServidor->setAdiantamento                                ( $request->get('boAdiantamento')            );
                        $obRPessoalServidor->roUltimoContratoServidor->setAdiantamento                                ( $request->get('boAdiantamento')            );

                        //dados aba dependente
                        $arDependentes = Sessao::read('DEPENDENTE');
                        if ( is_array($arDependentes) ) {
                            for ($inCount=0; $inCount<count($arDependentes); $inCount++) {
                                $arDependente = $arDependentes[$inCount];
                                $obRPessoalServidor->addRPessoalDependente();

                                $obRPessoalServidor->roRPessoalDependente->obRCGMPessoaFisica->setNumCgm            ( $arDependente['inCGMDependente']                                    );
                                $obRPessoalServidor->roRPessoalDependente->obRCGMPessoaFisica->setDataNascimento    ( $arDependente['stDataNascimentoDependente']                         );
            
                                $obRPessoalServidor->roRPessoalDependente->setCodGrau                               ( $arDependente['stGrauParentesco']                                   );
                                $obRPessoalServidor->roRPessoalDependente->setDependenteInvalido                    ( ($arDependente['boFilhoEquiparado'] == 't') ? true : false          );
                                $obRPessoalServidor->roRPessoalDependente->setCarteiraVacinacao                     ( ($arDependente['boCarteiraVacinacao'] == 't') ? true : false        );
                                $obRPessoalServidor->roRPessoalDependente->setComprovanteMatricula                  ( ($arDependente['boComprovanteMatricula'] == 't') ? true : false     );
                                $obRPessoalServidor->roRPessoalDependente->setDependentePrev                  	    ( ($arDependente['boDependentePrev'] == 't') ? true : false           );

                                $obRPessoalServidor->roRPessoalDependente->setCodVinculo                            ( $arDependente['inCodDependenteIR']                                  );
                                $obRPessoalServidor->roRPessoalDependente->setDataInicioSalarioFamilia              ( $arDependente['dtInicioSalarioFamilia']                             ); 
                                $obRPessoalServidor->roRPessoalDependente->setDependenteSalarioFamilia              ( ($arDependente['boDependenteSalarioFamilia'] == 't') ? true : false );
                                $obRPessoalServidor->roRPessoalDependente->obRPessoalCID->setCodCid                 ( $arDependente['inCodCIDDependente']                                 );

                                if ($arDependente['boincluirDataNascimentoDespendente']) {
                                    $obRPessoalServidor->roRPessoalDependente->obRCGMPessoaFisica->setDataNascimento( $arDependente['stDataNascimentoDependente'] );
                                }

                                $arVacinacoes = $arDependente['VACINACAO'];
                                for ($inCounter=0; $inCounter<count($arVacinacoes); $inCounter++) {
                                    $arVacinacao = $arVacinacoes[$inCounter];
                                    $obRPessoalServidor->roRPessoalDependente->addRPessoalCarteiraVacinacao();
                                    $obRPessoalServidor->roRPessoalDependente->roRPessoalCarteiraVacinacao->setDataApresentacao( $arVacinacao['dtApresentacaoCarteiraVacinacao'] );
                                    $obRPessoalServidor->roRPessoalDependente->roRPessoalCarteiraVacinacao->setApresentada     ( $arVacinacao['boApresentadaVacinacao'] );
                                }
                                $arMatriculas = $arDependente['MATRICULA'];
                                for ($inCounter=0; $inCounter<count($arMatriculas); $inCounter++) {
                                    $arMatricula = $arMatriculas[$inCounter];
                                    $obRPessoalServidor->roRPessoalDependente->addRPessoalComprovanteMatricula();
                                    $obRPessoalServidor->roRPessoalDependente->roRPessoalComprovanteMatricula->setDataApresentacao( $arMatricula['dtApresentacaoComprovanteMatricula']);
                                    $obRPessoalServidor->roRPessoalDependente->roRPessoalComprovanteMatricula->setApresentada     ( $arMatricula['boApresentadaMatricula']);
                                }
                            }
                        }

                        //monta array de atributos dinamicos
                        foreach ($arChave as $key => $value) {
                            $arChaves = preg_split( "/[^a-zA-Z0-9]/" , $key );
                            $inCodAtributo = $arChaves[0];
                            if ( is_array($value) ) {
                                $value = implode( "," , $value );
                            }
                            $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                        }

                        $arArquivosDocumentos = Sessao::read("arArquivosDocumentos");
                        $arArquivosDocumentos = (is_array($arArquivosDocumentos)) ? $arArquivosDocumentos : array();

                        $obRPessoalServidor->setArquivosDocumentos($arArquivosDocumentos);

                        $obErro = $obRPessoalServidor->incluirServidor($boTransacao);
                    }
                }
            }
        }

        if (!$obErro->ocorreu()) {
            sistemaLegado::alertaAviso($pgFilt,"Matrícula: ".$obRPessoalServidor->roUltimoContratoServidor->getRegistro()." - ".$rsCGM->getCampo("nom_cgm"),"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
    case "alterar_servidor":
        $nomeFoto = Sessao::read('FOTO_NAME');
        if ($nomeFoto) {
            //NOME DO ARQUIVO DA MINIATURA
            $nome_foto = sistemaLegado::getmicrotime() . "_" . rand(0, getrandmax());
            $imagem_gerada = CAM_GRH_PES_ANEXOS. $nome_foto . "_mini.jpg";
            $nome_foto = $nome_foto ."_mini.jpg";

            if (!copy(Sessao::read('FOTO_URL'), $imagem_gerada)){
                $obErro->setDescricao("Erro ao gravar foto. Contactar o administrador do sistema.");
            }
        }

        if ( !$obErro->ocorreu() ){
            $obRPessoalServidor->setCodServidor                         ( $request->get('inCodServidor'));
            $obRPessoalServidor->setCodUF                               ( $request->get('inCodUF'));
            $obRPessoalServidor->setCodMunicipio                        ( $request->get('inCodMunicipio') );
            $obRPessoalServidor->setCodEstadoCivil                      ( $request->get('inCodEstadoCivil'));
            $obRPessoalServidor->setCodRais                             ( $request->get('inCodRaca'));
            $obErro = $obRPessoalServidor->recuperaTodosRaca            ( $rsRaca, $boTransacao );
            if ( !$obErro->ocorreu() ){
                $obRPessoalServidor->setCodRaca                             ( $rsRaca->getCampo('cod_raca') );
                $obRPessoalServidor->obRPessoalCID->setCodCID               ( $request->get('inCodCID'));
                $obRPessoalServidor->setCodEdital                           ( '0' );
                $obRPessoalServidor->obRCGMPessoaFisica->setNumCGM          ( $request->get('inNumCGM') );
                $obRPessoalServidor->obRCGMPessoaFisica->setCPF             ( $request->get('stCPF') );
                $obRPessoalServidor->setNomePai                             ( $request->get('stNomePai') );
                $obRPessoalServidor->setNomeMae                             ( $request->get('stNomeMae') );
                $obRPessoalServidor->obRCGMPessoaFisicaConjuge->setNumCgm   ( $request->get('inCGMConjuge') );
                $obRPessoalServidor->setDataNascimento                      ( $request->get('stDataNascimento') );
                $obRPessoalServidor->setDataLaudo                           ( $request->get('dtDataLaudo') );

                //dados aba documentacao
                if ( !$obErro->ocorreu() && $request->get('stPisPasep', '') != '' && !checkPIS($request->get('stPisPasep'), false) ) {
                    $obErro->setDescricao("Campo PIS/PASEP da guia Documentação é inválido(".$request->get('stPisPasep').").");
                }

                if ( !$obErro->ocorreu() ){
                    $obRPessoalServidor->setPisPasep            ( $request->get('stPisPasep')                  );
                    $obRPessoalServidor->setDataPisPasep        ( $request->get('dtCadastroPis')               );
                    $obRPessoalServidor->setCarteiraReservista  ( $request->get('stCertificadoReservista')     );
                    $obRPessoalServidor->setCategoriaReservista ( $request->get('inCategoriaCertificado')      );
                    $obRPessoalServidor->setOrigemReservista    ( $request->get('inOrgaoExpedidorCertificado') );
                    $obRPessoalServidor->setNrTituloEleitor     ( $request->get('inTituloEleitor')             );
                    $obRPessoalServidor->setZonaTitulo          ( $request->get('inZonaTitulo')                );
                    $obRPessoalServidor->setSecaoTitulo         ( $request->get('inSecaoTitulo')               );
                    $obRPessoalServidor->setCaminhoFoto         ( $nome_foto                                   );

                    $arrCTPS = Sessao::read('CTPS');
                    if (is_array($arrCTPS) ) {
                        foreach ($arrCTPS as $arCTPS) {
                            $obRPessoalServidor->addRPessoalCTPS();
                            $obRPessoalServidor->roRPessoalCTPS->setCodCTPS         ( $arCTPS['inCodCTPS']              );
                            $obRPessoalServidor->roRPessoalCTPS->setNumero          ( $arCTPS['inNumeroCTPS']           );
                            $obRPessoalServidor->roRPessoalCTPS->setOrgaoExpedidor  ( $arCTPS['stOrgaoExpedidorCTPS']   );
                            $obRPessoalServidor->roRPessoalCTPS->setSerie           ( $arCTPS['stSerieCTPS']            );
                            $obRPessoalServidor->roRPessoalCTPS->setEmissao         ( $arCTPS['dtDataCTPS']             );
                            $obRPessoalServidor->roRPessoalCTPS->setUfCTPS          ( $arCTPS['inCodUF']                );
                        }
                    }
                    if ( !$obErro->ocorreu() ) {
                        // verificando se a data de alteração da função é maior que a data de nomeação
                        $dtDataAlteracaoFuncao = ( $request->get("dtDataAlteracaoFuncao", '') != '' ) ? $request->get("dtDataAlteracaoFuncao") : $request->get("hdnDataAlteracaoFuncao");
                        if ($dtDataAlteracaoFuncao == "") {
                            $dtPosse    = implode('',array_reverse(explode('/',$request->get('dtDataPosse'))));
                            $dtAdmissao = implode('',array_reverse(explode('/',$request->get('dtAdmissao'))));
                            $dtNomeacao = implode('',array_reverse(explode('/',$request->get('dtDataNomeacao'))));

                            $dtDataAlteracaoFuncao = $request->get('dtDataPosse');

                            if ($dtNomeacao > $dtPosse) {
                                $dtDataAlteracaoFuncao = $request->get('dtDataNomeacao');
                            }
                            if ($dtAdmissao > $dtPosse) {
                                $dtDataAlteracaoFuncao = $request->get('dtAdmissao');
                            }
                            if ($dtNomeacao > $dtAdmissao) {
                                $dtDataAlteracaoFuncao = $request->get('dtDataNomeacao');
                            }
                        }

                        if ($request->get('stContagemInicial') == 'dtNomeacao') {
                            if ( compData($dtDataAlteracaoFuncao , $request->get('dtDataNomeacao') ) == 2 ) {
                                $obErro->setDescricao("A data de Alteração da função deve ser maior ou igual a Data de Nomeação!");
                            }
                        } else {
                            if ( compData( $dtDataAlteracaoFuncao, $request->get('dtDataPosse')) == 2 ) {
                                 $obErro->setDescricao("A data de Alteração da função deve ser maior ou igual a Data da Posse!");
                            }
                        }
                    }//

                    if ( !$obErro->ocorreu() ) {
                        //dados aba contrato
                        $obRPessoalServidor->addContratoServidor();
                        $obRPessoalServidor->roUltimoContratoServidor->setCodContrato                                           ( $request->get("inCodContrato")             );
                        $obRPessoalServidor->roUltimoContratoServidor->setAlteracaoFuncao                                       ( $dtDataAlteracaoFuncao                     );
                        $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioBancoSalario->setNumBanco                    ( $request->get('inCodBancoSalario')         );
                        $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaSalario->setCodAgencia                ( $request->get('inCodAgenciaSalario')       );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalVinculoEmpregaticio->setCodVinculoEmpregaticio ( $request->get('inCodVinculoEmpregaticio')  );
                        $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioBancoFGTS->setNumBanco                       ( $request->get('inCodBancoFGTS')            );
                        $obRPessoalServidor->roUltimoContratoServidor->obRMonetarioAgenciaFGTS->setCodAgencia                   ( $request->get('inCodAgenciaFGTS')          );
                        $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoSindicato->obRCGM->setNumCGM            ( $request->get('inNumCGMSindicato')         );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalOcorrencia->setCodOcorrencia                   ( $request->get('stNumClassificacao')        );
                        $obRPessoalServidor->roUltimoContratoServidor->setCodConselho                                           ( $request->get('inCodConselho')             );
                        $obRPessoalServidor->roUltimoContratoServidor->setNroConselho                                           ( $request->get('inNumeroConselho')          );
                        $obRPessoalServidor->roUltimoContratoServidor->setValidadeConselho                                      ( $request->get('dtDataValidadeConselho')    );

                        if ($request->get("inCodCargo")) {
                            $inCodCargoTMP = $request->get('inCodCargo');
                        } else {
                            $inCodCargoTMP = $request->get('inHdnCodCargo');
                        }
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($inCodCargoTMP);

                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->addEspecialidadeSubDivisao();
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade( $request->get('inCodEspecialidadeCargo')   );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();

                        if ($request->get('inHdnCodSubDivisao', '') != "") {
                            $inCodSubDivisao = $request->get('inHdnCodSubDivisao');
                        } else {
                            $inCodSubDivisao = $request->get('inCodSubDivisao');
                        }
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $inCodSubDivisao );

                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(false);
                        $obErro = $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsCargo, $boTransacao);

                        if ( !$obErro->ocorreu() ) {
                            if ($rsCargo->getNumLinhas() < 1) {
                                sistemaLegado::exibeAviso('Cargo Inválido. Norma não está mais em vigor.', 'n_alterar', 'erro');
                                exit;
                            }

                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegimeFuncao->setCodRegime ( $request->get('inCodRegimeFuncao')         );
                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->setCodCargo   ( $request->get('inCodFuncao')               );

                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($request->get('inCodFuncao'));
                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(false);
                            $obErro = $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsFuncao, $boTransacao);

                            if ( !$obErro->ocorreu() ) {
                                if ($rsFuncao->getNumLinhas() < 1) {
                                    sistemaLegado::exibeAviso('Função Inválida. Norma não está mais em vigor.', 'n_alterar', 'erro');
                                    exit;
                                }

                                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($inCodCargoTMP);
                    
                                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->addEspecialidade();
                                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoEspecialidade->addEspecialidadeSubDivisao();
                                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoEspecialidade->setCodEspecialidade( $request->get('inCodEspecialidadeFuncao')  );
                                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->addCargoSubDivisao();
                                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $request->get('inCodSubDivisaoFuncao'));

                                if ($request->get('inHdnCodRegime', '') != "") {
                                    $inCodRegime = $request->get('inHdnCodRegime');
                                } else {
                                    $inCodRegime = $request->get('inCodRegime');
                                }
                                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->setCodRegime ( $inCodRegime );

                                //Verifica Norma
                                $arCodNorma = explode("/",$request->get('stCodNorma'));
                                if (count($arCodNorma)>0) {
                                    $stNumNorma = ltrim($arCodNorma[0],'0');
                                    if ($stNumNorma == "") {
                                        $stNumNorma = "0";
                                    }
                                    $obRNorma = new RNorma();
                                    $obRNorma->setNumNorma( $stNumNorma );
                                    $obRNorma->setExercicio( $arCodNorma[1] );
                                    $obErro = $obRNorma->listar($rsNorma, $boTransacao);
                                    $stCodNorma = $rsNorma->getCampo('cod_norma');
                                }

                                if ( !$obErro->ocorreu() ) {
                                    $obRPessoalServidor->roUltimoContratoServidor->obROrganogramaOrgao->setCodOrgao                                                 ( $request->get("hdnUltimoOrgaoSelecionado")          );
                                    $obRPessoalServidor->roUltimoContratoServidor->obROrganogramaLocal->setCodLocal                                                 ( $request->get('inCodLocal')                         );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRNorma->setCodNorma                                                            ( $stCodNorma                                  );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoAdmissao->setCodTipoAdmissao                                       ( $request->get('inCodTipoAdmissao')                  );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalFormaPagamento->setCodFormaPagamento                                   ( $request->get('inCodFormaPagamento')                );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoPagamento->setCodTipoPagamento                                     ( $request->get('inCodTipoPagamento')        	       );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoSalario->setCodTipoSalario                                         ( $request->get('inCodTipoSalario')          	       );
                                    $obRPessoalServidor->roUltimoContratoServidor->setRegistro                                                                      ( $request->get('inContrato')                	       );
                                    $obRPessoalServidor->roUltimoContratoServidor->setNroCartaoPonto                                                                ( $request->get('inCartaoPonto')             	       );
                                    $obRPessoalServidor->roUltimoContratoServidor->setAtivo                                                                         ( ( $request->get('stSituacao') == 1 ) ? true : false );
                                    $obRPessoalServidor->roUltimoContratoServidor->setNomeacao                                                                      ( $request->get('dtDataNomeacao')                     );
                                    $obRPessoalServidor->roUltimoContratoServidor->setPosse                                                                         ( $request->get('dtDataPosse')                        );
                                    $obRPessoalServidor->roUltimoContratoServidor->setAdmissao                                                                      ( $request->get('dtAdmissao')                         );
                                    $obRPessoalServidor->roUltimoContratoServidor->setDataBase                                                                      ( $request->get('dtDataBase')                         );
                                    $obRPessoalServidor->roUltimoContratoServidor->setValidadeExameMedico                                                           ( $request->get('dtValidadeExameMedico')              );
                                    $obRPessoalServidor->roUltimoContratoServidor->setOpcaoFgts                                                                     ( $request->get('dtDataFGTS')                         );
                                    $obRPessoalServidor->roUltimoContratoServidor->setContaCorrenteFgts                                                             ( $request->get('inContaCreditoFGTS')                 );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->setCodPadrao                           ( $request->get('inCodPadrao')                        );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->addNivelPadrao();
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setCodNivelPadrao ( $request->get('inCodProgressao') );
                                    $obRPessoalServidor->roUltimoContratoServidor->setSalario                                                                       ( $request->get('inSalario')                          );
                                    $obRPessoalServidor->roUltimoContratoServidor->setHrMensal                                                                      ( $request->get('stHorasMensais')            	       );
                                    $obRPessoalServidor->roUltimoContratoServidor->setHrSemanal                                                                     ( $request->get('stHorasSemanais')          	       );
                                    $obRPessoalServidor->roUltimoContratoServidor->setInicioProgressao                                                              ( $request->get('dtDataProgressao')          	       );
                                    $obRPessoalServidor->roUltimoContratoServidor->setContaCorrenteSalario                                                          ( $request->get('inContaSalario')            	       );
                                    $obRPessoalServidor->roUltimoContratoServidor->setAdiantamento                                                                  ( $request->get('boAdiantamento')            	       );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCategoria->setCodCategoria                                             ( $request->get('inCodCategoria')            	       );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->setCodGrade                                              ( $request->get('inCodGradeHorario')       	       );
                                    $obRPessoalServidor->roUltimoContratoServidor->setVigenciaSalario                                                               ( $request->get('dtVigenciaSalario') 				   );

                                    //dados aba dependente
                                    $arDependentes = Sessao::read('DEPENDENTE');
                                    if ( is_array($arDependentes) ) {
                                        for ($inCount=0; $inCount<count($arDependentes); $inCount++) {
                                            $arDependente = $arDependentes[$inCount];
                                            $obRPessoalServidor->addRPessoalDependente();
                                            $obRPessoalServidor->roRPessoalDependente->setCodDependente              ( $arDependente['inCodDependente']                                    );
                                            $obRPessoalServidor->roRPessoalDependente->obRCGMPessoaFisica->setNumCgm ( $arDependente['inCGMDependente']                                    );
                                            $obRPessoalServidor->roRPessoalDependente->setCodGrau                    ( $arDependente['stGrauParentesco']                                   );
                                            $obRPessoalServidor->roRPessoalDependente->setDependenteInvalido         ( ($arDependente['boFilhoEquiparado'] == 't') ? true : false          );
                                            $obRPessoalServidor->roRPessoalDependente->setCarteiraVacinacao          ( ($arDependente['boCarteiraVacinacao'] == 't') ? true : false        );
                                            $obRPessoalServidor->roRPessoalDependente->setComprovanteMatricula       ( ($arDependente['boComprovanteMatricula'] == 't') ? true : false     );
                                            $obRPessoalServidor->roRPessoalDependente->setDependentePrev             ( ($arDependente['boDependentePrev'] == 't') ? true : false           );

                                            $obRPessoalServidor->roRPessoalDependente->setCodVinculo                 ( $arDependente['inCodDependenteIR']                                  );
                                            $obRPessoalServidor->roRPessoalDependente->setDataInicioSalarioFamilia   ( $arDependente['dtInicioSalarioFamilia']                             );
                                            $obRPessoalServidor->roRPessoalDependente->setDependenteSalarioFamilia   ( ($arDependente['boDependenteSalarioFamilia'] == 't') ? true : false );
                                            $obRPessoalServidor->roRPessoalDependente->obRPessoalCID->setCodCid      ( $arDependente['inCodCIDDependente']                                 );

                                            if ($arDependente['boincluirDataNascimentoDespendente']) {
                                                $obRPessoalServidor->roRPessoalDependente->obRCGMPessoaFisica->setDataNascimento( $arDependente['stDataNascimentoDependente'] );
                                            }

                                            $arVacinacoes = $arDependente['VACINACAO'];
                                            for ($inCounter=0; $inCounter<count($arVacinacoes); $inCounter++) {
                                                $arVacinacao = $arVacinacoes[$inCounter];
                                                $obRPessoalServidor->roRPessoalDependente->addRPessoalCarteiraVacinacao();
                                                $obRPessoalServidor->roRPessoalDependente->roRPessoalCarteiraVacinacao->setDataApresentacao ( $arVacinacao['dtApresentacaoCarteiraVacinacao'] );
                                                $obRPessoalServidor->roRPessoalDependente->roRPessoalCarteiraVacinacao->setApresentada      ( $arVacinacao['boApresentadaVacinacao'] );
                                            }

                                            $arMatriculas = $arDependente['MATRICULA'];
                                            for ($inCounter=0; $inCounter<count($arMatriculas); $inCounter++) {
                                                $arMatricula = $arMatriculas[$inCounter];
                                                $obRPessoalServidor->roRPessoalDependente->addRPessoalComprovanteMatricula();
                                                $obRPessoalServidor->roRPessoalDependente->roRPessoalComprovanteMatricula->setDataApresentacao ( $arMatricula['dtApresentacaoComprovanteMatricula']);
                                                $obRPessoalServidor->roRPessoalDependente->roRPessoalComprovanteMatricula->setApresentada      ( $arMatricula['boApresentadaMatricula']);
                                            }
                                        }
                                    }

                                    //monta array de atributos dinamicos
                                    foreach ($arChave as $key => $value) {
                                        $arChaves = preg_split( "/[^a-zA-Z0-9]/" , $key );
                                        $inCodAtributo = $arChaves[0];

                                        if ( is_array($value) ) {
                                            $Newvalue = implode(",",$value);
                                            $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $Newvalue );
                                        } else {
                                            $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                                        }
                                    }

                                    $arArquivosDocumentos = Sessao::read("arArquivosDocumentos");
                                    $arArquivosDocumentos = (is_array($arArquivosDocumentos)) ? $arArquivosDocumentos : array();

                                    $obRPessoalServidor->setArquivosDocumentos($arArquivosDocumentos);

                                    $obErro = $obRPessoalServidor->alterarServidor($boTransacao);
                                }
                            }
                        }
                    }
                }
            }
        }

        if ( !$obErro->ocorreu() ){
            if ($request->get('actVoltar')) {
                // a variavel actVoltar contém o nome do programa que chamou a tela de servidor,
                // pra onde o sistema deve retornar se ela estiver vazia o sistema retorno para
                // a listagem de servidores
                $inContrato = ($request->get('inContratoAlterar')=='')?$obRPessoalServidor->roUltimoContratoServidor->getRegistro() : $request->get('inContratoAlterar');                
                sistemaLegado::alertaAviso($request->get('actVoltar') . '?inNumCGM='.$request->get('inNumCGM').'&inContrato='. $obRPessoalServidor->roUltimoContratoServidor->getRegistro(), "Matrícula: ".$inContrato." - ".$rsCGM->getCampo("nom_cgm"),"incluir","aviso", Sessao::getId(), "../");
            } else
                sistemaLegado::alertaAviso($pgList .'&inContrato='. $obRPessoalServidor->roUltimoContratoServidor->getRegistro(), "Matrícula: ".$request->get('inContratoAlterar')." - ".$rsCGM->getCampo("nom_cgm"),"alterar","aviso", Sessao::getId(), "../");
        } else
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
    break;

    case "excluir":
        $obRPessoalServidor->setCodServidor                          ( $request->get('inCodServidor') );
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $request->get('inCodContrato') );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->addEspecialidadeSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->addRPessoalCTPS();
        $obRPessoalServidor->addRPessoalDependente();
        $obErro = $obRPessoalServidor->excluirServidor($boTransacao);
        if ( !$obErro->ocorreu() )
            sistemaLegado::alertaAviso($pgList,"Servidor: ".$request->get('inNumCGM')." - ".$rsCGM->getCampo("nom_cgm"),"excluir","aviso", Sessao::getId(), "../");
        else
            sistemaLegado::alertaAviso( $pgList."?stAcao=excluir&".$stFiltro, urlencode($obErro->getDescricao()), "n_excluir","erro",Sessao::getId(),"../" );
    break;
}

/*
recebe duas datas retorna 0 se forem iguais 1 se a primeira for maior e 2 se a segunda for maior
*/

function compData($Data1 = '', $Data2 = '')
{
    $Data1 = explode ('/', $Data1);
    $Data1 = ($Data1[2] . $Data1[1] . $Data1[0]) *1 ;

    $Data2 = explode ('/', $Data2);
    $Data2 = ($Data2[2] . $Data2[1] . $Data2[0]) *1 ;

    if ( $Data1 == $Data2 )
        return 0;
    elseif ($Data1 > $Data2 )
        return 1;
    else
        return 2;

}

function checkPIS($pis, $checkZero=true)
{
    $pis = trim(preg_replace("/[^0-9]/", "", $pis));

    if (trim($pis) === "00000000000" && $checkZero==false) {
        return true;
    }

    if (strlen($pis) != 11 || intval($pis) == 0) {
        return false;
    }

    for ($d = 0, $p = 2, $c = 9; $c >= 0; $c--, ($p < 9) ? $p++ : $p = 2) {
        $d += $pis[$c] * $p;
    }

    return ($pis[10] == (((10 * $d) % 11) % 10));
}
?>

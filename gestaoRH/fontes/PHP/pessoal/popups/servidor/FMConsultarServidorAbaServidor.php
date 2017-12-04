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
    * Data de criação : 23/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Programador: Diego Lemos de Souza

    * @ignore

    $Id: FMConsultarServidorAbaServidor.php 66023 2016-07-08 15:01:19Z michel $

    * Casos de uso: uc-04.04.07
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php";
require_once CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php";
require_once CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarServidor";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";//ConsultarServidorAbaServidor
$pgProc              = "PR".$stPrograma.".php";
$pgOcul              = "OC".$stPrograma.".php";
$pgOculIdentificacao = "OC".$stPrograma."AbaIdentificacao.php";
$pgOculDocumentacao  = "OC".$stPrograma."AbaDocumentacao.php";
$pgOculContrato      = "OC".$stPrograma."AbaContrato.php";
$pgOculPrevidencia   = "OC".$stPrograma."AbaPrevidencia.php";
$pgOculDependentes   = "OC".$stPrograma."AbaDependentes.php";
$pgOculAtributos     = "OC".$stPrograma."AbaAtributos.php";
$pgJs                = "JS".$stPrograma.".js";

include_once $pgJs;

$obTPessoalContrato = new TPessoalContrato();
if ($request->get('inCodContrato')) {
    $stFiltro = " AND cod_contrato = ".$request->get('inCodContrato');
}
if ($request->get('inRegistro')) {
    $stFiltro = " AND registro = ".$request->get('inRegistro');
}
$obTPessoalContrato->recuperaCgmDoRegistro($rsCGMSimples,$stFiltro);

$obRPessoalServidor = new RPessoalServidor;
$obRPessoalServidor->addContratoServidor();

$obRConfiguracaoPessoal = new RConfiguracaoPessoal;
$obRConfiguracaoPessoal->Consultar( $boTransacao );
$stMascaraRegistro = $obRConfiguracaoPessoal->getMascaraRegistro();
$boGeracaoRegistro = $obRConfiguracaoPessoal->getGeracaoRegistro();

$obRPessoalServidor->obRCGMPessoaFisica->setNumCGM( $rsCGMSimples->getCampo("numcgm") );
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

$inMatricula                = $rsCGMSimples->getCampo("registro");
$inCodContrato              = $rsCGMSimples->getCampo("cod_contrato");
$stSexo                     = ($rsCGM->getCampo("sexo") == "f") ?  "Feminino" : "Masculino" ;
$stCPF                      = $rsCGM->getCampo("cpf");
$inNumCGM                   = $rsCGM->getCampo("numcgm");
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

$obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
##############################################################################################################
#                              Preenche informações da aba identificação                                     #
##############################################################################################################
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php";
$obTPessoalServidor = new TPessoalServidor();
$stFiltro = " WHERE PS.numcgm = ".$rsCGM->getCampo("numcgm");
$obTPessoalServidor->recuperaRelacionamento($rsServidor,$stFiltro);
$inCodServidor                  = $rsServidor->getCampo("cod_servidor");
$stNomeFoto                     = $rsServidor->getCampo("caminho_foto");
$inNumCGM                       = $rsServidor->getCampo("numcgm");
$inCodEstadoCivil               = $rsServidor->getCampo("cod_estado_civil");
$inCGMConjuge                   = $rsServidor->getCampo("numcgm_conjuge");
$stNomConjuge                   = addslashes($rsServidor->getCampo("nome_conjuge"));
$stNomePai                      = $rsServidor->getCampo("nome_pai");
$stNomeMae                      = $rsServidor->getCampo("nome_mae");
$inCodRaca                      = $rsServidor->getCampo("cod_rais");
$inCodCID                       = $rsServidor->getCampo("cod_cid");
$inCodUF                        = $rsServidor->getCampo("cod_uf");
$inCodTxtMunicipio              = $rsServidor->getCampo("cod_municipio");
$inCodMunicipio                 = $rsServidor->getCampo("cod_municipio");

##############################################################################################################
#                              Preenche informações da aba documentacao                                      #
##############################################################################################################
$stPisPasep                     = trim($rsServidor->getCampo("servidor_pis_pasep"));
$dtCadastroPis                  = $rsServidor->getCampo("dt_pis_pasep");
$inCertificadoReservista        = trim($rsServidor->getCampo("nr_carteira_res"));
$inCodCategoriaCertificado      = trim($rsServidor->getCampo("cat_reservista"));
$inCodOrgaoExpedidorCertificado = trim($rsServidor->getCampo("origem_reservista"));
$inTituloEleitor                = trim($rsServidor->getCampo("nr_titulo_eleitor"));
$inZonaTitulo                   = trim($rsServidor->getCampo("zona_titulo"));
$inSecaoTitulo                  = trim($rsServidor->getCampo("secao_titulo"));

##############################################################################################################
#                              Preenche informações da aba contrato                                          #
##############################################################################################################

$obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $request->get('inCodContrato') );
$obRPessoalServidor->roUltimoContratoServidor->setRegistro( $request->get('inRegistro') );

$obRPessoalServidor->roUltimoContratoServidor->listarDadosAbaContratoServidor( $rsContrato,$boTransacao );
$obRPessoalServidor->roUltimoContratoServidor->obRPessoalOcorrencia->setCodOcorrencia($rsContrato->getCampo("cod_ocorrencia"));
$obRPessoalServidor->roUltimoContratoServidor->obRPessoalOcorrencia->listarOcorrencia( $rsOcorrencia,$boTransacao );
$obRPessoalServidor->roUltimoContratoServidor->consultarContratoServidorSubDivisaoFuncao( $rsContratoServidorSubDivisaoFuncao, $boTransacao );
$obRPessoalServidor->roUltimoContratoServidor->consultarContratoServidorRegimeFuncao( $rsContratoServidorRegimeFuncao, $boTransacao );
$obRPessoalServidor->roUltimoContratoServidor->obROrganogramaOrgao->setCodOrgao( $rsContrato->getCampo("cod_orgao") );
$obRPessoalServidor->roUltimoContratoServidor->obROrganogramaOrgao->listarOrgaoReduzido( $rsOrgaoReduzido );
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
$stSituacao                 = $rsContrato->getCampo("situacao");
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
$inCodLotacao               = $rsOrgaoReduzido->getCampo("estruturado");
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
Sessao::write('dtVigenciaSalario', $dtVigenciaSalario);
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
$inDataBase                 = $rsContrato->getCampo("data_base");
//Conselho profissional
$inCodConselho              = $rsContratoServidorConselho->getCampo("cod_conselho");
$inNumeroConselho       = $rsContratoServidorConselho->getCampo("nr_conselho");
$dtDataValidadeConselho = $rsContratoServidorConselho->getCampo("dt_validade");

$obTNorma = new TNorma();
$obTNorma->setDado('cod_norma', $inCodNorma);
$obTNorma->recuperaPorChave($rsNorma);

$inCodTipoNormaTxt = $rsNorma->getCampo('cod_tipo_norma');

$jsOnload = "executaFuncaoAjax('processarForm','&inCodContrato=".$inCodContrato."&inCodGradeHorario=".$inCodGradeHorario."&inCodServidor=".$inCodServidor."');";

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue ( "" );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

include_once 'FMConsultarServidorAbaIdentificao.php';
include_once 'FMConsultarServidorAbaDocumentacao.php';
include_once 'FMConsultarServidorAbaContrato.php';
include_once 'FMConsultarServidorAbaDependentes.php';
include_once 'FMConsultarServidorAbaPrevidencia.php';

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );

#########################################################################################################
#                               ABA IDENTIFICAÇÃO                                                       #
#########################################################################################################
$obFormulario->addAba                ( "Identificação"                                                  );
$obFormulario->addTitulo             ( "Dados de Identificação"                                         );
$obFormulario->addSpan               ( $obSpnFoto                                                       );
$obFormulario->addComponente         ( $obLblCGM                                                        );
$obFormulario->addComponente         ( $obLblDataNascimento                                             );
$obFormulario->addComponente         ( $obLblSexo                                                       );
$obFormulario->addComponente         ( $obLblPai                                                        );
$obFormulario->addComponente         ( $obLblMae                                                        );
$obFormulario->addComponente         ( $obLblCodEstadoCivil                                             );
$obFormulario->addComponente         ( $obLblCGMConjuge                                                 );
$obFormulario->addComponente         ( $obLblCodRaca                                                    );
$obFormulario->addComponente         ( $obLblCodCID                                                     );
$obFormulario->addComponente         ( $obLblNacionalidade                                              );
$obFormulario->addComponente         ( $obLblCodUFOrigem                                                );
$obFormulario->addComponente         ( $obLblCodMunicipioOrigem                                         );
$obFormulario->addComponente         ( $obLblEndereco                                                   );
$obFormulario->addComponente         ( $obLblBairro                                                     );
$obFormulario->addComponente         ( $obLblUF                                                         );
$obFormulario->addComponente         ( $obLblMunicipio                                                  );
$obFormulario->addComponente         ( $obLblCEP                                                        );
$obFormulario->addComponente         ( $obLblTelefone                                                   );
$obFormulario->addComponente         ( $obLblEscolaridade                                               );
#########################################################################################################
#                               ABA DOCUMENTAÇÃO                                                        #
#########################################################################################################
$obFormulario->addAba               ( "Documentação" );
$obFormulario->addTitulo            ( "Documentação do Servidor"                                        );
$obFormulario->addComponente        ( $obLblCPF                                                         );
$obFormulario->addComponente        ( $obLblRG                                                          );
$obFormulario->addComponente        ( $obLblOrgaoEmissor                                                );
$obFormulario->addComponente        ( $obLblCNH                                                         );
$obFormulario->addComponente        ( $obLblCategoriaCNH                                                );
$obFormulario->addComponente        ( $obLblPisPasep                                                    );
$obFormulario->addComponente        ( $obLblCadastroPis                                                 );
$obFormulario->addComponente        ( $obLblTituloEleitor                                               );
$obFormulario->addComponente        ( $obLblSecaoTitulo                                                 );
$obFormulario->addComponente        ( $obLblZonaTitulo                                                  );
$obFormulario->addComponente        ( $obLblCertificadoReservista                                       );
$obFormulario->addComponente        ( $obLblCategoriaCertificado                                        );
$obFormulario->addComponente        ( $obLblOrgaoExpedidorCertificado                                   );
$obFormulario->addTitulo            ( "Dados de CTPS"                                                   );
$obFormulario->addSpan              ( $obSpnCTPS                                                        );
$obFormulario->addSpan              ( $obSpnListaArqDigital                                             );
#########################################################################################################
#                               ABA CONTRATO                                                            #
#########################################################################################################
$obFormulario->addAba               ( "Contrato", true                                                  );
$obFormulario->addTitulo            ( "Informações Contratuais"                                         );
$obFormulario->addComponente        ( $obLblContrato                                                    );
$obFormulario->addComponente        ( $obLblCartaoPonto                                                 );
$obFormulario->addComponente        ( $obLblCedencia                                                    );
$obFormulario->addSpan              ( $obSpnCedencia                                                    );
$obFormulario->addComponente        ( $obLblSituacao                                                    );
$obFormulario->addSpan              ( $obSpanAposentadoria                                              );
$obFormulario->addComponente        ( $obLblDataNomeacao                                                );
$obFormulario->addComponente        ( $obLblTipoNorma                                                   );
$obFormulario->addComponente        ( $obLblPortariaNomeacao                                            );
$obFormulario->addComponente        ( $obLblDataPosse                                                   );
$obFormulario->addComponente        ( $obLblAdmissao                                                    );
$obFormulario->addComponente        ( $obLblDataProgressao                                              );
$obFormulario->addComponente        ( $obLblDataExameMedico                                             );
$obFormulario->addComponente        ( $obLblTipoAdmissao                                                );
$obFormulario->addComponente        ( $obLblVinculoEmpregaticio                                         );
$obFormulario->addComponente        ( $obLblCategoria                                                   );
$obFormulario->addComponente        ( $obLblAgentesNocivos                                              );
$obFormulario->addSpan              ( $obSpnRescisao                                                    );
$obFormulario->addTitulo            ( "Informações do Cargo"                                            );
$obFormulario->addComponente        ( $obLblCodRegime                                                   );
$obFormulario->addComponente        ( $obLblCodSubDivisao                                               );
$obFormulario->addComponente        ( $obLblCargo                                                       );
$obFormulario->addComponente        ( $obLblCodEspecialidadeCargo                                       );
$obFormulario->addTitulo            ( "Informações da Função"                                           );
$obFormulario->addComponente        ( $obLblCodRegimeFuncao                                             );
$obFormulario->addComponente        ( $obLblCodSubDivisaoFuncao                                         );
$obFormulario->addComponente        ( $obLblCodFuncao                                                   );
$obFormulario->addComponente        ( $obLblCodEspecialidadeFuncao                                      );
$obFormulario->addComponente        ( $obLblAlteracaoFuncao                                             );
$obFormulario->addTitulo            ( "Informações Salariais"                                           );
$obFormulario->addComponente        ( $obLblHorasMensais                                                );
$obFormulario->addComponente        ( $obLblHorasSemanais                                               );
$obFormulario->addComponente        ( $obLblCodPadrao                                                   );
$obFormulario->addComponente        ( $obLblProgressao                                                  );
$obFormulario->addComponente        ( $obLblSalario                                                     );
$obFormulario->addComponente        ( $obLblVigenciaSalario                                             );
$obFormulario->addComponente        ( $obLblCodFormaPagamento                                           );
$obFormulario->addComponente        ( $obLblCodBancoSalario                                             );
$obFormulario->addComponente        ( $obLblCodAgenciaSalario                                           );
$obFormulario->addComponente        ( $obLblContaSalario                                                );
$obFormulario->addComponente        ( $obLblCodTipoPagamento                                            );
$obFormulario->addComponente        ( $obLblCodTipoSalario                                              );
$obFormulario->addComponente        ( $obLblAdiantamento                                                );
$obFormulario->addTitulo            ( "Informações de Lotação"                                          );
$obFormulario->addComponente        ( $obLblLotacao                                                     );
$obFormulario->addComponente        ( $obLblLocal                                                       );
$obFormulario->addTitulo            ( "Informações de FGTS"                                             );
$obFormulario->addComponente        ( $obLblDataFGTS                                                    );
$obFormulario->addComponente        ( $obLblCodBanco                                                    );
$obFormulario->addComponente        ( $obLblCodAgenciaBanco                                             );
$obFormulario->addComponente        ( $obLblContaCredito                                                );
$obFormulario->addTitulo            ( "Quadro de Horário"                                               );
$obFormulario->addComponente        ( $obLblGradeHorario                                                );
$obFormulario->addSpan              ( $obSpnTurnos                                                      );
$obFormulario->addTitulo            ( "Dados do Sindicato"                                              );
$obFormulario->addComponente        ( $obLblCgmSindicato                                                );
$obFormulario->addComponente        ( $obLblDataBase                                                    );
$obFormulario->addTitulo            ( "Conselho Profissional"                                           );
$obFormulario->addComponente        ( $obLblConselho                                                    );
$obFormulario->addComponente        ( $obLblNumeroConselho                                              );
$obFormulario->addComponente        ( $obLblDataValidade                                                );

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
$obFormulario->addSpan              ( $obSpnDependente                                                  );

#########################################################################################################
#                               ABA ATRIBUTOS                                                           #
#########################################################################################################
$obFormulario->addAba               ( "Atributos"                                                       );
$obMontaAtributos->geraFormulario   ( $obFormulario                                                     );

$obBtnFechar = new Button;
$obBtnFechar->setId                       ( "btnFecharCampos"                          );
$obBtnFechar->setName                     ( "btnFecharCampos"                          );
$obBtnFechar->setValue                    ( "    Fechar    "                           );
$obBtnFechar->setTipo                     ( "button"                                   );
$obBtnFechar->obEvento->setOnClick        ( "javascript:window.parent.window.close();" );

$obFormulario->defineBarra(array($obBtnFechar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

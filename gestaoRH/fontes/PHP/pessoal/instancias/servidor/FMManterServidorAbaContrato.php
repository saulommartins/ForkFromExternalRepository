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
* Página de Aba de Contrato
* Data de Criação   : ???

* @author Analista: ???
* @author Desenvolvedor: ???

* @ignore

$Revision: 32866 $
$Name$
$Author: alex $
$Date: 2008-03-26 14:19:12 -0300 (Qua, 26 Mar 2008) $

* Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php"                                         );
include_once ( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php"                               );
include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalOrganogramaVigentePorTimestamp.class.php"                    );
include_once ( $pgOculContrato );

$boFlagCarregaContrato = isset($boFlagCarregaContrato) ? $boFlagCarregaContrato : null;

if ($boFlagCarregaContrato == "off") {
    $stAcao = 'incluir';
}

//INFORMAÇÕES CONTRATUAIS
//registro do contrato
$inContrato = isset($inContrato) ? $inContrato : "";
$obIContratoDigitoVerificador = new IContratoDigitoVerificador($inContrato);
$obIContratoDigitoVerificador->setNull(false);
if ($inContrato != "") {
    $obIContratoDigitoVerificador->setAutomatico(true);
}
if ($stAcao == 'alterar') {
    $obIContratoDigitoVerificador->obTxtRegistroContrato->setDisabled(true);
}

//registro do cartao ponto
$obTxtCartaoPonto = new TextBox;
$obTxtCartaoPonto->setRotulo                            ( "Cartão Ponto"                                    );
$obTxtCartaoPonto->setName                              ( "inCartaoPonto"                                   );
$obTxtCartaoPonto->setValue                             ( isset($inCartaoPonto) ? $inCartaoPonto : null	    );
$obTxtCartaoPonto->setTitle                             ( "Informe o número do cartão ponto."               );
$obTxtCartaoPonto->setSize                              ( 12                                                );
$obTxtCartaoPonto->setMaxLength                         ( 10                                                );
$obTxtCartaoPonto->setInteiro                           ( true                                              );

if ( $request->get('inCodContrato') != "" ) {
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAdidoCedido.class.php");
    $obTPessoalAdidoCedido = new TPessoalAdidoCedido();
    $stFiltro = " AND contrato.cod_contrato = ".$_REQUEST['inCodContrato'];
    $obTPessoalAdidoCedido->recuperaRelacionamento($rsAdidoCedido,$stFiltro);

    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
    $dtCompetenciaInicial = $rsPeriodoMovimentacao->getCampo("dt_inicial");
    $dtCompetenciaFinal   = $rsPeriodoMovimentacao->getCampo("dt_final");

    $dtDataInicial = $rsAdidoCedido->getCampo("data_inicial");
    $dtDataFinal = $rsAdidoCedido->getCampo("data_final");

    if ($dtDataFinal == '') {
        $dtDataFinal = $dtCompetenciaFinal;
    }

    Sessao::write('rsAdidoCedido',$rsAdidoCedido);

    switch (true) {
        case ($rsAdidoCedido->getNumLinhas() == -1)
             or (sistemalegado::comparadatas($dtDataInicial, $dtCompetenciaFinal))
             or (sistemalegado::comparadatas($dtCompetenciaInicial, $dtDataFinal)):
            $stTipoCedencia = "Nenhum";
            break;
        case $rsAdidoCedido->getCampo("tipo_cedencia") == "c":
            $stTipoCedencia = "Cedido";
            break;
        case $rsAdidoCedido->getCampo("tipo_cedencia") == "a":
            $stTipoCedencia = "Adido";
            break;
    }

}

$obLblCedencia = new Label();
$obLblCedencia->setRotulo("Tipo de Cedência");
$obLblCedencia->setId("stTipoCedencia");
$obLblCedencia->setValue(isset($stTipoCedencia) ? $stTipoCedencia : null);

$obSpnCedencia = new Span();
$obSpnCedencia->setId("spnCedencia");

$obHdnContratoAlterar = new hidden();
$obHdnContratoAlterar->setName    ( "inContratoAlterar" );
$obHdnContratoAlterar->setValue   ( $inContrato );

$stSituacao = isset($stSituacao) ? $stSituacao : null;
$obHdnSituacao = new hidden();
$obHdnSituacao->setName    ( "stSituacao" );
$obHdnSituacao->setValue   ( ( $stSituacao == 'Ativo' ) ? true : false );

$obLblSituacao = new Label();
$obLblSituacao->setId      ( "stSituacao" );
$obLblSituacao->setValue   ( $stSituacao );
$obLblSituacao->setRotulo  ( "Situação" );

$obSpanAposentadoria = new Span();
$obSpanAposentadoria->setId("spnAposentadoria");

$obTxtDataNomeacao = new Data;
$obTxtDataNomeacao->setName               ( "dtDataNomeacao"           );
$obTxtDataNomeacao->setTitle              ("Informe a data de nomeação.");
$obTxtDataNomeacao->setNull               ( false                      );
$obTxtDataNomeacao->setRotulo             ( "Data de Nomeação"         );
$obTxtDataNomeacao->setValue              ( isset($dtDataNomeacao) ? $dtDataNomeacao : null );
$obTxtDataNomeacao->obEvento->setOnBlur   ("buscaValor('validaDataPosse',3);"    );

include_once ( CAM_GA_NORMAS_COMPONENTES."IBuscaInnerNorma.class.php"                               );
$obTipoNormaNorma = new IBuscaInnerNorma(false,false);
$obTipoNormaNorma->obITextBoxSelectTipoNorma->obSelect->setDisabled(true);
$obTipoNormaNorma->obITextBoxSelectTipoNorma->obTextBox->setReadOnly(true);
$obTipoNormaNorma->obBscNorma->setRotulo("Ato de Nomeação");

$obCmbCodPortariaNomeacao = new Select;
$obCmbCodPortariaNomeacao->setName                  ( "stPortariaNomeacao" );
$obCmbCodPortariaNomeacao->setValue                 ( isset($inCodPortariaNomeacao) ? trim($inCodPortariaNomeacao) : null );
$obCmbCodPortariaNomeacao->setRotulo                ( "Ato de Nomeação"      );
$obCmbCodPortariaNomeacao->setTitle                 ( "Selecione o ato de nomeação." );
$obCmbCodPortariaNomeacao->setNull                  ( false );
$obCmbCodPortariaNomeacao->setCampoId               ( "[num_norma_exercicio]" );
$obCmbCodPortariaNomeacao->setCampoDesc             ( "nom_norma" );
$obCmbCodPortariaNomeacao->addOption                ( "", "Selecione" );
$obCmbCodPortariaNomeacao->setStyle                 ( "width: 250px"  );

$obTxtDataPosse = new Data;
$obTxtDataPosse->setName   ( "dtDataPosse" );
$obTxtDataPosse->setNull   ( false );
$obTxtDataPosse->setRotulo ( "Data da Posse" );
$obTxtDataPosse->setTitle  ( "Informe a data da posse." );
$obTxtDataPosse->setValue  ( isset($dtDataPosse) ? $dtDataPosse : null );
$obTxtDataPosse->obEvento->setOnBlur("buscaValor('validaDataPosse',3);");

$obTxtDataProgressao = new Data;
$obTxtDataProgressao->setName   ( "dtDataProgressao" );
$obTxtDataProgressao->setTitle  ("Informe a data de progressão.");
$obTxtDataProgressao->setNull   ( true );
$obTxtDataProgressao->setRotulo ( "Data Início para Progressão " );
$obTxtDataProgressao->setValue  ( isset($dtDataProgressao) ? $dtDataProgressao : null );
$obTxtDataProgressao->obEvento->setOnBlur( "buscaValor('preencheProgressao',3);");

$obTxtDataExameMedico = new Data;
$obTxtDataExameMedico->setName   ( "dtValidadeExameMedico" );
$obTxtDataExameMedico->setNull   ( true );
$obTxtDataExameMedico->setRotulo ( "Validade do Exame Médico" );
$obTxtDataExameMedico->setTitle  ( "Informe a validade do exame médico." );
$obTxtDataExameMedico->setValue  ( isset($dtValidadeExameMedico) ? $dtValidadeExameMedico : null  );
$obTxtDataExameMedico->obEvento->setOnBlur("buscaValor('validaDataExameMedico',3);");

// Tipo de admissao
$obBscTipoAdmissao = new BuscaInner;
$obBscTipoAdmissao->setRotulo                         ( "Tipo de Admissão"                              );
$obBscTipoAdmissao->setTitle                          ( "Selecione o tipo de admissão."                 );
$obBscTipoAdmissao->setNull                           ( false                                           );
$obBscTipoAdmissao->setId                             ( "stTipoAdmissao"                                );
$obBscTipoAdmissao->setValue                          ( isset($stTipoAdmissao) ? $stTipoAdmissao : null   );
$obBscTipoAdmissao->obCampoCod->setName               ( "inCodTipoAdmissao"                             );
$obBscTipoAdmissao->obCampoCod->setValue              ( isset($inCodTipoAdmissao) ? $inCodTipoAdmissao : null );
$obBscTipoAdmissao->obCampoCod->setSize               ( 10                                              );
$obBscTipoAdmissao->obCampoCod->setMaxLength          ( 3                                               );
$obBscTipoAdmissao->obCampoCod->obEvento->setOnChange ( "buscaValor('buscaTipoAdmissao',3);"            );
$obBscTipoAdmissao->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/LSProcurarTipoAdmissao.php','frm','inCodTipoAdmissao','stTipoAdmissao','','".Sessao::getId()."','800','550')" );

//Tipo de vinculo empregaticio
$obBscVinculoEmpregaticio = new BuscaInner;
$obBscVinculoEmpregaticio->setRotulo                         ( "Vínculo Empregatício"                               );
$obBscVinculoEmpregaticio->setTitle                          ( "Selecione o tipo de  vínculo."                      );
$obBscVinculoEmpregaticio->setNull                           ( false                                                );
$obBscVinculoEmpregaticio->setId                             ( "stVinculoEmpregaticio"                              );
$obBscVinculoEmpregaticio->setValue                          ( isset($stVinculoEmpregaticio) ? $stVinculoEmpregaticio : null);
$obBscVinculoEmpregaticio->obCampoCod->setName               ( "inCodVinculoEmpregaticio"                           );
$obBscVinculoEmpregaticio->obCampoCod->setValue              ( isset($inCodVinculoEmpregaticio) ? $inCodVinculoEmpregaticio : null);
$obBscVinculoEmpregaticio->obCampoCod->setSize               ( 10                                                   );
$obBscVinculoEmpregaticio->obCampoCod->setMaxLength          ( 3                                                    );
$obBscVinculoEmpregaticio->obCampoCod->obEvento->setOnChange ( "buscaValor('buscaVinculoEmpregaticio',3);"          );
$obBscVinculoEmpregaticio->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/LSProcurarVinculoEmpregaticio.php','frm','inCodVinculoEmpregaticio','stVinculoEmpregaticio','','".Sessao::getId()."','800','550')" );

//Selecão da categoria
$obBscCategoria = new BuscaInner;
$obBscCategoria->setRotulo                         ( "Categoria"                                );
$obBscCategoria->setTitle                          ( "Selecione a categoria do funcionário."    );
$obBscCategoria->setNull                           ( false                                      );
$obBscCategoria->setId                             ( "stCategoria"                              );
$obBscCategoria->setValue                          ( isset($stCategoria) ? $stCategoria : null  );
$obBscCategoria->obCampoCod->setName               ( "inCodCategoria"                           );
$obBscCategoria->obCampoCod->setValue              ( isset($inCodCategoria) ? $inCodCategoria : null );
$obBscCategoria->obCampoCod->setSize               ( 10                                         );
$obBscCategoria->obCampoCod->setMaxLength          ( 3                                          );
$obBscCategoria->obCampoCod->obEvento->setOnChange ( "buscaValor('buscaCategoria',3);");
$obBscCategoria->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/LSProcurarCategoria.php','frm','inCodCategoria','stCategoria','','".Sessao::getId()."','800','550')" );

//Selecão dos agentes nocivos
$obTxtAgentesNocivos = new TextBox;
$obTxtAgentesNocivos->setRotulo             ( "Classificação dos Agentes Nocivos"   );
$obTxtAgentesNocivos->setName               ( "stNumClassificacao"                  );
$obTxtAgentesNocivos->setValue              ( isset($stNumClassificacao) ? $stNumClassificacao : null               );
$obTxtAgentesNocivos->setTitle              ( "Informe se o trabalhador possui ou não exposição a agentes nocivos." );
$obTxtAgentesNocivos->setSize               ( 10                                    );
$obTxtAgentesNocivos->setMaxLength          ( 3                                     );
$obTxtAgentesNocivos->setNull               ( false                                 );

$obRPessoalServidor->roUltimoContratoServidor->obRPessoalOcorrencia->setCodOcorrencia("");
$obRPessoalServidor->roUltimoContratoServidor->obRPessoalOcorrencia->listarOcorrencia( $rsAgentesNocivos );
$obCmbAgentesNocivos = new Select;
$obCmbAgentesNocivos->setName                ( "stNumClassificacaoCmb"              );
$obCmbAgentesNocivos->setValue               ( isset($stNumClassificacao) ? $stNumClassificacao : null               );
$obCmbAgentesNocivos->setRotulo              ( "Classificação dos Agentes Nocivos"  );
$obCmbAgentesNocivos->setTitle               ( "Informe se o trabalhador possui ou não exposição a agentes nocivos." );
$obCmbAgentesNocivos->setNull                ( false                                );
//Adicionado condicional para evitar erro que seleciona o campo "Selecione" sempre que o valor é "0"
//Assim quando o $stNumClassificacao for diferente de '' ou null não é adicionado o "Selecione"
if ($stNumClassificacao == '' || $stNumClassificacao == null) {
    $obCmbAgentesNocivos->addOption          ( "", "Selecione"                      );
}
$obCmbAgentesNocivos->setCampoId             ( "[num_ocorrencia]"                   );
$obCmbAgentesNocivos->setCampoDesc           ( "descricao"                          );
$obCmbAgentesNocivos->preencheCombo          ( $rsAgentesNocivos                    );
$obCmbAgentesNocivos->setStyle               ( "width: 250px"                       );
//FIM INFORMAÇÕES CONTRATUAIS

//INFORMAÇÕES DO CARGO
//Selecão da regime
$obTxtCodRegime = new TextBox;
$obTxtCodRegime->setRotulo                  ( "Regime"                              );
$obTxtCodRegime->setName                    ( "inCodRegime"                         );
$obTxtCodRegime->setValue                   ( isset($inCodRegime) ? $inCodRegime : null);
$obTxtCodRegime->setTitle                   ( "Informe o regime de trabalho."       );
$obTxtCodRegime->setSize                    ( 10                                    );
$obTxtCodRegime->setMaxLength               ( 8                                     );
$obTxtCodRegime->setInteiro                 ( true                                  );
$obTxtCodRegime->setNull                    ( true                                  );
$obTxtCodRegime->obEvento->setOnChange      ( "buscaValor('preencheSubDivisao',3);preencheCampo( this, document.frm.inCodRegimeFuncao);preencheCampo( this, document.frm.stRegimeFuncao);"    );

$obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->obTRegime->recuperaTodos( $rsRegime );
$obCmbCodRegime = new Select;
$obCmbCodRegime->setName                    ( "stRegime"                            );
$obCmbCodRegime->setValue                   ( isset($inCodRegime) ? $inCodRegime : null );
$obCmbCodRegime->setRotulo                  ( "Regime"                              );
$obCmbCodRegime->setTitle                   ( "Selecione o regime."                 );
$obCmbCodRegime->setNull                    ( false                                 );
$obCmbCodRegime->setCampoId                 ( "[cod_regime]"                        );
$obCmbCodRegime->setCampoDesc               ( "descricao"                           );
$obCmbCodRegime->addOption                  ( "", "Selecione"                       );
$obCmbCodRegime->preencheCombo              ( $rsRegime                             );
$obCmbCodRegime->obEvento->setOnChange      ( "buscaValor('preencheSubDivisao',3);preencheCampo( this, document.frm.inCodRegimeFuncao);preencheCampo( this, document.frm.stRegimeFuncao);"    );

//Selecão da Sub-divisao
$obTxtCodSubDivisao = new TextBox;
$obTxtCodSubDivisao->setRotulo              ( "Subdivisão"                          );
$obTxtCodSubDivisao->setName                ( "inCodSubDivisao"                     );
$obTxtCodSubDivisao->setValue               ( isset($inCodSubDivisao) ? $inCodSubDivisao : null );
$obTxtCodSubDivisao->setTitle               ( "Selecione a subdivisão do regime."   );
$obTxtCodSubDivisao->setSize                ( 10                                    );
$obTxtCodSubDivisao->setMaxLength           ( 8                                     );
$obTxtCodSubDivisao->setInteiro             ( true                                  );
$obTxtCodSubDivisao->setNull                ( true                                  );
$obTxtCodSubDivisao->obEvento->setOnChange ( "buscaValor('preencheCargo',3);preencheCampo( this, document.frm.inCodSubDivisaoFuncao);preencheCampo( this, document.frm.stSubDivisaoFuncao);" );

$obCmbCodSubDivisao = new Select;
$obCmbCodSubDivisao->setName                ( "stSubDivisao"                        );
$obCmbCodSubDivisao->setValue               ( isset($inCodSubDivisao) ? $inCodSubDivisao : null );
$obCmbCodSubDivisao->setRotulo              ( "Subdivisão"                          );
$obCmbCodSubDivisao->setTitle               ( "Selecione a subdivisão."             );
$obCmbCodSubDivisao->setNull                ( false                                 );
$obCmbCodSubDivisao->setCampoId             ( "[cod_sub_divisao]"                   );
$obCmbCodSubDivisao->setCampoDesc           ( "descricao"                           );
$obCmbCodSubDivisao->addOption              ( "", "Selecione"                       );
$obCmbCodSubDivisao->obEvento->setOnChange ( "buscaValor('preencheCargo',3);preencheCampo( this, document.frm.inCodSubDivisaoFuncao);preencheCampo( this, document.frm.stSubDivisaoFuncao);"      );

$obTxtCargo = new TextBox;
$obTxtCargo->setRotulo                      ( "Cargo"                               );
$obTxtCargo->setName                        ( "inCodCargo"                          );
$obTxtCargo->setValue                       ( isset($inCodCargo) ? $inCodCargo : null );
$obTxtCargo->setTitle                       ( "Selecione o cargo do servidor."      );
$obTxtCargo->setSize                        ( 10                                    );
$obTxtCargo->setMaxLength                   ( 10                                    );
$obTxtCargo->setInteiro                     ( true                                  );
$obTxtCargo->setNull                        ( true                                  );
$obTxtCargo->obEvento->setOnChange          ( "buscaValor('preencheEspecialidade',3);preencheCampo( this, document.frm.inCodFuncao);preencheCampo( this, document.frm.stFuncao);" );

$obCmbCargo = new Select;
$obCmbCargo->setName                        ( "stCargo"                             );
$obCmbCargo->setValue                       ( isset($inCodCargo) ? $inCodCargo : null );
$obCmbCargo->setRotulo                      ( "Cargo"                               );
$obCmbCargo->setTitle                       ( "Selecione o cargo do servidor."      );
$obCmbCargo->setNull                        ( false                                 );
$obCmbCargo->addOption                      ( "", "Selecione"                       );
$obCmbCargo->setCampoId                     ( "[cod_cargo]"                         );
$obCmbCargo->setCampoDesc                   ( "descricao"                           );
$obCmbCargo->obEvento->setOnChange          ( "buscaValor('preencheEspecialidade',3);
                                               preencheCampo( this, document.frm.inCodFuncao);
                                               preencheCampo( this, document.frm.stFuncao);" );

//Selecão da Especialidade Cargo
$obTxtCodEspecialidadeCargo = new TextBox;
$obTxtCodEspecialidadeCargo->setRotulo      ( "Especialidade"                          );
$obTxtCodEspecialidadeCargo->setName        ( "inCodEspecialidadeCargo"                );
$obTxtCodEspecialidadeCargo->setValue       ( $inCodEspecialidadeCargo                 );
$obTxtCodEspecialidadeCargo->setTitle       ( "Selecione a especialidade do servidor." );
$obTxtCodEspecialidadeCargo->setSize        ( 10                                       );
$obTxtCodEspecialidadeCargo->setMaxLength   ( 10                                       );
$obTxtCodEspecialidadeCargo->setInteiro     ( true                                     );
$obTxtCodEspecialidadeCargo->setNull        ( true                                     );
$obTxtCodEspecialidadeCargo->obEvento->setOnChange( "buscaValor('preenchePreEspecialidadeFuncao',3);" );

$obCmbCodEspecialidadeCargo = new Select;
$obCmbCodEspecialidadeCargo->setName        ( "stEspecialidadeCargo"                   );
$obCmbCodEspecialidadeCargo->setValue       ( $inCodEspecialidadeCargo                 );
$obCmbCodEspecialidadeCargo->setRotulo      ( "Função"                                 );
$obCmbCodEspecialidadeCargo->setTitle       ( "Selecione a especialidade do servidor." );
$obCmbCodEspecialidadeCargo->setNull        ( true                                     );
$obCmbCodEspecialidadeCargo->setCampoId     ( "[cod_especialidade]"                    );
$obCmbCodEspecialidadeCargo->setCampoDesc   ( "descricao_especialidade"                );
$obCmbCodEspecialidadeCargo->addOption      ( "", "Selecione"                          );
$obCmbCodEspecialidadeCargo->obEvento->setOnChange( "buscaValor('preenchePreEspecialidadeFuncao',3);" );

//FIM INFORMAÇÕES DO CARGO

//INFORMAÇÕES DA FUNÇÃO
$obTxtCodRegimeFuncao = new TextBox;
$obTxtCodRegimeFuncao->setRotulo             ( "Regime"                             );
$obTxtCodRegimeFuncao->setName               ( "inCodRegimeFuncao"                  );
$obTxtCodRegimeFuncao->setValue              ( $inCodRegimeFuncao                   );
$obTxtCodRegimeFuncao->setTitle              ( "Informe o regime de trabalho."      );
$obTxtCodRegimeFuncao->setSize               ( 10                                   );
$obTxtCodRegimeFuncao->setMaxLength          ( 8                                    );
$obTxtCodRegimeFuncao->setInteiro            ( true                                 );
$obTxtCodRegimeFuncao->setNull               ( true                                 );
$obTxtCodRegimeFuncao->obEvento->setOnChange ( "buscaValor('preencheSubDivisaoFuncao',3);" );

$obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->obTRegime->recuperaTodos( $rsRegime );
$obCmbCodRegimeFuncao = new Select;
$obCmbCodRegimeFuncao->setName               ( "stRegimeFuncao"                     );
$obCmbCodRegimeFuncao->setValue              ( $inCodRegimeFuncao                   );
$obCmbCodRegimeFuncao->setRotulo             ( "Regime"                             );
$obCmbCodRegimeFuncao->setTitle              ( "Selecione o regime."                );
$obCmbCodRegimeFuncao->setNull               ( false                                );
$obCmbCodRegimeFuncao->setCampoId            ( "[cod_regime]"                       );
$obCmbCodRegimeFuncao->setCampoDesc          ( "descricao"                          );
$obCmbCodRegimeFuncao->addOption             ( "", "Selecione"                      );
$obCmbCodRegimeFuncao->preencheCombo         ( $rsRegime                            );
$obCmbCodRegimeFuncao->obEvento->setOnChange ( "buscaValor('preencheSubDivisaoFuncao',3);" );

$obTxtCodSubDivisaoFuncao = new TextBox;
$obTxtCodSubDivisaoFuncao->setRotulo        ( "Subdivisão"                          );
$obTxtCodSubDivisaoFuncao->setName          ( "inCodSubDivisaoFuncao"               );
$obTxtCodSubDivisaoFuncao->setValue         ( $inCodSubDivisaoFuncao                );
$obTxtCodSubDivisaoFuncao->setTitle         ( "Selecione a subdivisão do regime."   );
$obTxtCodSubDivisaoFuncao->setSize          ( 10                                    );
$obTxtCodSubDivisaoFuncao->setMaxLength     ( 8                                     );
$obTxtCodSubDivisaoFuncao->setInteiro       ( true                                  );
$obTxtCodSubDivisaoFuncao->setNull          ( true                                  );
$obTxtCodSubDivisaoFuncao->obEvento->setOnChange ( "buscaValor('preencheFuncao',3);" );

$obCmbCodSubDivisaoFuncao = new Select;
$obCmbCodSubDivisaoFuncao->setName          ( "stSubDivisaoFuncao"                  );
$obCmbCodSubDivisaoFuncao->setValue         ( $inCodSubDivisao                      );
$obCmbCodSubDivisaoFuncao->setRotulo        ( "Subdivisão"                          );
$obCmbCodSubDivisaoFuncao->setTitle         ( "Selecione a subdivisão."             );
$obCmbCodSubDivisaoFuncao->setNull          ( false                                 );
$obCmbCodSubDivisaoFuncao->setCampoId       ( "[cod_sub_divisao]"                   );
$obCmbCodSubDivisaoFuncao->setCampoDesc     ( "descricao"                           );
$obCmbCodSubDivisaoFuncao->addOption        ( "", "Selecione"                       );
$obCmbCodSubDivisaoFuncao->obEvento->setOnChange ( "buscaValor('preencheFuncao',3);" );

//Selecão da funcao
$obTxtCodFuncao = new TextBox;
$obTxtCodFuncao->setRotulo                  ( "Função"                              );
$obTxtCodFuncao->setName                    ( "inCodFuncao"                         );
$obTxtCodFuncao->setValue                   ( $inCodFuncao                          );
$obTxtCodFuncao->setTitle                   ( "Selecione a função do servidor."     );
$obTxtCodFuncao->setSize                    ( 10                                    );
$obTxtCodFuncao->setMaxLength               ( 10                                    );
$obTxtCodFuncao->setInteiro                 ( true                                  );
$obTxtCodFuncao->setNull                    ( false                                 );
$obTxtCodFuncao->obEvento->setOnChange      ( " buscaValor('preencheEspecialidadeFuncao',3);" );

$obCmbCodFuncao = new Select;
$obCmbCodFuncao->setName                    ( "stFuncao"                            );
$obCmbCodFuncao->setValue                   ( $inCodFuncao                          );
$obCmbCodFuncao->setRotulo                  ( "Função"                              );
$obCmbCodFuncao->setTitle                   ( "Selecione a função do servidor."     );
$obCmbCodFuncao->setNull                    ( false                                 );
$obCmbCodFuncao->setCampoId                 ( "[cod_cargo]"                         );
$obCmbCodFuncao->setCampoDesc               ( "descricao"                           );
$obCmbCodFuncao->addOption                  ( "", "Selecione"                       );
$obCmbCodFuncao->obEvento->setOnChange      ( "buscaValor('preencheEspecialidadeFuncao',3);" );

//Selecão da Especialidade Funcao
$obTxtCodEspecialidadeFuncao = new TextBox;
$obTxtCodEspecialidadeFuncao->setRotulo     ( "Especialidade"                          );
$obTxtCodEspecialidadeFuncao->setName       ( "inCodEspecialidadeFuncao"               );
$obTxtCodEspecialidadeFuncao->setValue      ( $inCodEspecialidadeFuncao                );
$obTxtCodEspecialidadeFuncao->setTitle      ( "Selecione a especialidade do servidor." );
$obTxtCodEspecialidadeFuncao->setSize       ( 10                                       );
$obTxtCodEspecialidadeFuncao->setMaxLength  ( 10                                       );
$obTxtCodEspecialidadeFuncao->setInteiro    ( true                                     );
$obTxtCodEspecialidadeFuncao->setNull       ( true                                     );
$obTxtCodEspecialidadeFuncao->obEvento->setOnChange( "buscaValor('preencheInformacoesSalariais',3);" );

$obCmbCodEspecialidadeFuncao = new Select;
$obCmbCodEspecialidadeFuncao->setName       ( "stEspecialidadeFuncao"                 );
$obCmbCodEspecialidadeFuncao->setValue      ( $inCodEspecialidadeFuncao               );
$obCmbCodEspecialidadeFuncao->setRotulo     ( "Especialidade"                         );
$obCmbCodEspecialidadeFuncao->setTitle      ( "Selecione a especialidade do servidor." );
$obCmbCodEspecialidadeFuncao->setNull       ( true                                    );
$obCmbCodEspecialidadeFuncao->setCampoId    ( "[cod_especialidade]"                   );
$obCmbCodEspecialidadeFuncao->setCampoDesc  ( "descricao_especialidade"               );
$obCmbCodEspecialidadeFuncao->addOption     ( "", "Selecione"                         );
$obCmbCodEspecialidadeFuncao->obEvento->setOnChange( "buscaValor('preencheInformacoesSalariais',3);" );

$obDataAlteracaoFuncao = new Data;
$obDataAlteracaoFuncao->setRotulo           ( "Data da Alteração da Função"         );
$obDataAlteracaoFuncao->setTitle            ( "Data da alteração da função."         );
$obDataAlteracaoFuncao->setName             ( "dtDataAlteracaoFuncao"               );
$obDataAlteracaoFuncao->setId               ( 'dtDataAlteracaoFuncao'               );
$obDataAlteracaoFuncao->setValue            ( $dtDataAlteracaoFuncao                );
$obDataAlteracaoFuncao->setSize             ( 10                                    );
$obDataAlteracaoFuncao->setMaxLength        ( 10                                    );
$obDataAlteracaoFuncao->setNull             ( false                                 );
$obDataAlteracaoFuncao->setInteiro          ( false                                 );
$obDataAlteracaoFuncao->setReadOnly         ( true                                  );
$obDataAlteracaoFuncao->setStyle            ( "color: #888888"                      );
$obDataAlteracaoFuncao->obEvento->setOnChange("buscaValor('validaDataAlteracaoFuncao',3);");

$obHdnDataAlteracaoFuncao = new Hidden;
$obHdnDataAlteracaoFuncao->setName          ( "hdnDataAlteracaoFuncao"              );
$obHdnDataAlteracaoFuncao->setValue         ( $dtDataAlteracaoFuncao                );

//FIM INFORMAÇÕES DA FUNÇÃO

//INFORMAÇÕES DE LOTAÇÃO
$obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();
$obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);

include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
$obIMontaOrganograma = new IMontaOrganograma();
$obIMontaOrganograma->setCodOrgao($inCodLotacao);
$obIMontaOrganograma->setNivelObrigatorio(1);
$obIMontaOrganograma->obROrganograma->setCodOrganograma($rsOrganogramaVigente->getCampo('cod_organograma'));

//Informação de local
$obBscLocal = new BuscaInner;
$obBscLocal->setRotulo                      ( "Local"                               );
$obBscLocal->setTitle                       ( "Local de trabalho do servidor."       );
$obBscLocal->setNull                        ( true                                  );
$obBscLocal->setId                          ( "stLocal"                             );
$obBscLocal->obCampoCod->setName            ( "inCodLocal"                          );
$obBscLocal->obCampoCod->setValue           ( $inCodLocal                           );
$obBscLocal->obCampoCod->setSize            ( 10                                    );
$obBscLocal->obCampoCod->obEvento->setOnBlur("buscaValor('buscaLocal',3);"             );
$obBscLocal->setFuncaoBusca                 ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarLocal.php','frm','inCodLocal','stLocal','','".Sessao::getId()."','800','550')" );

//********************************
//
// Informações de FGTS
//
//********************************

//Data da opcao do FGTS
$obTxtDataFGTS = new Data;
$obTxtDataFGTS->setName                     ( "dtDataFGTS"                          );
$obTxtDataFGTS->setNull                     ( true                                  );
$obTxtDataFGTS->setRotulo                   ( "Data de Opção do FGTS"               );
$obTxtDataFGTS->setTitle                    ( "Informe a data de opção do FGTS."     );
$obTxtDataFGTS->setValue                    ( $dtDataFGTS                           );
$obTxtDataFGTS->obEvento->setOnChange       ("buscaValor('validarDataFGTS',3);");

//Selecão do banco
$obTxtCodBanco = new TextBox;
$obTxtCodBanco->setRotulo               ( "Banco"                                   );
$obTxtCodBanco->setName                 ( "inCodBancoFGTS"                          );
$obTxtCodBanco->setValue                ( $inCodBancoFGTS                           );
$obTxtCodBanco->setTitle                ( "Selecione o banco para crédito do FGTS."  );
$obTxtCodBanco->setSize                 ( 10                                        );
$obTxtCodBanco->setMaxLength            ( 10                                        );
$obTxtCodBanco->setInteiro              ( true                                      );
$obTxtCodBanco->setNull                 ( true                                      );
$obTxtCodBanco->obEvento->setOnChange   ( "buscaValor('preencheAgenciaBancaria',3);"   );

$obRPessoalServidor->roUltimoContratoServidor->obRMonetarioBancoFGTS->obTMONBanco->recuperaTodos( $rsBancoFGTS );
$obCmbCodBanco = new Select;
$obCmbCodBanco->setName                  ( "stBancoFGTS" );
$obCmbCodBanco->setValue                 ( $inCodBancoFGTS  );
$obCmbCodBanco->setRotulo                ( "Banco"   );
$obCmbCodBanco->setTitle                 ( "Informe o banco para crédito do FGTS." );
$obCmbCodBanco->setNull                  ( true );
$obCmbCodBanco->setCampoId               ( "num_banco" );
$obCmbCodBanco->setCampoDesc             ( "nom_banco" );
$obCmbCodBanco->addOption                ( "", "Selecione" );
$obCmbCodBanco->preencheCombo            ( $rsBancoFGTS     );
$obCmbCodBanco->setStyle                 ( "width: 250px"  );
$obCmbCodBanco->obEvento->setOnChange    ( "buscaValor('preencheAgenciaBancaria',3);" );

//Selecão do agencia bancaria
$obTxtCodAgenciaBanco = new TextBox;
$obTxtCodAgenciaBanco->setRotulo             ( "Agência"     );
$obTxtCodAgenciaBanco->setName               ( "inCodAgenciaFGTS" );
$obTxtCodAgenciaBanco->setValue              ( $inCodAgenciaFGTS  );
$obTxtCodAgenciaBanco->setTitle              ( "Selecione a agência para crédito do FGTS." );
$obTxtCodAgenciaBanco->setSize               ( 10    );
$obTxtCodAgenciaBanco->setMaxLength          ( 10    );
$obTxtCodAgenciaBanco->setInteiro            ( false );
$obTxtCodAgenciaBanco->setNull               ( true );
$obTxtCodAgenciaBanco->setCaracteresAceitos  ( "[0-9-A-Za-z]" );

$obCmbAgenciaBanco = new Select;
$obCmbAgenciaBanco->setName                  ( "stAgenciaBancoFGTS" );
$obCmbAgenciaBanco->setValue                 ( $inCodAgenciaFGTS  );
$obCmbAgenciaBanco->setRotulo                ( "Agência"   );
$obCmbAgenciaBanco->setTitle                 ( "Selecione a agência para crédito do FGTS." );
$obCmbAgenciaBanco->setNull                  ( true );
$obCmbAgenciaBanco->setCampoId               ( "[cod_agencia_banco]" );
$obCmbAgenciaBanco->setCampoDesc             ( "descricao" );
$obCmbAgenciaBanco->addOption                ( "", "Selecione" );
$obCmbAgenciaBanco->setStyle                 ( "width: 250px"  );

$obTxtContaCredito = new TextBox;
$obTxtContaCredito->setRotulo    ( "Conta para Crédito");
$obTxtContaCredito->setTitle     ( "Informe a conta para crédito do FGTS.");
$obTxtContaCredito->setName      ( "inContaCreditoFGTS" );
$obTxtContaCredito->setValue     ( $inContaCreditoFGTS  );
$obTxtContaCredito->setMaxLength ( 15  );
$obTxtContaCredito->setSize      ( 12  );
$obTxtContaCredito->setNull      ( true );

//******************************************
//
// Informacoes salariais
//
//******************************************
//seleção de padrao
$obTxtCodPadrao = new TextBox;
$obTxtCodPadrao->setRotulo             ( "Padrão"     );
$obTxtCodPadrao->setName               ( "inCodPadrao" );
$obTxtCodPadrao->setValue              ( $inCodPadrao );
$obTxtCodPadrao->setTitle              ( "Informe o padrão." );
$obTxtCodPadrao->setSize               ( 10    );
$obTxtCodPadrao->setMaxLength          ( 10    );
$obTxtCodPadrao->setInteiro            ( true );
$obTxtCodPadrao->setNull               ( true );
$obTxtCodPadrao->obEvento->setOnChange    ( "buscaValor('preencheProgressao',3);" );

$obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->listarPadraoPorContratosInativos( $rsPadrao );

$obCmbCodPadrao = new Select;
$obCmbCodPadrao->setName                  ( "stPadrao"            );
$obCmbCodPadrao->setValue                 ( $inCodPadrao          );
$obCmbCodPadrao->setRotulo                ( "Padrao"              );
$obCmbCodPadrao->setTitle                 ( "Selecione o padrão." );
$obCmbCodPadrao->setNull                  ( true                  );
$obCmbCodPadrao->setCampoId               ( "[cod_padrao]" );
$obCmbCodPadrao->setCampoDesc             ( "[descricao] - [valor]" );
$obCmbCodPadrao->addOption                ( "", "Selecione"       );
$obCmbCodPadrao->preencheCombo            ( $rsPadrao             );
//$obCmbCodPadrao->setStyle                 ( "width: 250px"        );
$obCmbCodPadrao->obEvento->setOnChange    ( "buscaValor('preencheProgressao',3);" );

$obHdnProgressao =  new Hidden;
$obHdnProgressao->setName   ( "inCodProgressao" );
$obHdnProgressao->setValue  ( $inCodProgressao  );

//Label da progressao
$obLblProgressao = new Label;
$obLblProgressao->setRotulo ( 'Progressão'    );
$obLblProgressao->setName   ( 'stlblProgressao' );
$obLblProgressao->setId     ( 'stlblProgressao' );
$obLblProgressao->setValue  ( isset($stlblProgressao) ? $stlblProgressao : null );

$obTxtHorasMensais = new TextBox;
$obTxtHorasMensais->setRotulo           ( "Horas Mensais"                            );
$obTxtHorasMensais->setName             ( "stHorasMensais"                           );
$obTxtHorasMensais->setValue            ( $stHorasMensais                            );
$obTxtHorasMensais->setTitle            ( "Informe a quantidade de horas mensais."    );
$obTxtHorasMensais->setNull             ( false                                      );
$obTxtHorasMensais->setSize             ( 6                                          );
$obTxtHorasMensais->setMaxLength        ( 6                                          );
$obTxtHorasMensais->setFloat            ( true                                       );
$obTxtHorasMensais->obEvento->setOnChange      ( "buscaValor('calculaSalario',3);" );

$obTxtHorasSemanais = new TextBox;
$obTxtHorasSemanais->setRotulo           ( "Horas Semanais"                           );
$obTxtHorasSemanais->setName             ( "stHorasSemanais"                          );
$obTxtHorasSemanais->setValue            ( $stHorasSemanais                           );
$obTxtHorasSemanais->setTitle            ( "Informe a quantidade de horas semanais."   );
$obTxtHorasSemanais->setNull             ( false                                      );
$obTxtHorasSemanais->setSize             ( 6                                          );
$obTxtHorasSemanais->setMaxLength        ( 6                                          );
$obTxtHorasSemanais->setFloat            ( true                                       );

//Valor do salario salarial
$obTxtSalario = new Moeda;
$obTxtSalario->setRotulo    ( "Salário");
$obTxtSalario->setTitle     ( "Informe o salário do servidor.");
$obTxtSalario->setName      ( "inSalario" );
$obTxtSalario->setValue     ( $inSalario  );
$obTxtSalario->setMaxLength ( 14  );
$obTxtSalario->setSize      ( 15  );
$obTxtSalario->setNull      ( false );

//Vigência
$obDtVigenciaSalario = new Data;
$obDtVigenciaSalario->setName               ( "dtVigenciaSalario"            );
$obDtVigenciaSalario->setTitle              ("Informe a vigência do salário.");
$obDtVigenciaSalario->setNull               ( false                          );
$obDtVigenciaSalario->setRotulo             ( "Vigência do Salário"          );
$obDtVigenciaSalario->setValue              ( $dtVigenciaSalario             );
$obDtVigenciaSalario->obEvento->setOnChange ( "buscaValor('validarVigenciaSalario',3);" );

//Selecão do banco
$obTxtCodBancoSalario = new TextBox;
$obTxtCodBancoSalario->setRotulo             ( "Banco"     );
$obTxtCodBancoSalario->setName               ( "inCodBancoSalario" );
$obTxtCodBancoSalario->setValue              ( $inCodBancoSalario  );
$obTxtCodBancoSalario->setTitle              ( "Selecione o banco para crédito do salário." );
$obTxtCodBancoSalario->setSize               ( 10    );
$obTxtCodBancoSalario->setMaxLength          ( 10    );
$obTxtCodBancoSalario->setInteiro            ( true );
$obTxtCodBancoSalario->obEvento->setOnChange    ( "buscaValor('preencheAgenciaBancariaSalario',3);" );
if( !$inCodBancoSalario )
    $obTxtCodBancoSalario->setDisabled           ( true );

$obRPessoalServidor->roUltimoContratoServidor->obRMonetarioBancoSalario->obTMONBanco->recuperaTodos( $rsBancoSalario );
$obCmbCodBancoSalario = new Select;
$obCmbCodBancoSalario->setName                  ( "stBancoSalario" );
$obCmbCodBancoSalario->setValue                 ( $inCodBancoSalario  );
$obCmbCodBancoSalario->setRotulo                ( "Banco"   );
$obCmbCodBancoSalario->setTitle                 ( "Selecione o banco para crédito do salário." );
$obCmbCodBancoSalario->setCampoId               ( "num_banco" );
$obCmbCodBancoSalario->setCampoDesc             ( "nom_banco" );
$obCmbCodBancoSalario->addOption                ( "", "Selecione" );
$obCmbCodBancoSalario->preencheCombo            ( $rsBancoSalario     );
$obCmbCodBancoSalario->setStyle                 ( "width: 250px"  );
$obCmbCodBancoSalario->obEvento->setOnChange    ( "buscaValor('preencheAgenciaBancariaSalario',3);" );
if( !$inCodBancoSalario )
    $obCmbCodBancoSalario->setDisabled              ( true  );

//Selecão do agencia bancaria
$obTxtCodAgenciaSalario = new TextBox;
$obTxtCodAgenciaSalario->setRotulo             ( "Agência"     );
$obTxtCodAgenciaSalario->setName               ( "inCodAgenciaSalario" );
$obTxtCodAgenciaSalario->setValue              ( $inCodAgenciaSalario  );
$obTxtCodAgenciaSalario->setTitle              ( "Selecione a agência para crédito do salário." );
$obTxtCodAgenciaSalario->setSize               ( 10    );
$obTxtCodAgenciaSalario->setMaxLength          ( 10    );
$obTxtCodAgenciaSalario->setInteiro            ( false );
$obTxtCodAgenciaSalario->setCaracteresAceitos  ( "[0-9-A-Za-z]" );
if( !$inCodAgenciaSalario )
    $obTxtCodAgenciaSalario->setDisabled           ( true );

$obCmbCodAgenciaSalario = new Select;
$obCmbCodAgenciaSalario->setName                  ( "stAgenciaSalario" );
$obCmbCodAgenciaSalario->setValue                 ( $inCodAgenciaSalario  );
$obCmbCodAgenciaSalario->setRotulo                ( "Agência"   );
$obCmbCodAgenciaSalario->setTitle                 ( "Selecione a agência para crédito do salário." );
$obCmbCodAgenciaSalario->setCampoId               ( "[cod_agencia_banco]" );
$obCmbCodAgenciaSalario->setCampoDesc             ( "descricao" );
$obCmbCodAgenciaSalario->addOption                ( "", "Selecione" );
$obCmbCodAgenciaSalario->setStyle                 ( "width: 250px"  );
if( !$inCodAgenciaSalario )
    $obCmbCodAgenciaSalario->setDisabled              ( true  );

//Conta para crédito salarial
$obTxtContaSalario = new TextBox;
$obTxtContaSalario->setRotulo    ( "Conta para Crédito");
$obTxtContaSalario->setTitle     ( "Informe a conta para crédito do salário." );
$obTxtContaSalario->setName      ( "inContaSalario" );
$obTxtContaSalario->setValue     ( $inContaSalario  );
$obTxtContaSalario->setMaxLength ( 15  );
$obTxtContaSalario->setSize      ( 12  );
if( !$inContaSalario )
    $obTxtContaSalario->setDisabled  ( true );

//Habilita campos
$obChkSalario = new CheckBox;
$obChkSalario->setRotulo             ( "Crédito em Conta"                                         );
$obChkSalario->setTitle              ( "Informe se existem especializações associadas ao cargo."   );
$obChkSalario->setName               ( "boHabilitaSalario"                                        );
$obChkSalario->setLabel              ( "Sim"                                                      );
if ($_REQUEST['stAcao'] == 'incluir') {
   $obChkSalario->setChecked        (  false                                                  );
}
if ($_REQUEST['stAcao'] == 'alterar') {
   if ($inContaSalario) {
      $obChkSalario->setChecked  (  true                                                      );
   } else {
      $obChkSalario->setChecked  (  false                                                       );
   }
}
$obChkSalario->obEvento->setOnChange ( "buscaValor('habilita',3);" );

//Forma de pagamento
$obTxtCodFormaPagamento = new TextBox;
$obTxtCodFormaPagamento->setRotulo             ( "Forma de Pagamento"     );
$obTxtCodFormaPagamento->setName               ( "inCodFormaPagamento" );
$obTxtCodFormaPagamento->setValue              ( $inCodFormaPagamento  );
$obTxtCodFormaPagamento->setTitle              ( "Selecione a forma de pagamento." );
$obTxtCodFormaPagamento->setSize               ( 10    );
$obTxtCodFormaPagamento->setMaxLength          ( 3    );
$obTxtCodFormaPagamento->setInteiro            ( true );
$obTxtCodFormaPagamento->setNull               ( true );
$obTxtCodFormaPagamento->obEvento->setOnChange ( "buscaValor('habilita',3);" );

$obCmbCodFormaPagamento = new Select;
$obCmbCodFormaPagamento->setName                  ( "stFormaPagamento" );
$obCmbCodFormaPagamento->setValue                 ( $inCodFormaPagamento  );
$obCmbCodFormaPagamento->setRotulo                ( "Forma de Pagamento"   );
$obCmbCodFormaPagamento->setTitle                 ( "Selecione a forma de pagamento." );
$obCmbCodFormaPagamento->setNull                  ( false);
$obCmbCodFormaPagamento->setCampoId               ( "[cod_forma_pagamento]" );
$obCmbCodFormaPagamento->setCampoDesc             ( "descricao" );
$obCmbCodFormaPagamento->addOption                ( "", "Selecione" );
$obCmbCodFormaPagamento->addOption                ( "1", "Em dinheiro"  );
$obCmbCodFormaPagamento->addOption                ( "2", "Cheque" );
$obCmbCodFormaPagamento->addOption                ( "3", "Crédito em conta"  );
$obCmbCodFormaPagamento->addOption                ( "4", "Ordem de pagamento"  );
$obCmbCodFormaPagamento->setStyle                 ( "width: 250px"  );
$obCmbCodFormaPagamento->obEvento->setOnChange ( "buscaValor('habilita',3);" );

//Tipo de pagamento
$obTxtCodTipoPagamento = new TextBox;
$obTxtCodTipoPagamento->setRotulo             ( "Tipo de Pagamento"     );
$obTxtCodTipoPagamento->setName               ( "inCodTipoPagamento" );
$obTxtCodTipoPagamento->setValue              ( $inCodTipoPagamento  );
$obTxtCodTipoPagamento->setTitle              ( "Selecione o tipo de pagamento." );
$obTxtCodTipoPagamento->setSize               ( 10    );
$obTxtCodTipoPagamento->setMaxLength          ( 3    );
$obTxtCodTipoPagamento->setInteiro            ( true );
$obTxtCodTipoPagamento->setNull               ( true );

$obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoPagamento->recuperaTodosTipoPagamento( $rsTipoPagamento );
$obCmbCodTipoPagamento = new Select;
$obCmbCodTipoPagamento->setName                  ( "stTipoPagamento" );
$obCmbCodTipoPagamento->setValue                 ( $inCodTipoPagamento  );
$obCmbCodTipoPagamento->setRotulo                ( "Tipo de Pagamento"   );
$obCmbCodTipoPagamento->setTitle                 ( "Informe o tipo de pagamento." );
$obCmbCodTipoPagamento->setNull                  ( false);
$obCmbCodTipoPagamento->setCampoId               ( "[cod_tipo_pagamento]" );
$obCmbCodTipoPagamento->setCampoDesc             ( "descricao" );
$obCmbCodTipoPagamento->addOption                ( "", "Selecione" );
$obCmbCodTipoPagamento->preencheCombo            ( $rsTipoPagamento     );
$obCmbCodTipoPagamento->setStyle                 ( "width: 250px"  );

// Tipo de salario
$obTxtCodTipoSalario = new TextBox;
$obTxtCodTipoSalario->setRotulo             ( "Tipo de Salário"     );
$obTxtCodTipoSalario->setName               ( "inCodTipoSalario" );
$obTxtCodTipoSalario->setValue              ( $inCodTipoSalario  );
$obTxtCodTipoSalario->setTitle              ( "Selecione o tipo de salário." );
$obTxtCodTipoSalario->setSize               ( 10    );
$obTxtCodTipoSalario->setMaxLength          ( 3    );
$obTxtCodTipoSalario->setInteiro            ( true );
$obTxtCodTipoSalario->setNull               ( true );

$obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoSalario->obTPessoalTipoSalario->recuperaTodos( $rsTipoSalario );
$obCmbCodTipoSalario = new Select;
$obCmbCodTipoSalario->setName                  ( "stTipoSalario" );
$obCmbCodTipoSalario->setValue                 ( $inCodTipoSalario  );
$obCmbCodTipoSalario->setRotulo                ( "Tipo de Salário"   );
$obCmbCodTipoSalario->setTitle                 ( "Informe o tipo de salário." );
$obCmbCodTipoSalario->setNull                  ( false);
$obCmbCodTipoSalario->setCampoId               ( "[cod_tipo_salario]" );
$obCmbCodTipoSalario->setCampoDesc             ( "descricao" );
$obCmbCodTipoSalario->addOption                ( "", "Selecione" );
$obCmbCodTipoSalario->preencheCombo            ( $rsTipoSalario     );
$obCmbCodTipoSalario->setStyle                 ( "width: 250px"  );

$obChkAdiantamento = new CheckBox;
$obChkAdiantamento->setRotulo         ( "Adiantamento"   );
$obChkAdiantamento->setTitle          ( "Informe se o servidor recebe adiantamento."   );
$obChkAdiantamento->setName           ( "boAdiantamento"  );
$obChkAdiantamento->setValue          ( 't' );
$obChkAdiantamento->setChecked        ( ($boAdiantamento == 't') );

//Cod Contrato
$obHdnCodContrato = new Hidden;
$obHdnCodContrato->setName          ( "inCodContrato" );
$obHdnCodContrato->setValue         ( $_REQUEST["inCodContrato"]  );

//Cod Cargo
$obHdnCodCargo = new Hidden;
$obHdnCodCargo->setName          ( "inHdnCodCargo" );
$obHdnCodCargo->setValue         ( $inCodCargo  );

//Cod Regime
$obHdnCodRegime = new Hidden;
$obHdnCodRegime->setName          ( "inHdnCodRegime" );
$obHdnCodRegime->setValue         ( $inCodRegime  );

//Cod SubDivisao
$obHdnCodSubDivisao = new Hidden;
$obHdnCodSubDivisao->setName          ( "inHdnCodSubDivisao" );
$obHdnCodSubDivisao->setValue         ( $inCodSubDivisao  );

//Cod Organorama
$obHdnCodOrganograma = new Hidden;
$obHdnCodOrganograma->setName          ( "inHdnCodOrganograma" );
$obHdnCodOrganograma->setValue         ( isset($inCodOrganograma) ? $inCodOrganograma : null );

//Registro
$obHdnRegistro = new Hidden;
$obHdnRegistro->setName          ( "stHdnRegistro" );
$obHdnRegistro->setId            ( "stHdnRegistro" );
$obHdnRegistro->setValue         ( isset($inContratoAutomatico) ? $inContratoAutomatico : null  );

if ($boFlagCarregaContrato == "off") {
    $stAcao = 'alterar';
    $stOrigem = 'incluir';
}

//***********************************
//
//dados do sindicato
//
//***********************************

//Define o objeto INNER para buscar sindicato cadastrado
$obBscCgmSindicato = new BuscaInner;
$obBscCgmSindicato->setRotulo           ( "CGM do Sindicato"        );
$obBscCgmSindicato->setTitle            ( "CGM do sindicato."        );
$obBscCgmSindicato->setId               ( "stNomSindicato"          );
$obBscCgmSindicato->obCampoCod->setName ( "inNumCGMSindicato"       );
$obBscCgmSindicato->obCampoCod->setValue( $inNumCGMSindicato        );
$obBscCgmSindicato->obCampoCod->obEvento->setOnBlur("buscaValor('buscaSindicato',3);");
$obBscCgmSindicato->setFuncaoBusca( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/LSProcurarSindicato.php','frm','inNumCGMSindicato','stNomSindicato','','".Sessao::getId()."','800','550')" );

$obTxtDataBase = new TextBox;
$obTxtDataBase->setName   ( "dtDataBase" );
$obTxtDataBase->setTitle  ( "Informe a data-base do servidor." );
$obTxtDataBase->setRotulo ( "Data-Base" );
$obTxtDataBase->setValue  ( isset($inDataBase) ? $inCodOrganograma : null  );
$obTxtDataBase->setInteiro( true  );
$obTxtDataBase->setReadOnly(true);
$obTxtDataBase->obEvento->setOnBlur ("buscaValor('validaDataBase',3);");

$obRPessoalServidor->obRPessoalConselho->listarConselho( $rsConselho );
$obTxtConselho = new TextBox;
$obTxtConselho->setRotulo               ( "Conselho"                                );
$obTxtConselho->setTitle                ( "Selecione o conselho profissional."       );
$obTxtConselho->setName                 ( "inCodConselho"                           );
$obTxtConselho->setValue                ( $inCodConselho                            );
$obTxtConselho->setMaxLength            ( 3                                         );
$obTxtConselho->setSize                 ( 12                                        );
$obTxtConselho->setInteiro              ( true                                      );
$obTxtConselho->setNull                 ( true                                      );

$obCmbConselho = new Select;
$obCmbConselho->setName                 ( "inConselho"                              );
$obCmbConselho->setValue                ( $inCodConselho                            );
$obCmbConselho->setStyle                ( "width: 250px"                            );
$obCmbConselho->setRotulo               ( "Conselho"                                );
$obCmbConselho->setTitle                ( "Selecione o conselho profissional."       );
$obCmbConselho->addOption               ( "", "Selecione"                           );
$obCmbConselho->setCampoID              ( "[cod_conselho]"                          );
$obCmbConselho->setCampoDesc            ( "descricao"                               );
$obCmbConselho->preencheCombo           ( $rsConselho                               );
$obCmbConselho->setNull                 ( true                                      );

$obTxtNumeroConselho = new TextBox;
$obTxtNumeroConselho->setRotulo         ( "Número Conselho Profissional"            );
$obTxtNumeroConselho->setTitle          ( "Informe o registro no conselho profissional."  );
$obTxtNumeroConselho->setName           ( "inNumeroConselho"                        );
$obTxtNumeroConselho->setValue          ( $inNumeroConselho                         );
$obTxtNumeroConselho->setSize           ( 15                                        );
$obTxtNumeroConselho->setMaxLength      ( 14                                        );
$obTxtNumeroConselho->setNull           ( true                                      );

$obTxtDataValidade = new Data;
$obTxtDataValidade->setName             ( "dtDataValidadeConselho"                  );
$obTxtDataValidade->setValue            ( $dtDataValidadeConselho                   );
$obTxtDataValidade->setTitle            ( "Informe a validade do registro no conselho profissional." );
$obTxtDataValidade->setNull             ( true                                      );
$obTxtDataValidade->setRotulo           ( "Data de Validade"                        );
$obTxtDataValidade->setSize             ( 15                                        );
$obTxtDataValidade->obEvento->setOnChange("buscaValor('validarDataValidade',3);");

//Selecão da grade de horário
$obTxtGradeHorario = new TextBox;
$obTxtGradeHorario->setRotulo                  ( "Tipo"                                );
$obTxtGradeHorario->setName                    ( "inCodGradeHorario"                   );
$obTxtGradeHorario->setValue                   ( $inCodGradeHorario                    );
$obTxtGradeHorario->setTitle                   ( "Selecione a grade de horário."        );
$obTxtGradeHorario->setSize                    ( 10                                    );
$obTxtGradeHorario->setMaxLength               ( 10                                    );
$obTxtGradeHorario->setInteiro                 ( true                                  );
$obTxtGradeHorario->setNull                    ( false                                 );
$obTxtGradeHorario->obEvento->setOnChange      ( "buscaValor('preencheTurnos',3);"        );

$obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->listarGrade( $rsGradeHorario,$boTransacao );
$obCmbGradeHorario = new Select;
$obCmbGradeHorario->setName                    ( "stGradeHorario"                      );
$obCmbGradeHorario->setValue                   ( $inCodGradeHorario                    );
$obCmbGradeHorario->setRotulo                  ( "Tipo"                                );
$obCmbGradeHorario->setTitle                   ( "Selecione a grade de horário."        );
$obCmbGradeHorario->setNull                    ( false                                 );
$obCmbGradeHorario->addOption                  ( "", "Selecione"                       );
$obCmbGradeHorario->setCampoId                 ( "[cod_grade]"                         );
$obCmbGradeHorario->setCampoDesc               ( "descricao"                           );
$obCmbGradeHorario->preencheCombo              ( $rsGradeHorario                       );
$obCmbGradeHorario->setStyle                   ( "width: 250px"                        );
$obCmbGradeHorario->obEvento->setOnChange      ( "buscaValor('preencheTurnos',3);"        );

$obSpnTurnos = new Span;
$obSpnTurnos->setId ('spnTurnos' );

$obDtAdmissao = new Data;
$obDtAdmissao->setName               ( "dtAdmissao"           );
$obDtAdmissao->setTitle              ("Informe a data de admissão.");
$obDtAdmissao->setNull               ( false                      );
$obDtAdmissao->setRotulo             ( "Data de Admissão"         );
$obDtAdmissao->setValue              ( $dtAdmissao            );
$obDtAdmissao->obEvento->setOnChange("buscaValor('validaDataAdmissao',3);");

$obSpnRescisao = new Span();
$obSpnRescisao->setId("spnRescisao");

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
    * Oculto de Manter Registro de Evento (Folha Complementar)
    * Data de Criação: 20/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30998 $
    $Name$
    $Author: souzadl $
    $Date: 2007-11-23 10:56:25 -0200 (Sex, 23 Nov 2007) $

    * Casos de uso: uc-04.05.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                             );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"                                              );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                           );
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php"                                           );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                                  );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                        );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                          );
include_once ( CAM_GRH_PES_COMPONENTES."IBuscaInnerLotacao.class.php"                                       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEventoComplementar";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function processarFiltro($boExecuta=false,$boMensagem=false)
{
    $stJs .= gerarSpan1(false,$boMensagem);
    $stJs .= "f.inFiltrar.options[1].selected = true;\n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function habilitaSpanFiltro($boExecuta=false)
{
    switch ($_POST['inFiltrar']) {
        case 0:
            $stJs .= gerarSpan1();
        break;
        case 1:
            $stJs .= gerarSpan2();
        break;
        case 2:
            $stJs .= gerarSpan3();
        break;
        case 3:
            $stJs .= gerarSpan4();
        break;
        case 4:
            $stJs .= gerarSpan5();
        break;
        case 5:
            $stJs .= gerarSpan6();
        break;
        case 6:
            $stJs .= gerarSpan7();
        break;
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan1($boExecuta=false,$boMensagem=false)
{
    if ($boMensagem) {
        $stMensagem = "Nenhuma folha complementar está aberta. Para efetuar o registro de eventos em uma folha complementar, é necessário abri-lo ou reabri-lo.";
        $obLblMensagem = new Label;
        $obLblMensagem->setRotulo               ( "Situação"                                                );
        $obLblMensagem->setValue                ( $stMensagem                                               );
    } else {
        $obIFiltroContrato            = new IFiltroContrato(true, true);
    }

    $obFormulario = new Formulario;
    if ($boMensagem) {
        $obFormulario->addComponente            ( $obLblMensagem                                            );
    } else {
        $obIFiltroContrato->geraFormulario( $obFormulario                                            );
        $obFormulario->obJavaScript->montaJavaScript();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);
    }
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnFiltrar').innerHTML = '".$obFormulario->getHTML()."';\n";
    $stJs .= "f.hdnFiltrar.value                       = '".$stEval."';\n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan2($boExecuta=false)
{
    $obForm = new Form;
    $obForm->setAction                          ( $pgList                                                   );

    $obBscCGM = new BuscaInner;
    $obBscCGM->setRotulo                        ( "CGM"                                                     );
    $obBscCGM->setTitle                         ( "Informe o CGM do servidor."                              );
    $obBscCGM->setNull                          ( false                                                     );
    $obBscCGM->setId                            ( "inCampoInner"                                            );
    $obBscCGM->obCampoCod->setName              ( "inNumCGM"                                                );
    $obBscCGM->obCampoCod->setValue             ( $inNumCGM                                                 );
    $obBscCGM->obCampoCod->obEvento->setOnBlur  ( "buscaValorFiltro('buscaCGM');"                           );
    $obBscCGM->setFuncaoBusca                   ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarCgm.php','frm','inNumCGM','inCampoInner','','".Sessao::getId()."&inFiltro=1','800','550')" );

    $obCmbRegistro = new Select;
    $obCmbRegistro->setRotulo                   ( "Matrícula"                                                );
    $obCmbRegistro->setTitle                    ( "Selecione o contrato."                                   );
    $obCmbRegistro->setNull                     ( false                                                     );
    $obCmbRegistro->setName                     ( "inContrato"                                              );
    $obCmbRegistro->setValue                    ( $inContrato                                               );
    $obCmbRegistro->setStyle                    ( "width: 200px"                                            );
    $obCmbRegistro->addOption                   ( "", "Selecione"                                           );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo                    ( "CGM/Matrícula"                                            );
    $obFormulario->addComponente                ( $obBscCGM                                                 );
    $obFormulario->addComponente                ( $obCmbRegistro                                            );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnFiltrar').innerHTML = '".$obFormulario->getHTML()."';    \n";
    $stJs .= "f.hdnFiltrar.value                       = '".$stEval."';                     \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan3($boExecuta=false)
{
    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalRegime->listarRegime( $rsRegime );
    $obTxtRegime = new TextBox;
    $obTxtRegime->setRotulo                     ( "Regime"                                                  );
    $obTxtRegime->setName                       ( "inCodRegime"                                             );
    $obTxtRegime->setValue                      ( $inCodRegime                                              );
    $obTxtRegime->setTitle                      ( "Selecione o regime ao qual o cargo pertence para o filtro." );
    $obTxtRegime->setSize                       ( 10                                                        );
    $obTxtRegime->setMaxLength                  ( 8                                                         );
    $obTxtRegime->setInteiro                    ( true                                                      );
    $obTxtRegime->setNull                       ( true                                                      );
    $obTxtRegime->obEvento->setOnChange         ( "buscaValorFiltro('preencheSubDivisaoCargo');"            );

    $obCmbRegime = new Select;
    $obCmbRegime->setName                       ( "stRegime"                                                );
    $obCmbRegime->setValue                      ( $inCodRegime                                              );
    $obCmbRegime->setRotulo                     ( "Regime"                                                  );
    $obCmbRegime->setTitle                      ( "Selecione o regime ao qual o cargo pertence para o filtro." );
    $obCmbRegime->setNull                       ( false                                                     );
    $obCmbRegime->setCampoId                    ( "[cod_regime]"                                            );
    $obCmbRegime->setCampoDesc                  ( "descricao"                                               );
    $obCmbRegime->addOption                     ( "", "Selecione"                                           );
    $obCmbRegime->preencheCombo                 ( $rsRegime                                                 );
    $obCmbRegime->setStyle                      ( "width: 250px"                                            );
    $obCmbRegime->obEvento->setOnChange         ( "buscaValorFiltro('preencheSubDivisaoCargo');"            );

    $obTxtSubDivisao = new TextBox;
    $obTxtSubDivisao->setRotulo                 ( "Subdivisão"                                              );
    $obTxtSubDivisao->setName                   ( "inCodSubDivisao"                                         );
    $obTxtSubDivisao->setValue                  ( $inCodSubDivisao                                          );
    $obTxtSubDivisao->setTitle                  ( "Selecione a subdivisão a qual o cargo pertence para o filtro." );
    $obTxtSubDivisao->setSize                   ( 10                                                        );
    $obTxtSubDivisao->setMaxLength              ( 8                                                         );
    $obTxtSubDivisao->setInteiro                ( true                                                      );
    $obTxtSubDivisao->setNull                   ( true                                                      );
    $obTxtSubDivisao->obEvento->setOnChange     ( "buscaValorFiltro('preencheCargoCargo');"                 );

    $obCmbSubDivisao = new Select;
    $obCmbSubDivisao->setName                   ( "stSubDivisao"                                            );
    $obCmbSubDivisao->setValue                  ( $inCodSubDivisao                                          );
    $obCmbSubDivisao->setRotulo                 ( "Subdivisão"                                              );
    $obCmbSubDivisao->setTitle                  ( "Selecione a subdivisão a qual o cargo pertence para o filtro." );
    $obCmbSubDivisao->setNull                   ( false                                                     );
    $obCmbSubDivisao->setCampoId                ( "[cod_sub_divisao]"                                       );
    $obCmbSubDivisao->setCampoDesc              ( "descricao"                                               );
    $obCmbSubDivisao->addOption                 ( "", "Selecione"                                           );
    $obCmbSubDivisao->setStyle                  ( "width: 250px"                                            );
    $obCmbSubDivisao->obEvento->setOnChange     ( "buscaValorFiltro('preencheCargoCargo');"                 );

    $obTxtCargo = new TextBox;
    $obTxtCargo->setRotulo                      ( "Cargo"                                                   );
    $obTxtCargo->setName                        ( "inCodCargo"                                              );
    $obTxtCargo->setValue                       ( $inCodCargo                                               );
    $obTxtCargo->setTitle                       ( "Selecione o cargo para o filtro."                        );
    $obTxtCargo->setSize                        ( 10                                                        );
    $obTxtCargo->setMaxLength                   ( 10                                                        );
    $obTxtCargo->setInteiro                     ( true                                                      );
    $obTxtCargo->setNull                        ( true                                                      );
    $obTxtCargo->obEvento->setOnChange          ( "buscaValorFiltro('preencheEspecialidadeCargo');"         );

    $obCmbCargo = new Select;
    $obCmbCargo->setName                        ( "stCargo"                                                 );
    $obCmbCargo->setValue                       ( $inCodCargo                                               );
    $obCmbCargo->setRotulo                      ( "Cargo"                                                   );
    $obCmbCargo->setTitle                       ( "Selecione o cargo para o filtro."                        );
    $obCmbCargo->setNull                        ( false                                                     );
    $obCmbCargo->addOption                      ( "", "Selecione"                                           );
    $obCmbCargo->setCampoId                     ( "[cod_cargo]"                                             );
    $obCmbCargo->setCampoDesc                   ( "descricao"                                               );
    $obCmbCargo->setStyle                       ( "width: 250px"                                            );
    $obCmbCargo->obEvento->setOnChange          ( "buscaValorFiltro('preencheEspecialidadeCargo');"         );

    $obTxtEspecialidade = new TextBox;
    $obTxtEspecialidade->setRotulo              ( "Especialidade"                                           );
    $obTxtEspecialidade->setName                ( "inCodEspecialidade"                                      );
    $obTxtEspecialidade->setValue               ( $inCodEspecialidade                                       );
    $obTxtEspecialidade->setTitle               ( "Selecione a especialidade para o filtro."                );
    $obTxtEspecialidade->setSize                ( 10                                                        );
    $obTxtEspecialidade->setMaxLength           ( 10                                                        );
    $obTxtEspecialidade->setInteiro             ( true                                                      );
    $obTxtEspecialidade->setNull                ( true                                                      );

    $obCmbEspecialidade = new Select;
    $obCmbEspecialidade->setName                ( "stEspecialidade"                                         );
    $obCmbEspecialidade->setValue               ( $inCodEspecialidade                                       );
    $obCmbEspecialidade->setRotulo              ( "Função"                                                  );
    $obCmbEspecialidade->setTitle               ( "Selecione a especialidade para o filtro."                );
    $obCmbEspecialidade->setNull                ( true                                                      );
    $obCmbEspecialidade->setCampoId             ( "[cod_especialidade]"                                     );
    $obCmbEspecialidade->setCampoDesc           ( "descricao_especialidade"                                 );
    $obCmbEspecialidade->addOption              ( "", "Selecione"                                           );
    $obCmbEspecialidade->setStyle               ( "width: 250px"                                            );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo                    ( "Cargo"                                                   );
    $obFormulario->addComponenteComposto        ( $obTxtRegime          ,$obCmbRegime                       );
    $obFormulario->addComponenteComposto        ( $obTxtSubDivisao      ,$obCmbSubDivisao                   );
    $obFormulario->addComponenteComposto        ( $obTxtCargo           ,$obCmbCargo                        );
    $obFormulario->addComponenteComposto        ( $obTxtEspecialidade   ,$obCmbEspecialidade                );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnFiltrar').innerHTML = '".$obFormulario->getHTML()."';    \n";
    $stJs .= "f.hdnFiltrar.value                       = '".$stEval."';                     \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan4($boExecuta=false)
{
    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalRegime->listarRegime( $rsRegime );
    $obTxtRegime = new TextBox;
    $obTxtRegime->setRotulo                     ( "Regime"                                                  );
    $obTxtRegime->setName                       ( "inCodRegime"                                             );
    $obTxtRegime->setValue                      ( $inCodRegime                                              );
    $obTxtRegime->setTitle                      ( "Selecione o regime ao qual a função pertence para o filtro." );
    $obTxtRegime->setSize                       ( 10                                                        );
    $obTxtRegime->setMaxLength                  ( 8                                                         );
    $obTxtRegime->setInteiro                    ( true                                                      );
    $obTxtRegime->setNull                       ( true                                                      );
    $obTxtRegime->obEvento->setOnChange         ( "buscaValorFiltro('preencheSubDivisaoCargo');"            );

    $obCmbRegime = new Select;
    $obCmbRegime->setName                       ( "stRegime"                                                );
    $obCmbRegime->setValue                      ( $inCodRegime                                              );
    $obCmbRegime->setRotulo                     ( "Regime"                                                  );
    $obCmbRegime->setTitle                      ( "Selecione o regime ao qual a função pertence para o filtro." );
    $obCmbRegime->setNull                       ( false                                                     );
    $obCmbRegime->setCampoId                    ( "[cod_regime]"                                            );
    $obCmbRegime->setCampoDesc                  ( "descricao"                                               );
    $obCmbRegime->addOption                     ( "", "Selecione"                                           );
    $obCmbRegime->preencheCombo                 ( $rsRegime                                                 );
    $obCmbRegime->setStyle                      ( "width: 250px"                                            );
    $obCmbRegime->obEvento->setOnChange         ( "buscaValorFiltro('preencheSubDivisaoCargo');"            );

    $obTxtSubDivisao = new TextBox;
    $obTxtSubDivisao->setRotulo                 ( "Subdivisão"                                              );
    $obTxtSubDivisao->setName                   ( "inCodSubDivisao"                                         );
    $obTxtSubDivisao->setValue                  ( $inCodSubDivisao                                          );
    $obTxtSubDivisao->setTitle                  ( "Selecione a subdivisão a qual a função pertence para o filtro." );
    $obTxtSubDivisao->setSize                   ( 10                                                        );
    $obTxtSubDivisao->setMaxLength              ( 8                                                         );
    $obTxtSubDivisao->setInteiro                ( true                                                      );
    $obTxtSubDivisao->setNull                   ( true                                                      );
    $obTxtSubDivisao->obEvento->setOnChange     ( "buscaValorFiltro('preencheCargoCargo');"                 );

    $obCmbSubDivisao = new Select;
    $obCmbSubDivisao->setName                   ( "stSubDivisao"                                            );
    $obCmbSubDivisao->setValue                  ( $inCodSubDivisao                                          );
    $obCmbSubDivisao->setRotulo                 ( "Subdivisão"                                              );
    $obCmbSubDivisao->setTitle                  ( "Selecione a subdivisão a qual a função pertence para o filtro." );
    $obCmbSubDivisao->setNull                   ( false                                                     );
    $obCmbSubDivisao->setCampoId                ( "[cod_sub_divisao]"                                       );
    $obCmbSubDivisao->setCampoDesc              ( "descricao"                                               );
    $obCmbSubDivisao->addOption                 ( "", "Selecione"                                           );
    $obCmbSubDivisao->setStyle                  ( "width: 250px"                                            );
    $obCmbSubDivisao->obEvento->setOnChange     ( "buscaValorFiltro('preencheCargoCargo');"                 );

    $obTxtFuncao = new TextBox;
    $obTxtFuncao->setRotulo                     ( "Função"                                                  );
    $obTxtFuncao->setName                       ( "inCodCargo"                                              );
    $obTxtFuncao->setValue                      ( $inCodCargo                                               );
    $obTxtFuncao->setTitle                      ( "Selecione a função para o filtro."                       );
    $obTxtFuncao->setSize                       ( 10                                                        );
    $obTxtFuncao->setMaxLength                  ( 10                                                        );
    $obTxtFuncao->setInteiro                    ( true                                                      );
    $obTxtFuncao->setNull                       ( true                                                      );
    $obTxtFuncao->obEvento->setOnChange         ( "buscaValorFiltro('preencheEspecialidadeCargo');"         );

    $obCmbFuncao = new Select;
    $obCmbFuncao->setName                       ( "stCargo"                                                 );
    $obCmbFuncao->setValue                      ( $inCodCargo                                               );
    $obCmbFuncao->setRotulo                     ( "Função"                                                  );
    $obCmbFuncao->setTitle                      ( "Selecione a função para o filtro."                       );
    $obCmbFuncao->setNull                       ( false                                                     );
    $obCmbFuncao->addOption                     ( "", "Selecione"                                           );
    $obCmbFuncao->setCampoId                    ( "[cod_cargo]"                                             );
    $obCmbFuncao->setCampoDesc                  ( "descricao"                                               );
    $obCmbFuncao->setStyle                      ( "width: 250px"                                            );
    $obCmbFuncao->obEvento->setOnChange         ( "buscaValorFiltro('preencheEspecialidadeCargo');"         );

    $obTxtEspecialidade = new TextBox;
    $obTxtEspecialidade->setRotulo              ( "Especialidade"                                           );
    $obTxtEspecialidade->setName                ( "inCodEspecialidade"                                      );
    $obTxtEspecialidade->setValue               ( $inCodEspecialidade                                       );
    $obTxtEspecialidade->setTitle               ( "Selecione a especialidade para o filtro."                );
    $obTxtEspecialidade->setSize                ( 10                                                        );
    $obTxtEspecialidade->setMaxLength           ( 10                                                        );
    $obTxtEspecialidade->setInteiro             ( true                                                      );
    $obTxtEspecialidade->setNull                ( true                                                      );

    $obCmbEspecialidade = new Select;
    $obCmbEspecialidade->setName                ( "stEspecialidade"                                         );
    $obCmbEspecialidade->setValue               ( $inCodEspecialidade                                       );
    $obCmbEspecialidade->setRotulo              ( "Especialidade"                                           );
    $obCmbEspecialidade->setTitle               ( "Selecione a especialidade para o filtro."                );
    $obCmbEspecialidade->setNull                ( true                                                      );
    $obCmbEspecialidade->setCampoId             ( "[cod_especialidade]"                                     );
    $obCmbEspecialidade->setCampoDesc           ( "descricao_especialidade"                                 );
    $obCmbEspecialidade->addOption              ( "", "Selecione"                                           );
    $obCmbEspecialidade->setStyle               ( "width: 250px"                                            );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo                    ( "Função"                                                  );
    $obFormulario->addComponenteComposto        ( $obTxtRegime          ,$obCmbRegime                       );
    $obFormulario->addComponenteComposto        ( $obTxtSubDivisao      ,$obCmbSubDivisao                   );
    $obFormulario->addComponenteComposto        ( $obTxtFuncao          ,$obCmbFuncao                       );
    $obFormulario->addComponenteComposto        ( $obTxtEspecialidade   ,$obCmbEspecialidade                );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnFiltrar').innerHTML = '".$obFormulario->getHTML()."';    \n";
    $stJs .= "f.hdnFiltrar.value                       = '".$stEval."';                     \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan5($boExecuta=false)
{
    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->listarPadrao($rsPadrao);
    $obTxtPadrao = new TextBox;
    $obTxtPadrao->setRotulo                     ( "Padrão"                                          );
    $obTxtPadrao->setName                       ( "inCodPadrao"                                     );
    $obTxtPadrao->setValue                      ( $inCodPadrao                                      );
    $obTxtPadrao->setTitle                      ( "Selecione o padrão para o filtro."               );
    $obTxtPadrao->setSize                       ( 10                                                );
    $obTxtPadrao->setMaxLength                  ( 10                                                );
    $obTxtPadrao->setInteiro                    ( true                                              );
    $obTxtPadrao->setNull                       ( true                                              );

    $obCmbPadrao = new Select;
    $obCmbPadrao->setName                       ( "stPadrao"                                        );
    $obCmbPadrao->setValue                      ( $inCodPadrao                                      );
    $obCmbPadrao->setRotulo                     ( "Padrão"                                          );
    $obCmbPadrao->setTitle                      ( "Selecione o padrão para o filtro."               );
    $obCmbPadrao->setNull                       ( false                                             );
    $obCmbPadrao->setCampoId                    ( "[cod_padrao]"                                    );
    $obCmbPadrao->setCampoDesc                  ( "[descricao]"                                     );
    $obCmbPadrao->addOption                     ( "", "Selecione"                                   );
    $obCmbPadrao->preencheCombo                 ( $rsPadrao                                         );
    $obCmbPadrao->setStyle                      ( "width: 250px"                                    );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo                    ( "Padrão"                                          );
    $obFormulario->addComponenteComposto        ( $obTxtPadrao      ,$obCmbPadrao                   );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnFiltrar').innerHTML = '".$obFormulario->getHTML()."';    \n";
    $stJs .= "f.hdnFiltrar.value                       = '".$stEval."';                     \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan6($boExecuta=false)
{
    $obIBuscaInnerLotacao = new IBuscaInnerLotacao;
    $obIBuscaInnerLotacao->obBscLotacao->setTitle("Selecione a lotação para o filtro.");
    $obIBuscaInnerLotacao->obBscLotacao->setNull(false);

    $obFormulario = new Formulario;
    $obFormulario->addTitulo      ( "Lotação" );
    $obIBuscaInnerLotacao->geraFormulario ( $obFormulario );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnFiltrar').innerHTML = '".$obFormulario->getHTML()."';    \n";
    $stJs .= "f.hdnFiltrar.value                       = '".$stEval."';                     \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan7($boExecuta=false)
{
    $obBscLocal = new BuscaInner;
    $obBscLocal->setRotulo                      ( "Local"                               );
    $obBscLocal->setTitle                       ( "Selecione o local para o filtro."    );
    $obBscLocal->setNull                        ( false                                 );
    $obBscLocal->setId                          ( "stLocal"                             );
    $obBscLocal->obCampoCod->setName            ( "inCodLocal"                          );
    $obBscLocal->obCampoCod->setValue           ( $inCodLocal                           );
    $obBscLocal->obCampoCod->setSize            ( 10                                    );
    $obBscLocal->obCampoCod->obEvento->setOnBlur("buscaValorFiltro('buscaLocal');"      );
    $obBscLocal->setFuncaoBusca                 ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/LSProcurarLocal.php','frm','inCodLocal','stLocal','','".Sessao::getId()."','800','550')" );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo                    ( "Local"                                           );
    $obFormulario->addComponente                ( $obBscLocal                                       );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnFiltrar').innerHTML = '".$obFormulario->getHTML()."';    \n";
    $stJs .= "f.hdnFiltrar.value                       = '".$stEval."';                     \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function buscaCGM($boExecuta=false)
{
    $obRCGMPessoaFisica = new RCGMPessoaFisica;
    $obRPessoalServidor = new RPessoalServidor;
    $obRCGMPessoaFisica->setNumCGM( $_POST['inNumCGM'] );
    $obRCGMPessoaFisica->consultarCGM( $rsCGMPessoaFisica );
    $boErro = false;
    if ( $rsCGMPessoaFisica->getNumLinhas() <= 0 or $obRCGMPessoaFisica->getNumCGM() == 0  ) {
        $stJs .= "alertaAviso('@CGM ".$_POST['inNumCGM']." não encontrado.','form','erro','".Sessao::getId()."');";
        $stJs .= 'f.inNumCGM.value = "";';
        $stJs .= 'f.inCampoInner.focus();';
        $stJs .= 'd.getElementById("inCampoInner").innerHTML = "&nbsp;&nbsp;";';
        $boErro = true;
    }
    if ( $obRCGMPessoaFisica->getNumCGM() and !$boErro ) {
        $obRPessoalServidor->obRCGMPessoaFisica->setNumCGM( $_POST['inNumCGM'] );
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->consultaCGMServidor( $rsServidor, "", $boTransacao );
        if ( $rsServidor->getNumLinhas() <= 0 ) {
            $stJs .= "alertaAviso('@CGM ".$_POST['inNumCGM']." não cadastrado como servidor.','form','erro','".Sessao::getId()."');\n";
            $stJs .= "f.inNumCGM.value = '';\n";
            $stJs .= "f.inCampoInner.focus();\n";
            $stJs .= 'd.getElementById("inCampoInner").innerHTML = "&nbsp;&nbsp;";';
            $boErro = true;
        } else {
            $stJs .= 'd.getElementById("inCampoInner").innerHTML = "'.$rsCGMPessoaFisica->getCampo('nom_cgm').'";';
            $obRPessoalServidor->consultaRegistrosServidor( $rsRegistros, "", "", true );
            $stJs .= "limpaSelect(f.inContrato,0);\n";
            $stJs .= "f.inContrato[0] = new Option('Selecione','','selected');\n";
            $inIndex = 1;
            while ( !$rsRegistros->eof() ) {
                $stJs .= "f.inContrato[".$inIndex."] = new Option('".$rsRegistros->getCampo('registro')."','".$rsRegistros->getCampo('registro')."','');\n";
                $inIndex++;
                $rsRegistros->proximo();
            }
        }
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencheSubDivisaoCargo($boExecuta=false)
{
    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalRegime->addPessoalSubDivisao();
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->roPessoalRegime->setCodRegime( $_POST['inCodRegime'] );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->listarSubDivisao( $rsSubDivisao,"","",$boTransacao );
    $inContador = 1;
    //Limpa combo de sub-divisÃ£o
    $stJs .= "limpaSelect(f.stSubDivisao,0);                                \n";
    $stJs .= "f.stSubDivisao[0] = new Option('Selecione','','selected');    \n";
    $stJs .= "f.inCodSubDivisao.value = '';                                 \n";
    //Limpa combo de cargo
    $stJs .= "limpaSelect(f.stCargo,0);                                     \n";
    $stJs .= "f.stCargo[0] = new Option('Selecione','','selected');         \n";
    $stJs .= "f.inCodCargo.value = '';                                      \n";
    //Limpa combo de especialidade
    $stJs .= "limpaSelect(f.stEspecialidade,0);                             \n";
    $stJs .= "f.stEspecialidade[0] = new Option('Selecione','','selected'); \n";
    $stJs .= "f.inCodEspecialidade.value = '';                              \n";
    while ( !$rsSubDivisao->eof() ) {
        $stJs .= "f.stSubDivisao.options[$inContador] = new Option('".$rsSubDivisao->getCampo('nom_sub_divisao')."','".$rsSubDivisao->getCampo('cod_sub_divisao')."',''); \n";
        $inContador++;
        $rsSubDivisao->proximo();
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencheCargoCargo($boExecuta=false)
{
    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $_POST['inCodSubDivisao'] );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisao( $rsCargo );
    //Limpa combo de cargo
    $stJs .= "limpaSelect(f.stCargo,0);                                     \n";
    $stJs .= "f.stCargo[0] = new Option('Selecione','','selected');         \n";
    $stJs .= "f.inCodCargo.value = '';                                      \n";
    //Limpa combo de especialidade
    $stJs .= "limpaSelect(f.stEspecialidade,0);                             \n";
    $stJs .= "f.stEspecialidade[0] = new Option('Selecione','','selected'); \n";
    $stJs .= "f.inCodEspecialidade.value = '';                              \n";
    $inContador = 1;
    while ( !$rsCargo->eof() ) {
        $stJs .= "f.stCargo.options[$inContador] = new Option('".$rsCargo->getCampo('descricao')."','".$rsCargo->getCampo('cod_cargo')."',''); \n";
        $inContador++;
        $rsCargo->proximo();
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencheEspecialidadeCargo($boExecuta=false)
{
    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->setCodCargo( $_POST['inCodCargo'] );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->addEspecialidade();
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->listarEspecialidadesPorCargo( $rsEspecialidade );
    $stJs .= "limpaSelect(f.stEspecialidade,0);                                \n";
    $stJs .= "f.stEspecialidade[0] = new Option('Selecione','','selected');    \n";
    $stJs .= "f.inCodEspecialidade.value = '';                                 \n";
    $inContador = 1;
    while ( !$rsEspecialidade->eof() ) {
        $stJs .= "f.stEspecialidade.options[$inContador] = new Option('".$rsEspecialidade->getCampo('descricao')."','".$rsEspecialidade->getCampo('cod_especialidade')."',''); \n";
        $inContador++;
        $rsEspecialidade->proximo();
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function processarForm($boExecuta=false)
{
    $stJs .= "d.links['id_layer_1'].href = \"javascript:buscaValor('habilitaLayer_1');HabilitaLayer('layer_1');\";     \n";
    if ( Sessao::read('boBase') ) {
        $stJs .= "d.links['id_layer_2'].href = \"javascript:buscaValor('habilitaLayer_2');HabilitaLayer('layer_2');\"; \n";
    }
    $stJs .= gerarSpan1Form();
    $stJs .= gerarSpan2Form();
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function habilitaLayer($boExecuta=false)
{
    $arId = explode("_",$_POST["stCtrl"]);
    Sessao::write('numAba',$arId[1]);
    $stJs .= gerarSpan1Form();
    if ( Sessao::read('numAba') == 1 ) {
        $stJs .= gerarSpan2Form();
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan1Form($boExecuta=false)
{
    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
    $obRFolhaPagamentoConfiguracao->consultar($boTransacao);
    $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

    $stFixado           = ( isset($_GET['stFixado']) and $_GET['stFixado'] != "" ) ?  $_GET['stFixado'] : Sessao::read('stFixado') ;
    $stTipo             = ( isset($_GET['stTipo']) and $_GET['stTipo']   != "" ) ?  $_GET['stTipo']   : Sessao::read('stTipo');
    $nuValor            = ( isset($_GET['nuValor']) and $_GET['nuValor']  != "" ) ?  $_GET['nuValor']  : Sessao::read('nuValor');
    $boQuantidade       = true;
    $boLimiteCalculo    = ( isset($_GET['boLimiteCalculo']) and $_GET['boLimiteCalculo'] != "" )    ? $_GET['boLimiteCalculo'] : Sessao::read('boLimiteCalculo');
    $stDescricaoEvento  = ( isset($_GET['stDescricao']) and $_GET['stDescricao']     != "" )    ? "'".$_GET['stDescricao']."'"     : "'".Sessao::read('stDescricao')."'";
    $inCodigo        = ( isset($_GET['inCodigo']) and $_GET['inCodigo']     != "" )    ? $_GET['inCodigo']     : Sessao::read('inCodigo');
    $stTextoComplementar= ( isset($_GET['stTextoComplementar']) and $_GET['stTextoComplementar'] != "") ? $_GET['stTextoComplementar'] : Sessao::read('stTextoComplementar');
    if ($stFixado == 'Q') {
        $nuQuantidade = $nuValor;
        $boQuantidade = false;
    }
    switch ( Sessao::read('numAba') ) {
        case 2:
            $obLista = new Lista;
            $obLista->setTitulo("Bases de Evento");
            $obLista->setRecordSet( processarBase() );
            $obLista->setMostraPaginacao( false );

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Código");
            $obLista->ultimoCabecalho->setWidth( 10 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Evento");
            $obLista->ultimoCabecalho->setWidth( 20 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento("RIGHT");
            $obLista->ultimoDado->setCampo( "inCodigoBase" );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento("LEFT");
            $obLista->ultimoDado->setCampo( "stDescricao" );
            $obLista->commitDado();

            $obLista->montaHTML();
            $stSpan1 = $obLista->getHTML();
            $stSpan1 = str_replace("\n","",$stSpan1);
            $stSpan1 = str_replace("  ","",$stSpan1);
            $stSpan1 = str_replace("'","\\'",$stSpan1);

        break;
        default:
            $obTxtValor= new Moeda;
            $obTxtValor->setRotulo                      ( "Valor"                                                   );
            $obTxtValor->setTitle                       ( "Informe o valor do evento."                              );
            $obTxtValor->setName                        ( "nuValor"                                                 );
            $obTxtValor->setValue                       ( $nuValor                                                  );
            $obTxtValor->setMaxLength                   ( 18                                                        );
            $obTxtValor->setSize                        ( 18                                                        );
            $obTxtValor->setNull                        ( false                                                     );

            $obTxtQuantidade= new Moeda;
            $obTxtQuantidade->setRotulo                 ( "Quantidade"                                              );
            $obTxtQuantidade->setTitle                  ( "Informe a quantidade do evento."                         );
            $obTxtQuantidade->setName                   ( "nuQuantidade"                                            );
            $obTxtQuantidade->setValue                  ( $nuQuantidade                                             );
            $obTxtQuantidade->setMaxLength              ( 18                                                        );
            $obTxtQuantidade->setSize                   ( 18                                                        );
            $obTxtQuantidade->setNull                   ( $boQuantidade                                             );

            $obTxtQuantidadeParc= new TextBox;
            $obTxtQuantidadeParc->setRotulo             ( "Quantidade de Parcelas"                                  );
            $obTxtQuantidadeParc->setTitle              ( "Informe a quantidade de parcelas para o evento."         );
            $obTxtQuantidadeParc->setName               ( "inQuantidadeParc"                                        );
            $obTxtQuantidadeParc->setValue              ( $inQuantidadeParc                                         );
            $obTxtQuantidadeParc->setMaxLength          ( 10                                                        );
            $obTxtQuantidadeParc->setSize               ( 18                                                        );
            $obTxtQuantidadeParc->setNull               ( false                                                     );
            $obTxtQuantidadeParc->obEvento->setOnBlur   ( "buscaValor('preenchePrevisaoMesAno');"                   );

            $obLblMesAno= new Label;
            $obLblMesAno->setRotulo                     ( "Previsão Mês/Ano Limite"                                 );
            $obLblMesAno->setName                       ( "stMesAno"                                                );
            $obLblMesAno->setId                         ( "stMesAno"                                                );
            $obLblMesAno->setValue                      ( $stMesAno                                                 );

            $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento();
            $obRFolhaPagamentoEvento->addConfiguracaoEvento();
            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarConfiguracaoEvento($rsCaracteristicas);
            $obCmbCaracteristica = new Select;
            $obCmbCaracteristica->setName                   ( "inCodConfiguracao"                                       );
            $obCmbCaracteristica->setValue                  ( $inCodConfiguracao                                        );
            $obCmbCaracteristica->setRotulo                 ( "*Característica"                                         );
            $obCmbCaracteristica->setTitle                  ( "Selecione a característica do evento."                   );
            $obCmbCaracteristica->setCampoId                ( "cod_configuracao"                                        );
            $obCmbCaracteristica->setCampoDesc              ( "descricao"                                               );
            $obCmbCaracteristica->addOption                 ( "", "Selecione"                                           );
            $obCmbCaracteristica->preencheCombo             ( $rsCaracteristicas                                        );
            $obCmbCaracteristica->setStyle                  ( "width: 250px"                                            );
            $obCmbCaracteristica->obEvento->setOnChange     ( "buscaValor('validaEvento');"                             );

            $obBtnIncluir = new Button;
            $obBtnIncluir->setName                          ( "btnIncluir"                                              );
            $obBtnIncluir->setValue                         ( "Incluir"                                                 );
            $obBtnIncluir->setTipo                          ( "button"                                                  );
            $obBtnIncluir->obEvento->setOnClick             ( "buscaValor('incluirEvento');"                            );

            $obBtnAlterar = new Button;
            $obBtnAlterar->setName                          ( "btnAlterar"                                              );
            $obBtnAlterar->setValue                         ( "Alterar"                                                 );
            $obBtnAlterar->setTipo                          ( "button"                                                  );
            $obBtnAlterar->obEvento->setOnClick             ( "buscaValor('alterarEvento');"                            );

            $obBtnLimpar = new Button;
            $obBtnLimpar->setName                           ( "btnLimpar"                                               );
            $obBtnLimpar->setValue                          ( "Limpar"                                                  );
            $obBtnLimpar->setTipo                           ( "button"                                                  );
            $obBtnLimpar->obEvento->setOnClick              ( "buscaValor('limparEvento');"                             );

            $obSpnSpan2 = new Span;
            $obSpnSpan2->setId                              ( "spnSpan2"                                                );

            $obFormulario = new Formulario;
            if ( isset($stTipo) and $stTipo != "" ) {
                if ($stFixado == 'V') {
                    $obFormulario->addComponente        ( $obTxtValor                                               );
                }
                $obFormulario->addComponente            ( $obTxtQuantidade                                          );
                if ($stTipo == 'V') {
                    if ($boLimiteCalculo == 't') {
                        $obFormulario->addComponente    ( $obTxtQuantidadeParc                                      );
                        $obFormulario->addComponente    ( $obLblMesAno                                              );
                    }
                }
            }
            $obFormulario->addComponente                ( $obCmbCaracteristica                                      );
            $obFormulario->agrupaComponentes            ( array($obBtnIncluir,$obBtnAlterar,$obBtnLimpar),"",""     );
            $obFormulario->addSpan                      ( $obSpnSpan2                                               );
            $obFormulario->obJavaScript->montaJavaScript();
            $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
            $stEval = str_replace("\n","",$stEval);
            $obFormulario->montaInnerHtml();
            $stSpan1 = $obFormulario->getHTML();
        break;
    }
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stSpan1."';  \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan2Form($boExecuta=false)
{
    $inNumAba = Sessao::read('numAba');
    $rsEventos = new recordset;
    $obLista = new Lista;
    $obLista->setTitulo( "Eventos Cadastrados" );
    if ( is_array(Sessao::read('eventos')) ) {
        $rsEventos->preenche(Sessao::read('eventos'));
        $rsEventos->ordena('inCodigo');
    }

    $arEventos = $rsEventos->getElementos();
    $arTemp = array();
    foreach ($arEventos as $arEvento) {
        if ($arEvento['inQuantidadeParc'] != "") {
            $arEvento['nuQuantidade'] = (int) $arEvento['nuQuantidade']."/".$arEvento['inQuantidadeParc'];
        }
        $arTemp[] = $arEvento;
    }

    $rsEventos->preenche($arTemp);

    $obLista->setRecordSet( $rsEventos );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Evento");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Configuração");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[inCodigo] - [stDescricao]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "nuValor" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "nuQuantidade" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "stConfiguracao" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:alteraDado('montaAlterarEvento');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:alteraDado('excluirEvento');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('spnSpan2').innerHTML = '".$stHtml."';   \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function processarBase()
{
    $arEventosBase = ( is_array(Sessao::read('eventosBase')) ) ? Sessao::read('eventosBase') : array();
    $arEventosTemp = array();
    foreach ($arEventosBase as $arEventoBase) {
        $boErro = false;
        foreach ($arEventosTemp as $arEventoBase2) {
            if ($arEventoBase['inCodigoBase'] == $arEventoBase2['inCodigoBase']) {
                $boErro = true;
                break;
            }
        }
        if ($boErro == false) {
            $arEventosTemp[] = $arEventoBase;
        }
    }
    $rsBase = new recordset;
    $rsBase->preenche($arEventosTemp);
    $rsBase->ordena('inCodigoBase');

    return $rsBase;
}

function validaEvento($boExecuta=false,&$rsEvento)
{
    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao(Sessao::read('inCodSubDivisao'));
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->setCodCargo( Sessao::read('inCodFuncao') );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->addEspecialidade();
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade( Sessao::read('inCodEspecialidade') );
    $obRFolhaPagamentoPeriodoContratoServidor->addRFolhaPagamentoRegistroEvento();
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setCodigo( str_pad($_POST['inCodigo'],5,"0",STR_PAD_LEFT) );
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->listarEvento( $rsEventoEvento );
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->addConfiguracaoEvento();
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao($_POST['inCodConfiguracao']);
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->listarEventosConfiguracao( $rsEvento );
    if ( $rsEvento->getNumLinhas() > 0 ) {
        $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setCodEvento($rsEvento->getCampo('cod_evento'));
        $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setTimestamp($rsEvento->getCampo('timestamp'));
        $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->addConfiguracaoEvento();
        $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->addCasoEvento();
        $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->setCodCaso( $rsEvento->getCampo('cod_caso') );
        $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao( $rsEvento->getCampo('cod_configuracao') );
        $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->listarEventosBasePorCaso($rsEventosBase);
        Sessao::write('rsEventosBase',$rsEventosBase);
    }
    if ( $rsEventoEvento->getNumLinhas() < 0 ) {
        $stJs .= "alertaAviso('@O evento informado não existe.','form','erro','".Sessao::getId()."');      \n";;
        $stJs .= limparEvento();
    } elseif ($rsEvento->getNumLinhas() < 0 ) {
        $stJs .= "alertaAviso('@O evento informado não possui configuração para a subdivisão/cargo e/ou especialidade do contrato em manutenção.','form','erro','".Sessao::getId()."');      \n";;
        $stJs .= limparEvento();
    } elseif ( $rsEvento->getCampo('tipo') == 'B' ) {
        $stJs .= "alertaAviso('@O evento informado não é do tipo válido.','form','erro','".Sessao::getId()."');      \n";;
        $stJs .= limparEvento();
    }/*else{
        $stJs .= "f.btnIncluir.disabled = false;    \n";
    }*/
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }

}

function buscaEvento($boExecuta=false)
{
    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoPeriodoContratoServidor->addRFolhaPagamentoRegistroEvento();
    $stJs .= validaEvento(false,$rsEvento);
    if ( $rsEvento->getNumLinhas() > 0 ) {
        Sessao::write('stProcesso',"inclusao");
        Sessao::write('stTipo',$rsEvento->getCampo('tipo'));
        Sessao::write('stFixado',$rsEvento->getCampo('fixado'));
        Sessao::write('nuValor',number_format($rsEvento->getCampo('valor_quantidade'),2,',','.'));
        Sessao::write('stDescricaoEvento',$rsEvento->getCampo('descricao'));
        Sessao::write('stProventosDescontos',$rsEvento->getCampo('proventos_descontos'));
        Sessao::write('boLimiteCalculo',$rsEvento->getCampo('limite_calculo'));
        $stJs .= gerarSpan1Form();
        $stJs .= gerarSpan2Form();
        $stJs .= "d.getElementById('inCampoInner').innerHTML = '".$rsEvento->getCampo('descricao')."';          \n";
        $stJs .= "f.inCampoInner.value = '".$rsEvento->getCampo('descricao')."';                                \n";
        $stJs .= "f.inCodigo.value = '".$rsEvento->getCampo('codigo')."';                                       \n";
        $stTextoComplementar = ( trim($rsEvento->getCampo('observacao')) != "" ) ? $rsEvento->getCampo('observacao') : "&nbsp;";
        $stJs .= "d.getElementById('stTextoComplementar').innerHTML = '".$stTextoComplementar."';  \n";
        $stJs .= "f.hdnTextoComplementar.value = '".$rsEvento->getCampo('observacao')."';                       \n";
        $stJs .= "f.inCodConfiguracao.options[1].selected = true; \n";
        $stJs .= "f.btnAlterar.disabled = true;                                                                 \n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}
function preenchePrevisaoMesAno($boExecuta=false)
{
    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);
    $inQuantidadeParc   = ( $_POST['inQuantidadeParc'] != "" ) ? $_POST['inQuantidadeParc'] : Sessao::read('inQuantidadeParc');
    $arDataFinal        = explode("/",$rsUltimaMovimentacao->getCampo('dt_final'));
    $inResto            = (($inQuantidadeParc)%12);
    $inInt              = intval((($inQuantidadeParc)/12));
    if ($inResto) {
        $inAno = $arDataFinal[2] + $inInt;
    } else {
        $inAno = $arDataFinal[2] + $inInt-1;
    }
    $inMes = ( $inResto == 0 ) ? 12 : $inResto;
    $inMes = ( strlen($inMes) == 1 ) ? '0'.$inMes : $inMes;
    $stMesAno = $inMes ."/". $inAno;
    $stJs .= "d.getElementById('stMesAno').innerHTML = '".$stMesAno."'  \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function incluirEvento($boExecuta=false)
{
    $inNumAba = Sessao::read('numAba');
    $obErro = new erro;
    $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
    $boLimiteCalculo    = ( isset($_GET['boLimiteCalculo']) and $_GET['boLimiteCalculo'] != "" )    ? $_GET['boLimiteCalculo'] : Sessao::read('boLimiteCalculo');
    if ( Sessao::read('stProcesso') != 'inclusao' and Sessao::read('stProcesso') != "" ) {
        $obErro->setDescricao("Alteração em processo clique em alterar para confirmar ou limpar para cancelar.");
    }
    if ( !$obErro->ocorreu() and $_POST['inCodigo'] == "" ) {
        $obErro->setDescricao("Campo Evento inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['inCodConfiguracao'] == "" ) {
        $obErro->setDescricao("Campo Característica inválido!()");
    }
    if ( !$obErro->ocorreu() ) {
        $rsRecordSet = new Recordset;
        switch ($inNumAba) {
            case 1:
                $rsRecordSet->preenche( Sessao::read('eventos') );
            break;
        }
        while ( !$rsRecordSet->eof() ) {
            if ( $rsRecordSet->getCampo('inCodigo') == $_POST['inCodigo'] and $rsRecordSet->getCampo('inCodConfiguracao') == $_POST['inCodConfiguracao'] ) {
                $obErro->setDescricao("Evento já incluído na lista.");
                break;
            }
            $rsRecordSet->proximo();
        }
    }
    $stFixado = ( $_GET['stFixado'] != "" ) ? $_GET['stFixado'] : Sessao::read('stFixado');
    if ( !$obErro->ocorreu() and $stFixado == 'V' and $_POST['nuValor'] == "" ) {
        $obErro->setDescricao("Campo Valor inválido!()");
    }
    //if ( !$obErro->ocorreu() and $inNumAba == 3 and $boLimiteCalculo == 't' and $stFixado == 'Q' and $_POST['inQuantidadeParc'] == "" ) {
    if ( !$obErro->ocorreu() and $inNumAba == 3 and isset($_POST['inQuantidadeParc']) and $_POST['inQuantidadeParc'] == "" ) {
        $obErro->setDescricao("Campo Quantidade de Parcelas inválido!()");
    }
    $nuValor      = number_format((float) SistemaLegado::intToDecimal(str_replace(".","",str_replace(",","", $_POST['nuValor']     )),2),2,',','.');
    $nuQuantidade = number_format((float) SistemaLegado::intToDecimal(str_replace(".","",str_replace(",","", $_POST['nuQuantidade'])),2),2,',','.');

    if ( !$obErro->ocorreu() ) {
        $rsRecordSet->setUltimoElemento();
        $inUltimoId = $rsRecordSet->getCampo("inId");
        if (!$inUltimoId) {
            $inProxId = 1;
        } else {
            $inProxId = $inUltimoId + 1;
        }
        $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento();
        $obRFolhaPagamentoEvento->addConfiguracaoEvento();
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao( $_POST['inCodConfiguracao'] );
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarConfiguracaoEvento($rsCaracteristicas);
        $stConfiguracao = ( $rsCaracteristicas->getNumLinhas() == 1 ) ? trim($rsCaracteristicas->getCampo('descricao')) : "";

        $arElementos = array();
        $arElementos['inId']                = $inProxId;
        $arElementos['inCodigo']            = $_POST['inCodigo'];
        $arElementos['stDescricao']         = trim($_POST['inCampoInner']);
        $arElementos['nuValor']             = ($nuValor != "")?$nuValor:'0,00';
        $arElementos['nuQuantidade']        = ($nuQuantidade != "")?$nuQuantidade:'0,00';
        $arElementos['stTextoComplementar'] = $_POST['hdnTextoComplementar'];
        $arElementos['inCodConfiguracao']   = $_POST['inCodConfiguracao'];
        $arElementos['stConfiguracao']      = $stConfiguracao;
        $arElementos['stTipo']              = Sessao::read('stTipo');
        $arElementos['stFixado']            = Sessao::read('stFixado');
        $arElementos['boLimiteCalculo']     = Sessao::read('boLimiteCalculo');
        $arElementos['stProventosDescontos']= Sessao::read('stProventosDescontos');
        $arElementos['inQuantidadeParc']          = $_POST['inQuantidadeParc'];

        //Processamento dos eventos base vinculados ao evento selecionado e inserido no array arElementos
        //O evento base estï¿½ vinculado ao evento correspondente ao valor da variï¿½vel inCodigo

        $rsEventosBasePorCaso = Sessao::read('rsEventosBase');
        if ( $rsEventosBasePorCaso->getNumLinhas() ) {
            $inIdBase = count(Sessao::read('eventosBase'));
            $arEventosBase = Sessao::read('eventosBase');
            while (!$rsEventosBasePorCaso->eof()) {
                $arElementosBase = array();
                $arElementosBase['inId']                = $inIdBase;
                $arElementosBase['inCodigo']            = $_POST['inCodigo'];
                $arElementosBase['inCodigoBase']        = $rsEventosBasePorCaso->getCampo('codigo');
                $arElementosBase['stDescricao']         = $rsEventosBasePorCaso->getCampo('descricao');
                $arElementosBase['nuValor']             = '';
                $arElementosBase['nuQuantidade']        = '';
                $arElementosBase['stTextoComplementar'] = '';
                $arElementosBase['stTipo']              = $rsEventosBasePorCaso->getCampo('tipo');
                $arElementosBase['stFixado']            = $rsEventosBasePorCaso->getCampo('fixado');
                $arElementosBase['boLimiteCalculo']     = $rsEventosBasePorCaso->getCampo('limite_calculo');
                $arElementosBase['stProventosDescontos']= '';
                $arElementosBase['inCodConfiguracao']   = $_POST['inCodConfiguracao'];
                $arElementosBase['stConfiguracao']      = $stConfiguracao;
                $arEventosBase[]        = $arElementosBase;
                $inIdBase++;
                $rsEventosBasePorCaso->proximo();
            }
            Sessao::write("eventosBase",$arEventosBase);
        }

        if ( $inNumAba == 3 or ($inNumAba == 2 and Sessao::read('stTipo') == 'V') ) {
            $arElementos['inQuantidadeParc']= $_POST['inQuantidadeParc'];
        }
        switch ($inNumAba) {
            case 1:
                $arEventos = Sessao::read("eventos");
                $arEventos[]   = $arElementos;
                Sessao::write("eventos",$arEventos);
            break;
        }
        $stJs .= gerarSpan2Form();
        $stJs .= limparEvento();
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function alterarEvento($boExecuta=false)
{
    $inNumAba = Sessao::read('numAba');
    $obErro = new erro;
    if ($_POST['inCodigo'] == "") {
        $obErro->setDescricao("Campo Evento inválido!()");
    }
    if ( !$obErro->ocorreu() ) {
        $rsRecordSet = new Recordset;
        switch ($inNumAba) {
            case 1:
                $rsRecordSet->preenche( Sessao::read('eventos') );
                $arEventos = Sessao::read('eventos');
            break;
        }
        while ( !$rsRecordSet->eof() ) {
            if ( $rsRecordSet->getCampo('inId') != Sessao::read('inId') and  $rsRecordSet->getCampo('inCodigo') == $_POST['inCodigo'] ) {
                $obErro->setDescricao("Evento já incluído na lista.");
                break;
            }
            $rsRecordSet->proximo();
        }
    }
    if ( !$obErro->ocorreu() and Sessao::read('stFixado') == 'V' and $_POST['nuValor'] == "" ) {
        $obErro->setDescricao("Campo Valor inválido!()");
    }
    if ( !$obErro->ocorreu() and $inNumAba == 3 and isset($_POST['inQuantidadeParc']) and $_POST['inQuantidadeParc'] == "" ) {
        $obErro->setDescricao("Campo Quantidade de Parcelas inválido!()");
    }
    if ( !$obErro->ocorreu() ) {
        $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento();
        $obRFolhaPagamentoEvento->addConfiguracaoEvento();
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao( $_POST['inCodConfiguracao'] );
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarConfiguracaoEvento($rsCaracteristicas);
        $stConfiguracao = ( $rsCaracteristicas->getNumLinhas() == 1 ) ? trim($rsCaracteristicas->getCampo('descricao')) : "";

        $nuValor      = number_format((float) SistemaLegado::intToDecimal(str_replace(".","",str_replace(",","", $_POST['nuValor'])),2),2,',','.');
        $nuQuantidade = number_format((float) SistemaLegado::intToDecimal(str_replace(".","",str_replace(",","", $_POST['nuQuantidade'])),2),2,',','.');

        $arElementos = array();
        $arElementos['inId']                = Sessao::read('inId');
        $arElementos['inCodigo']            = $_POST['inCodigo'];
        $arElementos['stDescricao']         = $_POST['inCampoInner'];
        $arElementos['nuValor']             = ($nuValor != "")?$nuValor:'0,00';
        $arElementos['nuQuantidade']        = ($nuQuantidade!="")?$nuQuantidade:'0,00';
        $arElementos['stTextoComplementar'] = $_POST['hdnTextoComplementar'];
        $arElementos['inCodConfiguracao']   = $_POST['inCodConfiguracao'];
        $arElementos['stConfiguracao']      = $stConfiguracao;
        $arElementos['stTipo']              = Sessao::read('stTipo');
        $arElementos['stFixado']            = Sessao::read('stFixado');
        $arElementos['boLimiteCalculo']     = Sessao::read('boLimiteCalculo');
        $arElementos['stProventosDescontos']= Sessao::read('stProventosDescontos');
        $arElementos['inQuantidadeParc']          = $_POST['inQuantidadeParc'];
        $arTemp      = array();
        foreach ($arEventos as $arEvento) {
            if ($arEvento['inId'] != $arElementos['inId']) {
                $arTemp[] = $arEvento;
            } else {
                $arElementos['boAutomatico']  = $arEvento['boAutomatico'];
                $arElementos['inCodRegistro'] = $arEvento['inCodRegistro'];
                $arElementos['stTimestamp'] = $arEvento['stTimestamp'];
                $arTemp[] = $arElementos;
            }
        }
        switch ($inNumAba) {
            case 1:
                Sessao::write('eventos',$arTemp);
            break;
        }
        $stJs .= gerarSpan2Form();
        $stJs .= limparEvento();
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function montaAlterarEvento($boExecuta=false)
{
    Sessao::write('stProcesso','alteracao');
    $inNumAba = Sessao::read('numAba');
    $inId     = $_GET['inId'];
    $rsEventos = new recordset;
    switch ($inNumAba) {
        case 1:
            $rsEventos->preenche( Sessao::read('eventos') );
        break;
    }
    while ( !$rsEventos->eof() ) {
        if ( $rsEventos->getCampo('inId') == $inId ) {
            Sessao::write('stDescricaoEvento',$rsEventos->getCampo('stDescricao'));
            Sessao::write('stTipo',$rsEventos->getCampo('stTipo'));
            Sessao::write('stFixado',$rsEventos->getCampo('stFixado'));
            Sessao::write('nuValor',$rsEventos->getCampo('nuValor'));
            Sessao::write('boLimiteCalculo',$rsEventos->getCampo('boLimiteCalculo'));
            Sessao::write('inId',$rsEventos->getCampo('inId'));
            $nuQuantidade                        = $rsEventos->getCampo('nuQuantidade');
        if ($inNumAba != 3 or $inNumAba != 2) {
                $inQuantidadeParc                = $rsEventos->getCampo('inQuantidadeParc');
                Sessao::write('inQuantidadeParc',$rsEventos->getCampo('inQuantidadeParc'));
            }
            $stJs .= gerarSpan1Form();
            $stJs .= gerarSpan2Form();
            //$stJs .= bloqueiaAbasForm();
            $stJs .= "f.inCodigo.value = '".$rsEventos->getCampo('inCodigo')."';                                          \n";
            $stJs .= "d.getElementById('inCampoInner').innerHTML = '".$rsEventos->getCampo('stDescricao')."';                   \n";
            $stJs .= "f.inCampoInner.value = '".$rsEventos->getCampo('stDescricao')."';                                         \n";
            $stTextoComplementar = ( $rsEventos->getCampo('stTextoComplementar') != "" ) ? $rsEventos->getCampo('stTextoComplementar') : "&nbsp;";
            $stJs .= "d.getElementById('stTextoComplementar').innerHTML = '".$stTextoComplementar."';    \n";
            $stJs .= "f.hdnTextoComplementar.value = '".$rsEventos->getCampo('stTextoComplementar')."';                         \n";
            $stJs .= "limpaSelect(f.inCodConfiguracao,0); \n";
            $stJs .= "f.inCodConfiguracao.options[0] = new Option('Selecione','', '');\n";
            $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento();
            $obRFolhaPagamentoEvento->addConfiguracaoEvento();
            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarConfiguracaoEvento($rsCaracteristicas);
            $inIndex = 1;
            while ( !$rsCaracteristicas->eof() ) {
                $stSelected = "";
                if ( $rsCaracteristicas->getCampo('cod_configuracao') == $rsEventos->getCampo('inCodConfiguracao') ) {
                    $stSelected = " selected ";
                }
                $stJs .= "f.inCodConfiguracao.options[".$inIndex."] = new Option('".$rsCaracteristicas->getCampo('descricao')."','".$rsCaracteristicas->getCampo('cod_configuracao')."', '".$stSelected."');\n";
                $inIndex++;
                $rsCaracteristicas->proximo();
            }
            $stJs .= "f.inCodConfiguracao.value = '".$rsEventos->getCampo('inCodConfiguracao')."'\n";
        }
        $rsEventos->proximo();
    }
    $stJs .= "f.nuQuantidade.value = '".$nuQuantidade."';               \n";
    if ( $inNumAba != 3 or ( $inNumAba != 2 and  Sessao::read('stTipo') != 'V' )) {
        $stJs .= "f.inQuantidadeParc.value = '".$inQuantidadeParc."';   \n";
        $stJs .= preenchePrevisaoMesAno();
    }
    $stJs .= "f.btnAlterar.disabled = false;\n";
    $stJs .= "f.btnIncluir.disabled = true;\n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function excluirEvento($boExecuta=false)
{
    $obErro = new erro;
    if ( Sessao::read('stProcesso') == "inclusao" ) {
        $obErro->setDescricao('Inclusão em processo clique em incluir para confirmar ou limpar para cancelar.');
    }
    if ( !$obErro->ocorreu() and Sessao::read('stProcesso') == "alteracao" ) {
        $obErro->setDescricao('Alteração em processo clique em alterar para confirmar ou limpar para cancelar.');
    }
    if ( !$obErro->ocorreu() ) {
        $inNumAba = Sessao::read('numAba');
        $arTemp   = array();
        switch ($inNumAba) {
            case 1:
                $arEventosTemp = Sessao::read('eventos') ;
            break;
        }
        foreach ($arEventosTemp as $arEvento) {
            if ($arEvento['inId'] != $_GET['inId']) {
                $arTemp[] = $arEvento;
            } else {
                $inCodigo = $arEvento['inCodigo'];
            }
        }
        $arEventosBase = Sessao::read('eventosBase');
        $arTempBase    = array();
        if (is_array($arEventosBase) && count($arEventosBase)>0) {
            foreach ($arEventosBase as $arEventoBase) {
                if ($arEventoBase['inCodigo'] != $inCodigo) {
                    $arTempBase[] = $arEventoBase;
                }
            }
        }
        Sessao::write('eventosBase',$arTempBase);
        switch ($inNumAba) {
            case 1:
                Sessao::write('eventos',$arTemp);
            break;
        }
        $stJs .= gerarSpan2Form();
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function limparEvento($boExecuta=false)
{
    $inNumAba = Sessao::read('numAba');
    Sessao::write('stProcesso',"");
    Sessao::remove('stTipo');
    Sessao::remove('stDescricaoEvento');
    Sessao::remove('inCodigo');
    Sessao::remove('stTextoComplementar');
    Sessao::remove('stFixado');
    Sessao::remove('nuValor');
    Sessao::remove('boLimiteCalculo');
    Sessao::remove('stProventosDescontos');
    $stJs .= "f.inCodigo.value = '';                                                                             \n";
    $stJs .= "d.getElementById('inCampoInner').innerHTML = '&nbsp;';                                                \n";
    $stJs .= "d.getElementById('stTextoComplementar').innerHTML = '&nbsp;';                                         \n";
    $stFixado = ( $_GET['stFixado'] != "" ) ? $_GET['stFixado'] : Sessao::read('stFixado');
    if ($stFixado == 'V') {
        $stJs .= "f.nuValor.value = '';                                                                             \n";
    }
    $stJs .= "f.nuQuantidade.value = '';                                                                            \n";
    $stJs .= processarForm();
    $stJs .= "d.links['id_layer_1'].href = \"javascript:buscaValor('habilitaLayer_1');HabilitaLayer('layer_1');\";  \n";
    if ( Sessao::read('boBase') ) {
        $stJs .= "d.links['id_layer_2'].href = \"javascript:buscaValor('habilitaLayer_2');HabilitaLayer('layer_2');\";  \n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

switch ($_POST["stCtrl"]) {
    case "habilitaSpanFiltro":
        $stJs.= habilitaSpanFiltro();
    break;
    case "buscaCGM":
        $stJs.= buscaCGM();
    break;
    case "buscaLotacao":
        $stJs.= buscaLotacao();
    break;
    case "preencheSubDivisaoCargo":
        $stJs.= preencheSubDivisaoCargo();
    break;
    case "preencheCargoCargo":
        $stJs.= preencheCargoCargo();
    break;
    case "preencheEspecialidadeCargo":
        $stJs.= preencheEspecialidadeCargo();
    break;
    case "incluirEvento":
        $stJs.= incluirEvento();
    break;
    case "alterarEvento":
        $stJs.= alterarEvento();
    break;
    case "excluirEvento":
        $stJs.= excluirEvento();
    break;
    case "limparEvento":
        $stJs.= limparEvento();
    break;
    case "processarForm":
        $stJs.= processarForm();
    break;
    case "habilitaLayer_1":
    case "habilitaLayer_2":
        $stJs.= habilitaLayer();
    break;
    case "alertaAviso":
        $stJs.= alertaAviso();
    break;
    case "preenchePrevisaoMesAno":
        $stJs.= preenchePrevisaoMesAno();
    break;
    case "buscaEvento":
        $stJs.= buscaEvento();
    break;
    case "limparForm":
        $stJs .= habilitaSpanFiltro();
        $stJs .= "f.inFiltrar.options[".($_REQUEST["inFiltrar"]+1)."].selected = true;\n";
    break;
    case "montaAlterarEvento":
        $stJs.= montaAlterarEvento();
    break;
    case "validaEvento":
        $stJs.= validaEvento(false,new recordset);
    break;

}
if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>

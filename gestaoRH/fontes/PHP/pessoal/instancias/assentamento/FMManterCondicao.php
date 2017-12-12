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
* Formulário de definição de condição do assentamento
* Data de Criação: 04/08/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage

$Revision: 30860 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

* Casos de uso: uc-04.04.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamentoVinculado.class.php"      );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCondicaoAssentamento.class.php"       );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamento.class.php"               );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalVantagem.class.php"                   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCondicao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stAcao = $request->get('stAcao');
Sessao::write('assentamentoVinculado', array());
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$stLink .= '&inCodClassificacaoTxt='.$_REQUEST['inCodClassificacao'];
$stLink .= "&stAcao=".$stAcao;

$obRPessoalVantagem               = new RPessoalVantagem;
$obRPessoalAssentamento1          = new RPessoalAssentamento($obRPessoalVantagem);
$obRPessoalAssentamento2          = new RPessoalAssentamento($obRPessoalVantagem);
$obRPessoalCondicaoAssentamento   = new RPessoalCondicaoAssentamento();
$obRPessoalAssentamentoVinculado  = new RPessoalAssentamentoVinculado( $obRPessoalAssentamento1,
$obRPessoalAssentamento2,$obRPessoalCondicaoAssentamento );
$rsClassificacao = $rsAssentamento = $rsFuncao = new Recordset;

$obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->obRPessoalClassificacaoAssentamento->listarClassificacao( $rsClassificacao );
$obRPessoalAssentamentoVinculado->obRFuncao->listar( $rsFuncao );

if ($stAcao == 'alterar') {
    $rsAssentamentoVinculado = new Recordset;
    $obRPessoalAssentamentoVinculado->roRPessoalCondicaoAssentamento->setCodCondicao( $_REQUEST['inCodCondicao'] );
    $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->setCodAssentamento( $_REQUEST['inCodAssentamento'] );
    $obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->getTimestamp( $_REQUEST['stTimestampAssentamento'] );
    $obRPessoalAssentamentoVinculado->obRPessoalAssentamento2->getTimestamp( $_REQUEST['stTimestamp'] );
    $obRPessoalAssentamentoVinculado->listarAssentamentoVinculado( $rsAssentamentoVinculado );

    $inCodCondicao           = $_REQUEST['inCodCondicao'];
    $stSigla                 = $_REQUEST['stSigla'];
    $inCodClassificacaoTxt   = $_REQUEST['inCodClassificacao'];
    $inCodAssentamento       = $_REQUEST['inCodAssentamento'];
    $inUltimoId              = 1;
    $arAssentamentoVinculado = array();

    while ( !$rsAssentamentoVinculado->eof() ) {
        $arElementos['inId']                            = $inUltimoId;
        $arElementos['boCondicao']                      = $rsAssentamentoVinculado->getCampo('condicao');
        $arElementos['boDia']                           = $rsAssentamentoVinculado->getCampo('meio_dia');
        $arElementos['boContagemDias']                  = $rsAssentamentoVinculado->getCampo('contagem');
        $arElementos['inCodClassificacaoVinculacaoTxt'] = $rsAssentamentoVinculado->getCampo('cod_classificacao');
        $arElementos['inNomClassificacaoVinculacaoTxt'] = $rsAssentamentoVinculado->getCampo('descricao');
        $arElementos['inCodAssentamentoVinculacao']     = $rsAssentamentoVinculado->getCampo('sigla');
        $arElementos['stSiglaVinculado']                = $rsAssentamentoVinculado->getCampo('sigla');

        if ( $rsAssentamentoVinculado->getCampo('condicao') == 'a' ) {
            $stCondicao = "Averbados";
        } else {
            $stCondicao = "Protelados";
        }

        if ( $rsAssentamentoVinculado->getCampo('meio_dia') == false ) {
            $arElementos['inDiasIncidencia']            = $rsAssentamentoVinculado->getCampo('dias_incidencia');
        } else {
            $arElementos['inDiasIncidencia']            = '1/2';
        }

        $arElementos['inDiasVinculado']                 = $rsAssentamentoVinculado->getCampo('dias_protelar_averbar');
        $arElementos['stCondicao']                      = $stCondicao;
        $arElementos['inCodFuncaoTxt']                  = $rsAssentamentoVinculado->getCampo('cod_modulo').'.';
        $arElementos['inCodFuncaoTxt']                 .= $rsAssentamentoVinculado->getCampo('cod_biblioteca').'.';
        $arElementos['inCodFuncaoTxt']                 .= $rsAssentamentoVinculado->getCampo('cod_funcao');

        $arElementos['inCodCondicao']                   = $rsAssentamentoVinculado->getCampo('cod_condicao');
        $arElementos['stTimestamp']                     = $rsAssentamentoVinculado->getCampo('timestamp');
        $arElementos['inCodAssentamento']               = $rsAssentamentoVinculado->getCampo('cod_assentamento');
        $arElementos['stTimestampAssentamento']         = $rsAssentamentoVinculado->getCampo('timestamp_assentamento');
        $arElementos['inCodAssentamentoVinculado']      = $rsAssentamentoVinculado->getCampo('cod_assentamento_vinculado');
        $arElementos['stTimestampAssentamentoVinculado']= $rsAssentamentoVinculado->getCampo('timestamp_assentamento_vinculado');

        $arAssentamentoVinculado[] = $arElementos;
        $inUltimoId++;
        $rsAssentamentoVinculado->proximo();
    }

    Sessao::write('assentamentoVinculado', $arAssentamentoVinculado);
    $stJS  = "buscaValor('alteracao');";
    sistemaLegado::executaFrameOculto( $stJS );
}

$obTxtClassificacao = new TextBox;
$obTxtClassificacao->setRotulo              ( "Classificação"                           );
$obTxtClassificacao->setTitle               ( "Informe a classificação do assentamento." );
$obTxtClassificacao->setName                ( "inCodClassificacaoTxt"                   );
$obTxtClassificacao->setId                  ( "inCodClassificacaoTxt"                   );
$obTxtClassificacao->setValue               ( $inCodClassificacaoTxt                    );
$obTxtClassificacao->setSize                ( 10                                        );
$obTxtClassificacao->setMaxLength           ( 6                                         );
$obTxtClassificacao->setInteiro             ( true                                      );
$obTxtClassificacao->setNull                ( false                                     );
$obTxtClassificacao->obEvento->setOnChange  ( "buscaValor('preencheAssentamento1');"    );

$obCmbClassificacao = new Select;
$obCmbClassificacao->setRotulo              ( "Classificação"                           );
$obCmbClassificacao->setName                ( "inCodClassificacao"                      );
$obCmbClassificacao->setValue               ( $inCodClassificacaoTxt                    );
$obCmbClassificacao->setStyle               ( "width: 200px"                            );
$obCmbClassificacao->setCampoID             ( "cod_classificacao"                       );
$obCmbClassificacao->setCampoDesc           ( "descricao"                               );
$obCmbClassificacao->addOption              ( "", "Selecione"                           );
$obCmbClassificacao->setNull                ( false                                     );
$obCmbClassificacao->preencheCombo          ( $rsClassificacao                          );
$obCmbClassificacao->obEvento->setOnChange  ( "buscaValor('preencheAssentamento1');"    );

$obTxtAssentamento = new TextBox;
$obTxtAssentamento->setRotulo               ( "Assentamento"                            );
$obTxtAssentamento->setTitle                ( "Informe o assentamento."                  );
$obTxtAssentamento->setName                 ( "stSigla"                                 );
$obTxtAssentamento->setValue                ( $stSigla                                  );
$obTxtAssentamento->setSize                 ( 10                                        );
$obTxtAssentamento->setMaxLength            ( 10                                        );
$obTxtAssentamento->setNull                 ( false                                     );
$obTxtAssentamento->obEvento->setOnChange   ( "limparAssentamento();"                   );

$obCmbAssentamento = new Select;
$obCmbAssentamento->setRotulo               ( "Assentamento"                            );
$obCmbAssentamento->setName                 ( "inCodAssentamento"                       );
$obCmbAssentamento->setValue                ( $stSigla                                  );
$obCmbAssentamento->setStyle                ( "width: 200px"                            );
$obCmbAssentamento->setCampoID              ( "sigla_sem_espaco"                        );
$obCmbAssentamento->setCampoDesc            ( "descricao"                               );
$obCmbAssentamento->addOption               ( "", "Selecione"                           );
$obCmbAssentamento->setNull                 ( false                                     );
$obCmbAssentamento->preencheCombo           ( $rsAssentamento                           );
$obCmbAssentamento->obEvento->setOnChange   ( "limparAssentamento();"                   );

$obRdnCondicaoProtelacao = new Radio();
$obRdnCondicaoProtelacao->setName           ( "boCondicao"                              );
$obRdnCondicaoProtelacao->setRotulo         ( "Condição"                                );
$obRdnCondicaoProtelacao->setTitle          ( "Selecione a condição a ser aplicada no assentamento." );
$obRdnCondicaoProtelacao->setLabel          ( "Protelação"                              );
$obRdnCondicaoProtelacao->setValue          ( "p"                                       );
$obRdnCondicaoProtelacao->setNull           ( false                                     );
if ( $boCondicao == "p" or !isset($boCondicao) ) {
    $obRdnCondicaoProtelacao->setChecked    ( true                                      );
}

$obRdnCondicaoAverbacao = new Radio();
$obRdnCondicaoAverbacao->setName            ( "boCondicao"                              );
$obRdnCondicaoAverbacao->setTitle           ( "Selecione a condição a ser aplicada no assentamento." );
$obRdnCondicaoAverbacao->setRotulo          ( "Condição"                                );
$obRdnCondicaoAverbacao->setLabel           ( "Averbação"                               );
$obRdnCondicaoAverbacao->setValue           ( "a"                                       );
$obRdnCondicaoAverbacao->setNull            ( false                                     );
if ($boCondicao == "a") {
    $obRdnCondicaoAverbacao->setChecked     ( true                                      );
}

$obTxtDiasIncidencia = new TextBox;
$obTxtDiasIncidencia->setRotulo             ( "*Dias para Incidência"                    );
$obTxtDiasIncidencia->setName               ( "inDiasIncidencia"                        );
$obTxtDiasIncidencia->setTitle              ( "Informe a quantidade de dias em que o assentamento incidirá." );
$obTxtDiasIncidencia->setValue              ( $inDiasIncidencia                         );
$obTxtDiasIncidencia->setSize               ( 6                                         );
$obTxtDiasIncidencia->setMaxLength          ( 3                                         );
$obTxtDiasIncidencia->setNull               ( true                                     );

//Assentamento para Vinculação
$obTxtClassificacaoVinculacao = new TextBox;
$obTxtClassificacaoVinculacao->setRotulo              ( "*Classificação"                                            );
$obTxtClassificacaoVinculacao->setTitle               ( "Informe a classificação do assentamento a ser vinculado."   );
$obTxtClassificacaoVinculacao->setName                ( "inCodClassificacaoVinculacaoTxt"                           );
$obTxtClassificacaoVinculacao->setValue               ( $inCodClassificacaoVinculacaoTxt                            );
$obTxtClassificacaoVinculacao->setSize                ( 10                                                          );
$obTxtClassificacaoVinculacao->setMaxLength           ( 6                                                           );
$obTxtClassificacaoVinculacao->setInteiro             ( true                                                        );
$obTxtClassificacaoVinculacao->setNull                ( true                                                        );
$obTxtClassificacaoVinculacao->obEvento->setOnChange  ( "buscaValor('preencheAssentamento2');"                      );

$obCmbClassificacaoVinculacao = new Select;
$obCmbClassificacaoVinculacao->setRotulo              ( "*Classificação"                                            );
$obCmbClassificacaoVinculacao->setName                ( "inCodClassificacaoVinculacao"                              );
$obCmbClassificacaoVinculacao->setValue               ( $inCodClassificacaoVinculacao                               );
$obCmbClassificacaoVinculacao->setStyle               ( "width: 200px"                                              );
$obCmbClassificacaoVinculacao->setCampoID             ( "cod_classificacao"                                         );
$obCmbClassificacaoVinculacao->setCampoDesc           ( "descricao"                                                 );
$obCmbClassificacaoVinculacao->addOption              ( "", "Selecione"                                             );
$obCmbClassificacaoVinculacao->setNull                ( true                                                        );
$obCmbClassificacaoVinculacao->preencheCombo          ( $rsClassificacao                                            );
$obCmbClassificacaoVinculacao->obEvento->setOnChange  ( "buscaValor('preencheAssentamento2');"                      );

$obTxtAssentamentoVinculacao = new TextBox;
$obTxtAssentamentoVinculacao->setRotulo               ( "*Assentamento"                                             );
$obTxtAssentamentoVinculacao->setTitle                ( "Informe o assentamento a ser vinculado."                    );
$obTxtAssentamentoVinculacao->setName                 ( "stSiglaVinculado"                                          );
$obTxtAssentamentoVinculacao->setValue                ( $stSiglaVinculado                                           );
$obTxtAssentamentoVinculacao->setSize                 ( 10                                                          );
$obTxtAssentamentoVinculacao->setMaxLength            ( 10                                                          );
$obTxtAssentamentoVinculacao->setNull                 ( true                                                        );

$obCmbAssentamentoVinculacao = new Select;
$obCmbAssentamentoVinculacao->setRotulo               ( "*Assentamento"                                             );
$obCmbAssentamentoVinculacao->setName                 ( "inCodAssentamentoVinculacao"                               );
$obCmbAssentamentoVinculacao->setValue                ( $inCodAssentamentoVinculacao                                );
$obCmbAssentamentoVinculacao->setStyle                ( "width: 200px"                                              );
$obCmbAssentamentoVinculacao->setCampoID              ( "sigla_sem_espaco"                                          );
$obCmbAssentamentoVinculacao->setCampoDesc            ( "descricao"                                                 );
$obCmbAssentamentoVinculacao->addOption               ( "", "Selecione"                                             );
$obCmbAssentamentoVinculacao->setNull                 ( true                                                        );
$obCmbAssentamentoVinculacao->preencheCombo           ( $rsAssentamento                                             );

$obTxtDiasVinculado = new TextBox;
$obTxtDiasVinculado->setRotulo                        ( "*Dias (Protelados/Averbados)"                               );
$obTxtDiasVinculado->setName                          ( "inDiasVinculado"                                           );
$obTxtDiasVinculado->setValue                         ( $inDiasVinculado                                            );
$obTxtDiasVinculado->setTitle                         ( "Informe a quantidade de dias que serão protelados ou averbados." );
$obTxtDiasVinculado->setSize                          ( 6                                                           );
$obTxtDiasVinculado->setMaxlength                     ( 3                                                           );
$obTxtDiasVinculado->setInteiro                       ( true                                                        );
$obTxtDiasVinculado->setNull                          ( true                                                         );

$obinnBuscaFuncao = new BuscaInner;
$obinnBuscaFuncao->setRotulo ( "Fórmula"                 );
$obinnBuscaFuncao->setId     ( "stFuncao"                );
$obinnBuscaFuncao->obCampoCod->setName   ( "inCodFuncao" );
$obinnBuscaFuncao->obCampoCod->setValue  ( $inCodFuncao  );
$obinnBuscaFuncao->obCampoCod->obEvento->setOnChange("buscaValor('buscaFuncao');");
$obinnBuscaFuncao->obCampoCod->obEvento->setOnBlur  ("buscaValor('buscaFuncao');");
$obinnBuscaFuncao->obCampoCod->setMascara("99.99.999");
$obinnBuscaFuncao->setFuncaoBusca( "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodFuncao','stFuncao','todos','".Sessao::getId()."','800','550');" );

$obBtnIncluir = new Button;
$obBtnIncluir->setName                                ( "btnIncluir"                                                );
$obBtnIncluir->setValue                               ( "Incluir"                                                   );
$obBtnIncluir->setTipo                                ( "button"                                                    );
$obBtnIncluir->obEvento->setOnClick                   ( "incluirAssentamentoVinculado();"                           );

$obBtnAlterar = new Button;
$obBtnAlterar->setName                                ( "btnAlterar"                                                );
$obBtnAlterar->setValue                               ( "Alterar"                                                   );
$obBtnAlterar->setTipo                                ( "button"                                                    );
$obBtnAlterar->obEvento->setOnClick                   ( "buscaValor('alterarAssentamentoVinculado');"               );

$obBtnLimpar = new Button;
$obBtnLimpar->setName                                 ( "btnLimpar"                                                 );
$obBtnLimpar->setValue                                ( "Limpar"                                                    );
$obBtnLimpar->setTipo                                 ( "button"                                                    );
$obBtnLimpar->obEvento->setOnClick                    ( "limparAssentamentoVinculado();"                            );

$obSpnAssentamentoVinculado = new Span;
$obSpnAssentamentoVinculado->setId                    ( "spnAssentamentosVinculados"                                );

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

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( ""       );

//Define o código da Classificação da Condição quando o combo está desabilitado.
$obHdnCodClassificacao = new Hidden;
$obHdnCodClassificacao->setName     ( "stHdnCodClassificacao" );

$obHdnCodAssentamento = new Hidden;
$obHdnCodAssentamento->setName     ( "stHdnCodAssentamento" );

$obHdnCodClassificacaoVinculado = new Hidden;
$obHdnCodClassificacaoVinculado->setName     ( "stHdnCodClassificacaoVinculado" );

$obHdnCodAssentamentoVinculado = new Hidden;
$obHdnCodAssentamentoVinculado->setName     ( "stHdnCodAssentamentoVinculado" );

$obHdnAssentamentoVinculado = new Hidden;
$obHdnAssentamentoVinculado->setName( "inAssentamentoVinculado" );
$obHdnAssentamentoVinculado->setValue( $inAssentamentoViculado  );

$obHdnCodCondicao = new Hidden;
$obHdnCodCondicao->setName( "inCodCondicao" );
$obHdnCodCondicao->setValue( $inCodCondicao  );

//DEFINICAO DO FORMULARIO

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );

$obFormulario->addHidden                ( $obHdnCtrl                );
$obFormulario->addHidden                ( $obHdnAcao                );
$obFormulario->addHidden                ( $obHdnAssentamentoVinculado );
$obFormulario->addHidden                ( $obHdnCodCondicao );
$obFormulario->addHidden                ( $obHdnCodClassificacao );
$obFormulario->addHidden                ( $obHdnCodAssentamento );
$obFormulario->addHidden                ( $obHdnCodClassificacaoVinculado );
$obFormulario->addHidden                ( $obHdnCodAssentamentoVinculado );

$obFormulario->addTitulo                ( "Assentamento"                                                    );
$obFormulario->addComponenteComposto    ( $obTxtClassificacao, $obCmbClassificacao                          );
$obFormulario->addComponenteComposto    ( $obTxtAssentamento, $obCmbAssentamento                            );
$obFormulario->addTitulo                ( "Condição do Assentamento para Vinculação"                                   );
$obFormulario->agrupaComponentes        ( array($obRdnCondicaoProtelacao,$obRdnCondicaoAverbacao)           );
$obFormulario->addComponente            ( $obTxtDiasIncidencia                                              );
$obFormulario->addComponenteComposto    ( $obTxtClassificacaoVinculacao, $obCmbClassificacaoVinculacao      );
$obFormulario->addComponenteComposto    ( $obTxtAssentamentoVinculacao, $obCmbAssentamentoVinculacao        );
$obFormulario->addComponente            ( $obTxtDiasVinculado                                               );
$obFormulario->addComponente            ( $obinnBuscaFuncao                                                 );
$obFormulario->defineBarra              ( array( $obBtnIncluir, $obBtnAlterar, $obBtnLimpar ) ,'',''        );
$obFormulario->addSpan                  ( $obSpnAssentamentoVinculado                                       );
$obFormulario->setFormFocus             ($obTxtClassificacao->getId()                                       );
if ($_REQUEST['stAcao']=='alterar') {
    $obFormulario->Cancelar($pgList."?".Sessao::getId().$stLink);
} else {
    $obLimparForm = new Button;
    $obLimparForm->setName                    ( "btnLimparForm"            );
    $obLimparForm->setValue                   ( "Limpar"                   );
    $obLimparForm->setTipo                    ( "button"                   );
    $obLimparForm->obEvento->setOnClick       ( "buscaValor('limpaForm');" );
    $obLimparForm->setDisabled                ( false                      );

    $obBtnOK = new Ok;
    $botoesForm     = array ( $obBtnOK , $obLimparForm );

    $obFormulario->defineBarra($botoesForm);
}
$obFormulario->show();
?>

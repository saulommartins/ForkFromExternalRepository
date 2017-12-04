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
    * Página de Formulário FolhaPagamentoIRRF
    * Data de Criação: 05/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 31571 $
    $Name:  $
    $Author: alex $
    $Date: 2007-11-14 11:18:50 -0200 (Qua, 14 Nov 2007) $

    * Casos de uso: uc-04.05.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoIRRF.class.php"                                       );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                               );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfComprovanteRendimento.class.php"                      );

$link = Sessao::read("link");
$stPrograma = "ManterTabelaIRRF";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$link["pg"]."&pos=".$link["pos"];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
include_once($pgJs);
include_once($pgOcul);
$obRFolhaPagamentoIRRF = new RFolhaPagamentoIRRF;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stLink = "";
foreach ($_GET as $stCampo=>$stValor) {
    if ($stCampo != 'PHPSESSID' and $stCampo != 'iURLRandomica' and $stCampo != 'stAcao') {
        $stLink .= "&".$stCampo."=".$stValor;
    }
}
$stLocation = $pgList.$stLink;

if ($stAcao == 'incluir') {
    $obRFolhaPagamentoIRRF->listarIRRF($rsIRRF);
}
if ($stAcao == 'alterar') {
    $inCodTabela = $_GET['inCodTabela'];
    $obRFolhaPagamentoIRRF->setTimestamp($_GET["stTimestamp"]);
    $obRFolhaPagamentoIRRF->setCodTabela($inCodTabela);
    $obRFolhaPagamentoIRRF->listarIRRF($rsIRRF,$stFiltro);

    $rsIRRF->addFormatacao('vl_dependente','NUMERIC_BR');
    $rsIRRF->addFormatacao('vl_limite_isencao','NUMERIC_BR');

    $flValorDependente = $rsIRRF->getCampo('vl_dependente');
    $flValorLimite     = $rsIRRF->getCampo('vl_limite_isencao');
    $dtVigencia        = $rsIRRF->getCampo('vigencia');
    $inCodTabela       = $rsIRRF->getCampo('cod_tabela');
    $stTimestamp       = $rsIRRF->getCampo('timestamp');
    Sessao::write('dtVigencia',$dtVigencia);

    $obRFolhaPagamentoIRRF->addRFolhaPagamentoEvento();
    $obRFolhaPagamentoIRRF->roRFolhaPagamentoEventoIRRF->listarTabelaIRRFEvento($rsEventoIRRF);
    while (!$rsEventoIRRF->eof()) {
        $stValue          = "inCodigoIRRF".$rsEventoIRRF->getCampo('cod_tipo');
        $$stValue         = $rsEventoIRRF->getCampo('codigo');
        $rsEventoIRRF->proximo();
    }

    $obRFolhaPagamentoIRRF->listarCID($rsCID);
    $arCid = array();
    while (!$rsCID->eof()) {
        $arTemp = array();
        $arTemp['inId']     = count($arCid)+1;
        $arTemp['cod_cid']  = $rsCID->getCampo('cod_cid');
        $arTemp['descricao']= $rsCID->getCampo('descricao');
        $arCid[] = $arTemp;
        $rsCID->proximo();
    }
    Sessao::write("cid",$arCid);
    $obRFolhaPagamentoIRRF->addRFolhaPagamentoFaixaIRRF();
    $obRFolhaPagamentoIRRF->roRFolhaPagamentoFaixaIRRF->listarFaixaIRRF($rsFaixaIRRF);
    $rsFaixaIRRF->addFormatacao('vl_inicial','NUMERIC_BR');
    $rsFaixaIRRF->addFormatacao('vl_final','NUMERIC_BR');
    $rsFaixaIRRF->addFormatacao('aliquota','NUMERIC_BR');
    $rsFaixaIRRF->addFormatacao('parcela_deduzir','NUMERIC_BR');
    $arFaixa = array();
    while (!$rsFaixaIRRF->eof()) {
        $arTemp = array();
        $arTemp['inId']             = count($arFaixa)+1;
        $arTemp['flValorInicial']   = $rsFaixaIRRF->getCampo('vl_inicial');
        $arTemp['flValorFinal']     = $rsFaixaIRRF->getCampo('vl_final');
        $arTemp['flAliquota']       = $rsFaixaIRRF->getCampo('aliquota');
        $arTemp['flParcela']        = $rsFaixaIRRF->getCampo('parcela_deduzir');
        $arFaixa[] = $arTemp;
        $rsFaixaIRRF->proximo();
    }
    Sessao::write("faixa",$arFaixa);

    $obTFolhaPagamentoTabelaIrrfComprovanteRendimento = new TFolhaPagamentoTabelaIrrfComprovanteRendimento();
    $stFiltroEventoAjudaCusto = " AND tabela_irrf_comprovante_rendimento.cod_tabela = $inCodTabela
                                  AND tabela_irrf_comprovante_rendimento.timestamp = '$stTimestamp'";
    $obTFolhaPagamentoTabelaIrrfComprovanteRendimento->recuperaRelacionamento($rsEventoAjudaCusto, $stFiltroEventoAjudaCusto);

    $arEventoAjudaCusto = array();
    while (!$rsEventoAjudaCusto->eof()) {
        $arTemp = array();
        $arTemp['inId']             = count($arEventoAjudaCusto)+1;
        $arTemp['flCodigoEvento']   = $rsEventoAjudaCusto->getCampo('cod_evento');
        $arTemp['flCodigo']         = $rsEventoAjudaCusto->getCampo('codigo');
        $arTemp['flDescricao']      = $rsEventoAjudaCusto->getCampo('descricao');
    $arTemp['flDescNatureza']   = ($rsEventoAjudaCusto->getCampo('natureza')=='D')?"Descontos":"Proventos";
        $arEventoAjudaCusto[] = $arTemp;
        $rsEventoAjudaCusto->proximo();
    }
    Sessao::write("eventoAjudaCusto",$arEventoAjudaCusto);

    processarFormAlteracao(true);
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                  );
$obHdnAcao->setValue                            ( $stAcao                                                   );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                  );
$obHdnCtrl->setValue                            ( ""                                                        );

$obHdnId = new Hidden;
$obHdnId->setName                               ( "inId"                                                    );
$obHdnId->setValue                              ( ""                                                        );

$obHdnCodTabela = new Hidden;
$obHdnCodTabela->setName                        ( "inCodTabela"                                             );
$obHdnCodTabela->setValue                       ( $inCodTabela                                              );

$obHdnTimestamp = new Hidden;
$obHdnTimestamp->setName                        ( "stTimestamp"                                             );
$obHdnTimestamp->setValue                       ( $stTimestamp                                              );

$obHdnVigenciaAntiga = new Hidden;
$obHdnVigenciaAntiga->setName                   ( "dtVigenciaAntiga"                                        );
$obHdnVigenciaAntiga->setValue                  ( $dtVigencia                                               );

$obRFolhaPagamentoIRRF->addRPessoalCID();
$obRFolhaPagamentoIRRF->roRPessoalCID->listarOrdenadoPorDescricao( $rsCID );

$obBscCID = new BuscaInner;
$obBscCID->setRotulo               ( "CID"                                                             );
$obBscCID->setTitle                ( "Informe o CID para inserção de IRRF de inativos e pensionistas." );
$obBscCID->setNull                 ( true                                                              );
$obBscCID->setId                   ( "stCID"                                                           );
$obBscCID->setValue                ( $stDescricaoCID                                                   );
$obBscCID->obCampoCod->setName     ( "inCodCID"                                                        );
$obBscCID->obCampoCod->setValue    ( $inCodCID                                                         );
$obBscCID->obCampoCod->setSize     ( 10                                                                );
$obBscCID->obCampoCod->setAlign    ( "left"                                                            );
$obBscCID->obCampoCod->obEvento->setOnChange ( "buscaValor('preencheCID');"                                      );
$obBscCID->setFuncaoBusca          ( "abrePopUp('".CAM_GRH_PES_POPUPS."CID/FLProcurarCID.php','frm','inCodCID','stCID','','".Sessao::getId()."','800','550')" );

$obHdnCodCID = new Hidden;
$obHdnCodCID->setName  ( "inSiglaCID" );

$obSpnSpan1 = new Span;
$obSpnSpan1->setId                              ( "spnSpan1"                                                );

$obSpnSpan2 = new Span;
$obSpnSpan2->setId                              ( "spnSpan2"                                                );

$obTxtVlrDependente = new Moeda;
$obTxtVlrDependente->setRotulo                  ( "Valor por Dependente para Dedução"                       );
$obTxtVlrDependente->setName                    ( "flValorDependente"                                       );
$obTxtVlrDependente->setValue                   ( $flValorDependente                                        );
$obTxtVlrDependente->setTitle                   ( "Informe o valor a ser deduzido por dependete."           );
$obTxtVlrDependente->setMaxLength               ( 14                                                        );
$obTxtVlrDependente->setNull                    ( false                                                     );

$obTxtVlrLimite = new Moeda;
$obTxtVlrLimite->setRotulo                      ( "Valor Limite para Isenção (Inativos/Pensionistas) Acima de 65 anos" );
$obTxtVlrLimite->setName                        ( "flValorLimite"                                          );
$obTxtVlrLimite->setValue                       ( $flValorLimite                                           );
$obTxtVlrLimite->setTitle                       ( "Informe o valor limite de insenção para inativos e pensionistas acima de 65 anos." );
$obTxtVlrLimite->setMaxLength                   ( 14                                                        );
$obTxtVlrLimite->setNull                        ( false                                                     );

$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
$obRFolhaPagamentoConfiguracao->consultar();
$stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

$obRFolhaPagamentoIRRF->addRFolhaPagamentoEvento();
$obRFolhaPagamentoIRRF->roRFolhaPagamentoEventoIRRF->listarEventoIRRF($rsTiposEvento);
while (!$rsTiposEvento->eof()) {
    $stNomeComponente = "obBscEventoIRRF".$rsTiposEvento->getCampo('cod_tipo');
    $stValue          = "inCodigoIRRF".$rsTiposEvento->getCampo('cod_tipo');
    $stInner          = "inCampoInnerIRRF".$rsTiposEvento->getCampo('cod_tipo');
    $stNatureza       = 'D';
    if ( strpos(strtolower($rsTiposEvento->getCampo('descricao')),strtolower('Informativo')) ) {
        $stNatureza       = 'I';
    }
    if ( strpos(strtolower($rsTiposEvento->getCampo('descricao')),strtolower('Base')) ) {
        $stNatureza       = 'B';
    }

    $$stNomeComponente = new BuscaInner;
    $$stNomeComponente->setRotulo                       ( $rsTiposEvento->getCampo('descricao')                       );
    $$stNomeComponente->setTitle                        ( "Informe o Evento."                                         );
    $$stNomeComponente->setId                           ( $stInner                                                    );
    $$stNomeComponente->setNull                         ( false                                                       );
    $$stNomeComponente->obCampoCod->setName             ( $stValue                                                    );
    $$stNomeComponente->obCampoCod->setValue            ( $$stValue                                                   );
    $$stNomeComponente->obCampoCod->setAlign            ( "LEFT"                                                      );
    $$stNomeComponente->obCampoCod->setMascara          ( $stMascaraEvento                                            );
    $$stNomeComponente->obCampoCod->setPreencheComZeros ( "E"                                                         );
    $$stNomeComponente->obCampoCod->obEvento->setOnChange( "preencherEvento('IRRF".$rsTiposEvento->getCampo('cod_tipo')."','".$stNatureza."',true);" );
    $$stNomeComponente->setFuncaoBusca                  ( "abrePopUp('".CAM_GRH_FOL_POPUPS."IRRF/FLManterTabelaIRRF.php','frm','".$stValue."','".$stInner."','','".Sessao::getId()."&stNatureza=".$stNatureza."&boEventoSistema=true','800','550')" );
    $rsTiposEvento->proximo();
}

$obTxtVlrInicial = new Moeda;
$obTxtVlrInicial->setRotulo                     ( "*Valor Inicial da Base"                                  );
$obTxtVlrInicial->setName                       ( "flValorInicial"                                          );
$obTxtVlrInicial->setValue                      ( $flValorInicial                                           );
$obTxtVlrInicial->setTitle                      ( "Informe o valor inicial da base."                        );
$obTxtVlrInicial->setMaxLength                  ( 14                                                        );

$obTxtVlrFinal = new Moeda;
$obTxtVlrFinal->setRotulo                       ( "*Valor Final da Base"                                    );
$obTxtVlrFinal->setName                         ( "flValorFinal"                                            );
$obTxtVlrFinal->setValue                        ( $flValorFinal                                             );
$obTxtVlrFinal->setTitle                        ( "Informe o valor final da base."                          );
$obTxtVlrFinal->setMaxLength                    ( 14                                                        );

$obTxtVlrAliquota = new Moeda;
$obTxtVlrAliquota->setRotulo                    ( "*Alíquota"                                               );
$obTxtVlrAliquota->setName                      ( "flAliquota"                                              );
$obTxtVlrAliquota->setValue                     ( $flAliquota                                               );
$obTxtVlrAliquota->setTitle                     ( "Informe o valor da alíquota."                            );
$obTxtVlrAliquota->setMaxLength                 ( 14                                                        );

$obTxtVlrParcela = new Moeda;
$obTxtVlrParcela->setRotulo                     ( "*Parcela a Deduzir"                                      );
$obTxtVlrParcela->setName                       ( "flParcela"                                               );
$obTxtVlrParcela->setValue                      ( $flParcela                                                );
$obTxtVlrParcela->setTitle                      ( "Informe o valor da parcela a deduzir."                   );
$obTxtVlrParcela->setMaxLength                  ( 14                                                        );

$obTxtDtVigencia = new Data;
$obTxtDtVigencia->setName                       ( "dtVigencia"                                              );
$obTxtDtVigencia->setValue                      ( $dtVigencia                                               );
$obTxtDtVigencia->setRotulo                     ( "Vigência"                                                );
$obTxtDtVigencia->setNull                       ( false                                                     );
$obTxtDtVigencia->setTitle                      ( 'Informe a data da vigência.'                             );
$obTxtDtVigencia->obEvento->setOnChange         ( "buscaValor('validarVigencia');"                          );

$obBtnIncluirCID = new Button;
$obBtnIncluirCID->setName                       ( "btnIncluirCID"                                           );
$obBtnIncluirCID->setValue                      ( "Incluir"                                                 );
$obBtnIncluirCID->setTipo                       ( "button"                                                  );
$obBtnIncluirCID->obEvento->setOnClick          ( "buscaValor('incluirCID');"                               );

$obBtnLimparCID = new Button;
$obBtnLimparCID->setName                        ( "btnLimparCID"                                            );
$obBtnLimparCID->setValue                       ( "Limpar"                                                  );
$obBtnLimparCID->setTipo                        ( "button"                                                  );
$obBtnLimparCID->obEvento->setOnClick           ( "buscaValor('limparCID');"                                );

$obBtnIncluirFaixa = new Button;
$obBtnIncluirFaixa->setName                     ( "btnIncluirFaixa"                                         );
$obBtnIncluirFaixa->setValue                    ( "Incluir"                                                 );
$obBtnIncluirFaixa->setTipo                     ( "button"                                                  );
$obBtnIncluirFaixa->obEvento->setOnClick        ( "buscaValor('incluirFaixa');"                             );

$obBtnAlterarFaixa = new Button;
$obBtnAlterarFaixa->setName                     ( "btnAlterarFaixa"                                         );
$obBtnAlterarFaixa->setValue                    ( "Alterar"                                                 );
$obBtnAlterarFaixa->setTipo                     ( "button"                                                  );
$obBtnAlterarFaixa->obEvento->setOnClick        ( "buscaValor('alterarFaixa');"                             );

$obBtnLimparFaixa = new Button;
$obBtnLimparFaixa->setName                      ( "btnLimparFaixa"                                          );
$obBtnLimparFaixa->setValue                     ( "Limpar"                                                  );
$obBtnLimparFaixa->setTipo                      ( "button"                                                  );
$obBtnLimparFaixa->obEvento->setOnClick         ( "buscaValor('limparFaixa');"                              );

$obBtnOk = new Ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimpar"                                               );
$obBtnLimpar->setValue                          ( "Limpar"                                                  );
$obBtnLimpar->setTipo                           ( "button"                                                  );
$obBtnLimpar->obEvento->setOnClick              ( "buscaValor('limparForm');"                               );

$obLblTabela = new Label;
$obLblTabela->setRotulo("Situação");
$obLblTabela->setValue("A tabela de IRRF já está criada");

//COMPROVANTE DE RENDIMENTOS

$obBtnIncluirEventoAjudaCusto = new Button;
$obBtnIncluirEventoAjudaCusto->setName                     ( "btnIncluirEventoAjudaCusto"                        );
$obBtnIncluirEventoAjudaCusto->setValue                    ( "Incluir"                                                 );
$obBtnIncluirEventoAjudaCusto->setTipo                     ( "button"                                                  );
$obBtnIncluirEventoAjudaCusto->obEvento->setOnClick        ( "buscaValor('incluirEventoAjudaCusto');"            );

$obSpnSpan3 = new Span;
$obSpnSpan3->setId                              ( "spnSpan3"                                                );

$obBscEventoAjudaCusto = new BuscaInner;
$obBscEventoAjudaCusto->setRotulo                       ( "Evento"                                                    );
$obBscEventoAjudaCusto->setTitle                        ( "Informe o Evento de Proventos ou Descontos correspondente à Diárias ou Ajuda de Custo");
$obBscEventoAjudaCusto->setId                           ( "inCampoInnerEventoAjudaCusto"                                  );
$obBscEventoAjudaCusto->obCampoCod->setName             ( "inCodigoEventoAjudaCusto"                                      );
$obBscEventoAjudaCusto->obCampoCod->setAlign            ( "LEFT"                                                      );
$obBscEventoAjudaCusto->obCampoCod->setMascara          ( $stMascaraEvento                                            );
$obBscEventoAjudaCusto->obCampoCod->setPreencheComZeros ( "E"                                                         );
$obBscEventoAjudaCusto->obCampoCod->obEvento->setOnChange( "preencherEvento('EventoAjudaCusto','P,D',false);" );
$obBscEventoAjudaCusto->setFuncaoBusca                  ( "abrePopUp('".CAM_GRH_FOL_POPUPS."IRRF/FLManterTabelaIRRF.php','frm','inCodigoEventoAjudaCusto','inCampoInnerEventoAjudaCusto','','".Sessao::getId()."&stNatureza=P,D&boEventoSistema=false','800','550')" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                   );
$obForm->setTarget                              ( "oculto"                                                  );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                   );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
if ( ($rsIRRF->getNumLinhas() <= 0 and $stAcao == 'incluir') or $stAcao == 'alterar' ) {
    $obFormulario->addHidden                        ( $obHdnAcao                                                );
    $obFormulario->addHidden                        ( $obHdnCtrl                                                );
    $obFormulario->addHidden                        ( $obHdnId                                                  );
    $obFormulario->addHidden                        ( $obHdnCodTabela                                           );
    $obFormulario->addHidden                        ( $obHdnTimestamp                                           );
    $obFormulario->addHidden                        ( $obHdnVigenciaAntiga                                      );
    $obFormulario->addHidden                        ( $obHdnCodCID                                              );
    $obFormulario->addTitulo                        ( "Dados da Tabela de IRRF"                                 );
    $obFormulario->addComponente                    ( $obBscCID                                                 );

    $obFormulario->defineBarra                      ( array($obBtnIncluirCID,$obBtnLimparCID),"",""             );
    $obFormulario->addSpan                          ( $obSpnSpan1                                               );
    $obFormulario->addComponente                    ( $obTxtVlrDependente                                       );

    $rsTiposEvento->setPrimeiroElemento();
    while (!$rsTiposEvento->eof()) {
        $stNomeComponente = "obBscEventoIRRF".$rsTiposEvento->getCampo('cod_tipo');
        $obFormulario->addComponente                ( $$stNomeComponente                                        );
        if ( $rsTiposEvento->getCorrente() == 1 ) {
            $obFormulario->addComponente            ( $obTxtVlrLimite                                           );
        }
        $rsTiposEvento->proximo();
    }

    $obFormulario->addTitulo                        ( "Comprovante de Rendimentos"                              );
    $obFormulario->addComponente                	( $obBscEventoAjudaCusto                                  );
    $obFormulario->defineBarra                      ( array($obBtnIncluirEventoAjudaCusto),"","" );
    $obFormulario->addSpan                          ( $obSpnSpan3                                               );
    $obFormulario->addTitulo                        ( "Dados das Faixas de Desconto"                            );
    $obFormulario->addComponente                    ( $obTxtVlrInicial                                          );
    $obFormulario->addComponente                    ( $obTxtVlrFinal                                            );
    $obFormulario->addComponente                    ( $obTxtVlrAliquota                                         );
    $obFormulario->addComponente                    ( $obTxtVlrParcela                                          );
    $obFormulario->defineBarra                      ( array($obBtnIncluirFaixa,$obBtnAlterarFaixa,$obBtnLimparFaixa),"","" );
    $obFormulario->addSpan                          ( $obSpnSpan2                                               );
    $obFormulario->addComponente                    ( $obTxtDtVigencia                                          );
    if ($stAcao == 'incluir') {
        $obFormulario->defineBarra                  ( array($obBtnOk,$obBtnLimpar)                              );
    } else {
        $obFormulario->Cancelar($stLocation);
    }
    //$obFormulario->setFormFocus                     ( $obTxtCID->getId()                                        );
} else {
    $obFormulario->addTitulo( "Tabela de IRRF" );
    $obFormulario->addComponente( $obLblTabela );
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

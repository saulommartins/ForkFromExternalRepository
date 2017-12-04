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
    * Formulário de Manter Cadastro FGTS
    * Data de Criação: 10/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30766 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-19 17:46:57 -0300 (Ter, 19 Jun 2007) $

    * Casos de uso: uc-04.05.42
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFGTS.class.php"                                       );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                               );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );

$link = Sessao::read("link");
$stPrograma = "ManterConfiguracaoFGTS";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$link["pg"]."&pos=".$link["pos"];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

include_once($pgJs);
include_once($pgOcul);
$obRFolhaPagamentoFGTS = new RFolhaPagamentoFGTS;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stLink = "";
foreach ($_GET as $stCampo=>$stValor) {
    if ($stCampo != 'PHPSESSID' and $stCampo != 'iURLRandomica' and $stCampo != 'stAcao') {
        $stLink .= "&".$stCampo."=".$stValor;
    }
}
$stLocation = $pgList.$stLink;

#sessao->transf = array();
if ($stAcao == 'incluir') {
    $obRFolhaPagamentoFGTS->listarFGTS($rsFGTS);
}

if ($stAcao == 'alterar') {
    $inCodFGTS = $_GET['inCodFGTS'];
    $obRFolhaPagamentoFGTS->setCodFGTS($inCodFGTS);
    $obRFolhaPagamentoFGTS->listarFGTS($rsFGTS);

    $obRFolhaPagamentoFGTS->addRFolhaPagamentoEvento();
    $obRFolhaPagamentoFGTS->roRFolhaPagamentoFGTSEvento->listarFGTSEvento($rsFGTSEvento);
    while (!$rsFGTSEvento->eof()) {
        $stValue          = "inCodigoFGTS".$rsFGTSEvento->getCampo('cod_tipo');
        $$stValue         = $rsFGTSEvento->getCampo('codigo');
        $rsFGTSEvento->proximo();
    }

    $obRFolhaPagamentoFGTS->addRPessoalCategoria();
    $obRFolhaPagamentoFGTS->roRFolhaPagamentoFGTSCategoria->listarFGTSCategoria($rsFGTSCategoria);
    $rsFGTSCategoria->addFormatacao("aliquota_deposito","NUMERIC_BR");
    $rsFGTSCategoria->addFormatacao("aliquota_contribuicao","NUMERIC_BR");

    $arCategoria = array();
    while (!$rsFGTSCategoria->eof()) {
        $arTemp['inId']                     = $rsFGTSCategoria->getCorrente();
        $arTemp['inCodTxtCategoriaSefip']   = $rsFGTSCategoria->getCampo('cod_categoria');
        $arTemp['stDescricao']              = $rsFGTSCategoria->getCampo('descricao');
        $arTemp['flValorDeposito']          = $rsFGTSCategoria->getCampo('aliquota_deposito');
        $arTemp['flValorRemuneracao']       = $rsFGTSCategoria->getCampo('aliquota_contribuicao');
        $arCategoria[]= $arTemp;

        $rsFGTSCategoria->proximo();
    }
    Sessao::write("categoria",$arCategoria);
    $dtVigencia = $rsFGTS->getCampo('vigencia');
    Sessao::write('dtVigencia',$dtVigencia);

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

$obHdnCodFGTS = new Hidden;
$obHdnCodFGTS->setName                         ( "inCodFGTS"                                                );
$obHdnCodFGTS->setValue                        ( $inCodFGTS                                                 );

$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
$obRFolhaPagamentoConfiguracao->consultar();
$stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

$obRFolhaPagamentoFGTS->addRFolhaPagamentoEvento();
$obRFolhaPagamentoFGTS->roRFolhaPagamentoFGTSEvento->listarTipoEventoFGTS($rsTiposEvento);
while (!$rsTiposEvento->eof()) {
    $stNomeComponente = "obBscEventoFGTS".$rsTiposEvento->getCampo('cod_tipo');
    $stValue          = "inCodigoFGTS".$rsTiposEvento->getCampo('cod_tipo');
    $stInner          = "inCampoInnerFGTS".$rsTiposEvento->getCampo('cod_tipo');
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
    $$stNomeComponente->obCampoCod->obEvento->setOnChange( "preencherEvento('".$rsTiposEvento->getCampo('cod_tipo')."','".$stNatureza."');" );
    $$stNomeComponente->setFuncaoBusca                  ( "abrePopUp('".CAM_GRH_FOL_POPUPS."FGTS/FLManterConfiguracaoFGTS.php','frm','".$stValue."','".$stInner."','','".Sessao::getId()."&stNatureza=".$stNatureza."&boEventoSistema=true','800','550')" );
    $rsTiposEvento->proximo();
}

$obinCategoria = new BuscaInner;
$obinCategoria->setRotulo            ( '*Categoria Sefip' );
$obinCategoria->setID                ( 'stCategoria'     );
$obinCategoria->obCampoCod->setName  ( 'inCodCategoria'  );
$obinCategoria->obCampoCod->setAlign ( 'right'           );
$obinCategoria->obCampoCod->obEvento->setOnChange ( "buscaValor ( 'buscaCategoria'  );" );
$obinCategoria->setFuncaoBusca( "abrePopUp('". CAM_GRH_PES_POPUPS. "assentamento/LSCategoria.php',
                                'frm', 'inCodCategoria', 'stCategoria',
                                'retorno', '".Sessao::getId()."&stAcao=SELECIONAR','800','550' );" );

$obSpan1 = new Span;
$obSpan1->setId                                 ( "spnCategorias"                                                );

$obTxtDtVigencia = new Data;
$obTxtDtVigencia->setName                       ( "dtVigencia"                                              );
$obTxtDtVigencia->setValue                      ( $dtVigencia                                               );
$obTxtDtVigencia->setRotulo                     ( "Vigência"                                                );
$obTxtDtVigencia->setNull                       ( false                                                     );
$obTxtDtVigencia->setTitle                      ( 'Informe a data da vigência.'                             );
$obTxtDtVigencia->obEvento->setOnChange         ( "buscaValor('validarVigencia');"                          );

$obBtnIncluirCategoria = new Button;
$obBtnIncluirCategoria->setName                 ( "btnIncluirCategoria"                                     );
$obBtnIncluirCategoria->setValue                ( "Incluir"                                                 );
$obBtnIncluirCategoria->setTipo                 ( "button"                                                  );
$obBtnIncluirCategoria->obEvento->setOnClick    ( "buscaValor('incluirCategoria');"                         );

$obBtnLimparCategoria = new Button;
$obBtnLimparCategoria->setName                  ( "btnLimparCategoria"                                      );
$obBtnLimparCategoria->setValue                 ( "Limpar"                                                  );
$obBtnLimparCategoria->setTipo                  ( "button"                                                  );
$obBtnLimparCategoria->obEvento->setOnClick     ( "buscaValor('limparCategoria');"                          );

$obBtnOk = new Ok;
$obBtnOk->obEvento->setOnClick                  ( "buscaValor('submeterForm');"                             );

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimpar"                                               );
$obBtnLimpar->setValue                          ( "Limpar"                                                  );
$obBtnLimpar->setTipo                           ( "button"                                                  );
$obBtnLimpar->obEvento->setOnClick              ( "buscaValor('limparForm');"                               );

$obBtnCancelar = new Cancelar($stLocation);
$obBtnCancelar->obEvento->setOnClick("Cancelar('".$stLocation."');");

$obLblFGTS = new Label;
$obLblFGTS->setRotulo("Situação");
$obLblFGTS->setValue("A configuração do FGTS já está criada");

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                   );
$obForm->setTarget                              ( "oculto"                                                  );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                   );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
if ( ($rsFGTS->getNumLinhas() <= 0 and $stAcao == 'incluir') or $stAcao == 'alterar'  ) {
    $obFormulario->addHidden                        ( $obHdnAcao                                                );
    $obFormulario->addHidden                        ( $obHdnCtrl                                                );
    $obFormulario->addHidden                        ( $obHdnCodFGTS                                             );
    $obFormulario->addTitulo                        ( "Dados do FGTS"                                           );
    $rsTiposEvento->setPrimeiroElemento();
    while (!$rsTiposEvento->eof()) {
        $stNomeComponente = "obBscEventoFGTS".$rsTiposEvento->getCampo('cod_tipo');
        $obFormulario->addComponente                ( $$stNomeComponente                                        );
        if ( $rsTiposEvento->getCorrente() == 1 ) {
            $stId = $$stNomeComponente->getId();
        }
        $rsTiposEvento->proximo();
    }

    $obFormulario->addTitulo                        ( "Dados das Categorias"                                    );
    $obFormulario->addComponente                    ( $obinCategoria                                            );
    $obFormulario->defineBarraAba                   ( array($obBtnIncluirCategoria,$obBtnLimparCategoria),"","" );
    $obFormulario->addSpan                          ( $obSpan1                                                  );
    $obFormulario->addComponente                    ( $obTxtDtVigencia                                          );
    if ($stAcao == 'incluir') {
        $obFormulario->defineBarra                  ( array($obBtnOk,$obBtnLimpar)                              );
    } else {
        $obFormulario->defineBarra                  ( array($obBtnOk,$obBtnCancelar)                              );
    }
    $obFormulario->setFormFocus                     ( $stId                                                     );
} else {
    $obFormulario->addTitulo( "Configurar FGTS" );
    $obFormulario->addComponente( $obLblFGTS );
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

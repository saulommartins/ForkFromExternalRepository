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
    * Página de Formulário para configuração
    * Data de Criação   : 22/01/2007

    * @author Diego Barbosa Victoria

    * @ignore

    $Id: FMManterDividaFundada.php 62196 2015-04-06 20:59:21Z carlos.silva $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php" );
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';

$stPrograma = "ManterDividaFundada";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "telaPrincipal" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
$obEntidadeUsuario->setNull ( true );
$obEntidadeUsuario->setRotulo( "*Entidade"                       );
$obEntidadeUsuario->setCodEntidade($request->get('inCodEntidade'));

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName  ( "inCodEntidade"                );
$obHdnCodEntidade->setValue ( $request->get('inCodEntidade') );

$obInOrgao = new Inteiro;
$obInOrgao->setName      ( "inNumOrgao"                );
$obInOrgao->setId        ( "inNumOrgao"                );
$obInOrgao->setRotulo    ( "Número do Órgão"           );
$obInOrgao->setTitle     ( "Informe o número do órgão" );
$obInOrgao->setValue     ( $request->get('inNumOrgao') );
$obInOrgao->setNull      ( false                       );
$obInOrgao->setSize      ( 10                          );
$obInOrgao->setMaxLength ( 2                           );

$obInUnidade = new Inteiro;
$obInUnidade->setName      ( "inNumUnidade"                );
$obInUnidade->setId        ( "inNumUnidade"                );
$obInUnidade->setRotulo    ( "Número da Unidade"           );
$obInUnidade->setTitle     ( "Informe o número da unidade" );
$obInUnidade->setValue     ( $request->get('inNumUnidade') );
$obInUnidade->setNull      ( false                         );
$obInUnidade->setSize      ( 10                            );
$obInUnidade->setMaxLength ( 2                             );

$obTxtExercicio = new TextBox;
$obTxtExercicio->setName     ( "stExercicio"                );
$obTxtExercicio->setId       ( "stExercicio"                );
$obTxtExercicio->setValue    ( $request->get('inExercicio') );
$obTxtExercicio->setRotulo   ( "Exercício"                  );
$obTxtExercicio->setTitle    ( "Informe o exercício."       );
$obTxtExercicio->setInteiro  ( false                        );
$obTxtExercicio->setNull     ( false                        );
$obTxtExercicio->setMaxLength( 4                            );
$obTxtExercicio->setSize     ( 5                            );

if ($stAcao === 'alterar') {
    $obNorma = new RNorma;
    $obNorma->setCodNorma( $request->get('inCodNorma') );
    $obNorma->listarDecreto( $rsNorma );

    $inLeiAutorizacao = $request->get('inCodNorma');
    $stNomNorma       = $rsNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma_exercicio')." - ".$rsNorma->getCampo('nom_norma');
    
    if ( $request->get('inNumCgm') != '' )
        $stNomeCGMCredor  = SistemaLegado::pegaDado('nom_cgm','sw_cgm','where numcgm='.$request->get('inNumCgm'));
    else    
        $stNomeCGMCredor  = "";

} else {
    $inLeiAutorizacao = "";
    $stNomNorma       = "";

}

$obIPopUpLeiAutorizacao = new IPopUpNorma();
$obIPopUpLeiAutorizacao->obInnerNorma->setId                ( "stNomeLeiAutorizacao" );
$obIPopUpLeiAutorizacao->obInnerNorma->obCampoCod->setId    ( "inCodLeiAutorizacao"  );
$obIPopUpLeiAutorizacao->obInnerNorma->obCampoCod->setName  ( "inCodLeiAutorizacao"  );
$obIPopUpLeiAutorizacao->obInnerNorma->setRotulo            ( "Lei de Autorização"   );
$obIPopUpLeiAutorizacao->obInnerNorma->setTitle             ( "Informe o número de Lei de Autorização");
$obIPopUpLeiAutorizacao->obInnerNorma->obCampoCod->setValue ( $inLeiAutorizacao      );
$obIPopUpLeiAutorizacao->obInnerNorma->setValue             ( $stNomNorma            );

$obBscCGM = new IPopUpCGM($obForm);
$obBscCGM->setId                    ( 'stNomeCGM'               );
$obBscCGM->setRotulo                ( 'Credor'                  );
$obBscCGM->setTitle                 ( 'Nome, denominação ou razão social da entidade (credora da dívida) ');
$obBscCGM->setValue                 ( $stNomeCGMCredor          );
$obBscCGM->obCampoCod->setName      ( 'inCGM'                   );
$obBscCGM->obCampoCod->setId        ( 'inCGM'                   );
$obBscCGM->obCampoCod->setSize      ( 8                         );
$obBscCGM->obCampoCod->setValue     ( $request->get('inNumCgm') );
$obBscCGM->setNull                  ( true                      );

$obCmbTipoLancamento = new Select();
$obCmbTipoLancamento->setName   ( 'inTipoLancamento'                    );
$obCmbTipoLancamento->setId     ( 'inTipoLancamento'                    );
$obCmbTipoLancamento->setRotulo ( 'Tipo de Lançamento'                  );
$obCmbTipoLancamento->setNull   (  false                                );
$obCmbTipoLancamento->addOption ( '','Selecione'                        );
$obCmbTipoLancamento->addOption ( '1','1 - Dívida Fundada Interna'      );
$obCmbTipoLancamento->addOption ( '2','2 - Dívida Fundada Externa'      );
$obCmbTipoLancamento->addOption ( '3','3 - Diversos'                    );
$obCmbTipoLancamento->setValue  ( $request->get('inCodTipoLancamento')  );

$obFlValorSaldoAnterior = new Numerico();
$obFlValorSaldoAnterior->setId        ( "flValorSaldoAnterior"             );
$obFlValorSaldoAnterior->setName      ( "flValorSaldoAnterior"             );
$obFlValorSaldoAnterior->setRotulo    ( "Valor do Saldo Anterior"          );
$obFlValorSaldoAnterior->setTitle     ( "Informe Valor do Saldo Anterior." );
$obFlValorSaldoAnterior->setValue     ( number_format($request->get('vlSaldoAnterior'), 2, ',', '.') );
$obFlValorSaldoAnterior->setNull      ( false                              );
$obFlValorSaldoAnterior->setDecimais  ( 2                                  );
$obFlValorSaldoAnterior->setMaxLength ( 16                                 );
$obFlValorSaldoAnterior->setSize      ( 17                                 );

$obFlValorContratacao = new Numerico();
$obFlValorContratacao->setId        ( "flValorContratacao"              );
$obFlValorContratacao->setName      ( "flValorContratacao"              );
$obFlValorContratacao->setRotulo    ( "Valor de Contratação"            );
$obFlValorContratacao->setTitle     ( "Informe o Valor de Contratação." );
$obFlValorContratacao->setNull      ( false                             );
$obFlValorContratacao->setValue     ( number_format($request->get('vlContratacao'), 2, ',', '.') );
$obFlValorContratacao->setDecimais  ( 2                                 );
$obFlValorContratacao->setMaxLength ( 16                                );
$obFlValorContratacao->setSize      ( 17                                );

$obFlValorAmortizacao = new Numerico();
$obFlValorAmortizacao->setId        ( "flValorAmortizacao"              );
$obFlValorAmortizacao->setName      ( "flValorAmortizacao"              );
$obFlValorAmortizacao->setRotulo    ( "Valor de Amortização"            );
$obFlValorAmortizacao->setTitle     ( "Informe o Valor de Amortização." );
$obFlValorAmortizacao->setValue     ( number_format($request->get('vlAmortizacao'), 2, ',', '.') );
$obFlValorAmortizacao->setNull      ( false                             );
$obFlValorAmortizacao->setDecimais  ( 2                                 );
$obFlValorAmortizacao->setMaxLength ( 16                                );
$obFlValorAmortizacao->setSize      ( 17                                );

$obFlValorCancelamento = new Numerico();
$obFlValorCancelamento->setId        ( "flValorCancelamento"              );
$obFlValorCancelamento->setName      ( "flValorCancelamento"              );
$obFlValorCancelamento->setRotulo    ( "Valor de Cancelamento"            );
$obFlValorCancelamento->setTitle     ( "Informe o Valor de Cancelamento." );
$obFlValorCancelamento->setValue     ( number_format($request->get('vlCancelamento'), 2, ',', '.') );
$obFlValorCancelamento->setNull      ( false                              );
$obFlValorCancelamento->setDecimais  ( 2                                  );
$obFlValorCancelamento->setMaxLength ( 16                                 );
$obFlValorCancelamento->setSize      ( 17                                 );

$obFlValorEncampacao = new Numerico();
$obFlValorEncampacao->setId        ( "flValorEncampacao"              );
$obFlValorEncampacao->setName      ( "flValorEncampacao"              );
$obFlValorEncampacao->setRotulo    ( "Valor de Encampação"            );
$obFlValorEncampacao->setTitle     ( "Informe o Valor de Encampação." );
$obFlValorEncampacao->setValue     ( number_format($request->get('vlEncampacao'), 2, ',', '.') );
$obFlValorEncampacao->setNull      ( false                            );
$obFlValorEncampacao->setDecimais  ( 2                                );
$obFlValorEncampacao->setMaxLength ( 16                               );
$obFlValorEncampacao->setSize      ( 17                               );

$obFlValorCorrecao = new Numerico();
$obFlValorCorrecao->setId        ( "flValorCorrecao"              );
$obFlValorCorrecao->setName      ( "flValorCorrecao"              );
$obFlValorCorrecao->setRotulo    ( "Valor da Correção"            );
$obFlValorCorrecao->setTitle     ( "Informe o Valor da Correção." );
$obFlValorCorrecao->setValue     ( number_format($request->get('vlCorrecao'), 2, ',', '.') );
$obFlValorCorrecao->setNull      ( false                          );
$obFlValorCorrecao->setDecimais  ( 2                              );
$obFlValorCorrecao->setMaxLength ( 16                             );
$obFlValorCorrecao->setSize      ( 17                             );

$obFlValorSaldoAtual = new Numerico();
$obFlValorSaldoAtual->setId        ( "flValorSaldoAtual"               );
$obFlValorSaldoAtual->setName      ( "flValorSaldoAtual"               );
$obFlValorSaldoAtual->setRotulo    ( "Valor do Saldo Atual"            );
$obFlValorSaldoAtual->setTitle     ( "Informe o Valor do Saldo Atual." );
$obFlValorSaldoAtual->setValue     ( number_format($request->get('vlSaldoAtual'), 2, ',', '.') );
$obFlValorSaldoAtual->setNull      ( false                             );
$obFlValorSaldoAtual->setDecimais  ( 2                                 );
$obFlValorSaldoAtual->setMaxLength ( 16                                );
$obFlValorSaldoAtual->setSize      ( 17                                );

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm                  ( $obForm                   );
$obFormulario->addHidden                ( $obHdnAcao                );
$obFormulario->addHidden                ( $obHdnCtrl                );
$obFormulario->addHidden                ( $obHdnCodEntidade         );
$obFormulario->addComponente            ( $obEntidadeUsuario        );
$obFormulario->addComponente            ( $obInOrgao                );
$obFormulario->addComponente            ( $obInUnidade              );
$obIPopUpLeiAutorizacao->geraFormulario ( $obFormulario             );
$obFormulario->addComponente            ( $obBscCGM                 );
$obFormulario->addComponente            ( $obTxtExercicio           );
$obFormulario->addComponente            ( $obCmbTipoLancamento      );
$obFormulario->addComponente            ( $obFlValorSaldoAnterior   );
$obFormulario->addComponente            ( $obFlValorContratacao     );
$obFormulario->addComponente            ( $obFlValorAmortizacao     );
$obFormulario->addComponente            ( $obFlValorCancelamento    );
$obFormulario->addComponente            ( $obFlValorEncampacao      );
$obFormulario->addComponente            ( $obFlValorCorrecao        );
$obFormulario->addComponente            ( $obFlValorSaldoAtual      );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

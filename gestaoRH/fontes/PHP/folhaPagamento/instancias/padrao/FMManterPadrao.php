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
    * Página de Formulario de Inclusao/Alteracao de Padroes
    * Data de Criação   : 02/12/2004

    * @author Gustavo Passos Tourinho

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-04 12:26:42 -0300 (Qua, 04 Jul 2007) $

    * Casos de uso :uc-04.05.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php"                                        );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPadrao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

include_once($pgJS);

#sessao->transf = array();
Sessao::write('Progressao',array());

$obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
$rsFaixas  = $rsNorma = $rsPadraoNorma = new RecordSet;

$obRFolhaPagamentoPadrao->obRNorma->obRTipoNorma->listarTodos ( $rsTipoNorma );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}
if ($stAcao == "incluir") {
   $obRFolhaPagamentoPadrao->obRFolhaPagamentoNivelPadrao->listarNivelPadrao( $rsFaixas );
} elseif ($stAcao == 'alterar') {

    $obRFolhaPagamentoPadrao->obRFolhaPagamentoNivelPadrao->roRFolhaPagamentoPadrao->setCodPadrao( $request->get('inCodPadrao'));
    $obRFolhaPagamentoPadrao->obRFolhaPagamentoNivelPadrao->listarNivelPadrao( $rsFaixas );
    $inCount = 0;
    $rsFaixas->addFormatacao("percentual", "NUMERIC_BR");
    $arProgressoes = Sessao::read("Progressao");
    while ( !$rsFaixas->eof() ) {
        $arTMP['inId']              = ++$inCount;
        $arTMP['inCodNivelPadrao']  = $rsFaixas->getCampo("cod_nivel_padrao");
        $arTMP['descricao']         = $rsFaixas->getCampo("descricao");
        $arTMP['percentual']        = $rsFaixas->getCampo("percentual");
        $arTMP['valor']             = $rsFaixas->getCampo("valor");
        $arTMP['qtdmeses']          = $rsFaixas->getCampo("qtdmeses");

        $arProgressoes[] = $arTMP;
        $rsFaixas->proximo();
    }
    Sessao::write("Progressao",$arProgressoes);

    $stHorasMensais  = str_replace (".", ",", $request->get("stHorasMensais"));
    $stHorasSemanais = str_replace (".", ",", $request->get("stHorasSemanais"));

    $obRFolhaPagamentoPadrao->obRNorma->setCodNorma                   ( $request->get("inCodNormaTxt"));
    $obRFolhaPagamentoPadrao->obRNorma->consultar                     ( $rsNormas                   );
    $inCodTipoNorma = $inCodTipoNormaTxt = $obRFolhaPagamentoPadrao->obRNorma->obRTipoNorma->getCodTipoNorma();

    $js = 'buscaValor("preencheInner");';
    SistemaLegado::executaFramePrincipal($js);

}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( ""  );

$obHdnCodPadrao =  new Hidden;
$obHdnCodPadrao->setName   ( "hdnCodPadrao" );
$obHdnCodPadrao->setValue  ( $request->get("inCodPadrao") );

//------------utilizado na alteração
$obLblDescricao = new Label;
$obLblDescricao->setRotulo ( "Descrição" );
$obLblDescricao->setName   ( "lblDescricaoPadrão" );
$obLblDescricao->setValue  ( $request->get("stDescricao") );

$obHdnDescricao =new Hidden;
$obHdnDescricao->setName   ( "hdnDescricaoPadrao" );
$obHdnDescricao->setValue  ( $request->get("stDescricao") );

//--------------------------------

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo        ( "Descrição"                       );
$obTxtDescricao->setName          ( "stDescricao"                     );
$obTxtDescricao->setValue         ( $request->get("stDescricao")      );
$obTxtDescricao->setTitle         ( "Informe a descrição do padrão"   );
$obTxtDescricao->setNull          ( false                             );
$obTxtDescricao->setSize          ( 80                                );
$obTxtDescricao->setMaxLength     ( 80                                );
$obTxtDescricao->setEspacosExtras ( false                             );

//------------utilizado na alteração
$obLblHorasMensais = new Label;
$obLblHorasMensais->setRotulo ( "Horas mensais"   );
$obLblHorasMensais->setName   ( "lblHorasMensais" );
$obLblHorasMensais->setValue  ( $request->get("stHorasMensais") );

$obHdnHorasMensais =new Hidden;
$obHdnHorasMensais->setName   ( "hdnHorasMensais" );
$obHdnHorasMensais->setValue  ( $request->get("stHorasMensais") );

//--------------------------------

$obTxtHorasMensais = new TextBox;
$obTxtHorasMensais->setRotulo    ( "Horas mensais"                         );
$obTxtHorasMensais->setName      ( "stHorasMensais"                        );
$obTxtHorasMensais->setValue     ( $request->get("stHorasMensais")         );
$obTxtHorasMensais->setTitle     ( "Informe a quantidade de horas mensais" );
$obTxtHorasMensais->setNull      ( false                                   );
$obTxtHorasMensais->setSize      ( 6                                       );
$obTxtHorasMensais->setMaxLength ( 6                                       );
$obTxtHorasMensais->setFloat     ( true                                    );
$obTxtHorasMensais->obEvento->setOnChange      ( "validaHoraMensal(document.frm.stHorasMensais.value);" );

//------------utilizado na alteração
$obLblHorasSemanais = new Label;
$obLblHorasSemanais->setRotulo ( "Horas semanais"   );
$obLblHorasSemanais->setName   ( "lblHorasSemanais" );
$obLblHorasSemanais->setValue  ( $request->get("stHorasSemanais") );

$obHdnHorasSemanais =new Hidden;
$obHdnHorasSemanais->setName   ( "hdnHorasSemanais" );
$obHdnHorasSemanais->setValue  ( $request->get("stHorasSemanais") );
//--------------------------------

$obTxtHorasSemanais = new TextBox;
$obTxtHorasSemanais->setRotulo    ( "Horas semanais"                         );
$obTxtHorasSemanais->setName      ( "stHorasSemanais"                        );
$obTxtHorasSemanais->setValue     ( $request->get("stHorasSemanais")         );
$obTxtHorasSemanais->setTitle     ( "Informe a quantidade de horas semanais" );
$obTxtHorasSemanais->setNull      ( false                                    );
$obTxtHorasSemanais->setSize      ( 6                                        );
$obTxtHorasSemanais->setMaxLength ( 6                                        );
$obTxtHorasSemanais->setFloat     ( true                                     );
$obTxtHorasSemanais->obEvento->setOnChange      ( "validaHoraSemanal(document.frm.stHorasSemanais.value);" );

$obTxtValor = new Moeda;
$obTxtValor->setRotulo    ( "Valor"                        );
$obTxtValor->setName      ( "stValorPadrao"                );
$obTxtValor->setValue     ( $request->get("stValorPadrao") );
$obTxtValor->setTitle     ( "Informe o valor do padrão."   );
$obTxtValor->setNull      ( false                          );
$obTxtValor->setSize      ( 19                             );
$obTxtValor->setMaxLength ( 19                             );
$obTxtValor->obEvento->setOnChange    ( "validaValorPadrao(document.frm.stValorPadrao.value); calculaCorrecao(); buscaValor('recalcularProgressao') ");

$obTxtTipoNorma = new TextBox;
$obTxtTipoNorma->setRotulo    ( "Tipo de Norma"                                 );
$obTxtTipoNorma->setTitle     ( "Informe o tipo de norma para seleção da norma" );
$obTxtTipoNorma->setName      ( "inCodTipoNormaTxt"                             );
$obTxtTipoNorma->setValue     ( $inCodTipoNormaTxt                              );
$obTxtTipoNorma->setSize      ( 5                                               );
$obTxtTipoNorma->setMaxLength ( 5                                               );
$obTxtTipoNorma->setInteiro   ( true                                            );
$obTxtTipoNorma->setNull      ( false                                           );
$obTxtTipoNorma->obEvento->setOnChange ( "buscaValor('MontaNorma');"                     );

$obCmbTipoNorma = new Select;
$obCmbTipoNorma->setRotulo             ( "Tipo de Norma"             );
$obCmbTipoNorma->setName               ( "inCodTipoNorma"            );
$obCmbTipoNorma->setValue              ( $inCodTipoNorma             );
$obCmbTipoNorma->setStyle              ( "width: 200px"              );
$obCmbTipoNorma->setCampoID            ( "cod_tipo_norma"            );
$obCmbTipoNorma->setCampoDesc          ( "nom_tipo_norma"            );
$obCmbTipoNorma->addOption             ( "", "Selecione"             );
$obCmbTipoNorma->setNull               ( false                       );
$obCmbTipoNorma->preencheCombo         ( $rsTipoNorma                );
$obCmbTipoNorma->obEvento->setOnChange ( "buscaValor('MontaNorma');" );

$obTxtNorma = new TextBox;
$obTxtNorma->setRotulo   ( "Norma"                               );
$obTxtNorma->setTitle    ( "Informe a norma vinculada ao padrão" );
$obTxtNorma->setName     ( "inCodNormaTxt"                       );
$obTxtNorma->setValue    ( $request->get("inCodNormaTxt")        );
$obTxtNorma->setSize     ( 5                                     );
$obTxtNorma->setMaxLength( 5                                     );
$obTxtNorma->setInteiro  ( true                                  );
$obTxtNorma->setNull     ( false                                 );

$obCmbNorma = new Select;
$obCmbNorma->setRotulo   ( "Norma"         );
$obCmbNorma->setName     ( "inCodNorma"    );
$obCmbNorma->setValue    ( $request->get("inCodNorma") );
$obCmbNorma->setStyle    ( "width: 200px"  );
$obCmbNorma->setCampoID  ( "cod_norma"     );
$obCmbNorma->setCampoDesc( "nom_norma"     );
$obCmbNorma->addOption   ( "", "Selecione" );
$obCmbNorma->setNull     ( false           );
//$obCmbNorma->preencheCombo    ( $rsNorma        );

$obTxtDtVigencia = new Data;
$obTxtDtVigencia->setName  ( "dtVigencia"                  );
$obTxtDtVigencia->setValue ( $request->get("dtVigencia")    );
$obTxtDtVigencia->setRotulo( "Vigência"                    );
$obTxtDtVigencia->setNull  ( false                         );
$obTxtDtVigencia->setTitle ( 'Informe a data da vigência.' );

//Armazena o código do Intervalo para alteração
$obHdnIdProgressao = new Hidden;
$obHdnIdProgressao->setName( "inIdProgressao" );
$obHdnIdProgressao->setValue( "" );

$obTxtDescricaoProgessao = new TextBox;
$obTxtDescricaoProgessao->setRotulo        ( "Descrição da Progressão"      );
$obTxtDescricaoProgessao->setName          ( "stDescricaoNivel"             );
$obTxtDescricaoProgessao->setValue         ( ""                             );
$obTxtDescricaoProgessao->setTitle         ( "Informe a descrição do nível" );
$obTxtDescricaoProgessao->setSize          ( 80                             );
$obTxtDescricaoProgessao->setMaxLength     ( 80                             );
$obTxtDescricaoProgessao->setEspacosExtras ( false                          );

$obTxtValorProgressao = new TextBox;
$obTxtValorProgressao->setRotulo           ( "Valor de Correção"           );
$obTxtValorProgressao->setName             ( "stValorCorrecao"             );
$obTxtValorProgressao->setValue            ( ""                            );
$obTxtValorProgressao->setTitle            ( "Informe o valor de correção" );
$obTxtValorProgressao->setSize             ( 19                            );
$obTxtValorProgressao->setMaxLength        ( 19                            );
$obTxtValorProgressao->setFloat            ( true                          );

$obTxtPercentual = new Moeda;
$obTxtPercentual->setRotulo            ( "Percentual"                       );
$obTxtPercentual->setName              ( "stPercentual"                     );
$obTxtPercentual->setValue             ( ""                                 );
$obTxtPercentual->setTitle             ( "Informe o percentual da correção" );
$obTxtPercentual->setSize              ( 7                                  );
$obTxtPercentual->setMaxLength         ( 6                                  );
$obTxtPercentual->setFloat             ( true                               );
$obTxtPercentual->obEvento->setOnChange("validaPercentual( this ); calculaCorrecao(); ");

$obTxtMeses = new TextBox;
$obTxtMeses->setRotulo      ( "Meses para Incidência"                     );
$obTxtMeses->setName        ( "stMeses"                                   );
$obTxtMeses->setValue       ( ""                                          );
$obTxtMeses->setTitle       ( "Informe o número de meses para incidência" );
$obTxtMeses->setSize        ( 3                                           );
$obTxtMeses->setMaxLength   ( 3                                           );
$obTxtMeses->setInteiro     ( true                                        );

$obBtnOkey = new Button;
$obBtnOkey->setName              ( "btOkey"                             );
$obBtnOkey->setValue             ( "Incluir"                            );
$obBtnOkey->obEvento->setOnClick ( "buscaValor('incluirProgressao');" );

$obBtnOkeyAltera = new Button;
$obBtnOkeyAltera->setName              ( "btOkeyAltera"                             );
$obBtnOkeyAltera->setValue             ( "Alterar"                            );
$obBtnOkeyAltera->obEvento->setOnClick ( "buscaValor('alterarProgressao');" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btLimpar"            );
$obBtnLimpar->setValue             ( "Limpar"              );
$obBtnLimpar->obEvento->setOnClick ( "limparProgressao ()" );

$obSpnProgressao = new Span;
$obSpnProgressao->setId ( "spnListaProgressao" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addForm               ( $obForm                                );
$obFormulario->addTitulo             ( "Dados do padrão"                      );
$obFormulario->addHidden             ( $obHdnAcao                             );
$obFormulario->addHidden             ( $obHdnCtrl                             );
$obFormulario->addHidden             ( $obHdnCodPadrao                        );

if ($stAcao=='alterar') {
     $obFormulario->addComponente    ( $obLblDescricao                        );
     $obFormulario->addComponente    ( $obLblHorasMensais                     );
     $obFormulario->addComponente    ( $obLblHorasSemanais                    );
     $obFormulario->addHidden        ( $obHdnHorasMensais                     );
     $obFormulario->addHidden        ( $obHdnHorasSemanais                    );
     $obFormulario->addHidden        ( $obHdnDescricao                        );
} else {
     $obFormulario->addComponente    ( $obTxtDescricao                        );
     $obFormulario->addComponente    ( $obTxtHorasMensais                     );
     $obFormulario->addComponente    ( $obTxtHorasSemanais                    );
}
$obFormulario->addComponente         ( $obTxtValor                            );
$obFormulario->addComponenteComposto ( $obTxtTipoNorma, $obCmbTipoNorma       );
$obFormulario->addComponenteComposto ( $obTxtNorma, $obCmbNorma               );
$obFormulario->addComponente         ( $obTxtDtVigencia                       );
if ($stAcao != "alterar" || $rsFaixas->eof()) {
    $obFormulario->addTitulo             ( "Progressão"                           );
    $obFormulario->addHidden             ( $obHdnIdProgressao     );
    $obFormulario->addComponente         ( $obTxtDescricaoProgessao               );
    $obFormulario->addComponente         ( $obTxtPercentual                       );
    $obFormulario->addComponente         ( $obTxtValorProgressao                  );
    $obFormulario->addComponente         ( $obTxtMeses                            );
    $obFormulario->agrupaComponentes     ( array ($obBtnOkey, $obBtnOkeyAltera, $obBtnLimpar));
    $obFormulario->addSpan               ( $obSpnProgressao                       );
}

if ($stAcao == "incluir") {
    $obFormulario->OK   ();
    SistemaLegado::executaFramePrincipal ("f.stDescricao.focus();");
} else {
    $obFormulario->Cancelar ();
}

$obFormulario->show ();

?>

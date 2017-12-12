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
* Página de Formulário de inclusão/alteração do vale transporte
* Data de Criação: 08/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30922 $
$Name$
$Author: souzadl $
$Date: 2006-08-18 16:32:22 -0300 (Sex, 18 Ago 2006) $

* Casos de uso: uc-04.06.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioValeTransporte.class.php"      );
include_once ( CAM_GA_ADM_NEGOCIO."RConfiguracaoGeral.class.php"            );

SistemaLegado::exibirAjuda( "../../../../../Manuais/HTML/beneficios/UC-04.06.03/manUC-04.06.03.html" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterValeTransporte";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

$obRBeneficioValeTransporte = new RBeneficioValeTransporte;
$obRConfiguracaoGeral       = new RConfiguracaoGeral;
$rsFaixasDesconto = $rsMunicipioOrigem  = $rsMunicipioDestino = new RecordSet;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

$arSessaoLink = Sessao::read('link');
$stLocation = $pgList."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao']."&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"]."&stNomeEmpresaVT=".$_REQUEST['stNomeEmpresaVT'].$stFiltro;
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

if ($_REQUEST['stAcao'] == 'consultar' or $_REQUEST['stAcao'] == 'alterar') {
    $rsValeTransporte = new Recordset;
    $stFiltro = " AND vale_transporte.cod_vale_transporte = ".$_REQUEST['inCodValeTransporte'];
    $obRBeneficioValeTransporte->listarValeTransporte( $rsValeTransporte, $stFiltro );
    $inCodValeTransporte    = $rsValeTransporte->getCampo( 'cod_vale_transporte' );
    $inNumCGM               = $rsValeTransporte->getCampo( 'numcgm' );
    $stNomCGM               = $rsValeTransporte->getCampo( 'nom_cgm' );
    $inCodUFOrigem          = $rsValeTransporte->getCampo( 'uf_origem' );
    $inCodUFDestino         = $rsValeTransporte->getCampo( 'uf_destino' );
    $inCodMunicipioOrigem   = $rsValeTransporte->getCampo( 'municipio_origem' );
    $inCodMunicipioDestino  = $rsValeTransporte->getCampo( 'municipio_destino' );
    $inCodLinhaOrigem       = $rsValeTransporte->getCampo( 'cod_linha_origem' );
    $inCodLinhaDestino      = $rsValeTransporte->getCampo( 'cod_linha_destino' );
    $flCusto                = number_format($rsValeTransporte->getCampo( 'valor' ),2,',','.');
    $dtVigencia             = $rsValeTransporte->getCampo( 'inicio_vigencia' );
    $stUFOrigem             = $rsValeTransporte->getCampo( 'sigla_uf_o' );
    $stUFDestino            = $rsValeTransporte->getCampo( 'sigla_uf_d' );
    $stNomUFOrigem          = $rsValeTransporte->getCampo( 'nom_uf_o' );
    $stNomUFDestino         = $rsValeTransporte->getCampo( 'nom_uf_d' );
    $stMunicipioOrigem      = $rsValeTransporte->getCampo( 'nom_municipio_o' );
    $stMunicipioDestino     = $rsValeTransporte->getCampo( 'nom_municipio_d' );
    $stLinhaOrigem          = $rsValeTransporte->getCampo( 'origem' );
    $stLinhaDestino         = $rsValeTransporte->getCampo( 'destino' );
    if ($_REQUEST['stAcao'] == 'alterar') {
        sistemaLegado::executaFrameOculto("buscaDado('preencheCamposAlteracao');");
    }
} else {
    $obRConfiguracaoGeral->consultarConfiguracaoGeral( $rsRecordSet );
    while ( !$rsRecordSet->eof() ) {
        if ( $rsRecordSet->getCampo('parametro') == 'cod_uf' ) {
            $inCodUFOrigem = $inCodUFDestino = $rsRecordSet->getCampo('valor');
        }
        if ( $rsRecordSet->getCampo('parametro') == 'cod_municipio' ) {
            $inCodMunicipioOrigem = $inCodMunicipioDestino = $rsRecordSet->getCampo('valor');
//             sistemaLegado::executaFrameOculto("preencheMunicipioIncluir();");
        }
        $rsRecordSet->proximo();
    }
}
//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( ""       );

//Define o objeto INNER para armazenar a empresa fornecedora
if ($_REQUEST['stAcao'] == 'incluir') {
    include_once CAM_GP_COM_COMPONENTES.'IPopUpFornecedor.class.php';

    //Define componente para Fornecedor
    $obBscEmpresaTransporte = new IPopUpFornecedor($obForm);
    $obBscEmpresaTransporte->setNull(false);
    $obBscEmpresaTransporte->obCampoCod->setValue($inNumCGM);
    $obBscEmpresaTransporte->obCampoCod->setName('inNumCGM');
    $obBscEmpresaTransporte->obCampoCod->obEvento->setOnBlur("buscaCGM('buscaCGM');");

    //UF ORIGEM
    $obTxtCodUFOrigem = new TextBox;
    $obTxtCodUFOrigem->setRotulo             ( "UF de Origem"                       );
    $obTxtCodUFOrigem->setName               ( "inCodUFOrigem"                      );
    $obTxtCodUFOrigem->setValue              ( $inCodUFOrigem                       );
    $obTxtCodUFOrigem->setTitle              ( "Informe a UF origem do itinerário"  );
    $obTxtCodUFOrigem->setSize               ( 7                                    );
    $obTxtCodUFOrigem->setMaxLength          ( 7                                    );
    $obTxtCodUFOrigem->setInteiro            ( true                                 );
    $obTxtCodUFOrigem->setNull               ( false                                );
    $obTxtCodUFOrigem->obEvento->setOnChange ( "preencheMunicipioOrigem();"         );

    $obRBeneficioValeTransporte->obRBeneficioItinerario->listarUF( $rsUFOrigem );
    $obCmbUFOrigem = new Select;
    $obCmbUFOrigem->setName                  ( "stNomeUFOrigem"                     );
    $obCmbUFOrigem->setValue                 ( $inCodUFOrigem                       );
    $obCmbUFOrigem->setRotulo                ( "UF de Origem"                       );
    $obCmbUFOrigem->setTitle                 ( "Informe a UF origem do itinerário"  );
    $obCmbUFOrigem->setNull                  ( false                                );
    $obCmbUFOrigem->setCampoId               ( "[cod_uf]"                           );
    $obCmbUFOrigem->setCampoDesc             ( "nom_uf"                             );
    $obCmbUFOrigem->addOption                ( "", "Selecione"                      );
    $obCmbUFOrigem->preencheCombo            ( $rsUFOrigem                          );
    $obCmbUFOrigem->setStyle                 ( "width: 200px"                       );
    $obCmbUFOrigem->obEvento->setOnChange    ( "preencheMunicipioOrigem();"         );

    //MUNICIPIO ORIGEM
    $obTxtCodMunicipioOrigem = new TextBox;
    $obTxtCodMunicipioOrigem->setRotulo      ( "Município de Origem"                        );
    $obTxtCodMunicipioOrigem->setName        ( "inCodMunicipioOrigem"                       );
    $obTxtCodMunicipioOrigem->setValue       ( $inCodMunicipioOrigem                        );
    $obTxtCodMunicipioOrigem->setTitle       ( "Informe o município origem do itinerário"   );
    $obTxtCodMunicipioOrigem->setSize        ( 7                                            );
    $obTxtCodMunicipioOrigem->setMaxLength   ( 7                                            );
    $obTxtCodMunicipioOrigem->setInteiro     ( true                                         );

    // $obRBeneficioValeTransporte->listarMunicipio( $rsMunicipioOrigem );
    $obCmbMunicipioOrigem = new Select;
    $obCmbMunicipioOrigem->setName           ( "stNomeMunicipioOrigem"                      );
    $obCmbMunicipioOrigem->setValue          ( $inCodMunicipioOrigem                        );
    $obCmbMunicipioOrigem->setTitle          ( "Informe o município origem do itinerário"   );
    $obCmbMunicipioOrigem->setNull           ( false                                        );
    $obCmbMunicipioOrigem->setCampoId        ( "[cod_municipio]"                            );
    $obCmbMunicipioOrigem->setCampoDesc      ( "nom_municipio"                              );
    $obCmbMunicipioOrigem->addOption         ( "", "Selecione"                              );
    $obCmbMunicipioOrigem->preencheCombo     ( $rsMunicipioOrigem                           );
    $obCmbMunicipioOrigem->setStyle          ( "width: 200px"                               );

    //UF DESTINO
    $obTxtCodUFDestino = new TextBox;
    $obTxtCodUFDestino->setRotulo             ( "UF de Destino"                         );
    $obTxtCodUFDestino->setName               ( "inCodUFDestino"                        );
    $obTxtCodUFDestino->setValue              ( $inCodUFDestino                         );
    $obTxtCodUFDestino->setTitle              ( "Informe a UF destino do itinerário"    );
    $obTxtCodUFDestino->setSize               ( 7                                       );
    $obTxtCodUFDestino->setMaxLength          ( 7                                       );
    $obTxtCodUFDestino->setInteiro            ( true                                    );
    $obTxtCodUFDestino->obEvento->setOnChange ( "preencheMunicipioDestino();"           );

    $obRBeneficioValeTransporte->obRBeneficioItinerario->listarUF( $rsUFDestino );
    $obCmbUFDestino = new Select;
    $obCmbUFDestino->setName                 ( "stNomeUFDestino"                        );
    $obCmbUFDestino->setValue                ( $inCodUFDestino                          );
    $obCmbUFDestino->setRotulo               ( "UF de Destino"                          );
    $obCmbUFDestino->setTitle                ( "Informe a UF destino do itinerário"     );
    $obCmbUFDestino->setNull                 ( false                                    );
    $obCmbUFDestino->setCampoId              ( "cod_uf"                                 );
    $obCmbUFDestino->setCampoDesc            ( "nom_uf"                                 );
    $obCmbUFDestino->addOption               ( "", "Selecione"                          );
    $obCmbUFDestino->preencheCombo           ( $rsUFDestino                             );
    $obCmbUFDestino->setStyle                ( "width: 200px"                           );
    $obCmbUFDestino->obEvento->setOnChange   ( "preencheMunicipioDestino();"            );

    //MUNICIPIO DESTINO
    $obTxtCodMunicipioDestino = new TextBox;
    $obTxtCodMunicipioDestino->setRotulo    ( "Município de Destino"                        );
    $obTxtCodMunicipioDestino->setName      ( "inCodMunicipioDestino"                       );
    $obTxtCodMunicipioDestino->setValue     ( $inCodMunicipioDestino                        );
    $obTxtCodMunicipioDestino->setTitle     ( "Informe o município destino do itinerário"   );
    $obTxtCodMunicipioDestino->setSize      ( 7                                             );
    $obTxtCodMunicipioDestino->setMaxLength ( 7                                             );
    $obTxtCodMunicipioDestino->setInteiro   ( true                                          );

    // $obRBeneficioValeTransporte->listarMunicipio( $rsMunicipioDestino );
    $obCmbMunicipioDestino = new Select;
    $obCmbMunicipioDestino->setName         ( "stNomeMunicipioDestino"                      );
    $obCmbMunicipioDestino->setRotulo       ( "Município de Destino"                        );
    $obCmbMunicipioDestino->setValue        ( $inCodMunicipioDestino                        );
    $obCmbMunicipioDestino->setTitle        ( "Informe o município destino do itinerário"   );
    $obCmbMunicipioDestino->setNull         ( false                                         );
    $obCmbMunicipioDestino->setCampoId      ( "cod_municipio"                               );
    $obCmbMunicipioDestino->setCampoDesc    ( "nom_municipio"                               );
    $obCmbMunicipioDestino->addOption       ( "", "Selecione"                               );
    $obCmbMunicipioDestino->preencheCombo   ( $rsMunicipioDestino                           );
    $obCmbMunicipioDestino->setStyle        ( "width: 200px"                                );

    //Linha de Origem
    $obTxtCodLinhaOrigem = new TextBox;
    $obTxtCodLinhaOrigem->setRotulo    ( "Linha de Origem"          );
    $obTxtCodLinhaOrigem->setName      ( "inCodLinhaOrigem"         );
    $obTxtCodLinhaOrigem->setValue     ( $inCodLinhaOrigem          );
    $obTxtCodLinhaOrigem->setTitle     ( "Informe a linha origem"   );
    $obTxtCodLinhaOrigem->setSize      ( 7                          );
    $obTxtCodLinhaOrigem->setMaxLength ( 7                          );
    $obTxtCodLinhaOrigem->setInteiro   ( true                       );

    $obRBeneficioValeTransporte->obRBeneficioLinha->listarLinha( $rsLinhaOrigem,$stFiltro=""," ORDER BY descricao" );
    $obCmbLinhaOrigem = new Select;
    $obCmbLinhaOrigem->setName         ( "stNomeLinhaOrigem"        );
    $obCmbLinhaOrigem->setRotulo       ( "Linha de Origem"          );
    $obCmbLinhaOrigem->setValue        ( $inCodLinhaOrigem          );
    $obCmbLinhaOrigem->setTitle        ( "Informe a linha origem"   );
    $obCmbLinhaOrigem->setNull         ( false                      );
    $obCmbLinhaOrigem->setCampoId      ( "cod_linha"                );
    $obCmbLinhaOrigem->setCampoDesc    ( "descricao"                );
    $obCmbLinhaOrigem->addOption       ( "", "Selecione"            );
    $obCmbLinhaOrigem->preencheCombo   ( $rsLinhaOrigem             );
    $obCmbLinhaOrigem->setStyle        ( "width: 200px"             );

    //Linha de Destino
    $obTxtCodLinhaDestino = new TextBox;
    $obTxtCodLinhaDestino->setRotulo    ( "Linha de Destino"         );
    $obTxtCodLinhaDestino->setName      ( "inCodLinhaDestino"        );
    $obTxtCodLinhaDestino->setValue     ( $inCodLinhaDestino         );
    $obTxtCodLinhaDestino->setTitle     ( "Informe a linha destino"  );
    $obTxtCodLinhaDestino->setSize      ( 7                          );
    $obTxtCodLinhaDestino->setMaxLength ( 7                          );
    $obTxtCodLinhaDestino->setInteiro   ( true                       );

    $obRBeneficioValeTransporte->obRBeneficioLinha->listarLinha( $rsLinhaDestino,$stFiltro=""," ORDER BY descricao" );
    $obCmbLinhaDestino = new Select;
    $obCmbLinhaDestino->setName         ( "stNomeLinhaDestino"       );
    $obCmbLinhaDestino->setRotulo       ( "Linha de Destino"         );
    $obCmbLinhaDestino->setValue        ( $inCodLinhaDestino         );
    $obCmbLinhaDestino->setTitle        ( "Informe a linha destino"  );
    $obCmbLinhaDestino->setNull         ( false                      );
    $obCmbLinhaDestino->setCampoId      ( "cod_linha"                );
    $obCmbLinhaDestino->setCampoDesc    ( "descricao"                );
    $obCmbLinhaDestino->addOption       ( "", "Selecione"            );
    $obCmbLinhaDestino->preencheCombo   ( $rsLinhaDestino            );
    $obCmbLinhaDestino->setStyle        ( "width: 200px"             );

} else {
    $obHdnCodValeTransporte = new Hidden;
    $obHdnCodValeTransporte->setName     ( "inCodValeTransporte" );
    $obHdnCodValeTransporte->setValue    ( $inCodValeTransporte  );

    $obHdnNumCGM = new Hidden;
    $obHdnNumCGM->setName     ( "inNumCGM" );
    $obHdnNumCGM->setValue    ( $inNumCGM  );

    $obLblEmpresaTransporte = new Label;
    $obLblEmpresaTransporte->setName             ( "stEmpresaTransporte"                    );
    $obLblEmpresaTransporte->setRotulo           ( "Fornecedor"                             );
    $obLblEmpresaTransporte->setValue            ( $inNumCGM . " - " . $stNomCGM            );

    //UF ORIGEM
    $obLblUFOrigem = new Label;
    $obLblUFOrigem->setName               ( "stUFOrigem"                         );
    $obLblUFOrigem->setRotulo             ( "UF de Origem"                       );
    $obLblUFOrigem->setValue              ( $inCodUFOrigem . " - " .$stUFOrigem  );

    //MUNICIPIO ORIGEM
    $obLblMunicipioOrigem = new Label;
    $obLblMunicipioOrigem->setName        ( "stMunicipioOrigem"                         );
    $obLblMunicipioOrigem->setRotulo      ( "Município de Origem"                       );
    $obLblMunicipioOrigem->setValue       ( $inCodMunicipioOrigem . " - " . $stMunicipioOrigem );

    //UF DESTINO
    $obLblUFDestino = new Label;
    $obLblUFDestino->setName               ( "stUFDestino"                          );
    $obLblUFDestino->setRotulo             ( "UF de Destino"                        );
    $obLblUFDestino->setValue              ( $inCodUFDestino . " - " . $stUFDestino );

    //MUNICIPIO DESTINO
    $obLblMunicipioDestino = new Label;
    $obLblMunicipioDestino->setName        ( "stMunicipioDestino"                         );
    $obLblMunicipioDestino->setRotulo      ( "Município de Destino"                       );
    $obLblMunicipioDestino->setValue       ( $inCodMunicipioDestino ." - ".$stMunicipioDestino );

    //Linha de Origem
    $obLblLinhaOrigem = new Label;
    $obLblLinhaOrigem->setRotulo    ( "Linha de Origem"       );
    $obLblLinhaOrigem->setName      ( "stLinhaOrigem"         );
    $obLblLinhaOrigem->setValue     ( $inCodLinhaOrigem ." - ".$stLinhaOrigem );

    //Linha de Destino
    $obLblLinhaDestino = new Label;
    $obLblLinhaDestino->setRotulo    ( "Linha de Destino"       );
    $obLblLinhaDestino->setName      ( "stLinhaDestino"         );
    $obLblLinhaDestino->setValue     ( $inCodLinhaDestino ." - ". $stLinhaDestino );

    $obSpanCusto = new Span;
    $obSpanCusto->setId ( "listaCustos" );

//     $js = "buscaValor('listaCustos');";
    Sessao::write("inCodValeTransporte", $inCodValeTransporte);
    Sessao::write("inNumCGM", $inNumCGM);
//     sistemaLegado::executaFrameOculto($js);
}

$obTxtCustoUnitario = new Moeda;
$obTxtCustoUnitario->setRotulo          ( "Custo Unitário"                      );
$obTxtCustoUnitario->setName            ( "flCusto"                             );
$obTxtCustoUnitario->setTitle           ( "Informe o custo unitário do vale"    );
$obTxtCustoUnitario->setNull            ( false                                 );
$obTxtCustoUnitario->setMaxLength       ( 10                                    );

$obDtVigencia = new Data;
if ($_REQUEST['stAcao'] == 'incluir') {
    $obDtVigencia->setRotulo                ( "Vigência do Custo"                               );
} else {
    $obDtVigencia->setRotulo                ( "Data da Alteração"                               );
}
$obDtVigencia->setName                  ( "dtVigencia"                                      );
$obDtVigencia->setTitle                 ( "Informe a vigência inicial do custo deste vale"  );
$obDtVigencia->setNull                  ( false                                             );

$obSpanFaixasDesconto = new Span;
$obSpanFaixasDesconto->setId ( "listaFaixaDesconto" );

sistemaLegado::executaFrameOculto("buscaValor('preencheSpans');");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden        ( $obHdnCtrl                );
$obFormulario->addHidden        ( $obHdnAcao                );

if ($_REQUEST['stAcao'] == 'incluir') {
    $obFormulario->addTitulo        ( "Fornecedor de Vale-Transporte"     );
    $obFormulario->addComponente    ( $obBscEmpresaTransporte   );

    $obFormulario->addTitulo        ( "Itinerário"              );
    $obFormulario->addComponenteComposto ( $obTxtCodUFOrigem         , $obCmbUFOrigem         );
    $obFormulario->addComponenteComposto ( $obTxtCodMunicipioOrigem  , $obCmbMunicipioOrigem  );
    $obFormulario->addComponenteComposto ( $obTxtCodUFDestino        , $obCmbUFDestino        );
    $obFormulario->addComponenteComposto ( $obTxtCodMunicipioDestino , $obCmbMunicipioDestino );
    $obFormulario->addComponenteComposto ( $obTxtCodLinhaOrigem      , $obCmbLinhaOrigem      );
    $obFormulario->addComponenteComposto ( $obTxtCodLinhaDestino     , $obCmbLinhaDestino     );

    $obFormulario->addTitulo        ( "Características do Vale" );
    $obFormulario->addComponente    ( $obTxtCustoUnitario       );
    $obFormulario->addComponente    ( $obDtVigencia             );

    $obFormulario->addSpan          ( $obSpanFaixasDesconto     );

    $obFormulario->OK();
} elseif ($_REQUEST['stAcao'] == 'alterar') {
    $obFormulario->addHidden        ( $obHdnCodValeTransporte   );
    $obFormulario->addHidden        ( $obHdnNumCGM              );
    $obFormulario->addTitulo        ( "Fornecedor de Vale-Transporte"     );
    $obFormulario->addComponente    ( $obLblEmpresaTransporte   );

    $obFormulario->addTitulo        ( "Itinerário"              );
    $obFormulario->addComponente    ( $obLblUFOrigem            );
    $obFormulario->addComponente    ( $obLblMunicipioOrigem     );
    $obFormulario->addComponente    ( $obLblUFDestino           );
    $obFormulario->addComponente    ( $obLblMunicipioDestino    );
    $obFormulario->addComponente    ( $obLblLinhaOrigem         );
    $obFormulario->addComponente    ( $obLblLinhaDestino        );

    $obFormulario->addTitulo        ( "Correção de Custo"       );
    $obFormulario->addComponente    ( $obDtVigencia             );
    $obFormulario->addComponente    ( $obTxtCustoUnitario       );

    $obFormulario->addSpan          ( $obSpanCusto              );

    $obFormulario->addSpan          ( $obSpanFaixasDesconto     );

    $arSessaoLink = Sessao::read('link');
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao."&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"]."&stNomeEmpresaVT=".$_REQUEST['stNomeEmpresaVT'] );
} else {
    $obFormulario->addTitulo        ( "Fornecedor de Vale-Transporte"     );
    $obFormulario->addComponente    ( $obLblEmpresaTransporte   );

    $obFormulario->addTitulo        ( "Itinerário"              );
    $obFormulario->addComponente    ( $obLblUFOrigem            );
    $obFormulario->addComponente    ( $obLblMunicipioOrigem     );
    $obFormulario->addComponente    ( $obLblUFDestino           );
    $obFormulario->addComponente    ( $obLblMunicipioDestino    );
    $obFormulario->addComponente    ( $obLblLinhaOrigem         );
    $obFormulario->addComponente    ( $obLblLinhaDestino        );

    $obFormulario->addSpan          ( $obSpanCusto              );

    $obFormulario->addSpan          ( $obSpanFaixasDesconto     );

    $obVoltar = new Button;
    $obVoltar->setName  ( "Voltar" );
    $obVoltar->setValue ( "Voltar" );
    $obVoltar->setStyle ( "width: 80px" );
    $obVoltar->obEvento->setOnClick("Cancelar('$stLocation');");

    $obFormulario->defineBarra          ( array($obVoltar) );
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

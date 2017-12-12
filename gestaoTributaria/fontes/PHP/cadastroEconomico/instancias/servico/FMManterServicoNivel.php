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
    * Página de Formulario de Inclusao/Alteracao de Serviços
    * Data de Criação   : 22/11/2004

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: FMManterServicoNivel.php 63839 2015-10-22 18:08:07Z franver $

    *Casos de uso: uc-05.02.03

*/

/*
$Log$
Revision 1.17  2006/09/15 14:33:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMServico.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."MontaServico.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterServico";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

include_once ($pgJS);

$obRCEMServico = new RCEMServico;
$obMontaServico = new MontaServico;
$rsUltimoNivel = new RecordSet;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//DEFINICAO DAS VERIFICACOES
if ($stAcao == "incluir") {
    $arChaveNivel = preg_split( "/-/", $_REQUEST["stChaveNivel"] );
    $inCodigoVigencia = $arChaveNivel[0];
    $inCodigoNivel    = $arChaveNivel[1];

    $obRCEMServico->setCodigoVigencia ( $inCodigoVigencia );
    $obRCEMServico->setCodigoNivel    ( $inCodigoNivel    );

    $obMontaServico->setCodigoVigenciaServico ( $inCodigoVigencia );
    $obMontaServico->setCodigoNivelServico    ( $inCodigoNivel    );
    $obRCEMServico->consultarNivel();
} else {
    $inCodigoVigencia    = $_REQUEST["inCodigoVigencia"];
    $inCodigoNivel       = $_REQUEST["inCodigoNivel"];
    $inCodigoServico     = $_REQUEST["inCodigoServico"];
    $stValorComposto     = $_REQUEST["stValorComposto"];
    $stValorReduzido     = $_REQUEST["stValorReduzido"];

    $obRCEMServico->setCodigoVigencia ( $inCodigoVigencia );
    $obRCEMServico->setCodigoNivel    ( $inCodigoNivel    );
    $obRCEMServico->setCodigoServico  ( $inCodigoServico  );

    $obMontaServico->setCodigoVigenciaServico( $inCodigoVigencia    );
    $obMontaServico->setCodigoNivelServico   ( $inCodigoNivel       );
    $obMontaServico->setCodigoServico        ( $inCodigoServico     );
    $obMontaServico->setValorCompostoServico ( $stValorComposto     );
    $obRCEMServico->consultarServico();
}

// VERIFICA SE E O ULTIMO NIVEL
$obErro = $obRCEMServico->listarNiveisPosteriores( $rsUltimoNivel );
if ( $rsUltimoNivel->getCampo(cod_nivel) == "" ) {
    $boUltimoNivel = true;
} else {
    $boUltimoNivel = false;
}

$stNomeNivel        = $obRCEMServico->getNomeNivel();
$obRCEMServico->geraMascara( $stMascara );

$x = explode ('.', $_REQUEST["stValorComposto"]);
$inValorServico =  $x[ $_REQUEST["inCodigoNivel"] - 1 ];
$stValorComposto    = substr( $stValorComposto, 0, strlen( $stValorComposto ) - ( strlen( $inValorServico )+1));

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnCodigoNivel = new Hidden;
$obHdnCodigoNivel->setName  ( "inCodigoNivel" );
$obHdnCodigoNivel->setValue ( $inCodigoNivel  );

$obHdnCodigoVigencia = new Hidden;
$obHdnCodigoVigencia->setName  ( "inCodigoVigencia" );
$obHdnCodigoVigencia->setvalue ( $inCodigoVigencia );

if ($stAcao == 'aliquota') {
    $obHdnNomeServico = new Hidden;
    $obHdnNomeServico->setName  ( "stNomeServico" );
    $obHdnNomeServico->setvalue ( $_REQUEST["stNomeServico"] );
}

$obHdnDtVigencia = new Hidden;
$obHdnDtVigencia->setName  ( "dtDataInicio" );
$obHdnDtVigencia->setvalue ( $_REQUEST["dtDataInicio"] );

$obHdnDtVigenciaAntiga = new Hidden;
$obHdnDtVigenciaAntiga->setName  ( "dtVigenciaAntiga" );
$obHdnDtVigenciaAntiga->setvalue ( $_REQUEST["dtDataInicio"] );

$obHdnChaveServico = new Hidden;
$obHdnChaveServico->setName  ( "stChaveServico" );
$obHdnChaveServico->setValue ( $_REQUEST['stValorComposto']  );

$obHdnValorReduzido = new Hidden;
$obHdnValorReduzido->setName  ( "stValorReduzido" );
$obHdnValorReduzido->setValue ( $stValorReduzido  );

$obHdnValorServicoAntigo = new Hidden;
$obHdnValorServicoAntigo->setName  ( "stValorServicoAntigo" );
$obHdnValorServicoAntigo->setValue ( ltrim ($inValorServico, '0')  );

$obLblCodigoServico = new Label;
$obLblCodigoServico->setName  ( "inValorServico" );
$obLblCodigoServico->setValue ( $inCodigoServico  );
$obLblCodigoServico->setRotulo( "Código" );

$obLblNomeServico = new Label;
$obLblNomeServico->setName  ( "stNomeServico" );
$obLblNomeServico->setValue ( $_REQUEST["stNomeServico"]  );
$obLblNomeServico->setRotulo( "Nome" );

$obLblAliquota = new Label;
$obLblAliquota->setRotulo( "Alíquota" );
$obLblAliquota->setName ( "stLBAliquota" );
$obLblAliquota->setValue( $_REQUEST["flAliquota"]  );

$obTxtCodigoServico = new TextBox;
$obTxtCodigoServico->setName      ( "inValorServico" );
$obTxtCodigoServico->setRotulo    ( "Código"           );
$obTxtCodigoServico->setNull      ( false                 );
$obTxtCodigoServico->setInteiro   ( true                 );
$obTxtCodigoServico->setMaxLength ( strlen( $stMascara )   );
$obTxtCodigoServico->setSize      ( strlen( $stMascara )   );
//$obTxtCodigoServico->setMascara   ( $stMascara          );
//$obTxtCodigoServico->setValidaCaracteres      ( true    );
$obTxtCodigoServico->setValue     ( ltrim ($inValorServico, '0')   );

$obTxtServico = new TextBox;
$obTxtServico->setName      ( "stNomeServico"     );
$obTxtServico->setRotulo    ( "Nome do Serviço"   );
$obTxtServico->setMaxLength ( 240                 );
$obTxtServico->setSize      ( 40                  );
$obTxtServico->setValue     ( $_REQUEST["stNomeServico"]      );
$obTxtServico->setNull      ( false );

$obDtDataIncio = new Data;
$obDtDataIncio->setName  ( "dtInicio" );
$obDtDataIncio->setRotulo( "Data de Início" );
$obDtDataIncio->setValue ( $_REQUEST["dtDataInicio"]  );
$obDtDataIncio->setNull  ( false );

$flAliquota = str_replace( '.', ',', $_REQUEST["flAliquota"] );
$obTxtAliquota = new Moeda;
$obTxtAliquota->setRotulo          ( "Alíquota" );
$obTxtAliquota->setName            ( "flAliquota" );
$obTxtAliquota->setValue           ( $flAliquota  );
$obTxtAliquota->setTitle           ( "Alíquota cobrada sobre o serviço" );
$obTxtAliquota->setNull            ( false );
$obTxtAliquota->setMaxLength       ( 10    );

//DEFINICAO DOS COMPONENTES PARA ALTERAÇÃO
$obLblValorComposto = new Label;
$obLblValorComposto->setRotulo ( "Nível Superior" );
$obLblValorComposto->setValue  ( $stValorComposto );

$obHdnCodigoServico = new Hidden;
$obHdnCodigoServico->setName  ( "inCodServico" );
$obHdnCodigoServico->setValue ( $inCodigoServico  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto"     );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.02.03");
$obFormulario->addTitulo     ( "Dados para Serviço" );

$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnCodigoNivel );
if ($stAcao == 'aliquota') {
    $obFormulario->addHidden ( $obHdnNomeServico );
}
$obFormulario->addHidden ( $obHdnDtVigencia );
$obFormulario->addHidden ( $obHdnDtVigenciaAntiga );
$obFormulario->addHidden ( $obHdnCodigoVigencia );

if ($stAcao == "incluir") {
    if ($_GET["stValorComposto"]) {
        $obMontaServico->setValorCompostoServico(  $_GET["stValorComposto"] );
        $obMontaServico->geraFormularioPreenchido( $obFormulario  );
    } else {
        $obMontaServico->setCadastroServico( true );
        $obMontaServico->geraFormulario( $obFormulario  );
    }
} else {
    $obFormulario->addHidden ($obHdnValorServicoAntigo);

    $obFormulario->addHidden( $obHdnCodigoServico );
    $obFormulario->addHidden    ( $obHdnChaveServico );
    $obFormulario->addHidden    ( $obHdnValorReduzido );
    if ($boUltimoNivel) {
        $obFormulario->addComponente ( $obLblValorComposto);
    }
}

if ($stAcao == 'aliquota') {
    $obFormulario->addHidden     ( $obHdnNomeServico      );
    $obFormulario->addHidden     ( $obHdnDtVigenciaAntiga );
    $obFormulario->addComponente ( $obLblCodigoServico );
    $obFormulario->addComponente ( $obLblNomeServico   );
} else {
    $obFormulario->addComponente ( $obTxtCodigoServico );
    $obFormulario->addComponente ( $obTxtServico   );
}

if ($stAcao == 'aliquota') {
    $obFormulario->addComponente( $obDtDataIncio );
} else {
    $obFormulario->addHidden( $obHdnDtVigencia );
}
if ($boUltimoNivel && $stAcao == 'alterar') {
    $obFormulario->addComponente( $obLblAliquota );
}

if ($boUltimoNivel && $stAcao != 'alterar') {
    $obFormulario->addComponente ( $obTxtAliquota      );
}

if ($stAcao == "incluir") {
    $obFormulario->OK();
} else {
    $obFormulario->Cancelar();
}
$obFormulario->show();
?>

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
    * Página de Formulario de Inclusao/Alteração de Níveis
    * Data de Criação   : 20/11/2004

    * @author Tonismar Régis Bernardo
    * @author Desenvolvedor: Lizandro Kirst da Silva

    * @ignore

    * $Id: FMManterHierarquia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.02

*/

/*
$Log$
Revision 1.7  2006/09/15 14:32:55  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNivelServico.class.php"       );

$stAcao = $request->get('stAcao');
$link = Sessao::read( "link" );
$stLink = "?".Sessao::getId()."&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterHierarquia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php".$stLink;
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRCEMNivelServico = new RCEMNivelServico;
$rsUltimoNivel        = new RecordSet;
$rsListaVigencia      = new RecordSet;
$rsVigenciaAtual     = new RecordSet;

$obHdnAcao =  new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnNivel = new Hidden;
$obHdnNivel->setName  ( "inCodigoNivel" );
$obHdnNivel->setValue ( $_REQUEST["inCodigoNivel"]  );

if ($stAcao == "incluir") {
    $obRCEMNivelServico->recuperaUltimoNivel    ( $rsUltimoNivel );
    $stNivelSuperior = $rsUltimoNivel->getCampo( "nom_nivel" );
} elseif ($stAcao == "alterar") {
    $inCodigoVigencia = $_REQUEST["inCodigoVigencia"];
    $inCodigoNivel    = $_REQUEST["inCodigoNivel"];

    $obHdnVigencia = new Hidden;
    $obHdnVigencia->setName  ( "inCodigoVigencia" );
    $obHdnVigencia->setValue ( $inCodigoVigencia  );

    $obRCEMNivelServico->setCodigoVigencia ( $inCodigoVigencia );
    $obRCEMNivelServico->setCodigoNivel    ( $inCodigoNivel    );
    $obRCEMNivelServico->consultarNivel();
    $stNomeNivel = $obRCEMNivelServico->getNomeNivel();
    $stMascaraNivel = $obRCEMNivelServico->getMascara();
    $obRCEMNivelServico->recuperaUltimoNivel(  $rsUltimoNivel );
    $stNivelSuperior = $rsUltimoNivel->getCampo( "nom_nivel" );
}
//DEFINICAO DOS COMPONENTES

if ($stAcao == "incluir") {
    $obRCEMNivelServico->recuperaVigenciaAtual ( $rsVigenciaAtual );
    $stVigenciaAtual = $rsVigenciaAtual->getCampo( "cod_vigencia" );
    $obRCEMNivelServico->recuperaUltimoNivel( $rsUltimoNivel );
    $stNivelSuperior = $rsUltimoNivel->getCampo( "nom_nivel" );

    $obRCEMNivelServico->setCodigoVigencia("");
    $obRCEMNivelServico->listarVigencia( $rsListaVigencia );
//    $obRCEMNivelServico->setInicioVigencia( $rsVigenciaAtual->getCampo( "dtinicio" ) );

    if ($_REQUEST["inCodigoVigencia"]) {
        $inCount = 0;
        while (!$rsListaVigencia->eof()) {
            if ($rsListaVigencia->getCampo('cod_vigencia') == $_REQUEST["inCodigoVigencia"]) {
                $inLinhaVigencia = $inCount; // key($rsListaVigencia->arElementos);
            }
            $rsListaVigencia->proximo();
        $inCount++;
        }
    }
    $rsListaVigencia->ordena( 'cod_vigencia', 'DESC');
    $rsListaVigencia->setPrimeiroElemento();
    $obCmbDataVigencia = new Select;
    $obCmbDataVigencia->setRotulo    ( "Vigência"        );
    $obCmbDataVigencia->addOption    ( "", "Selecione"   );
    $obCmbDataVigencia->setCampoId   ( "cod_vigencia"    );
    $obCmbDataVigencia->setCampoDesc ( "dt_inicio"       );
    $obCmbDataVigencia->setStyle     ( "width:150px"     );
    $obCmbDataVigencia->setNull      ( false             );
    $obCmbDataVigencia->setName      ( "inCodigoVigencia");
    $obCmbDataVigencia->setId        ( $stCodigoVigencia );
    $obCmbDataVigencia->setValue     ( $stVigenciaAtual );
    $obCmbDataVigencia->preencheCombo( $rsListaVigencia  );
    $obCmbDataVigencia->obEvento->setOnChange("buscaValor('UltimoNivel')");
} elseif ($stAcao == "alterar") {
    $obRCEMNivelServico->consultarNivel();
    $stNomeNivel = $obRCEMNivelServico->getNomeNivel();
    $stMascaraNivel = $obRCEMNivelServico->getMascara();
    $obRCEMNivelServico->recuperaUltimoNivel(  $rsUltimoNivel );
    $stNivelSuperior = $rsUltimoNivel->getCampo( "nom_nivel" );

    $obLblVigencia = new Label;
    $obLblVigencia->setName   ( "stVigencia" );
    $obLblVigencia->setRotulo ( "Vigência" );
    $obLblVigencia->setValue  ( $inCodigoVigencia."-".$_REQUEST["dtInicioVigencia"] );

    $obLblCodigoNivel = new Label;
    $obLblCodigoNivel->setName   ( "stCodigoNivel" );
    $obLblCodigoNivel->setRotulo ( "Código do Nível" );
    $obLblCodigoNivel->setValue  ( $inCodigoNivel );
}

$obTxtNomeNivel = new TextBox;
$obTxtNomeNivel->setName         ( "stNomeNivel" );
$obTxtNomeNivel->setSize         ( 40 );
$obTxtNomeNivel->setMaxLength    ( 80 );
$obTxtNomeNivel->setNull         ( false );
$obTxtNomeNivel->setRotulo       ( "Nome" );
$obTxtNomeNivel->setValue        ( $stNomeNivel );

$obLblNivelSuperior = new Label;
$obLblNivelSuperior->setName    ( "stNivelSuperior" );
$obLblNivelSuperior->setId    ( "stNivelSuperior" );
$obLblNivelSuperior->setRotulo  ( "Nível Superior" );
$obLblNivelSuperior->setValue   ( $stNivelSuperior );

$obTxtMascara = new TextBox;
$obTxtMascara->setName        ( "stMascaraNivel" );
$obTxtMascara->setSize        ( 40 );
$obTxtMascara->setAcento      ( false );
$obTxtMascara->setAlfaNumerico( true );
$obTxtMascara->setMaxLength   ( 80 );
$obTxtMascara->setNull        ( false );
$obTxtMascara->setRotulo      ( "Máscara" );
$obTxtMascara->setValue       ( $stMascaraNivel );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

//DEFINICAO DO FORMULARIO
$obFomulario = new Formulario;
$obFomulario->addForm       ( $obForm );
$obFomulario->setAjuda     ( "UC-05.02.02");
$obFomulario->addTitulo     ( "Dados para Nível" );
$obFomulario->addHidden     ( $obHdnAcao );
$obFomulario->addHidden     ( $obHdnCtrl );
$obFomulario->addHidden     ( $obHdnNivel    );
if ($stAcao == "incluir") {
    $obFomulario->addComponente ( $obCmbDataVigencia );
} elseif ($stAcao == "alterar") {
    $obFomulario->addComponente ( $obLblVigencia );
    $obFomulario->addHidden     ( $obHdnVigencia );
    $obFomulario->addComponente ( $obLblCodigoNivel );
}
$obFomulario->addComponente ( $obTxtNomeNivel );
$obFomulario->addComponente ( $obLblNivelSuperior );
$obFomulario->addComponente ( $obTxtMascara );

if ($stAcao == "incluir") {
    $obFomulario->OK();
} else {
    $obFomulario->Cancelar( $pgList );
}
$obFomulario->show();
$inLinhaVigencia++;
$stJs =  "setaVigencia(".$inLinhaVigencia.");\n";
$stJs .= "buscaValor('UltimoNivel');\n";
sistemaLegado::executaFrameOculto($stJs);
?>

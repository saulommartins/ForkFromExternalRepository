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
    * Página de cadastro de alteração de caracteríosticas de localização
    * Data de Criação   : 24/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FMManterLocalizacaoCaracteristica.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.03
*/

/*
$Log$
Revision 1.7  2006/09/18 10:30:48  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterLocalizacao";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

$obRCIMLocalizacao  = new RCIMLocalizacao;
$obMontaLocalizacao = new MontaLocalizacao;
$obMontaAtributos   = new MontaAtributos;
$rsAtributos        = new RecordSet;

$inCodigoVigencia    = $_REQUEST["inCodigoVigencia"];
$inCodigoNivel       = $_REQUEST["inCodigoNivel"];
$inCodigoLocalizacao = $_REQUEST["inCodigoLocalizacao"];
$stValorComposto     = $_REQUEST["stValorComposto"];

$obRCIMLocalizacao->setCodigoVigencia    ( $inCodigoVigencia    );
$obRCIMLocalizacao->setCodigoNivel       ( $inCodigoNivel       );
$obRCIMLocalizacao->setCodigoLocalizacao ( $inCodigoLocalizacao );

$obMontaLocalizacao->setCodigoVigencia    ( $inCodigoVigencia    );
$obMontaLocalizacao->setCodigoNivel       ( $inCodigoNivel       );
$obMontaLocalizacao->setCodigoLocalizacao ( $inCodigoLocalizacao );
$obMontaLocalizacao->setValorComposto     ( $stValorComposto     );
$obRCIMLocalizacao->consultarLocalizacao();
//NIVEL SUPERIOR
    $stPai = explode(".",$_REQUEST["stValorComposto"]);
    $stPai[count($stPai) - 1] = preg_replace( "/([1-9])/","0", $stPai[count($stPai) - 1]);

    foreach ($stPai as $posicao) {
        $stPaiComposto .= ".$posicao";
    }
    $inCount = 0;
    if ($inCodigoNivel <= 2) {
        $inDec = 2;
    } else {
        $inDec = 1;
    }
    while ( $inCount < (count($stPai) - $inDec)) {
        $stPaiReduzido .= ".$stPai[$inCount]";
        $inCount++;
    }
    $stPaiComposto = substr($stPaiComposto, 0);
//    $stPaiReduzido = $stPai[$inCodigoNivel -2];
    $stPaiReduzido = substr($stPaiReduzido, 1);

    $obRCIMLocalizacao->setValorReduzido($stPaiReduzido);
    $obRCIMLocalizacao->recuperaPaiLocalizacao($rsPai);
    $stNomeLocalizacao      = $rsPai->getCampo(nom_localizacao);
    $stCompostoPai  = $rsPai->getCampo("valor_composto");

//DEFINICAO DOS ATRIBUTOS
$arChavePersistenteValores = array( "cod_nivel"=> $inCodigoNivel,
                                    "cod_vigencia"=> $inCodigoVigencia,
                                    "cod_localizacao"=>$inCodigoLocalizacao
                                  );
$obRCIMLocalizacao->obRCadastroDinamico->setChavePersistenteValores( $arChavePersistenteValores );
$obRCIMLocalizacao->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
//$obRCIMLocalizacao->obRCadastroDinamico->obPersistenteValores->debug();

$stNomeNivel        = $obRCIMLocalizacao->getNomeNivel();
$stMascara          = $obRCIMLocalizacao->getMascara();
$inValorLocalizacao = $obRCIMLocalizacao->getValor();

$obRCIMLocalizacao->setCodigoNivel($inCodigoNivel-1);
$obRCIMLocalizacao->listarNiveis($rsTemp);
$obRCIMLocalizacao->setCodigoNivel($inCodigoNivel);
$stNomeNivelSuperior = $rsTemp->getCampo('nom_nivel');

$obMontaAtributos->setName ("Atributo_");
$obMontaAtributos->setRecordSet( $rsAtributos );
$obMontaAtributos->recuperaValores();

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST['stCtrl'] );

$obHdnCodigoNivel = new Hidden;
$obHdnCodigoNivel->setName  ( "inCodigoNivel" );
$obHdnCodigoNivel->setValue ( $inCodigoNivel  );

$obHdnCodigoVigencia = new Hidden;
$obHdnCodigoVigencia->setName  ( "inCodigoVigencia" );
$obHdnCodigoVigencia->setvalue ( $inCodigoVigencia );

$obHdnNomeLocalizacao = new Hidden;
$obHdnNomeLocalizacao->setName      ( "stNomeLocalizacao" );
$obHdnNomeLocalizacao->setValue     ( $stNomeLocalizacao  );

$obLbNomeNivel = new Label;
$obLbNomeNivel->setRotulo( "Nível" );
$obLbNomeNivel->setValue( $stNomeNivel );

$obLblNomeLocalizacao = new Label;
$obLblNomeLocalizacao->setName      ( "stNomeLocalizacao" );
$obLblNomeLocalizacao->setRotulo    ( "Nome"              );
$obLblNomeLocalizacao->setValue     ( $stNomeLocalizacao  );

$obLblValorComposto = new Label;
$obLblValorComposto->setRotulo ( "Nível Superior" );
//$obLblValorComposto->setValue  ( $stValorComposto );
$obLblValorComposto->setValue  ( $stNomeNivelSuperior );

$obHdnCodigoLocalizacao = new Hidden;
$obHdnCodigoLocalizacao->setName  ( "inCodigoLocalizacao" );
$obHdnCodigoLocalizacao->setValue ( $inCodigoLocalizacao  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto"     );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm            ( $obForm                 );
$obFormulario->setAjuda ( "UC-05.01.03" );
$obFormulario->addTitulo          ( "Dados para nível"      );
$obFormulario->addHidden          ( $obHdnAcao              );
$obFormulario->addHidden          ( $obHdnCtrl              );
$obFormulario->addHidden          ( $obHdnCodigoNivel       );
$obFormulario->addHidden          ( $obHdnCodigoVigencia    );
$obFormulario->addHidden          ( $obHdnCodigoLocalizacao );
$obFormulario->addHidden          ( $obHdnNomeLocalizacao   );
$obFormulario->addComponente      ( $obLbNomeNivel          );
if ($inCodigoNivel > 1) {
    $obFormulario->addComponente      ( $obLblValorComposto     );
}
$obFormulario->addComponente      ( $obLblNomeLocalizacao   );
$obMontaAtributos->geraFormulario ( $obFormulario           );
if ($_REQUEST['stAcao'] == "incluir") {
    $obFormulario->OK();
} else {
    $obFormulario->Cancelar();
}
$obFormulario->show();
?>

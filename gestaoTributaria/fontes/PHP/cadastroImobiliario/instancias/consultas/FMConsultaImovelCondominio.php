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
    * Página para Consulta de Condomínio do Imóvel
    * Data de Criação: 15/06/2004

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    * $Id: FMConsultaImovelCondominio.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.18
*/

/*
$Log$
Revision 1.9  2006/09/18 10:30:20  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"           );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"       );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaImovel";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?".Sessao::getId().$stLink;
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgOculCons = "OC".$stPrograma."Construcao.php";
$pgJs       = "JS".$stPrograma.".js";

include_once( $pgJs       );
include_once( $pgOcul     );
include_once( $pgOculCons );

$stAcao = $request->get('stAcao');

$stFiltro = '';
$arTransf4 = Sessao::read('sessao_transf4');

if ($arTransf4) {
    $stFiltro = '';
    foreach ($arTransf4 as $stCampo => $stValor) {
        if ( is_array($stValor) ) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                $stFiltro .= "&".$stCampo2."=".@urlencode( $stValor2 );
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

if ($_REQUEST["inCodCondominio"]) {
    $obRCIMCondominio = new RCIMCondominio;
    $obRCIMCondominio->setCodigoCondominio      ( $_REQUEST["inCodCondominio"] );
    $obRCIMCondominio->listarProcessos          ( $rsListaProcesso             );

    $obRCIMCondominio->consultarCondominio      ( $rsCondominio                );
    $boTimestampIgual = $rsListaProcesso->getCampo("timestamp") == $rsCondominio->getCampo("timestamp");

    $arChaveAtributoCondominio = array( "cod_condominio"  => $_REQUEST["inCodCondominio"] );
    $obRCIMCondominio->obRCadastroDinamico->setChavePersistenteValores          ( $arChaveAtributoCondominio );
    $obRCIMCondominio->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos               );

    $obMontaAtributos = new MontaAtributos;
    $obMontaAtributos->setTitulo     ( "Atributos do condomínio" );
    $obMontaAtributos->setName       ( "Atributo_"               );
    $obMontaAtributos->setLabel      ( true                      );
    $obMontaAtributos->setRecordSet  ( $rsAtributos              );

    //DEFINICAO DOS COMPONENTES
    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName ( "stCtrl" );

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ( "stAcao" );
    $obHdnAcao->setValue( $stAcao );

    //COMPONENTES PARA A ABA INSCRICAO IMOBILIARIA
    $obLblCodigo = new Label;
    $obLblCodigo->setRotulo( "Código"                                 );
    $obLblCodigo->setValue ( $obRCIMCondominio->getCodigoCondominio() );

    $obLblNome = new Label;
    $obLblNome->setRotulo  ( "Nome"                                   );
    $obLblNome->setValue   ( $obRCIMCondominio->getNomCondominio()    );

    $obLblTipo = new Label;
    $obLblTipo->setRotulo  ( "Tipo"                                   );
    $obLblTipo->setValue   ( $obRCIMCondominio->getNomeTipo()         );

    $obLblCGM = new Label;
    $obLblCGM->setRotulo   ( "CGM" );
    $obLblCGM->setValue    ( $obRCIMCondominio->obRCGM->getNumCGM()." - ".$obRCIMCondominio->obRCGM->getNomCGM() );

    $obLblArea = new Label;
    $obLblArea->setRotulo  ( "Área Total Comum"                       );
    $obLblArea->setValue   ( $obRCIMCondominio->getAreaTotalComum()   );

    //DEFINICAO DO FORM
    $obForm = new Form;
    $obForm->setAction( $pgProc  );
    $obForm->setTarget( "oculto" );

    //DEFINICAO DO FORMULARIO

    $obFormulario = new FormularioAbas;
    $obFormulario->addForm      ( $obForm               );
    $obFormulario->setAjuda ( "UC-05.01.18" );
    $obFormulario->addHidden    ( $obHdnCtrl            );

    //ABA -> CONDOMINIO
    $obFormulario->addAba       ( "Condomínio"          );
    $obFormulario->addTitulo    ( "Dados do condomínio" );
    $obFormulario->addComponente( $obLblCodigo          );
    $obFormulario->addComponente( $obLblNome            );
    $obFormulario->addComponente( $obLblTipo            );
    $obFormulario->addComponente( $obLblCGM             );
    $obFormulario->addComponente( $obLblArea            );
    $obMontaAtributos->geraFormulario ( $obFormulario );
    include_once 'FMConsultaImovelCondominioListaProcessos.php';
    $obFormulario->addSpan      ( $obSpnProcessoCondominio          );
    $obFormulario->addSpan      ( $obSpnAtributosProcessoCondominio );
    //FIM ABA -> CONDOMINIO

    //ABA -> EDIFICACOES
    $obFormulario->addAba       ( "Edificações"          );
    include_once 'FMConsultaImovelEdificacoes.php';
    $obFormulario->addSpan      ( $obSpnListaEdificacoes );
    $obFormulario->addSpan      ( $obSpnEdificacao       );
    //FIM ABA -> EDIFICACOES

    //ABA -> CONSTRUCOES
    $obFormulario->addAba       ( "Construções"          );
    include_once 'FMConsultaImovelConstrucoes.php';
    $obFormulario->addSpan      ( $obSpnListaConstrucoes );
    $obFormulario->addSpan      ( $obSpnConstrucao       );
    //FIM ABA -> CONSTRUCOES

    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
    $obButtonVoltar = new Button;
    $obButtonVoltar->setName  ( "Voltar" );
    $obButtonVoltar->setValue ( "Voltar" );
    $obButtonVoltar->obEvento->setOnClick( "Cancelar('".$stLocation."');" );
    $obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );

    $obFormulario->show();
} else {
    $rsRecordSet = new RecordSet;
    $obLista     = new Lista;
    $obLista->setRecordSet( $rsRecordSet          );
    $obLista->setTitulo   ( "Dados de Condomínio" );
    $obLista->show();

    $obForm       = new Form;
    $obFormulario = new FormularioAbas;
    $obFormulario->addForm( $obForm );

    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
    $obButtonVoltar = new Button;
    $obButtonVoltar->setName  ( "Voltar" );
    $obButtonVoltar->setValue ( "Voltar" );
    $obButtonVoltar->obEvento->setOnClick( "Cancelar('".$stLocation."');" );
    $obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );

    $obFormulario->show();
}
?>

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
    * Página de Formulário da Caonfiguração do cadastro imobiliario
    * Data de Criação   : 05/04/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    * $Id: FMManterCondominioCaracteristicaAbaCaracteristica.php 63161 2015-07-30 19:45:43Z arthur $

    * Casos de uso: uc-05.01.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

// DEFINICAO DOS COMPONENTES DO FORMULARIO
$obLblCodigoCondominio = new Label;
$obLblCodigoCondominio->setRotulo  ( "Código"                        );
$obLblCodigoCondominio->setName    ( "inCodigoCondominio"            );
$obLblCodigoCondominio->setValue   ( $request->get("inCodigoCondominio") );

$obLblLocalizacao = new Label;
$obLblLocalizacao->setRotulo    ( "Localização" );
$obLblLocalizacao->setId        ( "Localizacao" );
$obLblLocalizacao->setName      ( "stLocalizacao" );
$obLblLocalizacao->setValue     ( $request->get('stLocalizacao')  );

$obLblLote = new Label;
$obLblLote->setRotulo           ( "Lote"      );
$obLblLote->setId               ( "inNumLote" );
$obLblLote->setName             ( "inNumLote" );
$obLblLote->setValue            ( STR_PAD($request->get('stValorLote'),strlen($request->get('stMascaraLote')),'0',STR_PAD_LEFT) );

$obLblNomCondominio = new Label;
$obLblNomCondominio->setRotulo  ( "Nome"                       );
$obLblNomCondominio->setName    ( "stNomCondominio"            );
$obLblNomCondominio->setValue   ( $_REQUEST["stNomCondominio"] );

$obLblTipoCondominio = new Label;
$obLblTipoCondominio->setRotulo  ( "Tipo"                 );
$obLblTipoCondominio->setName    ( "stTipoCondominio"     );
$obLblTipoCondominio->setValue   ( $_REQUEST["stNomTipo"] );

$obLblCGM = new Label;
$obLblCGM->setRotulo  ( "CGM"                        );
$obLblCGM->setName    ( "inCGM"            );
$obLblCGM->setValue   ( $_REQUEST["inNumCGM"]." - ".$_REQUEST["stNomCGM"] );

//ATRIBUTOS
//DEFINICAO DOS ATRIBUTOS DE IMOVEL
$arChaveAtributoCondominio =  array( "cod_condominio" => $_REQUEST["inCodigoCondominio"] );
$obRCIMCondominio->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCondominio );
$obRCIMCondominio->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosCondominio );

$obMontaAtributosCondominio = new MontaAtributos;
$obMontaAtributosCondominio->setTitulo     ( "Atributos"        );
$obMontaAtributosCondominio->setName       ( "Atributo_"  );
$obMontaAtributosCondominio->setRecordSet  ( $rsAtributosCondominio );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Número do processo no protocolo que formaliza este imóvel" );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setId   ("inProcesso");

$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

?>
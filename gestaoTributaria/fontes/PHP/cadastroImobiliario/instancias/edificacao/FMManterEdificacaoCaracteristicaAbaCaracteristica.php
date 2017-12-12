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
    * Página de alteração de características para o cadastro de edificação
    * Data de Criação   : 05/04/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Leusin Oigen

    * @ignore

    * $Id: FMManterEdificacaoCaracteristicaAbaCaracteristica.php 63281 2015-08-12 13:32:43Z arthur $

    * Casos de uso: uc-05.01.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$arChaveAtributo = array( "cod_tipo" => $_REQUEST["inCodigoTipo"], "cod_construcao" => $_REQUEST["inCodigoConstrucao"] );
$obRCIMEdificacao->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
$obRCIMEdificacao->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosEdificacao );

$obMontaAtributosEdificacao = new MontaAtributos;
$obMontaAtributosEdificacao->setTitulo     ( "Atributos"              );
$obMontaAtributosEdificacao->setName       ( "AtributoEdificacao_"    );
$obMontaAtributosEdificacao->setRecordSet  ( $rsAtributosEdificacao );

$obLblCodigoEdificacao = new Label;
$obLblCodigoEdificacao->setRotulo      ( "Código"                            );
$obLblCodigoEdificacao->setName        ( "inCodigoConstrucao"                );
$obLblCodigoEdificacao->setValue       ( $_REQUEST["inCodigoConstrucao"]     );

$obLblTipoEdificacao = new Label;
$obLblTipoEdificacao->setRotulo        ( "Tipo de Edificação"                );
$obLblTipoEdificacao->setName          ( "stTipoEdificacao"                  );
$obLblTipoEdificacao->setValue         ( $_REQUEST["stTipoEdificacao"]       );

$obLblNomeCondominio = new Label;
$obLblNomeCondominio->setRotulo        ( "Condomínio"                        );
$obLblNomeCondominio->setName          ( "stImovelCond"                      );
$obLblNomeCondominio->setValue         ( $_REQUEST["stImovelCond"]           );

$obLblInscricaoImobiliaria = new Label;
$obLblInscricaoImobiliaria->setRotulo  ( "Inscrição imobiliária"             );
$obLblInscricaoImobiliaria->setName    ( "stImovelCond"                      );
$obLblInscricaoImobiliaria->setValue   ( $_REQUEST["stImovelCond"]           );

$obLblTipoUnidade = new Label;
$obLblTipoUnidade->setRotulo           ( "Tipo de unidade"                   );
$obLblTipoUnidade->setName             ( "stTipoUnidade"                     );
$obLblTipoUnidade->setValue            ( $_REQUEST["stTipoUnidade"]          );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Número do processo no protocolo que formaliza este imóvel" );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setId   ("inProcesso");

$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

?>
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
    * Página de formulário para alteração de características de construção
    * Data de Criação   : 05/14/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Lucas Leusin Oigen

    * @ignore

    * $Id: FMManterConstrucaoCaracteristicaAbaCaracteristica.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.12
*/

/*
$Log$
Revision 1.6  2006/09/18 10:30:16  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//DEFINICAO DOS ATRIBUTOS DE LOTE
$arChaveAtributoConstrucao =  array( "cod_construcao" => $_REQUEST["inCodigoConstrucao"] );
$obRCIMConstrucao->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoConstrucao );
$obRCIMConstrucao->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

$obLblCodigoConstrucao = new Label;
$obLblCodigoConstrucao->setName     ( "inCodigoConstrucao" );
$obLblCodigoConstrucao->setRotulo   ( "Código" );
$obLblCodigoConstrucao->setValue    ( $obRCIMConstrucao->getCodigoConstrucao() );

$obLblNomeCondominio = new Label;
$obLblNomeCondominio->setName      ( "stNomeCondominio" );
$obLblNomeCondominio->setRotulo    ( "Condomínio" );
$obLblNomeCondominio->setValue     ( $_REQUEST["stNomeCond"] );

$obLblNumeroInscricao = new Label;
$obLblNumeroInscricao->setName      ( "inNumeroInscricao" );
$obLblNumeroInscricao->setRotulo    ( "Inscrição Imobiliária" );
$obLblNumeroInscricao->setTitle     ( "Inscrição imobiliária com a qual a edificação está vinculada" );
$obLblNumeroInscricao->setValue     ( $_REQUEST["inNumeroInscricao"] );

$obLblDescricaoConstrucao = new Label;
$obLblDescricaoConstrucao->setName      ( "stDescricaoConstrucao" );
$obLblDescricaoConstrucao->setRotulo    ( "Descrição" );
$obLblDescricaoConstrucao->setValue     ( $obRCIMConstrucao->getDescricao() );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Número do processo no protocolo que formaliza este imóvel" );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setId   ("inProcesso");
//$obBscProcesso->obCampoCod->setValue( $inProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

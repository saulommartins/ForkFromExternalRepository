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

 $Id: OCGeraDocumentoConvenio.php 59806 2014-09-12 12:23:29Z lisiane $

 **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_OOPARSER."tbs_class.php";
include_once CAM_OOPARSER."tbsooo_class.php";

$OOParser = new clsTinyButStrongOOo;

$OOParser->SetZipBinary('zip');
$OOParser->SetUnzipBinary('unzip');
$OOParser->SetProcessDir('/tmp');
$OOParser->SetDataCharset('UTF8');

$OOParser->NewDocFromTpl('../../../../../../gestaoPatrimonial/fontes/PHP/licitacao/anexos/convenios/TemplateConvenio.odt');

$OOParser->LoadXmlFromDoc('content.xml');

$arRequest = Sessao::read('arRequest');

$num_convenio = $arRequest['inNumConvenio'];

// buscando descrição do tipo convênio
include_once ( TLIC.'TLicitacaoTipoConvenio.class.php' );

$obTLicitacaoTipoConvenio = new TLicitacaoTipoConvenio();
$obTLicitacaoTipoConvenio->setDado( 'cod_tipo_convenio', $arRequest['inCodTipoConvenio']);
$obTLicitacaoTipoConvenio->setDado('cod_uf_tipo_convenio', SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()));
$obTLicitacaoTipoConvenio->recuperaPorChave( $rsTipoConvenio );

$tipo_convenio = $rsTipoConvenio->getCampo( 'descricao' );

$cod_objeto = $arRequest['stObjeto'];
$descricao_objeto = $arRequest['txtObjeto'];
$obs = $arRequest['stObservacao'];
$cgm_responsavel = $arRequest['inCgmResponsavelJuridico'];
$nom_responsavel = $arRequest['stResponsavelJuridico'];
$data_assinatura = $arRequest['dtAssinatura'];
$data_vigencia_final = $arRequest['dtFinalVigencia'];
$valor_convenio = $arRequest['nuValorConvenio'];

$arVeiculos     = Sessao::read('arValores');
$arParticipante = Sessao::read('participantes');

while ( !$arParticipante->eof() ) {
    $arParticipante->setCampo( 'nuValorParticipacao' , number_format($arParticipante->getCampo( 'nuValorParticipacao' ),2,',','.'));
    $arParticipante->proximo();
}

$OOParser->MergeBlock( 'bv', $arVeiculos );
$OOParser->MergeBlock( 'bp', $arParticipante->arElementos );
$OOParser->SaveXmlToDoc();

header('Content-type: '.$OOParser->GetMimetypeDoc(). 'name=Convenio.odt');
header('Content-Length: '.filesize($OOParser->GetPathnameDoc()));
header('Content-Disposition: filename=Convenio.odt');
$OOParser->FlushDoc();
$OOParser->RemoveDoc();

// limpar sessao de veiculos
Sessao::remove('boAlteracao');
Sessao::remove('nuValorAtual');
Sessao::remove('nuPercentualAtual');
Sessao::remove('rsVeiculos');
Sessao::remove('participantes');

?>

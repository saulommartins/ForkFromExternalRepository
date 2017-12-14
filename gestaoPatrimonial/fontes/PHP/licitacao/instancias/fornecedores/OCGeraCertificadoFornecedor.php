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
    * Pagina de Geração do Certificado do Fornecedor
    * Data de Criação   : 29/11/2006

    * @author Desenvolvedor: Henrique Boaventura

    * @ignore

    * Casos de uso: uc-03.05.13

    $Id: OCGeraCertificadoFornecedor.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_OOPARSER."tbs_class.php" );
include_once ( CAM_OOPARSER."tbsooo_class.php" );

/* busca os dados do fornecedor */
include_once( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
$obCGM = new TCGM();
$rsRecordSet = new RecordSet();
Sessao::getExercicio();
$obCGM->setDado("numcgm",$_REQUEST['inCodFornecedor']);
$obCGM->recuperaRelacionamentoFornecedor( $rsRecordSet );
//Variáveis soltas no texto
$num_certificado = $_REQUEST['inNumCertificacao']."/".$_REQUEST['stExercicio'];
$razaoSocial = $rsRecordSet->getCampo("nom_cgm");

$documento   = ( $rsRecordSet->getCampo("documento") ) ? $rsRecordSet->getCampo("documento") : '';
$pessoa      = ( $rsRecordSet->getCampo("documento") ) ? $rsRecordSet->getCampo("pessoa") : '' ;
$endereco    = $rsRecordSet->getCampo("tipo_logradouro")." ".$rsRecordSet->getCampo("logradouro").", Nº: ".$rsRecordSet->getCampo("numero");
$endereco   .= ( trim($rsRecordSet->getCampo("complemento")) != "" ) ? $rsRecordSet->getCampo("complemento").", ":", ";
$endereco   .= " Bairro ".$rsRecordSet->getCampo("bairro");
$cidade      = $rsRecordSet->getCampo("cidade");
$estado      = $rsRecordSet->getCampo("uf");
Sessao::getExercicio();
$expedicao   = $_REQUEST['dtDataRegistro'];
$vencimento  = $_REQUEST['dtDataVigencia'];

//listar documentos

include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoCertificacaoDocumentos.class.php" );
$obTLicitacaoCertificadoDocumentos = new TLicitacaoCertificacaoDocumentos();
$obTLicitacaoCertificadoDocumentos->setDado( "num_certificacao" , $_REQUEST['inNumCertificacao']);
$obTLicitacaoCertificadoDocumentos->setDado( "exercicio"       , $_REQUEST['stExercicio']       );
$obTLicitacaoCertificadoDocumentos->setDado( "cgm_fornecedor"  , $_REQUEST["inCodFornecedor"]);
$stFiltro = $obTLicitacaoCertificadoDocumentos->recuperaFiltroDocumentos();
$obTLicitacaoCertificadoDocumentos->recuperaDocumentos( $rsDocumentos , $stFiltro );
if ( !is_array($rsDocumentos->arElementos) ) {
    $rsDocumentos->arElementos = array();
}

//listar atividades

$request = Sessao::read('request');

include_once( CAM_GP_COM_MAPEAMENTO."TComprasFornecedorAtividade.class.php" );
$obTComprasFornecedorAtividade = new TComprasFornecedorAtividade();
$obTComprasFornecedorAtividade->setDado( "cgm_fornecedor", $request["inCodFornecedor"] );
$obTComprasFornecedorAtividade->recuperaAtividadeFornecedor( $rsAtividades );
if ( !is_array($rsAtividades->arElementos) ) {
    $rsAtividades->arElementos = array();
}

// instantiate a TBS OOo class
$OOParser = new clsTinyButStrongOOo;
// setting the object
$OOParser->SetZipBinary('zip');
$OOParser->SetUnzipBinary('unzip');
$OOParser->SetProcessDir('/tmp');
$OOParser->SetDataCharset('UTF8');

// create a new openoffice document from the template with an unique id
$OOParser->NewDocFromTpl("../../../../../../gestaoPatrimonial/fontes/PHP/licitacao/anexos/fornecedores/CertificadoFornecedor.odt");

$OOParser->LoadXmlFromDoc('content.xml');
$OOParser->MergeBlock( 'Bdo', $rsDocumentos->arElementos );
$OOParser->MergeBlock( 'Bat', $rsAtividades->arElementos );

$OOParser->SaveXmlToDoc();

$OOParser->LoadXmlFromDoc('styles.xml');
$OOParser->SaveXmlToDoc();

// display

header('Content-Type: '.$OOParser->GetMimetypeDoc().' name=CertificadoFornecedor.odt');
header('Content-Length: '.filesize($OOParser->GetPathnameDoc()));
header('Content-Disposition: attachment; filename=CertificadoFornecedor.odt');

$OOParser->FlushDoc();
$OOParser->RemoveDoc();

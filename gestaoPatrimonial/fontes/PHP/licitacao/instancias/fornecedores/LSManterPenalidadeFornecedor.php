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
    * Pagina de lista para Cadastro/Certificação
    * Data de Criação   : 03/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 18941 $
    $Name$
    $Autor: $
    $Date: 2006-12-21 15:30:39 -0200 (Qui, 21 Dez 2006) $

    * Casos de uso: uc-03.05.28
*/

/*

$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( TLIC."TLicitacaoParticipanteCertificacaoPenalidade.class.php" );

$stPrograma = "ManterPenalidadeFornecedor";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";

$obTLicitacaoParticipanteCertificacaoPenalidade = new TLicitacaoParticipanteCertificacaoPenalidade();

$stCaminho = CAM_GP_LIC_INSTANCIAS."fornecedores/";
$stAcao = $request->get('stAcao');

switch ($stAcao) {
    case 'alterar':
        $pgProx = $pgForm; break;
    case 'excluir':
        $pgProx = $pgProc; break;
    case 'anular':
        $pgProx = $pgForm; break;
    case 'consultar':
        $pgProx = $pgForm; break;
}

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

if ($_REQUEST['inCodFornecedor']) {
    $obTLicitacaoParticipanteCertificacaoPenalidade->setDado( 'cgm_fornecedor', $_REQUEST['inCodFornecedor'] );
}

if ($_REQUEST['inNumCertificacao']) {
    $inNumCertificacao = substr( $_REQUEST['inNumCertificacao'],0,6 );
    $obTLicitacaoParticipanteCertificacaoPenalidade->setDado( 'num_certificacao', $inNumCertificacao );
}

$stFiltro = "";
$stLink   = "";

$stLink .= "&stAcao=".$stAcao;

$obTLicitacaoParticipanteCertificacaoPenalidade->recuperaListaPenalidade( $rsPenalidades );

while ( !$rsPenalidades->eof() ) {
    $rsPenalidades->setCampo( 'num_certificacao', str_pad( $rsPenalidades->getCampo('num_certificacao'), 6, "0", STR_PAD_LEFT) );
    $rsPenalidades->proximo();
}

$rsPenalidades->setPrimeiroElemento();

$obLista = new Lista();
$obLista->setRecordSet( $rsPenalidades );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Certificação");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Fornecedor" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[num_certificacao]/[exercicio]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cgm_fornecedor]-[nom_cgm]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inCodFornecedor" , "cgm_fornecedor" );
$obLista->ultimaAcao->addCampo("&stExercicio", "exercicio" );
$obLista->ultimaAcao->addCampo("&inCertificacao", "num_certificacao" );
$obLista->ultimaAcao->addCampo("&stCodDocumento", "cod_documento" );
$obLista->ultimaAcao->addCampo("&stCodDocumentoTxt", "cod_documento" );
$obLista->ultimaAcao->addCampo("&inCodTipoDocumento", "cod_tipo_documento" );
$obLista->ultimaAcao->addCampo("&stNomFornecedor", "nom_cgm" );
$obLista->ultimaAcao->addCampo("&stNomDocumento", "nome_documento" );
$obLista->ultimaAcao->addCampo("&stDescQuestao" ,"");

$obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );

$obLista->commitAcao();

$obLista->setAjuda("UC-03.05.28");
$obLista->Show();

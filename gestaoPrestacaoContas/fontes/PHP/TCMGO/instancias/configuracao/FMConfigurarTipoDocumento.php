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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once "../../../../../../gestaoPatrimonial/fontes/PHP/licitacao/classes/mapeamento/TLicitacaoDocumento.class.php";
include_once (CAM_GPC_TGO_MAPEAMENTO."TTCMGOTipoDocumentoTCM.class.php");

$stPrograma = "ConfigurarTipoDocumento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao','configurar');

$boTransacao = new Transacao();

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Lista os Tipos de Documentos cadastrados no Sistema do URBEM
$obTLicitacaoDocumento = new TLicitacaoDocumento();
$obTLicitacaoDocumento->recuperaDocumentos($rsLicitacaoDocumentos, "", "ORDER BY cod_documento", $boTransacao);

$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista dos Tipos de Documentos do URBEM');
$obLista->setRecordSet($rsLicitacaoDocumentos);

//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Documento URBEM', 15);

//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[cod_documento] - [nom_documento]');
$obLista->commitDado();

// Lista os Tipos de Documentos cadastrados no Sistema do TCMGO
$obTTCMGOTipoDocumentoTCM = new TTCMGOTipoDocumentoTCM();
$obTTCMGOTipoDocumentoTCM->recuperaTodos($rsTipoDocumentoTCM, "", " ORDER BY cod_documento_tcm", $boTransacao);

$obDocumentoTTCMGO = new Select;
$obDocumentoTTCMGO->setName       ('inCodDocumento_[cod_documento_tcm]');
$obDocumentoTTCMGO->setId         ('inCodDocumento_[cod_documento_tcm]');
$obDocumentoTTCMGO->setValue      ('[cod_documento_tcm]');
$obDocumentoTTCMGO->addOption     ('', 'Selecione');
$obDocumentoTTCMGO->setCampoId    ('[cod_documento_tcm]');
$obDocumentoTTCMGO->setCampoDesc  ('[descricao]');
$obDocumentoTTCMGO->preencheCombo ( $rsTipoDocumentoTCM );

$obLista->addCabecalho('Lista de Documentos do TCMGO', 20);
$obLista->addDadoComponente($obDocumentoTTCMGO);
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo("descricao");
$obLista->commitDadoComponente();

$obSpnDocumentos = new Span();
$obSpnDocumentos->setId('spnDocumentos');
$obLista->montaHTML();
$obSpnDocumentos->setValue($obLista->getHTML());

$obFormulario = new Formulario();
$obFormulario->addForm  ($obForm);

$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addSpan  ($obSpnDocumentos);

$obFormulario->Cancelar( $pgForm.'?'.Sessao::getId().'&stAcao='.$stAcao );
$obFormulario->show();

//preenche os selects com os dados que estão cadastrados no banco
$jsOnload = "executaFuncaoAjax('carregaComboTipoDocumentoTCM');";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
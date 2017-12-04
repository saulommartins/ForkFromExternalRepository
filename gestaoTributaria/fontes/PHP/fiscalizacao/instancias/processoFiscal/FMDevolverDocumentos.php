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
    * Formulário que Prorrogar Recebimento de Documentos
    * Data de Criação: 26/08/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Aldo Jean Soares Silva

    * @package URBEM
    * @subpackage

    * @ignore

    * Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once( CAM_GT_FIS_NEGOCIO."RFISDevolverDocumentos.class.php" );
require_once( CAM_GT_FIS_VISAO."VFISDevolverDocumentos.class.php" );
require_once( CAM_GA_ADM_COMPONENTES.'ITextBoxSelectDocumento.class.php' );
require_once( CAM_GT_FIS_NEGOCIO."RFISIniciarProcessoFiscal.class.php" );
require_once( CAM_GT_FIS_VISAO."VFISIniciarProcessoFiscal.class.php" );
include_once( CAM_GT_FIS_INSTANCIAS."processoFiscal/JSEmitirDocumento.php" );

// Instanciando a Classe de Controle e de Visao
$obController = new RFISDevolverDocumentos;
$obVFISDevolverDocumentos = new VFISDevolverDocumentos($obController);

// Captura ação da funcionalidade. $stAcao = "devolverDocumentos"
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

// Define o nome dos arquivos PHP
$stPrograma = "DevolverDocumentos";
$pgFilt     	= "FL".$stPrograma.".php";
$pgList    	= "LS".$stPrograma.".php";
$pgForm  	= "FM".$stPrograma.".php";
$pgProc   	= "PR".$stPrograma.".php";
$pgOcul   	= "OC".$stPrograma.".php";
$pgJS      	= "JS".$stPrograma.".js";

include_once( $pgJS );

// Captura valor de inscrição de acordo com o Tipo de Fiscalização.
$inInscricao = $obVFISDevolverDocumentos->getValorTipoInscricao( $_REQUEST );

// Retorna array com resultado de consultas.
$obRsProcesso = $obVFISDevolverDocumentos->getObRsProcesso();

# Dados do Fiscal Logado
$rsFiscalLogado = $obVFISDevolverDocumentos->dadosFiscal();
$inCodFiscal = $rsFiscalLogado->getCampo('cod_fiscal');
//echo "<pre>",print_r($rsFiscalLogado),"</pre>";

$stTipoFiscalizacao = $obRsProcesso->arElementos[0]['cod_tipo']. " - " . $obRsProcesso->arElementos[0]['descricao'];
$inProcessoFiscal = $obRsProcesso->arElementos[0]['cod_processo'];

// Cria um novo formulario
$obForm = new Form();
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

// stAcao
$obHdnAcao = new Hidden();
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

// stCtrl
$obHdnCtrl = new Hidden();
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "devolverDocumentos" );

// stDescricao
$obHdnCtrl = new Hidden();
$obHdnCtrl->setName( "stDescricao" );
$obHdnCtrl->setValue( $_REQUEST["inCodigo"] );

// Cod. Tipo Fiscalização
$obHdnInTipoFiscalizacao = new Hidden();
$obHdnInTipoFiscalizacao->setName( "inTipoFiscalizacao" );
$obHdnInTipoFiscalizacao->setValue( $_REQUEST["inTipoFiscalizacao"] );

// Cod. Processo Fiscal
$obHdnInCodProcesso = new Hidden();
$obHdnInCodProcesso->setName( "inCodProcesso" );
$obHdnInCodProcesso->setId( "inCodProcesso" );
$obHdnInCodProcesso->setValue( $_REQUEST["inCodProcesso"] );

// Cod. Fiscal
$obHdnInCodFiscal = new Hidden();
$obHdnInCodFiscal->setName( "inCodFiscal" );
$obHdnInCodFiscal->setId( "inCodFiscal" );
$obHdnInCodFiscal->setValue($inCodFiscal);

// Tipo Fiscalização
$obTipoFiscalizacao = new Label();
$obTipoFiscalizacao->setRotulo( "Tipo de Fiscalização" );
$obTipoFiscalizacao->setName( "stTipoFiscalizacao" );

$obTipoFiscalizacao->setValue( $stTipoFiscalizacao );

// Processo Fiscal
$obProcessoFiscal = new Label();
$obProcessoFiscal->setRotulo( "Processo Fiscal" );
$obProcessoFiscal->setName( "inProcessoFiscal" );

//$obProcessoFiscal->setId( "inProcessoFiscal" );
$obProcessoFiscal->setValue( $inProcessoFiscal  );

// Inscricao Economica
$obInscricaoEconomica = new Label();
$obInscricaoEconomica->setRotulo( "Inscrição Econômica" );
$obInscricaoEconomica->setName( "stInscricaoEconomica" );
$obInscricaoEconomica->setValue( $inInscricao );

// Inscricao Municipal
$obInscricaoImobiliaria = new Label();
$obInscricaoImobiliaria->setRotulo( "Inscrição Imobiliaria" );
$obInscricaoImobiliaria->setName( "stInscricaoImobiliaria" );
$obInscricaoImobiliaria->setValue( $inInscricao );

// Observações
$obObservacoes = new TextArea();
$obObservacoes->setRotulo( "Observações" );
$obObservacoes->setName( "stObservacoes" );
$obObservacoes->setValue( $stObservacoes );
$obObservacoes->setTitle( "Informe as Observações." );
$obObservacoes->setNull( true );

// Termo de Devolução
$obTermoDevolucao = new ITextBoxSelectDocumento;
$obTermoDevolucao->setCodAcao(Sessao::read('acao')) ;
$obTermoDevolucao->obTextBoxSelectDocumento->setNull( false );
$obTermoDevolucao->obTextBoxSelectDocumento->setRotulo( "Termo de Devolução" );
$obTermoDevolucao->obTextBoxSelectDocumento->setName( "stCodDocumento" );
$obTermoDevolucao->obTextBoxSelectDocumento->setTitle( "Selecione o Termo de Devolução." );
$obTermoDevolucao->obTextBoxSelectDocumento->obTextBox->setSize( 10 );
$obTermoDevolucao->obTextBoxSelectDocumento->obSelect->setStyle( "width: 261px;" );

// Monta o formulário
$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnInTipoFiscalizacao );
$obFormulario->addHidden( $obHdnInCodProcesso );
$obFormulario->addHidden( $obHdnInCodFiscal );

// Adiciona Hidden ao formulário de acordo com o tipo de inscrição.
$obFormulario->addHidden(
                    $obVFISDevolverDocumentos->getHiddenInscricao(
                    $_REQUEST['inTipoFiscalizacao'],
                    $_REQUEST["inInscricao"] )
                    ) ;

// Título e filtros TipoFiscalizacao e ProcessoFiscal
$obFormulario->addTitulo( "Dados para Devolução de Documentos" );
$obFormulario->addComponente( $obTipoFiscalizacao );
$obFormulario->addComponente( $obProcessoFiscal );

// Adiciona o componente label ao formulário de acordo com o tipo de fiscalização.
$obFormulario->addComponente(
                        $obVFISDevolverDocumentos->getLabelinscricao(
                        $_REQUEST['inTipoFiscalizacao'],
                        $obInscricaoEconomica,
                        $obInscricaoImobiliaria )
                        );

// Checkbox para controle de situação (Devolvido ou Recebido).
$obChkStatusSituacao = new CheckBox();
$listaDocumentos = $obVFISDevolverDocumentos->getListaDocumentos( $_REQUEST['inCodProcesso'], $obChkStatusSituacao ) ;

// Hiddens para atualização da tabela fiscalizacao.documentos_entrega
$inCodProcessoLista = $listaDocumentos->arElementos[0]['cod_processo'] ;
$inCodDocumentoLista = $listaDocumentos->arElementos[0]['cod_documento'] ;
$inSequenciaLista = $listaDocumentos->arElementos[0]['sequencia'] ;

if ($listaDocumentos->arElementos[0]['dt_prorrogada'] != "") {
    $stDataEntregaLista = $listaDocumentos->arElementos[0]['dt_prorrogada'];
} else {
    $stDataEntregaLista = $listaDocumentos->arElementos[0]['dt_entrega'];
}

$stObservacaoLista = $listaDocumentos->arElementos[0]['observacao'] ;
$boSituacao = $listaDocumentos->arElementos[0]['situacao'] ;

//inCodProcessoLista
$obHdnInCodProcessoLista = new Hidden();
$obHdnInCodProcessoLista->setName( "inCodProcessoLista" );
$obHdnInCodProcessoLista->setId( "inCodProcessoLista" );
$obHdnInCodProcessoLista->setValue( $inCodProcessoLista );

//inCodDocumentoLista
$obHdnInCodDocumentoLista = new Hidden();
$obHdnInCodDocumentoLista->setName( "inCodDocumentoLista" );
$obHdnInCodDocumentoLista->setId( "inCodDocumentoLista" );
$obHdnInCodDocumentoLista->setValue( $inCodDocumentoLista );

//inSequenciaLista
$obHdnInSequenciaLista = new Hidden();
$obHdnInSequenciaLista->setName( "inSequenciaLista" );
$obHdnInSequenciaLista->setId( "inSequenciaLista" );
$obHdnInSequenciaLista->setValue( $inSequenciaLista );

//stDataEntregaLista
$obHdnStDataEntregaLista = new Hidden();
$obHdnStDataEntregaLista->setName( "stDataEntregaLista" );
$obHdnStDataEntregaLista->setId( "stDataEntregaLista" );
$obHdnStDataEntregaLista->setValue( $stDataEntregaLista );

//stObservacaoLista
$obHdnStObservacaoLista = new Hidden();
$obHdnStObservacaoLista->setName( "stObservacaoLista" );
$obHdnStObservacaoLista->setId( "stObservacaoLista" );
$obHdnStObservacaoLista->setValue( $stObservacaoLista );

//boSituacao
$obHdnBoSituacao = new Hidden();
$obHdnBoSituacao->setName( "boSituacao" );
$obHdnBoSituacao->setId( "boSituacao" );
$obHdnBoSituacao->setValue( $boSituacao );

// Adição de hiddens ao form para alteração na tabela fiscalizacao.documentos_entrega.
$obFormulario->addHidden( $obHdnInCodProcessoLista );
$obFormulario->addHidden( $obHdnInCodDocumentoLista );
$obFormulario->addHidden( $obHdnInSequenciaLista );
$obFormulario->addHidden( $obHdnStDataEntregaLista );
$obFormulario->addHidden( $obHdnStObservacaoLista );
$obFormulario->addHidden( $obHdnBoSituacao );

// Tabela para exibição da lista de dados.
$tableListaDocumentos = new Table();

// Tabela que receberá a lista de documentos.
$tableListaDocumentos->setSummary('Lista de Documentos');
$tableListaDocumentos->setRecordset( $listaDocumentos  );
$tableListaDocumentos->Head->addCabecalho( 'Código', 5,'');
$tableListaDocumentos->Head->addCabecalho( 'Nome', 70,'');
$tableListaDocumentos->Head->addCabecalho( 'Situação', 70,'');
$tableListaDocumentos->Body->addCampo( 'cod_documento' , 'C','' );
$tableListaDocumentos->Body->addCampo( 'nom_documento', 'C','' );
$tableListaDocumentos->Body->addCampo( 'check' , 'C', '' );
$tableListaDocumentos->montaHTML();

$obListaDocumentos = $tableListaDocumentos->getHTML();

$obListaDocumentos = str_replace("\n", "", $obListaDocumentos);
$obListaDocumentos = str_replace("  ", "", $obListaDocumentos);
$obListaDocumentos = str_replace("'", "\\'", $obListaDocumentos);

// Spam para a tabela
$obSpanListaDocumentos = new Span;
$obSpanListaDocumentos->setValue($obListaDocumentos);

// Inserção de componentes no formulário
$obFormulario->addComponente($obObservacoes);
$obTermoDevolucao->geraFormulario($obFormulario);
$obFormulario->addSpan( $obSpanListaDocumentos );

$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

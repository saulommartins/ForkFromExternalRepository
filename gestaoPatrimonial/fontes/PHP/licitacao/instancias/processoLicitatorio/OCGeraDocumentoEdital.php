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
    * Pagina de Gera?o do Documento do Edital
    * Data de Cria?o   : 05/11/2006

    * @author Desenvolvedor: Tonismar R?is Bernardo

    * @ignore

    * $Id: OCGeraDocumentoEdital.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.05.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_OOPARSER."tbs_class.php" );
include_once ( CAM_OOPARSER."tbsooo_class.php" );

//PEGANDO INFORMACOES DA PREFEITURA
include_once(CLA_MASCARA_CNPJ);
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
//include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php");
$obTConfiguracao = new TAdministracaoConfiguracao;
$obMascaraCNPJ   = new MascaraCNPJ;
$obTAcao         = new TAdministracaoAcao;
$obRGestao       = new RAdministracaoGestao;
$request = Sessao::read('request');

$stChave =  $obTConfiguracao->getComplementoChave();
$obTConfiguracao->setComplementoChave("cod_modulo,parametro,exercicio");
$arPropriedades = array( "nom_prefeitura" => "","cnpj" => "" ,"fone" => "", "fax" => "", "e_mail" => "", "logradouro" => "",
                     "numero" => "", "nom_municipio" => "", "cep" => "" , "logotipo" => "", "cod_uf" => "", "CGMPrefeito" => "" );

$obTConfiguracao->setDado( "exercicio" , Sessao::getExercicio());
foreach ($arPropriedades as $stParametro => $stValor) {
    $obErro = $obTConfiguracao->pegaConfiguracao($stValor, $stParametro );
    $arConfiguracao[$stParametro] = $stValor;
    if ( $obErro->ocorreu() ) {
        break;
    }
}

$obMascaraCNPJ->mascaraDado( $arConfiguracao['cnpj'] );
$obTConfiguracao->setComplementoChave($stChave);

$stFiltro = " AND A.cod_acao = ".Sessao::read('acao');
$obErro = $obTAcao->recuperaRelacionamento( $rsRecordSet, $stFiltro, ' A.cod_acao ', $boTransacao );

if ( !$obErro->ocorreu() ) {
    $arConfiguracao[ "nom_modulo" ]         = $rsRecordSet->getCampo( "nom_modulo" );
    $arConfiguracao[ "nom_funcionalidade" ] = $rsRecordSet->getCampo( "nom_funcionalidade" );
    $arConfiguracao[ "nom_acao" ]           = $rsRecordSet->getCampo( "nom_acao" );
}

$stFiltro = ' and M.cod_uf = '. $arConfiguracao['cod_uf'] ;

unset($obTConfiguracao);
$obTConfiguracao = new TAdministracaoConfiguracao;
$obErro = $obTConfiguracao->recuperaMunicipio( $rsMunicipio, $stFiltro );

//CONSULTANDO DADOS DA LICITACAO
include_once( TLIC."TLicitacaoLicitacao.class.php" );
$obTLicitacaoLicitacao = new TLicitacaoLicitacao();
$obTLicitacaoLicitacao->setDado('exercicio', $request['stExercicioLicitacao']);
$obTLicitacaoLicitacao->setDado('cod_entidade', $request['inCodEntidade']);
$obTLicitacaoLicitacao->setDado('cod_modalidade', $request['inCodModalidade']);
$obTLicitacaoLicitacao->setDado('cod_licitacao', $request['inCodLicitacao']);
$obTLicitacaoLicitacao->recuperaPorChave( $rsLicitacao );

$obTLicitacaoLicitacao->recuperaValorLicitacao($rsValorLicitacao);

$valorLicitacao = number_format($rsValorLicitacao->getCampo('valor_total'),2,',','.');

//CONSULTANDO NOME DA MODALIDADE
include_once( TCOM.'TComprasModalidade.class.php');
$obTComprasModalidade = new TComprasModalidade();
$obTComprasModalidade->setDado( 'cod_modalidade', $request['inCodModalidade'] );
$obTComprasModalidade->recuperaPorChave( $rsModalidade );
$modalidade = $rsModalidade->getCampo('descricao');
//

//CONSULTANDO CRITERIO DE JUGAMENTO
include_once( TLIC.'TLicitacaoCriterioJulgamento.class.php');
$obTLicitacaoCriterioJulgamento = new TLicitacaoCriterioJulgamento();
$obTLicitacaoCriterioJulgamento->setDado( 'cod_criterio', $rsLicitacao->getCampo('cod_criterio') );
$obTLicitacaoCriterioJulgamento->recuperaPorChave( $rsCriterioJulgamento );
$criterio = stripslashes($rsCriterioJulgamento->getCampo('descricao'));

//CONSULTANDO OBJETO
include_once( TCOM.'TComprasObjeto.class.php');
$obTComprasObjeto = new TComprasObjeto();
$obTComprasObjeto->setDado( 'cod_objeto', $rsLicitacao->getCampo('cod_objeto') );
$obTComprasObjeto->recuperaPorChave( $rsObjeto );
$objeto = stripslashes($rsObjeto->getCampo('descricao'));

//LISTANDO DOCUMENTOS
include_once( TLIC.'TLicitacaoLicitacaoDocumentos.class.php' );
$obTLicitacaoDocumentos = new TLicitacaoLicitacaoDocumentos();
$obTLicitacaoDocumentos->setDado('cod_modalidade', $rsLicitacao->getCampo('cod_modalidade'));
$obTLicitacaoDocumentos->setDado('exercicio', $rsLicitacao->getCampo('exercicio'));
$obTLicitacaoDocumentos->setDado('cod_entidade', $rsLicitacao->getCampo('cod_entidade'));
$obTLicitacaoDocumentos->setDado('cod_licitacao', $rsLicitacao->getCampo('cod_licitacao'));
$obTLicitacaoDocumentos->recuperaDocumentosLicitacao( $rsDocumentos );

//RESPONSAVEL JURIDICO
include_once( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
$obCGM = new TCGM();
$obCGM->setDado('numcgm', $request['inResponsavelJuridico']);
$obCGM->recuperaPorChave( $rsResponsavel );
$responsavel = $rsResponsavel->getCampo('nom_cgm');

$obCGM->setDado('numcgm', $arConfiguracao['CGMPrefeito'] );
$obCGM->recuperaPorChave( $rsPrefeito );

if ( $rsPrefeito->getCampo('nom_cgm') ) {
    $prefeito = $rsPrefeito->getCampo('nom_cgm');
} else {
    $prefeito = '';
}

$expressaoVerificaFormatoData = '/[0-9]{4,4}[-][0-9]{2,2}[-][0-9]{2,2}/';
//VARIAVEIS SOLTAS NO TEXTO
$nom_prefeitura = $arConfiguracao['nom_prefeitura'];
$logradouro     = $arConfiguracao['logradouro'    ];
$nom_municipio  = $rsMunicipio->getCampo('nom_municipio');
$uf             = $rsMunicipio->getCampo('nom_uf');
$sigla_uf       = $rsMunicipio->getCampo('sigla_uf');
$cep            = $arConfiguracao['cep'];
$cnpj           = $arConfiguracao['cnpj'];
$telefone       = $arConfiguracao['fone'];

if (preg_match($expressaoVerificaFormatoData, $request['dtEntrega'])) {
    $dt_ent_prop    = SistemaLegado::dataToBr($request['dtEntrega']);
} else {
    $dt_ent_prop    = $request['dtEntrega'];
}

$hr_ent_prop    = $request['stHoraEntrega'];
$local_ent      = $request['stLocalEntrega'];

if (preg_match($expressaoVerificaFormatoData, $request['dtAbertura'])) {
    $dt_abr_prop    = SistemaLegado::dataToBr($request['dtAbertura']);
} else {
    $dt_abr_prop    = $request['dtAbertura'];
}

$hr_abr_prop    = $request['stHoraAbertura'];
$local_abr      = $request['stLocalAbertura'];
$exercicio      = Sessao::getExercicio();
$licitacao      = $request['inCodLicitacao'].'/'.$request['stExercicioLicitacao'];

if (preg_match($expressaoVerificaFormatoData, $request['dtValidade'])) {
    $validade    = SistemaLegado::dataToBr($request['dtValidade']);
} else {
    $validade    = $request['dtValidade'];
}

$qtdDiasValidade= $request['qtdDiasValidade'];
$fpagto         = $request['txtCodPagamento'];

if ($request['hdnProcesso']) {
    $processo = $request['hdnProcesso'];
} else {
    $processo = '';
}

//LISTAR ITENS
include_once ( TCOM."TComprasMapaItem.class.php");
$obTComprasMapaItem = new TComprasMapaItem;
$obTComprasMapaItem->setDado('exercicio'   , "'".Sessao::getExercicio()."'");
$obTComprasMapaItem->setDado('cod_entidade', $rsLicitacao->getCampo('cod_entidade'));
$obTComprasMapaItem->setDado('cod_mapa'    , $rsLicitacao->getCampo('cod_mapa'));
$stFiltro = $obTComprasMapaItem->recuperaFiltroItensEdital();
$obTComprasMapaItem->recuperaItensEdital( $rsItem, $stFiltro );

$rsItem->setPrimeiroElemento();
while ( !$rsItem->eof() ) {
    include_once ( CAM_GP_COM_MAPEAMENTO . 'TComprasMapaItem.class.php' );
    $obTComprasMapaItem = new TComprasMapaItem();
    $itemComplemento = "";
    $obTComprasMapaItem->setDado('cod_mapa'     , $rsItem->getCampo('cod_mapa'));
    $obTComprasMapaItem->setDado('cod_item'     , $rsItem->getCampo('cod_item'));
    $obTComprasMapaItem->setDado('exercicio'    , $rsItem->getCampo('exercicio'));
    $obTComprasMapaItem->setDado('cod_entidade' , $rsItem->getCampo('cod_entidade'));
    $obTComprasMapaItem->recuperaComplementoItemMapa( $rsItemComplemento );

    $rsItemComplemento->setPrimeiroElemento();
    While (!$rsItemComplemento->eof()) {
        if ($itemComplemento == "") {
            $itemComplemento= $rsItemComplemento->getCampo('complemento');
        } else {
            $itemComplemento= $itemComplemento ." \n".$rsItemComplemento->getCampo('complemento');
        }

        $rsItemComplemento->proximo();
    }
    $rsItem->setCampo( 'complemento', $itemComplemento);

    $rsItem->setCampo( 'quantidade', number_format($rsItem->getCampo('quantidade'), 4, ',', '.' ) );
    $rsItem->proximo();
}

!is_array($rsItem->arElementos) ? $rsItem->arElementos = array() : $rsItem->arElementos ;

//LISTAR CONVENIOS
include_once ( TLIC."TLicitacaoConvenio.class.php" );
$obTLicitacao = new TLicitacaoConvenio();
$rsConvenio = new RecordSet();

$obTLicitacao->recuperaRelacionamento( $rsConvenio );

if ( !is_array($rsConvenio->arElementos) ) {
    $rsConvenio->arElementos = array();
} elseif ( !$rsConvenio->eof() ) {
    $rsConvenio->setCampo( 'observacao', stripslashes(str_replace('\r\n',' ',$rsConvenio->getCampo('observacao'))), true );
}

//LISTAR DOTACOES
include_once ( TCOM."TComprasMapa.class.php" );
$obTComprasMapa = new TComprasMapa();
$rsMapaItem = new RecordSet();
$obTComprasMapa->setDado('cod_mapa', $rsLicitacao->getCampo('cod_mapa'));
$obTComprasMapa->setDado('exercicio', $rsLicitacao->getCampo('exercicio'));
$obTComprasMapa->recuperaDotacaoEdital( $rsMapaItem );
if ( !is_array($rsMapaItem->arElementos) ) {
    $rsMapaItem->arElementos = array();
}

// instantiate a TBS OOo class
$OOParser = new clsTinyButStrongOOo;

// setting the object
$OOParser->SetDataCharset('UTF8');
$OOParser->SetZipBinary('zip');
$OOParser->SetUnzipBinary('unzip');
$OOParser->SetProcessDir('/tmp');

// ENQUANTO NÃO TEM O COMPONENTE QUE SELECIONA DOCUMENTO O ARQUIVO DE TEMPLATE FICOU FIXADO COM ESTE
$OOParser->NewDocFromTpl('../../../../../../gestaoPatrimonial/fontes/PHP/licitacao/anexos/processoLicitatorio/Edital.odt');

$OOParser->LoadXmlFromDoc('content.xml');
$OOParser->MergeBlock( 'Bki', $rsItem->arElementos );
$OOParser->MergeBlock( 'Bco', $rsConvenio->arElementos );
$OOParser->MergeBlock( 'Bdo', $rsMapaItem->arElementos );
$OOParser->MergeBlock( 'Bdc', $rsDocumentos->arElementos );

$OOParser->SaveXmlToDoc();

$OOParser->LoadXmlFromDoc('styles.xml');
$OOParser->SaveXmlToDoc();

// display
header('Content-Type: '.$OOParser->GetMimetypeDoc().' name=Edital.odt');
header('Content-Length: '.filesize($OOParser->GetPathnameDoc()));
header('Content-Disposition: attachment; filename=Edital.odt');

$OOParser->FlushDoc();
$OOParser->RemoveDoc();

?>

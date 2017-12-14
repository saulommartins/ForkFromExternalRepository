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
    * Página de Formulário para cadastro de documentos exigidos
    * Data de Criação   : 06/10/2006

    * @author Leandro André Zis

    * @ignore

    * Casos de uso : uc-03.05.22

    $Id: OCGeraContrato.php 59612 2014-09-02 12:00:51Z gelson $

*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_OOPARSER.'tbs_class.php');
include_once(CAM_OOPARSER.'tbsooo_class.php');
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoContrato.class.php"                                 );
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
$obTConfiguracao = new TAdministracaoConfiguracao;
Sessao::getExercicio();
$stChave =  $obTConfiguracao->getComplementoChave();
$obTConfiguracao->setComplementoChave("parametro,cod_modulo");
$arPropriedades = array("cod_uf" => "" );

include_once( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
$obTCGM = new TCGM();

$obTConfiguracao->setDado( "exercicio" , Sessao::getExercicio() );
foreach ($arPropriedades as $stParametro => $stValor) {
    $obErro = $obTConfiguracao->pegaConfiguracao($stValor, $stParametro );
    $arConfiguracao[$stParametro] = $stValor;
    if ( $obErro->ocorreu() ) {
        break;
    }
}

$arRelatorio = Sessao::read('arRelatorio');

$stFiltro = ' and M.cod_uf = '. $arConfiguracao['cod_uf'] ;
$obErro = $obTConfiguracao->recuperaMunicipio( $rsMunicipio, $stFiltro );

include_once( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
$obTCGM = new TCGM();
$obTCGM->setDado('numcgm',sistemaLegado::pegaConfiguracao('CGMPrefeito'));
$obTCGM->recuperaRelacionamento( $rsPrefeito );
// data
$nom_entidade = $arRelatorio['nomEntidade'];
//$nom_orgao = $arRelatorio['nomOrgao'];
//$nom_unidade = $arRelatorio['nomUnidade'];
$num_contrato = $arRelatorio['numContrato'];
$nom_fornecedor= $arRelatorio['nomFornecedor'];
$nom_logradouro = $arRelatorio['nom_logradouro'];
$nom_representante = $arRelatorio['nomRepresentante'];
//$nom_contratado = $arRelatorio['nomContratado'];

$estado = $rsMunicipio->getCampo('nom_uf');;
$cgc_entidade = ( $arRelatorio['cgcEntidade'] != '' ) ? $arRelatorio['cgcEntidade'] : '';
$nom_prefeito = $arRelatorio['nomPrefeito'];

//seta as palavras para o sexo do prefeito
if ( $rsPrefeito->getCampo('sexo') == 'f' ) {
    $pal_prefeito = "Prefeita";
    $pal_tratamento = "Senhora";
    $pal_pronome = "sua";
    $pal_artigo = "a";
} else {
    $pal_prefeito = "Prefeito";
    $pal_tratamento = "Senhor";
    $pal_artigo = "o";
    $pal_pronome = "seu";
}

$cgc_fornecedor = $arRelatorio['cgcFornecedor'] ? $arRelatorio['cgcFornecedor'] : '';
$cod_modalidade = $arRelatorio['codModalidade'];
$cod_licitacao = $arRelatorio['codLicitacao'];
$cod_entidade = $arRelatorio['codEntidade'];
$desc_objeto = $arRelatorio['descObjeto'];
$nom_cidade = $rsMunicipio->getCampo('nom_municipio');
$nom_documento_sxw = $arRelatorio['nomDocumentoSxw'];
$data_inicio = $arRelatorio['dataInicio'];
$data_vigencia = $arRelatorio['dataVigencia'];
$data = date('d/m/y');

$arDataIni = explode('/',$arRelatorio['dataInicio']);
$arDataFim = explode('/',$arRelatorio['dataVigencia']);

//quantidade de dias para executar o contrato,
//exclui-se 2 dias pois não é levado em consideração a data de início nem a data de término
$dias = round( (mktime(0,0,0,$arDataFim[1],$arDataFim[0],$arDataFim[2]) - mktime(0,0,0,$arDataIni[1],$arDataIni[0],$arDataIni[2]) ) / 86400,0) - 2;

// instantiate a TBS OOo class
$OOo = new clsTinyButStrongOOo;
//$OOo->SetDataCharset('ISO 8859-1');

// setting the object
$OOo->SetZipBinary('zip');
$OOo->SetUnzipBinary('unzip');
$OOo->SetProcessDir('/tmp/');
$OOo->SetDataCharset('UTF8');

include_once( TLIC."TLicitacaoHomologacao.class.php" );
$obTLicitacaoHomologacao = new TLicitacaoHomologacao();
/* esse filtro devia estar no mapeamento mas para não alterar o sql fiz aqui. */
$stFiltro = "
    where
        sw_cgm.numcgm = ".$arRelatorio['cgmFornecedor']." and
        adjudicacao.cod_licitacao = ".$arRelatorio['codLicitacao']." and
        adjudicacao.cod_modalidade = ".$arRelatorio['codModalidade']." and
        adjudicacao.cod_entidade = ".$arRelatorio['codEntidade']." and
        adjudicacao.exercicio_licitacao = '".Sessao::getExercicio()."'
";

$obTLicitacaoHomologacao->recuperaItensHomologacao( $rsItens, $stFiltro );
$i=0;
$dt_homologacao = '';

while ( !$rsItens->eof() ) {
    $arItens[$i++] = $rsItens->getCampo( 'cod_item' );
    $dt_homologacao = $rsItens->getCampo( 'dt_homologacao' );
    $rsItens->proximo();
}

$obTContrato = new TLicitacaoContrato;
$obTContrato->setDado('cod_modalidade', $cod_modalidade);
$obTContrato->setDado('cod_licitacao', $cod_licitacao );
$obTContrato->setDado('cod_entidade', $cod_entidade );
$obTContrato->setDado('exercicio', Sessao::getExercicio());
$obTContrato->setDado('cgm_fornecedor', $arRelatorio['cgmFornecedor']);
$obTContrato->recuperaProjAtiv($rsProjAtiv);

$inCount = 0;
$valor_total = 0;
foreach ($rsProjAtiv->arElementos as $arItem) {
    $rsProjAtiv->arElementos[$inCount]['vl_cotacao'] = number_format($arItem['vl_cotacao'],2,',','.');
    $valor_total+=$arItem['vl_cotacao'];
    $inCount++;
}
//somatório dos valores dos itens do contrato
$valor_total = number_format($valor_total,2,',','.');

include_once( TLIC."TLicitacaoContratoDocumento.class.php" );

$obTContratoDocumento = new TLicitacaoContratoDocumento();
$obTContratoDocumento->setDado('num_contrato',$arRelatorio['numContrato']);
$obTContratoDocumento->setDado('cod_entidade',$arRelatorio['codEntidade']);
$obTContratoDocumento->setDado('exercicio',Sessao::getExercicio());
$obTContratoDocumento->recuperaDocumentos( $rsDocumentos );

while ( !$rsDocumentos->eof() ) {
    $nom_documentos.= $rsDocumentos->getCampo('nom_documento').', ';
    $rsDocumentos->proximo();
}
$nom_documentos = substr($nom_documentos,0,strlen($nom_documentos)-2);

if ( !is_array( $rsProjAtiv->arElementos) ) { $rsProjAtiv->arElementos = array(); }

// create a new openoffice document from the template with an unique id
$OOo->NewDocFromTpl("../../../../../../gestaoPatrimonial/fontes/PHP/licitacao/anexos/contrato/TemplateContrato.sxw");
// merge data with OOo file content.xml
$OOo->LoadXmlFromDoc('content.xml');
$OOo->MergeBlock('blk1',$rsProjAtiv->arElementos) ;
$OOo->SaveXmlToDoc();
// merge data with OOo file styles.xml
$OOo->LoadXmlFromDoc('styles.xml');
$OOo->SaveXmlToDoc();

// display
header('Content-Type: '.$OOo->GetMimetypeDoc().' name=Contrato.odt');
header('Content-Length: '.filesize($OOo->GetPathnameDoc()));
header('Content-Disposition: attachment; filename=Contrato.odt');
$OOo->FlushDoc();
$OOo->RemoveDoc();

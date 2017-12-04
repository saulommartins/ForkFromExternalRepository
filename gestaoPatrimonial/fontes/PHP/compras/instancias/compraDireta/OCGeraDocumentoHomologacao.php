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
    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
    *
    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once TLIC.'TLicitacaoComissaoLicitacao.class.php';
include_once CAM_OOPARSER."tbs_class.php";
include_once CAM_OOPARSER."tbsooo_class.php";

//PEGANDO INFORMACOES DA PREFEITURA
include_once(CLA_MASCARA_CNPJ);
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
$obTConfiguracao = new TAdministracaoConfiguracao;
$obMascaraCNPJ   = new MascaraCNPJ;
$obTAcao         = new TAdministracaoAcao;
$obRGestao       = new RAdministracaoGestao;

$requestSessao = Sessao::read('request');

$stChave =  $obTConfiguracao->getComplementoChave();
$obTConfiguracao->setComplementoChave("parametro,cod_modulo");
$arPropriedades = array( "nom_prefeitura" => "","cnpj" => "" ,"fone" => "", "fax" => "", "e_mail" => "", "logradouro" => "",
                     "numero" => "", "nom_municipio" => "", "cep" => "" , "logotipo" => "", "cod_uf" => "", "CGMPrefeito" => "" );

foreach ($arPropriedades as $stParametro => $stValor) {
    $stValor = sistemalegado::pegaConfiguracao( $stParametro );
    $arConfiguracao[$stParametro] = $stValor ? $stValor : " ";
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
$obErro = $obTConfiguracao->recuperaMunicipio( $rsMunicipio, $stFiltro );

//CONSULTANDO NOME DA MODALIDADE
include_once( TCOM.'TComprasModalidade.class.php');
$obTComprasModalidade = new TComprasModalidade();
$obTComprasModalidade->setDado( 'cod_modalidade', $requestSessao['inCodModalidade'] );
$obTComprasModalidade->recuperaPorChave( $rsModalidade );
$desc_modalidade = $rsModalidade->getCampo('descricao');

//RESPONSAVEL JURIDICO
include_once( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
$obCGM = new TCGM();
$obCGM->setDado('numcgm', $arConfiguracao['CGMPrefeito'] );
$obCGM->recuperaPorChave( $rsPrefeito );
$nom_prefeito = $rsPrefeito->getCampo('nom_cgm');

//RECUPERANDO NRO PROCESSO
include_once( TCOM."TComprasCompraDireta.class.php" );
$obTComprasCompraDireta = new TComprasCompraDireta();
$obTComprasCompraDireta->setDado('exercicio_entidade' , $requestSessao['stExercicioCompraDireta']);
$obTComprasCompraDireta->setDado('cod_entidade'       , $requestSessao['inCodEntidade']);
$obTComprasCompraDireta->setDado('cod_modalidade'     , $requestSessao['inCodModalidade']);
$obTComprasCompraDireta->setDado('cod_compra_direta'  , $requestSessao['inCodCompraDireta']);
$obTComprasCompraDireta->recuperaCompraDireta( $rsCompraDireta );

//CONSULTANDO JUSTIFICATIVA E RAZAO
include_once( CAM_GP_COM_MAPEAMENTO."TComprasJustificativaRazao.class.php" );
$obTComprasJustificativaRazao = new TComprasJustificativaRazao();
$obTComprasJustificativaRazao->setDado('exercicio_entidade', $requestSessao['stExercicioCompraDireta']);
$obTComprasJustificativaRazao->setDado('cod_entidade'      , $requestSessao['inCodEntidade']);
$obTComprasJustificativaRazao->setDado('cod_modalidade'    , $requestSessao['inCodModalidade']);
$obTComprasJustificativaRazao->setDado('cod_compra_direta' , $requestSessao['inCodCompraDireta']);
$obTComprasJustificativaRazao->recuperaPorChave($rsJustificativaRazao);

if( $rsJustificativaRazao->getCampo('justificativa') ) {
    $justificativa = "Justificativa: ".$rsJustificativaRazao->getCampo('justificativa');
}else
    $justificativa = $rsJustificativaRazao->getCampo('justificativa');

if( $rsJustificativaRazao->getCampo('razao') ){
    $razao = "Razão da Escolha: ".$rsJustificativaRazao->getCampo('razao');
}else
    $razao = $rsJustificativaRazao->getCampo('razao');

//CONSULTANDO ITENS DA HOMLOGACAO
include_once( CAM_GP_COM_MAPEAMENTO."TComprasCompraDiretaHomologacao.class.php" );
$obTComprasCompraDiretaHomologacao = new TComprasCompraDiretaHomologacao();
$obTComprasCompraDiretaHomologacao->setDado('exercicio'         , $requestSessao['stExercicioCompraDireta']);
$obTComprasCompraDiretaHomologacao->setDado('cod_entidade'      , $requestSessao['inCodEntidade']);
$obTComprasCompraDiretaHomologacao->setDado('cod_modalidade'    , $requestSessao['inCodModalidade']);
$obTComprasCompraDiretaHomologacao->setDado('cod_compra_direta' , $requestSessao['inCodCompraDireta']);
$obTComprasCompraDiretaHomologacao->recuperaItensRelatorio($rsItens);


//CONSULTANDO PROCESSO
include_once( CAM_GP_COM_MAPEAMENTO."TComprasCompraDiretaProcesso.class.php" );
$obTComprasCompraDiretaProcesso = new TComprasCompraDiretaProcesso();
$obTComprasCompraDiretaProcesso->setDado('cod_compra_direta'  , $requestSessao['inCodCompraDireta']);
$obTComprasCompraDiretaProcesso->setDado('exercicio_entidade' , $requestSessao['stExercicioCompraDireta']);
$obTComprasCompraDiretaProcesso->setDado('cod_entidade'       , $requestSessao['inCodEntidade']);
$obTComprasCompraDiretaProcesso->setDado('cod_modalidade'     , $requestSessao['inCodModalidade']);
$obTComprasCompraDiretaProcesso->recuperaPorCompraDireta( $rsCompraDiretaProcesso );

$proc_admin = $rsCompraDiretaProcesso->getCampo('cod_processo').'/'.$rsCompraDiretaProcesso->getCampo('exercicio_processo');

if (count($rsItens->arElementos)<1) {
    $stmensagem = 'Não há itens homologados para gerar o termo de homologação';
    sistemaLegado::exibeAviso($stmensagem ,"n_incluir","erro");
    exit;
}

$obTComissaoLicitacao = new TLicitacaoComissaoLicitacao;
$obTComissaoLicitacao->recuperaMembroResponsavel($rsComissao);

$nomePresidenteComissao = $rsComissao->getCampo('nom_cgm');

if ($rsComissao->getCampo('cod_tipo_comissao') == 3) {
  $nomeCargo = "Pregoeiro";
} else {
  $nomeCargo = "Presidente da Comissão";
}

Sessao::write('rsItens', $rsItens);

include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php");
$obTOrcamentoEntidade = new TOrcamentoEntidade;
$stFiltro = " AND e.cod_entidade = ".$requestSessao['inCodEntidade'];
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, $stFiltro);
if ($rsEntidade->getNumLinhas() > 0) {
    $nom_entidade = $rsEntidade->getCampo('nom_cgm');
} else {
    $nom_entidade = "";
}

//VARIAVEIS SOLTAS NO TEXTO
$nom_prefeitura = $arConfiguracao['nom_prefeitura'];
$logradouro     = $arConfiguracao['logradouro'    ];
$nom_municipio  = $rsMunicipio->getCampo('nom_municipio');
$nomeMunicipio  = $rsMunicipio->getCampo('nom_municipio');
$uf             = $rsMunicipio->getCampo('nom_uf');
$cep            = $arConfiguracao['cep'];
$cnpj           = $arConfiguracao['cnpj'];
$telefone       = $arConfiguracao['fone'];

$nom_unidade    = '';
$nom_orgao      = '';

while (!$rsItens->eof()) {

    include_once ( CAM_GP_COM_MAPEAMENTO . 'TComprasMapaItem.class.php' );
    $obTComprasMapaItem = new TComprasMapaItem();
    $itemComplemento = "";
    $obTComprasMapaItem->setDado('cod_mapa'     , $rsItens->getCampo('cod_mapa'));
    $obTComprasMapaItem->setDado('cod_item'     , $rsItens->getCampo('cod_item'));
    $obTComprasMapaItem->setDado('exercicio'    , $rsItens->getCampo('exercicio_mapa'));
    $obTComprasMapaItem->setDado('cod_entidade' , $rsItens->getCampo('cod_entidade'));
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
    $rsItens->setCampo( 'complemento', $itemComplemento);

    $timestamp = $rsItens->getCampo('timestamp');
    $rsItens->proximo();
}

$stTimestamp = explode(' ', $timestamp);
$arData = explode('-', $stTimestamp[0]);
$stHora = substr($stTimestamp[1], 0, 5);

$stMesExtenso = array('Janeiro' , 'Fevereiro', 'Março'   , 'Abril',
                      'Maio'    , 'Junho'    , 'Julho'   , 'Agosto',
                      'Setembro', 'Outubro'  , 'Novembro', 'Dezembro');

foreach ($stMesExtenso as $chave=>$mes) {
    if ($arData[1]-1 == $chave) {
        $arData[1] = $mes;
    }
}

list($ano, $mes, $dia) = explode('-', $stTimestamp[0]);

$numeroDia     = $arData[2];
$nomeMes       = $arData[1];
$anoExtenso    = $arData[0];
$horaMinuto    = $stHora;
$data          = $dia."/".$mes."/".$ano;

$cod_licitacao = $requestSessao['inCodCompraDireta'].'/'.$requestSessao['stExercicioCompraDireta'];
$inCodObjeto = SistemaLegado::pegaDado("cod_objeto", "compras.compra_direta", "WHERE cod_compra_direta = ".$requestSessao['inCodCompraDireta']." AND cod_modalidade = ".$requestSessao['inCodModalidade']." AND cod_entidade = ".$requestSessao['inCodEntidade']." AND exercicio_entidade = '".$requestSessao['stExercicioCompraDireta']."' " );
$objeto      = SistemaLegado::pegaDado("descricao", "compras.objeto", "WHERE cod_objeto= ".$inCodObjeto);

$obTComprasCompraDireta->recuperaDotacaoOrcamentaria($rsDotacao);
foreach ($rsDotacao->arElementos as $el) {
    $dotacao[] = array('unidade_orcamentaria' => $el['num_unidade'].' - '.$el['nom_unidade'],
                                   'programa' => $el['cod_programa'].' - '.$el['descricao'],
                           'elemento_despesa' => $el['cod_estrutural'].' - '.$el['descricao_estrutural'],
                                      'fonte' => $el['cod_fonte'].' - '.$el['nom_recurso']
            );
}

$valor = 0;
foreach ($rsItens->arElementos as $el) {
    $valor = $valor + str_replace(',', '.', str_replace('.', '', $el['vl_total']));
}
$valor = number_format($valor, 2, ',', '.');

include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");
$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
$obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
$obTAdministracaoConfiguracao->recuperaMunicipio($rsMunicipio);
$municipio = strtoupper($rsMunicipio->getCampo('nom_municipio'));

$fornecedor = $rsItens->arElementos[0]['nom_fornecedor'];

// instantiate a TBS OOo class
$OOParser = new clsTinyButStrongOOo;
$OOParser->SetDataCharset('UTF8');

// setting the object
$OOParser->SetZipBinary('zip');
$OOParser->SetUnzipBinary('unzip');
$OOParser->SetProcessDir('/tmp');

// create a new openoffice document from the template with an unique id
$OOParser->NewDocFromTpl('../../../../../../gestaoPatrimonial/fontes/PHP/compras/anexos/homologacao/TemplateHomologacao.sxw');
$OOParser->LoadXmlFromDoc('content.xml');
$OOParser->MergeBlock( 'blk', $rsItens->arElementos );
$OOParser->MergeBlock( 'blk2', $dotacao);

$OOParser->SaveXmlToDoc();
$OOParser->LoadXmlFromDoc('styles.xml');
$OOParser->SaveXmlToDoc();

// display
header('Content-type: '.$OOParser->GetMimetypeDoc(). 'name=Homologacao.sxw');
header('Content-Length: '.filesize($OOParser->GetPathnameDoc()));
header('Content-Disposition: filename=Homologacao.sxw');

$OOParser->FlushDoc();
$OOParser->RemoveDoc();

?>

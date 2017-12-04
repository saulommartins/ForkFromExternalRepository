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

    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * $Id: OCGeraDocumentoHomologacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.05.21
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

$request = Sessao::read('request');

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
$boTransacao = "";
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
$obTComprasModalidade->setDado( 'cod_modalidade', $request['inCodModalidade'] );
$obTComprasModalidade->recuperaPorChave( $rsModalidade );
$desc_modalidade = $rsModalidade->getCampo('descricao');

//RESPONSAVEL JURIDICO
include_once( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
$obCGM = new TCGM();
$obCGM->setDado('numcgm', $arConfiguracao['CGMPrefeito'] );
$obCGM->recuperaPorChave( $rsPrefeito );
$nom_prefeito = $rsPrefeito->getCampo('nom_cgm');
//RECUPERANDO NRO PROCESSO
include_once( TLIC."TLicitacaoLicitacao.class.php" );
$obTLicitacaoLicitacao = new TLicitacaoLicitacao();
$obTLicitacaoLicitacao->setDado('exercicio', $request['stExercicioLicitacao']);
$obTLicitacaoLicitacao->setDado('cod_entidade', $request['inCodEntidade']);
$obTLicitacaoLicitacao->setDado('cod_modalidade', $request['inCodModalidade']);
$obTLicitacaoLicitacao->setDado('cod_licitacao', $request['inCodLicitacao']);
$obTLicitacaoLicitacao->recuperaLicitacao( $rsLicitacao );

//CONSULTANDO JUSTIFICATIVA E RAZAO
include_once( TLIC."TLicitacaoJustificativaRazao.class.php" );
$obTLicitacaoJustificativaRazao = new TLicitacaoJustificativaRazao;
$obTLicitacaoJustificativaRazao->setDado( 'cod_licitacao'          , $request['inCodLicitacao']       );
$obTLicitacaoJustificativaRazao->setDado( 'cod_modalidade'         , $request['inCodModalidade']      );
$obTLicitacaoJustificativaRazao->setDado( 'cod_entidade'           , $request['inCodEntidade']        );
$obTLicitacaoJustificativaRazao->setDado( 'exercicio'              , $request['stExercicioLicitacao'] );
$obTLicitacaoJustificativaRazao->recuperaPorChave($rsJustificativaRazao);

if( $rsJustificativaRazao->getCampo('justificativa') ) {
    $justificativa = "Justificativa: ".$rsJustificativaRazao->getCampo('justificativa');
}else
    $justificativa = $rsJustificativaRazao->getCampo('justificativa');

if( $rsJustificativaRazao->getCampo('razao') ){
    $razao = "Razão da Escolha: ".$rsJustificativaRazao->getCampo('razao');
}else
    $razao = $rsJustificativaRazao->getCampo('razao');
    
//CONSULTANDO ITENS DA HOMLOGACAO
include_once( TLIC."TLicitacaoHomologacao.class.php" );
$obTLicitacaoHomologacao = new TLicitacaoHomologacao();
$obTLicitacaoHomologacao->setDado('exercicio', $request['stExercicioLicitacao']);
$obTLicitacaoHomologacao->setDado('cod_entidade', $request['inCodEntidade']);
$obTLicitacaoHomologacao->setDado('cod_modalidade', $request['inCodModalidade']);
$obTLicitacaoHomologacao->setDado('cod_licitacao', $request['inCodLicitacao']);
$obTLicitacaoHomologacao->recuperaItensRelatorio($rsItens);

if (count($rsItens->arElementos)<1) {
    $stmensagem = 'Não há itens homologados para gerar o termo de homologação';
    sistemaLegado::exibeAviso($stmensagem ,"n_incluir","erro");
    exit;
}

$obTComissaoLicitacao = new TLicitacaoComissaoLicitacao;
$obTComissaoLicitacao->setDado( 'exercicio'     , $request['stExercicioLicitacao']);
$obTComissaoLicitacao->setDado( 'cod_licitacao' , $request['inCodLicitacao']      );
$obTComissaoLicitacao->recuperaMembro( $rsComissao);

$nomePresidenteComissao = $rsComissao->getCampo('nom_cgm');

if ($rsComissao->getCampo('cod_tipo_comissao') == 3) {
  $nomeCargo = "Pregoeiro";
} else {
  $nomeCargo = "Presidente da Comissão";
}

Sessao::write('rsItens', $rsItens);

$arProc = explode('/', $rsLicitacao->getCampo('processo'));

include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php");
$obTOrcamentoEntidade = new TOrcamentoEntidade;
$stFiltro = " AND e.cod_entidade = ".$request['inCodEntidade'];
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

$proc_admin =  $arProc[0].'/'.$arProc[1];
$cod_licitacao = $request['inCodLicitacao'].'/'.$request['stExercicioLicitacao'];

// instantiate a TBS OOo class
$OOParser = new clsTinyButStrongOOo;

// setting the object
$OOParser->SetZipBinary('zip');
$OOParser->SetUnzipBinary('unzip');
$OOParser->SetProcessDir('/tmp');
$OOParser->SetDataCharset('UTF8');

// create a new openoffice document from the template with an unique id
$OOParser->NewDocFromTpl('../../../../../../gestaoPatrimonial/fontes/PHP/licitacao/anexos/homologacao/TemplateHomologacao.sxw');
$OOParser->LoadXmlFromDoc('content.xml');
$OOParser->MergeBlock( 'blk', $rsItens->arElementos );

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

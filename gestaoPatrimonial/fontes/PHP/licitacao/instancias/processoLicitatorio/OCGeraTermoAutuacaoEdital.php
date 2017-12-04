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
    * Pagina de Geracao do Termo de Autuação de Edital
    * Data de Criação: 13/01/2009

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Grasiele Torres

    * @ignore

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_OOPARSER."tbs_class.php" );
include_once ( CAM_OOPARSER."tbsooo_class.php" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/CGM/classes/mapeamento/TCGM.class.php';
include_once (CLA_MASCARA_CNPJ);
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
include_once ( TLIC.'TLicitacaoComissaoLicitacao.class.php');
include_once ( TLIC.'TLicitacaoLicitacao.class.php');
include_once ( TLIC.'TLicitacaoEdital.class.php');
include_once ( TCOM.'TComprasModalidade.class.php');
include_once ( TCOM.'TComprasObjeto.class.php');

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
$obTConfiguracao->setComplementoChave("parametro,cod_modulo");
$arPropriedades = array( "nom_prefeitura" => "","cnpj" => "" ,"fone" => "", "fax" => "", "e_mail" => "", "logradouro" => "",
                     "numero" => "", "complemento" => "", "nom_municipio" => "", "cep" => "" , "logotipo" => "", "cod_
                     uf" => "", "CGMPrefeito" => "" );

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

$rsProcesso = Sessao::read('rsProcesso');

//CONSULTANDO DADOS DA LICITACAO
$obTLicitacaoLicitacao = new TLicitacaoLicitacao();
$obTLicitacaoLicitacao->setDado('exercicio', $rsProcesso->getCampo('exercicio'));
$obTLicitacaoLicitacao->setDado('cod_entidade', $rsProcesso->getCampo('cod_entidade'));
$obTLicitacaoLicitacao->setDado('cod_modalidade', $rsProcesso->getCampo('cod_modalidade'));
$obTLicitacaoLicitacao->setDado('cod_licitacao', $rsProcesso->getCampo('cod_licitacao'));
$obTLicitacaoLicitacao->recuperaPorChave( $rsLicitacao );

//CONSULTANDO NOME DA MODALIDADE
$obTComprasModalidade = new TComprasModalidade();
$obTComprasModalidade->setDado( 'cod_modalidade', $rsProcesso->getCampo('cod_modalidade'));
$obTComprasModalidade->recuperaPorChave( $rsModalidade );

$obTComprasObjeto = new TComprasObjeto();
$obTComprasObjeto->setDado( 'cod_objeto', $rsProcesso->getCampo('cod_objeto'));
$obTComprasObjeto->recuperaPorChave( $rsObjeto );

$obTLicitacaoLicitacao = new TLicitacaoLicitacao();
$obTLicitacaoLicitacao->setDado("cod_licitacao"  , $rsProcesso->getCampo('cod_licitacao'));
$obTLicitacaoLicitacao->setDado("exercicio"      , $rsProcesso->getCampo('exercicio'));
$obTLicitacaoLicitacao->setDado("cod_modalidade" , $rsProcesso->getCampo('cod_modalidade'));
$obTLicitacaoLicitacao->setDado("cod_entidade"   , $rsProcesso->getCampo('cod_entidade'));
$obTLicitacaoLicitacao->recuperaPorChave($rsLicitacao);

$obTLicitacaoEdital = new TLicitacaoEdital();
$obTLicitacaoEdital->setDado("num_edital" , $rsProcesso->getCampo('num_edital'));
$obTLicitacaoEdital->setDado("exercicio"  , $rsProcesso->getCampo('exercicio'));
$obTLicitacaoEdital->recuperaPorChave($rsEdital);

$obTLicitacaoLicitacao = new TLicitacaoLicitacao();
$obTLicitacaoLicitacao->setDado("cod_licitacao"  , $rsProcesso->getCampo('cod_licitacao'));
$obTLicitacaoLicitacao->setDado("exercicio"      , $rsProcesso->getCampo('exercicio'));
$obTLicitacaoLicitacao->setDado("cod_modalidade" , $rsProcesso->getCampo('cod_modalidade'));
$obTLicitacaoLicitacao->setDado("cod_entidade"   , $rsProcesso->getCampo('cod_entidade'));
$obTLicitacaoLicitacao->recuperaDadosLicitacao($rsDadosLicitacao);

if ( !is_array($rsDadosLicitacao->arElementos) ) {
    $rsDadosLicitacao->arElementos = array();
}

$stTimestamp = explode(' ', $rsProcesso->getCampo('timestamp'));
$data = explode('-', $stTimestamp[0]);
$dataProposta = implode('/',array_reverse(explode('-',$stTimestamp[0])));
$horaProposta = substr($stTimestamp[1], 0, 5);

$stMesExtenso = array('Janeiro' , 'Fevereiro', 'Março'   , 'Abril',
                      'Maio'    , 'Junho'    , 'Julho'   , 'Agosto',
                      'Setembro', 'Outubro'  , 'Novembro', 'Dezembro');

foreach ($stMesExtenso as $chave=>$mes) {
    if (date("m") -1 == $chave) {
        $data[1] = $mes;
    }
}

//VARIAVEIS SOLTAS NO TEXTO
$codObjeto           = $rsProcesso->getCampo('cod_objeto');
$descricaoObjeto     = $rsObjeto->getCampo('descricao');
$codLicitacao        = $rsProcesso->getCampo('cod_licitacao');
$exercicioLicitacao  = $rsProcesso->getCampo('exercicio_licitacao');
$modalidade          = $rsModalidade->getCampo('descricao');
$descricaoModalidade = $rsModalidade->getCampo('descricao');
$valorOrcado    = $rsLicitacao->getCampo('vl_cotado');
$nom_prefeitura = $arConfiguracao['nom_prefeitura'];
$logradouro     = $arConfiguracao['logradouro'    ];
$numero         = $arConfiguracao['numero'        ];
$complemento    = $arConfiguracao['complemento'   ];
$nom_municipio  = $rsMunicipio->getCampo('nom_municipio');
$uf             = $rsMunicipio->getCampo('nom_uf');
$cep            = $arConfiguracao['cep'];
$cnpj           = $arConfiguracao['cnpj'];
$telefone       = $arConfiguracao['fone'];
$hora_abertura_propostas = $rsEdital->getCampo('hora_abertura_propostas');
$dt_abertura_propostas   = $rsEdital->getCampo('dt_abertura_propostas');
$numeroDia      = date("d");
$nomeMes        = $data[1];
$anoExtenso     = date("Y");
$horaMinuto     = $$horaProposta;
$nomeMunicipio  = $rsMunicipio->getCampo('nom_municipio');
$data           = $rsProcesso->getCampo('dt_licitacao');
$nomeEntidade   = $rsProcesso->getCampo('nom_cgm');

$arAssinaturas = Sessao::read('assinaturas');

if (count($arAssinaturas['selecionadas']) > 0) {
    foreach ($arAssinaturas['selecionadas'] as $arSelecionadas) {
        $stEntidade  = $arSelecionadas['inCodEntidade'];
        $stTimestamp = "'".$arSelecionadas['timestamp']."'";
        $inCGM       = $arSelecionadas['inCGM'];
        $stNomCGM    = $arSelecionadas['stNomCGM'];
    }
}

$nomeServidor = $stNomCGM;
$matricula = $inCGM;

$obTLicitacaoLicitacao = new TLicitacaoLicitacao();
$obTLicitacaoLicitacao->setDado( 'numcgm', $inCGM);
$obTLicitacaoLicitacao->recuperaNorma( $rsNorma );

$norma = $rsNorma->getCampo('num_norma') .'/'. $rsNorma->getCampo('exercicio');
$tipoNorma = $rsNorma->getCampo('nom_tipo_norma');

// instantiate a TBS OOo class
$OOParser = new clsTinyButStrongOOo;

// setting the object
$OOParser->SetZipBinary  ('zip'  );
$OOParser->SetUnzipBinary('unzip');
$OOParser->SetProcessDir ('/tmp' );

// create a new openoffice document from the template with an unique id
$OOParser->NewDocFromTpl('../../../../../../gestaoPatrimonial/fontes/PHP/licitacao/anexos/processoLicitatorio/TemplateAutuacaoEdital.odt');

$OOParser->LoadXmlFromDoc('content.xml');
$OOParser->MergeBlock( 'Blk', $rsDadosLicitacao->arElementos);

$OOParser->SaveXmlToDoc();

$OOParser->LoadXmlFromDoc('styles.xml');
$OOParser->SaveXmlToDoc();

// display
header('Content-type: '.$OOParser->GetMimetypeDoc(). 'name=AutuacaoEdital.odt');
header('Content-Length: '.filesize($OOParser->GetPathnameDoc()));
header('Content-Disposition: filename=AutuacaoEdital.odt');

$OOParser->FlushDoc();
$OOParser->RemoveDoc();

Sessao::write('request',null);
//Sessao::remove('acao');
Sessao::remove('rsProcesso');
Sessao::remove('assinaturas');

?>

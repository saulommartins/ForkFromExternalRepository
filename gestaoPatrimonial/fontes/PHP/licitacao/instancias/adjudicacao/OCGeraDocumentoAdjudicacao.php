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
    * Pagina de Gera?o do Documento da Homlogacao
    * Data de Cria?o   : 25/11/2006

    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso: uc-03.05.21

    $Id: OCGeraDocumentoAdjudicacao.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_OOPARSER."tbs_class.php";
include_once CAM_OOPARSER."tbsooo_class.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/CGM/classes/mapeamento/TCGM.class.php';
include_once CLA_MASCARA_CNPJ;
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
include_once TLIC.'TLicitacaoComissaoLicitacao.class.php';
include_once TCOM.'TComprasModalidade.class.php';

$obTConfiguracao = new TAdministracaoConfiguracao;
$obMascaraCNPJ   = new MascaraCNPJ;
$obTAcao         = new TAdministracaoAcao;
$obRGestao       = new RAdministracaoGestao;

// recupera itens adjudicacao
$arItens         = Sessao::read('itensAdjudicacao');
$_REQUEST        = Sessao::read('request') ;

$arItensAux = array();
$count = 0;

foreach ($arItens as $chave1 => $item) {
    if ($item['status'] == 'Adjudicado' || $item['status'] == 'Homologado') {
        foreach ($item as $chave2 => $valor2) {
            $arItensAux[$count][$chave2] = $valor2;

            if ($chave2 == 'cgmFornecedor') {
                $obTCGM = new TCGM();
                $obTCGM->setDado('numcgm', $valor2);
                $obTCGM->recuperaRelacionamentoFornecedor($rsFornecedor);
                $arItensAux[$count]['nom_fornecedor'] = $rsFornecedor->getCampo('nom_cgm');
            }

            if ($chave2 == 'valorReferencia') {
                $arItensAux[$count]['valorTotal'] = $valor2;
            }

            if ($chave2 == 'valorCotacao') {
                $arItensAux[$count]['valorCotacao'] = $valor2;
            }

            if ($chave2 == 'nomUnidade') {
                $arItensAux[$count]['unidadeMedida'] = $valor2;
            }

            if ($chave2 == 'complemento') {
                $valor2= str_replace( " <br>", "", $valor2);
                $arItensAux[$count]['complemento'] = $valor2;
            }

            if ($chave2 == 'quantidade') {
                $valorTotal = number_format($valor2, 4, ',','.');
                $arItensAux[$count]['quantidade'] = $valorTotal;
            }

            if ($chave2 == 'valorUnitario') {
                $arItensAux[$count]['valorUnitario'] = $valor2;
            }
        }
        $count++;
    }
}

$obTConfiguracao = new TAdministracaoConfiguracao;
$stChave =  $obTConfiguracao->getComplementoChave();
$obTConfiguracao->setComplementoChave("parametro,cod_modulo");
$obTConfiguracao->setDado( "exercicio" , Sessao::getExercicio() );

$arPropriedades = array( "nom_prefeitura" => "",
                         "cnpj"           => "" ,
                         "fone"           => "",
                         "fax"            => "",
                         "e_mail"         => "",
                         "logradouro"     => "",
                         "numero"         => "",
                         "nom_municipio"  => "",
                         "cep"            => "" ,
                         "logotipo"       => "",
                         "cod_uf"         => "",
                         "CGMPrefeito"    => ""
                       );

foreach ($arPropriedades as $stParametro => $stValor) {
    $obErro = $obTConfiguracao->pegaConfiguracao($stValor, $stParametro );
    $arConfiguracao[$stParametro] = $stValor;
    if ( $obErro->ocorreu() ) {
        break;
    }
}
$obTConfiguracao->setComplementoChave($stChave);

$rsProcesso = Sessao::read('rsProcesso');

$arEntidade = explode(' - ', $rsProcesso->getCampo('entidade'));

$processoAdmin = $rsProcesso->getCampo('processo');
$codLicitacao = $arItens[0]['codLicitacao'];
$exercicioLicitacao = $arItens[0]['licitacaoExercicio'];

$obTComprasModalidade = new TComprasModalidade();
$obTComprasModalidade->setDado( 'cod_modalidade', $_REQUEST['inCodModalidade'] );
$obTComprasModalidade->recuperaPorChave( $rsModalidade );
$descricaoModalidade = $rsModalidade->getCampo('descricao');

$obTComissaoLicitacao= new TLicitacaoComissaoLicitacao();
$obTComissaoLicitacao->setDado( 'cod_licitacao', $arItens[0]['codLicitacao'] );
$obTComissaoLicitacao->setDado( 'exercicio', $arItens[0]['licitacaoExercicio'] );
$obTComissaoLicitacao->recuperaMembro($rsComissao);

$nomePresidenteComissao = $rsComissao->getCampo('nom_cgm');

if ($rsComissao->getCampo('cod_tipo_membro') == 3) {
  $nomeCargo = "Pregoeiro";
} else {
  $nomeCargo = "Presidente da Comissão";
}

$stFiltro = ' and M.cod_uf = '. $arConfiguracao['cod_uf'] ;
$obErro = $obTConfiguracao->recuperaMunicipio( $rsMunicipio, $stFiltro );

$stTimestamp = explode(' ', $_REQUEST['timestamp_adjudicacao']);
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
$nomeMunicipio = $rsMunicipio->getCampo('nom_municipio');
$data          = $dia."/".$mes."/".$ano;
$nomeEntidade  = $arEntidade[1];

//limpa lista itens adjudicação
Sessao::write('itensAdjudicacao', '');

// instantiate a TBS OOo class
$OOParser = new clsTinyButStrongOOo;

// setting the object
$OOParser->SetZipBinary  ('zip'  );
$OOParser->SetUnzipBinary('unzip');
$OOParser->SetProcessDir ('/tmp' );
$OOParser->SetDataCharset('UTF8');

// create a new openoffice document from the template with an unique id
$OOParser->NewDocFromTpl('../../../../../../gestaoPatrimonial/fontes/PHP/licitacao/anexos/adjudicacao/TemplateAdjudicacao.sxw');

$OOParser->LoadXmlFromDoc('content.xml');
$OOParser->MergeBlock    ( 'blk',  $arItensAux);

$OOParser->SaveXmlToDoc();

$OOParser->LoadXmlFromDoc('styles.xml');
$OOParser->SaveXmlToDoc();

// display
header('Content-type: '.$OOParser->GetMimetypeDoc(). 'name=Adjudicacao.odt');
header('Content-Length: '.filesize($OOParser->GetPathnameDoc()));
header('Content-Disposition: filename=Adjudicacao.odt');

$OOParser->FlushDoc();
$OOParser->RemoveDoc();

?>

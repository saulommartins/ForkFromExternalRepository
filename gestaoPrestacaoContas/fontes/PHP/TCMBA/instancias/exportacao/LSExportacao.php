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
    * Listagens dos Criados pelo modulo Exportacao
    * Data de Criação   : 08/06/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Id: LSExportacao.php 63819 2015-10-19 20:52:10Z michel $

    * Casos de uso: uc-06.03.00
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

// pega array de arquivos prcessados da sessao
$arFiltro = Sessao::read('arArquivosDownload');
if ( !is_array($arFiltro['arArquivosDownload']) ) {
    $arFiltro['arArquivosDownload'] = array();
}

//Cria Titulo da lista de arquivos
$codAcao = Sessao::read('acao');
$obRAdministracaoAcao = new RAdministracaoAcao();
$obRAdministracaoAcao->setCodigoAcao($codAcao);
$obRAdministracaoAcao->listar($rsAcao,null,null,null);
$stNomAcao = $rsAcao->getCampo('nom_acao');
$stTituloRegistros = ($stNomAcao!='') ? 'Arquivos - '.$stNomAcao : 'Arquivos';

// cria recordset e preenche com o conteudo do array
$arArquivos = Sessao::read('arArquivosDownload');
$arArquivosErro = array();

//Verifica recebimento de Arquivos e separa arquivo Informativo de Erros da lista de arquivos do Tribunal
if ( !is_array($arArquivos) )
    $arArquivos = array();
else{
    $arArquivosTemp = array();
    foreach($arArquivos as $chave => $arArquivo){
        if( $arArquivo['stNomeArquivo'] != 'URBEM_Informativo_de_Erros.txt' )
            $arArquivosTemp[] = $arArquivo;
        else
            $arArquivosErro[] = $arArquivo;
    }
    $arArquivos = $arArquivosTemp;
}

//Monta Lista de Arquivos do Tribunal
$rsArquivos = new RecordSet;
$rsArquivos->preenche($arArquivos);

$obLista    = new Lista;
$obLista->setTitulo($stTituloRegistros);
$obLista->setRecordSet( $rsArquivos );
$obLista->setMostraPaginacao( false );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Arquivos");
$obLista->ultimoCabecalho->setWidth( 55 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo('stNomeArquivo');
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
// SETA O LINK DA ACAO
$obLista->addAcao();
$obLista->ultimaAcao->setAcao('download');
$obLista->ultimaAcao->addCampo('&arq'   ,'stLink');
$obLista->ultimaAcao->addCampo('&label' ,'stNomeArquivo');
$obLista->ultimaAcao->setLink('../../../exportacao/instancias/processamento/download.php?sim=sim');
$obLista->commitAcao();
$obLista->montaInnerHtml();
$stHtmlLista = $obLista->getHtml();

$obSpnListaRegistros = new Span;
$obSpnListaRegistros->setId ( "spanListaRegistros" );
$obSpnListaRegistros->setValue( $stHtmlLista );

//Monta Lista de Arquivos de Erro
if(count($arArquivosErro)>0){
    $rsArquivosErro = new RecordSet;
    $rsArquivosErro->preenche($arArquivosErro);

    $obListaErro    = new Lista;
    $obListaErro->setTitulo('Arquivos - Informativo de Erros');
    $obListaErro->setRecordSet( $rsArquivosErro );
    $obListaErro->setMostraPaginacao( false );

    $obListaErro->addCabecalho();
    $obListaErro->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaErro->ultimoCabecalho->setWidth( 5 );
    $obListaErro->commitCabecalho();
    $obListaErro->addCabecalho();
    $obListaErro->ultimoCabecalho->addConteudo("Arquivos");
    $obListaErro->ultimoCabecalho->setWidth( 55 );
    $obListaErro->commitCabecalho();
    $obListaErro->addCabecalho();
    $obListaErro->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaErro->ultimoCabecalho->setWidth( 40 );
    $obListaErro->commitCabecalho();

    $obListaErro->addDado();
    $obListaErro->ultimoDado->setCampo('stNomeArquivo');
    $obListaErro->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaErro->commitDado();
    // SETA O LINK DA ACAO
    $obListaErro->addAcao();
    $obListaErro->ultimaAcao->setAcao('download');
    $obListaErro->ultimaAcao->addCampo('&arq'   ,'stLink');
    $obListaErro->ultimaAcao->addCampo('&label' ,'stNomeArquivo');
    $obListaErro->ultimaAcao->setLink('../../../exportacao/instancias/processamento/download.php?sim=sim');
    $obListaErro->commitAcao();
    $obListaErro->montaInnerHtml();
    $stHtmlListaErro = $obListaErro->getHtml();

    $obSpnListaErro = new Span;
    $obSpnListaErro->setId ( "spanListaErro" );
    $obSpnListaErro->setValue( $stHtmlListaErro );
}

$obForm = new Form;

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

if(count($arArquivosErro)>0)
    $obFormulario->addSpan  ( $obSpnListaErro       );

$obFormulario->addSpan      ( $obSpnListaRegistros  );

$obFormulario->show();
SistemaLegado::LiberaFrames();
?>

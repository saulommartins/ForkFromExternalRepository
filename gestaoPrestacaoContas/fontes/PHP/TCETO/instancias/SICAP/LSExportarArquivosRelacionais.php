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
  * Lista de arquivos gerados
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: LSExportarArquivosRelacionais.php 60674 2014-11-07 15:51:39Z franver $
  * $Date: 2014-11-07 13:51:39 -0200 (Fri, 07 Nov 2014) $
  * $Author: franver $
  * $Rev: 60674 $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$arFiltro = Sessao::read('filtro');
// pega array de arquivos prcessados da sessao
$arArquivos = array();

if ( is_array(Sessao::read('arArquivosDownload'))) {
    $arArquivos = Sessao::read('arArquivosDownload');
}

// cria recordset e preenche com o conteudo do array
$rsArquivos = new RecordSet;
$rsArquivos->preenche($arArquivos);

$obLista    = new Lista;

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
//$obLista->ultimaAcao->setFuncao(true);
$obLista->ultimaAcao->addCampo('&arq'   ,'stLink');
$obLista->ultimaAcao->addCampo('&label' ,'stNomeArquivo');
$obLista->ultimaAcao->setLink('../../../exportacao/instancias/processamento/download.php?sim=sim');
$obLista->commitAcao();

$obLista->show();
SistemaLegado::LiberaFrames();
?>

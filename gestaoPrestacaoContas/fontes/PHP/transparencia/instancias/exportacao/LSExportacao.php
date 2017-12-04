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
 * Data de Criação   : 16/11/2012

 * @author Analista: Gelson
 * @author Desenvolvedor: Carolina

 * @ignore

 $Id: LSExportacao.php 59612 2014-09-02 12:00:51Z gelson $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

// pega array de arquivos prcessados da sessao
$arFiltro = Sessao::read('arArquivosDownload');
if (!is_array($arFiltro['arArquivosDownload']) ) {
    $arFiltro['arArquivosDownload'] = array();
}

$arArquivos = Sessao::read('arArquivosDownload');

// cria recordset e preenche com o conteudo do array
$rsArquivos = new RecordSet;
$rsArquivos->preenche($arArquivos);

$obLista = new Lista;

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
$obLista->ultimoCabecalho->addConteudo("Ações");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo('stNomeArquivo');
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao('download');
$obLista->ultimaAcao->addCampo('&arq'   ,'stLink');
$obLista->ultimaAcao->addCampo('&label' ,'stNomeArquivo');
$obLista->ultimaAcao->setLink('../../../exportacao/instancias/processamento/download.php?sim=sim');
$obLista->commitAcao();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao('&nbsp;&nbsp;&nbsp;&nbsp;');
$obLista->commitAcao();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao('Enviar Pacote');
$obLista->ultimaAcao->addCampo('?label' ,'stNomeArquivo');
$obLista->ultimaAcao->setLink('../../../transparencia/instancias/exportacao/PRExportarTransparencia.php');
$obLista->commitAcao();

$obLista->show();
SistemaLegado::LiberaFrames();

?>

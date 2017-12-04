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
   * Data de Criação   : 05/08/2010

   * @author Desenvolvedor: Tonismar R. Bernardo

   * @ignore
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$listaArquivo = array();
$listaArquivo = Sessao::read('arArquivosDownload');

$arquivos = new RecordSet();
$arquivos->preenche($listaArquivo);

$lista = new Lista();
$lista->setRecordSet($arquivos);
$lista->setMostraPaginacao(false);

$lista->addCabecalho();
$lista->ultimoCabecalho->addConteudo('&nbsp;');
$lista->ultimoCabecalho->setWidth(5);
$lista->commitCabecalho();
$lista->addCabecalho();
$lista->ultimoCabecalho->addConteudo('Arquivos');
$lista->ultimoCabecalho->setWidth(55);
$lista->commitCabecalho();
$lista->addCabecalho();
$lista->ultimoCabecalho->addConteudo('&nbsp;');
$lista->ultimoCabecalho->setWidth(5);
$lista->commitCabecalho();

$lista->addDado();
$lista->ultimoDado->setCampo('stNomeArquivo');
$lista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$lista->commitDado();

$lista->addAcao();
$lista->ultimaAcao->setAcao('download');
$lista->ultimaAcao->addCampo('&arq','stLink');
$lista->ultimaAcao->addCampo('&label','stNomeArquivo');
$lista->ultimaAcao->setLink('../../../exportacao/instancias/processamento/download.php?sim=sim');
$lista->commitAcao();

$lista->Show();
SistemaLegado::LiberaFrames();

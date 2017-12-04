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
    Página de frame oculto para processamento

    @date: 05/08/2010

    @author: Analista: Gelson
    @author: Desenvolvedor: Tonismar
**/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CLA_EXPORTADOR;

SistemaLegado::BloqueiaFrames();

/** Recupera valor da ação **/
$stAcao = $_POST['stAcao '] ? $_POST['stAcao'] : $_GET['stAcao'];
$filtro = Sessao::read('filtroRelatorio');

$exportador = new Exportador();

foreach ($filtro['listaSelecionados'] as $arquivo) {
    $exportador->addArquivo($arquivo);
    $exportador->roUltimoArquivo->setTipoDocumento('Coletora');

    $pos = strpos($arquivo,'.txt');
    include substr($arquivo, 0, $pos).'.inc.php';
}

$exportador->show();

SistemaLegado::LiberaFrames();

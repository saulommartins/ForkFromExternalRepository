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
    * Processamento
    * Data de Criação   : 05/02/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Lucas Stephanou

    * @ignore

    * Casos de uso: uc-03.04.33

    $Id: FMPreviewCompraDireta.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt( 3, 35, 1);
$preview->setVersaoBirt( '2.5.0' );
$preview->setNomeArquivo('CompraDireta');
$preview->setTitulo('Documento para Cotação de Preços');

$preview->addParametro( "codCompraDireta"         , $_REQUEST["inCodCompraDireta"] );
$preview->addParametro( "codEntidade"             , $_REQUEST["inCodEntidade"]     );
$preview->addParametro( "codModalidade"           , $_REQUEST["inCodModalidade"]   );
$preview->addParametro( "stExercicioCompraDireta" , Sessao::getExercicio()         );
$preview->addParametro( "codTipoLicitacao" , $_REQUEST["inCodTipoCotacao"]              );

$stDtEmissao = $_REQUEST['stDtEmissao'];
if ($stDtEmissao != '') {
    $preview->addParametro('data_emissao', $stDtEmissao);
} else {
    $preview->addParametro('data_emissao', '');
}

$preview->preview();

?>

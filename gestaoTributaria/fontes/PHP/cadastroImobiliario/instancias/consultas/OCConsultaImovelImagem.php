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
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
switch ($_REQUEST["stCtrl"]) {
case 'carregaImagem':
    include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelFoto.class.php");

    Sessao::setTrataExcecao( false );
    $obImageBox=Sessao::read('obImageBox');
    $obTCIMImovelFoto = new TCIMImovelFoto();
    $obTCIMImovelFoto->setDado('cod_foto',$_REQUEST['inCodFoto']);
    $obTCIMImovelFoto->setDado('inscricao_municipal',$_REQUEST['inCodInscricao']);

    Sessao::setTrataExcecao( true );
    Sessao::getTransacao()->setMapeamento( $obTCIMImovelFoto );
    header('Content-type: image/jpg');
    $obTCIMImovelFoto->recuperaFoto($stImagem);
    $obImageBox->ajustaTamanhoImagem( $stImagem,$_REQUEST['boBox']);

    Sessao::encerraExcecao();
break;
}
?>

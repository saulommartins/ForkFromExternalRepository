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
*/

include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelFoto.class.php");

$obImageBox= new ImageBox();
$obImageBox->setRotulo("Fotos do imóvel");

$obTCIMImovelFoto = new TCIMImovelFoto();
$obTCIMImovelFoto->recuperaFotosPorInscricao($_REQUEST['inCodInscricao'],$rsFotos);

while (!$rsFotos->eof() ) {
    $obImg = new Img();
    $obImg->setId('idImagem_'.$rsFotos->getCampo('cod_foto'));

    $stURL = $pgOculFoto."?".Sessao::read('sessao_id').'&stCtrl=carregaImagem';
    $stURL.= '&inCodFoto='.$rsFotos->getCampo('cod_foto').'&inCodInscricao='.$_REQUEST['inCodInscricao'];
    $obImg->setCaminho($stURL);
    $obImageBox->addImagem($rsFotos->getCampo('descricao')? $rsFotos->getCampo('descricao'):'Foto '.$rsFotos->getCampo('cod_foto'),$obImg);
    $rsFotos->proximo();
}

Sessao::write('obImageBox',$obImageBox);

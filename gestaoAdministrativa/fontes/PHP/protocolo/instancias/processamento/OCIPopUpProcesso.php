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
    * Data de Criação: 02/12/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: OCIPopUpProcesso.php 62215 2015-04-08 21:28:32Z jean $

    * Casos de uso: uc-01.06.98
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_PROT_MAPEAMENTO."TProcesso.class.php" );

switch ($_REQUEST['stCtrl']) {
    case "preencheProcesso":
        $stJs = isset($stJs) ? $stJs  : null;
        if ($_REQUEST['stNumProcesso'] != '') {
            $arProcesso = explode('/',$_REQUEST['stNumProcesso'] );
            //se nao for preenchido o valor do exercicio, assume o da sessao
            if ( strlen($arProcesso[1]) != 4 ) {
                $arProcesso[1] = Sessao::getExercicio();
            }
            
            if ($arProcesso[0] == '00000') {
                $stJs .= "$('".$_REQUEST['stNomCampo']."').value = '';";
                $stJs .= "$('stIDChaveProcesso').html = '';";
            } else {
                //verifica se o processo existe no banco
                $obTProcesso = new TProcesso();
                $obTProcesso->setDado( 'cod_processo', (int) $arProcesso[0] );
                $obTProcesso->setDado( 'ano_exercicio', $arProcesso[1] );
                $obTProcesso->recuperaPorChave( $rsProcesso );
    
                if ( $rsProcesso->getNumLinhas() == -1 ) {
                    $stJs .= "$('".$_REQUEST['stNomCampo']."').value = '';";
                    $stJs .= "alertaAviso('Código do processo inválido.','form','erro','".Sessao::getId()."');\n";
                } else {
                     $stJs .= "f.".$_REQUEST['stNomCampo'].".value = '".$arProcesso[0] . '/' . $arProcesso[1] ."';";
                }
            }
        }
    break;
}
echo $stJs;
?>

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
 * Página de Oculto de Manter Escola
 * Data de Criação: 15/04/2014

 * @author Analista:      Gelson Wolowski
 * @author Desenvolvedor: Lisiane Morais>

 * @ignore

 $Id: OCManterEscola.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaEscola.class.php" );


$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {
    # Método responsável por atualizar o campo Ativo.
    case 'carregaAtivo':
        $inNumCgm = $_REQUEST['numCgm'];
        
        if($inNumCgm != ""){
            # Filtro para listar somente escolas cadastradas.
            $obTFrotaEscola = new TFrotaEscola;
            $obTFrotaEscola->setDado('numcgm',$_REQUEST['numCgm']);
    
            $obTFrotaEscola->recuperaPorChave($rsFrotaEscola);
         
            if($rsFrotaEscola->getNumLinhas() > 0){
                if($rsFrotaEscola->getCampo('ativo') == f){
                    $stJs .= "jQuery('#boAtivoNao').attr('checked' , true);  \n";
                }else{
                    $stJs .= "jQuery('#boAtivoSim').attr('checked' , true);  \n";
                }
            }
        }
    break;
}

if (!empty($stJs)) {
    echo $stJs;
}

?>

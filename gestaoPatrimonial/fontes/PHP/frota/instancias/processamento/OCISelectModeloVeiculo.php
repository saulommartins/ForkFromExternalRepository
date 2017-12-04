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
    * Data de Criação: 23/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: OCISelectModeloVeiculo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_FRO_MAPEAMENTO."TFrotaModelo.class.php" );

switch ($_REQUEST["stCtrl"]) {
    case "montaModelo":
        //limpa o select de modelos
        $stJs .= "limpaSelect($('".$_REQUEST['stModelo']."'),0); \n";

        //adiciona a primeira posicao padrao
        $stJs .= "$('".$_REQUEST['stModelo']."').options[0] = new Option('Selecione','','selected'); \n";

        if ($_REQUEST['inCodMarca'] != '') {
            $inCount = 0;

            //recupera os modelos de acordo com a marca
            $obTFrotaModelo = new TFrotaModelo();
            $stFiltro = ' AND marca.cod_marca = '.$_REQUEST['inCodMarca'].' ';
            $obTFrotaModelo->recuperaRelacionamento( $rsModelo, $stFiltro,' ORDER BY nom_modelo ' );

            //preenche o select com o resultado do banco
            while ( !$rsModelo->eof() ) {
                $inCount++;
                $stJs .= "$('".$_REQUEST['stModelo']."').options[".$inCount."] = new Option('".addslashes($rsModelo->getCampo('nom_modelo'))."','".$rsModelo->getCampo('cod_modelo')."',''); \n";
                $rsModelo->proximo();
            }

        }

    break;
}
echo $stJs;
?>

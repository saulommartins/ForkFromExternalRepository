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
    * Oculto do Componente monta PaisEstadoMunicipio
    * Data de Criação: 19/08/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: OCMontaPaisEstadoMunicipio.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-01.01.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoPais.class.php");
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php");
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php");

$stPersistente  = $_GET['stPersistente'];
$stIdCombo      = $_GET['stIdCombo'];
$stCampoId      = $_GET['stCampoId'];
$stCampoDesc    = $_GET['stCampoDesc'];

switch ($_GET['stCtrl']) {
    case 'preencher':
        $obPersistente = new $stPersistente;
        $inCount = 0;//
        $stFiltro = "";
        $boFlagListar = true;
        foreach ($_GET as $stParametro => $inValor) {
            if ($inCount > 4) {
                //CASO NÃO SEJA INFORMADO O VALOR DE UM PARAMETRO NÃO É EXECUTADO O MÉTODO LISTAR
                if (empty($inValor)) {
                    $boFlagListar = false;
                    break;
                } elseif ($stParametro != 'stCtrl') {
                    $stFiltro .= $stParametro."=".$inValor." AND ";
                }
            }
            $inCount++;
        }
        echo "limpaSelect( document.getElementById('".$stIdCombo."'),1);\n";
        if ($boFlagListar) {
            $stFiltro = " WHERE ".substr($stFiltro,0,strlen($stFiltro) - 4);
            $obPersistente->recuperaTodos( $rsLista, $stFiltro );
            //$obPersistente->debug();
            $inContador = 0;
            while (!$rsLista->eof()) {
                $inId        = $rsLista->getCampo($stCampoId);
                $stDescricao = $rsLista->getCampo($stCampoDesc);
                echo "document.getElementById('".$stIdCombo."').options[".++$inContador."] = new Option('".addslashes($stDescricao)."','$inId');\n";
                $rsLista->proximo();
            }
        }
    break;
    case 'limpar':
        echo "limpaSelect( document.getElementById('".$stIdCombo."'),1);\n";
    break;
}
?>

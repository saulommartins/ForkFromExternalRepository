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
* Oculto de Processamento e para PopUp de Empenho
* Data de Criação: 18/10/2006

* @author Analista:
* @author Desenvolvedor: Bruce Cruz de Sena

* Casos de uso :
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stCampoCod    = $_GET['stNomCampoCod'];
$stCampoDesc   = $_GET['stIdCampoDesc'];
$inCodigo      = $_REQUEST[ $stCampoCod ];
$inCodEntidade = $_REQUEST['inCodEntidade'];

$stCtrl = $_REQUEST["stCtrl"];

switch ($stCtrl) {
    case 'obra_tcmgo':
        $stFornecedor = '&nbsp;';

        if ($inCodigo) {
            include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $obTEmpenhoEmpenho->setDado( 'cod_entidade'   , $_REQUEST["inCodEntidade"] );
            $obTEmpenhoEmpenho->setDado( 'exercicio'      , $_REQUEST["stExercicio"  ] );
            $obTEmpenhoEmpenho->setDado( 'cod_empenho'    , $inCodigo                  );
            $obTEmpenhoEmpenho->setDado( 'cod_estrutural' , '4.4.9.0.51'               );
            $obTEmpenhoEmpenho->recuperaEmpenhoObra ($rsLista);
            if ( $rsLista->getCampo('nom_fornecedor') ) {
                $stFornecedor = str_replace( "'","\'",$rsLista->getCampo( "nom_fornecedor" ) );
            } else {
                $stJs .= "f.$stCampoCod.value='';";
                $stJs .= "d.getElementById( '$stCampoDesc' ).innerHTML = '&nbsp;';";
                $stJs .= "alertaAviso('Empenho informado está anulado ou não existe.','frm','erro','".Sessao::getId()."'); \n";
            }
        }
        $stJs .= "d.getElementById('$stCampoDesc').innerHTML = '$stFornecedor';\n";
    break;
}
echo $stJs;
?>

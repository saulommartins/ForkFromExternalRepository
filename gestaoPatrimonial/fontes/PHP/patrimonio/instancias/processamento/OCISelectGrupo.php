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
    * Oculto de Relatório de Concessão de Vale-Tranporte
    * Data de Criação: 07/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Revision: 28706 $
    $Name$
    $Author: luiz $
    $Date: 2008-03-24 16:17:06 -0300 (Seg, 24 Mar 2008) $

    * Casos de uso: uc-04.06.11
*/

/*
$Log$
Revision 1.6  2007/10/05 12:59:58  hboaventura
inclusão dos arquivos

Revision 1.5  2007/09/18 15:36:35  hboaventura
Adicionando ao repositório

Revision 1.4  2007/05/22 02:18:51  diego
Bug #9275#

Revision 1.3  2006/07/06 14:07:05  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupo.class.php" );

switch ($_REQUEST["stCtrl"]) {
    case "montaGrupo":
        $stJs .= "limpaSelect($('".$_REQUEST['stGrupo']."'),0); \n";
        $stJs .= "$('".$_REQUEST['stGrupo']."').options[0] = new Option('Selecione','', 'selected');\n";
        $inCount = 0;
        $obMapeamento = new TPatrimonioGrupo();
        if ( !empty($_REQUEST[$_REQUEST['stNatureza']]) ) {
            $obMapeamento->recuperaTodos($rsRecord,' WHERE cod_natureza = '.$_REQUEST[ $_REQUEST['stNatureza'] ], 'ORDER BY nom_grupo' );
            while (!$rsRecord->eof()) {
                $inCount++;
                $inId   = $rsRecord->getCampo("cod_grupo");
                $stDesc = $rsRecord->getCampo("cod_grupo").' - '.$rsRecord->getCampo("nom_grupo");

                $stJs .= "$('".$_REQUEST['stGrupo']."').options[$inCount] = new Option('".addslashes($stDesc)."','".$inId."','".$stSelected."'); \n";
                $rsRecord->proximo();
            }
        }
//    $stJs .= $js;
//    SistemaLegado::executaFrameOculto( $stJs );

    break;
}
echo $stJs;
?>

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
  * Página de
  * Data de criação : 26/05/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    Caso de uso: uc-06.02.02
**/

/*
$Log$
Revision 1.8  2006/07/06 13:52:24  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:42:06  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include_once (CAM_GP_PAT_NEGOCIO."RPatrimonioGrupo.class.php"                                         );
include_once (TPAT."TPatrimonioGrupo.class.php"                                         );
include_once (TCRJ."TCRJClassificacaoGrupoPatrimonio.class.php"                         );

switch ($_REQUEST['stCtrl']) {
    case "BuscaSigla":
        $obTTCRJClassificacaoGrupoPatrimonio = new TCRJClassificacaoGrupoPatrimonio();
        $obTTCRJClassificacaoGrupoPatrimonio->setDado('cod_natureza',$_REQUEST['inCodNatureza']);
        $obTTCRJClassificacaoGrupoPatrimonio->setDado('cod_grupo',$_REQUEST['inCodGrupo']);
        $obTTCRJClassificacaoGrupoPatrimonio->consultar() ;
        $stJs .= "f.stSiglaClassificacao.value = '".$obTTCRJClassificacaoGrupoPatrimonio->getDado('sigla')."';\n";

    break;

}
SistemaLegado::executaFrameOculto( $stJs );

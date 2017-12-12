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
* Arquivo instância para popup de CGM
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 30668 $
$Name$
$Author: jose.eduardo $
$Date: 2006-07-10 12:53:06 -0300 (Seg, 10 Jul 2006) $

Casos de uso: uc-02.01.05
*/

/*
$Log$
Revision 1.7  2006/07/10 15:52:43  jose.eduardo
Bug #6122#

Revision 1.6  2006/07/05 20:44:01  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"        );

/*
echo "<pre>";
print_r($_REQUEST);
echo "</pre>";
*/

switch ($_GET['stCtrl']) {

    case 'buscaPopup':

        if (strlen( trim($_REQUEST['inCodRecurso']) ) > 0) {

            $obRegra = new ROrcamentoRecurso();
            $rsLista = new RecordSet;
            $obRegra->setCodRecurso( $_REQUEST['inCodRecurso'] );
            $obRegra->listar($rsLista);
            $stDescricaoRecurso = $rsLista->getCampo("nom_recurso");
            $inCodRecurso = $rsLista->getCampo("cod_recurso");

        } else {
            $stDescricaoRecurso = null;
            $inCodRecurso = null;
        }

        sistemaLegado::executaFrameOculto("f.inCodRecurso.value = '".$inCodRecurso."'; \n retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricaoRecurso."');");

    break;

}

?>

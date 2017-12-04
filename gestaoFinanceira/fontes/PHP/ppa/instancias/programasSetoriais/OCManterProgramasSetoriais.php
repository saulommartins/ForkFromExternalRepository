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
 * Arquivo oculto do Programa Setorial
 *
 * @category    Urbem
 * @package     PPA
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id: $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

switch ($_GET['stCtrl']) {
case 'preencheMacroObjetivo':
    //limpa o combo de macro objetivos
    $stJs .= "jq('#inCodMacroObjetivo').removeOption(/./);";
    $stJs .= "jq('#inCodMacroObjetivoTxt').val('');";
    $stJs .= "var arOption = { '' : 'Selecione', ";

    //se nao for vazio, busca os dados
    if (($_GET['inCodPPA'] != '') OR ($_GET['inCodPPATxt'] != '')) {
        include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAMacroObjetivo.class.php';

        $inCodPPA = ($_GET['inCodPPA']) ? $_GET['inCodPPA'] : $_GET['inCodPPATxt'];

        $obTPPAMatroObjetivo = new TPPAMacroObjetivo;
        //Filtro para a consulta
        $stFiltro = ' WHERE cod_ppa = ' . $inCodPPA . ' ';
        $obTPPAMatroObjetivo->recuperaTodos($rsMacroObjetivo, $stFiltro);

        //percorre todo o recordset montando o combo de macro objetivos
        while (!$rsMacroObjetivo->eof()) {
            $stDescricao = str_replace( chr(13), ' ',$rsMacroObjetivo->getCampo('descricao'));
            $stDescricao = str_replace("\n",' ',$stDescricao);
            $stDescricao = addslashes($stDescricao);

            $stJs .= " '" . $rsMacroObjetivo->getCampo('cod_macro') . "' : '" . substr($stDescricao,0,100) . "', ";

            $rsMacroObjetivo->proximo();
        }
    }

    $stJs .= '};';
    $stJs .= "jq('#inCodMacroObjetivo').addOption(arOption,false);";
    if ($_REQUEST['inCodMacro']) {
        $stJs .= "jq('#inCodMacroObjetivoTxt').val('".$_REQUEST['inCodMacro']."');";
        $stJs .= "jq('#inCodMacroObjetivo').selectOptions('".$_REQUEST['inCodMacro']."', true);";
    }

    break;

}

echo $stJs;

?>

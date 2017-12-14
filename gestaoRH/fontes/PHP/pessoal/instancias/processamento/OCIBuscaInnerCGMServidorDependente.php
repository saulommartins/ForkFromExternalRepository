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
    * Oculto do componente IServidorDependente
    * Data de Criação: 04/03/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Alex Cardoso

    * @ignore

    $Id: OCIBuscaInnerCGMServidorDependente.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.08.17
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php"                                );

function preencherServidorDependente()
{
    $obTPessoalServidor = new TPessoalServidor;
    $stExtensao = $_REQUEST["stExtensao"];
    if ($_REQUEST["inCGMServidorDependente".$stExtensao] != "") {

        if ($_REQUEST["boFiltrarPensaoJudicial"]) {
            $stFiltroPensaoJudicial = " WHERE cod_servidor IN (
                                                SELECT pensao.cod_servidor
                                                  FROM pessoal.pensao,
                                                       (SELECT pensao.cod_pensao,
                                                               max(timestamp) as timestamp
                                                          FROM pessoal.pensao
                                                      GROUP BY cod_pensao) as max_pensao
                                                 WHERE pensao.cod_pensao = max_pensao.cod_pensao AND
                                                       pensao.timestamp = max_pensao.timestamp AND
                                                       pensao.cod_pensao NOT IN (
                                                         SELECT pensao_excluida.cod_pensao
                                                           FROM pessoal.pensao_excluida,
                                                                (SELECT cod_pensao,
                                                                        max(timestamp) as timestamp
                                                                   FROM pessoal.pensao_excluida
                                                               GROUP BY cod_pensao) as max_pensao_excluida
                                                          WHERE pensao_excluida.cod_pensao = max_pensao_excluida.cod_pensao AND
                                                                pensao_excluida.timestamp  = max_pensao_excluida.timestamp
                                                       )
                                                 GROUP BY pensao.cod_servidor
                                       )";
        }

        $stFiltro .= " AND cod_servidor IN (
                           SELECT
                                cod_servidor
                           FROM
                                pessoal.servidor_dependente
                           $stFiltroPensaoJudicial
                           GROUP BY cod_servidor
                      )\n";

        $stFiltro .= " AND servidor.numcgm = ".$_REQUEST['inCGMServidorDependente'.$stExtensao];
        $obTPessoalServidor->recuperaServidorPessoaFisica( $rsRecordSet , $stFiltro);
        //$obTPessoalServidor->debug();

        $stNull = "&nbsp;";
        if ( $rsRecordSet->getNumLinhas() <= 0) {
            $stJs .= "document.frm.inCGMServidorDependente$stExtensao.value = '';                                                           \n";
            $stJs .= "document.frm.inCGMServidorDependente$stExtensao.focus();                                                              \n";
            $stJs .= "document.getElementById('stCGMServidorDependente$stExtensao').innerHTML = '$stNull';                                     \n";
            $stJs .= "document.frm.HdninCGMServidorDependente$stExtensao.value = '';    \n";
            $stJs .= "alertaAviso('@Campo CGM do Servidor inválido. (".$_REQUEST["inCGMServidorDependente".$stExtensao].")','form','erro','".Sessao::getId()."');\n";
        } else {
            $stJs .= "document.getElementById('stCGMServidorDependente$stExtensao').innerHTML = '".$rsRecordSet->getCampo('nom_cgm')."';    \n";
            $stJs .= "document.frm.HdninCGMServidorDependente$stExtensao.value = '".$rsRecordSet->getCampo("numcgm")."';    \n";
        }
    } else {
        $stJs .= "document.getElementById('stCGMServidorDependente$stExtensao').innerHTML = '&nbsp;';";
    }

    return $stJs;
}
switch ($_GET["stCtrl"]) {
    case "preencherServidorDependente":
        $stJs .= preencherServidorDependente();
    break;
}
if ($stJs) {
    echo $stJs;
}
?>

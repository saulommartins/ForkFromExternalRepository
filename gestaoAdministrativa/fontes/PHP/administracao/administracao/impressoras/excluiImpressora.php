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
    * Manutneção de impressoras
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    * Casos de uso: uc-01.03.92

    $Id: excluiImpressora.php 66029 2016-07-08 20:55:48Z carlos.silva $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";
include_once CAM_FW_LEGADO."impressorasLegado.class.php";
include_once CAM_FW_LEGADO."mascarasLegado.lib.php";
include_once CAM_FW_LEGADO."paginacaoLegada.class.php";
include_once CAM_FW_LEGADO."configuracaoLegado.class.php";
include_once CAM_FW_LEGADO."auditoriaLegada.class.php";

setAjuda( "UC-01.03.92" );

$excluir = $_REQUEST['excluir'];
$ctrl    = $_REQUEST['ctrl'];
$acao    = $_REQUEST['acao'];

if (isset($excluir)) {
    $ctrl           = 1;
    $excluir        = explode("-",$excluir);
    $cod_impressora = $excluir[0];
    $pagina         = $excluir[1];
    $nom_impressora = $excluir[2];
}

?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>
<?php

if (!(isset($ctrl)))
$ctrl = 0;

if (!isset($pagina)) {
    $pagina = 0;
}
?>
 <script type="text/javascript">
 function zebra(id, classe)
 {
       var tabela = document.getElementById(id);
        var linhas = tabela.getElementsByTagName("tr");
            for (var i = 0; i < linhas.length; i++) {
            ((i%2) != 0) ? linhas[i].className = classe : void(0);
        }
    }
</script>
<?php
switch ($ctrl) {

    case 0:

        echo "<table width=100% id='processos'>";

        if (isset($acao)) {
            $stSQL  = "      SELECT                                                   \n";
            $stSQL .= "              impressora.cod_impressora                        \n";
            $stSQL .= "           ,  impressora.nom_impressora                        \n";
            $stSQL .= "           ,  impressora.fila_impressao                        \n";
            $stSQL .= "           ,  impressora.cod_orgao                             \n";
            $stSQL .= "           ,  impressora.cod_local                             \n";
            $stSQL .= "           ,  local.descricao as nom_local                     \n";
            $stSQL .= "           ,  orgao_descricao.descricao as nom_orgao           \n";
            $stSQL .= "                                                               \n";
            $stSQL .= "        FROM  administracao.impressora                         \n";
            $stSQL .= "                                                               \n";
            $stSQL .= "  INNER JOIN  organograma.orgao_descricao                      \n";
            $stSQL .= "          ON  orgao_descricao.cod_orgao = impressora.cod_orgao \n";
            $stSQL .= "                                                               \n";
            $stSQL .= "  INNER JOIN  organograma.local                                \n";
            $stSQL .= "          ON  local.cod_local = impressora.cod_local           \n";
            $stSQL .= "                                                               \n";
            $stSQL .= "       WHERE  impressora.cod_impressora > 0                    \n";

            Sessao::write('sSQLs',$stSQL);
        }

        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("nom_impressora","ASC");
        $sSQL = $paginacao->geraSQL();

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($stSQL);
        $dbEmp->vaiPrimeiro();
        if($dbEmp->numeroDeLinhas==0)
            exit("<br><b>Nenhum registro encontrado!</b>");

        $lista="";
        echo "<tr>
             <tr>
                 <td class='alt_dados' colspan='9'>Registros de impressora</td>
             </tr>
             <td class='labelleft' width='5%'>&nbsp;</td>
             <td class=labelleft>Órgão</td>
             <td class=labelleft>Local</td>
             <td class=labelleft>Nome da Impressora</td>
             <td class=labelleft>Fila de Impressão</td>
             <td class=labelleft>Excluir</td>
          </tr>";

        $count = $paginacao->contador();
        while (!$dbEmp->eof()) {
            $nom_impressora  = trim($dbEmp->pegaCampo("nom_impressora"));
            $cod_impressora = trim($dbEmp->pegaCampo("cod_impressora"));
            $filaImpressao = $dbEmp->pegaCampo("fila_impressao");
            $cod_orgao = trim($dbEmp->pegaCampo("cod_orgao"));
            $cod_local = trim($dbEmp->pegaCampo("cod_local"));
            $orgao = trim($dbEmp->pegaCampo("nom_orgao"));
            $local = trim($dbEmp->pegaCampo("nom_local"));
            $dbEmp->vaiProximo();

            $lista .= "<tr>
                        <td class='labelcenter'>".$count++."</td>
                        <td class=show_dados>".$orgao."</td>
                        <td class=show_dados>".$local."</td>
                        <td class=show_dados>".$nom_impressora."</td>
                        <td class=show_dados>".$filaImpressao."</td>
                        <td class=show_dados>
                            <a href='#' onClick=\"alertaQuestao('".$_SERVER['PHP_SELF']."?acao=".$acao."','excluir','".$cod_impressora."-".$pagina."-".$nom_impressora."','".$nom_impressora."','sn_excluir','".Sessao::getId()."');\">
                                <img src='".CAM_FW_IMAGENS."btnexcluir.gif'  border='0'>
                            </a>
                        </td>
                   </tr>";
        }

        echo $lista;
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo "<table id= 'paginacao' width=695 align=center>
                <tr>
                    <td align=center><font size=2>";
                        $paginacao->mostraLinks();
                        echo "</font>
                    </td>
                </tr></table>";
                        ?>
        <script>zebra('processos','zb');</script>
<?php
    break;

    case 1:

        $exclusao = new impressorasLegado;
        $exclusao->pegaCodImpressora($cod_impressora);
        $objeto = "Impressora: ".$nom_impressora;;
        $pag = $PHP_SELF."?".Sessao::getId()."&acao=".$acao."&pagina=".$pagina;

        if ($exclusao->deleteImpresssora()) {
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $objeto);
            $audicao->insereAuditoria();
            alertaAviso($pag, $objeto, "excluir", "aviso");
        } else {
            if ( strpos($exclusao->stErro, "fk_") )
                $objeto = "A $objeto não pode ser excluída porque está sendo utilizada";

            alertaAviso($pag,$objeto,"n_excluir","erro");
        }

    break;

}

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
